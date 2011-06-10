$(document).ready(function(){
	if ($('ul#mots_sel').length) {
		$('ul#mots_sel li').removeClass('last');
		$('ul#mots_sel li:last').addClass('last');
	}
});
function mot_sel_add(id_mot,titre,name){
	if (!$('ul#mots_sel').length)
		$('div.mots_chemin').parents('li.editer_mots').find('label:first').after("<ul id='mots_sel'></ul>");
	var sel ='ul#mots_sel input[value='+id_mot+']';
	$('ul#mots_sel li.show').removeClass('show');
	if ($(sel).length==0){
		$('ul#mots_sel li:last').removeClass('last');
		$('ul#mots_sel').append('<li class="last show">'
		+'<input type="hidden" name="'+name+'[]" value="'+id_mot+'"/>'
		+titre
		+" <a href='#' onclick='mot_sel_remove(this);return false;'>"
		+"<img src='"+img_fermer+"' /></a>"
		+'<em>, </em></li>');
	}
	else {
		$(sel).parent().addClass('show');
	}
}
function mot_sel_remove(node){
	$(node).parent().fadeOut('fast');
	$('ul#mots_sel li').removeClass('show');
	setTimeout(function(){
		$(node).parent().remove();
		$('ul#mots_sel li').removeClass('last');
		$('ul#mots_sel li:last').addClass('last');
	},400);
}