var onglet_actif = undefined;
jQuery.fn.desactive_onglet = function() {
	var url = $(this).children('a').href();
	if (url){
		var ancre = url.split('#'); ancre = ancre[1];
		$('#'+ancre).hide();
	}
	$(this).removeClass('onglet_on').addClass('onglet');
}

jQuery.fn.active_onglet = function(hash) {
	if (onglet_actif)	$(onglet_actif).desactive_onglet();
	onglet_actif = this;
	var url = $(this).children('a').href();
	var ancre = url.split('#'); ancre = ancre[1];
	$(this).addClass('onglet_on').removeClass('onglet');
	$('#'+ancre).show();
	if (hash)
		window.location.hash=hash;
	else
		window.location.hash=ancre;
}

function refresh_apercu(r){
	$('#apercu_gauche').html(r);
	$('#apercu').html(r);
}
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
		var redir = url + "&var_ajaxcharset=utf-8&bloc="+idtarget;
		action = action.replace(/redirect=[^&#]*/,'');
		$('#'+idtarget+',#apercu_gauche').ajaxWait();
		$('#'+idtarget).load(action,{redirect: redir}, function(){ $('#'+idtarget).ajaxAction();});
		$.get( url+"&var_ajaxcharset=utf-8&bloc=apercu" , function(data){refresh_apercu(data);} );
		if (idtarget!='proprietes')
			$('#proprietes').load(url+"&var_ajaxcharset=utf-8&bloc=proprietes",function(){ $('#proprietes').ajaxAction(); });
		return false;
	});
	$('#'+id+' form.ajaxAction').each(function(){
		var idtarget = $(this).children('input[@name=idtarget]').val();
		if (!idtarget) idtarget = $(this).parent().id();
		var redir = $(this).children('input[@name=redirect]');
		var url = (($(redir).val()).split('#'))[0];
		$(redir).val(url + "&var_ajaxcharset=utf-8&bloc="+idtarget);
		$(this).ajaxForm('#'+idtarget, 
			// apres
			function(){ 
				$('#'+idtarget).ajaxAction();
				$.get(url+"&var_ajaxcharset=utf-8&bloc=apercu",function(data){refresh_apercu(data);});
				if (idtarget!='proprietes')
					$('#proprietes').load(url+"&var_ajaxcharset=utf-8&bloc=proprietes",function(){ $('#proprietes').ajaxAction(); });
			},
			// avant
			function(){ $('#'+idtarget+",#apercu_gauche").prepend("<div>"+ajax_image_searching+"</div>");}
			);
	});
}

$(document).ready(function(){
	var hash = window.location.hash;
	var onglets = $('#barre_onglets div.onglet');
	$(onglets)
		.each(function(){ $(this).desactive_onglet()})
		.click(function(){ $(this).active_onglet(); })
		.mouseout(function(){$(onglet_actif).addClass('onglet_on');});
	if ((hash=='#champs')||(hash=='#champ_visible')||(hash=='#nouveau_champ'))
		$(onglets).eq(2).active_onglet(hash);
	else if (window.location.hash=='proprietes')
		$(onglets).eq(1).active_onglet();
	else
		$(onglets).eq(0).active_onglet();

	$('#champs').ajaxAction();
	$('#proprietes').ajaxAction();
});