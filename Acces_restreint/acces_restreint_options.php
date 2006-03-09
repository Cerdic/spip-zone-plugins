<?php

include_spip('base/acces_restreint');
include_spip('inc/acces_restreint');

//$GLOBALS['surcharge']['exec/auteurs_edit']=dirname(__FILE__).'/exec/auteurs_edit.php';

// ajouter un marqueur de cache pour permettre de differencier le cache en fonction des zones autorisees
// potentiellement une version de cache differente par combinaison de zones habilitees + le cache de base sans autorisation
if ($auteur_session['id_auteur']){
	$zones = AccesRestreint_liste_zones_appartenance_auteur(intval($auteur_session['id_auteur']));
	$zones = join("-",$zones);
	$GLOBALS['marqueur'].=":zones_acces_autorises $zones";
}

?>