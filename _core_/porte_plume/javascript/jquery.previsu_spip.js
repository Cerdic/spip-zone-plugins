;(function($) {
	$.fn.previsu_spip = function(settings) {
		var options;

		options = {
			previewParserPath:	'' ,
			previewParserVar:	'data',
			textEditer:	'Editer',
			textVoir:	'Voir'
		};
		$.extend(options, settings);

		return this.each(function() {
			var $$, textarea, tabs, preview;
			$$ = $(this);
			textarea = this;

			// init and build previsu buttons
			function init() {
				tabs = $('<div class="markItUpTabs"></div>').prependTo($$.parent());
				$(tabs).append(
					'<a href="#previsuVoir" class="previsuVoir">' + options.textVoir + '</a>' +
					'<a href="#previsuEditer" class="previsuEditer on">' + options.textEditer + '</a>'
				);
				
				preview = $('<div class="markItUpPreview"></div>').insertAfter(tabs);
				preview.hide();
				
				$('.previsuVoir').click(function(){
					mark = $(this).parent().parent();
					$(mark).find('.markItUpPreview').height(
						  $(mark).find('.markItUpHeader').height()
						+ $(mark).find('.markItUpEditor').height()
						+ $(mark).find('.markItUpFooter').height()
					);
					$(mark).find('.markItUpHeader').hide();
					$(mark).find('.markItUpEditor').hide();
					$(mark).find('.markItUpFooter').hide();
					$(this).addClass('on').next().removeClass('on');
					$(mark).find('.markItUpPreview').show()
						.addClass('ajaxLoad')
						.html(renderPreview())
						.removeClass('ajaxLoad');
					return false;
				});
				$('.previsuEditer').click(function(){
					mark = $(this).parent().parent();
					$(mark).find('.markItUpPreview').hide();
					$(mark).find('.markItUpHeader').show();
					$(mark).find('.markItUpEditor').show();
					$(mark).find('.markItUpFooter').show();
					$(this).addClass('on').prev().removeClass('on');
					return false;
				});
			}


			function renderPreview() {	
				var phtml;			
				if (options.previewParserPath !== '') {
					$.ajax( {
						type: 'POST',
						async: false,
						url: options.previewParserPath,
						data: options.previewParserVar+'='+encodeURIComponent($$.val()),
						success: function(data) {
							phtml = data; 
						}
					} );
				}
				return phtml;
			}
	
			init();
		});
	};
})(jQuery);
