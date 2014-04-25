<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

abstract class JCmsControllerBase extends JControllerBase
{
	/**
	 * Configuration variables
	 * @var array
	 */
	protected $config;
	
	/**
	 * Associative array of models
	 * stored as $models[$prefix][$name] used by get models
	 * @var array
	 */
	protected $models = array();
	
	/**
	 * URL for redirection.
	 *
	 * @var    string
	 * @since  12.2
	 * @note   Replaces _redirect.
	*/
	protected $redirect;
	
	/**
	 * Redirect message.
	 *
	 * @var    string
	 * @since  12.2
	 * @note   Replaces _message.
	 */
	protected $message;
	
	/**
	 * Redirect message type.
	 *
	 * @var    string
	 * @since  12.2
	 * @note   Replaces _messageType.
	 */
	protected $messageType;
	
	/**
	 * Instantiate the controller.
	 *
	 * @param   JInput            $input  The input object.
	 * @param   JApplicationBase  $app    The application object.
	 * @param   array             $config Configuration
	 * @since  12.1
	 */
	public function __construct(JInput $input, $app = null, $config = array())
	{
		parent::__construct($input, $app);
		
		if (!array_key_exists('option', $config))
		{
			$config['option'] = $input->get('option');
		}
		
		$this->config = $config;
	}
	
	/**
	 * Method to check the session token
	 * @return void
	 */
	protected function validateSession()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	}
	
	/**
	 * Method to refresh the session token to prevent the back button
	 * @return void
	 */
	protected function refreshToken()
	{
		$session = JFactory::getSession();
		$session->getToken(true);
	}
	
	/**
	 * Method to translate a string using JText::_() method
	 * @param string $string
	 * @return string translation result
	 */
	protected function translate($string)
	{
		return JText::_($string);
	}
	
	/**
	 * Method to get the option prefix from the input
	 * @return string ucfirst(substr($this->config['option'], 4));
	 */
	protected function getPrefix()
	{
		$prefix = ucfirst(substr($this->config['option'], 4));
	
		return $prefix;
	}
	
	/**
	 * Method to get a model, creating it if it doesn't already exist.
	 * @param string $prefix
	 * @param string $name
	 * @param array $config
	 * @return JCmsModelBase
	 * @throws ErrorException
	 */
	public function getModel($prefix, $name, $config = array())
	{
		$prefix = ucfirst($prefix);
		$name = ucfirst($name);
	
		if (isset($this->models[$prefix][$name]))
		{
			return $this->models[$prefix][$name];
		}
	
		$class = $prefix.'Model'.$name;
	
		if (!class_exists($class))
		{
			throw new ErrorException(JText::sprintf('JLIB_APPLICATION_ERROR_MODELCLASS_NOT_FOUND', $class));
			return false;
		}
	
		$config = $this->normalizeConfig($config);
		
		$this->models[$prefix][$name] = new $class($config);
	
		return $this->models[$prefix][$name];
	}
	
	/**
	 * Method to insure all config variables are are included.
	 * Intended to be used in getModel, getView and other factory methods 
	 * that can be passed a config array.
	 * @param array $config to normalize
	 */
	protected function normalizeConfig($config)
	{
		//Safe merge. will not overwrite existing keys
		$config += $this->config;
	}
	
	/**
	 * Redirects the browser or returns false if no redirect is set.
	 * @return  boolean  False if no redirect exists.
	 *
	 * @since   12.2
	 */
	public function redirect()
	{
		if ($this->hasRedirect())
		{
			$app = $this->app;
	
			// Enqueue the redirect message
			$app->enqueueMessage($this->message, $this->messageType);
	
			// Execute the redirect
			$app->redirect($this->redirect);
		}
	
		return false;
	}
	
	/**
	 * Method to check if the controller has a redirect
	 * @return boolean
	 */
	public function hasRedirect()
	{
		if (!empty($this->redirect))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Set a URL for browser redirection.
	 *
	 * @param   string  $url   URL to redirect to.
	 * @param   string  $msg   Message to display on redirect. Optional.
	 * @param   string  $type  Message type. Optional, defaults to 'message'.
	 * @param   bool    $useJRoute should we phrase the url with JRoute?
	 *
	 * @return  $this  Object to support chaining.
	 *
	 * @since   12.2
	 */
	public function setRedirect($url, $msg = null, $type = 'message', $useJRoute = true)
	{
		if ($useJRoute)
		{
			$this->redirect = JRoute::_($url, false);
		}
		else
		{
			$this->redirect = $url;
		}
	
		if ($msg !== null)
		{
			$this->message = $msg;
		}
	
		$this->messageType = $type;
	
		return $this;
	}
	
	/**
	 * Method to cast all values in a cid array to integer values
	 * @param array $cid
	 * @return array $cleanCid
	 */
	protected function cleanCid($cid)
	{
		$cleanCid = array();
		foreach ((array)$cid AS $id)
		{
			$cleanCid[] = (int)$id;
		}
		return $cleanCid;
	}
	
}