/* Splickerbox - Code javascript
 *
 * Badge à la flickr, par BoOz booz AT rezo.net
 *
 * Fonctionne avec jQuery.
 **/

//fonction gadget pour avoir le this du contexte au bon objet 
//quand on fait des appels depuis des callbacks (setTimeout et autres events)
function getObjectMethodClosure(object, method) {
	return function(arg) {
		return object[method](arg); 
	}
}	

//Quand le document est pret, on lance le plugin sur chaque
//div de class splickrbox
$(document).ready(function(){
		$(".splickrbox").splicker();
	});

//le plugin splicker, pour chaque image du div
//on lui cree un objet SplickerBox
jQuery.fn.splicker = function() {
	return this.each(function() {
			var img_cnt = $(this).find('img').size();
			if(img_cnt > 0) {
				var size = $(this).find('img').css('width').replace('px',"");
				var box = new jQuery.SplickerBox(this,img_cnt,size);
			}
		});
}

//Constructeur de l'objet
jQuery.SplickerBox = function(e,m,s) {
	this.elt = e;
	this.max = m;
	$(this.elt).append('<div class="changeMe" style="position:absolute;left:0px;top:0px;"></div>');
	this.c = $(this.elt).find('.changeMe');
	this.rows = $('table',this.elt).get(0).rows.length-1;
	this.cols = this.max/this.rows;
	this.cptj = 0;
	this.last = 0;
	this.left = this.top=0;
	if(s == 0 || s == 'auto')
		this.cote = 100;
	else
		this.cote = s;
	this.init();
};

//les methodes
jQuery.SplickerBox.prototype = {
	//on choisit la prochaine image
	itere: function() {
		this.cptj = Math.round(Math.random()*this.max) % this.max;
		if(this.cptj == this.last) this.cptj = (this.last+this.cols)%this.max;
		this.last = this.cptj;
		$("#statusMsg").html("it"+this.cptj+"=?"+this.max);
	},
	//fixer la taille des images à la moitiée de la taille réelle
	//l'image doit être carree
	init: function() {
		$(this.elt).find('img').css({display: 'block', width:(this.cote/2) + "px",height:(this.cote/2) + "px", height: (this.cote/2) + "px",border:0});
		$(this.c).css({width: this.cote + "px",height: this.cote + "px"});
		this.start();
	},
	
	//on lance la premiere vignette apres un temps random entre 0 et 2s
	start: function() {
		setTimeout(getObjectMethodClosure(this,'doyourstuff'),(Math.random()*2)*1000);
	},

	//iteration apres le timeout
	postpone: function() {
		$(this.c).css('background-color','transparent');
		this.itere();		
		$(this.c).empty();
		this.start();
	},

	//toute les bidouilles sur la taille etc
	//pour l'animation
	doyourstuff: function() {
		var or = $(this.elt).find('img').get(this.cptj);
		var image = or.cloneNode(true);
		image.style.width="100%";
		image.style.height="100%";

		var href = or.parentNode.href;

		$(image).css("cursor","pointer").click(function(){
				//thickbox
				if(typeof imageArray != 'undefined' && href.match(/\.(jpeg|jpg|png|gif)$/i)){
					TB_show('',href,'image');
				}else{
					if ($.modalbox && href.match(/\.(jpeg|jpg|png|gif)$/i)) {
						$.modalbox(href);
					} else {
						window.document.location = href ;
					}
				}
			});

		$(this.c).append(image);
		$(this.c).css({width:this.cote+'px',height:this.cote+'px'});

		var colonne = this.cptj%this.cols;
		this.left = colonne*this.cote/2;
		l = (colonne-(colonne%2));
		if((colonne == this.cols-1) && this.cols%2 > 0) l = l-1;
		l = l*this.cote/2;
		$(this.c).css("left",l+"px");
		
		var ligne = (this.cptj-(this.cptj%this.cols))/this.cols;
		this.top = ligne*this.cote/2;
		t = (ligne - (ligne%2));
		if((ligne == this.rows-1) && (this.rows%2 > 0)) t = t-1;
		t= t* this.cote/2;
		$(this.c).css("top",t + "px");


		var back = $(this.elt.parentNode).css('background-color');
		//if(!back || back=='transparent') back = 'white';
		//$(this.c).css('background-color',back);		
		$(this.c).fadeIn(2000);		
		setTimeout(getObjectMethodClosure(this,'resize'),4000); 
		setTimeout(getObjectMethodClosure(this,'postpone'),7000);

	},
	//on anime la grosse vignette
	resize: function() {
		var t = new Number(this.top);
		var l = new Number(this.left);
		jQuery(this.c).animate({top:t,left:l,width:this.cote/2,height:this.cote/2},1500);
	}
};
