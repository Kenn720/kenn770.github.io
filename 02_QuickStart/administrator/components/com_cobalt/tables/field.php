<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('legacy.access.rules');

class CobaltTableField extends JTable
{

	public function __construct(&$_db )
	{
		parent::__construct('#__js_res_fields', 'id', $_db);
	}

	public function bind($array, $ignore = '')
	{
		$params = JRequest::getVar('params', array(), 'post', 'array');
		if($params)
		{
			$registry = new JRegistry();
			$registry->loadArray($params);
			$this->filter = $registry->get('params.filter_enable', 0);
			$array['params'] = (string)$registry;
		}

		if (isset($array['rules']) && is_array($array['rules'])) {
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		$this->key = 'k'.md5($array['label'].'-'.$array['field_type']);


		return parent::bind($array, $ignore);
	}

	public function delete($pk = null)
	{
		if(parent::delete($pk))
		{
			$sql = "DELETE FROM #__js_res_record_values WHERE field_id = ".($pk ? $pk : $this->id);
			$this->_db->setQuery($sql);
			$this->_db->query();

			return true;
		}

		return false;
	}
	public function check()
	{
		if(trim($this->label) == '')
		{
			$this->setError(JText::_('CNOLABEL'));
			return false;
		}

		if(trim($this->user_id) == '') {
			$this->user_id = (int)JFactory::getUser()->get('id');
		}

		return true;
	}

	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_cobalt.field.'.(int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->label;
	}

}
?>
