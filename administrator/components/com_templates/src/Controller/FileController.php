<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Templates\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Input\Input;

/**
 * Template style controller class.
 *
 * @since  1.6
 */
class FileController extends BaseController
{
	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @since  1.6
	 * @see    BaseController
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * Method for closing the template.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function cancel()
	{
		$this->setRedirect(Route::_('index.php?option=com_templates&view=templates', false));
	}

	/**
	 * Method for closing a file.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function close()
	{
		$file = base64_encode('home');
		$id   = $this->input->get('id');
		$url  = 'index.php?option=com_templates&view=template&id=' . $id . '&file=' . $file;
		$this->setRedirect(Route::_($url, false));
	}

	/**
	 * Load data in a format for grapes.js
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function load()
	{
		// Send json mime type.
		$this->app->mimeType = 'application/json';
		$this->app->setHeader('Content-Type', $this->app->mimeType . '; charset=' . $this->app->charSet);
		$this->app->sendHeaders();

		// Check if user token is valid.
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
			echo new JsonResponse;
			$this->app->close();
		}

		// Check if the user is authorized to do this.
		if (!$this->app->getIdentity()->authorise('core.admin'))
		{
			$this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			echo new JsonResponse;
			$this->app->close();
		}

		/** @var \Joomla\Component\Templates\Administrator\Model\TemplateModel $model */
		$table = $this->getModel()->getTable('PagebuilderData');
		$result = $table->load(
			[
				'extension_id' => $this->input->getInt('id'),
				'file_name' => base64_decode($this->input->getBase64('file')),
			]
		);

		// If we have a match return the response. Else return the file contents if it exists.
		if ($result)
		{
			$response = [
				'gjs-components' => json_decode($table->{'gjs-components'}),
				'gjs-assets' => json_decode($table->{'gjs-assets'}),
				// TODO: Unsure if these should be saved + returned or not. For now left out as storing HTML/CSS to DB
				//       is not ideal!
//				'gjs-html' => $table->{'gjs-html'},
//				'gjs-css' => $table->{'gjs-css'},
			];
		}
		else
		{
			$response = [
				'html' => $model->getSource()->source,
			];
		}

		echo json_encode($response);

		$this->app->close();
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional (note, the empty array is atypical compared to other models).
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel  The model.
	 *
	 * @since   3.2
	 */
	public function getModel($name = 'Template', $prefix = 'Administrator', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @return  boolean
	 *
	 * @since   3.2
	 */
	protected function allowEdit()
	{
		return $this->app->getIdentity()->authorise('core.admin');
	}

	/**
	 * Saves a template source file.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function save()
	{
		// Send json mime type.
		$this->app->mimeType = 'application/json';
		$this->app->setHeader('Content-Type', $this->app->mimeType . '; charset=' . $this->app->charSet);
		$this->app->sendHeaders();

		// Check if user token is valid.
		if (!Session::checkToken('post'))
		{
			$this->app->setHeader('status', 403, true);
			$this->app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
			echo new JsonResponse;
			$this->app->close();
		}

		/** @var \Joomla\Component\Templates\Administrator\Model\TemplateModel $model */
		$model        = $this->getModel();
		$fileName     = $this->input->get->get('file');
		$explodeArray = explode(':', base64_decode($fileName));

		// Access check.
		if (!$this->allowEdit())
		{
			$this->app->setHeader('status', 403, true);
			$this->app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');
			echo new JsonResponse;
			$this->app->close();

			return;
		}

		// TODO: Save the CSS somewhere!
		$data = [
			'extension_id' => $this->input->get->get('id'),
			'filename' => end($explodeArray),
			'source' => $this->input->json->get('gjs-html', null, 'raw'),
			'gjs-assets' => $this->input->json->get('gjs-assets', null, 'raw'),
			'gjs-components' => $this->input->json->get('gjs-components', null, 'raw'),
			'gjs-styles' => $this->input->json->get('gjs-styles', null, 'raw'),
		];

		// Match the stored id's with the submitted.
		if (empty($data['extension_id']) || empty($data['filename']))
		{
			$this->app->setHeader('status', 400, true);
			$this->app->enqueueMessage(Text::_('COM_TEMPLATES_ERROR_SOURCE_ID_FILENAME_MISMATCH'), 'error');
			echo new JsonResponse;
			$this->app->close();

			return;
		}
		elseif ($data['extension_id'] != $model->getState('extension.id'))
		{
			$this->app->setHeader('status', 400, true);
			$this->app->enqueueMessage(Text::_('COM_TEMPLATES_ERROR_SOURCE_ID_FILENAME_MISMATCH'), 'error');
			echo new JsonResponse;
			$this->app->close();

			return;
		}

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			$this->app->setHeader('status', 500, true);
			$this->setMessage($model->getError(), 'error');

			return;
		}

		$data = $model->validate($form, $data);

		// TODO: Manipulate the Model so we actually have proper validation!
		$data = [
			'extension_id' => $this->input->get->get('id'),
			'filename' => end($explodeArray),
			'source' => $this->input->json->get('gjs-html', null, 'raw'),
			'gjs-assets' => $this->input->json->get('gjs-assets', null, 'raw'),
			'gjs-components' => $this->input->json->get('gjs-components', null, 'raw'),
			'gjs-styles' => $this->input->json->get('gjs-styles', null, 'raw'),
		];

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof \Exception)
				{
					$this->app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$this->app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$this->app->setHeader('status', 400, true);
			echo new JsonResponse;
			$this->app->close();

			return;
		}

		// Attempt to save the data.
		if (!$model->save($data))
		{
			$this->app->setHeader('status', 500, true);
			$this->app->enqueueMessage(Text::sprintf('JERROR_SAVE_FAILED', $model->getError()), 'warning');
			echo new JsonResponse;
			$this->app->close();

			return;
		}

		// Success
		$this->app->enqueueMessage(Text::_('COM_TEMPLATES_FILE_SAVE_SUCCESS'));
		echo new JsonResponse;
		$this->app->close();
	}
}
