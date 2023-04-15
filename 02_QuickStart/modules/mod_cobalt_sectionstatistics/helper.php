<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class modCobaltSectionStatisticsHelper
{

	static public function getData($params, $section_id)
	{
		$list = array();
		if($params->get('records_num', 1))
		{
			$list['records_num_label'] = CStatistics::records_num($section_id);
		}
		if($params->get('comments_num', 1))
		{
			$list['comments_num_label'] = CStatistics::comments_num($section_id);
		}
		if($params->get('authors_num', 1))
		{
			$list['authors_num_label'] = CStatistics::authors_num($section_id);
		}
		if($params->get('members_num', 1))
		{
			$list['members_num_label'] = CStatistics::members_num($section_id, $params);
		}
		if($params->get('views_num', 1))
		{
			$list['views_num_label'] = CStatistics::views_num($section_id);
		}

		return $list;
	}


}

?>