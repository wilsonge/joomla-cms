<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ContactHelper', JPATH_ADMINISTRATOR . '/components/com_contact/helpers/contact.php');

/**
 * Item Model for a Contact.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class ContactModelContact extends JModelActions
{
	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_contact.contact';

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check against the category.
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_contact.category.' . (int) $record->catid);
		}
		// Default to component settings if category not known.
		else
		{
			return parent::canEditState($record);
		}
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_contact.contact', 'contact', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// @todo Before new MVC we returned a JObject. Therefore to keep b/c we will return one here
		// In Joomla 4.0 we should convert to a JRegistry or stdClass.
		$item = new JObject($item);

		if ($item)
		{
			// Convert the metadata field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();
		}

		// Load associated contact items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_contact', '#__contact_details', 'com_contact.item', $item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		// Load item tags
		if (!empty($item->id))
		{
			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->id, 'com_contact.contact');
		}

		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_contact.edit.contact.data', array());

		if (empty($data))
		{
			// @todo remove dynamic setting of the id. Set it in the state!!
			if (isset($this->id))
			{
				$id = $this->id;
			}
			else
			{
				$id = $this->getStateVar($this->getName() . '.id', 0);
			}

			$data = $this->getItem($id);

			// Prime some default values.
			if ($this->getStateVar('contact.id') == 0)
			{
				$app = JFactory::getApplication();
				$data->set('catid', $app->input->get('catid', $app->getUserState('com_contact.contacts.filter.category_id'), 'int'));
			}
		}

		$this->preprocessData('com_contact.contact', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 *
	 * @return  void
	 *
	 * @since    3.0
	 * @throws   RuntimeException
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();

		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['name']);
			$data['name'] = $name;
			$data['alias'] = $alias;
			$data['published'] = 0;
		}

		$links = array('linka', 'linkb', 'linkc', 'linkd', 'linke');

		foreach ($links as $link)
		{
			if ($data['params'][$link])
			{
				$data['params'][$link] = JStringPunycode::urlToPunycode($data['params'][$link]);
			}
		}

		try
		{
			parent::save($data);
		}
		catch (Exception $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$id = (int) $this->state->get($this->getName() . '.id');
			$item = $this->getItem($id);

			// Adding self to the association
			$associations = $data['associations'];

			foreach ($associations as $tag => $id)
			{
				if (empty($id))
				{
					unset($associations[$tag]);
				}
			}

			// Detecting all item menus
			$all_language = $item->language == '*';

			if ($all_language && !empty($associations))
			{
				throw new RuntimeException(JText::_('COM_CONTACT_ERROR_ALL_LANGUAGE_ASSOCIATED'), 403);
			}

			$associations[$item->language] = $item->id;

			// Deleting old association for these items
			$db = $this->getDb();
			$query = $db->getQuery(true)
				->delete('#__associations')
				->where('context=' . $db->quote('com_contact.item'))
				->where('id IN (' . implode(',', $associations) . ')');
			$db->setQuery($query);
			$db->execute();

			// @todo convert to exceptions
			if ($error = $db->getErrorMsg())
			{
				throw new RuntimeException($error);
			}

			if (!$all_language && count($associations))
			{
				// Adding new association for these items
				$key = md5(json_encode($associations));
				$query->clear()
					->insert('#__associations');

				foreach ($associations as $id)
				{
					$query->values($id . ',' . $db->quote('com_contact.item') . ',' . $db->quote($key));
				}

				$db->setQuery($query);
				$db->execute();

				// @todo convert to exceptions
				if ($error = $db->getErrorMsg())
				{
					throw new RuntimeException ($error);
				}
			}
		}
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTableInterface  $table  The JTable object
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable(JTableInterface $table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

		$table->generateAlias();

		if (empty($table->id))
		{
			// Set the values
			$table->created = $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName('#__contact_details'));
				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified = $date->toSql();
			$table->modified_by = $user->get('id');
		}
		// Increment the content version number.
		$table->version++;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
	}

	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Association content items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');

			// force to array (perhaps move to $this->loadFormData())
			$data = (array) $data;

			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_CONTACT_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data['language']) || $tag != $data['language'])
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_contact');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to toggle the featured setting of contacts.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			// @todo add appropriate http code here to the exception
			throw new RuntimeException(JText::_('COM_CONTACT_NO_ITEM_SELECTED'));
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDb();

			$query = $db->getQuery(true);
			$query->update('#__contact_details');
			$query->set('featured = ' . (int) $value);
			$query->where('id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);

			$db->execute();
		}
		catch (Exception $e)
		{
			throw new RuntimeException ($e->getMessage(), $e->getCode());
		}

		$table->reorder();

		// Clean component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $parent_id  The id of the parent.
	 * @param   string   $alias      The alias.
	 * @param   string   $title      The title.
	 *
	 * @return  array  Contains the modified title and alias.
	 *
	 * @since   3.1
	 */
	protected function generateNewTitle($category_id, $alias, $name)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias, 'catid' => $category_id)))
		{
			if ($name == $table->name)
			{
				$name = JString::increment($name);
			}

			$alias = JString::increment($alias, 'dash');
		}

		return array($name, $alias);
	}
}
