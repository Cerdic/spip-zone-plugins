<?php
/***************************************************************************\
 * Plugin Vider Rubrique pour Spip 3.0
 * Licence GPL (c) 2012 - Apsulis
 * Suppression de tout le contenu d'une rubrique
 *
\***************************************************************************/


function vider_rubrique_objet_poubelle($objet,$id_objet,$statut){
	spip_log("Suppression $objet : $id_objet.",'vider_rubrique');
	$c = array('statut' => $statut);

	include_spip('action/editer_objet');
	if ($err=objet_instituer($objet, $id_objet, $c))
		$res = array('message_erreur'=>$err,'objet'=>$objet);
	else {
		$res = array('message_ok'=>_T('info_modification_enregistree'));
	}
	if(lire_config("vider_rubrique/config/effacement")=="oui"){
		supprimer_les_logos($objet,$id_objet);
	}
	return $res;
}

function supprimer_rubrique($liste_id) {
	$supprimer_rubrique = charger_fonction('supprimer_rubrique','action');
	/* On efface les rubriques les plus profondes en premier, sinon on ne pourra pas supprimer ses parents */
	$les_id = array_reverse(explode(",",$liste_id));
	foreach ($les_id as $key => $value) {
		$supprimer_rubrique($value);
		supprimer_les_logos("rubrique",$value);
		spip_log("Suppression de la rubrique : $value.",'vider_rubrique');
	}

	return true;
}

function supprimer_les_logos($type,$id_objet){
	supprimer_logo($type,$id_objet);
	supprimer_logo($type,$id_objet,'off');	
}
function supprimer_logo($type,$id_objet,$logo_type='on'){
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$le_logo = $chercher_logo($id_objet, 'id_'.$type, $logo_type);
	$le_logo = $le_logo[0];
	spip_log("Suppression du logo : $le_logo",'vider_rubrique');
	if ( !file_exists($le_logo) ) return false;
	else {
		spip_unlink($le_logo);
	}
}
