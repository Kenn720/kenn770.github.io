<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
JFactory::getLanguage()->load($this->name, JPATH_ROOT, JFactory::getLanguage()->getTag());
defined('_JEXEC') or die();
$app = JFactory::getApplication();
?>

<?php if($this->close):?>
	<script type="text/javascript">
		window.parent.SqueezeBox.close();
	</script>
<?php endif;?>


<form method="post" name="adminForm" id="adminForm" class="form-horizontal">

<?php if($app->input->get('tmpl') == 'component'):?>
	<style type="text/css">
		body.component {
			padding-top: 60px;
		}
	</style>
	<div class="navbar navbar-fixed-top" style="background-color: #f5f5f5; padding: 0 15px 6px;">
		<div class="navbar-inner">
			<div class="pull-right">
				<button class="btn" type="button" onclick="javascript:Cobalt.submitTask('templates.apply')"><i class="icon-edit"></i> <?php echo JText::_('CSAVE')?></button>
				<button class="btn" type="button" onclick="javascript:Joomla.submitbutton('templates.saveclose')"><i class="icon-save"></i> <?php echo JText::_('CSAVECLOSE')?></button>
				<button class="btn" type="button" onclick="<?php echo (!JRequest::getVar('inner') ? 'parent.SqueezeBox.close();' : "javascript:Joomla.submitbutton('templates.cancel')");?>"><i class="icon-cancel "></i> <?php echo JText::_('CCLOSE')?></a></button>
			</div>
		</div>
	</div>
