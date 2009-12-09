/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/*!
 * jquery.qtip. The jQuery tooltip plugin
 *
 * Copyright (c) 2009 Craig Thompson
 * http://craigsworks.com
 *
 * Licensed under MIT
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Launch  : February 2009
 * Version : 1.0.0-rc3
 * Released: Tuesday 12th May, 2009 - 00:00
 * Debug: jquery.qtip.debug.js
 */
(function($)
{
	jQuery.fn.qtip_activate = function() {
	  return this.each(function() {
			var content = jQuery(this).siblings('.qTipContent');
			if (content.length)
				jQuery(this).qtip({
					content: {
						text: content
					},
					style: {
						tip: true,
						name: 'cream' // Inherit from preset style
						/*width: { max:220}*/
					},
					position: {
					 corner: { target: 'rightTop', tooltip: 'leftTop' }
					}
				});
			jQuery(this).addClass('qTipDone');
		});
	}

	jQuery(function() {
		jQuery('a.qTip').qtip_activate();
	});

	// ... et a chaque fois que le DOM change
	onAjaxLoad(function() {
		if (jQuery){
			jQuery('a.qTip',this).qtip_activate();
		}
	});

})(jQuery);
