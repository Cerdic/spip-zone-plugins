<?php

function outils_poney_bolivien_config_dist() {
	$poney = find_in_path('images/poney.png');
	
	add_outil(array(
		'id'          => "poney_bolivien",
		'nom'         => _T("blagoulames:poney_nom"),
		'description' => _T("blagoulames:poney_description"),
		'categorie'   => _T('blagoulames:categorie'),
		'code:js'     => "
			",
		'code:jq'     => "
			jQuery('<img src=\'$poney\' id=\'poney_bolivien\' />')
			.appendTo('body')
			.css('position', 'fixed')
			.css('z-index', '1000000')
			.css('right', '0')
			.css('top', '30%')
			.animate({right:'85%'}, {duration:5000, queue:'gauche'})
			.animate(
				{
					width:'100%',
					height:'100%',
					right:'0',
					top:'0'
				},
				{
					duration:5000,
					queue:'gauche',
				}
			)
			.dequeue('gauche')
			.animate({top:'20%'}).animate({top:'30%'})
			.animate({top:'20%'}).animate({top:'30%'})
			.animate({top:'20%'}).animate({top:'30%'})
			.animate({top:'20%'}).animate({top:'30%'});
			",
	));
	
}
?>
