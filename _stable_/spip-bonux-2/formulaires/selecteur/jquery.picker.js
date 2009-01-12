/**
item_picked et picker doivent seulement etre voisins

<ul class='item_picked'>..</ul>
... 
<div class='picker'>
...
<xx class='item_picker'>
</xx>
...
</div>
**/
jQuery(document).ready(function(){
	var picked = jQuery('ul.item_picked');
	if (picked.length) {
		picked.find('>li').removeClass('last').find('li:last').addClass('last');
	}
});

jQuery.fn.item_pick = function(id_item,name,title){
	var picked = this.parents('.item_picker').siblings('ul.item_picked');
	if (!picked.length) {
		this.parents('.item_picker').before("<ul class='item_picked'></ul>");
		picked = this.parents('.item_picker').siblings('ul.item_picked');
	}
	var select = picked.is('.select');
	if (select)
		picked.html('');
	else
		jQuery('li.on',picked).removeClass('on');
	var sel=jQuery('input[value='+id_item+']',picked);
	if (sel.length==0){
		jQuery('li:last',picked).removeClass('last');
		picked.append('<li class="last on">'
		+'<input type="hidden" name="'+name+'[]" value="'+id_item+'"/>'
		+ title
		+(select?"":" <a href='#' onclick='jQuery(this).item_unpick();return false;'>"
		  +"<img src='"+img_unpick+"' /></a>"
		  )
		+'<span class="sep">, </span></li>');
	}
	else
		sel.parent().addClass('on');
	return this; // don't break the chain
}
jQuery.fn.item_unpick = function(){
	var picked = this.parents('ul.item_picked');
	this.parent().remove();
	picked.find('>li').removeClass('last').find('li:last').addClass('last');
}