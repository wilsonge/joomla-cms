<?php
/**
 * Bootstrap file for the Joomla Platform.  Including this file into your application will make Joomla
 * Platform libraries available for use.
 *
 * @package    Joomla.Platform
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

trigger_error(
	sprintf(
		'Bootstrapping Joomla using the %1$s file is deprecated.  Use %2$s instead.',
		__FILE__,
		__DIR__ . '/bootstrap.php'
	),
	E_USER_DEPRECATED
);

// Set the platform root path as a constant if necessary.
if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', __DIR__);
}

// Detect the native operating system type.
$os = strtoupper(substr(PHP_OS, 0, 3));

if (!defined('IS_WIN'))
{
	define('IS_WIN', ($os === 'WIN') ? true : false);
}

if (!defined('IS_UNIX'))
{
	define('IS_UNIX', (IS_WIN === false) ? true : false);
}

// Import the platform version library if necessary.
if (!class_exists('JPlatform'))
{
	require_once JPATH_PLATFORM . '/platform.php';
}

// Import the library loader if necessary.
if (!class_exists('JLoader'))
{
	require_once JPATH_PLATFORM . '/loader.php';
}

// Make sure that the Joomla Platform has been successfully loaded.
if (!class_exists('JLoader'))
{
	throw new RuntimeException('Joomla Platform not loaded.');
}

// Setup the autoloaders.
JLoader::setup();

// Check if the JsonSerializable interface exists already
if (!interface_exists('JsonSerializable'))
{
	JLoader::register('JsonSerializable', JPATH_PLATFORM . '/vendor/joomla/compat/src/JsonSerializable.php');
}

// Register classes that don't follow one file per class naming conventions.
JLoader::register('JAuthenticationResponse',  JPATH_PLATFORM . '/joomla/user/response.php');
JLoader::register('JAuthentication',  JPATH_PLATFORM . '/joomla/user/authentication.php');
JLoader::register('JText', JPATH_PLATFORM . '/joomla/language/text.php');
JLoader::register('JRoute', JPATH_PLATFORM . '/joomla/application/route.php');

// Register the PasswordHash lib
JLoader::register('PasswordHash', JPATH_PLATFORM . '/phpass/PasswordHash.php');
