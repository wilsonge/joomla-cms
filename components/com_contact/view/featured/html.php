<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ConfigViewCmsHtml', JPATH_SITE . '/components/com_config/view/cms/html.php');

/**
 * Frontpage View class
 *
 * @package     Joomla.Site
 * @subpackage  com_contact
 * @since       1.6
 */
class ContactViewFeaturedHtml extends JViewHtmlLegacy
{
	protected $state;

	protected $items;

	protected $category;

	protected $categories;

	protected $pagination;

	/**
	 * Display the view
	 *
	 * @return  mixed  False on error, null otherwise.
	 */
	public function render()
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		$model      = $this->getModel();

		// Get some data from the models
		$state		= $model->getState();
		$items		= $model->getItems();
		$pagination	= $model->getPagination();

		// Prepare the data.
		// Compute the contact slug.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$temp		= new JRegistry;
			$temp->loadString($item->params);
			$item->params = clone($params);
			$item->params->merge($temp);
			if ($item->params->get('show_email', 0) == 1)
			{
				$item->email_to = trim($item->email_to);
				if (!empty($item->email_to) && JMailHelper::isEmailAddress($item->email_to))
				{
					$item->email_to = JHtml::_('email.cloak', $item->email_to);
				} else {
					$item->email_to = '';
				}
			}
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$maxLevel         = $params->get('maxLevel', -1);
		$this->maxLevel   = &$maxLevel;
		$this->state      = &$state;
		$this->items      = &$items;
		$this->params     = &$params;
		$this->pagination = &$pagination;

		$this->_prepareDocument();

		return parent::render();
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_CONTACT_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$document = JFactory::getDocument();
		$document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
