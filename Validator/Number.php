<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\FormBundle\Validator;

/**
 * Number validates a number (integer or float). It also converts the input value to a float.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Number.class.php 22018 2009-09-14 16:56:28Z fabien $
 */
class Number extends Base
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max: The maximum value allowed
   *  * min: The minimum value allowed
   *
   * Available error codes:
   *
   *  * max
   *  * min
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see Base
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max', '"%value%" must be at most %max%.');
    $this->addMessage('min', '"%value%" must be at least %min%.');

    $this->addOption('min');
    $this->addOption('max');

    $this->setMessage('invalid', '"%value%" is not a number.');
  }

  /**
   * @see Base
   */
  protected function doClean($value)
  {
    if (!is_numeric($value))
    {
      throw new Error($this, 'invalid', array('value' => $value));
    }

    $clean = floatval($value);

    if ($this->hasOption('max') && $clean > $this->getOption('max'))
    {
      throw new Error($this, 'max', array('value' => $value, 'max' => $this->getOption('max')));
    }

    if ($this->hasOption('min') && $clean < $this->getOption('min'))
    {
      throw new Error($this, 'min', array('value' => $value, 'min' => $this->getOption('min')));
    }

    return $clean;
  }
}
