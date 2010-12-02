<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supp_statut_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	
	action_supp_statut_post($arg);
	
}

function action_supp_statut_post($statut) {
		
	$statuts=statut_auteurs_get_statuts();
	// faire une boucle ici pour supprimer le statut installe dans le meta
	$retour=array();
	foreach ($statuts as $s=>$libelle){
		if($s!=$statut){
			$retour[$s]=$libelle;
		}
	}
	
	ecrire_meta('statut_auteurs:autre_statut_auteur',serialize($retour));
	redirige_par_entete('/ecrire/?exec=gestion_statut_auteurs',true);
}
?>