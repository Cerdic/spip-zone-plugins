 /* if (window.jQuery) */
 jQuery(function(){
	// clic sur un outil
	jQuery('.blocs_resume').prev().toggleClass('blocs_click').click(function()	{
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible')
		.next().toggleClass('blocs_invisible');
		return false;
		});
		
	 jQuery('h4.blocs_click').click( function() {
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
		});
		
	jQuery('h4.blocs_ajax').click(function(){
		var k=jQuery(this).children().attr("href");
		jQuery(this).removeClass('blocs_ajax')
		.parent().children(".blocs_destination").load(k);	
		});
});

// un JS actif replie les blocs invisibles
document.write('<style type="text/css">div.blocs_invisible{display:none;}</style>');
