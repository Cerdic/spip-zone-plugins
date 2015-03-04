<?php

function date_u($date) {
	return date("U", strtotime($date));
}

function calcul_date_insert_head($flux) {
	$lang = $GLOBALS["spip_lang"];

	$ret = "<script src='spip.php?page=js.calcul_date_lang&lang=$lang' type='text/javascript'></script>";
	$ret .= "<script src='" . find_in_path("javascript/calcul_date.js") . "' type='text/javascript'></script>";

	return $flux.$ret;

}


// Ca, on ne l'utilise plus: on fait un POST et on recupere la date du serveur de cette facon...
/*
function calcul_date_affichage_final($flux) {

	$ret .= "
	<script type='text/javascript'><!--
	var date_now = \"".date('U')."\";
	--></script>
	";

	return str_replace("</head>", $ret."</head>", $flux);
}
*/
?>
