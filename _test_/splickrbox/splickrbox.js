/* Splickerbox - Code javascript
 *
 * Badge à la flickr, par BoOz booz AT rezo.net
 *
 * Fonctionne avec jQuery.
 **/

function getObjectMethodClosure(object, method) {
	return function(arg) {
		return object[method](arg); 
	}
}	



$(document).ready(function(){
		$(".splickrbox").splicker();
	});



jQuery.fn.splicker = function() {
	return this.each(function() {
			var img_cnt = $(this).find('img').size();
			if(img_cnt > 0) {
				var size = $(this).find('img').css('width').replace('px',"");
				var box = new jQuery.SplickerBox(this,img_cnt,$(this).find('.changeMe'),size);
			}
		});
}

jQuery.SplickerBox = function(e,m,changeMe,s) {
	this.elt = e;
	this.max = m;
	this.c = changeMe;
	this.cptj = 0;
	this.left = this.top=0;
	if(s == 0 || s == 'auto')
		this.cote = 70;
	else
		this.cote = s;
	this.init()
}


jQuery.SplickerBox.prototype = {
	itere: function() {
		this.cptj = Math.round(Math.random()*this.max) % this.max;
		$("#statusMsg").html("it"+this.cptj+"=?"+this.max);
	},
	init: function() {
		$(this.elt).find('img').css({width:(this.cote/2) + "px",height:(this.cote/2) + "px", height: (this.cote/2) + "px",border:0});
		$(this.c).css({width: this.cote + "px",height: this.cote + "px"});
		this.start();
	},
	
	start: function() {
		setTimeout(getObjectMethodClosure(this,'doyourstuff'),(Math.random()*2)*1000);
	},

	postpone: function() {
		this.itere();		
		$(this.c).empty();
		this.start();
	},

	doyourstuff: function() {

		var or = $(this.elt).find('img').get(this.cptj);
		var image = or.cloneNode(true);
		image.style.width="100%";
		image.style.height="100%";

		var href = or.parentNode.href;

		$(image).css("cursor","pointer").click(function(){
				//thickbox
				if(typeof imageArray != 'undefined'){
					TB_on();
					TB_show('',href,'image');
				}else{
					window.document.location = href ;
				}
			});
		
		$(this.c).css('width',this.cote);
		$(this.c).css('height',this.cote);
		$(this.c).append(image);

		if(this.cptj%3 == 0){
			$(this.c).css("left","0px");
			this.left="0";
		}
		if(this.cptj%3 == 1){
			$(this.c).css("left", (this.cote/2) + "px");
			this.left= this.cote/2 ;
		}
		if(this.cptj%3 == 2){
			$(this.c).css("left", (this.cote/2) + "px");
			this.left= this.cote ;
		}
		
		if(this.cptj>=0 && this.cptj<=2){
			$(this.c).css("top","0px");
			this.top="0";
		}
		if(this.cptj>=3 && this.cptj<=5){
			$(this.c).css("top","0px");
			this.top=this.cote/2;
		}
		if(this.cptj>=6 && this.cptj<=8){
			$(this.c).css("top",this.cote + "px");
			this.top=this.cote;
		}
		if(this.cptj >=9 && this.cptj<=11){
			$(this.c).css("top", this.cote + "px");
			this.top= 3*(this.cote/2) ;
		}
		
		$(this.c).fadeIn(2000);		


		setTimeout(getObjectMethodClosure(this,'resize'),4000);

		setTimeout(getObjectMethodClosure(this,'postpone'),7000);

	},
	resize: function() {
		var t = new Number(this.top);
		var l = new Number(this.left);
		jQuery(this.c).animate({top:t,left:l,width:0,height:0},1500);
	}
}