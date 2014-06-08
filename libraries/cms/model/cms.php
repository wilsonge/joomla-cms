<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * JLayout class for rendering output.
 *
 * @since  3.4
 */
class JModelCms extends JModelDatabase implements JModelCmsInterface
{
	/**
	 * The name of the model.
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $name;

	/**
	 * Gets the data from the model for use in the view.
	 *
	 * @return  array  The array of data from the model
	 *
	 * @since   3.4
	 */
	public function getData()
	{
		return array();
	}

	/**
	 * Method to get the model name
	 *
	 * The model name by default parsed using the classname
	 *
	 * @return  string  The name of the model
	 *
	 * @since   3.4
	 * @throws  RuntimeException
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$className = get_class($this);
			$viewPos = strpos($className, 'Model');

			if ($viewPos === false)
			{
				throw new RuntimeException(JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}

			$lastPart = substr($className, $viewPos + 5);
			$pathParts = explode(' ', JStringNormalise::fromCamelCase($lastPart));

			if (!empty($pathParts[1]))
			{
				$this->name = strtolower($pathParts[0]);
			}
			else
			{
				$this->name = strtolower($lastPart);
			}
		}

		return $this->name;
	}
}
