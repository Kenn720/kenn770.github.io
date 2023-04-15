<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>

<div class="input text" id="flt_div<?php echo $this->id;?>">
<input autocomplete="off" id="flt<?php echo $module.$this->id;?>" type="text" name="filters[<?php echo $this->key;?>]"
	data-autocompleter-default="<?php echo $this->value;?>"
	value="<?php echo $this->value;?>"></div>

<script type="text/javascript">
Cobalt.typeahead('#flt<?php echo $module.$this->id;?>', {
	field_id: <?php echo $this->id ?>,
	func:'onFilterData',
}, {limit:12});
</script>