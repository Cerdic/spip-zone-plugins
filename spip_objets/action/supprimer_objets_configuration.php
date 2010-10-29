<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_objets_configuration_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	
	action_supprimer_objets_configuration_post($arg);
	
}

function action_supprimer_objets_configuration_post($objet) {
	sql_drop_table("spip_".$objet);
	sql_drop_table("spip_".$objet."_liens");
	$objets_installes=liste_objets_meta();
	// faire une boucle ici pour supprimer lobjet installe dans le meta
	$retour=array();
	foreach ($objets_installes as $key=>$tmp){
		if($tmp!=$objet){
			$retour[]=$objet;
		}
	}
	//TODO : on pourrait passer par une fonction qui gére cette insertion,
	// étant donné qu'a court terme on va gérer d'autres infos dans cette meta 
	//(lien_rubrique=oui, lien_article=oui, libelle compréhensible,....) 
	ecrire_meta('objets_installes',serialize($retour));
}
?>