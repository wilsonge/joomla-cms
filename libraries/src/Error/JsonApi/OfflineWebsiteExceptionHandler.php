<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Error\JsonApi;

\defined('JPATH_PLATFORM') or die;

use Exception;
use Joomla\CMS\Application\Exception\OfflineWebsiteException;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

/**
 * Handler for the site being in offline mode
 *
 * @since  4.0.0
 */
class OfflineWebsiteExceptionHandler implements ExceptionHandlerInterface
{
	/**
	 * If the exception handler is able to format a response for the provided exception,
	 * then the implementation should return true.
	 *
	 * @param   \Exception  $e  The exception to be handled
	 *
	 * @return boolean
	 *
	 * @since  4.0.0
	 */
	public function manages(Exception $e)
	{
		return $e instanceof OfflineWebsiteException;
	}

	/**
	 * Handle the provided exception.
	 *
	 * @param   Exception  $e  The exception being handled
	 *
	 * @return  \Tobscure\JsonApi\Exception\Handler\ResponseBag
	 *
	 * @since  4.0.0
	 */
	public function handle(Exception $e)
	{
		$status = 503;
		$error = ['title' => $e->getMessage()];

		$code = $e->getCode();

		if ($code)
		{
			$error['code'] = $code;
		}

		return new ResponseBag($status, [$error]);
	}
}
