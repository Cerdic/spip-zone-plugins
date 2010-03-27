jQuery(document).ready(function(){
	jQuery('a[rel=enclosure]').each(function(){
		if(jQuery(this).attr('href').match(/\.mp3(\\?.*)$/i)){
			jQuery(this).wrap('<div class="ui360"></div>');
		}
	});
});