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
 * Callback validates an input value if the given callback does not throw a Error.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Callback.class.php 9048 2008-05-19 09:11:23Z FabianLange $
 */
class Callback extends Base
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * callback:  A valid PHP callback (required)
   *  * arguments: An array of arguments to pass to the callback
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see Base
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('callback');
    $this->addOption('arguments', array());

    $this->setOption('required', false);
  }

  /**
   * @see Base
   */
  protected function doClean($value)
  {
    return call_user_func($this->getOption('callback'), $this, $value, $this->getOption('arguments'));
  }
}
