<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$_src_path = __DIR__.'/../../../..';
require_once $_src_path.'/Bundle/Lime2Bundle/LimeAutoloader.php';
LimeAutoloader::enableLegacyMode();
LimeAutoloader::register();

require_once $_src_path.'/vendor/symfony/src/Symfony/Foundation/ClassLoader.php';
$loader = new Symfony\Foundation\ClassLoader();
$loader->registerNamespace('Symfony', $_src_path.'/vendor/symfony/src');
$loader->registerNamespace('Bundle', $_src_path);
$loader->register();

// Helper for cross platform testcases that validate output
function fix_linebreaks($content)
{
  return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
}