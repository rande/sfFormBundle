<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\FormBundle\Validator\String;
use Bundle\FormBundle\Validator\AndOperator;
use Bundle\FormBundle\Validator\Error;
use Bundle\FormBundle\Validator\ErrorSchema;


$t = new lime_test(25);

$v1 = new String(array('max_length' => 3));
$v2 = new String(array('min_length' => 3));

$v = new AndOperator(array($v1, $v2));

// __construct()
$t->diag('__construct()');
$v = new AndOperator();
$t->is($v->getValidators(), array(), '->__construct() can take no argument');
$v = new AndOperator($v1);
$t->is($v->getValidators(), array($v1), '->__construct() can take a validator as its first argument');
$v = new AndOperator(array($v1, $v2));
$t->is($v->getValidators(), array($v1, $v2), '->__construct() can take an array of validators as its first argument');
try
{
  $v = new AndOperator('string');
  $t->fail('__construct() throws an exception when passing a non supported first argument');
}
catch (\InvalidArgumentException $e)
{
  $t->pass('__construct() throws an exception when passing a non supported first argument');
}

// ->addValidator()
$t->diag('->addValidator()');
$v = new AndOperator();
$v->addValidator($v1);
$v->addValidator($v2);
$t->is($v->getValidators(), array($v1, $v2), '->addValidator() adds a validator');

// ->clean()
$t->diag('->clean()');
$t->is($v->clean('foo'), 'foo', '->clean() returns the string unmodified');

try
{
  $v->setOption('required', true);
  $v->clean(null);
  $t->fail('->clean() throws an Error exception if the input value is required');
  $t->skip('', 1);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error exception if the input value is required');
  $t->is($e->getCode(), 'required', '->clean() throws a Error');
}

$v2->setOption('max_length', 2);
try
{
  $v->clean('foo');
  $t->fail('->clean() throws an Error exception if one of the validators fails');
  $t->skip('', 2);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error exception if one of the validators fails');
  $t->is($e[0]->getCode(), 'max_length', '->clean() throws a SchemaError');
  $t->ok($e instanceof ErrorSchema, '->clean() throws a SchemaError');
}

$v1->setOption('max_length', 2);
try
{
  $v->clean('foo');
  $t->fail('->clean() throws an Error exception if one of the validators fails');
  $t->skip('', 4);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error exception if one of the validators fails');
  $t->is(count($e), 2, '->clean() throws an error for every error');
  $t->is($e[0]->getCode(), 'max_length', '->clean() throws a SchemaError');
  $t->is($e[1]->getCode(), 'max_length', '->clean() throws a SchemaError');
  $t->ok($e instanceof ErrorSchema, '->clean() throws a SchemaError');
}

$v->setOption('halt_on_error', true);
try
{
  $v->clean('foo');
  $t->fail('->clean() throws an Error exception if one of the validators fails');
  $t->skip('', 3);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error exception if one of the validators fails');
  $t->is(count($e), 1, '->clean() only returns the first error if halt_on_error option is true');
  $t->is($e[0]->getCode(), 'max_length', '->clean() throws a SchemaError');
  $t->ok($e instanceof ErrorSchema, '->clean() throws a SchemaError');
}

try
{
  $v->setMessage('invalid', 'Invalid.');
  $v->clean('foo');
  $t->fail('->clean() throws an Error exception if one of the validators fails');
  $t->skip('', 2);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error exception if one of the validators fails');
  $t->is($e->getCode(), 'invalid', '->clean() throws a Error if invalid message is not empty');
  $t->ok(!$e instanceof ErrorSchema, '->clean() throws a Error if invalid message is not empty');
}

// ->asString()
$t->diag('->asString()');
$v1 = new String(array('max_length' => 3));
$v2 = new String(array('min_length' => 3));
$v = new AndOperator(array($v1, $v2));
$t->is($v->asString(), "(\n  Bundle\FormBundle\Validator\String({ max_length: 3 })\n  and\n  Bundle\FormBundle\Validator\String({ min_length: 3 })\n)"
, '->asString() returns a string representation of the validator');

$v = new AndOperator(array($v1, $v2), array(), array('required' => 'This is required.'));
$t->is($v->asString(), "(\n  Bundle\FormBundle\Validator\String({ max_length: 3 })\n  and({}, { required: 'This is required.' })\n  Bundle\FormBundle\Validator\String({ min_length: 3 })\n)" 
, '->asString() returns a string representation of the validator');
