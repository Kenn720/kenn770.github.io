<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltrelate.php';

require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'api.php';

class JFormFieldCReadmore extends CFormFieldRelate
{
	public function getInput()
	{
		$name = "jform[fields][$this->id]";
		ArrayHelper::clean_r($this->value);

		if($this->params->get('params.strict') == 2 && $this->request->get('id'))
		{
			$db = JFactory::getDbo();
			$db->setQuery("SELECT record_id FROM #__js_res_record_values WHERE field_id = {$this->id} AND field_value = " . $this->request->get('id'));
			$add = $db->loadColumn();
			ArrayHelper::clean_r($add);

			$this->value = array_merge($this->value, $add);
		}

		$this->value = array_unique($this->value);

		$this->inputvalue = $this->_render_input($this->params->get('params.input_mode'), $name, $this->request->getInt('section_id'), $this->_getTypes());

		return $this->_display_input();
	}

	public function onRenderFull($record, $type, $section)
	{
		$this->request->set('_rmfid', $this->id);
		$this->request->set('_rmrid', $record->id);
		$this->request->set('_rmstrict', $this->params->get('params.strict'));

		$api           = new CobaltApi();
		$this->content = $api->records($section->id, 'show_related', $this->params->get('params.orderby', 'r.ctime DESC'),
			0, NULL, $this->params->get('params.cat_id', 0), $this->params->get('params.multi_limit', 10),
			$this->params->get('params.tmpl_list', 'default')
		);

		return $this->_display_output('full', $record, $type, $section);

	}

	public function onRenderList($record, $type, $section)
	{
		return NULL;
	}

	public function onGetList($params)
	{
		$db         = JFactory::getDbo();
		$section_id = $this->request->getInt('section_id');

		$query = $db->getQuery(TRUE);
		$query->select('id, title, null, title');
		$query->from('#__js_res_record');
		if(CStatistics::hasUnPublished($section_id))
		{
			$query->where('published = 1');
		}
		$query->where('hidden = 0');
		$query->where('section_id = ' . $section_id);
		$query->where('type_id IN (' . implode(',', $this->_getTypes()) . ')');
		if($this->params->get('params.user_strict'))
		{
			$user_id = JFactory::getUser()->get('id');
			$query->where('user_id = ' . ($user_id ? $user_id : 1));
		}
		$db->setQuery($query);

		return $db->loadRowList();
	}

	private function _getTypes()
	{
		$types   = $this->params->get('params.type');
		$types[] = JModelLegacy::getInstance('Fields', 'CobaltModel')->getFieldTypeId($this->id);
		ArrayHelper::clean_r($types);
		JArrayHelper::toInteger($types);

		return $types;
	}
}
