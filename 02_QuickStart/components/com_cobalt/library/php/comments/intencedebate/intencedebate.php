<?php
include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/cobaltcomments.php';

class CobaltCommentsIntencedebate extends CobaltComments {
	
	public function getNum($type, $item) {
		$out = '<script>
		var idcomments_acct = "'.$type->params->get ('comments.accid').'";
		var idcomments_post_id = "'.$item->id.'";
		var idcomments_post_url = "'.$item->href.'";
		</script>
		<script type="text/javascript" src="http://www.intensedebate.com/js/genericLinkWrapperV2.js"></script>';
		
		return $out;
	}
	
	public function getComments($type, $item) {
		$out = '<h2>' . JText::_ ( 'CCOMMENTS' ) . '</h2>';
		$out .= '<script>
		var idcomments_acct = "'.$type->params->get ('comments.accid').'";
		var idcomments_post_id = "'.$item->id.'";
		var idcomments_post_url = "'.$item->href.'";
		</script>
		<span id="IDCommentsPostTitle" style="display:none"></span>
		<script type="text/javascript" src="http://www.intensedebate.com/js/genericCommentWrapperV2.js"></script>';
		return $out;
	}
}

