<?php

function outils_lame_igraine_config_dist() {
	
	add_outil(array(
		'id'          => "lame_igraine",
		'nom'         => _T("blagoulames:igraine_nom"),
		'description' => _T("blagoulames:igraine_description"),
		'categorie'   => _T('blagoulames:categorie'),
		'code:js'     => "
			speedy = ['slow','normal','fast'];
			
			function migraine(){
				speed = Math.floor(Math.random()*3);
				wait = Math.floor(Math.random()*500);
				opa = Math.floor(Math.random()*8)/10 + 0.2;
				//qui = Math.floor(Math.random() * jQuery('body div[id]').length);
				setTimeout(function(){
					jQuery('body')
						//.eq(qui)
						.animate({opacity:opa}, speedy[speed], 'linear', migraine);
				},wait);

			}
			",
		'code:jq'     => "
			migraine();
			",
	));
	
}
?>
