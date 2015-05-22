var clip = null;

;(function($){

$(function(){
	var copypaste_init = function(){
		$('.copypaste').each(function(){
			if(!$(this).prev().is('.copypaste_container'))
				$(this).before('<div class="copypaste_container" style="position:relative"><a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a></div>');
		});
		$('.coloration_code .cadre_download').each(function(){
			/**
			 * On ajoute la class si pas déjà fait
			 */
			if(!$(this).is('.copypaste_container'))
				$(this)
					.addClass('copypaste_container')
					.css({'position':"relative"})
					.append(' - <a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a>');
		});
		$('.coloration_code').each(function(){
			if(!$(this).is('.code') && !$(this).find('.cadre_download')[0] && !$(this).next().is('.cadre_download') && $(this).attr('data-clipboard-text') != '')
				$(this).append('<p class="download cadre_download copypaste_container" style="position:relative"><a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a></p>');
		});
		
		$('.cadre_download a.copypaste_link').each(function() {
			var code = $(this).parent('.cadre_download').prev('.cadre'),me = this;
			var content_data = code.attr('data-clipboard-text'),
				width = $(this).width(),height = $(this).height();
			if(!content_data && $(this).parent().find('a').eq(0)){
				$.get($(this).parent().find('a').eq(0).attr('href'),function(data) {
					$(me).parent().append('<div style="display:none" class="data-clipboard-hidden">'+data+'</div>');
				});
			}else{
				$(me).attr('data-clipboard-text',content_data);
			}
		});
		ZeroClipboard.destroy();
		var clip = new ZeroClipboard( $('.copypaste_link') );

		clip.on('ready', function(event) {
			$('.copypaste_copied').html(locale.zeroclipboard.link_title_copy)
				.attr('title',locale.zeroclipboard.link_title_copy)
				.removeClass('copypaste_copied');
			clip.on('copy', function(event) {
				if(typeof($(event.target).attr('data-clipboard-text')) == "undefined"){
					event.clipboardData.setData('text/plain', $(event.target).parent().find('.data-clipboard-hidden').html());
				}
			});
			clip.on('aftercopy', function(event) {
				if(event.data['text/plain'] != undefined){
					$(event.target)
						.html(locale.zeroclipboard.link_title_copied).attr('title',locale.zeroclipboard.link_title_copied).addClass('copypaste_copied');
					$('.copypaste_copied').not($(event.target)).each(function(i){
						$(this)
							.html(locale.zeroclipboard.link_title_copy)
							.attr('title',locale.zeroclipboard.link_title_copy)
							.removeClass('copypaste_copied');
					});
				}
			});
		});

		clip.on( 'error', function(event) {
			ZeroClipboard.destroy();
		});
	}
	copypaste_init();
});
})(jQuery);