<?php

namespace Bundle\sfFormBundle\Validator;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * FromDescription converts a string to a validator.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: FromDescription.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class FromDescription extends Decorator
{
  protected
    $tokens = array(),
    $string = '';

  /**
   * @see Base
   */
  public function __construct($string, $options = array(), $messages = array())
  {
    $this->string = $string;
    $this->tokens = $this->tokenize($string);

    parent::__construct($options, $messages);
  }

  /**
   * Returns a PHP representation for the validator.
   *
   * This PHP representation can be evaled to return the object validator.
   *
   * This is mainly useful to cache the result of the validator string parsing.
   *
   * @return string The PHP representation for the validator
   */
  public function asPhp()
  {
    return $this->reduceTokens($this->tokens, 'asPhp');
  }

  /**
   * @see Decorator
   */
  public function getValidator()
  {
    if (null === $this->validator)
    {
      $this->validator = $this->reduceTokens($this->tokens, 'getValidator');
    }

    return $this->validator;
  }

  /**
   * Tokenizes a validator string to a list of tokens in RPN.
   *
   * @param  string $string  A validator string
   *
   * @return array  An array of tokens
   */
  protected function tokenize($string)
  {
    $tokens = array();
    $len = strlen($string);
    $i = 0;

    while ($i < $len)
    {

      if (preg_match('/^([a-z0-9_\-\\\]+)\s*(<=|>=|<|>|==|!=)/i', substr($string, $i), $match))
      {
        // schema compare validator
        $i += strlen($match[0]);

        $leftField = $match[1];
        $operator = $match[2];

        // arguments (optional)
        $arguments = $this->parseArguments($string, $i);

        // rightField
        if (!preg_match('/\s*([a-z0-9_\-\\\]+)/', substr($string, $i), $match))
        {
          throw new \DomainException('Parsing problem.');
        }

        $i += strlen($match[0]);
        $rightField = $match[1];

        $tokens[] = new FDToken('Bundle\\sfFormBundle\\Validator\\SchemaCompare', array($leftField, $operator, $rightField, $arguments[0], isset($arguments[1]) ? $arguments[1] : array()));
      }
      else if (preg_match('/^(and|or)/i', substr($string, $i), $match))
      {
        // all, any validador
        $i += strlen($match[0]);

        // arguments (optional)
        $arguments = $this->parseArguments($string, $i);

        $tokens[] = new FDTokenOperator(strtolower($match[1]), $arguments);
      }
      else if (preg_match('/^(?:([\\\a-z0-9_\-]+)\:)?([\\\a-z0-9_\-]+)/i', substr($string, $i), $match))
      {
        // var_dump($string, $i, substr($string, $i),  $match); die();
        // single validator (optionally filtered)
        $i += strlen($match[0]);
        
        $class = ''.$match[2];

        $arguments = $this->parseArguments($string, $i); 

        // $token = new FDToken($class, isset($arguments[1]) ? $arguments[1] : array());
        $token = new FDToken($class, $arguments);
        
        if ($match[1])
        {
          $token = new FDTokenFilter($match[1], $token);
        }
        
        $tokens[] = $token;
      }
      else if ('(' == $string[$i])
      {
        $tokens[] = new FDTokenLeftBracket();
        ++$i;
      }
      else if (')' == $string[$i])
      {
        $tokens[] = new FDTokenRightBracket();
        ++$i;
      }
      else if (in_array($string[$i], array(' ', "\t", "\r", "\n")))
      {
        ++$i;
      }
      else
      {
        throw new \DomainException(sprintf('Unable to parse string (%s).', $string));
      }
    }

    return $this->convertInfixToRpn($tokens);
  }

  /**
   * Parses validator arguments.
   *
   * @param  string  $string  The string to parse
   * @param  integer $i       The indice to start the parsing
   *
   * @return array   An array of parameters
   */
  protected function parseArguments($string, &$i)
  {
    $len = strlen($string);

    if ($i + 1 > $len || '(' != $string[$i])
    {
      return array(array(), array());
    }

    ++$i;

    $args = '';
    $opened = 0;

    while ($i < $len)
    {
      if ('(' == $string[$i])
      {
        ++$opened;
      }
      else if (')' == $string[$i])
      {
        if (!$opened)
        {
          break;
        }

        --$opened;
      }

      $args .= $string[$i++];
    }

    ++$i;

    $args = \Symfony\Components\Yaml\Inline::load('['.(!$args ? '{}' : $args).']');    
    return $args;
  }

