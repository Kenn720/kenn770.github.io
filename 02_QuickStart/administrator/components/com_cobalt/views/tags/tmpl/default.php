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
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<style type="text/css">
.icon-48-tags {
	background-image:url(components/com_cobalt/images/titles/tags.png);
}
</style>
<script type="text/javascript">

  var showedForm = 0;
  var linkView   = '';

  function showForm( id )
  {
    if( showedForm == 0 ) {

      showedForm = id;

      var container = document.getElementById( "tag_container_"+id );
      var link      = document.getElementById( "tag_"+id );
      linkView = container.innerHTML;
      var tag = link.innerHTML;

      container.innerHTML =
        '<div class="input-append pull-left">' +
			'<input type="text" style="margin-left: 5px;" name="tag" value="' + tag + '" />' +
			'<button rel="tooltip" class="btn" type="button" onclick="submitbutton(\'tags.save\');"><i class="icon-save"></i></button> '+
			'<button rel="tooltip" class="btn" type="button" onclick="cancelForm();"><i class="icon-cancel"></i></button>' +
			'<input type="hidden" name="id" value="' + id + '" />' +
		'</div>';



    } else {

      cancelForm( showedForm );
      showForm( id );

    }
  }

  function cancelForm( )
  {
    var container = document.getElementById( "tag_container_"+showedForm );
    container.innerHTML = linkView;
    showedForm = 0;
  }
</script>

<h1><?php echo JText::_('CTAGS')?></h1>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm"  id="adminForm">
	<div class="btn-toolbar" id="filter-bar">
		<div class="input-append btn-group pull-left">
			<input type="text" placeholder="<?php echo JText::_('CFILTER_SEARCH_TAGSDESC'); ?>" style="margin-left: 5px;" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" />

			<button rel="tooltip" class="btn" type="submit" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button rel="tooltip" class="btn" type="button" onclick="document.id('filter_search').value='';this.form.submit();" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
		</div>


		<div class="btn-group pull-right ">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="btn-group pull-right ">
			<label for="limit" class="element-invisible">ghdfh</label>
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', false, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
		<div class="btn-group pull-right ">
			<select name="filter_category" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('CFILTERSECTION');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('cobalt.sections'), 'value', 'text', $this->state->get('filter.category'));?>
			</select>
		</div>
	</div>
	<div class="clr"> </div>

	<table class="table table-striped">
		<thead>
			<th width="1%">
				<?php echo JText::_('CNUM')?>
			</th>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  'CTAGNAME', 't.tag', $listDirn, $listOrder); ?>
			</th>
			<th width="10%" class="nawrap center">
				<?php echo JHtml::_('grid.sort',  'CCREATED', 't.ctime', $listDirn, $listOrder); ?>
			</th>
			<th width="15%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', 'CLANGUAGE', 't.language', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort',  'ID', 't.id', $listDirn, $listOrder); ?>
			</th>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php $k=1; foreach ($this->items as $i => $row) :?>

			<tr class="<?php $k = 1 - $k; echo "row$k"; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td id="tag_container_<?php echo $row->id; ?>">
					<a href="javascript: void(0); showForm(<?php echo $row->id; ?>)" id="tag_<?php echo $row->id; ?>"><?php echo $row->tag; ?></a>
				</td>
				<td class="nowrap center small">
					<?php $data = new JDate( $row->ctime ); echo $data->format( JText::_('CDATE1' ) ); ?>
				</td>
				<td class="center">
					<?php echo $row->language; ?>
				</td>
				<td align="center">
					<?php echo $row->id; ?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>