<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_THEMES_INTERFACE_PRIVEE',(_DIR_PLUGINS.end($p)));


function themes_interface_privee_header_prive($flux) {
	global $visiteur_session, $REQUEST_URI;
	$c = (is_array($visiteur_session)
	AND is_array($visiteur_session['prefs']))
		? $visiteur_session['prefs']['couleur']
		: 1;

	$couleurs = charger_fonction('couleurs', 'inc');
	
	$interface = $GLOBALS['visiteur_session']['prefs']['interface_privee'];
	if (strlen($interface) == 0) $interface = "standard";
	
	
	if ($interface == "blanche") $flux .= '<link rel="stylesheet" type="text/css" href="../spip.php?page=style_prive_blanche&amp;ltr=' . $GLOBALS['spip_lang_left'] . '&'. $couleurs($c).'" id="css_blanche" />';
	else if ($interface == "wpip") $flux .= '<link rel="stylesheet" type="text/css" href="../spip.php?page=style_prive_wpip&amp;ltr=' . $GLOBALS['spip_lang_left'] . '&'. $couleurs($c).'" id="css_wpip" />';
	else if ($interface == "degrades") $flux .= '<link rel="stylesheet" type="text/css" href="../spip.php?page=style_prive_degrades&amp;ltr=' . $GLOBALS['spip_lang_left'] . '&'. $couleurs($c).'" id="css_degrades" />';
	else if ($interface == "bonux") {
		$flux .= '<link rel="stylesheet" type="text/css" href="../spip.php?page=style_prive_bonux&amp;ltr=' . $GLOBALS['spip_lang_left'] . '&'. $couleurs($c).'" id="css_degrades" />';
	}

	
	$url = url_absolue($REQUEST_URI);
	
	// Ajouter les liens pour changer d'interface
	$bouton["$interface"] = "<img src='".chemin("images/puce-verte-breve.gif")."' alt='selectionnee' />";
	
	$liens = "<div class='titrem' style='text-align: center;'>Choix de l’interface</div>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["standard"]."</td><td class='arial2'><a href='?action=preferer_interface&amp;arg=standard&amp;redirect=".urlencode($url)."' class='lien_sous'>Standard</a></td></tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["degrades"]."</td><td class='arial2'><a href='?action=preferer_interface&amp;arg=degrades&amp;redirect=".urlencode($url)."' class='lien_sous'>Standard avec effets</a></td></tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["bonux"]."</td><td class='arial2'><a href='?action=preferer_interface&amp;arg=bonux&amp;redirect=".urlencode($url)."' class='lien_sous'>Contrast&eacute;e</a></td></tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["wpip"]."</td><td class='arial2'><a href='?action=preferer_interface&amp;arg=wpip&amp;redirect=".urlencode($url)."' class='lien_sous'>&Agrave; la fa&ccedil;on de Wordpress</a></td></tr>";
	$liens .= "<tr class='tr_liste'><td>".$bouton["blanche"]."</td><td class='arial2'><a href='?action=preferer_interface&amp;arg=blanche&amp;redirect=".urlencode($url)."' class='lien_sous'>Blanche</a></td></tr>";
	
	$flux .= "
		<script type='text/javascript'><!--
			$(document).ready(function() {
				$('#bandeauinterface').append(\"<div id='bandeauprefinterface' class='cadre cadre-liste'><table width='100%' cellpadding='2' cellspacing='0' border='0'>$liens</table></div>\");
			});
		--></script>
	";
	
	
	return $flux;
}

?>