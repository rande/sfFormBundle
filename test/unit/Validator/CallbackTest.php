<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\sfFormBundle\Validator\Callback;
use Bundle\sfFormBundle\Validator\Error;
use Bundle\sfFormBundle\Validator\ErrorSchema;
// use Bundle\sfFormBundle\Tool\Callable;

$t = new lime_test(7);

function clean_test($validator, $value, $arguments)
{
  if ($value != 'foo')
  {
    throw new Error($validator, 'must_be_foo');
  }

  return "*$value*".implode('-', $arguments);
}

// __construct()
$t->diag('__construct()');
try
{
  new Callback();
  $t->fail('__construct() throws an \RuntimeException if you don\'t pass a callback option');
}
catch (\RuntimeException $e)
{
  $t->pass('__construct() throws an \RuntimeException if you don\'t pass a callback option');
}

$v = new Callback(array('callback' => 'clean_test'));

// ->configure()
$t->diag('->configure()');
$t->is($v->clean(''), null, '->configure() switch required to false by default');

// ->clean()
$t->diag('->clean()');
$t->is($v->clean('foo'), '*foo*', '->clean() calls our validator callback');
try
{
  $v->clean('bar');
  $t->fail('->clean() throws a Error');
  $t->skip('', 1);
}
catch (Error $e)
{
  $t->pass('->clean() throws a Error');
  $t->is($e->getCode(), 'must_be_foo', '->clean() throws a Error');
}

$t->diag('callback with arguments');
$v = new Callback(array('callback' => 'clean_test', 'arguments' => array('fabien', 'symfony')));
$t->is($v->clean('foo'), '*foo*fabien-symfony', '->configure() can take an arguments option');

// ->asString()
$t->diag('->asString()');
$v = new Callback(array('callback' => 'clean_test'));
$t->is($v->asString(), 'Bundle\sfFormBundle\Validator\Callback({ callback: clean_test })', '->asString() returns a string representation of the validator');
