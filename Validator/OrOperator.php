<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\sfFormBundle\Validator;

use Symfony\Components\Yaml\Inline as YamlInline;


/**
 * Or validates an input value if at least one validator passes.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Or.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class OrOperator extends Base
{
  protected
    $validators = array();

  /**
   * Constructor.
   *
   * The first argument can be:
   *
   *  * null
   *  * a Base instance
   *  * an array of Base instances
   *
   * @param mixed $validators  Initial validators
   * @param array $options     An array of options
   * @param array $messages    An array of error messages
   *
   * @see Base
   */
  public function __construct($validators = null, $options = array(), $messages = array())
  {
    
    if ($validators instanceof Base)
    {
      $this->addValidator($validators);
    }
    else if (is_array($validators))
    {
      foreach ($validators as $validator)
      {
        $this->addValidator($validator);
      }
    }
    else if (null !== $validators)
    {
      throw new \InvalidArgumentException('Or constructor takes a Base object, or a Base array.');
    }
    
    parent::__construct($options, $messages);
  }

  /**
   * @see Base
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', null);
  }

  /**
   * Adds a validator.
   *
   * @param Base $validator  An Base instance
   */
  public function addValidator(Base $validator)
  {
    $this->validators[] = $validator;
  }

  /**
   * Returns an array of the validators.
   *
   * @return array An array of Base instances
   */
  public function getValidators()
  {
    return $this->validators;
  }

  /**
   * @see Base
   */
  protected function doClean($value)
  {
    $errors = array();
    foreach ($this->validators as $validator)
    {
      try
      {
        return $validator->clean($value);
      }
      catch (Error $e)
      {
        $errors[] = $e;
      }
    }

    if ($this->getMessage('invalid'))
    {
      throw new Error($this, 'invalid', array('value' => $value));
    }

    throw new ErrorSchema($this, $errors);
  }

  /**
   * @see Base
   */
  public function asString($indent = 0)
  {
    $validators = '';
    for ($i = 0, $max = count($this->validators); $i < $max; $i++)
    {
      $validators .= "\n".$this->validators[$i]->asString($indent + 2)."\n";

      if ($i < $max - 1)
      {
        $validators .= str_repeat(' ', $indent + 2).'or';
      }

      if ($i == $max - 2)
      {
        $options = $this->getOptionsWithoutDefaults();
        $messages = $this->getMessagesWithoutDefaults();

        if ($options || $messages)
        {
          $validators .= sprintf('(%s%s)',
            $options ? YamlInline::dump($options) : ($messages ? '{}' : ''),
            $messages ? ', '.YamlInline::dump($messages) : ''
          );
        }
      }
    }

    return sprintf("%s(%s%s)", str_repeat(' ', $indent), $validators, str_repeat(' ', $indent));
  }
}