  /**
   * Converts a token array from an infix notation to a RPN.
   *
   * @param  array $tokens  An array of tokens in infix notation
   *
   * @return array An array of token in RPN
   */
  protected function convertInfixToRpn($tokens)
  {
    $outputStack = array();
    $operatorStack = array();
    $precedences = array('and' => 2, 'or' => 1, '(' => 0);

    // based on the shunting yard algorithm
    foreach ($tokens as $token)
    {
      switch (get_class($token))
      {
        case 'Bundle\\sfFormBundle\\FDToken':
          $outputStack[] = $token;
          break;
        case 'Bundle\\sfFormBundle\\FDTokenLeftBracket':
          $operatorStack[] = $token;
          break;
        case 'Bundle\\sfFormBundle\\FDTokenRightBracket':
          while (!$operatorStack[count($operatorStack) - 1] instanceof FDTokenLeftBracket)
          {
            $outputStack[] = array_pop($operatorStack);
          }
          array_pop($operatorStack);
          break;
        case 'Bundle\\sfFormBundle\\FDTokenOperator':
          while (count($operatorStack) && $precedences[$token->__toString()] <= $precedences[$operatorStack[count($operatorStack) - 1]->__toString()])
          {
            $outputStack[] = array_pop($operatorStack);
          }
          $operatorStack[] = $token;
          break;
        default:
          $outputStack[] = $token;
      }
    }

    while (count($operatorStack))
    {
      $token = array_pop($operatorStack);
      if ($token instanceof FDTokenLeftBracket || $token instanceof FDTokenRightBracket)
      {
        throw new \DomainException(sprintf('Uneven parenthesis in string (%s).', $this->string));
      }

      $outputStack[] = $token;
    }

    return $outputStack;
  }

  /**
   * Reduces tokens to a single token and convert it with the given method.
   *
   * @param  array  $tokens  An array of tokens
   * @param  string $method  The method name to execute on each token
   *
   * @return mixed  A single validator representation
   */
  protected function reduceTokens($tokens, $method)
  {
    if (1 == count($tokens))
    {
      return $tokens[0]->$method();
    }
    // reduce to a single validator
    while (count($tokens) > 1)
    {
      $i = 0;
      
      while (isset($tokens[$i]) && !$tokens[$i] instanceof FDTokenOperator)
      {
        $i++;
      }
      
      $tokens[$i] = $tokens[$i]->$method($tokens[$i - 2], $tokens[$i - 1]);
      unset($tokens[$i - 1], $tokens[$i - 2]);

      $tokens = array_values($tokens);
    }

    return $tokens[0];
  }
}

class FDToken
{
  protected
    $class,
    $arguments;

  public function __construct($class, $arguments = array())
  {
    $this->class = $class;
    $this->arguments = $arguments;
  }

  public function asPhp()
  {
    return sprintf('new %s(%s)', $this->class, implode(', ', array_map(create_function('$a', 'return var_export($a, true);'), $this->arguments)));
  }

  public function getValidator()
  {
    $reflection = new \ReflectionClass($this->class);

    return $reflection->newInstanceArgs($this->arguments);
  }
}

class FDTokenFilter
{
  protected
    $field,
    $token;

  public function __construct($field, FDToken $token)
  {
    $this->field = $field;
    $this->token = $token;
  }

  public function asPhp()
  {
    return sprintf('new Bundle\\FromBundle\\Validator\\SchemaFilter(\'%s\', %s)', $this->field, $this->token->asPhp());
  }

  public function getValidator()
  {
    return new SchemaFilter($this->field, $this->token->getValidator());
  }
}

class FDTokenOperator
{
  protected
    $class,
    $operator,
    $token;

  public function __construct($operator, $arguments = array())
  {
    $this->operator = $operator;
    $this->arguments = $arguments;
    $this->class = 'or' == $operator ? 'Bundle\\sfFormBundle\\Validator\\OrOperator' : 'Bundle\\sfFormBundle\\Validator\\AndOperator';
  }

  public function __toString()
  {
    return $this->operator;
  }

  public function asPhp($tokenLeft, $tokenRight)
  {
    return sprintf('new %s(array(%s, %s), %s)',
      $this->class,
      in_array(get_class($tokenLeft), array('Bundle\\sfFormBundle\\Validator\\FDToken', 'Bundle\\sfFormBundle\\Validator\\FDTokenFilter')) ? $tokenLeft->asPhp() : $tokenLeft,
      in_array(get_class($tokenRight), array('Bundle\\sfFormBundle\\Validator\\FDToken', 'Bundle\\sfFormBundle\\Validator\\FDTokenFilter')) ? $tokenRight->asPhp() : $tokenRight,
      implode(', ', array_map(create_function('$a', 'return var_export($a, true);'), $this->arguments))
    );
  }

  public function getValidator($tokenLeft, $tokenRight)
  {
    $reflection = new \ReflectionClass($this->class);

    $validators = array(
      in_array(get_class($tokenLeft), array('Bundle\\sfFormBundle\\Validator\\FDToken', 'Bundle\\sfFormBundle\\Validator\\FDTokenFilter')) ? $tokenLeft->getValidator() : $tokenLeft,
      in_array(get_class($tokenRight), array('Bundle\\sfFormBundle\\Validator\\FDToken', 'Bundle\\sfFormBundle\\Validator\\FDTokenFilter')) ? $tokenRight->getValidator() : $tokenRight,
    );

    return $reflection->newInstanceArgs(array_merge(array($validators), $this->arguments));
  }
}

class FDTokenLeftBracket
{
  public function __toString()
  {
    return '(';
  }
}

class FDTokenRightBracket
{
  public function __toString()
  {
    return ')';
  }
}
