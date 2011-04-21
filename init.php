<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package    Leemo/Release
 * @author     Alexey Popov
 * @copyright  (c) 2011 Leemo Studio
 * @license    http://www.opensource.org/licenses/bsd-license.php
 */
Route::set('release', 'release(/<action>(/<file>))', array('file' => '.+'))
	->defaults(array(
		'controller' => 'release',
		'action'     => 'index',
		'file'       => NULL
	));