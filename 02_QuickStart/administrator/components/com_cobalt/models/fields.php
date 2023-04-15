<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class CobaltBModelFields extends JModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'f.id',
				'f.ordering',
				'f.field_type',
				'f.published',
				'f.access',
				'f.label',
				'g.title'
			);
		}
		$this->option = 'com_cobalt';
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$search = $app->getUserStateFromRequest($this->context . '.fields.search', 'filter_search');
		$this->setState('fields.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.fields.state', 'filter_published', '', 'string');
		$this->setState('fields.state', $published);

		$access = $app->getUserStateFromRequest($this->context . '.fields.access', 'filter_access', '', 'string');
		$this->setState('fields.access', $access);

		$type = $app->getUserStateFromRequest($this->context . '.fields.type', 'filter_type', '', 'int');
		$this->setState('fields.type', $type);

		$ftype = $app->getUserStateFromRequest($this->context . '.fields.ftype', 'filter_field', '', 'string');
		$this->setState('fields.ftype', $ftype);

		parent::populateState('f.ordering', 'asc');
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('fields.search');
		$id .= ':' . $this->getState('fields.state');
		$id .= ':' . $this->getState('fields.access');
		$id .= ':' . $this->getState('fields.type');
		$id .= ':' . $this->getState('fields.ftype');
		$id .= ':' . @$this->type_id;

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('f.*');
		$query->from('#__js_res_fields AS f');

		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = f.access');

		$query->select('g.title AS group_field_title, g.icon as icon');
		$query->join('LEFT', '#__js_res_fields_group AS g ON g.id = f.group_id');

		$search = $this->getState('fields.search');
		if($search = $this->getState('fields.search'))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('(f.label LIKE ' . $search . ')');
		}

		if($access = $this->getState('fields.access'))
		{
			$query->where('f.access = ' . (int)$access);
		}

		if($ftype = $this->getState('fields.ftype'))
		{
			$query->where('f.field_type = ' . $query->quote($ftype));
		}

		if($ftype = $this->getState('fields.types'))
		{
			$query->where('f.field_type IN ("' . $ftype . '")');
		}

		$published = $this->getState('fields.state');
		if(is_numeric($published))
		{
			$query->where('f.published = ' . (int)$published);
		}
		else
			if($published === '')
			{
				$query->where('(f.published IN (0, 1))');
			}

		$query->where('f.type_id = ' . (int)(isset($this->type_id) ? $this->type_id : $this->getState('fields.type')));

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape('g.ordering ASC'));
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		//$query->group('t.id');


		//echo nl2br(str_replace('#__','jos_',$query));
		//exit;
		return $query;
	}

	public function getFields()
	{
		$fileds = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'fields' ;
		$folders = JFolder::folders($fileds);
		$out = array();
		foreach($folders as $folder)
		{
			$file = $fileds . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . '.xml';
			if(!JFile::exists($file))
			{
				JError::raiseWarning(100, JText::sprintf('C_MSG_CANNOTLOADFILE', $folder));
				continue;
			}
			$xml = simplexml_load_file($file);


			$group = (string)$xml->group;
			$name = (string)$xml->name;

			$field = new stdClass();
			$field->name = $name;
			$field->file_name = $folder;
			$field->license = (string)$xml->license;
			$field->author = (string)$xml->author;
			$field->email = (string)$xml->authorEmail;
			$field->url = (string)$xml->authorUrl;
			$field->description = (string)$xml->description;
			$field->description_full = (string)$field->description;
			$field->description_full .= sprintf('<table><tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr></table>', JText::_('JAUTHOR'), (string)$field->author, JText::_('JSITE'), (string)$field->url,
				JText::_('JGLOBAL_EMAIL'), (string)$field->email, JText::_('CLICENSE'), (string)$field->license);

			$field->icon = JURI::root(TRUE) . '/libraries/mint/forms/fields/cobalt';
			if(JFile::exists($fileds . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . '.png'))
			{
				$field->icon .= "/{$folder}/{$folder}.png";
			}
			else
			{
				$field->icon .= "/rtext/rtext.png";
			}

			$out[$group][$folder] = $field;
			unset($field, $xml);
		}

		return $out;
	}
}
