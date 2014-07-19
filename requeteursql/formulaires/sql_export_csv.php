<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_sql_export_csv_charger_dist($id_sql_requete)
{
	$valeurs = array(
	'delim'=>';'
	);
	include_spip('inc/autoriser');
	if (!autoriser('voir','sqlrequete',$id_sql_requete)){
		$valeurs['editable'] = false;
	}
	return $valeurs;
}


function formulaires_sql_export_csv_verifier_dist($id_sql_requete) {
	$erreurs = array();
	return $erreurs;
}


function formulaires_sql_export_csv_traiter_dist($id_sql_requete) {
	// Lecture de la requête dans la table spip_sql_requetes
	$result = sql_select(array('titre','requetesql'),'spip_sql_requetes',"id_sql_requete = $id_sql_requete");
	if($res = sql_fetch($result)) {
		$delim = _request('delim');
		return array('redirect' => generer_url_ecrire(
			'requeteursql_export_csv',
			"delim=$delim&id_sql_requete=$id_sql_requete")
		);
	}
	else {
		return array('message_erreur' => _T('requeteursql:export_erreur'));
	}
}

?>
