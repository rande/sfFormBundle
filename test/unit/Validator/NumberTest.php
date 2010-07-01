<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\sfFormBundle\Validator\Number;
use Bundle\sfFormBundle\Validator\Error;
// use Bundle\sfFormBundle\Validator\ErrorSchema;

$t = new lime_test(13);

$v = new Number();

// ->clean()
$t->diag('->clean()');
$t->is($v->clean(12.3), 12.3, '->clean() returns the numbers unmodified');
$t->is($v->clean('12.3'), 12.3, '->clean() converts strings to numbers');

$t->is($v->clean(12.12345678901234), 12.12345678901234, '->clean() returns the numbers unmodified');
$t->is($v->clean('12.12345678901234'), 12.12345678901234, '->clean() converts strings to numbers');

try
{
  $v->clean('not a float');
  $t->fail('->clean() throws a Error if the value is not a number');
}
catch (Error $e)
{
  $t->pass('->clean() throws a Error if the value is not a number');
}

$v->setOption('required', false);
$t->ok($v->clean(null) === null, '->clean() returns null for null values');

$v->setOption('max', 2);
$t->is($v->clean(1), 1, '->clean() checks the maximum number allowed');
try
{
  $v->clean(3.4);
  $t->fail('"max" option set the maximum number allowed');
}
catch (Error $e)
{
  $t->pass('"max" option set the maximum number allowed');
}

$v->setMessage('max', 'Too large');
try
{
  $v->clean(5);
  $t->fail('"max" error message customization');
}
catch (Error $e)
{
  $t->is($e->getMessage(), 'Too large', '"max" error message customization');
}

$v->setOption('max', null);

$v->setOption('min', 3);
$t->is($v->clean(5), 5, '->clean() checks the minimum number allowed');
try
{
  $v->clean('1');
  $t->fail('"min" option set the minimum number allowed');
  $t->skip('', 1);
}
catch (Error $e)
{
  $t->pass('"min" option set the minimum number allowed');
  $t->is($e->getCode(), 'min', '->clean() throws a Error');
}

$v->setMessage('min', 'Too small');
try
{
  $v->clean(1);
  $t->fail('"min" error message customization');
}
catch (Error $e)
{
  $t->is($e->getMessage(), 'Too small', '"min" error message customization');
}

$v->setOption('min', null);
