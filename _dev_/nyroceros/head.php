<?php

function Nyro_insert_head($flux){
	$flux = Nyro_header_prive($flux);
	return $flux;
}

function Nyro_header_prive($flux) {
	include_spip("inc/filtres");

	$flux .='
<script src=\''.url_absolue(find_in_path('js/jquery.nyroModal-1.2.8.js')).'\' type=\'text/javascript\'></script>
<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/nyroModal.full.css')).'" type="text/css" media="projection, screen, tv" />
';
	return $flux;
}

?>
