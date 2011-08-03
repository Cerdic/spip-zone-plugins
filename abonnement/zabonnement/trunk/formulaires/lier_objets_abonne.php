<?php

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_lier_objets_abonne_charger($objet, $source, $id_source, $identifiant){
	return 
		array(
			'objet' => $objet,
			'source' => $source,
			'id_source' => $id_source,
			id_table_objet($source) => $id_source,
			'identifiant' => $identifiant,
			'editable' => autoriser('associer',objet_type($source),$id_source,null,array('cible'=>$objet))
		);
}

function formulaires_lier_objets_abonne_verifier($objet, $source, $id_source, $identifiant){
	// si pas d'id, le selecteur generique n'a pas fonctionne
	// on fait comment alors ??
	if (!_request('pid_objet')) {
		$erreurs['message_erreur'] = _T('abo:pas_de_identifiant');
	}

	return $erreurs;
}

function formulaires_lier_objets_abonne_traiter($objet, $source, $id_source, $identifiant){
	$id_objet = _request('pid_objet');
	// (article,12041,objet_type(auteurs),219)
	spip_log("($source,$id_source,".objet_type($objet).",$id_objet)",'abonnement');
	include_spip('action/editer_contactabonnement');
			$arg=array(
				'id_auteur'=>$id_objet,
				'objet'=>$source,
				'table'=>"spip_".$source."s",
				'ids' => array($id_source), //tjs envoyer un array
				//'prix'=>$abo['prix_unitaire_ht'],
				//'duree'=>3,
				//'periode'=>'jour',
				//'id_commandes_detail'=>$abo['id_commandes_detail'],
				'statut'=>'offert',//puisque espace prive
				);

			editer_contactabonnement($arg);
	
	return array(true,''); // permettre d'editer encore le formulaire
}

?>
