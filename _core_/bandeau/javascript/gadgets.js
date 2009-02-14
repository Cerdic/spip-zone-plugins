function init_gadgets(url_toutsite,url_navrapide,url_agenda,html_messagerie){
    var t=null;
	jQuery('#boutonbandeautoutsite')
	.one('mouseover',function(event){
		if ((typeof(window['_OUTILS_DEVELOPPEURS']) == 'undefined') || ((event.altKey || event.metaKey) != true)) {
			changestyle('bandeautoutsite');
			jQuery('#gadget-rubriques')
			.load(url_toutsite).show();
		} else { window.open(url_toutsite+'&transformer_xml=valider_xml'); }
	})
	.one('focus', function(){jQuery(this).mouseover();})
	.bind('mouseover',function(){clearTimeout(t);jQuery('#gadget-rubriques').show();})
	.bind('mouseout',function(){t=setTimeout(function(){jQuery('#gadget-rubriques').hide();},300);});

    jQuery('#gadget-rubriques')
      .bind('mouseover',function(){clearTimeout(t);})
      .bind('mouseout',function(){t=setTimeout(function(){jQuery('#gadget-rubriques').hide();},300);});
}