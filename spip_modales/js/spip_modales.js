function spip_modales_magnific() {
	$('.magnific-group').magnificPopup({
		delegate: 'a',
		type: 'image',
		gallery:{
			enabled:true
		}
	});
}

$(document).ready(function() {
    if($('.magnific-group').length){ spip_modales_magnific(); }
});