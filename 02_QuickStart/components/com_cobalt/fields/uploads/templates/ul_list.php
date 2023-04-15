<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
?>
<ul>
	<?php foreach ($files AS $file):?>
		<li>
			<a 
			<?php 
				$class = '';
				if($this->params->get('params.show_in_browser', 0))
				{
					switch ($this->params->get('params.show_target', 0))
					{
						case 0:
							echo ' target="_blank" ';
							echo ' href="'.$file->url.'"';
							break;
							
						case 1:
							echo ' onclick="popUpFile'.$this->id.'(\''.$file->url.'\');return false;" ';
							echo ' href="javascript:void(0);"';
							break;
						
						case 2:
							echo ' href="'.$file->url.'"';
							$class = ' class="modal"';
							break;
							
						case 3:
							echo ' href="'.$file->url.'"';
							break;
					}
				}
				else
				{
					echo ' target="_blank" ';
					echo ' href="'.$file->url.'"';
				}
			?>
			>
			<?php echo ($this->params->get('params.allow_edit_title', 0) && $file->title ? $file->title : $file->realname);?>
			</a>
			<?php if($hits):?>
				<b><?php echo JText::_('CHITS');?>: <span style="color:purple"><?php echo (int)$file->hits?></span></b>
			<?php endif;?>
			<?php if($size):?>
				<b><?php echo JText::_('CSIZE');?>: <span style="color:green"><?php echo HTMLFormatHelper::formatSize($file->size);?></span></b>
			<?php endif;?>
			
			<?php if($descr && $file->description):?>
				<p><?php echo $file->description;?></p>
			<?php endif;?>
		</li>
	<?php endforeach;?>
</ul>