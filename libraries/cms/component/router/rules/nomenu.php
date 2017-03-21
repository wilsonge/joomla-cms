<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Component
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Rule to process URLs without a menu item
 *
 * @since  3.4
 */
class JComponentRouterRulesNomenu implements JComponentRouterRulesInterface
{
	/**
	 * Router this rule belongs to
	 *
	 * @var JComponentRouterView
	 * @since 3.4
	 */
	protected $router;

	/**
	 * Class constructor.
	 *
	 * @param   JComponentRouterView  $router  Router this rule belongs to
	 *
	 * @since   3.4
	 */
	public function __construct(JComponentRouterView $router)
	{
		$this->router = $router;
	}

	/**
	 * Dummymethod to fullfill the interface requirements
	 *
	 * @param   array  &$query  The query array to process
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @codeCoverageIgnore
	 */
	public function preprocess(&$query)
	{
	}

	/**
	 * Parse a menu-less URL
	 *
	 * @param   array  &$segments  The URL segments to parse
	 * @param   array  &$vars      The vars that result from the segments
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function parse(&$segments, &$vars)
	{
		$active = $this->router->menu->getActive();
		$views = $this->router->getViews();

		if (!is_object($active))
		{
			if (isset($views[$segments[0]]))
			{
				$vars['view'] = array_shift($segments);

				if (isset($views[$vars['view']]->key) && isset($segments[0]))
				{
					$vars[$views[$vars['view']]->key] = preg_replace('/-/', ':', array_shift($segments), 1);
				}
			}
		}
		else
		{
			$viewName = $segments[0];
			unset($segments[0]);
			$segments = array_values($segments);
			$vars['view'] = $viewName;
			$view = $views[$viewName];
			$hasParent = count($segments) > 1;

			if ($hasParent)
			{
				if (is_callable(array($this->router, 'get' . ucfirst($view->parent->name) . 'Id')))
				{
					if ($view->parent->nestable)
					{
						for ($i = 0; $i < (count($segments) - 1); $i ++)
						{
							$vars[$view->parent->key] = call_user_func_array(array($this->router, 'get' . ucfirst($view->parent->name) . 'Id'), array($segments[$i], $vars));
							unset($segments[$i]);
						}
					}
					else
					{
						$vars[$view->parent->key] = call_user_func_array(array($this->router, 'get' . ucfirst($view->parent->name) . 'Id'), array($segments[0], $vars));
						unset($segments[0]);
					}
				}
			}

			$segments = array_values($segments);

			if (is_callable(array($this->router, 'get' . ucfirst($view->name) . 'Id')))
			{
				$itemKey = call_user_func_array(array($this->router, 'get' . ucfirst($view->name) . 'Id'), array($segments[0], $vars));

				if ($hasParent)
				{
					$vars[$view->parent_key] = $vars[$view->parent->key];
				}

				$vars[$view->key] = $itemKey;
				unset($segments[0]);
			}
		}
	}

	/**
	 * Build a menu-less URL
	 *
	 * @param   array  &$query     The vars that should be converted
	 * @param   array  &$segments  The URL segments to create
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function build(&$query, &$segments)
	{
		$menu_found = false;

		if (isset($query['Itemid']))
		{
			$item = $this->router->menu->getItem($query['Itemid']);

			if (!isset($query['option']) || ($item && $item->query['option'] == $query['option']))
			{
				$menu_found = true;
			}
		}

		if (!$menu_found && isset($query['view']))
		{
			$views = $this->router->getViews();
			if (isset($views[$query['view']]))
			{
				$view = $views[$query['view']];
				$segments[] = $query['view'];

				if ($view->key && isset($query[$view->key]))
				{
					if (is_callable(array($this->router, 'get' . ucfirst($view->name) . 'Segment')))
					{
						$result = call_user_func_array(array($this->router, 'get' . ucfirst($view->name) . 'Segment'), array($query[$view->key], $query));
						$segments[] = str_replace(':', '-', array_shift($result));
					}
					else
					{
						$segments[] = str_replace(':', '-', $query[$view->key]);
					}
					unset($query[$views[$query['view']]->key]);
				}
				unset($query['view']);
			}
		}

		// We're in the same component but cannot build from any existing menu item.
		// Let's build something as friendly as we can
		if ($menu_found && isset($query['view']))
		{

			$views = $this->router->getViews();
			if (isset($views[$query['view']]))
			{
				$segments[] = $query['view'];
				$view = $views[$query['view']];

				// If the view has a parent and we've had it supplied to us we want to prefix the item with the
				// parent slug(s??)
				if ($view->parent && isset($query[$view->parent_key]))
				{
					if (is_callable(array($this->router, 'get' . ucfirst($view->parent->name) . 'Segment')))
					{
						$result = call_user_func_array(array($this->router, 'get' . ucfirst($view->parent->name) . 'Segment'), array($query[$view->parent_key], $query));
						$segments[] = str_replace(':', '-', array_shift($result));
					}
					else
					{
						$segments[] = str_replace(':', '-', $query[$view->parent_key]);
					}

					unset($query[$view->parent_key]);
				}

				// Get the items slug to append
				if (is_callable(array($this->router, 'get' . ucfirst($view->name) . 'Segment')))
				{
					$result = call_user_func_array(array($this->router, 'get' . ucfirst($view->name) . 'Segment'), array($query[$view->key], $query));
					$segments[] = str_replace(':', '-', array_shift($result));
				}
				else
				{
					// If the view doesn't have a key (e.g. featured articles view then we need to handle this
					if ($view->key)
					{
						$segments[] = str_replace(':', '-', $query[$view->key]);
					}
				}

				unset($query[$view->key]);
				unset($query['view']);
			}
		}
	}
}
