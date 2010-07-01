<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\sfFormBundle\Validator\String;
use Bundle\sfFormBundle\Validator\Error;
use Bundle\sfFormBundle\Validator\ErrorSchema;

$t = new lime_test(33);

$v1 = new String();
$v2 = new String();

$e1 = new Error($v1, 'max_length', array('value' => 'foo', 'max_length' => 1));
$e2 = new Error($v2, 'min_length', array('value' => 'bar', 'min_length' => 5));

$e = new ErrorSchema($v1);

// __construct()
$t->diag('__construct()');
$t->is($e->getValidator(), $v1, '__construct() takes a  as its first argument');
$e = new ErrorSchema($v1, array('e1' => $e1, 'e2' => $e2));
$t->is($e->getErrors(), array('e1' => $e1, 'e2' => $e2), '__construct() can take an array of Error as its second argument');

// ->addError() ->getErrors()
$t->diag('->addError() ->getErrors()');
$e = new ErrorSchema($v1);
$e->addError($e1);
$e->addError($e2, 'e2');
$e->addError($e1, '2');
$t->is($e->getErrors(), array($e1, 'e2' => $e2, '2' => $e1), '->addError() adds an error to the error schema');

$t->diag('embedded errors');
$es1 = new ErrorSchema($v1, array($e1, 'e1' => $e1, 'e2' => $e2));
$es = new ErrorSchema($v1, array($e1, 'e1' => $e1, 'e2' => $es1));
$es->addError($e2, 'e1');
$t->is($es->getCode(), 'max_length e1 [max_length min_length] e2 [max_length e1 [max_length] e2 [min_length]]', '->addError() adds an error to the error schema');
$es->addError($e2);
$t->is($es->getCode(), 'max_length min_length e1 [max_length min_length] e2 [max_length e1 [max_length] e2 [min_length]]', '->addError() adds an error to the error schema');
$es->addError($es1, 'e3');
$t->is($es->getCode(), 'max_length min_length e1 [max_length min_length] e2 [max_length e1 [max_length] e2 [min_length]] e3 [max_length e1 [max_length] e2 [min_length]]', '->addError() adds an error to the error schema');
$es->addError($es1);
$t->is($es->getCode(), 'max_length min_length max_length e1 [max_length min_length max_length] e2 [max_length min_length e1 [max_length] e2 [min_length]] e3 [max_length e1 [max_length] e2 [min_length]]', '->addError() adds an error to the error schema');

$es = new ErrorSchema($v1, array($e1, 'e1' => $e1, 'e2' => $es1));
$es2 = new ErrorSchema($v1, array($e1, 'e1' => $e1, 'e2' => $es1));
$es->addError($es2, 'e2');
$t->is($es->getCode(), 'max_length e1 [max_length] e2 [max_length max_length e1 [max_length max_length] e2 [min_length max_length e1 [max_length] e2 [min_length]]]', '->addError() adds an error to the error schema');

// ->addErrors()
$t->diag('->addErrors()');
$es1 = new ErrorSchema($v1);
$es1->addError($e1);
$es1->addError($e2, '1');
$es = new ErrorSchema($v1);
$es->addErrors($es1);
$t->is($es->getGlobalErrors(), array($e1), '->addErrors() adds an array of errors to the current error');
$t->is($es->getNamedErrors(), array('1' => $e2), '->addErrors() merges a ErrorSchema to the current error');

// ->getGlobalErrors()
$t->diag('->getGlobalErrors()');
$e = new ErrorSchema($v1);
$e->addError($e1);
$e->addError($e2, 'e2');
$e->addError($e1, '2');
$t->is($e->getGlobalErrors(), array($e1), '->getGlobalErrors() returns all globals/non named errors');

// ->getNamedErrors()
$t->diag('->getNamedErrors()');
$t->is($e->getNamedErrors(), array('e2' => $e2, '2' => $e1), '->getNamedErrors() returns all named errors');

// ->getValue()
$t->diag('->getValue()');
$t->is($e->getValue(), null, '->getValue() always returns null');

// ->getArguments()
$t->diag('->getArguments()');
$t->is($e->getArguments(), array(), '->getArguments() always returns an empty array');
$t->is($e->getArguments(true), array(), '->getArguments() always returns an empty array');

// ->getMessageFormat()
$t->diag('->getMessageFormat()');
$t->is($e->getMessageFormat(), '', '->getMessageFormat() always returns an empty string');

// ->getMessage()
$t->diag('->getMessage()');
$t->is($e->getMessage(), '"foo" is too long (1 characters max). e2 ["bar" is too short (5 characters min).] 2 ["foo" is too long (1 characters max).]', '->getMessage() returns the error message string');

// ->getCode()
$t->diag('->getCode()');
$t->is($e->getCode(), 'max_length e2 [min_length] 2 [max_length]', '->getCode() returns the error code');

// implements \Countable
$t->diag('implements \Countable');
$e = new ErrorSchema($v1, array('e1' => $e1, 'e2' => $e2));
$t->is(count($e), 2, '"Error" implements \Countable');

// implements \Iterator
$t->diag('implements \Iterator');
$e = new ErrorSchema($v1, array('e1' => $e1, $e2));
$e->addError($e2, '2');
$errors = array();
foreach ($e as $name => $error)
{
  $errors[$name] = $error;
}
$t->is($errors, array('e1' => $e1, 0 => $e2, '2' => $e2), 'ErrorSchema implements the \Iterator interface');

// implements \ArrayAccess
$t->diag('implements \ArrayAccess');
$e = new ErrorSchema($v1, array('e1' => $e1, $e2));
$e->addError($e2, '2');
$t->is($e['e1'], $e1, 'ErrorSchema implements the \ArrayAccess interface');
$t->is($e[0], $e2, 'ErrorSchema implements the \ArrayAccess interface');
$t->is($e['2'], $e2, 'ErrorSchema implements the \ArrayAccess interface');
$t->is(isset($e['e1']), true, 'ErrorSchema implements the \ArrayAccess interface');
$t->is(isset($e['e2']), false, 'ErrorSchema implements the \ArrayAccess interface');
try
{
  $e['e1'] = $e2;
  $t->fail('ErrorSchema implements the \ArrayAccess interface');
}
catch (\LogicException $e)
{
  $t->pass('ErrorSchema implements the \ArrayAccess interface');
}

// implements \Serializable
$t->diag('implements \Serializable');

class NotSerializable implements \Serializable
{
  public function serialize()
  {
    throw new \Exception('Not serializable');
  }

  public function unserialize($serialized)
  {
    throw new \Exception('Not serializable');
  }
}

function will_crash($a)
{
  return serialize(new ErrorSchema(new String()));
}

$a = new NotSerializable();

try
{
  $serialized = will_crash($a);
  $t->pass('ErrorSchema implements \Serializable');
}
catch (Exception $e)
{
  $t->fail('ErrorSchema implements \Serializable');
}

$e = new ErrorSchema($v1);
$e1 = unserialize($serialized);
$t->is($e1->getMessage(), $e->getMessage(), 'ErrorSchema implements \Serializable');
$t->is($e1->getCode(), $e->getCode(), 'ErrorSchema implements \Serializable');
$t->is(get_class($e1->getValidator()), get_class($e->getValidator()), 'ErrorSchema implements \Serializable');
$t->is($e1->getArguments(), $e->getArguments(), 'ErrorSchema implements \Serializable');
$t->is($e1->getNamedErrors(), $e->getNamedErrors(), 'ErrorSchema implements \Serializable');
$t->is($e1->getGlobalErrors(), $e->getGlobalErrors(), 'ErrorSchema implements \Serializable');
