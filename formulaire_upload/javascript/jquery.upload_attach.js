// JavaScript Document

if(window.jQuery)
(function($) {
	$.fn.upload_attach = function(options) {
		return this.each(function() {
			var self = this;
			var o = $.extend({},options);
			$('form',self)
			// changer l'action : pas indispensable mais permet d'eviter la lenteur du
			// recalcul total de la page, qui provoque en plus un rechargement
			// des javascripts etc. dans l'iframe.
			.attr('action', o.url)
		
			.ajaxForm({
				'beforeSubmit': function(formData, jqForm, options){
					$(self)
					.addClass('ajaxloading')
					.css({'opacity': 0.5});
					return true;
				},
				'success': function(e){
					var h;
					if (h = $('<iframe><\/iframe>').html(e).find('.formulaire_upload').html()) {
						$(self)
						.html(h)
						.removeClass('ajaxloading')
						.css({'opacity':1.0}).
						upload_attach(options);
						if(o.callback) o.callback.apply(self);
					}
					else
						alert('bug !');
				}
			});
		
			// si multifile est la, on l'utilise (trop bien)
			if ($.fn.MultiFile)
			$('form input[@type=file]',self).MultiFile({
				max: 5,
				STRING: {'remove': 'x', 'selected': '$file'}
			});
		
		});	
	};
})(jQuery);
