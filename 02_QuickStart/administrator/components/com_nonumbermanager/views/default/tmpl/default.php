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
JHtml::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.popover');

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

$ids = array();
foreach ($this->items as $item)
{
	$ids[] = $item->id;
}

$config            = JComponentHelper::getParams('com_nonumbermanager');
$check_data        = $config->get('check_data', 1);
$hide_notinstalled = $config->get('hide_notinstalled', 0);

$key    = trim($config->get('key'));
$js_key = $key ? strtolower(substr($key, 0, 8) . md5(substr($key, 8))) : '';

$script = "
	var NNEM_IDS = ['" . implode("', '", $ids) . "'];
	var NNEM_NOUPDATE = '" . JText::_('NNEM_ALERT_NO_ITEMS_TO_UPDATE', true) . "';
	var NNEM_NONESELECTED = '" . JText::_('NNEM_ALERT_NO_ITEMS_SELECTED', true) . "';
	var NNEM_FAIL = '" . JText::_('NNEM_ALERT_FAIL', true) . "';
	var NNEM_CHANGELOG = '" . JText::_('NN_CHANGELOG', true) . "';
	var NNEM_TIMEOUT = " . (int) $config->get('timeout', 5) . ";
	var NNEM_TOKEN = '" . JSession::getFormToken() . "';
	var NNEM_KEY = '" . $js_key . "';
";
JFactory::getDocument()->addScriptDeclaration($script);

NNFrameworkFunctions::script('nnframework/script.min.js', '16.3.12531');
NNFrameworkFunctions::script('nonumbermanager/script.min.js', '5.2.5');

NNFrameworkFunctions::stylesheet('nnframework/style.min.css', '16.3.12531');
NNFrameworkFunctions::stylesheet('nonumbermanager/style.min.css', '5.2.5');

$script = "
	jQuery(document).ready(function() {
		nnManager.refreshData(" . ($check_data ? 1 : 0) . ");
	});
";
JFactory::getDocument()->addScriptDeclaration($script);

