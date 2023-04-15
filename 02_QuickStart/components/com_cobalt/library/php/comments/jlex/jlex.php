<?php
include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/cobaltcomments.php';

class CobaltCommentsJlex extends CobaltComments
{

	public function getNum($type, $item)
	{
		$out = array();
		$factoryx = JPATH_ROOT . '/components/com_jlexreview/load.php';
		if (JFile::exists($factoryx)) {

			$db = JFactory::getDbo();
			$db->setQuery("SELECT COUNT(*) FROM #__jlexreview WHERE object = 'com_cobalt' AND object_id = ".$item->id);
			$out[] = (int)$db->loadResult();

			require_once $factoryx;
			$out[] = JLexReviewLoad::quick_init('com_cobalt', $item->id, $item->section_id, true);
		}

		return implode(" ", $out);
	}

	public function getComments($type, $item)
	{
		$factoryx = JPATH_ROOT . '/components/com_jlexreview/load.php';
		if (JFile::exists($factoryx)) {
			require_once $factoryx;
			return JLexReviewLoad::init($item->title, 'com_cobalt', $item->id, $item->section_id);
		}
	}
}