<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\sfFormBundle\Validator\DateRange;
use Bundle\sfFormBundle\Validator\Date;
use Bundle\sfFormBundle\Validator\Error;
use Bundle\sfFormBundle\Validator\ErrorSchema;

$t = new lime_test(7);

try
{
  new DateRange();
  $t->fail('__construct() throws a Error if you don\'t pass a from_date and a to_date option');
  $t->skip('', 1);
}
catch (\RuntimeException $e)
{
  $t->pass('__construct() throws a Error if you don\'t pass a from_date and a to_date option');
  $t->is($e->getCode(), 0 /* rande, cannot be 'invalid' */, '->clean() throws a Error');
}

$v = new DateRange(array(
  'from_date' => new Date(array('required' => false)),
  'to_date' => new Date(array('required' => false))
));

// ->clean()
$t->diag('->clean()');

$values = $v->clean(array('from' => '2008-01-01', 'to' => '2009-01-01'));
$t->is($values, array('from' => '2008-01-01', 'to' => '2009-01-01'), '->clean() returns the from and to values');

try
{
  $v->clean(array('from' => '2008-01-01', 'to' => '1998-01-01'));
  $t->fail('->clean() throws a Error if the from date is after the to date');
  $t->skip('', 1);
}
catch (Error $e)
{
  $t->pass('->clean() throws a Error if the from date is after the to date');
  $t->is($e->getCode(), 'invalid', '->clean() throws a Error');
}

// custom field names
$t->diag('Custom field names options');

$v = new DateRange(array(
  'from_date'  => new Date(array('required' => true)),
  'to_date'    => new Date(array('required' => true)),
  'from_field' => 'custom_from',
  'to_field'   => 'custom_to',
));

try
{
  $v->clean(array('from' => '2008-01-01', 'to' => '1998-01-01'));
  $t->fail('->clean() take into account custom fields');
}
catch (Error $e)
{
  $t->pass('->clean() take into account custom fields');
}

$values = $v->clean(array('custom_from' => '2008-01-01', 'custom_to' => '2009-01-01'));
$t->is($values, array('custom_from' => '2008-01-01', 'custom_to' => '2009-01-01'), '->clean() returns the from and to values for custom field names');
