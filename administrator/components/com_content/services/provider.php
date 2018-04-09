<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Association\AssociationExtensionInterface;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\Exception\MissingCategoryException;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\CMS\Dispatcher\DispatcherFactory;
use Joomla\CMS\Dispatcher\DispatcherInterface;
use Joomla\CMS\Extension\Service\AssociationsAwareInterface;
use Joomla\CMS\Extension\Service\CategoryAwareInterface;
use Joomla\CMS\Extension\Service\ComponentInterface;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Content\Administrator\Helper\AssociationsHelper;
use Joomla\Component\Content\Administrator\Service\HTML\AdministratorService;
use Joomla\Component\Content\Administrator\Service\HTML\Icon;
use Joomla\Component\Content\Site\Service\Category;
use Joomla\DI\Container;

/**
 * The content service provider.
 *
 * @since  __DEPLOY_VERSION__
 */
return new class() implements ContainerAwareInterface, ComponentInterface, CategoryAwareInterface, AssociationsAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * The namespace
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $namespace;

	/**
	 * The global container
	 *
	 * @var    Container
	 * @since  __DEPLOY_VERSION__
	 */
	private $container;

	/**
	 * The constructor.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		$this->namespace = \Joomla\CMS\Component\ComponentHelper::getComponent('com_content');
	}

	/**
	 * Returns the dispatcher for the given application.
	 *
	 * @param   CMSApplicationInterface  $application  The application
	 *
	 * @return  DispatcherInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDispatcher(CMSApplicationInterface $application): DispatcherInterface
	{
		return (new DispatcherFactory($this->namespace, $this->createMVCFactory($application)))->createDispatcher($application);
	}

	/**
	 * Returns an MVCFactory.
	 *
	 * @param   CMSApplicationInterface  $application  The application
	 *
	 * @return  MVCFactoryInterface
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function createMVCFactory(CMSApplicationInterface $application): MVCFactoryInterface
	{
		$factory = new MVCFactoryFactory($this->namespace);
		$factory->setFormFactory($this->container->get(\Joomla\CMS\Form\FormFactoryInterface::class));

		return $factory->createFactory($this->container->get('app'));
	}

	/**
	 * Returns the category service. If the service is not available
	 * null is returned.
	 *
	 * @param   array   $options  The options
	 * @param   string  $section  The section
	 *
	 * @return  Categories
	 *
	 * @see Categories::setOptions()
	 *
	 * @since  __DEPLOY_VERSION__
	 * @throws MissingCategoryException
	 */
	public function getCategories(array $options = [], $section = ''): Categories
	{
		// This component doesn't support exceptions so throw as invalid if not found
		if (!empty($section))
		{
			throw new MissingCategoryException;
		}

		$category = new Category;
		$category->setOptions($options);

		return $category;
	}

	/**
	 * Returns the associations helper.
	 *
	 * @return  AssociationExtensionInterface|null
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getAssociationsExtension(): AssociationExtensionInterface
	{
		return (new AssociationsHelper);
	}

	public function register()
	{
		/**
		 * @var Registry $registry
		 */
		$registry = $this->container->get(Registry::class);
		$registry->register('contentadministrator', new AdministratorService);
		$registry->register('contenticon', new Icon($this->container->get(SiteApplication::class)));

		// The layout joomla.content.icons does need a general icon service
		$registry->register('icon', $registry->getService('contenticon'));
	}
};
