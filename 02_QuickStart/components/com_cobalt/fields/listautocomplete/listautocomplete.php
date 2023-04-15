<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltselectable.php';

class JFormFieldCListautocomplete extends CFormFieldSelectable
{

	public function getInput()
	{
		if (!is_array($this->value))
		{
			$this->value = explode(',', $this->value);
		}

		$options['max_width'] = $this->params->get('params.max_width', 400);
		$options['min_width'] = $this->params->get('params.min_width', 300);
		$options['coma_separate'] = $this->params->get('params.coma_separate', 0);
		$options['only_values'] = $this->params->get('params.only_values', 0);
		$options['min_length'] = $this->params->get('params.min_length', 1);
		$options['max_result'] = $this->params->get('params.max_result', 10);
		$options['case_sensitive'] = $this->params->get('params.case_sensitive', 0);
		$options['highlight'] = $this->params->get('params.highlight', 1);
		$options['max_items'] = $this->params->get('params.max_items', 0);
		$options['unique'] = 1;

		$options['ajax_url'] = 'index.php?option=com_cobalt&task=ajax.field_call&tmpl=component';
		$options['ajax_data'] = "field_id: {$this->id}, func: 'onGetSqlValues', field: 'listautocomplete'";

		if($this->isnew && $this->params->get('params.default_val'))
		{
			$this->value[] = $this->params->get('params.default_val');
		}

		$default = $this->value;


		if($default && !$this->params->get('params.sql_source'))
		{
			$default = array_combine($default, array_map(function($v){ return JText::_($v);}, $default));
		}

		$this->inputvalue = JHtml::_('mrelements.listautocomplete', "jform[fields][{$this->id}]", "field_" . $this->id, $default, array(), $options);
		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar lac{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]').val();";
		if($this->required)
		{
			$js .= "\n\t\tif(!lac{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('".addslashes(JText::sprintf("CFIELDREQUIRED", $this->label))."');}";
		}
		return $js;
	}

	public function validate($value, $record, $type, $section)
	{
		$count = explode(',', $value);
		if ($this->params->get('params.max_items', 0) && (count($count) > $this->params->get('params.max_items')))
		{
			$this->setError(JText::sprintf("L_ITEMSLIMITMSG", $this->label));
			return FALSE;
		}

		return parent::validate($value, $record, $type, $section);
	}

	public function onGetSqlValues($post)
	{
		if ($this->params->get('params.sql_source'))
		{
			$db = JFactory::getDbo();
			$user = JFactory::getUser();
			$sql = $this->params->get('params.sql', "SELECT 1 AS value, 'No sql query entered' AS text");
			$sql = str_replace('[USER_ID]', $user->get('id', 0), $sql);
			$db->setQuery($sql);
			$list = $db->loadObjectList();
			foreach ($list as $k => $item)
			{
				$out[]= array($item->id, $item->text, $item->text, $item->text);
			}
		}
		else
		{
			$list = explode("\n", str_replace("\r", "", $this->params->get('params.values', '')));
			$list = array_values($list);
			foreach($list as $k => $item)
			{
				$row = array($item, strip_tags(JText::_($item)), JText::_($item), JText::_($item));
				if (in_array($item, $this->value))
				{
					$key = array_search($item, $this->value);
					$this->value[$key] = $row;
				}

				$out[] = $row;
			}
		}


		return $out;
	}

	public function onImportData($row, $params)
	{
		return $row->get($params->get('field.' . $this->id . '.fname'));
	}

	public function onImport($value, $params, $record = null)
	{
		$values = explode($params->get('field.' . $this->id . '.separator', ','), $value);
		ArrayHelper::clean_r($values);
		return $values;
	}

	public function onImportForm($heads, $defaults, $record = null)
	{
		$out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][separator]" value="%s" class="span2" >',
			JText::_('CMULTIVALFIELDSEPARATOR'), $this->id, $defaults->get('field.' . $this->id . '.separator', ','));

		return $out;
	}

}
