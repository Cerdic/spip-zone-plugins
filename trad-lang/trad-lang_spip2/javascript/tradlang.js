var tradlang_switchers = function(){
	$('#formulaire_switcher_langue .fieldset ul,#formulaire_switcher_langue .boutons,.formulaire_choisir_module .fieldset ul,.formulaire_choisir_module .boutons').hide();
	$('#formulaire_switcher_langue .fieldset h3,.formulaire_choisir_module .fieldset h3').css({cursor:'pointer'}).click(function(){
		if($(this).next('ul').is(':hidden')){
			$(this).parents('.formulaire_spip').find('.fieldset ul:hidden,.boutons:hidden').slideDown();
		}else{
			$(this).parents('.formulaire_spip').find('.fieldset ul,.boutons').slideUp();
		}
	});
}

$(document).ready(function(){
	tradlang_switchers();
	onAjaxLoad(tradlang_switchers)
});