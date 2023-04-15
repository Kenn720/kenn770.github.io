<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltfield.php';

require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/cdate.php';

class JFormFieldCDatetime extends CFormField
{
	public function __construct($field, $default)
	{
		parent::__construct($field, $default);
		switch(substr($this->params->get('params.template_input'), 0, 13))
		{
			case 'multiple_date':
				$type = 'multiple';
				break;
			case 'range_date_pi':
				$type = 'range';
				break;
			case 'simple_select':
				$type = 'select';
				break;
			case 'single_date_p':
				$type = 'single';
				break;
			case 'text_input.ph':
				$type = 'input';
				break;
			case 'text_input_pi':
				$type = 'input_picker';
				break;
		}
		switch($this->params->get('params.template_filter'))
		{
			case 'range_date_picker.php':
				$filter = 'range';
				break;
			case 'single_date_picker.php':
				$filter = 'single';
				break;
			case 'compare.php':
				$filter = 'compare';
				break;
		}

		$this->params->set('params.type', $type);
		$this->params->set('params.filter_style', $filter);
	}

	public function getInput()
	{
		$doc = JFactory::getDocument();

		$url_path = JUri::root(TRUE) . '/media/mint/js/moodatepicker/';
		$lang     = JFactory::getLanguage()->getTag();
		if(JFile::exists(JPATH_ROOT . '/media/mint/js/moodatepicker/Locale.' . $lang . '.DatePicker.js'))
		{
			$doc->addScript($url_path . 'Locale.' . $lang . '.DatePicker.js');
			$doc->addScriptDeclaration('Locale.use("' . $lang . '");');
		}
		else
		{
			$doc->addScript($url_path . 'Locale.en-GB.DatePicker.js');
		}
		$doc->addScript($url_path . 'Picker.js');
		$doc->addScript($url_path . 'Picker.Attach.js');
		$doc->addScript($url_path . 'Picker.Date.js');
		$doc->addStyleSheet($url_path . 'datepicker.css');
		$doc->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/fields/datetime/datetime.css');
		$doc->addScript(JURI::root(TRUE) . '/components/com_cobalt/fields/datetime/phpformat.js');
		$doc->addScript(JURI::root(TRUE) . '/components/com_cobalt/fields/datetime/datetime.js');
		if($this->params->get('params.template_input') == 'range_date_picker.php')
		{
			$doc->addScript($url_path . 'Picker.Date.Range.js');
		}


		$format = $this->params->get('params.format', '%d %b %Y');
		if($format == 'custom' && $this->params->get('params.custom', '') != '')
		{
			$format = $this->params->get('params.custom', '');
		}

		settype($this->value, 'array');

		$this->format = $format;

		$default = '';
		if(in_array(substr($this->params->get('params.template_input'), 0, 13),
			array(
				'simple_select', 'text_input_pi',
				'text_input.ph'
			))
		)
		{
			if($this->params->get('params.input_default', '1 day'))
			{
				switch($this->params->get('params.input_default', '1 day'))
				{
					case 'now' :
						$sql = 'SELECT NOW()';
						break;
					case 'custom' :
						if($this->params->get('params.custom_input'))
						{
							$sql = "SELECT DATE_ADD(NOW(), INTERVAL {$this->params->get('params.custom_input')})";
						}
						else
						{
							$sql = 'SELECT NOW()';
						}
						break;
					default :
						$sql = "SELECT DATE_ADD(NOW(), INTERVAL {$this->params->get('params.input_default', '1 day')})";
				}
				$db = JFactory::getDbo();
				$db->setQuery($sql);
				$default = $db->loadResult();
			}
		}

		$this->default = $default;

		return $this->_display_input();

	}

