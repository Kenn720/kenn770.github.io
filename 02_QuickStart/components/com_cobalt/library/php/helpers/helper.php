<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.database.table');
jimport('joomla.form.form');

JHtml::_('bootstrap.framework');

require_once JPATH_ROOT . '/libraries/mint/helper.php';

include_once dirname(__FILE__) . '/community.php';
include_once dirname(__FILE__) . '/comments.php';
include_once dirname(__FILE__) . '/access.php';
include_once dirname(__FILE__) . '/events.php';
include_once dirname(__FILE__) . '/url.php';
include_once dirname(__FILE__) . '/rating.php';
include_once dirname(__FILE__) . '/filter.php';
include_once dirname(__FILE__) . '/emerald.php';
include_once dirname(__FILE__) . '/html.php';
include_once dirname(__FILE__) . '/auditlog.php';
include_once dirname(__FILE__) . '/statistics.php';
include_once dirname(__FILE__) . '/image.php';
include_once dirname(__FILE__) . '/template.php';
include_once dirname(__FILE__) . '/user.php';
include_once dirname(__FILE__) . '/itemsstore.php';


if(JComponentHelper::getParams('com_cobalt')->get('compatibility'))
{
	JHtml::_('bootstrap.loadCss');
}

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHTML::_('bootstrap.popover', '*[rel="popover"]',
	array(
		 'placement' => 'bottom',
		 'trigger'   => 'click'
	)
);
JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');
JHTML::_('bootstrap.tooltip', '*[rel="tooltipright"]',
	array(
		 'placement' => 'right'
	)
);
JHTML::_('bootstrap.tooltip', '*[rel="tooltipbottom"]',
	array(
		 'placement' => 'bottom'
	)
);

JFactory::getLanguage()->load('com_cobalt', JPATH_ROOT);
JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_cobalt/tables');
JForm::addFieldPath(JPATH_ROOT . '/libraries/mint/forms/fields');
JForm::addRulePath(JPATH_ROOT . '/libraries/mint/forms/rules');
JForm::addFormPath(JPATH_ROOT . '/libraries/mint/forms/forms');
JHtml::addIncludePath(JPATH_ROOT . '/components/com_cobalt/library/php/html');
JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_cobalt/models', 'CobaltModel');

$document = JFactory::getDocument();
if(!JFactory::getApplication()->isAdmin())
{
	$document->addScript(JRoute::_('index.php?option=com_cobalt&task=ajax.mainJS&Itemid=1'));
}

$document->addScript(JURI::root(TRUE) . '/components/com_cobalt/library/js/felixrating.js');

if(JFile::exists(JPATH_ROOT . '/components/com_cobalt/library/css/custom.css'))
{
	$document->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/library/css/custom.css');
}
else
{
	$document->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/library/css/style.css');
}

if(!JFactory::getApplication()->isAdmin())
{
	require_once JPATH_ROOT . '/components/com_cobalt/api.php';
}

$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
if(JFile::exists($em_api))
{
	require_once $em_api;
}

class MapHelper
{

	public static function loadGoogleMapAPI()
	{
		static $loaded = NULL;

		if($loaded == 1)
		{
			return;
		}

		$app     = JFactory::getApplication();
		$map_key = JComponentHelper::getParams('com_cobalt')->get('map_key');
		list($lang, $region) = explode('-', JFactory::getLanguage()->getTag());

		JFactory::getDocument()->addScript('//maps.googleapis.com/maps/api/js?language=' . $lang . '&region=' . $region . '&key=' . $map_key . '&sensor=true&libraries=weather,panoramio,visualization,places');

		$loaded = 1;
	}
}
class CobaltCommentHelper
{

	public static function laded($id)
	{
		static $included = array();
		if(array_key_exists($id, $included)) {
			return TRUE;
		}
		$included[$id] = 1;

		return FALSE;
	}
}

class CobaltFilter {
	static public function base64($string) {
		$b = base64_decode($string);
		$c = strip_tags($b);

		if(strlen($c) != strlen($b))
		{
			return '';
		}

		return $b;
	}

	static public function in($string)
	{
		$string = preg_replace("/[^0-9,]*/iU", "", $string);

		while(strpos($string, ',,') !== FALSE) {
			$string = str_replace(',,', ',', $string);
		}

		$string = preg_replace("/,$/iU", '', $string);
		$string = preg_replace("/^,/iU", '', $string);

		/*if($string == '') {
			$string = 0;
		}*/

		return $string;
	}
}

class CSubscriptionsHelper
{

	static public function auto_subscribe($record, $section_id)
	{
		$db = JFactory::getDbo();

		$db->setQuery('SELECT user_id FROM `#__js_res_user_options_autofollow` WHERE section_id = ' . $section_id);
		$user_list = $db->loadColumn();
		foreach($user_list as $user_id)
		{
			self::subscribe_record($record, $user_id);
		}
	}

	static public function unsubscribe_record($id)
	{
		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			return;
		}

		$record = JTable::getInstance('Record', 'CobaltTable');
		$record->load($id);

		$data = array(
			'user_id'    => $user->get('id'),
			'type'       => 'record',
			'ref_id'     => $id,
			'section_id' => $record->section_id
		);

		$table = JTable::getInstance('Subscribe', 'CobaltTable');
		$table->load($data);

		if($table->id)
		{
			$table->delete();
		}

