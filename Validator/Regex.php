<?php

namespace Bundle\sfFormBundle\Validator;

use Bundle\sfFormBundle\Tool\Callable;


/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Regex validates a value with a regular expression.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Regex.class.php 22149 2009-09-18 14:09:53Z Kris.Wallsmith $
 */
class Regex extends String
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * pattern:    A regex pattern compatible with PCRE or {@link Callable} that returns one (required)
   *  * must_match: Whether the regex must match or not (true by default)
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see String
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addRequiredOption('pattern');
    $this->addOption('must_match', true);
  }

  /**
   * @see String
   */
  protected function doClean($value)
  {
    $clean = parent::doClean($value);

    $pattern = $this->getPattern();

    if (
      ($this->getOption('must_match') && !preg_match($pattern, $clean))
      ||
      (!$this->getOption('must_match') && preg_match($pattern, $clean))
    )
    {
      throw new Error($this, 'invalid', array('value' => $value));
    }

    return $clean;
  }

  /**
   * Returns the current validator's regular expression.
   *
   * @return string
   */
  public function getPattern()
  {
    $pattern = $this->getOption('pattern');

    return $pattern instanceof Callable ? $pattern->call() : $pattern;
  }
}
