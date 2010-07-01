<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/bootstrap.php');

use Bundle\sfFormBundle\Field;
use Bundle\sfFormBundle\FieldSchema;
use Bundle\sfFormBundle\Widget\Form as WidgetForm;
use Bundle\sfFormBundle\Widget\Schema as WidgetSchema;
use Bundle\sfFormBundle\Widget\InputText as WidgetInputText;
use Bundle\sfFormBundle\Widget\InputHidden as WidgetInputHidden;
use Bundle\sfFormBundle\Validator\Error as ValidatorError;
use Bundle\sfFormBundle\Validator\String as ValidatorString;
use Bundle\sfFormBundle\Validator\ErrorSchema as ValidatorErrorSchema;


$t = new lime_test(11);

// widgets
$authorSchema = new WidgetSchema(array(
  'name' => $nameWidget = new WidgetInputText(),
));
$authorSchema->setNameFormat('article[author][%s]');

$schema = new WidgetSchema(array(
  'title'  => $titleWidget = new WidgetInputText(),
  'author' => $authorSchema,
));
$schema->setNameFormat('article[%s]');

// errors
$authorErrorSchema = new ValidatorErrorSchema(new ValidatorString());
$authorErrorSchema->addError(new ValidatorError(new ValidatorString(), 'name error'), 'name');

$articleErrorSchema = new ValidatorErrorSchema(new ValidatorString());
$articleErrorSchema->addError($titleError = new ValidatorError(new ValidatorString(), 'title error'), 'title');
$articleErrorSchema->addError($authorErrorSchema, 'author');

$parent = new FieldSchema($schema, null, 'article', array('title' => 'symfony', 'author' => array('name' => 'Fabien')), $articleErrorSchema);
$f = $parent['title'];
$child = $parent['author'];

// \ArrayAccess interface
$t->diag('ArrayAccess interface');
$t->is(isset($parent['title']), true, 'sfFormField implements the \ArrayAccess interface');
$t->is(isset($parent['title1']), false, 'sfFormField implements the \ArrayAccess interface');
$t->is($parent['title'], $f, 'sfFormField implements the \ArrayAccess interface');
try
{
  unset($parent['title']);
  $t->fail('Field implements the \ArrayAccess interface but in read-only mode');
}
catch (\LogicException $e)
{
  $t->pass('Field implements the \ArrayAccess interface but in read-only mode');
}

try
{
  $parent['title'] = null;
  $t->fail('Field implements the \ArrayAccess interface but in read-only mode');
}
catch (\LogicException $e)
{
  $t->pass('Field implements the \ArrayAccess interface but in read-only mode');
}

try
{
  $parent['title1'];
  $t->fail('Field implements the \ArrayAccess interface but in read-only mode');
}
catch (\LogicException $e)
{
  $t->pass('Field implements the \ArrayAccess interface but in read-only mode');
}

// implements \Countable
$t->diag('implements \Countable');
$widgetSchema = new WidgetSchema(array(
  'w1' => $w1 = new WidgetInputText(),
  'w2' => $w2 = new WidgetInputText(),
));
$f = new FieldSchema($widgetSchema, null, 'article', array());
$t->is(count($f), 2, 'FormSchema implements the \Countable interface');

// implements \Iterator
$t->diag('implements \Iterator');
$f = new FieldSchema($widgetSchema, null, 'article', array());

$values = array();
foreach ($f as $name => $value)
{
  $values[$name] = $value;
}
$t->is(isset($values['w1']), true, 'FormSchema implements the \Iterator interface');
$t->is(isset($values['w2']), true, 'FormSchema implements the \Iterator interface');
$t->is(count($values), 2, 'FormSchema implements the \Iterator interface');

$t->diag('implements \Iterator respecting the order of fields');
$widgetSchema->moveField('w2', 'first');
$f = new FieldSchema($widgetSchema, null, 'article', array());

$values = array();
foreach ($f as $name => $value)
{
  $values[$name] = $value;
}
$t->is(array_keys($values), array('w2', 'w1'), 'FormSchema keeps the order');
