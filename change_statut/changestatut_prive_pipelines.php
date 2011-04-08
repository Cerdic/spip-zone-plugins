<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function changestatut_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('changestatut.css').'" type="text/css" media="projection, screen, tv" />';

	$config = lire_config('changestatut',array());
	$classe = "bando2_vers".$config['statut']."21" ;

	$flux .= '<script type="text/javascript">
		jQuery(document).ready(function(){
			$( "#bando_outils .rapides .bando2_versredacteur21" ).removeClass("statut_on");
			$( "#bando_outils .rapides .bando2_versadmin21" ).removeClass("statut_on");
			$( "#bando_outils .rapides .bando2_verswebmestre21" ).removeClass("statut_on");
			$( "#bando_outils .rapides .'.$classe.'" ).addClass("statut_on") ;
		});</script>';

	return $flux;
}
?>