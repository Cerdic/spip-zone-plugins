function lit_message(id_message,url_mark){
	jQuery('#message_'+id_message).toggleClass('on');
	if (jQuery('#message_'+id_message+'>div.message').is('.new')){
		jQuery('#message_'+id_message+'>div.message').removeClass('new');
		jQuery.get(url_mark);
	}
	return false;
}