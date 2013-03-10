var tradlang_switchers = function(){
	$('#formulaire_switcher_langue .fieldset ul,#formulaire_switcher_langue .boutons,div.tradlang_choisir_module:not(".module_changed") .formulaire_choisir_module .fieldset ul,div.tradlang_choisir_module:not(".module_changed") .formulaire_choisir_module .boutons,table.info.trads tbody').hide();
	$('#formulaire_switcher_langue .fieldset h3,.formulaire_choisir_module .fieldset h3,table.info.trads caption').each(function(){
		if($(this).find('a.legend_link').size() == 0){
			$(this).wrapInner('<a href="#" class="legend_link"></a>');
			$(this).find('a.legend_link').unbind('click').click(function(){
				if($(this).parent('h3').next('ul').is(':hidden') || $(this).parent('caption').next('tbody').is(':hidden')){
					$(this).parents('.formulaire_spip,table').find('.fieldset ul:hidden,.boutons:hidden,tbody:hidden').slideDown();
				}else{
					$(this).parents('.formulaire_spip,table').find('.fieldset ul,.boutons,tbody').slideUp();
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