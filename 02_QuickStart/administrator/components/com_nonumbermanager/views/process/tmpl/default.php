<?php
/**
 * @package         NoNumber Extension Manager
 * @version         5.2.5
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.framework');
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

$task = JFactory::getApplication()->input->get('task');

$config = JComponentHelper::getParams('com_nonumbermanager');

$script = "
	/* NoNumber Extension Manager variable */
	var NNEM_IDS =   [ '" . implode("', '", array_keys($this->items)) . "' ];
	var NNEM_TOKEN = '" . JSession::getFormToken() . "';
	var NNEM_REFRESH_ON_CLOSE = " . (JFactory::getApplication()->input->get('refresh_on_close') ? 'true' : 'false') . ";
";
JFactory::getDocument()->addScriptDeclaration($script);

NNFrameworkFunctions::script('nnframework/script.min.js', '16.3.12531');
NNFrameworkFunctions::script('nonumbermanager/process.min.js', '5.2.5');

NNFrameworkFunctions::stylesheet('nnframework/style.min.css', '16.3.12531');
NNFrameworkFunctions::stylesheet('nonumbermanager/process.min.css', '5.2.5');
?>

<div id="nnem">
	<div class="titles">
		<div class="title pre process">
			<h2>
				<?php echo JText::_('NNEM_TITLE_' . strtoupper($task)); ?>:
				<span class="btn btn-primary" onclick="nnManagerProcess.process('<?php echo $task; ?>');">
					<?php echo JText::_('NN_START'); ?>
				</span>
			</h2>
		</div>
		<div class="title failed process hide">
			<h2>
				<?php echo JText::_('NNEM_TITLE_' . strtoupper($task)); ?>:
				<span class="btn btn-primary" onclick="nnManagerProcess.process('<?php echo $task; ?>', true);">
					<?php echo JText::_('NNEM_TITLE_RETRY'); ?>
				</span>
			</h2>

			<div class="alert alert-danger errors" style="display:none;">
				<h4 class="alert-heading"><?php echo JText::_('ERROR'); ?></h4>

				<div>
					<p class="alert-message"><?php echo JText::_('NNEM_MEET_REQUIREMENTS'); ?></p>
				</div>
			</div>
			<div class="alert alert-warning warnings" style="display:none;">
				<h4 class="alert-heading"><?php echo JText::_('WARNING'); ?></h4>

				<div></div>
			</div>
		</div>
		<div class="title processing hide">
			<h2><?php echo JText::sprintf('NNEM_PROCESS_' . strtoupper($task), '...'); ?></h2>
		</div>
		<div class="title done process hide">
			<div class="alert alert-success">
				<h2><?php echo JText::_('NNEM_TITLE_FINISHED'); ?></h2>
			</div>
			<?php if ($task != 'uninstall') : ?>
				<div class="alert alert-notice"><?php echo JText::_('NNEM_CLEAN_CACHE'); ?></div>
				<div class="alert alert-warning warnings" style="display:none;">
					<h4 class="alert-heading"><?php echo JText::_('WARNING'); ?></h4>

					<div></div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<table class="table processlist">
		<tbody>
			<?php foreach ($this->items as $item) : ?>
				<tr id="row_<?php echo $item->id; ?>">
					<td width="1%" nowrap="nowrap" class="ext_name">
						<span class="icon-nonumber icon-<?php echo $item->alias; ?>"></span>
						<span id="ext_name_<?php echo $item->id; ?>"><?php echo JText::_($item->name); ?></span>
					</td>
					<td class="statuses">
						<input type="hidden" id="url_<?php echo $item->id; ?>" value="<?php echo $item->url; ?>">

						<div class="queue_<?php echo $item->id; ?> status process queued">
							<span class="label"><?php echo JText::_('NNEM_QUEUED'); ?></span>
						</div>
						<div class="processing_<?php echo $item->id; ?> status processing hide">
							<div class="progress progress-striped active">
								<div class="bar" style="width: 100%;"></div>
							</div>
						</div>
						<div class="success_<?php echo $item->id; ?> status success process hide">
							<span class="label label-success"><?php echo JText::_(($task == 'uninstall') ? 'NNEM_UNINSTALLED' : 'NNEM_INSTALLED'); ?></span>
						</div>
						<div class="failed_<?php echo $item->id; ?> status failed process hide">
							<span class="label label-important"><?php echo JText::_('NNEM_INSTALLATION_FAILED'); ?></span>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
