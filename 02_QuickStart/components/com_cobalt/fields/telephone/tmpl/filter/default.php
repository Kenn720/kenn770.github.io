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

<input autocomplete="off" data-provide="typeahead" id="flt<?php echo $module.$this->id;?>" type="text" name="filters[<?php echo $this->key;?>]"
	size="20" data-autocompleter-default="<?php echo $this->value;?>"
	value="<?php echo $this->value;?>"/>
<script type="text/javascript">
	FilterTelephone("<?php echo $module.$this->id;?>", <?php echo $section->id;?>);
</script>