	public function validate($value, $record, $type, $section)
	{
		if($value[0] && in_array($this->params->get('params.type', 'single'), array('input', 'input_picker')))
		{
			if($this->params->get('params.input_order', 'month') == 'month')
			{
				$format = sprintf('m%sd%sY', $this->params->get('params.input_delimiter', '/'), $this->params->get('params.input_delimiter', '/'));
			}
			else
			{
				$format = sprintf('d%sm%sY', $this->params->get('params.input_delimiter', '/'), $this->params->get('params.input_delimiter', '/'));
			}
			$format .= $this->params->get('params.time') ? 'H:i:s' : '00:00:00';
			$cdate = new CDate();
			if($date = $cdate->strtotime($value[0], $format))
			{
			}
			elseif($date = strtotime($value[0]))
			{
			}
			if(!$date)
			{
				$this->setError('Wrong date input');
			}
		}
		if($this->params->get('params.type', 'single') == 'select')
		{
			$f   = $this->params->get('params.time') ? 'Y-m-d H:i:s' : 'Y-m-d 00:00:00';
			$val = $value[0];
			$val = sprintf('%s-%s-%s %s:%s', str_pad($val['year'], 4, 0, STR_PAD_LEFT), str_pad($val['month'], 2, 0, STR_PAD_LEFT), str_pad($val['day'], 2, 0, STR_PAD_LEFT), isset($val['hour']) ? str_pad($val['hour'], 2, 0, STR_PAD_LEFT) : '00', isset($val['min']) ? str_pad($val['min'], 2, 0, STR_PAD_LEFT) : '00');

			if($val = strtotime($val))
			{
				$value = array(date($f, $val));
			}
			else
			{
				$value = '';
			}
		}
		parent::validate($value, $record, $type, $section);
	}

	public function onJSValidate()
	{
		$js = '';

		$js .= "\n\t\tvar dat{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[{$this->id}\\\\]\"]').val();";
		if($this->required)
		{
			$js .= "\n\t\tif(!dat{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf('CFIELDREQUIRED', $this->label) . "');}";
		}

		return $js;
	}

	public function onFilterWornLabel($section)
	{
		$out = '';
		if(is_array($this->value) && array_key_exists('range', $this->value) && !empty($this->value['range']))
		{
			$value = explode(',', trim($this->value['range']));
			ArrayHelper::clean_r($value);
			$start = explode(' ', $value[0]);
			if(empty($start[1]))
			{
				$start[1] = '00:00:00';
			}

			if(empty($value[1]))
			{
				$value[1] = $value[0];
			}
			$end = explode(' ', $value[1]);
			if(empty($end[1]))
			{
				$end[1] = '23:59:59';
			}

			$end   = $this->_getFormatted(implode(' ', $end), TRUE);
			$start = $this->_getFormatted(implode(' ', $start), TRUE);

			$out .= $start . ' - ' . $end;

		}
		elseif(is_array($this->value) && array_key_exists('condition', $this->value) && !empty($this->value['condition']) && !empty($this->value['date']))
		{

			$value = $this->_getFormatted($this->value['date'], TRUE);

			$opt[1] = JText::_($this->params->get('params.filter_compare_stbefore'));
			$opt[2] = JText::_($this->params->get('params.filter_compare_stafter'));
			$opt[3] = JText::_($this->params->get('params.filter_compare_endbefore'));
			$opt[4] = JText::_($this->params->get('params.filter_compare_endafter'));

			$out .= $opt[$this->value['condition']] . ' ' . $value;
		}
		elseif(!empty($this->value) && is_string($this->value))
		{
			$out .= $this->_getFormatted($this->value, TRUE);
		}

		return $out;
	}

