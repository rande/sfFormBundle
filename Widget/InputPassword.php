<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\sfFormBundle\Widget;

/**
 * sfWidgetFormInputPassword represents a password HTML input tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormInputPassword.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class InputPassword extends Input
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * always_render_empty: true if you want the input value to be always empty when rendering (true by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('always_render_empty', true);

    $this->setOption('type', 'password');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The password stored in this widget, will be masked by the browser.
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return parent::render($name, $this->getOption('always_render_empty') ? null : $value, $attributes, $errors);
  }
}
