/*jshint asi: true, bitwise: true, boss:true, curl: true, debug:false, eqeqeq:true,
  eqnull: false, evil: false, forin: true, immed: false, laxbreak: false, newcap: true,
  noarg:true, noempty: true, nonew: true, onevar: true, passfail: false, plusplus: true,
  regexp: true, undef: true, strict: true, white: false, smarttabs: true */
/*global document:true, window:true, jQuery:true, $:true, History:true */

function AjaxNav() {
    "use strict";
    var onAjaxNavReq		= $.Event("onAjaxNavReq"),
    onAjaxNavLoad		= $.Event("onAjaxNavLoad"),
    onAjaxNavLocalisedLoad	= $.Event("onAjaxNavLocalisedLoad");

    function addUrlParam(url, param, value) {
	var re;
	// le sommaire est un cas special...
	if (url.match(/sommaire[&]?/)) {
	    url += '&' + param + '=' + value;
	} else { // le cas general
	    if (url.match(/\?/)) {
		re = new RegExp('[&?]' + param + '=');
		if (url.match(re)) {
		    re = new RegExp('([&?])' + param + '=[^&]*');
		    if (value) {
			url = url.replace(re, '$1' + param + '=' + value);
		    } else {
			re = new RegExp(param + '=' + '[^&]*[&]?');
			url = url.replace(re, '');
			url = url.replace(/\?$/, '');
		    }
		} else {
		    url += '&' + param + '=' + value;
		}
	    } else {
		url += '?' + param + '=' + value;
	    }
	}
	return url;
    }

/*
    // tests
    if (addUrlParam('http://blabla.com/hello?param1=value1', 'param2', 'value2') !==
	'http://blabla.com/hello?param1=value1&param2=value2') {
	alert('1');
    }
    if (addUrlParam('http://blabla.com/hello', 'param2', 'value2') !==
	'http://blabla.com/hello?param2=value2') {
	alert('2');
    }
    if (addUrlParam('http://blabla.com/sommaire', 'param2', 'value2') !==
	'http://blabla.com/sommaire&param2=value2') {
	alert('3');
    }
    if (addUrlParam('http://blabla.com/sommaire&param1=value1', 'param2', 'value2') !==
	'http://blabla.com/sommaire&param1=value1&param2=value2') {
	alert('4');
    }
    if (addUrlParam('http://blabla.com/hello?param1=value1&param2=value2', 'param1', 'value3') !==
	'http://blabla.com/hello?param1=value3&param2=value2') {
	alert('5');
    }
    if (addUrlParam('http://blabla.com/hello?param1=value1&param2=value2', 'param1', '') !==
	'http://blabla.com/hello?param2=value2') {
	alert('6');
    }
    if (addUrlParam('http://blabla.com/hello?param1=value1', 'param1', '') !==
	'http://blabla.com/hello') {
	alert('7');
    }
*/

    ///////////////////////
    // onStateChange //////
    ///////////////////////

    // charge la page en ajax a chaque evenement statechange
    $(window).bind('statechange', function() {
	// si on est pas sur une page ajax, on oublie.
	if (!$('html').data('is_ajax_page')) {
	    window.location.reload();
	}
	// on commence par demander des infos sur la page appelee.
	$.get(addUrlParam(History.getState().url, 'getinfos', 'svp'), function (data) {
	    var i;
	    // on decide si la page doit etre chargee en ajax.
	    if ((!data.page) || ($.inArray(data.page, AjaxNav.options.pagesToAjaxify) === -1)) {
		window.location.reload();
	    } else {
		// si l'url specifie une langue differente de la langue courante,
		// on recharge les divs localisees.
		document.title = data.title;
		if (data.lang !== $('html').attr('lang')) {
		    $('html').attr('lang', data.lang);
		    $('.loc_div').each(function (i) {
			$(this).trigger(onAjaxNavReq);
			$.get(addUrlParam(History.getState().url, 'getbyid', this.id),
			      function (data) {
			    var id = this.url.replace(/.*getbyid=/, '');
			    $('#' + id).empty().html(data).trigger(onAjaxNavLocalisedLoad);
			    prepareForAjax('#' + id);
			});
		    });
		}
		// on recharge ensuite les elements 'ajax_nav'.
		$('.ajax_nav').trigger(onAjaxNavReq);
		$('.ajax_nav').each(function (i) {
		    $.get(addUrlParam(History.getState().url, 'getbyid', this.id),
			  function (data) {
			var id	= this.url.replace(/.*getbyid=/, '');
			$('#' + id).empty().html(data).trigger(onAjaxNavLoad);
			prepareForAjax('#' + id);
		    });
		});
		// comme spip utilise les class sur le body par defaut, il vaut mieux les mettre
		// a jour.
		data.body_classes = data.body_classes.split(' ');
		$('body').removeClass()
		for (i = 0; i < data.body_classes.length; i += 1) {
		    $('body').addClass(data.body_classes[i]);
		}
	    } // fin chargement ajax
	}); // fin getinfos
    }); // fin onStateChange

    ///////////////////////
    // prepare for ajax ///
    ///////////////////////

    function prepareForAjax(selector) {
	var i;
	// on rends tous les liens inactifs et on attache un pushState au click.
	// Le chargement est alors declanche par l'evenement changestate.
	$(selector + ' a:not(".ajax_link")').each(function (i) {
	    $(this).addClass('ajax_link');
	    $(this).click(function (event) {
		// on ne change rien pour les ctr-click etc.
		if ( event.which === 2 || event.metaKey ) { return true; }
		if ($(this).hasClass('thickbox')) { return true; }
		History.pushState(null, null, $(this).attr('href'));
		event.preventDefault();
		return false;
	    });
	    return;
	});
	// on attribue la classe ajax_nav aux elements a charger en ajax.
	for (i = 0; i < AjaxNav.options.ajaxDivs.length; i += 1) {
	    $(selector + ' #' + AjaxNav.options.ajaxDivs[i]).addClass('ajax_nav');
	}
	// on attribue des classes aux divs a recharger en cas de changement de langue
	for (i = 0; i < AjaxNav.options.localizedDivs.length; i += 1) {
	    $(selector + ' #' + AjaxNav.options.localizedDivs[i]).addClass('loc_div');
	}
    }

    $(function () {
	// on teste la compatibilite avec l'API History
	if (!window.History) {
	    return false;
	}
	// On regarde si la page et une page ajax, si oui on met un marqueur sur html
	$.get(addUrlParam(History.getState().url, 'getinfos', 'svp'), function (data) {
	    if ((data.page) && ($.inArray(data.page, AjaxNav.options.pagesToAjaxify) !== -1)) {
		$('html').data('is_ajax_page', true);
		prepareForAjax('html');
	    }
	});
    });
}