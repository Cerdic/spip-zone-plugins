// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
var fancy_init = function() {
	$("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']", this)
		.addClass("fancybox")
		.attr("onclick","")
		.fancybox();
	$(fb_selecteur_galerie, this).attr("rel","galerie-portfolio");
	$(fb_selecteur_commun, this)
		.fancybox({
			"padding": fb_padding,
			"imageScale": fb_imagescale,
			"overlayShow": fb_overlayshow,
			"overlayOpacity": fb_overlayopacity,
			"hideOnContentClick": fb_hideoncontentclick
		});
	$(fb_selecteur_frame, this)
		.fancybox({
			"frameWidth": fb_framewidth,
			"frameHeight": fb_frameheight,
			"padding": fb_padding,
			"imageScale": fb_imagescale,
			"overlayShow": fb_overlayshow,
			"overlayOpacity": fb_overlayopacity,
			"hideOnContentClick": fb_hideoncontentclick
		});
};
