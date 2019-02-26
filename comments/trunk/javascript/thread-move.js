/*  Code de Cedric Morin permettant de deplacer le formulaire
	forum afin de le placer en dessous du message auquel on repond */

;(function( $ ) {
	jQuery.fn.add_reply_to = function(id_thread){
		var me = jQuery(this).parents('li.comment-li').eq(0);
		if (me.find('#formulaire_forum').length==0){
			jQuery('#formulaire_forum').closest('.ariaformprop').siblings('p.comment-reply').show();
			jQuery('#formulaire_forum')
				.closest('.ariaformprop')
				.hide()
				.detach()
				.appendTo(me)
				.slideDown('fast')
				.find('form')
				.removeClass('noajax')
				.find('input[name=id_forum]').val(id_thread);

			jQuery('#formulaire_forum').closest('.ariaformprop').siblings('p.comment-reply').hide();
			jQuery('#formulaire_forum').find('form.preview,.reponse_formulaire').remove();
			jQuery(me).find('.comment').last().find('p').last().positionner(true);
			var connect = jQuery('#formulaire_forum .saisie_session_nom a,#formulaire_forum .session_qui .details a').eq(0);
			if(connect.length!=0){
				var url = connect.attr('href').match(/url=([^&"']*)/);
				url = escape(unescape(url[1]).replace(/#.*/, "")+"#reply"+id_thread);
				connect.attr('href',connect.attr('href').replace(/url=([^&"']*)/,"url="+url));
			}
		}
	}
	function hash_reply(){
		var ancre = window.location.hash;
		var id;
		if ((id=ancre.match(/^#(reply)([0-9]+$)/)) && jQuery("#comment"+id[2]).length==1){
			var p = jQuery("#comment"+id[2]).parents('li.comment-li').eq(0).find('p.comment-reply');
			if (!p.length)
				p = jQuery("#comment"+id[2]).parents('ul').eq(0).siblings('p.comment-reply');
			p.find('a').eq(0).click();
		}
	}
	jQuery(function(){
		var ancre = window.location.hash;
		var id, id_forum, id_search, nom;
		// #comment100200 (id_forum)
		// #comment100200-100198 (id_forum - id_thread) (pour les boucles qui paginent par thread)
		if (id = ancre.match(/^#(forum|comment|reply)([0-9]+)(-[0-9]+)?$/)) {
			nom = id[1];
			id_forum = id_search = id[2];
			if (typeof id[3] !== 'undefined') {
				id_forum = id[2];
				id_search = id[3].substr(1);
			}
		}
		if (id && jQuery("#comment" + id_forum).length==0) {
			var a = jQuery('.comments-thread a.lien_pagination').last();
			var href = a.attr('href');
			href = href.replace(/debut_comments-list=[0-9]+#.*$/,'debut_comments-list=@'+id_search + ancre);
			a.after("<a href='"+href+"' style='visibility:hidden' id='comment"+id_forum+"' class='lien_pagination'>Go</a>");
			jQuery(a.parents('div.ajaxbloc').first()).ajaxbloc();
			if (nom == 'reply') {
				onAjaxLoad(hash_reply);
			}
			jQuery("#comment" + id_forum).eq(0).click();
		} else if (id && nom != 'comment' && jQuery("#comment" + id_forum).length==1) {
			window.location.hash = "#comment" + id_forum;
		}
		hash_reply();
	});
})( jQuery );
