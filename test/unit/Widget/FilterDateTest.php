<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\FormBundle\Widget\DateTime;
use Bundle\FormBundle\Widget\SchemaFormatter;
use Bundle\FormBundle\Widget\Widget;
use Bundle\FormBundle\Widget\Schema;
use Bundle\FormBundle\Widget\FilterDate;

use Bundle\FormBundle\Tool\Callable;
use Bundle\FormBundle\Tool\DomCssSelector;


class FormFormatterStub extends SchemaFormatter
{
  public function __construct() {}

  public function translate($subject, $parameters = array())
  {
    return sprintf('translation[%s]', $subject);
  }
}

class WidgetFormStub extends Widget
{
  public function __construct() {}

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return sprintf('##%s##', __CLASS__);
  }
}

$t = new lime_test(1);

$dom = new \DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->render()
$t->diag('->render()');

$ws = new Schema();
$ws->addFormFormatter('stub', new FormFormatterStub());
$ws->setFormFormatterName('stub');
$w = new FilterDate(array('from_date' => new WidgetFormStub(), 'to_date' => new WidgetFormStub()));
$w->setParent($ws);
$dom->loadHTML($w->render('foo'));
$css = new DomCssSelector($dom);
$t->is($css->matchSingle('label[for="foo_is_empty"]')->getValue(), 'translation[is empty]', '->render() translates the empty_label option');

