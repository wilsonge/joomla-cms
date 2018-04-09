<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Extension\Service;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Association\AssociationExtensionInterface;

/**
 * Access to component specific services.
 *
 * @since  __DEPLOY_VERSION__
 */
interface AssociationsAwareInterface
{
	/**
	 * Returns the associations extension helper class.
	 *
	 * @return  AssociationExtensionInterface
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getAssociationsExtension(): AssociationExtensionInterface;
}
