<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\sfFormBundle\Validator\SchemaForEach;
use Bundle\sfFormBundle\Validator\String;
use Bundle\sfFormBundle\Validator\Error;
use Bundle\sfFormBundle\Validator\ErrorSchema;

$t = new lime_test(5);

$v1 = new String(array('min_length' => 3));

// __construct()
$t->diag('__construct()');
$v = new SchemaForEach($v1, 3);
$t->is($v->getFields(), array($v1, $v1, $v1), '->__construct() takes a  object as its first argument');

$v = new SchemaForEach($v1, 6);
$t->is($v->getFields(), array($v1, $v1, $v1, $v1, $v1, $v1), '->__construct() takes a number of times to duplicate the validator');

// ->clean()
$t->diag('->clean()');
try
{
  $v->clean(array('f', 'a', 'b', 'i', 'e', 'n'));
  $t->fail('->clean() throws an Error');
  $t->skip('', 2);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error');
  $t->is(count($e->getGlobalErrors()), 0, '->clean() does not throw global errors');
  $t->is(count($e->getNamedErrors()), 6, '->clean() throws named errors');
}
