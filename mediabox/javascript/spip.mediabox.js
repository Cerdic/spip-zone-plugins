// Inside the function "this" will be "document" when called by ready()
// and "the ajaxed element" when called because of onAjaxLoad
var mediabox_init = function() {
	var options = {
		transition:box_settings.transition,
		speed:box_settings.speed,
		maxWidth:box_settings.maxWidth,
		maxHeight:box_settings.maxHeight,
		minWidth:box_settings.minWidth,
		minHeight:box_settings.minHeight,
		slideshowStart:box_settings.str_slideshowStart,
		slideshowStop:box_settings.str_slideshowStop,
		current:box_settings.str_current,
		previous:box_settings.str_previous,
		next:box_settings.str_next,
		close:box_settings.str_close
	};
	
	// passer le portfolio en mode galerie la box
	if (box_settings.selecteur_galerie){
		$(box_settings.selecteur_galerie, this)
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.colorbox(jQuery.extend({}, options, {rel:'galerieauto',slideshow:true,slideshowAuto:false}))
		.addClass("hasbox");
	}

	if (box_settings.traiter_toutes_images) {
		// selectionner tous les liens vers des images
		$("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']",this).not('.hasbox')
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.colorbox(options) // activer la box
		.addClass("hasbox") // noter qu\'on l\'a vue
		;
	}

	// charger la box sur autre chose
	if (box_settings.selecteur_commun){
		$(box_settings.selecteur_commun).not('.hasbox')
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
	$.modalbox = function (href, options) {
		var cbox_options = {
			href:href,
			overlayClose: (options && options.overlayClose) || false,
			iframe: (options && options.iframe) || false,
			minHeight: (options && options.minHeight) || '',
			maxHeight: (options && options.maxHeight) || box_settings.maxHeight,
			minWidth: (options && options.minWidth) || box_settings.minWidth,
			maxWidth: (options && options.maxWidth) || box_settings.maxWidth,
			slideshowStart:box_settings.str_slideshowStart,
			slideshowStop:box_settings.str_slideshowStop,
			current:box_settings.str_current,
			previous:box_settings.str_previous,
			next:box_settings.str_next,
			close:box_settings.str_close,
			onOpen: (options && options.onOpen) || null,
			onComplete: (options && options.onShow) || null,
			onClosed: (options && options.onClose) || null
		};
		
		return $.fn.colorbox(cbox_options);
	};

	$.modalboxload = function (url, options) {
		$.modalbox(url,options);
	};

	$.modalboxclose = function () {
		$.fn.colorbox.close();
	};

})(jQuery);