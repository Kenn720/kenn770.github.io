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

<?php
if(isset($this->value[0])&& is_array($this->value[0]) && isset($this->value[0]['day'])
	&& isset($this->value[0]['month']) && isset($this->value[0]['year']))
{
	$val = $this->value[0]['year'].'-'.$this->value[0]['month'].'-'.$this->value[0]['day'];
	if ($this->params->get('params.time', 0))
	{
		$val .= ' '.(isset($this->value[0]['hour']) ? $this->value[0]['hour'] : '00');
		$val .= ':'.(isset($this->value[0]['min']) ? $this->value[0]['min'] : '00');
	}
	$this->value[0] = $val;
}
$default = ($this->value && strtotime($this->value[0])) ? $this->value[0] : $this->default;
$s_value = strtotime($default);
?>

<select class="date_list" style="width:50px" name="jform[fields][<?php echo $this->id;?>][0][day]">
	<option value="0"><?php echo  JText::_('D_DAY');?></option>
	<?php
	$selected = $s_value ? date('j', $s_value) : 0;
	for($i = 1; $i <= 31; $i ++) :
	?>
	<option value="<?php echo $i;?>" <?php if($i == $selected) echo 'selected';?>><?php echo $i;?></option>
	<?php endfor; ?>
</select>

<?php $m_list = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');?>
<select class="date_list" style="width:70px" name="jform[fields][<?php echo $this->id;?>][0][month]">
	<option value="0"><?php echo  JText::_('D_MONTH');?></option>
	<?php
	$selected = $s_value ? date('n', $s_value) : 0;
	for($i = 1; $i <= 12; $i ++) :
	?>
	<option value="<?php echo $i;?>" <?php if($i == $selected) echo 'selected';?>><?php echo JText::_($m_list[($i - 1)]);?></option>
	<?php endfor; ?>
</select>

<select class="date_list" style="width:50px" name="jform[fields][<?php echo $this->id;?>][0][year]">
	<option value="0"><?php echo  JText::_('D_YEAR');?></option>
	<?php
	$selected = $s_value ? date('Y', $s_value) : 0;
	for($i = (date('Y') + 10); $i >= date('Y') - 80; $i --) :
	?>
	<option value="<?php echo $i;?>" <?php if($i == $selected) echo 'selected';?>><?php echo $i;?></option>
	<?php endfor; ?>
</select>

<?php if ($this->params->get('params.time', 0)) : ?>
	<select class="date_list" style="width:50px" name="jform[fields][<?php echo $this->id;?>][0][hour]">
		<option value=""><?php echo  JText::_('D_HOUR');?></option>
		<?php
		$selected = $s_value ? date('G', $s_value) : '';
		for($i = 0; $i <= 23; $i ++) :
		?>
		<option value="<?php echo $i;?>" <?php if($i == $selected) echo 'selected';?>><?php echo str_pad($i, 2, 0, STR_PAD_LEFT);?></option>
		<?php endfor; ?>
	</select>

	<select class="date_list" style="width:50px" name="jform[fields][<?php echo $this->id;?>][0][min]">
		<option value=""><?php echo  JText::_('D_MINUTE');?></option>
		<?php
		$selected = $s_value ? date('i', $s_value) : '';
		for($i = 0; $i < 60; $i ++) :
		?>
		<option value="<?php echo $i;?>" <?php if($i == $selected) echo 'selected';?>><?php echo str_pad($i, 2, 0, STR_PAD_LEFT);?></option>
		<?php endfor; ?>
	</select>
<?php endif; ?>
