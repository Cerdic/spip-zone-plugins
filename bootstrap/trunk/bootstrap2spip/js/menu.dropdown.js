jQuery(function(){
	// detecter les navbar avec deroulant
	// poser les class qui vont bien
	// et lancer le dropdown BootStrap dessus
	jQuery('.navbar .menu-items .menu-items').closest('.navbar').each(function(){
		jQuery(this)
			.find('.menu-items').eq(0)
			.children('.item').addClass('dropdown')
			.children('.menu-items').addClass('dropdown-menu')
			.siblings('a').addClass('dropdown-toggle').append('<b class="caret"></b>').dropdown()
			.siblings('.menu-items')
			.find('.menu-items').hide();
	});
});