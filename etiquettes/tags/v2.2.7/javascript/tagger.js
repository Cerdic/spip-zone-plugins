function tags_remove_tag(node){
	var ul = jQuery(node).closest('ul.tags');
	jQuery(node).parent().remove();
	if (!jQuery('li',ul).length)
		ul.addClass('empty').append(tags_linotag);
	if (ul.is('.autosubmit'))
		tags_autosubmit(ul.closest('form'));
}
function tags_autosubmit(form){jQuery(form).ajaxSubmit();}
function tags_autoadd(node,e){
	var unicode=e.keyCode? e.keyCode : e.charCode
	if (unicode==188){
		var input = jQuery(node);
		var ul = input.siblings('ul.tags');
		var tags = input.attr('value');
		tags=tags.split(',');
		var submit = false;
		for(var i=0;i<tags.length;i++){
			var tag = tags[i].replace(/^\s+/g,'').replace(/\s+$/g,'');
			if(tag.length){
				var li = jQuery('<li class="tag"><span class="label"><i class="icon-tag"></i>'+tag+'</span></li>');
				li.append(jQuery('<input type="hidden" name="tags[]" />').attr('value',tag));
				if (ul.is('.supprimable')) li.find('>span').append(tags_remove_img.clone());
				ul.append(li);
				if (ul.is('.empty'))
				 	ul.removeClass('empty').find('.notag').remove();
				submit = true;
			}
		}
		input.attr('value','');
		e.preventDefault();
		if (submit && ul.is('.autosubmit'))
			tags_autosubmit(ul.closest('form'));
	}
}