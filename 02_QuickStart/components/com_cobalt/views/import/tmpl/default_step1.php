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
<?php $jsoncode = time();?>
<ul class="nav nav-pills">
	<li class="active"><a><?php echo JText::_('CIMPORTUPLOAD')?></a></li>
	<li><a><?php echo JText::_('CIMPORTCONFIG')?></a></li>
	<li><a><?php echo JText::_('CIMPORTFINISH')?></a></li>
</ul>

<div id="progress" class="progress progress-striped hide">
	<div class="bar" style="width: 0%;"></div>
</div>
<div id="progress2" class="progress progress-striped hide">
	<div class="bar" style="width: 0%;"></div>
</div>

<hr>

<p>Import is very sensitive and complicated process.</p>
<p></p>
<p></p>

<ul>
	<li>You may to compress <b>.csv</b> or <b>.json</b> into <b>.zip</b> archive before upload.</li>
	<li>Single <b>.csv</b> or <b>.json</b> file have to be in archive root.	</li>
	<li>If you have any files like pictures, gallery or PDFs, upload then to the server in any folder.</li>
	<li>All previously imported articles will updated only associated fields.</li>
	<li>Unselected fields will not be overridden or emptied.</li>
</ul>

<p>
	Allowed formats are <b>.csv</b>, <b>.json</b> or compress it with <b>.zip</b>.
</p>

<script src="<?php echo JUri::root(true)?>/media/mint/js/uploader/js/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo JUri::root(true)?>/media/mint/js/uploader/js/jquery.iframe-transport.js"></script>
<script src="<?php echo JUri::root(true)?>/media/mint/js/uploader/js/jquery.fileupload.js"></script>

<style>
#fileupload {
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	opacity: 0;
	filter: alpha(opacity=0);
	transform: translate(-300px, 0) scale(4);
}
</style>


<form action="<?php echo JFactory::getURI()->toString();?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php if($this->types):?>
		<div class="control-group">
			<label class="control-label" for="type"><?php echo JText::_('CTYPETOIMPORT')?></label>
			<div class="controls">
				<?php echo JHtml::_('select.genericlist', $this->types, 'type', '', 'id', 'name');?>
			</div>
		</div>
	<?php else: ?>
		<input type="hidden" name="type" value="<?php echo $this->type;  ?>"/>
	<?php endif;?>
	<div class="control-group">
		<label class="control-label" for="type"><?php echo JText::_('CCSVDELIMITER')?></label>
		<div class="controls">
			<select name="delimiter">
				<option value=","><?php echo JText::_('CIMPORTDELCOMA')?></option>
				<option value=";"><?php echo JText::_('CIMPORTDELSEMI')?></option>
			</select>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="type"><?php echo JText::_('CIMPORTUPLOAD')?></label>
		<div class="controls">
			<span class="btn btn-success" style="position: relative;">
			<?php echo JText::_('CIMPORTUPLOADFILE')?>
			<input id="fileupload" type="file" name="files[]">
			</span>
		</div>
	</div>
	
	
	<div class="form-actions">
		<button class="pull-right btn btn-large btn-primary" id="next-step" disabled="disabled"><?php echo JText::_('CNEXT')?></button>
	</div>
	<input type="hidden" name="key" value="<?php echo $jsoncode?>">
	<input type="hidden" name="step" value="2">
</form>

<script>
(function ($) {
	$('#fileupload').fileupload({
		url: '<?php echo JRoute::_('index.php?option=com_cobalt&task=import.upload&tmpl=component', false);?>',
		dataType: 'json',
		maxChunkSize: 2000000,
		multipart: false,
		maxNumberOfFiles: 1,
		singleFileUploads: true,
		type: 'POST',
		change: function() {
			$('#progress2').hide();
			$('#progress2 .bar').text('').css('width', '0').removeClass('bar-warning').removeClass('bar-success');
			$('#progress .bar').text('').css('width', '0').removeClass('bar-warning').removeClass('bar-success');
		},
		done: function (e, data) {
			$('#progress').removeClass('active');
			$('#progress .bar')
				.text('<?php echo JText::_('CIMPORTUPLOADFINISH')?>')
				.css('width', '100%')
				.addClass('bar-success').removeClass('bar-warning');
			
			$.each(data.result.files, function (index, file) {
				if(file.error)
				{
					$('#progress .bar')
						.text(file.error)
						.css('width', '100%')
						.addClass('bar-warning')
						.removeClass('bar-success');
					return;
				}
				console.log(data);
				$.ajax({
					url: '<?php echo JRoute::_('index.php?option=com_cobalt&task=import.analize&tmpl=component&json='.$jsoncode, false);?>&file='+file.name,
					dataType: 'json',
					type: 'POST',
					beforeSend: function() {
						$('#progress2').show().addClass('active');
						setTimeout(function(){updatebar('<?php echo $jsoncode?>');}, 200);
					}
				}).done(function(data){
				});
				return false;
			});
		},
		progressall: function (e, data) {

			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress').show().addClass('active');
			$('#progress .bar')
				.css('width', progress + '%')
				.html('<?php echo JText::_('CIMPORTUPLOAD')?> <b>'+progress+'%</b>')
				.removeClass('bar-success').removeClass('bar-warning');
		}
	});
	function updatebar(name)
	{
		$.getJSON('<?php echo JUri::root();?>tmp/'+name+'.json', {dataType: 'json'}, function(data){
			if(data.error)
			{
				$('#progress').removeClass('active');
				$('#progress2 .bar')
					.text(data.error)
					.css('width', '100%')
					.addClass('bar-warning')
					.removeClass('bar-success');
			}
			else if(data.status < 100)
			{
				$('#progress2 .bar')
					.css('width', data.status + '%')
					.html(data.msg + ' <b>'+data.status+'%</b>');
				setTimeout(function(){updatebar(name);}, 200);
			}
			else if(data.status >= 100)
			{
				$('#progress2').removeClass('active');
				$('#progress2 .bar')
					.text('<?php echo JText::_('CIMPORTANYLIZEFINISH')?>')
					.css('width', '100%').removeClass('bar-warning')
					.addClass('bar-success');

				$('#next-step').removeAttr('disabled');
			}
				
		}).fail(function(){
				setTimeout(function(){updatebar(name);}, 200);
		}); 
		
	}
}(jQuery));
</script>