if (window.jQuery) jQuery(function(){
	// clic sur un outil
	jQuery('h4.blocs_titre').click( function() {
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
	});
});

// un JS actif replie les blocs invisibles
document.write('<style type="text/css">div.blocs_invisible{display:none;}</style>');