		$record->onFollow();
	}

	static public function subscribe_record($id, $user_id = NULL)
	{
		$user_id = $user_id ? $user_id : NULL;
		$user    = JFactory::getUser($user_id);
		if(!$user->get('id'))
		{
			return;
		}

		$record = $id;
		if(!is_object($id))
		{
			$record = JTable::getInstance('Record', 'CobaltTable');
			$record->load($id);
		}

		if(!in_array($record->access, $user->getAuthorisedViewLevels()) && !($record->user_id == $user->get('id')))
		{
			return;
		}

		$section = ItemsStore::getSection($record->section_id);
		if(!$section->params->get('events.subscribe_record'))
		{
			return;
		}

		$data = array(
			'user_id'    => $user->get('id'),
			'type'       => 'record',
			'ref_id'     => $record->id,
			'section_id' => $record->section_id
		);

		$table = JTable::getInstance('Subscribe', 'CobaltTable');
		$table->load($data);

		if(!$table->id)
		{
			$table->save($data);
		}
		$table->reset();

		$record->onFollow();
	}
}

class AttachmentHelper
{

	/**
	 *
	 *
	 * Enter description here ...
	 *
	 * @param array $list
	 *            list of saved attachments
	 */
	public static function getAttachments($list, $show_hits = FALSE)
	{
		if(!$list)
		{
			return array();
		}
		if(is_string($list))
		{
			$list = json_decode($list);
		}

		ArrayHelper::clean_r($list);

		if($show_hits)
		{
			$in = array();
			foreach($list as $attach)
			{
				settype($attach, 'array');
				$in[] = $attach['id'];
			}

			if($in)
			{
				$files = JTable::getInstance('Files', 'CobaltTable');
				$list  = $files->getFiles($in, 'id');
			}
		}
		foreach($list as &$file)
		{
			if(is_array($file))
			{
				$file = JArrayHelper::toObject($file);
			}
			$file->url = JURI::root(TRUE) . '/index.php?option=com_cobalt&task=files.download_attach&tmpl=component&id=' . $file->id;
		}

		return $list;
	}
}

class FieldHelper
{

	public function __construct($keys, $fields)
	{
		$this->keys   = $keys;
		$this->fields = $fields;
	}

	public function isExists($key)
	{
		return isset($this->fields[$key]);
	}

	public function hasIcon($key)
	{
		return $this->fields[$key]->params->get('core.icon', 0);
	}

	public function label($key)
	{
		return $this->fields[$key]->label;
	}

	public function result($key)
	{
		return $this->fields[$key]->result;
	}

	public function icon($key)
	{
		return $this->fields[$key]->params->get('core.icon', 0);
	}

	public static function sortName($field)
	{
		return sprintf('field^%s^%s', $field->key, $field->params->get('params.ordering_mode', 'digits'));
	}
}

class MetaHelper
{

	public static function setMeta($meta = array())
	{
		$doc = JFactory::getDocument();

		foreach($meta as $key => $value)
		{
			if($value)
			{
				$doc->setMetaData($key, Mint::_($value));
			}
		}
	}
}

class WornHelper
{

	public static function getItem($name, $label, $value, $text = NULL)
	{
		$o        = new stdClass();
		$o->name  = $name;
		$o->label = JText::_($label);
		$o->text  = $text ? $text : $value;
		$o->value = $value;

		return $o;
	}
}

// here was ItemsStore class

class AjaxHelper
{

	public static function error($msg)
	{
		$out = array(
			'success' => 0,
			'error'   => $msg
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public static function send($result, $key = 'result')
	{
		$out = array(
			'success' => 1,
			$key      => $result
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}
}

class ArrayHelper
{

	public static function trim_r(&$array)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}

		foreach($array as &$val)
		{
			if(is_array($val))
			{
				$val = self::trim_r($val);
			}
			elseif(is_string($val))
			{
				$val = trim($val);
			}
		}
	}

	public static function clean_r(&$array, $zero = FALSE)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}

		foreach($array as $k => &$val)
		{
			if(is_array($val))
			{
				self::clean_r($val);
			}
			elseif(is_string($val))
			{
				$val = trim($val);
				$val = str_replace("\r", "", $val);
			}

			if(($val != "0" && empty($val)) || ($val == "0" && $zero))
			{
				unset($array[$k]);
				continue;
			}
		}
		if(!$array)
		{
			$array = array();
		}
	}

	public static function tolower_r(&$array)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}
		foreach($array as $k => &$val)
		{
			$val = JString::strtolower($val);
		}
		if(!$array)
		{
			$array = array();
		}
	}

	public static function separate_r(&$array, $separator = '^')
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}
		foreach($array as $k => &$val)
		{
			$val = explode($separator, $val);
			$val = $val[0];
		}
		if(!$array)
		{
			$array = array();
		}
	}
}

class CensorHelper
{

	private static $params;

	private static $bad_words;

	private static $replacer;

	public static function cleanText($str)
	{
		if(empty($str))
		{
			return $str;
		}
		if(!self::$params)
		{
			self::$params    = JComponentHelper::getParams('com_cobalt');
			self::$bad_words = explode(',', self::$params->get('censor_words'));
			ArrayHelper::trim_r(self::$bad_words);
			self::$replacer = JText::_(trim(self::$params->get('censor_replace')));
		}
		if(!self::$params->get('censor'))
		{
			return $str;
		}
		if(is_array($str))
		{
			foreach($str as $key => $value)
			{
				if(is_array($value))
				{
					self::cleanText($value);
				}
				$str[$key] = str_ireplace(self::$bad_words, self::$replacer, $value);
			}

			return $str;
		}

		return str_ireplace(self::$bad_words, self::$replacer, $str);
	}
}

function list_controls($ctrl)
{
	if(!$ctrl)
	{
		return;
	}

	$out = '';

	foreach($ctrl as $key => $link)
	{
		if(is_array($link))
		{
			$out .= '<li class="option-link dropdown-submenu">' . $key;
			$out .= '<ul class="dropdown-menu">';
			$out .= list_controls($link);
			$out .= "</ul></li>";
		}
		else
		{
			$out .= "<li class='option-link'>{$link}</li>";
		}
	}

	return $out;
}