<?php
include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_cobalt/library/php/cobaltcomments.php';

class CobaltCommentsCore extends CobaltComments {

	public function getNum($type, $item)
	{
		return $item->comments;
	}

	public function getComments($type, $item) {}

	public function getIndex($type, $item) {

		$db = JFactory::getDbo();

		$db->setQuery("SELECT comment FROM #__js_res_comments WHERE published = 1 AND record_id = {$item->id}");
		$list = $db->loadColumn();

		return implode(', ', $list);
	}
}