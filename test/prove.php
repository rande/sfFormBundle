<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$_src_path = __DIR__.'/../../..';
require_once $_src_path.'/Bundle/Lime2Bundle/LimeAutoloader.php';
LimeAutoloader::enableLegacyMode();
LimeAutoloader::register();

require_once $_src_path.'/vendor/symfony/src/Symfony/Foundation/ClassLoader.php';
$loader = new Symfony\Foundation\ClassLoader();
$loader->registerNamespace('Symfony', $_src_path.'/vendor/symfony/src');
$loader->register();


$h = new lime_harness(new lime_output_color());
$h->base_dir = realpath(dirname(__FILE__));

$h->register(Symfony\Framework\WebBundle\Util\Finder::type('file')->prune('fixtures')->name('*Test.php')->in(array(
  // unit tests
  $h->base_dir.'/unit',
)));

exit($h->run() ? 0 : 1);