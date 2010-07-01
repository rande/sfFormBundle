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

require_once $_src_path.'/src/Symfony/src/Symfony/Foundation/UniversalClassLoader.php';
$loader = new Symfony\Foundation\UniversalClassLoader();
$loader->registerNamespace('Symfony', $_src_path.'/src/Symfony/src');
$loader->register();


$h = new lime_harness(new lime_output_color());
$h->base_dir = realpath(dirname(__FILE__));

$finder = new Symfony\Components\Finder\Finder();
$finder = $finder->files()->exclude('fixtures')->name('*Test.php')->in($h->base_dir.'/unit');

$files = array();
foreach($finder as $file)
{
  $files[] = $file->getPathname();
}

$h->register($files);

exit($h->run() ? 0 : 1);