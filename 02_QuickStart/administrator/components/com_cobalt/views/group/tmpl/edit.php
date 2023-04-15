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
		if (task == 'filter.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="#" method="post" name="adminForm" id="item-form" class="form-horizontal">
	<div class="row-fluid">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('CNEWGROUP') : JText::sprintf('CEDITGROUPS', $this->item->title); ?></legend>
			<div class="span6">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('icon'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('icon'); ?></div>
				</div>
				<div class="control-group form-vertical">
					<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
				</div>
			</div>
		</fieldset>
	</div>

	<input type="hidden" id="jform_type_id" name="jform[type_id]" value="<?php echo $this->state->get('groups.type');?>" />
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->state->get('groups.return');?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>