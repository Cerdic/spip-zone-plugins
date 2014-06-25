<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');

function formulaires_migration_evenements_charger_dist(){
	$valeurs = array(
		'id_article_origine'=>'',
		'id_article_cible'=>'',
		);
	var_dump($_POST);//c'est moche mais ça permet du debug
	return $valeurs;
}

 function formulaires_migration_evenements_verifier(){
 	$erreurs = array();
 	if (!_request('id_article_origine') AND !_request('id_article_cible')){
 		$erreurs['message_erreur'] = _T('move_events:erreur_tout');
 	}
 	elseif (!_request('id_article_cible')) {
 		$erreurs['message_erreur'] = _T('move_events:erreur_cible');
 	}
 	elseif (!_request('id_article_origine')) {
 		$erreurs['message_erreur'] = _T('move_events:erreur_origine');
 	}
 	elseif (_request('id_article_origine')==_request('id_article_cible')) {
 		$erreurs['message_erreur'] = _T('move_events:cible_origine_identiques');
 	}
 	return $erreurs;
 }

 function formulaires_migration_evenements_traiter(){

	//on met nos arguments dans des variables plus facles à manipuler
 	//$origine=picker_selected(_request("id_article_origine"),'article'); //peut être quand j'arriverai à le faire avec une saisie selecteur_article
 	$origine=_request("id_article_origine");
 	$cible=_request("id_article_cible");

 	echo $origine."<br>";
 	echo $cible."<br>";


	$where=array('id_article ='.$origine);
	$res = sql_select("titre,id_evenement", "spip_evenements",$where);//Ici on récupère les enregistrements qui vont bien dans un tableau
	$nombre = count($res);// nombre d'événements concernés mais ça marche pas

	echo $nombre;

	while ($row=sql_fetch($res)) {//on parcourt le tableau
		$where_id_evenement=array('id_evenement = '.$row['id_evenement']);//on met dans une variable l'id de l'événemnet que l'on est en train de traiter
		sql_updateq('spip_evenements',array('id_article'=>$cible),$where_id_evenement);// on met à jour l'id article de l'événement
	}

 	return array('message_ok'=>_T('move_events:migration_reussie', array('origine'=>$origine,'nombre'=>$nombre,'cible'=>$cible)));
 }

?>