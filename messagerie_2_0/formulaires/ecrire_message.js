function set_auteur(li){
	var id_auteur = undefined;
	var box = $('#destinataire').siblings('.details');
	if (li!=undefined && li.extra[0]) {
		id_auteur = li.extra[0];
		var nom = $(li).html();
		if (box.find('input[value='+id_auteur+']').length==0){
			box.append(" <span class='dest'>"
			+nom
			+"<input type='hidden' name='destinataires[]' value='"+id_auteur+"' /> "
			+$(box).find('span.dest:first').html()
			+"</span>");
		}
	}
	$(box)
	  .find('span.dest')
	  .hover(function(){$(this).addClass('hover');},function(){$(this).removeClass('hover');})
	  .find('img').click(function(){$(this).parent().remove();});
	$('#destinataire').attr('value','');//.get(0).focus();
}
function formulaire_ecrire_message_init(){
	if ($("#destinataire").length) {
		if ($("#destinataire")[0].autocompleter==undefined) {
			$('#destinataire').autocomplete(url_find_friend, {minChars:3, mustMatchOrEmpty:1,selectFirst:true,matchSubset:0, matchContains:1, cacheLength:10, onItemSelect:set_auteur });
			$('#destinataire').parent().bind('click',function(){$('#destinataire').get(0).focus();})
			set_auteur();
		}
	}
}
if (window.jQuery){
$('document').ready(function(){
	formulaire_ecrire_message_init();
	onAjaxLoad(formulaire_ecrire_message_init);
});
}
