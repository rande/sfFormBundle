<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\FormBundle\Widget\InputText;
use Bundle\FormBundle\Widget\Schema;
use Bundle\FormBundle\Widget\SchemaDecorator;

$t = new lime_test(16);

$w1 = new InputText();
$w2 = new InputText();
$ws = new Schema(array('w1' => $w1));

$w = new SchemaDecorator($ws, "<table>\n%content%</table>");

// ->getWidget()
$t->diag('->getWidget()');
$t->is($w->getWidget(), $ws, '->getWidget() returns the decorated widget');

// ->render()
$t->diag('->render()');
$output = <<<EOF
<table>
<tr>
  <th><label for="w1">W1</label></th>
  <td><input type="text" name="w1" id="w1" /></td>
</tr>
</table>
EOF;
$t->is($w->render(null), fix_linebreaks($output), '->render() decorates the widget');

// implements \ArrayAccess
$t->diag('implements \ArrayAccess');
$w['w2'] = $w2;
$w1->setParent($ws); $w2->setParent($ws);
$t->ok($w->getFields() == array('w1' => $w1, 'w2' => $w2), 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->ok($ws->getFields() == array('w1' => $w1, 'w2' => $w2), 'SchemaDecorator implements the \ArrayAccess interface for the fields');

try
{
  $w['w1'] = 'string';
  $t->fail('SchemaDecorator implements the \ArrayAccess interface for the fields');
}
catch (\LogicException $e)
{
  $t->pass('SchemaDecorator implements the \ArrayAccess interface for the fields');
}

$w = new SchemaDecorator($ws, "<table>\n%content%</table>");
$t->is(isset($w['w1']), true, 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->is(isset($w['w2']), true, 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->is(isset($ws['w1']), true, 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->is(isset($ws['w2']), true, 'SchemaDecorator implements the \ArrayAccess interface for the fields');

$w = new SchemaDecorator($ws, "<table>\n%content%</table>");
$t->ok($w['w1'] == $w1, 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->ok($w['w2'] == $w2, 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->ok($ws['w1'] == $w1, 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->ok($ws['w2'] == $w2, 'SchemaDecorator implements the \ArrayAccess interface for the fields');

$w = new SchemaDecorator($ws, "<table>\n%content%</table>");
unset($w['w1']);
$t->is($w['w1'], null, 'SchemaDecorator implements the \ArrayAccess interface for the fields');
$t->is($ws['w1'], null, 'SchemaDecorator implements the \ArrayAccess interface for the fields');

// __clone()
$t->diag('__clone()');
$w1 = clone $w;
$t->ok($w1->getWidget() !== $w->getWidget(), '__clone() clones the embedded widget');
//$t->ok($w1->getWidget() == $w->getWidget(), '__clone() clones the embedded widget');
