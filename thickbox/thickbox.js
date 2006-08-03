/*
 * Thickbox 1.2 - One box to rule them all.
 * By Cody Lindley (http://www.codylindley.com)
 * Under an Attribution, Share Alike License
 * Thickbox is built on top of the very light weight jquery library.
 *
 * Modified for SPIP <www.spip.net> by Fil <fil@rezo.net>
 *
 */

function TB_Image() {
	var t = this.title || this.name || '<small>'+this.href+'</small>';
	TB_on();
	TB_show(t,this.href,'image');
	return false;
}

function TB_init(){
	// add the thickbox to all links of class=thickbox
	$("a.thickbox").click(TB_Image);

	// and to all links of type image/jpeg (or gif|png)
	// here we also deal with the prev/next thing
	$("a[@type]")
	.each(
		function(i) {
			if (
				this.type.match(/^image[\/](jpeg|gif|png)$/i)
			) {
				this.onclick = TB_Image;

				// on stocke le lien dans un tableau
				imageArray.push ([
					this.href,
					this.title || this.name
				]);

			}
		}
	);

	// and to all links to images (maybe too slow?)
/*
	$("a[@href]").each(
		function(i) {
			if (
				this.href.match(/^[^?]+\.(jpeg|jpg|gif|png)$/i)
			) {
				this.onclick = TB_Image;
			}
		}
	);
*/
}

function TB_on() {

		$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
		$("#TB_overlay").click(TB_next);
		$(window).scroll(TB_position);
		TB_overlaySize();
		
		$("body").append("<div id='TB_load' style='display:none;'><img src='"+TB_chemin_animation+"' /></div>");
		TB_load_position();
}

function TB_show(caption, url, type) {//function called when the user clicks on a thickbox link
	try {

		var urlString = /\.jpg|\.jpeg|\.png|\.gif|\.html|\.htm|\.php|\.cfm|\.asp|\.aspx|\.jsp|\.jst|\.rb|\.txt/g;
		var urlType = url.toLowerCase().match(urlString);
		
		if (type=='image' || urlType == '.jpg' || urlType == '.jpeg' || urlType == '.png' || urlType == '.gif'){//code to show images

			imgPreloader = new Image();
			imgPreloader.onload = function(){
				
			imgPreloader.onload = null;
				
			// Resizing large images added by Christian Montoya
			var pagesize = getPageSize();
			var x = pagesize[0] - 150;
			var y = pagesize[1] - 150;
			var imageWidth = imgPreloader.width;
			var imageHeight = imgPreloader.height;
			if (imageWidth > x) {
				imageHeight = imageHeight * (x / imageWidth); 
				imageWidth = x; 
				if (imageHeight > y) { 
					imageWidth = imageWidth * (y / imageHeight); 
					imageHeight = y; 
				}
			} else if (imageHeight > y) { 
				imageWidth = imageWidth * (y / imageHeight); 
				imageHeight = y; 
				if (imageWidth > x) { 
					imageHeight = imageHeight * (x / imageWidth); 
					imageWidth = x;
				}
			}
			// End Resizing
			
			TB_WIDTH = imageWidth + 30;
			TB_HEIGHT = imageHeight + 60;
			$("#TB_window").append("<a href='' id='TB_ImageOff'><img id='TB_Image' src='"+url+"' width='"+imageWidth+"' height='"+imageHeight+"' alt='"+caption+"'/></a>"
								 + "<div id='TB_caption'>"+caption+"</div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton'>x</a></div>"); 
			$("#TB_closeWindowButton").click(TB_remove);		
			TB_position();
			$("#TB_load").remove();
			$("#TB_ImageOff").click(TB_next);
			$("#TB_window").slideDown();
			}
	  
			imgPreloader.src = url;
		}
		
		if(urlType=='.htm'||urlType=='.html'||urlType=='.php'||urlType=='.asp'||urlType=='.aspx'||urlType=='.jsp'||urlType=='.jst'||urlType=='.rb'||urlType=='.txt'||urlType=='.cfm' || (url.indexOf('TB_inline') != -1)){//code to show html pages
			
			var queryString = url.replace(/^[^\?]+\??/,'');
			var params = parseQuery( queryString );
			
			TB_WIDTH = (params['width']*1) + 30;
			TB_HEIGHT = (params['height']*1) + 40;
			ajaxContentW = TB_WIDTH - 30;
			ajaxContentH = TB_HEIGHT - 45;
			$("#TB_window").append("<div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton'>close</a></div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>");
			$("#TB_closeWindowButton").click(TB_remove);
			
				if(url.indexOf('TB_inline') != -1){
					$("#TB_ajaxContent").html($('#' + params['inlineId']).html());
					TB_position();
					$("#TB_load").remove();
					$("#TB_window").slideDown();
				}else{
					$("#TB_ajaxContent").load(url, function(){
						TB_position();
						$("#TB_load").remove();
						$("#TB_window").slideDown();
					});
				}
			
		}
		
		$(window).resize(TB_position);
		
	} catch(e) {
		alert( e );
	}
}

