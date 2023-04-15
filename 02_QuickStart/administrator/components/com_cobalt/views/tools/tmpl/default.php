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
<style>
	.icon {
		width: 200px;
		height: 200px;
		margin-right: 10px;
		margin-bottom: 30px;
		overflow: hidden;
	}
	.icon .alert div, .icon .alert h4  {
		text-align: center;
		margin-bottom: 5px;
	}
	.icon .alert {
		height: 100%;
	}
</style>
<div id="cpanel">
<?php foreach ($this->tools AS $row): ?>
	<?php 
		$link      = 'index.php?tmpl=component&option=com_cobalt&layout=form&view=tools&id='.$row->id.'&name='.$row->name;
		//$file_data = JApplicationHelper::parseXMLInstallFile( JPATH_PLUGINS. DIRECTORY_SEPARATOR .$row->folder. DIRECTORY_SEPARATOR .$row->element.'.xml');
		$h = (@$row->height ? @$row->height : '380');
		$w = (@$row->width ? @$row->width : '570');
  	?>
  	
  	<div class="pull-left icon">
		<div class="alert alert-info">
			<div><img src="<?php echo JURI::root(TRUE); ?>/<?php echo $row->icon; ?>"/></div>
			
			<h4><a rel="{handler: 'iframe', size: {x: <?php echo $w ?>, y: <?php echo $h ?>}}" href="<?php echo $link; ?>" class="hasTooltip modal" title="<?php echo $row->label; ?>::<?php echo htmlspecialchars($row->description); ?>">
				 <?php echo $row->label; ?></a></h4>

			<small><?php echo $row->description; ?></small>
		</div>
		<div class="clearfix"></div>
	</div>
<?php endforeach; ?>
</div>