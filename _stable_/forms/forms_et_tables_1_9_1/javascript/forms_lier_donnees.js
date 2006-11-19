jQuery.fn.ajaxWait = function() {
	$(this).prepend("<div>"+ajax_image_searching+"</div>");
	return this;
}
jQuery.fn.ajaxAction = function() {
	var id=$(this).id();
	$('#'+id+' a.ajaxAction').click(function(){
		var action = $(this).href();
		var idtarget = action.split('#')[1];
		if (!idtarget) idtarget = id;		
		var url = (($(this).rel()).split('#'))[0];
		var redir = url + "&var_ajaxcharset="+ajaxcharset+"&bloc="+idtarget;
		action = (action.split('#')[0]).replace(/&?redirect=[^&#]*/,''); // l'ancre perturbe IE ...
		$('#'+idtarget).ajaxWait();
		$('#'+idtarget).load(action,{redirect: redir}, function(){ $('#'+idtarget).ajaxAction();});
		return false;
	});
	$('#'+id+' form.ajaxAction').each(function(){
		var idtarget = $(this).children('input[@name=idtarget]').val();
		if (!idtarget) idtarget = $(this).parent().id();
		var redir = $(this).children('input[@name=redirectajax]');
		var url = (($(redir).val()).split('#'))[0];
		$(this).children('input[@name=redirect]').val(url + "&var_ajaxcharset="+ajaxcharset+"&bloc="+idtarget);
		$(redir).after("<input type='hidden' name='var_ajaxcharset' value='"+ajaxcharset+"' />");
		$(this).ajaxForm({"target":'#'+idtarget, 
			"after":
			function(){ 
				$('#'+idtarget).ajaxAction();
			},
			"before":
			function(param,form){ 
				$('#'+idtarget).prepend("<div>"+ajax_image_searching+"</div>");
			}
			});
	});
	var script = $('input[@name=autocompleteUrl]').val();
	$('#autocompleteMe').Autocomplete(
		{
			source: script,
			delay: 300,
			/*fx: {
				type: 'slide',
				duration: 400
			},*/
			autofill: false,
			helperClass: 'autocompleter',
			selectClass: 'selectAutocompleter',
			minchars: 1,
			onSelect : setDonnee,
			/*onShow : fadeInSuggestion,
			onHide : fadeOutSuggestion*/
		}
	);
}
/*var fadeInSuggestion = function(suggestionBox, suggestionIframe){
	$(suggestionBox).fadeTo(300,0.8);alert('show');
};
var fadeOutSuggestion = function(suggestionBox, suggestionIframe){
	$(suggestionBox).fadeTo(300,0);
};*/
var setDonnee = function(data) {
	$('#_id_donnee').val(data.id_donnee);
};

$(document).ready(function(){
		$('#forms_lier_donnees').ajaxAction();
});