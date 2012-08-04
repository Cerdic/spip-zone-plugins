var tradlang_switchers = function(){
	$('#formulaire_switcher_langue .fieldset ul,#formulaire_switcher_langue .boutons,.formulaire_choisir_module .fieldset ul,.formulaire_choisir_module .boutons').hide();
	$('#formulaire_switcher_langue .fieldset h3,.formulaire_choisir_module .fieldset h3').each(function(){
		if($(this).find('a.legend_link').size() == 0){
			$(this).wrapInner('<a href="#" class="legend_link"></a>');
			$(this).find('a.legend_link').unbind('click').click(function(){
				if($(this).parent('h3').next('ul').is(':hidden')){
					$(this).parents('.formulaire_spip').find('.fieldset ul:hidden,.boutons:hidden').slideDown();
				}else{
					$(this).parents('.formulaire_spip').find('.fieldset ul,.boutons').slideUp();
				}
				return false;
			});
		}
	});
	
}

$(document).ready(function(){
	tradlang_switchers();
	onAjaxLoad(tradlang_switchers)
});