<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Toolbar helper. Helps to build toolbars and subnmenu bars
 *
 * @author Sergey
 * @package		Cobalt
 * @subpackage	com_cobalt
 *
 */
class MRToolBar extends JToolBarHelper
{
	public static function addSubmenu($vName = 'records')
	{
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/blue-documents-stack.png"> '.
			JText::_('XML_SUBMENU_RECORDS'),
			'index.php?option=com_cobalt&view=records',
			$vName == 'records'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/folder.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_SECTIONS'),
			'index.php?option=com_cobalt&view=sections',
			$vName == 'sections'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/category.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_TYPES'),
			'index.php?option=com_cobalt&view=types',
			$vName == 'types'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/star.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_VOTES'),
			'index.php?option=com_cobalt&view=votes',
			$vName == 'votes'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/balloons.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_COMMENTS'),
			'index.php?option=com_cobalt&view=comments',
			$vName == 'comments'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/price-tag.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_TAGS'),
			'index.php?option=com_cobalt&view=tags',
			$vName == 'tags'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/document-text-image.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_TEMPLATES'),
			'index.php?option=com_cobalt&view=templates',
			$vName == 'templates'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/luggage.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_PACK'),
			'index.php?option=com_cobalt&view=packs',
			$vName == 'packs'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/hammer.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_TOOLS'),
			'index.php?option=com_cobalt&view=tools',
			$vName == 'tools'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/gear.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_CONFIGURATION'),
			'index.php?option=com_config&view=component&component=com_cobalt&return='.base64_encode(JFactory::getURI()),
			$vName == 'config'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/information.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_ABOUT'),
			'index.php?option=com_cobalt&view=about',
			$vName == 'about'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/lifebuoy.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_SUPPORT'),
			'http://support.mintjoomla.com/en/cobalt-8.html',
			$vName == 'html'
		);
	}

	public static function helpW($url, $width, $height)
	{
		$text	= JText::_('C_TOOLBAR_HELP');
		$doTask	= "popupWindow('$url', '".JText::_('C_TOOLBAR_HELP', TRUE)."', $width, $height, 1)";

		$html	= "<a href=\"#\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html .= "<span class=\"icon-32-help\" title=\"$text\">\n";
		$html .= "</span>\n";
		$html	.= "$text\n";
		$html	.= "</a>\n";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'help');
	}
	public static function mass()
	{
		$html = '<button class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
		'.JText::_('CMASS').' <span class="caret"></span></button>';

		$html .= '<ul class="dropdown-menu">';
		$html .= 	'<li><a onclick="Cobalt.submitTask(\'records.change_category\');">'.JText::_('C_TOOLBAR_MASSOP1').'</a></li>';
		$html .= 	'<li><a onclick="Cobalt.submitTask(\'records.change_field\');">'.JText::_('C_TOOLBAR_MASSOP2').'</a></li>';
		$html .= 	'<li><a onclick="Cobalt.submitTask(\'records.change_core\');">'.JText::_('C_TOOLBAR_MASSOP3').'</a></li>';
		$html .= '</ul>';

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'reset');
	}
	public static function addrrec()
	{
		$html = '<button onclick="jQuery(\'#add-info\').toggle()" class="btn btn-small btn-success"><span class="icon-new icon-white"></span>	'.JText::_('JTOOLBAR_NEW').'</button>';

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'new');
	}
	public static function emptyX()
	{
		$document = JFactory::getDocument();
		$document->addScript( JURI::root(TRUE).'/components/com_cobalt/library/js/dropdown.js');

		$html = '<a href="javascript:void(0);" onclick="javascript: emptyCategory();" class="toolbar"  id="reset_click" rel="reset_menu">';
		$html .= "<span class=\"icon-32-reset\" id=\"button_reset\" type=\"Standard\">";
		$html .= "</span>";
		$html .= JText::_('C_TOOLBAR_EMPTY');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'reset');
	}
	public static function reset()
	{
		$html = '<div class="btn-group">';
		$html .= '<button class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
		'.JText::_('CRESET').' <span class="caret"></span></button>';

		$html .= '<ul class="dropdown-menu">';
		$html .= 	'<li><a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('CSURE').'\')){Cobalt.submitTask(\'records.reset_hits\');}">'.JText::_('C_TOOLBAR_RESET_HITS').'</a></li>';
		$html .= 	'<li><a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('CSURE').'\')){Cobalt.submitTask(\'records.reset_com\');}">'.JText::_('C_TOOLBAR_RESET_COOMENT').'</a></li>';
		$html .= 	'<li><a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('CSURE').'\')){Cobalt.submitTask(\'records.reset_vote\');}">'.JText::_('C_TOOLBAR_RESET_RATING').'</a></li>';
		$html .= 	'<li><a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('CSURE').'\')){Cobalt.submitTask(\'records.reset_fav\');}">'.JText::_('C_TOOLBAR_RESET_FAVORIT').'</a></li>';
		$html .= 	'<li><a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('CSURE').'\')){Cobalt.submitTask(\'records.reset_ctime\');}">'.JText::_('C_TOOLBAR_RESET_CTIME').'</a></li>';
		$html .= 	'<li><a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('CSURE').'\')){Cobalt.submitTask(\'records.reset_mtime\');}">'.JText::_('C_TOOLBAR_RESET_MTIME').'</a></li>';
		$html .= 	'<li><a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('CSURE').'\')){Cobalt.submitTask(\'records.reset_extime\');}">'.JText::_('C_TOOLBAR_RESET_EXTIME').'</a></li>';
		$html .= '</ul>';
		$html .= '</div>';
		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'reset');
	}
	public static function close($contr, $string = 'Close')
	{
		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript:" . "document.adminForm.filter_order.value=''; document.adminForm.controller.value='$contr'; " . "submitbutton('');\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-cancel\" type=\"Standard\">";
		$html .= "</span>";
		$html .= JText::_($string);
		$html .= "</a>";
		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'close');
	}
	public static function edit($file)
	{
		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript:" . "if(document.adminForm.boxchecked.value==0){alert('" . JText::_("CSELECTTODELETE")."');}" . "else{ResEdit('$file')}\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-edit\" type=\"Standard\">";
		$html .= "</span>";
		$html .= JText::_('C_TOOLBAR_EDIT');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'edit');
	}

	public static function delete($table, $trash = 0)
	{
		$document = JFactory::getDocument();

		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript:" .
		"if(document.adminForm.boxchecked.value==0){alert('" .
		"Please make a selection from the list to Delete');}" .
		"else{if(confirm('" . JText::_('C_TOOLBAR_CONFIRMDELET') .
		"')){ResDelete('$table', '$trash');}}\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-delete\" type=\"Standard\" id=\"bar_delete\">";
		$html .= "</span>";
		$html .= JText::_('C_TOOLBAR_DELETE');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'delete');
	}
	public static function fields()
	{
		$document = JFactory::getDocument();

		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript:" . "if(document.adminForm.boxchecked.value==0){alert('" .JText::_( "CSELECTTOASSCESSFIEL")."');}" . "else{document.adminForm.controller.value='field'; document.adminForm.filter_order.value=''; submitbutton();}\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-field\" type=\"Standard\" id=\"bar_delete\">";
		$html .= "</span>";
		$html .= JText::_('C_TOOLBAR_FIELDS');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'field');
	}

	/* public static function publish($table, $publish)
	{
		$document = JFactory::getDocument();

		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript:" . "if(document.adminForm.boxchecked.value==0){alert('" . JText::_("CSELECTTOPUBUNPUB"). "');}" . "else{ResPublish(" . ($publish ? 0 : 1) . ", '$table');}\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-" . (! $publish ? 'un' : null) . "publish\" type=\"Standard\" id=\"bar_publish$publish\">";
		$html .= "</span>";
		$html .= ! $publish ? JText::_('C_TOOLBAR_UNPUB') : JText::_('C_TOOLBAR_PUB');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'publish');
	} */
	public static function types()
	{
		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript: " . "document.adminForm.controller.value='type'; document.adminForm.filter_order.value=''; submitbutton();\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-types\" type=\"Standard\">";
		$html .= "</span>";
		$html .= JText::_('C_TOOLBAR_TYPES');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'types');
	}
	public static function category()
	{
		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript: " . "document.adminForm.controller.value='category'; document.adminForm.filter_order.value=''; submitbutton();\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-category\" type=\"Standard\">";
		$html .= "</span>";
		$html .= JText::_('C_TOOLBAR_CATS');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'category');
	}
	public static function records()
	{
		$html = "<a href=\"javascript:void(0);\" onclick=\"javascript: " . "document.adminForm.controller.value='record'; document.adminForm.filter_order.value=''; submitbutton();\" class=\"toolbar\">";
		$html .= "<span class=\"icon-32-record\" type=\"Standard\">";
		$html .= "</span>";
		$html .= JText::_('C_TOOLBAR_CONTENT');
		$html .= "</a>";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'record');
	}
	public static function install()
	{
		$text	= JText::_('C_TOOLBAR_INSTALL');
		$doTask	= "slideinstall('ins')";

		$html = "<button href=\"#\" class=\"btn btn-small\"  data-toggle=\"collapse\" data-target=\"#ins_form\" rel=\"{onClose: function() {}}\">\n";
		$html .= "<i class=\"icon-upload\">\n";
		$html .= "</i>\n";
		$html .= "$text\n";
		$html .= "</button>\n";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'install');
	}
	public static function cr()
	{
		$text	= JText::_('C_TOOLBAR_COPREN');
		$doTask	= "slideinstall('cr')";

		$html = "<button href=\"#\" class=\"btn btn-small\"  data-toggle=\"collapse\" data-target=\"#cr_form\" rel=\"{onClose: function() {}}\">\n";
		$html .= "<i class=\"icon-save-copy\">\n";
		$html .= "</i>\n";
		$html .= "$text\n";
		$html .= "</button>\n";

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', $html, 'cr');
	}
}