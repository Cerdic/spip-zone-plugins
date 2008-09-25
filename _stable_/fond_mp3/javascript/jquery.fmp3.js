/***
 * 
 * javascript/jquery.fmp3.js
 * 
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 * 
 * Adaptation de jquery.jmp3.js pour fond_mp3 (fmp3), plug-in pour SPIP
 * 
 * Original note:
 * 
*  jMP3 v0.2.1 - 10.10.2006 (w/Eolas fix & jQuery object replacement)
* an MP3 Player jQuery Plugin (http://www.sean-o.com/jquery/jmp3)
* by Sean O
*
 * [...] Please refer to http://www.sean-o.com/jquery/jmp3
*
* Copyright (c) 2006 Sean O (http://www.sean-o.com)
* Licensed under the MIT License:
* http://www.opensource.org/licenses/mit-license.php
*
***/
jQuery.fn.jmp3 = function(passedOptions){

	// passable options
	var options = {
		'playerPath': ""			// swf player path
		, 'mp3path': ""			// path to MP3 file 
		, 'backColor': ""			// background color button
		, 'frontColor': ""			// foreground color button
		, 'width': "25"				// width of player
		, 'height': "20"			// height of player
		, 'repeatPlay': "false"			// repeat mp3?
		, 'songVolume': "50"			// mp3 volume (0-100)
		, 'autoplay': "false"		// play immediately on page load?
		, 'showDownload': "true"	// show download button in player
		, 'showfilename': "true"	// show .mp3 filename after player
	};
	
	// use passed options, if they exist
	if (passedOptions) {
		jQuery.extend(options, passedOptions);
	}
	
	// iterate through each object
	return this.each(function(){
		// filename needs to be enclosed in tag (e.g. <span class='mp3'>mysong.mp3</span>)
		var filename = options.filename + jQuery(this).html();
		// build the player HTML
		var mp3html = ""
			+ "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'"
				+ " id='fmp3-object'"
				+ " style='width:" + options.width + "px;height:" + options.height + "px'"
				+ " codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab'>"
			+ "<param name='movie' value='" + options.playerPath + "'"
				+ "?"
				+ "&file=" + options.mp3path 
				+ "&autoStart=" + options.autoplay
				+ "&backColor=" + options.backColor 
				+ "&frontColor=" + options.frontColor
				+ "&repeatPlay=" + options.repeatPlay 
				+ "&showDownload=" + options.showDownload 
				+ "&songVolume=" + options.songVolume + "' />"
			+ "<param name='wmode' value='transparent' />"
			+ "<embed wmode='transparent'"
				+ " width='" + options.width + "'"
				+ " height='" + options.height + "'"
				+ " src='" 
				+ options.playerPath 
				+ "?"
				+ "showDownload=" + options.showDownload 
				+ "&file=" + options.mp3path 
				+ "&autoStart=" + options.autoplay
				+ "&backColor=" + options.backColor 
				+ "&frontColor=" + options.frontColor
				+ "&repeatPlay=" + options.repeatPlay 
				+ "&songVolume=" + options.songVolume 
				+ "'"
				+ " type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />"
			+ "</object>"
			;
		// don't display filename if option is set
		if (options.showfilename == "false") { jQuery(this).html(""); }
		jQuery(this).prepend(mp3html+"&nbsp;");
		
		// Eolas workaround for IE (Thanks Kurt!)
		if(jQuery.browser.msie){ this.outerHTML = this.outerHTML; }
	});
};