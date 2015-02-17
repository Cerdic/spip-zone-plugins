function initEncarts() {
	var fadeDelay = 400; // millisecondes
	var fadeMin = 0;
	jQuery('.cEncartClass').each(
		function(key, val) {
			var encart = jQuery(this);
			if (encart.hasClass('initialized')) return false;
			var papa = encart.parent();
			var delai = 0; // timer
			var btnD = encart.find('.btnEncartDebut');
			var btnP = encart.find('.btnEncartPrecedent');
			var btnS = encart.find('.btnEncartSuivant');
			var btnPause = encart.find('.btnEncartPause');
			var btnPlay = encart.find('.btnEncartPlay');
			var classes = encart.attr('class').split(/\s+/);
			jQuery.each(classes, function(index, item) {
		    if (delai = item.match(/^cEncartTimer(.*)/)) {
		    	if (!isNaN(delai[1])) { // is numeric
		    		delai = parseInt(delai[1]);
		    		if (delai > 0) {
		    			bgencart = encart.css('background-color');
		    			encart.find('.contenu').animate({backgroundColor:'#000','opacity':fadeMin},0).animate({backgroundColor:bgencart,'opacity':1},fadeDelay,'linear');
		    			if (papa.hasClass("paused"))
		    				btnPlay.show();
		    			else
		    				btnPause.show();
		    			btnPause.css("cursor","pointer");
		    			btnPlay.css("cursor","pointer");
		    			btnPause.click(function(e) {
	    					papa.addClass("paused");
	    					btnPlay.show();
	    					btnPause.hide();
		    			});
		    			btnPlay.click(function(e) {
	    					papa.removeClass("paused");
	    					btnPlay.hide();
	    					btnPause.show();
		    			});
		    			setInterval(function() {
		    				if (!papa.hasClass("paused")) {
		    					encart.find('.contenu').animate({backgroundColor:'#000','opacity':fadeMin}, fadeDelay,'linear',function() {
				    				if (btnD.is('a') && btnS.is('a')) {
				    					btnS.click();
				    				}
				    				else {
				    					btnD.click();
				    				}
		    					});
		    				}
			    		},delai);
		    		}
		    	}
		    }
			});
			encart.addClass('initialized');
		}
	);
}

jQuery(document).ready(
  function() {
  	initEncarts();
    onAjaxLoad(initEncarts);
  }
);