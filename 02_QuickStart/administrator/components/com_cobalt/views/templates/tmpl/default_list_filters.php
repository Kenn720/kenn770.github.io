<?php 
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<table class="table table-hover" id="articleList">
    <thead>
      <tr>
        <th width="20">
          <?php echo JText::_('CNUM'); ?>
        </th>
        <th width="1%">
          <!--<input type="checkbox" id="checkMain" name="toggle" value=""  onclick="checkAll(<?php echo count( $this->items->filters ); ?>);" />-->
        </th>
        <th class="title">
          <?php echo JText::_('CNAME'); ?>
        </th>
        <th width="4%">
          <?php echo JText::_('CVERSION'); ?>
        </th>
        <th width="10%">
          <?php echo JText::_('CDATE'); ?>
        </th>
        <th width="10%">
          <?php echo JText::_('CAUTHOR'); ?>
        </th>
        <th width="10%">
          <?php echo JText::_('CAUTHEMAIL'); ?>
        </th>
        <th>
	        <?php echo JText::_('CDESCR'); ?>
	      </th>
      </tr>
    </thead>
  
    <tbody id="row_<?php echo @$row->id ?>">
  <?php
  $k = 0;
  foreach($this->items->filters AS $i => $item)
  {    
    $k = 1 - $k;
    $ident = '['.$item->ident.'],['.$item->type.']';
	$link = 'index.php?option=com_cobalt&view=templates&layout=form&cid[]='.$ident.'&tab=param-page6';    
  	$id = $item->ident.'-'.$item->type; 
    $js = '';
    if($item->img_path != '')
    {
    	$js =  'onmouseover="$(\'tmpl'.$id.'\').setStyle(\'display\', \'block\');" onmouseout="$(\'tmpl'.$id.'\').setStyle(\'display\', \'none\');"';
    }
	?>
    <tr class="row<?php echo $k; ?>">
		<td>
          	<small><?php echo ($i + 1);?></small>
        </td>
		<td>
          	<?php echo JHTML::_('grid.id', $i, $ident ); ?>
        </td>
		<td class="nowrap">			
	          <?php if($js != ''):?>
	          <a href="javascript: void(0);" <?php echo $js;?>><?php echo $item->ident; ?></a> [<?php echo $item->name ?>]
	          <div id="tmpl<?php echo $id;?>" style="display: none;" class="tmpl_img"><?php echo JHtml::image($item->img_path, $item->ident);?></div>
	          <?php else:?>
	          <b><?php echo $item->ident; ?></b> [<?php echo $item->name ?>]
	          <?php endif;?>         
        </td>
		<td>
			<small><span class="badge badge-success"><?php echo $item->version; ?></span></small>
		</td>
		<td>
          	<small><?php echo $item->creationdate; ?></small>
        </td>
		<td>
			<small>
			<a href="<?php echo (strstr($item->authorUrl, 'http://') ? '' : 'http://').$item->authorUrl; ?>"><?php echo $item->author; ?></a>
			</small>
		</td>
		<td>
			<small>
			<a href="mailto:<?php echo $item->authorEmail; ?>"><?php echo $item->authorEmail; ?></a>
			</small>
		</td>
		<td>
			<small>
          	<?php echo $item->description; ?>
          	</small>
        </td>
      </tr>
    <?php
  }
  ?>
    </tbody>
  </table>
