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
function focus_zone(selecteur){
	jQuery(selecteur).eq(0).find('a,input:visible').get(0).focus();
	return false;
}
jQuery(document).ready(function(){
	// deplier le menu au focus clavier,
	// enlever ce depliement si passage a la souris,
	// delai de fermeture.
	var timer; // pour timeout du menu...
	jQuery.fn.menuFocus = function(){
		// deplier le ul enfant
		jQuery(this).focus(function(){
			jQuery('#bando_navigation li.actif').removeClass('actif');
			jQuery(this).parent().addClass('actif');
		})
		// cacher en partant de l'onglet...
		.blur(function(){
			jQuery(this).parents('li.actif').removeClass('actif');
		})
		// remonter au li parent
		.parent()
		// le replier si un hover de souris sur un autre onglet,
		// timer sur la fermeture des onglets pour ne pas que ca aille trop vite
		.hover(function(){
			clearTimeout(timer);
			if (jQuery(this).parent().parent().is('li'))
				jQuery(this).parent().parent().addClass('actif').siblings('li').removeClass('actif');
			jQuery(this).addClass('actif').siblings('li').removeClass('actif');
		}, function(){
			var me = jQuery(this);
			timer = setTimeout(function(){
				me.removeClass('actif');
			}, 600);
		})

		// afficher le menu de l'onglet si un lien enfant devient actif
		.find('li > a').focus(function(){
			jQuery('#bando_navigation li.actif').removeClass('actif');
			jQuery(this).parent().parent().parent().addClass('actif');
		});
		return this;
	}
	jQuery('#bando_navigation li >a').menuFocus();
	jQuery('#bandeau_haut #formRecherche input').hover(function(){
		jQuery('#bandeau_haut ul.actif').trigger('mouseout');
	});
	jQuery('#bando_liens_rapides a')
		.focus(function(){
			jQuery('#bando_liens_rapides').addClass('actif');
		})
		.blur(function(){
			jQuery('#bando_liens_rapides').removeClass('actif');
		});
});
