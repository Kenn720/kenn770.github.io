<?php
include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/cobaltcomments.php';

class CobaltCommentsVk extends CobaltComments {
	
	private function _load($type) {
		static $load = null;
	
		if (! $load) {
			$js = 'VK.init({
			    apiId: '.$type->params->get('comments.appid').',
			    onlyWidgets: true
			  });';
				
			$doc = JFactory::getDocument ();
			$doc->addScript('http://userapi.com/js/api/openapi.js');
			$doc->addScriptDeclaration ( $js );
			
			$load = TRUE;
		}
	}
	
	public function getNum($type, $item) {
		return 0;
	}
	
	public function getComments($type, $item) {
		$this->_load($type);
		
		$pieces[] = 'width:'.$type->params->get ('comments.width', 500);
		$pieces[] = 'limit:'.$type->params->get ('comments.limit', 10);
		
		$out = '<h2>' . JText::_ ( 'CCOMMENTS' ) . '</h2>';
		$out .= '<div id="vk_comments"></div>
			<script type="text/javascript">
			 VK.Widgets.Comments(\'vk_comments\', {'.implode(', ', $pieces).'}, '.$item->id.');
			</script>';
		
		return $out;
	}
}

