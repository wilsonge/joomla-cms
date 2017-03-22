<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Legacy routing rules class from com_content
 *
 * @since  __DEPLOY_VERSION__
 */
class ContentRouterRulesLegacyParser implements JComponentRouterRulesInterface
{
	/**
	 * The router this rule belongs to
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $router = null;

	/**
	 * Constructor for this legacy router
	 *
	 * @param   JComponentRouterView  $router  The router this rule belongs to
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($router)
	{
		$this->router = $router;
	}

	/**
	 * Preprocess the route for the com_content component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function preprocess(&$query)
	{
	}

	/**
	 * Build the route for the com_content component
	 *
	 * @param   array  &$query     An array of URL arguments
	 * @param   array  &$segments  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function build(&$query, &$segments)
	{
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @param   array  &$vars      The URL attributes to be used by the application.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function parse(&$segments, &$vars)
	{
		/*
		 * If there was more than one segment, then we can determine where the URL points to
		 * because the first segment will have the target category id prepended to it.  If the
		 * last segment has a number prepended, it is an article, otherwise, it is a category.
		 */
		$cat_id = (int) $segments[0];

		$article_id = (int) $segments[count($segments) - 1];

		if ($article_id > 0)
		{
			$vars['view'] = 'article';
			$vars['catid'] = $cat_id;
			$vars['id'] = $article_id;
		}
		else
		{
			$vars['view'] = 'category';
			$vars['id'] = $cat_id;
		}

		/**
		 * We don't validate intermediate segments. So at this point we assume we're done
		 * and remove all segments from the array. Note this means that lots of incorrect URLs
		 * will still parse. But that's the price of some stupid settings in the legacy router
		 */
		$segments = array();
	}
}
