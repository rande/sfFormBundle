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
 * CSRFToken checks that the token is valid.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: CSRFToken.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
class CSRFToken extends Base
{
  /**
   * @see Base
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('token');

    $this->setOption('required', true);

    $this->addMessage('csrf_attack', 'CSRF attack detected.');
  }

  /**
   * @see Base
   */
  protected function doClean($value)
  {
    if ($value != $this->getOption('token'))
    {
      throw new Error($this, 'csrf_attack');
    }

    return $value;
  }
}
