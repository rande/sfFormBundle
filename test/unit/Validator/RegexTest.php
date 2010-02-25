<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\FormBundle\Validator\Regex;
use Bundle\FormBundle\Validator\Error;

use Bundle\FormBundle\Tool\Callable;

function generate_regex()
{
  return '/^123$/';
}

$t = new lime_test(11);

// __construct()
$t->diag('__construct()');
try
{
  new Regex();
  $t->fail('__construct() throws an \RuntimeException if you don\'t pass a pattern option');
}
catch (\RuntimeException $e)
{
  $t->pass('__construct() throws an \RuntimeException if you don\'t pass a pattern option');
}

// ->clean()
$t->diag('->clean()');

$v = new Regex(array('pattern' => '/^[0-9]+$/'));
$t->is($v->clean(12), '12', '->clean() checks that the value match the regex');

try
{
  $v->clean('symfony');
  $t->fail('->clean() throws an Error if the value does not match the pattern');
  $t->skip('', 1);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error if the value does not match the pattern');
  $t->is($e->getCode(), 'invalid', '->clean() throws a Error');
}

$v = new Regex(array('pattern' => '/^[0-9]+$/', 'must_match' => false));
$t->is($v->clean('symfony'), 'symfony', '->clean() checks that the value does not match the regex if must_match is false');

try
{
  $v->clean(12);
  $t->fail('->clean() throws an Error if the value matches the pattern if must_match is false');
  $t->skip('', 1);
}
catch (Error $e)
{
  $t->pass('->clean() throws an Error if the value matches the pattern if must_match is false');
  $t->is($e->getCode(), 'invalid', '->clean() throws a Error');
}

$v = new Regex(array('pattern' => new Callable('generate_regex')));

try
{
  $v->clean('123');
  $t->pass('->clean() uses the pattern returned by a Callable pattern option');
}
catch (Error $e)
{
  $t->fail('->clean() uses the pattern returned by a Callable pattern option');
}

// ->asString()
$t->diag('->asString()');

$v = new Regex(array('pattern' => '/^[0-9]+$/', 'must_match' => false));
$t->is($v->asString(), 'Bundle\FormBundle\Validator\Regex({ must_match: false, pattern: \'/^[0-9]+$/\' })', '->asString() returns a string representation of the validator');

// ->getPattern()
$t->diag('->getPattern()');

$v = new Regex(array('pattern' => '/\w+/'));
$t->is($v->getPattern(), '/\w+/', '->getPattern() returns the regular expression');
$v = new Regex(array('pattern' => new Callable('generate_regex')));
$t->is($v->getPattern(), '/^123$/', '->getPattern() returns a regular expression from a Callable');
