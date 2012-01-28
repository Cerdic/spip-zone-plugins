/*jshint asi: true, bitwise: true, boss:true, curl: true, debug:false, eqeqeq:true,
  eqnull: false, evil: false, forin: true, immed: false, laxbreak: false, newcap: true,
  noarg:true, noempty: true, nonew: true, onevar: true, passfail: false, plusplus: true,
  regexp: false, undef: true, strict: true, white: false, smarttabs: true */
/*global jQuery:true, History:true*/

function AjaxNav() {
    "use strict";
    var SITE_URL		= AjaxNav.options.siteURL,
    onAjaxNavReq		= $.Event("onAjaxNavReq"),
    onAjaxNavLoad		= $.Event("onAjaxNavLoad"),
    onAjaxNavLocalisedLoad	= $.Event("onAjaxNavLocalisedLoad");

    // parse la partie dynamique d'une url et retourne un objet contenant les parametres spip.
    function urlToVariables(urlString) {
	var tmpArray	= [],
	result		= {},
	i		= 0,
	variable	= '';

	// on ne garde que la partie dynamique de l'url, que ce soit le sommaire,
	// une url ajax, ou une url spip
	urlString = urlString.replace(SITE_URL, '');
	urlString = urlString.replace(/^#!/, '');
	urlString = urlString.replace(AjaxNav.options.urlPrefix, '');

	// on traite les ancres
	if (urlString.search(/#[^!]/) !== -1) {
	    result.anchor = urlString.replace(/.*#([^!].*)/, '$1');
	}
	// on vire l'ancre
	urlString = urlString.replace(/(.*)#[^!].*/, '$1');

	// on separe les variables avant de les traiter une par une.
	tmpArray = urlString.split(/[\?\&]/g);
	for (i = 0; i < tmpArray.length; i += 1) {
	    variable = tmpArray[i];
	    // si format abbrege, on ajoute un param 'short' qui stock l'abbreviation.
	    if ((i === 0) && ($.inArray('=', variable) === -1)) {
		result.short = variable;
	    } else { // sinon, on remplit l'objet...
		variable		= variable.split('=');
		result[variable[0]]	= variable[1];
	    }
	}
	if (result.short) { // si abrege on calcule et ajoute les parametres page et id
	    variable	= result.short.match(/^\D+/)[0];
	    result.page = variable;
	    if (result.short.match(/\d+$/)) {
		result['id_' + variable] = result.short.match(/\d+$/)[0];
	    }
	} else if (result.short === '') { // si url vide c'est une page de sommaire
	    result.page = 'sommaire';
	}

	// on test si l'url doit etre chargee en ajax, et si c'est le cas, on le stock
	// dans le parametre 'ajax'.
	if ($.inArray(result.page, AjaxNav.options.pagesToAjaxify) !== -1) {
	    result.ajax = true;
	} else {
	    result.ajax = false;
	}
	// les liens de recalcul ne sont jamais a charger en ajax.
	if ((result.var_mode) && (result.var_mode.search('calcul') !== -1)) {
	    result.ajax = false;
	}

	return result;
    }

    ///////////////////////
    // updatePage  ////////
    ///////////////////////

    function scrollToAnchor(anchor) {
	$('a[href$="' + anchor + '"]').filter(':first').each(function (i) {
	    this.scrollIntoView();
	});
    }

    // charge la page en ajax a chaque evenement statechange
    $(window).bind('statechange', function() {
	var param, urlString, State = urlToVariables(History.getState().url);

	$('.ajax_nav').trigger(onAjaxNavReq);

	// on utilise la variable State pour calculer l'url a charger
	// en ajax.
	urlString = '';
	for (param in State) {
	    if (State.hasOwnProperty(param)) {
		if ((param === 'short') || (param === 'anchor') ||
		    (param === 'ajax')) {
		    continue;
		} else if (param === 'page') {
		    urlString = AjaxNav.options.urlPrefix + 'page=' + State[param] + urlString;
		    if (AjaxNav.options.urlPrefix === '') { urlString = '?' + urlString; }
		} else {
		    urlString = urlString + '&' + param + '=' + State[param];
		}
	    }
	}
	urlString += State.anchor ? ('#' + State.anchor) : '';
	urlString = SITE_URL + urlString;
	urlString += (urlString === SITE_URL) ? '?' : '&';

	// on commence par demander des infos sur la page appelee.
	$.get(urlString+'getinfos=svp', function (data) {
	    var lang, body_classes, i;

	    data = JSON.parse(data);
	    lang = data.lang ? data.lang: (State.lang ? State.lang : '');
	    body_classes = data.body_classes.split(' ');

	    // si l'url specifie une langue differente de la langue courante,
	    // on recharge les divs localisees.
	    if (lang !== $('html').attr('lang')) {
		// on actualise l'attribut lang..
		$('html').attr('lang', lang);
		$('.loc_div').each(function (i) {
		    var url = History.getState().url;
		    url += (url === SITE_URL) ? '?' : '&';
		    url += 'getbyid=' + this.id;
		    $(this).trigger(onAjaxNavReq);
		    $.get(url, function (data) {
			var id = this.url.replace(/.*getbyid=/, '');
			$('#' + id).empty().html(data).trigger(onAjaxNavLocalisedLoad);
			rewriteLinksToAjax(); // a optimiser, ceci est appele trop souvent.
		    });
		});
	    }
	    // comme spip utilise les class sur le body par defaut, il vaut mieux les mettre
	    // a jour.
	    $('body').removeClass()
	    for (i = 0; i < body_classes.length; i += 1) {
		$('body').addClass(body_classes[i]);
	    }
	});

	// on recharge ensuite les elements 'ajax_nav'.
	$('.ajax_nav').each(function (i) {
	    var url = History.getState().url;
	    url += (url === SITE_URL) ? '?' : '&';
	    url += 'getbyid=' + this.id;
	    $.get(url, function (data) {
		var id	= this.url.replace(/.*getbyid=/, '');
		$('#' + id).empty().html(data).trigger(onAjaxNavLoad);
		rewriteLinksToAjax(); // a optimiser, ceci est appele trop souvent.
	    });
	});
    });

    ///////////////////////
    // rewrite links //////
    ///////////////////////

    function rewriteLinksToAjax() {
	var i;

	// pour les liens pas encore ajaxifies, on les rends inactifs et on attache un
	// pushState au click. Le chargement est alors declanche par l'evenement
	// changestate.
	$('a:not(".ajax_link")').each(function (i) {

	    var urlParams = urlToVariables(this.href);

	    if (urlParams.ajax) {
		$(this).addClass('ajax_link');
		$(this).click(function (event) {
		    // Continue as normal for cmd clicks etc
		    if ( event.which === 2 || event.metaKey ) { return true; }
		    History.pushState(urlParams, "", $(this).attr('href'));
		    event.preventDefault();
		    return false;
		});
	    }
	    return;
	});
	// on attribue la classe ajax_nav aux elements a charger en ajax.
	for (i = 0; i < AjaxNav.options.ajaxDivs.length; i += 1) {
	    $('#' + AjaxNav.options.ajaxDivs[i]).addClass('ajax_nav');
	}
	// on attribue des classes aux divs a recharger en cas de changement de langue
	for (i = 0; i < AjaxNav.options.localizedDivs.length; i += 1) {
	    $('#' + AjaxNav.options.localizedDivs[i]).addClass('loc_div');
	}
    }

    // on charge la page et on reecrit les liens concerne pour qu'il declanchent
    // un chargement ajax et une reecriture de la barre d'url.
    $(document).ready(function () {
	// test for History.js compatibiity
	if (!window.History) {
	    return false;
	}
	rewriteLinksToAjax();
    });
}