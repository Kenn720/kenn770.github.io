<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
if($vw = $this->request->get('view_what'))
	$client = $vw;
$key = $client.$this->id.$record->id;
JFactory::getDocument()->addScript(JUri::root(true).'/components/com_cobalt/fields/video/assets/video.js');
?>

<div id="video-block<?php echo $key;?>">
	<div id="destr<?php echo $key;?>" class="video-block" style="display: none;"><div id="mediaplayer<?php echo $key;?>"></div></div>
	<div id="htmlplayer<?php echo $key;?>">
		<div class="progress progress-success progress-striped">
			<div class="bar" style="width: 100%"><?php echo JText::_('V_LOADING');?></div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(function(){
	Cobalt.loadvideo(<?php echo $this->id ?>, <?php echo $record->id ?>, '<?php echo $key ?>', '<?php echo $client ?>', '<?php echo $this->params->get('params.default_width', 640)?>');
});
</script>
