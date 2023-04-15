<?php
/**
 * by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<ul class="nav nav-pills">
	<li><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=import&step=1&section_id='.$this->input->get('section_id')); ?>"><?php echo JText::_('CIMPORTUPLOAD')?></a></li>
	<li class="active"><a><?php echo JText::_('CIMPORTCONFIG')?></a></li>
	<li><a><?php echo JText::_('CIMPORTFINISH')?></a></li>
</ul>
<form action="<?php echo JFactory::getURI()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="row-fluid">
		<?php echo JHtml::_('select.genericlist', $this->presets, 'preset', 'class="span12"');?>
	</div>

	<div class="hide" id="preset-form">

	</div>

	<div class="form-actions">
		<button class="pull-right btn btn-primary" type="button" id="next-step"><?php echo JText::_('CNEXT')?></button>
	</div>
	<input type="hidden" name="task" value="import.import">
	<input type="hidden" name="step" value="3">
	<input type="hidden" name="type" value="<?php echo $this->input->get('type'); ?>">
	<input type="hidden" name="key" value="<?php echo $this->input->get('key'); ?>">
</form>
<script>
	(function($) {
		var preset = $('#preset');

		$('#next-step').bind('click', function() {
			if(!preset.val()) {
				alert('Please select import settings or create new...');
				return false;
			}
			if($('#importname').val() == 0) {
				alert('Enter name');
				return false;
			}
			$.each($('.cat-select'), function() {
				if(!$(this).val()) {
					alert('category not selected');
					return false;
				}
			});

			$.each($('div.required select'), function() {
				if($(this).val() == 0) {
					alert('Required fields are not set');
					return false;
				}
			});

			if($('#importfieldid').val() == 0) {
				alert('Set ID');
				return false;
			}
			<?php if($this->section->categories || ($this->section->params->get('personalize.personalize', 0) && in_array($this->section->params->get('personalize.pcat_submit', 0), $this->user->getAuthorisedViewLevels()))):?>
			if($('#importfieldcategory').val() == 0) {
				alert('Set Category');
				return false;
			}
			<?php endif;?>

			<?php if($this->type->params->get('properties.item_title') == 1):?>
			if($('#importfieldtitle').val() == 0) {
				alert('Set title');
				return false;
			}
			<?php endif;?>

			$(this).attr('disabled', 'disabled');

			$('#adminForm').submit();

		});

		preset.bind('change', function() {
			if($(this).val() != '') {
				$('#preset-form').html('').slideUp('fast', function(){
					$.get('<?php echo JRoute::_('index.php?option=com_cobalt&view=import&layout=params&tmpl=component', false);?>',
						{'preset': preset.val(), type: $('input[name="type"]').val(), section_id: <?php echo $this->input->get('section_id'); ?>})
					.done(function(data) {

							$('#preset-form').html(data).slideDown('fast', function() {
							$('#importfieldcategory').bind('change', categoryload);
						});
					});
				});
			} else {
				$('#preset-form').html('').slideUp('fast');
			}
		});

		window.categoryload =  function() {
			$('#cat-list').html('');
			var field = $('#importfieldcategory').val();
			if(!field) return false;

			$('#progress').slideDown('fast', function() {
				$.get('<?php echo JRoute::_('index.php?option=com_cobalt&view=import&layout=categories&tmpl=component', false);?>',
					{'preset': preset.val(), 'field': field, 'section_id':<?php echo $this->section->id;?>})
				.done(function(data) {
					$('#progress').slideUp('fast', function() {
						$('#cat-list').html(data);
					});
				});
			})
		}
	}(jQuery))
</script>