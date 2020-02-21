$(document).ready(function() {
	$('.editer_types_noisettes_masques > div > input[type=checkbox]').each(function() {
		var type_noisette = $( this ).val();
		var exclus = ["socialtags_fb_like", "socialtags_fb_like_box", "socialtags_badge_fb"];
		if( $.inArray( type_noisette, exclus ) != -1 ) {
			$( this ).attr( 'checked','checked' ).attr( 'onclick','return false;' ).css( "opacity", "0.5" );
		}
	});
});