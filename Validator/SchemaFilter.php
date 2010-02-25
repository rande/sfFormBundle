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
 * SchemaFilter executes non schema validator on a schema input value.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: SchemaFilter.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class SchemaFilter extends Schema
{
  /**
   * Constructor.
   *
   * @param string          $field      The field name
   * @param Base $validator  The validator
   * @param array           $options    An array of options
   * @param array           $messages   An array of error messages
   *
   * @see Base
   */
  public function __construct($field, Base $validator, $options = array(), $messages = array())
  {
    $this->addOption('field', $field);
    $this->addOption('validator', $validator);

    parent::__construct(null, $options, $messages);
  }

  /**
   * @see Base
   */
  protected function doClean($values)
  {
    if (null === $values)
    {
      $values = array();
    }

    if (!is_array($values))
    {
      throw new \InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $value = isset($values[$this->getOption('field')]) ? $values[$this->getOption('field')] : null;

    try
    {
      $values[$this->getOption('field')] = $this->getOption('validator')->clean($value);
    }
    catch (Error $error)
    {
      throw new ErrorSchema($this, array($this->getOption('field') => $error));
    }

    return $values;
  }

  /**
   * @see Base
   */
  public function asString($indent = 0)
  {
    return sprintf('%s%s:%s', str_repeat(' ', $indent), $this->getOption('field'), $this->getOption('validator')->asString(0));
  }
}
