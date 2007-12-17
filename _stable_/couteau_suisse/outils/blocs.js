jQuery(function(){
	// clic sur un outil
	jQuery('div.cs_blocs h4').click( function() {
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
	});
});

// un JS actif replie les blocs invisibles
document.write('<style type="text/css">div.cs_blocs div.blocs_invisible {display:none;}</style>');
