<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$Itemid = JFactory::getApplication()->input->getInt('Itemid');
$document = JFactory::getDocument();
$document->addScript(JURI::root(TRUE).'/media/system/js/mootree.js');
$document->addStyleSheet(JURI::root(TRUE).'/media/system/css/mootree.css');

$params = $this->tmpl_params['cindex'];
$parent_id = ($params->get('tmpl_params.cat_type', 2) == 1 && $this->category->id) ? $this->category->id : 1;

$cats_model = $this->models['categories'];
$cats_model->section = $this->section;
$cats_model->parent_id = $parent_id;
$cats_model->order = $params->get('tmpl_params.cat_ordering', 'c.lft ASC');
$cats_model->levels = 0;
$cats_model->all = 0;
$cats_model->nums = ($params->get('tmpl_params.cat_nums') || $params->get('tmpl_params.subcat_nums') || !$params->get('tmpl_params.cat_empty', 1));
$categories = $cats_model->getItems();

$parents = $this->models['categories']->getParentsByChild(isset($this->category->id) ? $this->category->id : 1);
?>
<style>
	LI.first_level {
		list-style-type: none;
	}
	.current {
		font-weight: bold;
	}
</style>
<script type="text/javascript">
	var parents = [<?php echo implode(',', $parents);?>];

	function initTree(id, title, expand)
	{
		tree = new MooTreeControl({
			div: 'tree'+id,
			mode: 'files',
			grid: true,
			theme: '<?php echo JURI::root(TRUE);?>/media/system/images/mootree.gif',
			loader: {icon: '<?php echo JURI::root(TRUE);?>/media/system/images/mootree_loader.gif', text: '<?php echo JText::_('CLOADING')?>'},
			onClick: function (node) {
				window.location = node.data.url;
			},
			onExpand: function(node, state) {
				if(state)
				{
					node.clear();
					if(node.id) id = node.id;
					ajax_getChilds(id, node);
					if(parents.contains(id)) node.selected = true;
				}
			},
		},{
			text: title,
			open: true,
			data: {url: '<?php echo JURI::root(TRUE);?>/index.php?option=com_cobalt&view=records&section_id=<?php echo $this->section->id;?>&cat_id='+ id + '&Itemid=<?php echo $Itemid;?>',
					id: id}
		});

		tree.disable(); // this stops visual updates while we're building the tree...

		var node1 = tree.insert({text:'loading', id:'0'});
		if(expand || parents.contains(id)) {
			tree.expand();
		}
		else {
			tree.collapse();
		}

		tree.enable(); // this turns visual updates on again.
	}

	function ajax_getChilds( id, mytree)
	{
		var req = new Request.JSON({
			url:'<?php echo JRoute::_('index.php?option=com_cobalt&task=ajax.category_childs&tmpl=component', FALSE); ?>',
			method:'post',
			autoCancel:true,
			data:{cat_id: id },
			onComplete: function(json) {
				if(json.success)
				{
					if(json.result.length)
					{
						Array.each(json.result, function(item, index, object){
							item.id = item.id.toInt();
							if( <?php echo $params->get('tmpl_params.cat_empty', 1);?> == 1 || (<?php echo $params->get('tmpl_params.cat_empty', 1);?> == 0 && (item.num_current > 0 || item.num_all > 0 )))
							{
								if(<?php echo $params->get('tmpl_params.cat_nums', 1);?> != 0)
								{
									item.title += ' (' +( (<?php echo $params->get('tmpl_params.cat_nums', 1);?> == 1) ? item.num_current : item.num_all) + ')';
								}
								var node = mytree.insert({
									text: item.title,
									id: item.id,
									open: parents.contains(item.id),
									data: {url: item.link + '&Itemid=<?php echo $Itemid;?>',
										id: 'cat-'+item.id}
								});
								if(item.childs_num > 0)
									node.insert({text:'loading', id:'0'});
								if(parents.contains(item.id))
								{
									node.select(true);
									node.clear();
									ajax_getChilds(item.id, node);
								}
							}
						});
					}
				}
				else
				{
					alert(json.error);
				}
			}
		}).send();
	}
</script>
<?php if (count($categories)):?>
	<div class="contentpane">
		<?php if($this->tmpl_params['cindex']->get('tmpl_core.show_title', 1)):?>
			<h2 class="contentheading"><?php echo JText::_($this->tmpl_params['cindex']->get('tmpl_core.title_label', 'Category Index'))?></h2>
		<?php endif;?>
		<?php foreach ($categories as $cat) :
				if(!$params->get('tmpl_params.cat_empty', 1) && !$cat->num_current && !$cat->num_all) continue;
				$expand = $params->get('tmpl_params.expand', 0);
				if(!$expand && ($this->category && $this->category->id == $cat->id) ) $expand = 1;
				if($params->get('tmpl_params.cat_nums', 1))
				{
					$cat->title .= ' ('.$cat->records_num.')';
				}
				?>
				<div id="tree<?php echo $cat->id;?>"></div>
				<script type="text/javascript">
					initTree(<?php echo $cat->id;?>, '<?php echo $cat->title;?>', <?php echo $expand;?> );
				</script>
		<?php endforeach;	?>
	</div>
<?php endif;?>