<?php endif;?>

			<ul class="nav nav-tabs">
				<li><a href="#page-info" data-toggle="tab"><?php echo JText::_('CTMPLINFO')?></a></li>
				<?php if($this->type == 'markup'):?>
					<li class="active"><a href="#page-main" data-toggle="tab"><?php echo JText::_('FS_GENERAL')?></a></li>
					<li class=""><a href="#page-title" data-toggle="tab"><?php echo JText::_('X_GROTIT')?></a></li>
					<li class=""><a href="#page-menu" data-toggle="tab"><?php echo JText::_('XML_LABEL_SP_SECMENU')?></a></li>
					<li class=""><a href="#page-filter" data-toggle="tab"><?php echo JText::_('XML_LABEL_SP_FILTERS')?></a></li>
					<li class=""><a href="#page-personal" data-toggle="tab"><?php echo JText::_('FS_PERSPARAMS')?></a></li>

				<?php else:?>
					<li class="active"><a href="#page-params" data-toggle="tab"><?php echo JText::_('FS_GENERAL')?></a></li>
				<?php endif;?>
			</ul>

	<div class="tab-content">
		<div class="tab-pane" id="page-info">
			<dl class="dl-horizontal">
				<dt><?php echo JText::_('CLABEL'); ?>:</dt>
				<dd><?php echo $this->xml_data['name'] ?></dd>
			</dl>
			<dl class="dl-horizontal">
				<dt><?php echo JText::_('CSYSTEMNAME'); ?>:</dt>
				<dd><?php echo $this->name; ?></dd>
			</dl>
			<dl class="dl-horizontal">
				<dt><?php echo JText::_('CAUTHOR'); ?>:</dt>
				<dd>
					<a href="mailto:<?php echo $this->xml_data['authorEmail'] ?>"><?php echo $this->xml_data['author']; ?></a>
          			(<a href="<?php echo $this->xml_data['authorUrl'] ?>" target="_blank"><?php echo $this->xml_data['authorUrl'] ?></a>)
          		</dd>
			</dl>
			<dl class="dl-horizontal">
				<dt><?php echo JText::_('CTYPE'); ?>:</dt>
				<dd><?php echo $this->type; ?></dd>
			</dl>
			<dl class="dl-horizontal">
				<dt><?php echo JText::_('CLOCATION'); ?>:</dt>
				<dd><?php echo str_replace(array("\\", "/"), " / ", $this->location); ?></dd>
			</dl>
			<dl class="dl-horizontal">
				<dt><?php echo JText::_('CDESCR'); ?>:</dt>
				<dd><?php echo $this->xml_data['description']; ?></dd>
			</dl>
			<?php if($this->img_path):?>
	        <dl class="dl-horizontal">
				<dt><?php echo JText::_('CVIEW'); ?>:</dt>
				<dd><img src="<?php echo $this->img_path; ?>"></dd>
			</dl>
			<?php endif;?>
			<dl class="dl-horizontal">
				<dt><?php echo JText::_('CCOPYRIGHT'); ?>:</dt>
				<dd><?php echo $this->xml_data['copyright'] ?></dd>
			</dl>
		</div>
		<?php if($this->type == 'markup'):?>
			<div class="tab-pane active" id="page-main">
				<div class="row-fluid">
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'general', $this->params, 'main', FORM_STYLE_TABLE, 1);?>
					</div>
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'alpha', $this->params, 'main', FORM_STYLE_TABLE, 1);?>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="page-title">
				<div class="row-fluid">
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'title', $this->params, 'title', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 't1', $this->params, 'title', FORM_STYLE_TABLE, 1);?>
					</div>
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 't2', $this->params, 'title', FORM_STYLE_TABLE, 1);?>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="page-menu">
				<div class="row-fluid">
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'menu', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'micons', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'm1', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'm2', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'm6', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
					</div>
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'm3', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'm4', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'mlabels', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'mlabels2', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'm5', $this->params, 'menu', FORM_STYLE_TABLE, 1);?>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="page-filter">
				<div class="row-fluid">
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'filters', $this->params, 'filters', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'f5', $this->params, 'filters', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'f6', $this->params, 'filters', FORM_STYLE_TABLE, 1);?>
					</div>
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'f2', $this->params, 'filters', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'f3', $this->params, 'filters', FORM_STYLE_TABLE, 1);?>
						<?php echo MEFormHelper::renderFieldset($this->form, 'f4', $this->params, 'filters', FORM_STYLE_TABLE, 1);?>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="page-personal">
				<div class="row-fluid">
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'user', $this->params, 'personal', FORM_STYLE_TABLE, 1);?>
					</div>
					<div class="span6">
						<?php echo MEFormHelper::renderFieldset($this->form, 'uinfo', $this->params, 'personal', FORM_STYLE_TABLE, 1);?>
					</div>
				</div>
			</div>
		<?php else:?>
			<div class="tab-pane active" id="page-params">
				<div class="row-fluid">

					<div class="span6">
						<?php
						$fieldsets = $this->form->getFieldsets('tmpl_params');
						if(count($fieldsets)): ?>
								<?php foreach ($fieldsets AS $set)
								{
									echo MEFormHelper::renderFieldset($this->form, $set->name, $this->params, 'tmpl_params', FORM_STYLE_TABLE, 1);
								}?>
						<?php endif;?>
					</div>

					<div class="span6">
						<?php
						$fieldsets = $this->form->getFieldsets('tmpl_core');
						if(count($fieldsets)): ?>
							<?php foreach ($fieldsets AS $set)
							{
								echo MEFormHelper::renderFieldset($this->form, $set->name, $this->params, 'tmpl_core', FORM_STYLE_TABLE, 1);
							}
							?>
						<?php endif; ?>
					</div>

				</div>
			</div>
		<?php endif;?>
	</div>

  <input type="hidden" name="task"         value="" />
  <input type="hidden" name="name"         value="<?php echo $this->name; ?>" />
  <input type="hidden" name="type"         value="<?php echo $this->type; ?>" />
  <input type="hidden" name="config"         value="<?php echo $this->config; ?>" />
  <?php if($app->input->get('inner')) :?>
	  <input type="hidden" name="inner"  value="1">
  	<input type="hidden" name="return" value="<?php echo JRequest::getVar('return', base64_encode($_SERVER['HTTP_REFERER']))?>" />
  <?php endif;?>
  <?php echo JHtml::_('form.token'); ?>
</form>
