if (window.jQuery) jQuery(function(){
	// si un resume est present...
	jQuery('div.blocs_resume').prev().toggleClass('blocs_click').click(function(){
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible')
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
		});

	// sinon...
	jQuery('h4.blocs_click').click( function(){
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
		});

	// si un bloc ajax est present...
	jQuery('h4.blocs_ajax').click(function(){
		var k=jQuery(this).children().attr("href");
		if(k=='javascript:;') return false;
		// une fois le bloc ajax en place, plus besoin de le recharger ensuite
		jQuery(this).children().attr("href", 'javascript:;');
		// on charge !
		jQuery(this).parent().children(".blocs_destination")
//.animeajax()
		.load(k);
		return false;
		});
});

// un JS actif replie les blocs invisibles
document.write('<style type="text/css">div.blocs_invisible{display:none;}</style>');
