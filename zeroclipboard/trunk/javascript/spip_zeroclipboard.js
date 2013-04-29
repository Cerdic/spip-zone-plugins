var clip = null;

;(function($){

$(function(){
	function clipcomplete(client,text){
		$(client.domElement)
			.html(locale.zeroclipboard.link_title_copied)
			.removeClass('copypaste_link')
			.addClass('copypaste_copied')
			.attr('title',locale.zeroclipboard.link_title_copied);
		$('.copypaste_copied').not($(client.domElement)).each(function(){
			$(this)
				.html(locale.zeroclipboard.link_title_copy)
				.toggleClass('copypaste_copied','copypaste_link')
				.attr('title',locale.zeroclipboard.link_title_copy);
		});
		var width = $(client.domElement).width(),
			height = $(client.domElement).height();
		clip.setSize(width,height);
		clip.setCurrent(client.domElement);
		$(client.domElement).parent().next('.copypaste').focus().select();
	}

	var copypaste_init = function(){
		clip = new ZeroClipboard();
		if(ZeroClipboard.detectFlashSupport()){
			clip.on('mouseover',function(client){});
			clip.on('mousedown',function(client){});
			clip.on('complete',clipcomplete);
			$('.copypaste').each(function(){
				if(!$(this).prev().is('.copypaste_container'))
					$(this).before('<div class="copypaste_container" style="position:relative"><a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a></div>');
				$(this).prev('div').find('a').unbind('mouseover').mouseover( function() {
					if(!clip.htmlBridge)
						clip.glue(this);
					else{
						clip.setCurrent(this);
						var width = $(this).width(),
							height = $(this).height();
						clip.setSize(width,height);
						clip.domElement = $(this);
					}
					clip.setText($(this).parent().next('.copypaste').val());
					clip.receiveEvent('mouseover', null);
				});
			});
			/**
			 * Ajouter le lien dans le <p class="cadre_download"...
			 */
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
				if(!$(this).find('.cadre_download')[0] && !$(this).next().is('.cadre_download') && $(this).attr('data-clipboard-text') != '')
					$(this).append('<p class="download cadre_download copypaste_container" style="position:relative"><a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a></p>');
			});
			
			$('.cadre_download a.copypaste_link').unbind('mouseover').mouseover(function() {
				var code = $(this).parent('.cadre_download').prev('.cadre'),me = this;
				var content_data = code.attr('data-clipboard-text'),
					width = $(this).width(),height = $(this).height();
				clip.domElement = me;
				if(!content_data){
					$.get($(this).parent().find('a').eq(0).attr('href'),function(data) {
						content_data = data;
						clip.setText(content_data);
						clip.setCurrent(me);
						clip.setSize(width,height);
						clip.receiveEvent('mouseover', null);
						$(me).parent('.cadre_download').prev('.cadre').attr('data-clipboard-text',content_data);
					});
				}else{
					clip.setText(content_data);
					clip.setCurrent(me);
					clip.setSize(width,height);
					clip.receiveEvent('mouseover', null);
				}
			});
			var copies = $('.copypaste_container');
			if(copies.size() && !clip.htmlBridge){
				clip.setText($('.copypaste').eq(0).val());
				clip.glue($('.copypaste_container')[0]);
				clip.receiveEvent('mouseover', null);
			}
		}
	}
	copypaste_init();
	onAjaxLoad(copypaste_init);
});
})(jQuery);