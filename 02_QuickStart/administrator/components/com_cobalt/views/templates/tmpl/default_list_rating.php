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
        <th class="title">
          <?php echo JText::_('CNAME'); ?>
        </th>
        <th>
          <?php echo JText::_('CVIEW'); ?>
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
   
  $tmpl_path = MRtemplates::getTmplPath( 'rating');
  
  foreach($this->items->rating AS $i => $item){
    
    $img_path  = MRtemplates::getTmplImgSrc( 'rating', $item->ident );
    
    $k = 1 - $k;
    $ident = '['.$item->ident.'],['.$item->type.']';
    ?>
    <tr class="row<?php echo $k; ?>">
		<td>
          	<small><?php echo ($i + 1);?></small>
        </td>

        <td>
          <b><?php echo $item->ident; ?></b> (<small><b><?php echo $item->name; ?></b></small>)
        </td>
        <td>
          <?php echo RatingHelp::loadRating($item->ident, rand ( 0, 100 ), $i, 0, 'callBackFunction', '1', 0); ?>
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
