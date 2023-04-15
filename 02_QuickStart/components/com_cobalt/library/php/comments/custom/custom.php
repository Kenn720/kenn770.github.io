<?php
include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_cobalt/library/php/cobaltcomments.php';

class CobaltCommentsCustom extends CobaltComments {
	
	public function getNum($type, $record)
	{
		$code = $type->params->get('comments.comment_custom_js_numm');
		$code = str_replace(array('[URL]', '[ID]'), array(JFactory::getURI()->toString(), $record->id), $code);
		
		return $code;
	}
	
	public function getComments($type, $record)
	{
		$out[] = '<h2>'.JText::_('CCOMMENTS').'</h2>';
		$code = $type->params->get('comments.comment_custom_js_comm');
		$code = str_replace(array('[URL]', '[ID]'), array(JFactory::getURI()->toString(), $record->id), $code);
		
		$out[] = $code;
		
		return implode(" ", $out);
	}
}