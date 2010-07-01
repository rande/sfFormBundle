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
 * Pass is an identity validator. It simply returns the value unmodified. 
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Pass.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
class Pass extends Base
{
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
    return $value;
  }
}
