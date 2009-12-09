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
	jQuery(document).ready(function(){
		jQuery("a.qTip").each(function(){
			var content = jQuery(this).siblings('.qTipContent');
			if (content.length)
				jQuery(this).qtip({
					content: {
						text: jQuery(this).siblings('.qTipContent')
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
		});
	});
})(jQuery);
