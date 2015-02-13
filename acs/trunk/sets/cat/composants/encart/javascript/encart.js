function initEncarts() {
	var fadeDelay = 200;
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
			var classes = encart.attr('class').split(/\s+/);
			jQuery.each(classes, function(index, item) {
		    if (delai = item.match(/^cEncartTimer(.*)/)) {
		    	if (!isNaN(delai[1])) { // is numeric
		    		delai = parseInt(delai[1]);
		    		if (delai > 0) {
		    			encart.find('.contenu').fadeTo(0,fadeMin).fadeTo(fadeDelay,1);
		    			btnPause.show();
		    			btnPause.css("cursor","pointer");
		    			btnPause.click(function(e) {
		    				if (papa.hasClass("paused")) {
		    					papa.removeClass("paused");
		    				}
		    				else {
		    					papa.addClass("paused");
		    				}
		    			});
		    			setInterval(function() {
		    				if (!papa.hasClass("paused")) {
		    					encart.find('.contenu').fadeTo(fadeDelay,fadeMin, function() {
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