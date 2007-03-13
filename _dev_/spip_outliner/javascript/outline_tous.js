$(document).ready(function(){
	$('dl').click(function(evt){
		$('dl.sel').removeClass('sel');
		$(this).addClass('sel');
		evt.cancelBubble = true;
		if (evt.stopPropagation) evt.stopPropagation();
		update_toolbar_icones();
	});
	$('dl').hover(function(){	$(this).addClass('hover');},function(){	$(this).removeClass('hover');});
	$('dl').dblclick( function() {window.location.replace($(this).attr('name')); } );
	$('body.outline_tous').click(function(){$('dl.sel').removeClass('sel');		update_toolbar_icones();});
});

function update_toolbar_icones(){
	if ($('dl.sel').size()>0)
		$('#toolbar a.inactif').removeClass('inactif');
	else
		$('#toolbar a.EditOutline,#toolbar a.SupprimeOutline').addClass('inactif');
}
function del_outline(lien){
	sel = $('dl.sel');
	if (sel.size()){
		sel=sel.eq(0);
		href = $(lien).attr('href')+'&id_form='+$(sel).attr('id');
		window.location.replace(href);
	}
	return false;
}
function ImporterOutline(){
	$('#outline_import:hidden').show('slow');
}
function EditOutline(){
	sel = $('dl.sel');
	if (sel.size()){
		sel=sel.eq(0);
		window.location.replace($(sel).attr('name')); 
	}
}