$(document).ready(function(){
	$('#formulaire_switcher_langue .fieldset ul,#formulaire_switcher_langue .boutons').hide();
	$('#formulaire_switcher_langue .fieldset h3').css({cursor:'pointer'}).click(function(){
		if($(this).next('ul').is(':hidden')){
			$(this).parents('.formulaire_spip').find('*:hidden').slideDown();
		}else{
			$(this).parents('.formulaire_spip').find('.fieldset ul,.boutons').slideUp();
		}
	});
});