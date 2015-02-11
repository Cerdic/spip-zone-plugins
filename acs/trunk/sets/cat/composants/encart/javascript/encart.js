function initEncarts() {
	jQuery('.cEncartClass').each(
		function(key, val) {
			var encart = jQuery(this);
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
			    				if (btnD.is('a') && btnS.is('a')) {
			    					btnS.click();
			    				}
			    				else {
			    					btnD.click();
			    				}
		    				}
			    		},delai);
		    		}
		    	}
		    }
			});
		}
	);
}

jQuery(document).ready(
  function() {
  	initEncarts();
    onAjaxLoad(initEncarts);
  }
);