	public function onFilterWhere($section, &$query)
	{
		$db  = JFactory::getDbo();
		$sql = $db->getQuery(TRUE);

		$sql->select('v.record_id');
		$sql->from('#__js_res_record_values AS v');
		$sql->where('v.section_id = ' . $section->id);
		$sql->where('v.field_key = ' . $db->quote($this->key));

		if(is_array($this->value) && array_key_exists('range', $this->value) && !empty($this->value['range']))
		{
			$value = explode(',', trim($this->value['range']));
			ArrayHelper::clean_r($value);
			$start = explode(' ', $value[0]);
			if(empty($start[1]))
			{
				$start[1] = '00:00:00';
			}

			if(empty($value[1]))
			{
				$value[1] = $value[0];
			}
			$end = explode(' ', $value[1]);
			if(empty($end[1]))
			{
				$end[1] = '23:59:59';
			}

			$end   = $db->quote(implode(' ', $end));
			$start = $db->quote(implode(' ', $start));

			switch($this->params->get('params.type', 'single'))
			{
				case 'multiple':
					$sql->where($start . " <= (SELECT MIN(field_value) FROM #__js_res_record_values
							WHERE record_id = v.record_id AND section_id = {$section->id}
							AND field_key = '{$this->key}')");

					$sql->where($end . " >= (SELECT MAX(field_value) FROM #__js_res_record_values
							WHERE record_id = v.record_id AND section_id = {$section->id}
							AND field_key = '{$this->key}')");
				case 'range':
					$sql->where($start . " <= (SELECT field_value FROM #__js_res_record_values
							WHERE record_id = v.record_id AND section_id = {$section->id}
							AND field_key = '{$this->key}' AND value_index = 0)");

					$sql->where($end . " >= (SELECT field_value FROM #__js_res_record_values
							WHERE record_id = v.record_id AND section_id = {$section->id}
							AND field_key = '{$this->key}' AND value_index = 1)");
					break;
				case 'single':
				default :
					$sql->where("v.field_value BETWEEN {$start} - INTERVAL 1 SECOND AND {$end}");
			}
		}
		elseif(is_array($this->value) && array_key_exists('condition', $this->value) && !empty($this->value['condition']) && !empty($this->value['date']))
		{
			$value = $this->value['date'];
			$value = $db->quote($value);

			switch($this->params->get('params.type', 'single'))
			{
				case 'multiple' :
					switch($this->value['condition'])
					{
						case 2 :
							$sql->where("(SELECT MIN(field_value) FROM #__js_res_record_values
									WHERE record_id = v.record_id AND section_id = {$section->id}
									AND field_key = '{$this->key}') >= {$value}");
							break;
						case 3 :
							$sql->where("(SELECT MAX(field_value) FROM #__js_res_record_values
									WHERE record_id = v.record_id AND section_id = {$section->id}
									AND field_key = '{$this->key}') <= {$value}");
							break;
						case 4 :
							$sql->where("(SELECT MAX(field_value) FROM #__js_res_record_values
									WHERE record_id = v.record_id AND section_id = {$section->id}
									AND field_key = '{$this->key}') >= {$value}");
							break;
						case 1 :
						default :
							$sql->where("(SELECT MIN(field_value) FROM #__js_res_record_values
									WHERE record_id = v.record_id AND section_id = {$section->id}
									AND field_key = '{$this->key}') <= {$value}");
							break;
					}
					break;
				case 'range' :
					switch($this->value['condition'])
					{
						case 2 :
							$sql->where("(v.field_value >=  {$value} AND v.value_index = '0')");
							break;
						case 3 :
							$sql->where("(v.field_value <= {$value} AND v.value_index = '1')");
							break;
						case 4 :
							$sql->where("(v.field_value >= {$value} AND v.value_index = '1')");
							break;
						case 1:
						default :
							$sql->where("(v.field_value <= {$value} AND v.value_index = '0')");
							break;

					}
					break;
				case 'single' :
				default :
					switch($this->value['condition'])
					{
						case 1 :
						case 3 :
							$sql->where("v.field_value <= {$value}");
							break;
						case 2 :
						case 4 :
							$sql->where("v.field_value >= {$value}");
							break;
						default :
							$sql->where("v.field_value = {$value}");
					}
					break;
			}
		}
		elseif(!empty($this->value) && is_string($this->value))
		{
			$value = $db->quote($this->value);
			switch($this->params->get('params.type', 'single'))
			{
				case 'range':
					$sql->where("DATE({$value}) BETWEEN
							(SELECT field_value FROM #__js_res_record_values
							WHERE record_id = v.record_id AND section_id = {$section->id}
							AND field_key = '{$this->key}' AND value_index = 0)
						AND (SELECT field_value FROM #__js_res_record_values
							WHERE record_id = v.record_id AND section_id = {$section->id}
							AND field_key = '{$this->key}' AND value_index = 1)");
					break;
				case 'multiple' :
				case 'single' :
				default :
					$sql->where("DATE(v.field_value) = DATE({$value})");
					break;
			}
		}
		else
		{
			return FALSE;
		}

		$ids = $this->getIds((string)$sql);

		return $ids;

		//$query->where("r.id IN(" . implode(',', $ids) . ")");
		//return TRUE;
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		$doc  = JFactory::getDocument();
		$path = JUri::root(TRUE) . '/media/mint/js/moodatepicker/';

		$lang = JFactory::getLanguage()->getTag();
		if(JFile::exists(JPATH_ROOT . '/media/mint/js/moodatepicker/Locale.' . $lang . '.DatePicker.js'))
		{
			$doc->addScript($path . 'Locale.' . $lang . '.DatePicker.js');
			$doc->addScriptDeclaration('Locale.use("' . $lang . '");');
		}
		else
		{
			$doc->addScript($path . 'Locale.en-GB.DatePicker.js');
		}
		$doc->addScript($path . 'Picker.js');
		$doc->addScript($path . 'Picker.Attach.js');
		$doc->addScript($path . 'Picker.Date.js');
		$doc->addScript($path . 'Picker.Date.Range.js');
		$doc->addStyleSheet($path . 'datepicker.css');
		$doc->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/fields/datetime/datetime.css');
		$doc->addScript(JURI::root(TRUE) . '/components/com_cobalt/fields/datetime/datetime.js');
		$doc->addScript(JURI::root(TRUE) . '/components/com_cobalt/fields/datetime/phpformat.js');


		$format = $this->params->get('params.format', '%d %b %Y');
		if($format == 'custom' && $this->params->get('params.custom', '') != '')
		{
			$format = $this->params->get('params.custom', '');
		}
		$this->format     = $format;
		$date             = new CDate();
		$this->php_format = $date->convertFormat($format);

		return $this->_display_filter($section, $module);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		if(isset($value[0]) && is_array($value[0]))
		{
			$value = $value[0];

			return implode('-', $value);
		}

		return implode(',', $value);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if(!isset($value[0]) || $value[0] == '')
		{
			return array();
		}

		$format = ($this->params->get('params.format') == 'custom' ? $this->params->get('params.custom') : $this->params->get('params.format'));

		$f = $this->params->get('params.time') ? 'Y-m-d H:i:s' : 'Y-m-d 00:00:00';
		if(!in_array($this->params->get('params.type'), array('select', 'multiple', 'range', 'single')))
		{
			if($this->params->get('params.input_order', 'month') == 'month') //$format = 'm-d-Y H:i:s';
			{
				$format = sprintf('m%sd%sY', $this->params->get('params.input_delimiter', '/'), $this->params->get('params.input_delimiter', '/'));
			}
			else
			{
				$format = sprintf('d%sm%sY', $this->params->get('params.input_delimiter', '/'), $this->params->get('params.input_delimiter', '/'));
			}
			//$format = 'd-m-Y H:i:s';
			$format .= $this->params->get('params.time') ? 'H:i:s' : '00:00:00';

			$cdate = new CDate();

			if($date = $cdate->strtotime($value[0], $format))
			{
				$value = array(date($f, $date));
			}
			elseif($date = strtotime($value[0]))
			{
				$value = array(date($f, $date));
			}
		}
		if($this->params->get('params.type') == 'select')
		{
			$value = $value[0];
			$val   = sprintf('%s-%s-%s %s:%s', str_pad($value['year'], 4, 0, STR_PAD_LEFT), str_pad($value['month'], 2, 0, STR_PAD_LEFT), str_pad($value['day'], 2, 0, STR_PAD_LEFT), isset($value['hour']) ? str_pad($value['hour'], 2, 0, STR_PAD_LEFT) : '00', isset($value['min']) ? str_pad($value['min'], 2, 0, STR_PAD_LEFT) : '00');

			if($val = strtotime($val))
			{
				$value = array(date($f, $val));
			}
			else
			{
				$value = array();
			}
		}

		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		ArrayHelper::clean_r($this->value);
		if(count($this->value) == 0)
		{
			return;
		}

		if($this->params->get('params.ovr_ctime', 0))
		{
			$value_date = $this->value[0];
			if($this->params->get('params.type', 'single') == 'multiple')
			{
				$value = $this->value;
				sort($value);
				$value_date = $value[0];
			}

			$cdate = JFactory::getDate($value_date);

			if($this->params->get('params.ctime_add', FALSE))
			{
				$cdate->add(DateInterval::createFromDateString($this->params->get('params.ctime_add')));
			}
			$record->ctime = $cdate->toSql();
		}
		if($this->params->get('params.ovr_extime', 0))
		{
			$value_date = $this->value[0];
			if($this->params->get('params.type', 'single') == 'multiple')
			{
				$value = $this->value;
				sort($value);
				$value_date = $value[0];
			}
			elseif($this->params->get('params.template_input', 0) == 'range_date_picker.php')
			{
				$value_date = $this->value[1];
			}

			$exdate = JFactory::getDate($value_date);

			if($this->params->get('params.extime_add', FALSE))
			{
				$exdate->add(DateInterval::createFromDateString($this->params->get('params.extime_add')));
			}
			$record->extime = $exdate->toSql();
		}

		return $this->value;
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($client, $record, $type, $section)
	{
		if(!$this->value)
		{
			return;
		}
		settype($this->value, 'array');
		natsort($this->value);

		$all = $dates = array();
		foreach($this->value as $value)
		{
			$date = $this->_getFormatted($value, FALSE, '_out');
			if(in_array(strip_tags($date), $all))
			{
				continue;
			}
			$all[] = strip_tags($date);
			if($this->params->get('params.filter_enable'))
			{
				$tip = ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $date . '</b>') : NULL);

				switch($this->params->get('params.filter_linkage'))
				{
					case 1 :
						$date = FilterHelper::filterLink('filter_' . $this->id, $value, $date, $this->type_id, $tip, $section);
						break;

					case 2 :
						$date .= ' ' . FilterHelper::filterButton('filter_' . $this->id, $value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
						break;
				}
			}
			$dates[] = $date;
		}


		$this->dates = $dates;

		return $this->_display_output($client, $record, $type, $section);
	}

	public function _getFormatted($value, $only_format = FALSE, $out = NULL)
	{
		if($value == '')
		{
			return '';
		}
		if(is_array($value))
		{
			return;
		}
		$params = $this->params;
		$format = $params->get('params.format' . $out, 'd M Y');
		if($format == 'custom' && $params->get('params.custom' . $out, '') != '')
		{
			$format = $params->get('params.custom' . $out, '');
		}
		$date = new CDate($value);
		$date = $date->format($date->convertFormat($format));
		if($only_format)
		{
			return $date;
		}

		$value_date = strtotime($value);
		if($params->get('params.computation', 'day') == 'day' && !$params->get('params.time', FALSE))
		{
			$value_date = strtotime('+1 day', $value_date);
		}

		$diff = time() - $value_date;
		$days = $diff / 3600 / 24;

		if($days < 0)
		{
			$this->date_type = 'normal';
			if(abs($days) <= $params->get('params.notify_days', 30))
			{
				$this->date_type = 'notify';
			}
		}
		else
		{
			$this->date_type = 'past';
		}
		$days = abs($days);
		switch($params->get('params.computation', 'day'))
		{
			case 'round' :
				$days = round($days);
				break;
			case 'int' :
				$days = intval($days);
				break;
			case 'ceil' :
				$days = ceil($days);
				break;
			case 'day' :
				$days = intval($days) + 1;
				break;
		}

		switch($params->get('params.mode', 2))
		{
			case '1' :
				$out = $this->_getDay($days);
				break;

			case '2' :
				$out = $this->_getDate($date);
				break;

			case '3' :
				$_day  = $this->_getDay($days);
				$_date = $this->_getDate($date);
				if($params->get('params.show_days', 1) == 1)
				{
					$out = $_day . ' ' . $params->get('params.date_days_separator', ' ') . ' ' . $_date;
				}
				else
				{
					$out = $_date . ' ' . $params->get('params.date_days_separator', ' ') . ' ' . $_day;
				}
				break;
			case '4' :
				$out = $this->_getAge($date, $value);
				break;
		}

		return $out;
	}

	private function _getColor($param_name)
	{
		if($color = $this->params->get('params.' . $param_name, ''))
		{
			$color = 'style="color: ' . $color . '"';
		}

		return $color;
	}

	private function _getDate($date)
	{
		$style = $this->params->get('params.date_style');
		$out   = JText::sprintf('%s %s %s', JText::_($this->params->get('params.date_before', '')), $date, JText::_($this->params->get('params.date_after', '')));
		$out   = JText::sprintf('<%s %s>%s</%s>', $this->params->get('params.' . $this->date_type . '_style', $style), $this->_getColor($this->date_type . '_color'), $out, $this->params->get('params.' . $this->date_type . '_style', $style));

		return $out;
	}

	private function _getDay($days)
	{
		$style = $this->params->get('params.date_style');
		$out   = JText::sprintf('%s %s %s', JText::_($this->params->get('params.' . $this->date_type . '_before')), $days, JText::_($this->params->get('params.' . $this->date_type . '_after')));
		if($days == 1 && $this->date_type == 'notify')
		{
			$out .= ' (' . $this->params->get('params.notify_msg') . ')';
		}
		$out = JText::sprintf('<%s %s>%s</%s>', $this->params->get('params.' . $this->date_type . '_style', $style), $this->_getColor($this->date_type . '_color'), $out, $this->params->get('params.' . $this->date_type . '_style', $style));

		return $out;
	}

	private function _getAge($date, $value)
	{
		$now    = JFactory::getDate();
		$b_date = JFactory::getDate($value);
		$age    = $b_date->diff($now)->y;

		$age_notify = $this->params->get('params.age_notify', FALSE);
		$age_expire = $this->params->get('params.age_expire', FALSE);

		$this->date_type = 'normal';
		if($age_notify && $age > $age_notify)
		{
			$this->date_type = 'notify';
		}

		if($age_expire && $age >= $age_expire)
		{
			$this->date_type = 'past';
		}

		$style = $this->params->get('params.age_style');
		$age   = JText::sprintf('%s %s %s', JText::_($this->params->get('params.age_before', '')), $age, JText::_($this->params->get('params.age_after', '')));
		$age   = JText::sprintf('<%s %s>%s</%s>', $this->params->get('params.' . $this->date_type . '_style', $style), $this->_getColor($this->date_type . '_color'), $age, $this->params->get('params.' . $this->date_type . '_style', $style));

		switch($this->params->get('params.age_format', '1'))
		{
			case '1' :
				$result = $age;
				break;
			case '2' :
				$result = $age . ' ' . $this->params->get('params.date_age_separator', ' ') . ' ' . $this->_getDate($date);
				break;
			case '3' :
				$result = $this->_getDate($date) . ' ' . $this->params->get('params.date_age_separator', ' ') . ' ' . $age;
				break;
			case 'custom' :
				$format = $this->params->get('params.age_custom', '[AGE], [DATE]');
				$date   = $this->_getDate($date);

				$result = str_replace(array('[AGE]', '[DATE]'), array($age, $date), JText::_($format));

				break;
		}

		return $result;
	}

	public function getCalendarEvents($post)
	{
		$app   = JFactory::getApplication();
		$start = date('Y-m-d', ($app->input->get('from') / 1000));
		$end   = date('Y-m-d', ($app->input->get('to') / 1000));

		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);
		if($this->params->get('params.type', 'single') == 'range')
		{

			$query->select('rv1.record_id');
			$query->from('#__js_res_record_values AS rv1');
			$query->leftJoin("#__js_res_record_values AS rv2 ON
				rv2.record_id = rv1.record_id AND
				rv2.value_index = 1 AND
				rv2.field_key = '{$this->key}'");
			$query->where("rv1.field_key = '$this->key'");
			$query->where('rv1.value_index = 0');
			$query->where("(
				(DATE(rv1.field_value) BETWEEN '{$start}' AND '{$end}')
				OR
				(DATE(rv2.field_value) BETWEEN '{$start}' AND '{$end}')
				OR
				(DATE(rv1.field_value) < '{$start}'	AND DATE(rv2.field_value) > '{$end}')
			)");
		}
		else
		{
			$query->select('record_id');
			$query->from('#__js_res_record_values');
			$query->where("field_key = '$this->key'");
			$query->where("(DATE(field_value) BETWEEN '{$start}' AND '{$end}' )");
		}

		$db->setQuery($query);
		$ids = $db->loadColumn();

		ArrayHelper::clean_r($ids);
		JArrayHelper::toInteger($ids);

		if(empty($ids))
		{
			return NULL;
		}

		$section        = ItemsStore::getSection($this->request->getInt('section_id'));
		$model          = JModelLegacy::getInstance('Records', 'CobaltModel');
		$model->section = $section;
		$model->section->params->set('general.show_past_records', 1);
		$model->section->params->set('general.show_future_records', 1);
		$model->_id_limit = $ids;

		$query = str_replace("\n", ' ', $model->getListQuery());

		$db->setQuery($query);
		$list = $db->loadAssocList();

		$db->setQuery("SELECT id FROM `#__js_res_fields` WHERE published = 1 AND `key` = '" . $this->key . "'");
		$fields_ids = $db->loadColumn();

		foreach($list AS &$event)
		{
			$event['url'] = JRoute::_(Url::record($event['id']));
			$fields       = json_decode($event['fields'], TRUE);

			$class = @$fields[$this->params->get('params.field_id_type')];
			if(is_array($class))
			{
				$class = implode('', $class);
			}
			if($class &&
				in_array(strtolower($class),
					array(
						'event-warning', 'event-info', 'event-inverse', 'event-success',
						'event-important'
					)
				)
			)
			{
				$event['class'] = $class;
			}

			foreach($fields_ids as $field_id)
			{
				if(!isset($fields[$field_id]))
				{
					continue;
				}
				switch($this->params->get('params.type', 'single'))
				{
					case 'multiple' :
						break;
					case 'range' :
						$event['start'] = strtotime($this->_getSourceDate($fields[$field_id][0], '12:00:00')) . '000';
						$event['end']   = !empty($fields[$field_id][1]) ? strtotime($this->_getSourceDate($fields[$field_id][1], '13:00:00')) . '300' : strtotime($this->_getSourceDate($fields[$field_id][0], '12:00:00')) . '300';
						break;
					case 'single' :
					default :
						$event['start'] = strtotime($this->_getSourceDate($fields[$field_id][0], '12:00:00')) . '000';
						$event['end']   = strtotime($this->_getSourceDate($fields[$field_id][0], '13:00:00')) . '300';
						break;
				}
				break;
			}
			unset($event['fields']);
		}

		return $list;
	}

	private function _getSourceDate($date, $time = '00:00:00')
	{
		$dates = explode(' ', $date);

		return $dates[0] . ' ' . $time;
	}
}