<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\sfFormBundle\Validator\Pass;

$t = new lime_test(2);

$v = new Pass();

// ->clean()
$t->diag('->clean()');
$t->is($v->clean(''), '', '->clean() always returns the value unmodified');
$t->is($v->clean(null), null, '->clean() always returns the value unmodified');
