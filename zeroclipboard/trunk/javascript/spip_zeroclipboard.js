var clip = null;

;(function($){

$(function(){
	function clipcomplete(client,text){
		$(client.domElement)
			.html(locale.zeroclipboard.link_title_copied)
			.removeClass('copypaste_link')
			.addClass('copypaste_copied')
			.attr('title',locale.zeroclipboard.link_title_copied);
		$(client.domElement).parent().next('.copypaste').focus().select();
		var width = $(client.domElement).width(),
			height = $(client.domElement).height();
		clip.div.innerHTML = clip.getHTML(width,height);
		clip.reposition(client.domElement);
		var style = clip.div.style;
		style.width = '' + width + 'px';
		style.height = '' + height + 'px';
		$('.copypaste_copied').not($(client.domElement)[0]).each(function(){
			$(this)
				.html(locale.zeroclipboard.link_title_copy)
				.toggleClass('copypaste_copied','copypaste_link')
				.attr('title',locale.zeroclipboard.link_title_copy);
		});
	}

	var copypaste_init = function(){
		if(clip)
			clip.destroy();
		clip = new ZeroClipboard.Client();
		clip.setHandCursor(true);
		clip.addEventListener('mouseOver',function(client){});
		clip.addEventListener('complete',clipcomplete);
		$('.copypaste').each(function(){
			if(!$(this).prev().is('.copypaste_container'))
				$(this).before('<div class="copypaste_container" style="position:relative"><a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a></div>');
			$(this).prev('div').find('a').unbind('mouseover').mouseover( function() {
				clip.setText($(this).parent().next('.copypaste').val());
				clip.receiveEvent('mouseover', null);
				if(!clip.div)
					clip.glue(this);
				else{
					clip.reposition(this);
					var width = $(this).width(),
						height = $(this).height();
					clip.div.innerHTML = clip.getHTML(width,height);
					var style = clip.div.style;
					style.width = '' + width + 'px';
					style.height = '' + height + 'px';
				}
			});
		});
		$('.coloration_code .cadre_download').each(function(){
			if(!$(this).is('.copypaste_container'))
				$(this).addClass('copypaste_container').css({'position':"relative"}).append(' - <a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a>');
			$(this).find('a.copypaste_link').unbind('mouseover').mouseover( function() {
				var content_paste = " ";
				var content = $.get($(this).parent().find('a').eq(0).attr('href'),function(data) {
					content_paste = data;
					clip.setText(content_paste);
					clip.receiveEvent('mouseover', null);
				});
				if(!clip.div)
					clip.glue(this);
				else{
					clip.reposition(this);
					var width = $(this).width(),
						height = $(this).height();
					clip.div.innerHTML = clip.getHTML(width,height);
					var style = clip.div.style;
					style.width = '' + width + 'px';
					style.height = '' + height + 'px';
				}
			});
		});
		$('.spip_cadre').each(function(){
			if(!$(this).next().is('.cadre_download') && $(this).attr('data-clipboard-text') != ''){
				$(this).after('<div class="cadre_download copypaste_container" style="position:relative"><a title="'+locale.zeroclipboard.link_title_copy+'" class="copypaste_link">'+locale.zeroclipboard.link_title_copy+'</a></div>');
				var me = $(this);
				$(this).next('.cadre_download').find('a.copypaste_link').unbind('mouseover').mouseover( function() {
					var content_data = me.attr('data-clipboard-text');
					clip.setText(content_data);
					clip.receiveEvent('mouseover', null);
					if(!clip.div)
						clip.glue(this);
					else{
						clip.reposition(this);
						var width = $(this).width(),
							height = $(this).height();
						clip.div.innerHTML = clip.getHTML(width,height);
						var style = clip.div.style;
						style.width = '' + width + 'px';
						style.height = '' + height + 'px';
					}
				});
			}
		});
		var copies = $('.copypaste_container');
		if(copies.size() && !clip.div){
			clip.setText($('.copypaste').eq(0).val());
			clip.glue($('.copypaste_container')[0]);
			clip.receiveEvent('mouseover', null);
		}
	}
	copypaste_init();
	onAjaxLoad(copypaste_init);
});
})(jQuery);