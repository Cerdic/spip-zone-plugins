//
// Gestion du longdesc : affichage de la legende
//
var baselongdesc = function(url) {
	return $("<link \/>").attr("href", url)[0].href.replace(/^([^/]+:\/\/[^/]*\/).*/, "$1");
}
var cachelongdesc = {};
var displaylongdesc=function(l) {
	if ((l = l.html()) && (l = l.replace(/^\s*$/, ""))) {
		$("<div id=\'fancy_legend\'>")
		.css({opacity: 0.9})
		.hide()
		.html(l)
		.appendTo("#fancy_content")
		.slideDown("slow")
		// compat crayons : ne pas disparaitre au click + vider le cache
		.click(function(e) { cachelongdesc={}; e.stopPropagation(); });
		$("#fancy_img")
		.hover(
			function(){ $("#fancy_legend").slideUp(); },
			function(){ $("#fancy_legend").slideDown(); }
		);
	}
}
// Gestion du longdesc : chargement de la legende
var showlongdesc=function(c) {
	var l=c.itemArray[c.itemCurrent]
	if (l && l.orig && l.orig[0])
		l = $(l.orig[0]).attr("longdesc");
	else
		l = null;
	if (l) {
		// la legende est dans un div de la page
		if (l.match(/^#/)) {
			displaylongdesc($(l));
		}
		// la legende est dans une autre page
		else {
			// meme domaine ?
			if (baselongdesc(l) == baselongdesc(window.location)) {
				var url = l.replace(/#.*/, "");
				var sel = l.replace(/^[^#]*/, "") || ">*";
				if (cachelongdesc[url])
					displaylongdesc($(sel, cachelongdesc[url]));
				else
					$.get(url, function (e) {
						cachelongdesc[url] = "<xml>" + e + "</xml>";
						displaylongdesc($(sel, cachelongdesc[url]));
					});
			}
			// cross domain ou autre (par ex data:)
			else {
				displaylongdesc(
					$("<a>afficher la l&eacute;gende</a>")
					.onclick()
					.wrap("<div \/>")
					.attr("href", l)
					.parent()
				);
			}
		}
	}
}

// cacher les embed sans wmode transparent
var hideembed=function() {
	if(!navigator.platform.match('Mac')){
		jQuery('embed[wmode!=transparent]:visible').addClass('fancy_cache').css('visibility','hidden')
			.parents('object').addClass('fancy_cache').css('visibility','hidden');
	}
}

// afficher les embed sans wmode transparent
var showembed=function() {
	if(!navigator.platform.match('Mac')){
		jQuery('.fancy_cache').removeClass('.fancy_cache').css('visibility','visible');
	}
}



// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
var fancy_init = function() {
	$("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']", this)
		.addClass("fancybox")
		.attr("onclick","")
		.fancybox();
	$(fb_selecteur_galerie, this).attr("rel","galerie-portfolio");
	$(fb_selecteur_commun, this)
		.fancybox(fb_commun_options);
	$(fb_selecteur_frame, this)
		.fancybox(fb_frame_options);
};
