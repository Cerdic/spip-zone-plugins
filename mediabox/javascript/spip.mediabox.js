// Inside the function "this" will be "document" when called by ready()
// and "the ajaxed element" when called because of onAjaxLoad
var mediabox_init = function() {
	var options = {
		transition:box_settings.trans,
		speed:box_settings.speed,
		maxWidth:box_settings.maxW,
		maxHeight:box_settings.maxH,
		minWidth:box_settings.minW,
		minHeight:box_settings.minH,
		slideshowStart:box_settings.str_ssStart,
		slideshowStop:box_settings.str_ssStop,
		current:box_settings.str_cur,
		previous:box_settings.str_prev,
		next:box_settings.str_next,
		close:box_settings.str_close
	};
	
	// passer le portfolio de la dist en mode galerie
	if (box_settings.sel_g){
		$(box_settings.sel_g, this)
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.colorbox(jQuery.extend({}, options, {rel:'galerieauto',slideshow:true,slideshowAuto:false}))
		.addClass("hasbox");
	}

	if (box_settings.tt_img) {
		// selectionner tous les liens vers des images
		$("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']",this).not('.hasbox')
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.colorbox(options) // activer la box
		.addClass("hasbox") // noter qu\'on l\'a vue
		;
	}

	// charger la box sur autre chose
	if (box_settings.sel_c){
		$(box_settings.sel_c).not('.hasbox')
		.colorbox(options)
		.addClass("hasbox") // noter qu\'on l\'a vue
		;
	}
};

;(function ($) {

	/*
	 * overlayClose:	(Boolean:false) Allow click on overlay to close the dialog?
	 * minHeight:		(Number:200) The minimum height for the container
	 * minWidth:		(Number:200) The minimum width for the container
	 * maxHeight:		(Number:null) The maximum height for the container. If not specified, the window height is used.
	 * maxWidth:		(Number:null) The maximum width for the container. If not specified, the window width is used.
	 * autoResize:		(Boolean:false) Resize container on window resize? Use with caution - this may have undesirable side-effects.
	 * onOpen:			(Function:null) The callback function used in place of SimpleModal's open
	 * onShow:			(Function:null) The callback function used after the modal dialog has opened
	 * onClose:			(Function:null) The callback function used in place of SimpleModal's close
	 */
	$.mediabox = function (href, options) {
		var cbox_options = {
			href:href,
			overlayClose: (options && options.overlayClose) || false,
			iframe: (options && options.iframe) || false,
			minHeight: (options && options.minHeight) || '',
			maxHeight: (options && options.maxHeight) || box_settings.maxH,
			minWidth: (options && options.minWidth) || box_settings.minW,
			maxWidth: (options && options.maxWidth) || box_settings.maxW,
			slideshowStart:box_settings.str_ssStart,
			slideshowStop:box_settings.str_ssStop,
			current:box_settings.str_cur,
			previous:box_settings.str_prev,
			next:box_settings.str_next,
			close:box_settings.str_close,
			onOpen: (options && options.onOpen) || null,
			onComplete: (options && options.onShow) || null,
			onClosed: (options && options.onClose) || null
		};
		
		return $.fn.colorbox(cbox_options);
	};
	$.mediaboxClose = function () {$.fn.colorbox.close();};

	// api modalbox
	$.modalbox = $.mediabox;
	$.modalboxload = function (url, options) {
		$.modalbox(url,options);
	};
	$.modalboxclose = $.mediaboxClose;

})(jQuery);