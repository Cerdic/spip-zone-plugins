/*Featured Image Zoomer (May 8th, 2010) Last updated: May 10th, 2010
* This notice must stay intact for usage 
* Author: Dynamic Drive at http://www.dynamicdrive.com/
* Visit http://www.dynamicdrive.com/ for full source code
* http://www.dynamicdrive.com/dynamicindex4/featuredzoomer.htm
*/

//conflit jQuery, on gere autrement les conflits sinon plantage
//jQuery.noConflict()

var featuredimagezoomer={
	// on passe en css 
	// loadinggif: 'spinningred.gif', //full path or URL to "loading" gif
	magnifycursor: 'crosshair', //Value for CSS's 'cursor' attribute, added to original image

	/////NO NEED TO EDIT BEYOND HERE////////////////
	dsetting: {magnifiersize:[200,200]}, //default settings

	isContained:function(m, e){
		var e=window.event || e;
		var c=e.relatedTarget || ((e.type=="mouseover")? e.fromElement : e.toElement);
		while (c && c!=m)try {c=c.parentNode} catch(e){c=m};
		if (c==m)
			return true;
		else
			return false;
	},

	showimage:function($, $img, $mag, showstatus){
		var specs=$img.data('specs');
		var coords=$img.data('specs').coords; //get coords of thumb image (from upper corner of document)
		specs.windimensions={w:$(window).width(), h:$(window).height()}; //remember window dimensions
		var magcoords={}; //object to store coords magnifier DIV should move to
		magcoords.left=coords.left+((specs.magpos=="left")? -specs.magsize.w-10 : +$img.width()+10);
		$mag.css({left: magcoords.left, top:coords.top}).show(); //position magnifier DIV on page
		var expliczoom = "Use Mouse Wheel to Zoom In/Out";
		// on recup la traduction dans le modele, 1er span si plusieurs zoom sur lapage !
		if ($('.zoomer span:first').text().length) expliczoom = $('.zoomer span:first').text();
		//if (specs.explication){expliczoom=specs.explication}
		specs.$statusdiv.html('Zoom '+specs.curpower+'<div class="tellzoom">'+ expliczoom +'</div>');
		if (showstatus) //show status DIV? (only when a range of zoom is defined)
			this.showstatusdiv(specs, 200, 2000);
	},

	hideimage:function($img, $mag, showstatus){
		var specs=$img.data('specs');
		$img.data('mouseisout', true);
		$mag.hide();
		if (showstatus)
			this.hidestatusdiv(specs);
	},

	showstatusdiv:function(specs, fadedur, showdur){
		clearTimeout(specs.statustimer);
		specs.$statusdiv.fadeIn(fadedur) ;//show status div
		specs.statustimer=setTimeout(function(){featuredimagezoomer.hidestatusdiv(specs)}, showdur); //hide status div after delay
	},

	hidestatusdiv:function(specs){
		specs.$statusdiv.stop(true, true).hide();
	},

	moveimage:function($img, $maginner, e){
		function getboundary(b, val){ //function to set x and y boundaries magnified image can move to
			if (b=="left"){
				var rb=-specs.imagesize.w*specs.curpower+specs.magsize.w;
				return (val>0)? 0 : (val<rb)? rb : val;
			}
			else{
				var tb=-specs.imagesize.h*specs.curpower+specs.magsize.h;
				return (val>0)? 0 : (val<tb)? tb : val;
			}
		}
		var specs=$img.data('specs');
		var imgcoords=specs.coords;
		var pagex=(e.pageX || specs.lastpagex), pagey=(e.pageY || specs.lastpagey);
		var x=pagex-imgcoords.left, y=pagey-imgcoords.top;
		var newx=-x*specs.curpower+specs.magsize.w/2; //calculate x coord to move enlarged image
		var newy=-y*specs.curpower+specs.magsize.h/2;
		$maginner.css({left:getboundary('left', newx), top:getboundary('top', newy)});
		specs.$statusdiv.css({left:pagex-10, top:pagey+20});
		specs.lastpagex=pagex; //cache last pagex value (either e.pageX or lastpagex), as FF1.5 returns undefined for e.pageX for "DOMMouseScroll" event
		specs.lastpagey=pagey;
	},

	magnifyimage:function($img, e, zoomrange){
		var delta=e.detail? e.detail*(-120) : e.wheelDelta; //delta returns +120 when wheel is scrolled up, -120 when scrolled down
		var zoomdir=(delta<=-120)? "out" : "in";
		var specs=$img.data('specs');
		var magnifier=specs.magnifier, od=specs.imagesize, power=specs.curpower;
		var newpower=(zoomdir=="in")? Math.min(power+1, zoomrange[1]) : Math.max(power-1, zoomrange[0]); //get new power
		var nd=[od.w*newpower, od.h*newpower]; //calculate dimensions of new enlarged image within magnifier
		magnifier.$image.css({width:nd[0], height:nd[1]});
		specs.curpower=newpower; //set current power to new power after magnification
		specs.$statusdiv.html('Zoom '+specs.curpower);
		this.showstatusdiv(specs, 0, 500);
		$img.trigger('mousemove');
	},

	init:function($, $img, options){
		var setting=$.fn.extend({}, this.dsetting, options);
		var $statusdiv=$('<div style="position:absolute;visibility:hidden;left:0;top:0;z-index:1000" />')
			//.html('<img src="'+this.loadinggif+'" />')
			.appendTo(document.body); //create DIV to show "loading" gif/ "Current Zoom" info
		var $magnifier=$('<div class="magnifyarea bibou" style="position:absolute;width:'+setting.magnifiersize[0]+'px;height:'+setting.magnifiersize[1]+'px;left:-10000px;top:-10000px;visibility:hidden;overflow:hidden;border:1px solid black;" />')
			.append('<div style="position:relative;left:0;top:0;" />')
			.appendTo(document.body); //create magnifier container

		function getspecs($maginner, $bigimage){ //get specs function
			var magsize={w:$magnifier.width(), h:$magnifier.height()};
			var imagesize={w:$img.width(), h:$img.height()};
			var power=(setting.zoomrange)? setting.zoomrange[0] : ($bigimage.width()/$img.width()).toFixed(5);
			$img.data('specs', {
				$statusdiv: $statusdiv,
				statustimer: null,
				magnifier: {$outer:$magnifier, $inner:$maginner, $image:$bigimage},
				magsize: magsize,
				magpos: setting.magnifierpos,
				imagesize: imagesize,
				curpower: power,
				coords: getcoords(),
				explication:setting.explication
			});
		}

		function getcoords(){ //get coords of thumb image function
			var offset=$img.offset(); //get image offset from document
			return {left:offset.left, top:offset.top}
		}

		$img.one('mouseover', function(e){
			var $maginner=$magnifier.find('div:eq(0)');
			//+ possibilite du href parent
			var $bigimage=$('<img src="'+(setting.largeimage || $img.parent('a').attr('href') || $img.attr('src'))+'"/>').appendTo($maginner);
 			var showstatus=setting.zoomrange && setting.zoomrange[1]>setting.zoomrange[0];
 			//.attr({alt:'', title:''}) //remove alt/title attribute of thumb image and 
			$img.css({opacity:0.1}); //remove alt/title attribute of thumb image and "dim" image while large image is loading
			var imgcoords=getcoords();
			$statusdiv.css({left:imgcoords.left+$img.width()/2-25-$statusdiv.width()/2, top:imgcoords.top+$img.height()/2-25-$statusdiv.height()/2, visibility:'visible'})
			.addClass("loadzoomer");
			$bigimage.bind('loadevt', function(){ //magnified image ONLOAD event function (to be triggered later)
				$img.css({opacity:1}); //restore thumb image opacity
				$statusdiv.empty().css({border:'1px solid black', background:'#C0C0C0', padding:'4px', font:'bold 13px Arial', opacity:0.8}).hide()
				.removeClass("loadzoomer");
				if (setting.zoomrange){ //if set large image to a specific power
					var nd=[$img.width()*setting.zoomrange[0], $img.height()*setting.zoomrange[0]]; //calculate dimensions of new enlarged image
					$bigimage.css({width:nd[0], height:nd[1]});
				}
				getspecs($maginner, $bigimage); //remember various info about thumbnail and magnifier
				$magnifier.css({display:'none', visibility:'visible'});
				if (!$img.data('mouseisout'))
					featuredimagezoomer.showimage($, $img, $magnifier, showstatus);
				$img.mouseover(function(e){ //image onmouseover
					$img.data('specs').coords=getcoords(); //refresh image coords (from upper left edge of document)
					if (!featuredimagezoomer.isContained($statusdiv.get(0), e))
						featuredimagezoomer.showimage($, $img, $magnifier, showstatus)
				})
				$img.mousemove(function(e){ //image onmousemove
					featuredimagezoomer.moveimage($img, $maginner, e)
				})
				featuredimagezoomer.moveimage($img, $maginner, e);
				$img.mouseout(function(e){ //image onmouseout
					if (!featuredimagezoomer.isContained($statusdiv.get(0), e))
						featuredimagezoomer.hideimage($img, $magnifier, showstatus);
				})
				$statusdiv.mouseout(function(e){ //status div onmouseout
					if (!featuredimagezoomer.isContained($statusdiv.get(0), e) && e.relatedTarget!=$img.get(0)){ //if mouse is not moving into another element within status div or into thumbnail image itself
						featuredimagezoomer.hideimage($img, $magnifier, showstatus); //hide image
					}
				})				
				if (setting.zoomrange && setting.zoomrange[1]>setting.zoomrange[0]){ //if zoom range enabled
					var mousewheelevt=(/Firefox/i.test(navigator.userAgent))? "DOMMouseScroll" : "mousewheel"; 
					//FF doesn't recognize mousewheel as of FF3.x
					;
					$img.bind(mousewheelevt, function(e){
						featuredimagezoomer.magnifyimage($img, e, setting.zoomrange);
						e.preventDefault();
					})
				}
			});
			//end $bigimage onload
			if ($bigimage.get(0).complete){ //if image has already loaded (account for IE, Opera not firing onload event if so)
				$bigimage.trigger('loadevt');
			}
			else{
				$bigimage.bind('load', function(){$bigimage.trigger('loadevt');})
			}
		
		});
	}

}


jQuery.fn.addimagezoom=function(options){
	var $=jQuery;
	return this.each(function(){ //return jQuery obj
		if (this.tagName!="IMG")
			return true; //skip to next matched element
		var $imgref=$(this);
		$imgref.css({cursor:featuredimagezoomer.magnifycursor});
		featuredimagezoomer.init($, $imgref, options)
	})
}