<?php

function themes_interface_privee_header_prive($flux) {
	global $visiteur_session, $REQUEST_URI;
	$c = (is_array($visiteur_session)
	AND is_array($visiteur_session['prefs']))
		? $visiteur_session['prefs']['couleur']
		: 1;

	$couleurs = charger_fonction('couleurs', 'inc');
	
	$interface = $GLOBALS['visiteur_session']['prefs']['interface_privee'];
	if (strlen($interface) == 0) $interface = "standard";
	
	//generer_url_public('style_prive', parametres_css_prive())
	if ($interface == "blanche") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('style_prive_blanche', parametres_css_prive()).'" id="css_blanche" />';
	else if ($interface == "wpip") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('style_prive_wpip', parametres_css_prive()).'" id="css_wpip" />';
	else if ($interface == "degrades") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('style_prive_degrades', parametres_css_prive()).'" id="css_degrades" />';
	else if ($interface == "ispip") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('style_prive_ispip', parametres_css_prive()).'" id="css_ispip" />';
	else if ($interface == "geek") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('style_prive_geek', parametres_css_prive()).'" id="css_igeek" />';
	else if ($interface == "bonux") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('style_prive_bonux', parametres_css_prive()).'" id="css_bonux" />';
	else if ($interface == "chocolat") {
		$flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('style_prive_chocolat', parametres_css_prive()).'" id="css_chocolat" />';
		$flux .= "\n<script type='text/javascript'><!--
				$(document).ready(function() {
					$('.bandeau_sec').css({'width': '150px'});
					$('.bandeau_sec .cellule36').css({'width': 'auto'});
				});
		--></script>";
	}

	
	$url = url_absolue($REQUEST_URI);
	
	// Ajouter les liens pour changer d'interface
	$bouton["$interface"] = "<img src='".chemin("images/puce-verte-breve.gif")."' alt='selectionnee' />";
	
	$entete = "<div class='titrem' style='text-align: center;'>Choix de l’interface<\/div>";
	$liens = "<tr class='tr_liste'><td>".$bouton["standard"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=standard&redirect=".urlencode($url))."' class='lien_sous'>Standard<\/a><\/td><\/tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["degrades"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=degrades&redirect=".urlencode($url))."' class='lien_sous'>Standard avec effets<\/a><\/td><\/tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["bonux"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=bonux&redirect=".urlencode($url))."' class='lien_sous'>Contrast&eacute;e<\/a><\/td><\/tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["wpip"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=wpip&redirect=".rawurlencode($url))."' class='lien_sous'>&Agrave; la fa&ccedil;on de Wordpress<\/a><\/td><\/tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["chocolat"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=chocolat&redirect=".rawurlencode($url))."' class='lien_sous'>Menthe et chocolat<\/a><\/td><\/tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["ispip"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=ispip&redirect=".rawurlencode($url))."' class='lien_sous'>iSpip<\/a><\/td><\/tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["geek"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=geek&redirect=".rawurlencode($url))."' class='lien_sous'>Revenge of the dark geek<\/a><\/td><\/tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["blanche"]."<\/td><td class='arial2'><a href='".generer_url_action('preferer_interface',"arg=blanche&redirect=".urlencode($url))."' class='lien_sous'>Blanche<\/a><\/td><\/tr>";
	
	$flux .= "
		<script type='text/javascript'><!--
			$(document).ready(function() {
				$('#bandeauinterface').append(\"<div id='bandeauprefinterface' class='cadre cadre-liste' style='width: 240px;'>$entete<table width='100%' cellpadding='2' cellspacing='0' border='0'>$liens<\/table><\/div>\");
			});
		--></script>
	";
	
	
	return $flux;
}

?>