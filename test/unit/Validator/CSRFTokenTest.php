<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\sfFormBundle\Validator\CSRFToken;
use Bundle\sfFormBundle\Validator\Error;
use Bundle\sfFormBundle\Validator\ErrorSchema;
use Bundle\sfFormBundle\Tool\Callable;

$t = new lime_test(5);

// __construct()
$t->diag('__construct()');
try
{
  new CSRFToken();
  $t->fail('__construct() throws an \RuntimeException if you don\'t pass a token option');
}
catch (\RuntimeException $e)
{
  $t->pass('__construct() throws an \RuntimeException if you don\'t pass a token option');
}

$v = new CSRFToken(array('token' => 'symfony'));

// ->clean()
$t->diag('->clean()');
$t->is($v->clean('symfony'), 'symfony', '->clean() checks that the token is valid');

try
{
  $v->clean('another');
  $t->fail('->clean() throws an Error if the token is not valid');
  $t->skip('', 1);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error if the token is not valid');
  $t->is($e->getCode(), 'csrf_attack', '->clean() throws a Error');
}

// ->asString()
$t->diag('->asString()');
$t->is($v->asString(), 'Bundle\sfFormBundle\Validator\CSRFToken({ token: symfony })', '->asString() returns a string representation of the validator');
