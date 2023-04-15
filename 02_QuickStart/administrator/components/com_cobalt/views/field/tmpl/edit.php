<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	
	Joomla.submitbutton = function (task)
	{	
		if (task == 'field.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	function ajax_loadfieldform(sel)
	{
		jQuery.ajax({
			url: '<?php echo JURI::base(); ?>index.php?option=com_cobalt&task=ajax.loadfieldform&tmpl=component',
			context: jQuery('#additional-form'),
			dataType: 'html',
			data:{field: sel.options[sel.selectedIndex].value}
		}).done(function(data) {
			jQuery(this).html(data);
			Cobalt.redrawBS();
		});
	}
	function ajax_loadpayform(sel)
	{
		jQuery.ajax({
			url: '<?php echo JURI::base(); ?>index.php?option=com_cobalt&task=ajax.loadcommerce&tmpl=component',
			context: jQuery('#additional-pay-form'),
			dataType: 'html',
			data:{gateway: sel.options[sel.selectedIndex].value, fid: <?php echo (int)$this->item->id;?> }
		}).done(function(data) {
			jQuery(this).html(data);
			Cobalt.redrawBS();
		});
	}
</script>

<form action="#" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#page-main" data-toggle="tab">
				<?php echo JText::_('FS_FORM')?></a></li>
		<li><a href="#page-emerald" data-toggle="tab"><?php echo JText::_('FS_EMERALD')?></a></li>
		<!--<li><a href="#page-permissions" data-toggle="tab"><?php echo JText::_('FS_PERMISSIONS')?></a></li>-->
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="page-main">
			<div class="pull-left" style="max-width: 500px; margin-right: 20px;">
				<legend><?php echo empty($this->item->id) ? JText::_('CNEWFIELD') : JText::sprintf('CEDITFIELDS', $this->item->label); ?></legend>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('label'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('label'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('field_type'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('field_type'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('group_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('group_id'); ?></div>
				</div>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'general', $this->item->params, 'core', FORM_STYLE_TABLE, 1); ?>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'label', $this->item->params, 'core', FORM_STYLE_TABLE, 1); ?>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'access2_view', $this->item->params, 'core', FORM_STYLE_TABLE, 1); ?>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'access2_submit', $this->item->params, 'core', FORM_STYLE_TABLE, 1); ?>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'access2_edit', $this->item->params, 'core', FORM_STYLE_TABLE, 1); ?>

			</div>
			<div class="pull-left" style="max-width: 500px">
				<legend><?php echo JText::_('CFIELDPARAMS'); ?></legend>
				<div id="additional-form">
					<?php echo @$this->parameters?>
				</div>
				<div id="additional-pay-form">
				</div>
			</div>
		</div>
		<div class="tab-pane" id="page-emerald">
			<p class="lead"><?php echo JText::_('FS_EMERALDINTEGRATE')?>
			<div class="pull-left" style="max-width: 500px; margin-right: 20px;">
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'sp', $this->item->params, 'emerald', FORM_STYLE_TABLE, 1); ?>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'sp4', $this->item->params, 'emerald', FORM_STYLE_TABLE, 1); ?>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'sp3', $this->item->params, 'emerald', FORM_STYLE_TABLE, 1); ?>
			</div>
			<div class="pull-left" style="max-width: 500px;">
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'sp2', $this->item->params, 'emerald', FORM_STYLE_TABLE, 1); ?>
				<?php echo MEFormHelper::renderFieldset($this->params_form, 'sp21', $this->item->params, 'emerald', FORM_STYLE_TABLE, 1); ?>
			</div>
		</div>
		<!--
		<div class="tab-pane" id="page-permissions">
			<?php echo $this->form->getInput('rules'); ?>
		</div>
		-->
	</div>

	<input type="hidden" id="jform_type_id" name="jform[type_id]" value="<?php echo JRequest::getInt('type_id', $this->state->get('fields.type'));?>" />
	<input type="hidden" id="jform_type_id" name="type_id" value="<?php echo $this->state->get('fields.type',JRequest::getInt('type_id'));?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>