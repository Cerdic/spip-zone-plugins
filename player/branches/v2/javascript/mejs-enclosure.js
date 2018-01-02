var mejsloader;
jQuery(function(){
	function buildPlayer(src){
		if (!jQuery("#mejs-hiddenplayer").length)
		jQuery('<div style="display:none"><audio class="mejs" id="mejs-hiddenplayer" type="audio/mpeg" src="'+src+'" data-mejsoptions=\'{"alwaysShowControls": false,"loop":false}\' controls="controls"></audio></div>')
			.appendTo(jQuery('body'));
	}
	function getPlayer(){
		return jQuery("#mejs-hiddenplayer").get(0).player;
	}
	function findLinks(){
		return jQuery("a[rel*='enclosure'][href$=mp3]");
	}
	function pauseLink(link){
		link.removeClass("playing").find("i.icon-pause").removeClass("icon-pause").addClass("icon-play");
		getPlayer().pause();
	}
	function toggleSound(link){
		console.log(link);
		if (link.is('.playing')){
			pauseLink(link);
		}
		else {
			pauseLink(findLinks().filter(".playing"));
			var p = getPlayer();
			p.pause();
			p.setSrc(link.attr("href"));
      p.load();
      setTimeout(function(){p.play();},10);
			link.addClass("playing").find("i.icon-play").removeClass("icon-play").addClass("icon-pause");
		}
	}
	var mp3_links = findLinks();
	if (mp3_links.length){
		buildPlayer(mp3_links.eq(0).attr("href"));
		mp3_links.not('.link-player').each(function(){
			var me = $(this).addClass('link-player');
			jQuery('<i class="icon-play"></i>').prependTo(me);
			me.bind("click",function(e){
				e.preventDefault();
				toggleSound(jQuery(this));
				return false;}
			)
		});
		mejsloader.init();
	}
});