//helper functions below

function TB_remove() {
	$("#TB_window").fadeOut("fast",function(){$('#TB_window,#TB_overlay').remove();});
	$("#TB_load").remove();
	return false;
}

function TB_next() {
	var current = $("#TB_Image").get(0).src;
	for (var i=0; i<imageArray.length; i++) {
		if (imageArray[i][0] == current) {
			var next = i+1;
		}
	}

	if (next<imageArray.length) {
		$("#TB_window").html('');
		TB_show(imageArray[next][1] || imageArray[next][0],
			imageArray[next][0], 'image');
	}
	else {
		TB_remove();
	}

	return false;
}

function TB_position() {
	var pagesize = getPageSize();	
	var arrayPageScroll = getPageScrollTop();
	
	$("#TB_window").css({width:TB_WIDTH+"px",height:TB_HEIGHT+"px",
	left: ((pagesize[0] - TB_WIDTH)/2)+"px", top: (arrayPageScroll[1] + ((pagesize[1]-TB_HEIGHT)/2))+"px" });
	TB_overlaySize();

}

function TB_overlaySize(){
	if (window.innerHeight && window.scrollMaxY) {	
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		yScroll = document.body.offsetHeight;
  	}
	$("#TB_overlay").css("height",yScroll +"px");
}

function TB_load_position() {
	var pagesize = getPageSize();
	var arrayPageScroll = getPageScrollTop();

	$("#TB_load")
	.css({left: ((pagesize[0] - 100)/2)+"px", top: (arrayPageScroll[1] + ((pagesize[1]-100)/2))+"px" })
	.css({display:"block"});
}

function parseQuery ( query ) {
	var Params = new Object ();
	if ( ! query ) return Params; // return empty object
	var Pairs = query.split(/[;&]/);
	for ( var i = 0; i < Pairs.length; i++ ) {
		var KeyVal = Pairs[i].split('=');
		if ( ! KeyVal || KeyVal.length != 2 ) continue;
		var key = unescape( KeyVal[0] );
		var val = unescape( KeyVal[1] );
		val = val.replace(/\+/g, ' ');
		Params[key] = val;
	}
	return Params;
}


function getPageScrollTop(){
	var yScrolltop;
	if (self.pageYOffset) {
		yScrolltop = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScrolltop = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScrolltop = document.body.scrollTop;
	}
	arrayPageScroll = new Array('',yScrolltop) 
	return arrayPageScroll;
}

function getPageSize(){
	var de = document.documentElement;
	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
	
	arrayPageSize = new Array(w,h) 
	return arrayPageSize;
}


/* init */
// $(document).load(TB_init) si on veut que ca marche dans l'espace prive
// (a cause des document_write() qu'il y a en pagaille...)
// $(document).ready(TB_init);
var imageArray = [];
$(document).load(TB_init);
