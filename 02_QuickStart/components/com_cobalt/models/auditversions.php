<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.modellist' );

class CobaltModelAuditversions extends JModelList
{

	public function __construct($config = array())
	{
		
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'av.ctime', 
				'av.username',
				'av.version'
			);
		}		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('av.version', 'desc');
	}

	public function getTable($type = 'Audit_versions', $prefix = 'CobaltTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getListQuery()
	{
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query->select('av.*');
		$query->from('#__js_res_audit_versions av');
		$query->where('av.record_id = '.$this->record->id);
		$query->where('av.version != '.$this->record->version);
		
				
		$orderCol = $this->state->get('list.ordering', 'av.version');
		$orderDirn = $this->state->get('list.direction', 'desc');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		
		return $query;
	}
	
}