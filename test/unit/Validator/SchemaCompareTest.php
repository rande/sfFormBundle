<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\FormBundle\Validator\SchemaCompare;
use Bundle\FormBundle\Validator\Error;

$t = new lime_test(112);

$v = new SchemaCompare('left', SchemaCompare::EQUAL, 'right');

// ->clean()
$t->diag('->clean()');
foreach (array(
  array(array('left' => 'foo', 'right' => 'foo'), SchemaCompare::EQUAL),
  array(array(), SchemaCompare::EQUAL),
  // array(null, SchemaCompare::EQUAL), // rande: FROM THE CODE THIS TEST CANNOT WORK
  array(array('left' => 1, 'right' => 2), SchemaCompare::LESS_THAN),
  array(array('left' => 2, 'right' => 2), SchemaCompare::LESS_THAN_EQUAL),
  array(array('left' => 2, 'right' => 1), SchemaCompare::GREATER_THAN),
  array(array('left' => 2, 'right' => 2), SchemaCompare::GREATER_THAN_EQUAL),
  array(array('left' => 'foo', 'right' => 'bar'), SchemaCompare::NOT_EQUAL),
  array(array('left' => '0000', 'right' => '0'), SchemaCompare::NOT_IDENTICAL),
  array(array('left' => '0000', 'right' => '0'), SchemaCompare::EQUAL),
  array(array('left' => '0000', 'right' => '0000'), SchemaCompare::IDENTICAL),
  
  array(array('left' => 'foo', 'right' => 'foo'), '=='),
  array(array(), '=='),
  // array(null, '=='), // rande: FROM THE CODE THIS TEST CANNOT WORK
  array(array('left' => 1, 'right' => 2), '<'),
  array(array('left' => 2, 'right' => 2), '<='),
  array(array('left' => 2, 'right' => 1), '>'),
  array(array('left' => 2, 'right' => 2), '>='),
  array(array('left' => 'foo', 'right' => 'bar'), '!='),
  array(array('left' => '0000', 'right' => '0'), '!=='),
  array(array('left' => '0000', 'right' => '0'), '=='),
  array(array('left' => '0000', 'right' => '0000'), '==='),
) as $values)
{
  $v->setOption('operator', $values[1]);
  $t->is($v->clean($values[0]), $values[0], '->clean() checks that the values match the comparison');
}

foreach (array(
  array(array('left' => 'foo', 'right' => 'foo'), SchemaCompare::NOT_EQUAL),
  array(array(), SchemaCompare::NOT_EQUAL),
  array(null, SchemaCompare::NOT_EQUAL),
  array(array('left' => 1, 'right' => 2), SchemaCompare::GREATER_THAN),
  array(array('left' => 2, 'right' => 3), SchemaCompare::GREATER_THAN_EQUAL),
  array(array('left' => 2, 'right' => 1), SchemaCompare::LESS_THAN),
  array(array('left' => 3, 'right' => 2), SchemaCompare::LESS_THAN_EQUAL),
  array(array('left' => 'foo', 'right' => 'bar'), SchemaCompare::EQUAL),
  array(array('left' => '0000', 'right' => '0'), SchemaCompare::IDENTICAL),
  array(array('left' => '0000', 'right' => '0'), SchemaCompare::NOT_EQUAL),
  array(array('left' => '0000', 'right' => '0000'), SchemaCompare::NOT_IDENTICAL),

  array(array('left' => 'foo', 'right' => 'foo'), '!='),
  array(array(), '!='),
  array(null, '!='),
  array(array('left' => 1, 'right' => 2), '>'),
  array(array('left' => 2, 'right' => 3), '>='),
  array(array('left' => 2, 'right' => 1), '<'),
  array(array('left' => 3, 'right' => 2), '<='),
  array(array('left' => 'foo', 'right' => 'bar'), '=='),
  array(array('left' => '0000', 'right' => '0'), '==='),
  array(array('left' => '0000', 'right' => '0'), '!='),
  array(array('left' => '0000', 'right' => '0000'), '!=='),
) as $values)
{
  $v->setOption('operator', $values[1]);

  foreach (array(true, false) as $globalError)
  {
    $v->setOption('throw_global_error', $globalError);
    try
    {
      $v->clean($values[0]);
      $t->fail('->clean() throws an Error if the value is the comparison failed');
      $t->skip('', 1);
    }
    catch (Error $e)
    {
      $t->pass('->clean() throws an Error if the value is the comparison failed');
      $t->is($e->getCode(), $globalError ? 'invalid' : 'left [invalid]', '->clean() throws a Error');
    }
  }
}

try
{
  $v->clean('foo');
  $t->fail('->clean() throws an \InvalidArgumentException exception if the first argument is not an array of value');
}
catch (\InvalidArgumentException $e)
{
  $t->pass('->clean() throws an \InvalidArgumentException exception if the first argument is not an array of value');
}

$v = new SchemaCompare('left', 'foo', 'right');
try
{
  $v->clean(array());
  $t->fail('->clean() throws an \InvalidArgumentException exception if the operator does not exist');
}
catch (\InvalidArgumentException $e)
{
  $t->pass('->clean() throws an \InvalidArgumentException exception if the operator does not exist');
}

// ->asString()
$t->diag('->asString()');
$v = new SchemaCompare('left', SchemaCompare::EQUAL, 'right');
$t->is($v->asString(), 'left == right', '->asString() returns a string representation of the validator');

$v = new SchemaCompare('left', SchemaCompare::EQUAL, 'right', array(), array('required' => 'This is required.'));
$t->is($v->asString(), 'left ==({}, { required: \'This is required.\' }) right', '->asString() returns a string representation of the validator');
