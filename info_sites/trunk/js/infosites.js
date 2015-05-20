// on ajoute les titres de section au sommaire du aside pour faire des liens vers les sections

$(document).ready(function(){
	if( $('.contenu .legend').length > 0 )
	{
		if( $('#aside .dropdown.boutons').length > 0 ){
			$('<div class="sommaire"><ul></ul></div>').insertAfter($('#aside .dropdown.boutons'));
		}
		else{
			$('#aside').append('<div class="sommaire"><ul></ul></div>');
		}

		$('.contenu .legend').each(function(){
			$('#aside .sommaire ul').append('<li><a href="#'+ $(this).attr('id') +'">'+ $(this).text() +'</a></li>');
		});
	}
});
