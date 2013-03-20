var crayons_textarea = function(){
	$('.crayon-html textarea').tooltip({
		showURL: false
	});
}

$(document).ready(function(){
	$('.bilan a,.bilan abbr,.bilan tr,.bilan td,.bilan .graph,textarea').tooltip({
		showURL: false
	});
	onAjaxLoad(crayons_textarea);
});