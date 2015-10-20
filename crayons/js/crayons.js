(function($){
/*
 *	crayons.js (c) Fil, toggg, 2006-2013 -- licence GPL
 */

// le prototype configuration de Crayons
$.prototype.cfgCrayons = function (options) {
	this.url_crayons_html = '?action=crayons_html';
	this.img = {
		'searching':{'txt':'En attente du serveur ...'},
		'edit':{'txt':'Editer'},
		'img-changed':{'txt':'Deja modifie'}
	};
	this.txt = {
	};
	for (opt in options) {
		this[opt] = options[opt];
	}
};

$.prototype.cfgCrayons.prototype.mkimg = function(what, extra) {
	var txt = this.img[what] ? this.img[what].txt : this.img['crayon'].txt;
	return '<em class="crayon-'+what+'" title="'+ txt + (extra ? extra : '') + '"></em>';
};

$.prototype.cfgCrayons.prototype.iconclick = function(c, type) {

	// le + qui passe en prive pour editer tout si classe type--id
	var link = c.match(/\b(\w+)--(\d+)\b/);
	link = link ?
		'<a href="ecrire/?exec=' + link[1] + 's_edit&id_' + link[1] + '=' + link[2] +
		'">' + this.mkimg('edit', ' (' + link[1] + ' ' + link[2] + ')') + '</a>' : '';

	// on recherche une class du type type-champ-id
	// comme article-texte-10 pour le texte de l'article 10
	// ou meta-valeur-meta
	var cray =
				c.match(/\b\w+-(\w+)-\d(?:-\w+)+\b/)   // numeros_lien-type-2-3-article (table-champ-cles)
				|| c.match(/\b\w+-(\w+)-\d+\b/)           // article-texte-10 (inclu dans le precedent, mais bon)
				|| c.match(/\b\meta-valeur-(\w+)\b/)      // meta-valeur-xx
				;

	var boite = !cray ? '' : this.mkimg(type, ' (' + cray[1] + ')');

	return "<span class='crayon-icones'><span>" + boite +
			this.mkimg('img-changed', cray ? ' (' + cray[1] + ')': '') +
			link +"</span></span>";
};

function entity2unicode(txt)
{
	var reg = txt.split(/&#(\d+);/i);
	for (var i = 1; i < reg.length; i+=2) {
		reg[i] = String.fromCharCode(parseInt(reg[i]));
	}
	return reg.join('');
};

function uniAlert(txt)
{
	alert(entity2unicode(txt));
};

function uniConfirm(txt)
{
	return confirm(entity2unicode(txt));
};

// donne le crayon d'un element
$.fn.crayon = function(){
	if (this.length)
		return $(
			$.map(this, function(a){
				return '#'+($(a).find('.crayon-icones').attr('rel'));
			})
			.join(','));
	else
		return $([]);
};

// ouvre un crayon
$.fn.opencrayon = function(evt, percent) {
	if (evt && evt.stopPropagation) {
		evt.stopPropagation();
	}
	return this
	.each(function(){
		// verifier que je suis un crayon
		if (!$(this).is('.crayon'))
			return;

		// voir si je dispose deja du crayon comme voisin
		if ($(this).is('.crayon-has')) {
			$(this)
			.css('visibility','hidden')
			.crayon()
				.show();
		}
		// sinon charger le formulaire
		else {
			// sauf si je suis deja en train de le charger (lock)
			if ($(this).find("em.crayon-searching").length) {
				return;
			}
			$(this)
			.find('>span.crayon-icones span')
			.append(configCrayons.mkimg('searching')); // icone d'attente
			var me=this;
			var offset = $(this).offset();
			var params = {
				'top': offset.top,
				'left': offset.left,
				'w': $(this).width(),
				'h': $(this).height(),
				'ww': (window.innerWidth ? window.innerWidth : (document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.offsetWidth)),
				'wh': (window.innerHeight ? window.innerHeight : (document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.offsetHeight)),
				'em': $(this).css('fontSize'), // Bug de jquery resolu : http://bugs.jquery.com/ticket/760
				'class': me.className,
				'color': $(this).css('color'),
				'font-size': $(this).css('fontSize'),
				'font-family': $(this).css('fontFamily'),
				'font-weight': $(this).css('fontWeight'),
				'line-height': $(this).css('lineHeight'),
				'min-height': $(this).css('lineHeight'),
				'text-align': $(this).css('textAlign'),
				'background-color': $(this).css('backgroundColor'),
				'self': configCrayons.self
			};
			if (me.type) params.type = me.type;
			if (params['background-color'] == 'transparent'
			|| params['background-color'] == 'rgba(0, 0, 0, 0)') {
				$(me).parents()
				.each(function(){
					var bg = $(this).css('backgroundColor');
					if (bg != 'transparent'
					&& (params['background-color'] == 'transparent'
					|| params['background-color'] == 'rgba(0, 0, 0, 0)'))
						params['background-color'] = bg;
				});
			}
			$.post(configCrayons.url_crayons_html,
				params,
				function (c) {
					try {
						c = $.parseJSON(c);
					} catch(e) {
						c = {'$erreur': 'erreur de communication :' + '  ' + e.message, '$html':''};
					}
					$(me)
					.find("em.crayon-searching")
						.remove();
					if (c.$erreur) {
						uniAlert(c.$erreur);
						return false;
					}
					id_crayon++;

					var position = 'absolute';
					$(me).parents().each(function(){
						if($(this).css("position") == "fixed")
							position = 'fixed';
					});

					$(me)
					.css('visibility','hidden')
					.addClass('crayon-has')
					.find('>.crayon-icones')
						.attr('rel','crayon_'+id_crayon);
					// Detection IE sur sa capacite a gerer zoom :
					// http://www.sitepoint.com/detect-css3-property-browser-support/
					if (document.createElement("detect").style.zoom === "") {
						$(me).css({'zoom':1});
					}
					var pos = $(me).offset();
					$('<div class="crayon-html" id="crayon_'+id_crayon+'"></div>')
					.css({
						'position':position,
						'top':pos['top']-1,
						'left':pos['left']-1
					})
					.appendTo('body')
					.html(c.$html);
					$(me)
					.activatecrayon(percent);
					// Si le crayon a une taille mini qui le fait deborder
					// a droite de l'ecran, recadrer vers la gauche
					var diff = $('#crayon_'+id_crayon).offset().left + $('#crayon_'+id_crayon).width() - $(window).width();
					if (diff>0) {
						 $('#crayon_'+id_crayon)
						 .css({'left': parseInt(pos['left'])-diff});
					}
				}
			);
		}
	});
};

// annule le crayon ouvert (fonction destructive)
$.fn.cancelcrayon = function() {
	this
		.filter('.crayon-has')
		.css('visibility','visible')
		.removeClass('crayon-has')
		.removeClass('crayon-changed')
	.crayon()
		.remove();
	return this;
};

// masque le crayon ouvert
$.fn.hidecrayon = function() {
	this
	.filter('.crayon-has')
	.css('visibility','visible')
	.crayon()
		.hide()
		.removeClass('crayon-hover');
	return this;
};

// active un crayon qui vient d'etre charge
$.fn.activatecrayon = function(percent) {
	var focus = false;
	this
	.crayon()
	.click(function(e){
		e.stopPropagation();
	});
	this
	.each(function(){
		var me = $(this);
		var crayon = $(this).crayon();
		crayon
		.find('form')
			.append(
				$('<input type="hidden" name="self" />')
				.attr('value',configCrayons.self)
			)
			.ajaxForm({
			"dataType":"json",
			"error": function(d) {
				uniAlert('erreur de communication');
				crayon
				.empty()
				.append(
					$('<div class="error">')
					.html(d.responseText || d.error || 'erreur inconnue')
				)
				.css({
					background: 'white',
					color: 'black',
					width: '480px',
					border: 'red solid 2px',
					padding: '10px'}
				);
			},
			"success": function(d) {
				// parfois le JSON n'est pas renvoye sous forme d'objet
				// mais d'une chaine encadree de <pre>...</pre>
				if (typeof d == "string") {
					try {
						d = $.parseJSON(d.replace(/^<pre>/,'').replace(/<[/]pre>$/,''));
					} catch(e) {
						d = {'$erreur': 'erreur de communication :' + '  ' + e.message, '$html':''};
					}
				}
				me
				.find("em.crayon-searching")
					.remove();

				//Remise a zero des warnings invalides (unwrap)
				crayon
				.find("span.crayon-invalide p")
					  .remove();
				crayon
				.find("span.crayon-invalide")
					  .each(function(){
					      $(this).replaceWith( this.childNodes );
						}
					    );

				if(d.$invalides) {
					for (invalide in d.$invalides) {
						//Affichage des warnings invalides
						d.$invalides[invalide]['retour']?retour=d.$invalides[invalide]['retour']:retour='';
						d.$invalides[invalide]['msg']?msg=d.$invalides[invalide]['msg']:msg='';
						crayon
						    .find("*[name='content_"+invalide+"']")
							.wrap("<span class=\"crayon-invalide\"></span>")
						    .parent()
						    .append("<p>"
								+ retour
								+ " "
								+ msg
								+ "</p>"
							);
						}

				}

				if (d.$erreur > '') {
					if (d.$annuler) {
						if (d.$erreur > ' ') {
							uniAlert(d.$erreur);
						}
						me
						.cancelcrayon();
					} else {
							uniAlert(d.$erreur+'\n'+configCrayons.txt.error);
					}
				}

				if (d.erreur > '' || d.$invalides) {
					crayon
					.find('form')
						.css('opacity', 1.0)
						.find(".crayon-boutons,.resizehandle")
							.show()
						.end()
						.find('.crayon-searching')
							.remove();
						return false;
				}
				// Desactive celui pour qui on vient de recevoir les nouvelles donnees
				$(me)
				.cancelcrayon();
				// Insere les donnees dans *tous* les elements ayant le meme code
				var tous = $(
					'.crayon.crayon-autorise.' +
						me[0].className.match(/crayon ([^ ]+)/)[1]
				)
				.html(
					d[$('input.crayon-id', crayon).val()]
				)
				.iconecrayon();

				// Invalider des préchargements ajax
				if (typeof jQuery.spip == 'object' && typeof jQuery.spip.preloaded_urls == 'object') {
					jQuery.spip.preloaded_urls = {};
				}

				// Declencher le onAjaxLoad normal de SPIP
				if (typeof jQuery.spip == 'object' && typeof jQuery.spip.triggerAjaxLoad == 'function') {
					jQuery.spip.triggerAjaxLoad(tous.get());
				}
				// SPIP 2.x
				else if (typeof triggerAjaxLoad == 'function') {
					triggerAjaxLoad(tous.get());
				}
			}})
			.bind('form-submit-validate',function(form,a, e, options, veto){
				if(!veto.veto)
				crayon
				.find('form')
					.css('opacity', 0.5)
					.after(configCrayons.mkimg('searching')) // icone d'attente
					.find(".crayon-boutons,.resizehandle")
						.hide();
			})
			// keyup pour les input et textarea ...
			.keyup(function(e){
				crayon
				.find(".crayon-boutons")
					.show();
				me
				.addClass('crayon-changed');
				e.cancelBubble = true; // ne pas remonter l'evenement vers la page
			})
			// ... change pour les select : ici on submit direct, pourquoi pas
			.change(function(e){
				crayon
				.find(".crayon-boutons")
					.show();
				me
				.addClass('crayon-changed');
				e.cancelBubble = true;
			})
			.keypress(function(e){
				e.cancelBubble = true;
			})
			// focus par defaut (crayons sans textarea/text, mais uniquement menus ou fichiers)
			.find('input:visible:not(:disabled):not([readonly]):first').focus().end()
			.find("textarea.crayon-active,input.crayon-active[type=text]")
				.each(function(n){
					// focus pour commencer a taper son texte directement dans le champ 
					// sur le premier textarea non readonly ni disabled
					// on essaie de positionner la selection (la saisie) au niveau du clic
					// ne pas le faire sur un input de [type=file]
					if (n==0) {
						if(!$(this).is(':disabled, [readonly]')){
							this.focus();
							focus = true;
						}
						// premiere approximation, en fonction de la hauteur du clic
						var position = parseInt(percent * this.textLength);
						this.selectionStart=position;
						this.selectionEnd=position;
					}else if(!focus && !$(this).is(':disabled, [readonly]'))
						this.focus();
				})
			.end()
			.keydown(function(e){
				if(!e.charCode && e.keyCode == 119 /* F8, windows */) {
						crayon
						.find("form.formulaire_crayon")
						.submit();
				}
				if (e.keyCode == 27) { /* esc */
					me
					.cancelcrayon();
				}
			})
			.keypress(function(e){
				// Clavier pour sauver
				if (
				(e.ctrlKey && (
					/* ctrl-s ou ctrl-maj-S, firefox */
					((e.charCode||e.keyCode) == 115) || ((e.charCode||e.keyCode) == 83))
					/* ctrl-s, safari */
					|| (e.charCode==19 && e.keyCode==19)
				) ||
				(
					e.shiftKey && (e.keyCode == 13) /* shift-return */
				)
				) {
					crayon
					.find("form.formulaire_crayon")
					.submit();
				}
				var maxh = this.className.match(/\bmaxheight(\d+)?\b/);
				if (maxh) {
					maxh = maxh[1] ? parseInt(maxh[1]) : 200;
					maxh = this.scrollHeight < maxh ? this.scrollHeight : maxh;
					if (maxh > this.clientHeight) {
						$(this).css('height', maxh + 'px');
					}
				}
			})
			.find(".crayon-submit")
				.click(function(e){
					e.stopPropagation();
					$(this)
					.parents("form:eq(0)")
					.submit();
				})
			.end()
			.find(".crayon-cancel")
				.click(function(e){
					e.stopPropagation();
					me
					.cancelcrayon();
				})
			.end()
			// decaler verticalement si la fenetre d'edition n'est pas visible
			.each(function(){
				var offset = $(this).offset();
				var hauteur = parseInt($(this).css('height'));
				var scrolltop = $(window).scrollTop();
				var h = $(window).height();
				if (offset['top'] - 5 <= scrolltop)
					$(window).scrollTop(offset['top'] - 5);
				else if (offset['top'] + hauteur - h + 20 > scrolltop)
					$(window).scrollTop(offset['top'] + hauteur - h + 30);
				// Si c'est textarea, on essaie de caler verticalement son contenu
				// et on lui ajoute un resizehandle
				$("textarea", this)
				.each(function(){
					if (percent && this.scrollHeight > hauteur) {
						this.scrollTop = this.scrollHeight * percent - hauteur;
					}
				})
				.resizehandle()
					// decaler les boutons qui suivent un resizer de 16px vers le haut
					.next('.resizehandle')
						.next('.crayon-boutons')
						.addClass('resizehandle_boutons');
			})
		.end();
		// Declencher le onAjaxLoad normal de SPIP
		// (apres donc le chargement de la page de saisie (controleur))
		if (typeof jQuery.spip == 'object' && typeof jQuery.spip.triggerAjaxLoad == 'function') {
			jQuery.spip.triggerAjaxLoad(crayon.get());
		}
		// SPIP 2.x
		else if (typeof triggerAjaxLoad == 'function') {
			triggerAjaxLoad(crayon.get());
		}
	});
};

// insere les icones et le type de crayon (optionnel) dans l'element
$.fn.iconecrayon = function(){
	return this.each(function() {
		var ctype = this.className.match(/\b[^-]type_(\w+)\b/);
		type = (ctype) ? ctype[1] : 'crayon';
		if (ctype) this.type = type; // Affecte son type a l'objet crayon
		$(this).prepend(configCrayons.iconclick(this.className, type))
		.find('.crayon-' + type + ', .crayon-img-changed') // le crayon a clicker lui-meme et sa memoire
			.click(function(e){
				$(this).parents('.crayon:eq(0)').opencrayon(e);
			});
		});
};

// initialise les crayons
$.fn.initcrayon = function(){
	var editme = function(e){
		timeme=null;
		$(this).opencrayon(e,
			// calcul du "percent" du click par rapport a la hauteur totale du div
			((e.pageY ? e.pageY : e.clientY) - document.body.scrollTop - this.offsetTop)
			/ this.clientHeight);
	};
	var timeme;
	this
	.addClass('crayon-autorise')
	.dblclick(editme)
	.bind("touchstart",function(e){var me=this;timeme=setTimeout(function(){editme.apply(me,[e]);},800);})
	.bind("touchend",function(e){if (timeme) {clearTimeout(timeme);timeme=null;}})
	.iconecrayon()
	.hover(	// :hover pour MSIE
		function(){
			$(this)
			.addClass('crayon-hover')
			.find('>span.crayon-icones')
				.find('>span>em.crayon-' + (this.type||'crayon') + ',>span>em.crayon-edit')
					.show();//'visibility','visible');
		},function(){
			$(this)
			.removeClass('crayon-hover')
			.find('>span.crayon-icones')
				.find('>span>em.crayon-' + (this.type||'crayon') + ',>span>em.crayon-edit')
					.hide();//('visibility','hidden');
		}
	);
	return this;
};

// demarrage
$.fn.crayonsstart = function() {
	if (!configCrayons.droits) return;
	id_crayon = 0; // global

	// sortie, demander pour sauvegarde si oubli
	if (configCrayons.txt.sauvegarder) {
		$(window).unload(function(e) {
			var chg = $(".crayon-changed");
			if (chg.length && uniConfirm(configCrayons.txt.sauvegarder)) {
				chg.crayon().find('form').submit();
			}
		});
	}

	// demarrer les crayons
	if ((typeof crayons_init_dynamique == 'undefined') || (crayons_init_dynamique==false)) {

		// compat jQuery 1.9
		if (typeof $.fn.live == 'undefined') {
			$.fn.live = function( types, data, fn ) {
				$( this.context ).on( types, this.selector, data, fn );
				return this;
			};
		}
		$('.crayon:not(.crayon-init)')
		.live('mouseover touchstart', function(e) {
			$(this)
			.addClass('crayon-init')
			.filter(configCrayons.droits)
			.initcrayon()
			.trigger('mouseover');
			if (e.type=='touchstart')
				$(this).trigger('touchstart');
		});
	}

	// un clic en dehors ferme tous les crayons ouverts ?
	if (configCrayons.cfg.clickhide)
	$("html")
	.click(function(){
		$('.crayon-has')
		.hidecrayon();
	});
};

})(jQuery);
