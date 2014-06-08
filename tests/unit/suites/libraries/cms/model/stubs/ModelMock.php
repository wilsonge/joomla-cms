<?php
/**
 * @package    Joomla.UnitTest
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

/**
 * Mock class for JModel.
 *
 * @package  Joomla.UnitTest
 * @since    12.1
 */
class JModelCmsMock
{
	/**
	 * Creates and instance of the mock JModel object.
	 *
	 * @param   object  $test  A test object.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public static function create($test)
	{
		// Collect all the relevant methods in JModelCmsInterface.
		$methods = array(
			'getState',
			'setState',
			'getName',
			'getData',
		);

		// Create the mock.
		$mockObject = $test->getMock(
			'JModelCms',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		return $mockObject;
	}
}
