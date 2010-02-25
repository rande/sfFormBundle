<?php

namespace Bundle\FormBundle\Validator;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Decorator decorates another validator.
 *
 * This validator has exactly the same behavior as the Decorator validator.
 *
 * The options and messages are proxied from the decorated validator.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Decorator.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
abstract class Decorator extends Base
{
  protected
    $validator = null;

  /**
   * @see Base
   */
  public function __construct($options = array(), $messages = array())
  {
    $this->validator = $this->getValidator();

    if (!$this->validator instanceof Base)
    {
      throw new \RuntimeException('The getValidator() method must return a Base instance.');
    }

    foreach ($options as $key => $value)
    {
      $this->validator->setOption($key, $value);
    }

    foreach ($messages as $key => $value)
    {
      $this->validator->setMessage($key, $value);
    }
  }

  /**
   * Returns the decorated validator.
   *
   * Every subclass must implement this method.
   *
   * @return Base A Base instance
   */
  abstract protected function getValidator();

  /**
   * @see Base
   */
  public function clean($value)
  {
    return $this->doClean($value);
  }

  /**
   * @see Base
   */
  protected function doClean($value)
  {
    return $this->validator->clean($value);
  }

  /**
   * @see Base
   */
  public function getMessage($name)
  {
    return $this->validator->getMessage($name);
  }

  /**
   * @see Base
   */
  public function setMessage($name, $value)
  {
    $this->validator->setMessage($name, $value);
  }

  /**
   * @see Base
   */
  public function getMessages()
  {
    return $this->validator->getMessages();
  }

  /**
   * @see Base
   */
  public function setMessages($values)
  {
    return $this->validator->setMessages($values);
  }

  /**
   * @see Base
   */
  public function getOption($name)
  {
    return $this->validator->getOption($name);
  }

  /**
   * @see Base
   */
  public function setOption($name, $value)
  {
    $this->validator->setOption($name, $value);
  }

  /**
   * @see Base
   */
  public function hasOption($name)
  {
    return $this->validator->hasOption($name);
  }

  /**
   * @see Base
   */
  public function getOptions()
  {
    return $this->validator->getOptions();
  }

  /**
   * @see Base
   */
  public function setOptions($values)
  {
    $this->validator->setOptions($values);
  }

  /**
   * @see Base
   */
  public function asString($indent = 0)
  {
    return $this->validator->asString($indent);
  }

  /**
   * @see Base
   */
  public function getDefaultOptions()
  {
    return $this->validator->getDefaultOptions();
  }

  /**
   * @see Base
   */
  public function getDefaultMessages()
  {
    return $this->validator->getDefaultMessages();
  }
}
