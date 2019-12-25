<?php

$GLOBALS['marqueur_skel'] = (isset($GLOBALS['marqueur_skel']) ?  $GLOBALS['marqueur_skel'] : '').":bootstrap4";
// la puce sans image si jamais on est pas encore en SPIP 3.3+
$GLOBALS['puce'] = '<span class="spip-puce ltr"><b>–</b></span>';
$GLOBALS['puce_rtl'] = '<span class="spip-puce rtl"><b>–</b></span>';

function bootstrap4_affichage_final($flux){
	if (
		$GLOBALS['html']
		AND isset($GLOBALS['visiteur_session']['statut'])
		AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
		AND $GLOBALS['visiteur_session']['webmestre']=='oui'
		AND strpos($flux,"<!-- insert_head -->")!==false
		AND $p=stripos($flux,"</body>")
	) {
		if ($f = find_in_path("js/hashgrid.js")){
			$flux = substr_replace($flux,'<script type="text/javascript" src="'.$f.'"></script>',$p,0);
		}
		if ((_VAR_MODE === 'debug' || _request('var_profile'))
			AND $p=stripos($flux,"</head>")){
			$file_css = direction_css(scss_select_css('css/spip.admin.css'));
			$css = file_get_contents($file_css);
			$css = "<style type='text/css'>$css</style>";
			$flux = substr_replace($flux,$css,$p,0);
		}

	}
	return $flux;
}