$loading = '<div class="progress progress-striped active" style="min-width: 60px;"><div class="bar" style="width: 100%;"></div></div>';
?>
	<div id="nnkey" <?php echo ($key || !$config->get('show_key_field', 1)) ? ' style="display:none;"' : '' ?>>
		<form action="<?php echo JRoute::_('index.php?option=com_nonumbermanager'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
			<div class="well">
				<h4><?php echo JText::_('NNEM_DOWNLOAD_KEY'); ?></h4>

				<p id="nnkey_text_empty"><?php echo html_entity_decode(JText::sprintf('NNEM_DOWNLOAD_KEY_DESC', '<a href="https://www.nonumber.nl/purchase" target="_blank">', '</a>', '<a href="https://www.nonumber.nl/downloads" target="_blank">', '</a>')); ?></p>

				<p id="nnkey_text_invalid"
				   style="display:none;"><?php echo html_entity_decode(JText::sprintf('NNEM_DOWNLOAD_KEY_INVALID', '<a href="https://www.nonumber.nl/downloads" target="_blank">', '</a>')); ?></p>

				<div>
					<?php
					require_once JPATH_SITE . '/plugins/system/nnframework/fields/key.php';
					$field   = new JFormFieldNN_Key;
					$element = new SimpleXMLElement('<field name="key" type="nn_key" filter="raw" action="Joomla.submitbutton(\'storekey\')" />');
					$field->setup($element, $key);
					echo $field->__get('input');
					?>
				</div>
				<input type="hidden" name="task" value="">
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>

	<a id="nnem_modal" href=""></a>
	<div id="nnem">
		<form action="" name="adminForm" id="adminForm">
			<table class="table<?php echo $hide_notinstalled ? ' hide_not_installed' : ''; ?>">
				<thead class="hidden-phone">
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)">
						</th>
						<th width="200" class="left nowrap"><?php echo JText::_('NNEM_EXTENSION'); ?></th>
						<th width="16" class="hidden-tablet"><!-- website --></th>
						<th width="48" class="hidden-tablet"><?php echo JText::_('NNEM_TYPE'); ?></th>

						<th width="5%"><!-- spacer --></th>

						<th width="60" class="left"><?php echo JText::_('NNEM_INSTALLED'); ?></th>

						<th width="5%"><!-- spacer --></th>

						<th width="1%" class="left"><span class="loaded hide"><?php echo JText::_('NNEM_NEW'); ?></span></th>
						<th width="60"><!-- new version --></th>

						<th width="5%"><!-- spacer --></th>

						<th><!-- pro --></th>

						<th width="5%"><!-- spacer --></th>

						<th width="20"><!-- uninstall --></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr id="row_<?php echo $item->id; ?>" class="<?php
						if ($item->installed)
						{
							echo 'installed'
								. ($item->pro ? ' pro_installed' : ' free_installed')
								. (empty($item->missing) ? '' : ' has_missing');
						}
						else
						{
							echo 'not_installed';
						}
						?>">
							<td class="center hidden-phone ext_checkbox">
								<span class="select hide">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</span>
							</td>
							<td class="nowrap ext_name">
								<input type="hidden" id="url_<?php echo $item->id; ?>" value="">
								<span class="hasPopover" data-trigger="hover"
								      title="<?php echo JText::_($item->name); ?>" data-content="<?php echo JText::_($item->name . '_DESC'); ?>">
									<span class="icon-nonumber icon-<?php echo $item->alias; ?> hidden-phone"></span>
									<?php echo JText::_($item->name); ?>
								</span>
							</td>
							<td class="center hidden-phone hidden-tablet ext_website">
								<a href="https://www.nonumber.nl/<?php echo $item->id; ?>" target="_blank">
									<span class="icon-out-2"></span>
								</a>
							</td>
							<td class="nowrap hidden-phone hidden-tablet ext_types">
								<?php echo $loading; ?>
								<div class="loaded">
									<?php foreach ($item->types as $type) : ?>
										<?php
										switch ($type->type)
										{
											case 'mod':
												$icon = '<span class="label label-important">M</span>';
												break;
											case 'plg_system':
												$icon = '<span class="label label-info">P<small>S</small></span>';
												break;
											case 'plg_editors-xtd':
												$icon = '<span class="label label-info">P<small>B</small></span>';
												break;
											default:
												$icon = '<span class="label label-success">C</span>';
												break;
										}
										?>
										<span class="not_installed data hide disabled" rel="tooltip"
										      title="<?php echo JText::_('NN_' . strtoupper($type->type)); ?>">
											<?php echo $icon; ?>
										</span>
										<span class="installed data hide" rel="tooltip" title="<?php echo JText::_('NN_' . strtoupper($type->type)); ?>">
											<a href="index.php?<?php echo $type->link; ?>" target="_blank"><?php echo $icon; ?></a>
										</span>
									<?php endforeach; ?>
								</div>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="nowrap ext_installed">
								<?php echo $loading; ?>
								<div class="loaded hide">
									<span class="installed nowrap data hide">
										<span class="uptodate data hide">
											<span class="current_version badge badge-success" rel="tooltip"
											      title="<?php echo makeSafe(JText::_('NNEM_COMMENT_UPTODATE')); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="downgrade data hide">
											<span class="current_version badge badge-success" rel="tooltip"
											      title="<?php echo makeSafe(JText::_('NNEM_COMMENT_DOWNGRADE')); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="update data hide">
											<span class="current_version badge badge-warning" rel="tooltip"
											      title="<?php echo makeSafe(JText::_('NNEM_COMMENT_UPDATE')); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="pro_installed data hide">
											<span class="pro_no_access data hide">
												<span class="current_version badge badge-important hasPopover" data-trigger="hover" data-placement="right"
												      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('NNEM_COMMENT')); ?>"
												      data-content="<?php echo makeSafe(JText::_('NNEM_COMMENT_NO_PRO')); ?>">
													<?php echo $item->version; ?>
												</span>
											</span>
										</span>
										<?php
										$missing = '';
										if ($item->installed && !empty($item->missing))
										{
											$missing = array();
											foreach ($item->missing as $m)
											{
												$missing[] = JText::_('NN_' . strtoupper($m));
											}
											$missing = JText::sprintf('NNEM_MISSING_EXTENSIONS', implode(',', $missing));
										}
										?>
										<span class="missing data <?php echo $missing ? '' : 'hide'; ?>">
											<span class="current_version badge badge-important hasPopover" data-trigger="hover" data-placement="right"
											      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('NNEM_COMMENT')); ?>"
											      data-content="<?php echo makeSafe($missing); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="hidden-tablet hidden-desktop">
											<span class="pro_installed label label-info data hide">P</span>
										</span>
										<span class="hidden-phone">
											<span class="pro_installed label label-info data hide">PRO</span>
										</span>
									</span>
								</div>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="center nowrap ext_install">
								<span class="install btn btn-small btn-success data hide" onclick="nnem_function('install', '<?php echo $item->id; ?>');">
									<span class="icon-box-add"></span> <?php echo JText::_('NNEM_TITLE_INSTALL'); ?>
								</span>
								<span class="update btn btn-small btn-warning data hide" onclick="nnem_function('update', '<?php echo $item->id; ?>');">
									<span class="icon-upload"></span> <?php echo JText::_('NNEM_TITLE_UPDATE'); ?>
								</span>
								<span class="reinstall label label-success disabled data hide">
									<?php echo JText::_('NNEM_TITLE_UPTODATE'); ?>
								</span>

								<div class="downgrade data hide">
									<span class="pro_access data">
										<span class=" btn btn-small" onclick="nnem_function('downgrade', '<?php echo $item->id; ?>');"
										      rel="tooltip" title="<?php echo makeSafe(JText::_('NNEM_COMMENT_DOWNGRADE')); ?>">
											<?php echo JText::_('NNEM_TITLE_DOWNGRADE'); ?>
										</span>
									</span>
									<span class="pro_installed data hide">
										<span class="pro_no_access data hide">
											<span class="btn btn-small btn-danger disabled hasPopover" data-trigger="hover" data-placement="right"
											      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('NNEM_COMMENT')); ?>"
											      data-content="<?php echo makeSafe(JText::_('NNEM_COMMENT_NO_PRO')); ?>">
												<?php echo JText::_('NNEM_TITLE_DOWNGRADE'); ?>
											</span>
										</span>
									</span>
								</div>

								<div class="pro_installed data hide">
									<span class="pro_no_access data hide">
										<span class="btn btn-small btn-danger disabled hasPopover" data-trigger="hover" data-placement="right"
										      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('NNEM_COMMENT')); ?>"
										      data-content="<?php echo makeSafe(JText::_('NNEM_COMMENT_NO_PRO')); ?>">
											<span class="icon-upload"></span> <?php echo JText::_('NNEM_TITLE_UPDATE'); ?>
										</span>
									</span>
								</div>
								<span class="hidden-tablet hidden-desktop nowrap">
									<div class="clearfix"></div>
									<span class="changelog data hide">
										<a href="https://www.nonumber.nl/<?php echo $item->id; ?>#changelog" target="_blank">
											<span class="new_version badge"></span></a>
										<span class="pro_access label label-info data hide">P</span>
									</span>
								</span>
							</td>
							<td class="hidden-phone nowrap ext_new">
								<span class="refresh no_external btn btn-small btn-primary data hide" onclick="nnem_function('refresh');">
									<span class="icon-refresh"></span> <?php echo JText::_('NNEM_CHECK_DATA'); ?>
								</span>
								<span class="changelog data hide">
									<span class="hidden-tablet">
										<a href="https://www.nonumber.nl/<?php echo $item->id; ?>#changelog" target="_blank"
										   class="hasPopover" data-trigger="hover"
										   title="<?php echo JText::_('NN_CHANGELOG'); ?>" data-content="">
											<span class="new_version badge"></span></a>
									</span>
									<span class="hidden-desktop">
										<a href="https://www.nonumber.nl/<?php echo $item->id; ?>#changelog" target="_blank"
										   class="changelog data hide">
											<span class="new_version badge"></span></a>
									</span>
									<span class="pro_access label label-info data hide">PRO</span>
								</span>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="center nowrap hidden-phone">
								<span class="pro_not_installed data hide">
									<span class="pro_available data hide">
										<span class="pro_no_access data hide">
											<a style="margin-bottom:4px;" class="btn btn-small btn-info hidden-tablet"
											   href="https://www.nonumber.nl/purchase?ext=<?php echo $item->id; ?>" target="_blank">
												<span class="icon-basket"></span> <?php echo JText::_('NNEM_BUY_PRO_VERSION'); ?>
											</a>
											<a style="margin-bottom:4px;" class="btn btn-small btn-info hidden-desktop"
											   rel="tooltip" title="<?php echo JText::_('NNEM_BUY_PRO_VERSION'); ?>"
											   href="https://www.nonumber.nl/purchase?ext=<?php echo $item->id; ?>" target="_blank">
												<span class="icon-basket"></span>
											</a>
										</span>
									</span>
								</span>
								<span class="pro_installed data hide">
									<span class="pro_key_invalid data hide">
										<a style="margin-bottom:4px;" class="btn btn-small btn-warning hidden-tablet"
										   href="https://www.nonumber.nl/purchase?ext=<?php echo $item->id; ?>" target="_blank">
											<span class="icon-basket"></span> <?php echo JText::_('NNEM_RENEW_SUBSCRIPTION'); ?>
										</a>
										<a style="margin-bottom:4px;" class="btn btn-small btn-warning hidden-desktop"
										   rel="tooltip" title="<?php echo JText::_('NNEM_RENEW_SUBSCRIPTION'); ?>"
										   href="https://www.nonumber.nl/purchase?ext=<?php echo $item->id; ?>" target="_blank">
											<span class="icon-basket"></span>
										</a>
									</span>
								</span>

								<span class="reinstall btn btn-small btn-default data hide" onclick="nnem_function('reinstall', '<?php echo $item->id; ?>');">
									<?php echo JText::_('NNEM_TITLE_REINSTALL'); ?>
								</span>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="center nowrap hidden-phone ext_uninstall">
								<?php if ($item->id != 'nonumberextensionmanager') : ?>
									<span class="installed btn btn-micro btn-danger data hide"
									      rel="tooltip" data-placement="left" title="<?php echo JText::_('NNEM_TITLE_UNINSTALL'); ?>"
									      onclick="nnem_function('uninstall', '<?php echo $item->id; ?>');">
										<span class="icon-cancel-2"></span>
									</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="task" value="">
			<input type="hidden" name="boxchecked" value="0">
		</form>
	</div>
<?php
// Copyright
require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
echo NoNumberVersions::getFooter('NONUMBER_EXTENSION_MANAGER', $config->get('show_copyright', 1));

function makeSafe($str)
{
	return str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $str);
}
