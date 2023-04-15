Cobalt = {};

Cobalt.hidehead = function()
{
	jQuery('header.header').css('display', 'none');
	jQuery('div.container-main').css('margin-top', '55px');
};

/**
 * If you want to create link that filter but current value.
 *
 * For example filter by content type as link. It sets filter value and
 * submit the form.
 *
 * @param el
 *            string ID of the INPUT element
 * @param val
 *            string value to set.
 */
Cobalt.setAndSubmit = function(el, val) {
	var elm = jQuery('#' + el);
	elm.val(val);
	elm.parents('form').submit();
};

Cobalt.yesno = function(yes, no)
{
	var y = jQuery(yes);
	var n = jQuery(no);
	y.on('click', function(){
		y.addClass('btn-success');
		n.removeClass('btn-danger');
		jQuery('input[type="radio"]', n).removeAttr('checked' );
		jQuery('input[type="radio"]', y).attr('checked', true);
	});
	n.on('click', function(){
		n.addClass('btn-danger');
		y.removeClass('btn-success');
		jQuery('input[type="radio"]', y).removeAttr('checked');
		jQuery('input[type="radio"]', n).attr('checked', true);
	});

}

/**
 * This method allow you create link or toolbar button to make an action on
 * selected records.
 *
 * @param task
 *            Joomla task. eg: resords.delete
 */
Cobalt.submitTask = function(task) {
	if (jQuery('input[name="boxchecked"]').val() == 0) {
		alert('Please first make a selection from the list');
	} else {
		Joomla.submitbutton(task);
	}
};

Cobalt.orderTable = function(ordr) {
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	if (order != ordr) {
		dirn = 'asc';
	} else {
		dirn = direction.options[direction.selectedIndex].value;
	}
	Joomla.tableOrdering(order, dirn, '');
};

Cobalt.addTmplEditLink = function(type, field_id, inside, root)
{
	var el = jQuery('#' + field_id + "_link");
	el.html('');
	jQuery('select[id=' + field_id + '] option:selected').each(function(){
		if(this.value == 0) return true;
		var config = this.value.split('.')[1];
		//console.log(config);
		var name = this.value.split('.')[0];
		var btn = jQuery(document.createElement('button'))
			.attr({
				'class': 'btn btn-mini',
				'type':'button',
			})
			.html('<i class="icon-options"></i> ' + name)
			.bind('click', function(){
				var url = root + 'administrator/index.php?option=com_cobalt&view=templates&layout=form&cid[]=['+name+'],['+type+']&config='+config+'&tmpl=component';
				if(inside == 'component')
				{
					url += '&inner=1';
					//console.log(url);
					window.location = url;
				}
				else
				{
					SqueezeBox.open(this, {
						'url': url,
						'handler': 'iframe',
						'size': {x: 950, y: 550}
					});
				}
			});

		el.append(btn);
		el.append('<br>');
	});
};

Cobalt.redrawBS = function() {
	$$('.hasTip').each(function(el) {
		var title = el.get('title');
		if (title) {
			var parts = title.split('::', 2);
			el.store('tip:title', parts[0]);
			el.store('tip:text', parts[1]);
		}
	});
	new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});

	jQuery('*[rel=tooltip]').tooltip()
	jQuery('*[rel=popover]').popover({
		trigger: 'hover'
	});

	jQuery('.radio.btn-group label').addClass('btn');
	jQuery(".btn-group label:not(.active)").click(function() {
		var label = jQuery(this);
		var input = jQuery('#' + label.attr('for'));

		if (!input.prop('checked')) {
			label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
			if(input.val()== '') {
					label.addClass('active btn-primary');
			 } else if(input.val()==0) {
					label.addClass('active btn-danger');
			 } else {
			label.addClass('active btn-success');
			 }
			input.prop('checked', true);
		}
	});
	jQuery(".btn-group input[checked=checked]").each(function() {
		if(jQuery(this).val()== '') {
		   jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
		} else if(jQuery(this).val()==0) {
		   jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
		} else {
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
		}
	});
};
