/*
     * author: iosif chiriluta
     * website: www.iosif.chiriluta.ro
     * script version: 1.2
     * script link download: http://iosif.chiriluta.ro/final/iZoom/iZoom.js
     * demo script: http://iosif.chiriluta.ro/final/iZoom       
*/
(function($){
	$.fn.iZoom = function(options){
          options = $.extend({
                         'diameter':150,
                         'borderColor':'#ccc',
                         'borderWidth':'1px',
                         'borderStyle':'solid'
                    },options || {});	    
		var $this = $(this); var loupe = $this; var wrpImage; var lWidth; var lHeight; var sWidth; var sHeight; var sBorderLeft; var sBorderTop;
		$this.mouseenter(function(e){
			$this.css('cursor','crosshair'); sWidth = $this.width(); sHeight = $this.height(); sBorderLeft = $this.css('border-left-width').replace('px',''); sBorderTop = $this.css('border-top-width').replace('px','');
						
			var lImage = new Image();
			lImage.src = $this.attr('src');
			lWidth = lImage.width;
			lHeight = lImage.height;
			
            loupe = $('<div>').css({
				position: 'absolute',
				width: options.diameter,
				height: options.diameter,
				'border-width': options.borderWidth,
                'border-style':  options.borderStyle,
                'border-color': options.borderColor,
				overflow: 'hidden',
				'z-index': '1000',
				'-moz-border-radius': options.diameter/2,
                '-khtml-border-radius': options.diameter/2,
                'border-radius': options.diameter/2,
				background: '#fff url('+lImage.src+') no-repeat'
			}).attr('id','iLoupe');				
				
			$('body').append(loupe);
			
		}).mousemove(function(e){
			var sxPointer = e.pageX - $this.offset().left - parseInt($this.css('padding-left'));
			var syPointer = e.pageY - $this.offset().top - parseInt($this.css('padding-top'));
		
			loupe.css({
				left: e.pageX+10,
				top: e.pageY+10
			});

			var sxPointerPer = sxPointer/sWidth*100;
			var syPointerPer = syPointer/sHeight*100;
			sxPointerPer = Math.round(sxPointerPer*Math.pow(10,10))/Math.pow(10,10);
			syPointerPer = Math.round(syPointerPer*Math.pow(10,10))/Math.pow(10,10);
			
			var sXPointer = Math.round(((sxPointerPer/100*lWidth)*Math.pow(10,0))/Math.pow(10,0));
			var sYPointer = Math.round(((syPointerPer/100*lHeight)*Math.pow(10,0))/Math.pow(10,0));

			(sxPointer>=0 && sxPointer<=sWidth && syPointer>=0 && syPointer<=sHeight) ? loupe.css({"background-position":(-sXPointer+options.diameter/2)+"px "+(-sYPointer+options.diameter/2)+"px"}) : false;
		}).mouseleave(function(){
			loupe.remove();
		});
	};	
})(jQuery);