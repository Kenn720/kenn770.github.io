<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die();

/**
 * Main Controller
 *
 * @package		Cobalt
 * @subpackage	com_cobalt
 * @since		6.0
 */

class CobaltControllerAjax extends JControllerLegacy
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}
	public function loadcommentparams()
	{
		$folder = $this->input->get('adp');

		if(!$folder)
		{
			exit();
		}

		$file = JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'library'. DIRECTORY_SEPARATOR .'php'. DIRECTORY_SEPARATOR .'comments'. DIRECTORY_SEPARATOR .$folder. DIRECTORY_SEPARATOR .$folder.'.xml';
		if(!JFile::exists($file))
		{
			exit();
		}

		$form = new JForm('params', array(
				'control' => 'params'
		));

		$id = $this->input->getInt('type');

		$default = array();
		if($id)
		{
			$type = JModelLegacy::getInstance('Type', 'CobaltBModel')->getItem($id);
			$default = $type->params;
		}

		$form->loadFile($file);


		$out = MEFormHelper::renderGroup($form, $default, 'comments', FORM_STYLE_TABLE, FORM_SEPARATOR_NONE);
		echo $out;
		JFactory::getApplication()->close();
	}
	public function loadfieldform()
	{
		echo JModelLegacy::getInstance('Field', 'CobaltBModel')->getFieldForm($this->input->get('field'));
		JFactory::getApplication()->close();
	}

	public function loadcommerce()
	{
		$gateway = $this->input->get('gateway');
		if($gateway == '')
		{
			echo '';
			exit();
		}

		$field = JModelLegacy::getInstance('Field', 'CobaltBModel')->getItem($this->input->get('fid'));

		$xml = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'gateways' . DIRECTORY_SEPARATOR . $gateway . DIRECTORY_SEPARATOR . $gateway . '.xml';
		if(! JFile::exists($xml))
		{
			echo "File not found: {$xml}";
		}
		$out = array();

		$form = new JForm('params', array(
			'control' => 'params'
		));

		$form->loadFile($xml);

		$out[] = MEFormHelper::renderGroup($form, ($field ? $field->params : array()), 'pay', FORM_STYLE_TABLE, FORM_SEPARATOR_NONE);

		echo implode(' ', $out);
		JFactory::getApplication()->close();
	}

	public function loadpacksection()
	{
		echo JModelLegacy::getInstance('Packsection', 'CobaltBModel')->getSectionForm($this->input->get('id'));
		JFactory::getApplication()->close();
	}

	public function loadsectiontypes()
	{
		$section_id = $this->input->getInt('section_id');
		$selected = $this->input->get('selected', array(), 'array');
		if(!$section_id)
		{
			echo '';
			JFactory::getApplication()->close();
		}

		$section = ItemsStore::getSection($section_id);

		$params = new JRegistry($section->params);
		$db = JFactory::getDbo();
		$query = $db->getQuery ( true );

		$query->select ( 'id, name' );
		$query->from ( '#__js_res_types' );
		$query->where ( 'published = 1' );
		$ids = implode(',', $params->get('general.type'));
		$query->where ( 'id IN (0'.($ids ? ','.$ids : '').')' );
		$db->setQuery($query);
		$list = $db->loadObjectList();
		if(empty($list))
		{
			echo '';
			JFactory::getApplication()->close();
		}
		foreach ($list as $val)
		{
		    $types[] = JHTML::_('select.option', $val->id, $val->name);
		}

		echo JHTML::_('select.options', $types, 'value', 'text', $selected);
		JFactory::getApplication()->close();
	}

}