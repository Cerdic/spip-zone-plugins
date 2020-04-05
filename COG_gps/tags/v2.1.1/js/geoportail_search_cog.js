

/** Fonction de recherche de commune dans le RGC
*	
*	Requete du type :
*	- q=nom commune&zone=FXX
*	ou 
*	- lon=X&lat=Y&zone=FXX
*	
*	Renvoie un objet :
*	success ({name:"nom", nadm:"num dep", adm:"nom dep", fcode:"code", carte:"top25", lon:x, lat:y })
*	ou
*	success ({ name:"paris", nadm:"num dep", adm:"nom dep", fcode:"code", carte:"top25", d:distance })
*/
(function($) { // Pour jQuery.noConflict()

	$.jqGeoSearchCog = function(q, options)
	{	// Options par defaut
		$.jqGeoSearchCog.param = $.extend({}, $.jqGeoSearchCog.defaults, options);

		// Info sur la fenetre
		var de = document.documentElement;
		var wt = $("body").width() + parseInt( $("body").css("margin-right")) + parseInt( $("body").css("margin-left")) +15;
		var ht = $("body").height() + parseInt( $("body").css("margin-top")) + parseInt( $("body").css("margin-bottom")) +15;
		var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
		var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight
		var scrollX = (typeof( window.pageXOffset ) == 'number') ? window.pageXOffset : (document.body && document.body.scrollLef) ? document.body.scrollLef : (document.documentElement) ? document.documentElement.scrollLeft : 0;
		var scrollY = (typeof( window.pageYOffset ) == 'number') ? window.pageYOffset : (document.body && document.body.scrollTop) ? document.body.scrollTop : (document.documentElement) ? document.documentElement.scrollTop : 0;
		// Verifier qu'on a ce qu'il faut, sinon le creer :
		if (jQuery('#query_back').length == 0)
		{	var back = $("<div class=query_back id=query_back "
				+"style='position:absolute; background-color:black; z-index:2000; display:none;' >"
				+"</div>").width(wt).height(ht).css("left",0).css("top",0).css("opacity",0.4).appendTo("body");
		}
		if (jQuery('#query').length == 0)
		{	var query = $("<div class=query id=query "
				+"style='position:absolute; z-index:2001; display:none;' >"
				+"<div class='jqCloseButton' onclick='javascript:$.jqGeoSearchCog.cancel()'></div>"
				+"<p>"+$.jqGeoSearchCog.param.title
				+($.jqGeoSearchCog.param.info!=''?"<br/><small>"+$.jqGeoSearchCog.param.info+"</small>":"")
				+"</p><ul></ul>"
				+"</div>").appendTo("body");
		}
		// Centrer la fenetre pour le choix
		jQuery('#query').css('left',scrollX+(w-jQuery('#query').width())/2);
		jQuery('#query').css('top',scrollY+(h-jQuery('#query').height())/2);
		
		// Envoyer la requete Ajax
		jQuery('#query_back').show();
		jQuery.ajax(
			{	type	: 'GET', 
				url		: $.jqGeoSearchCog.param['path']+'spip.php', 
				data	: "action=geoportail_search_cog&"+q, 
				success	: $.jqGeoSearchCog.select,
				error	: $.jqGeoSearchCog.error
			}
		);	
	}
	
	// Traiter la selection
	$.jqGeoSearchCog.select = function (msg, success, nb)
	{   var t = Array();
		// Recuperer le tableau
		if (isFinite(nb)) t.push($.jqGeoSearchCog.param.obj[nb]);
		else if (typeof(msg)=="object") t=msg;
		else eval ("t = " + msg);
		
		// Rien trouver
		if (t.length == 0) 
		{	$.jqGeoSearchCog.param.success(null);
			$.jqGeoSearchCog.cancel();
			return; 
		}
		// Une seule solution
		else if (t.length == 1) 
		{	$.jqGeoSearchCog.param.success(t[0]);
			$.jqGeoSearchCog.cancel();
			return;
		}
		// Demande a l'utilisateur de choisir
		else
		{	var html='';
			$.jqGeoSearchCog.param.obj = t;
			for (i=0; i<t.length; i++) if (t[i])
			{	html += '<li><a href="javascript:$.jqGeoSearchCog.select(null,true,'+i+')">'
				+t[i]["name"]+' ('+t[i]["nadm"]+')</a></li>';
			}
			jQuery('#query ul').html(html);
			jQuery('#query').show();
			return;
		}
	}
	
	// Zut rate !
	$.jqGeoSearchCog.error = function (hrequest, msg, obj)
	{	$.jqGeoSearchCog.param.error(hrequest, msg);
		$.jqGeoSearchCog.cancel();
	}
	
	// Fermer les fenetres
	$.jqGeoSearchCog.cancel = function ()
	{	if ($.jqGeoSearchCog.param.submit) return;
		jQuery('#query_back').hide();
		jQuery('#query').hide();
	}
	
	// Options
	$.jqGeoSearchCog.defaults = {
 		title	: 'S&eacute;lectionner une destination...',
 		info	: '',
 		submit	: false,
 		path	: '',
		success	: function (resp)		// Ca marche !
		{	alert (resp['name']+" ("+resp['nadm']+")");
		},
		error	: function (resp, e)		// Ooops
		{	alert("[searchError] "+e);
		}
	};
	
	// Parametres en cours
	$.jqGeoSearchCog.param = $.jqGeoSearchCog.defaults;
	

})(jQuery);
