<?php
/**
 * Plugin Portfolio/Gestion des documents
 * Licence GPL (c) 2006-2008 Cedric Morin, romy.tetue.net
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action editer_document
 *
 * @return unknown
 */
function action_editer_document_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// Envoi depuis les boutons "publier/supprimer cette document"
	if (preg_match(',^(\d+)\Wstatut\W(\w+)$,', $arg, $r)) {
		$id_document = $r[1];
		set_request('statut', $r[2]);
		revisions_documents($id_document);
	} 
	// Envoi depuis le formulaire d'edition d'une document existante
	else if ($id_document = intval($arg)) {
		revisions_documents($id_document);
	}
	// Envoi depuis le formulaire de creation d'une document
	else if ($arg == 'oui') {
		$id_document = insert_document();
		if ($id_document) revisions_documents($id_document);
	} 
	// Erreur
	else{
		include_spip('inc/headers');
		redirige_url_ecrire();
	}

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_document', $id_document, '&');
			
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else 
		return array($id_document,'');
}

/**
 * Creer un nouveau document
 *
 * @return unknown
 */
function insert_document() {

	return sql_insertq("spip_documents", array(
		'statut' => 'prop',
		'date' => 'NOW()',
		));
}


/**
 * Enregistre une revision de document.
 * $c est un contenu (par defaut on prend le contenu via _request())
 *
 * @param int $id_document
 * @param array $c
 */
function revisions_documents ($id_document, $c=false) {

	// champs normaux
	if ($c === false) {
		$c = array();
		foreach (array(
			'titre', 'descriptif', 'date', 'taille', 'largeur','hauteur','mode',
			'statut'
		) as $champ)
			if (($a = _request($champ)) !== null)
				$c[$champ] = $a;
	}

	// Si la document est publiee, invalider les caches et demander sa reindexation
	$t = sql_getfetsel("statut", "spip_documents", "id_document=$id_document");
	if ($t == 'publie') {
		$invalideur = "id='id_document/$id_document'";
		$indexation = true;
	}

	include_spip('inc/modifier');
	modifier_contenu('document', $id_document,
		array(
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c);


	// Changer le statut du document ?
	// le statut n'est jamais fixe manuellement mais decoule de celui des objets lies
	if(instituer_document($id_document)) {

		//
		// Post-modifications
		//
	
		// Invalider les caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_document/$id_document'");	
	}

}
/**
 * determiner le statut d'un document : prepa/publie
 * si on trouve un element joint sans champ statut ou avec un statut='publie' alors le doc est publie aussi
 *
 * @param int $id_document
 */
function instituer_document($id_document,$statut=null){
	$statut_ancien = sql_getfetsel("statut", "spip_documents", "id_document=$id_document");
	if ($statut===null){
		$statut = 'prepa';
	
		$trouver_table = charger_fonction('trouver_table','base');
		$res = sql_select('id_objet,objet','spip_documents_liens','id_document='.intval($id_document));
		while($row = sql_fetch($res)){
			$table = table_objet_sql($row['objet']);
			$desc = $trouver_table($table);
			if (!isset($desc['field']['statut'])){
				$statut = 'publie';
				continue;
			}
			$id_table = id_table_objet($row['objet']);
			if (sql_getfetsel('statut',$table,$id_table.'='.intval($row['id_objet']))=='publie'){
				$statut = 'publie';
				continue;
			}
		}
		if ($statut=='publie' AND $statut_ancien=='publie')
			return false;
		if ($statut!='publie' AND $statut_ancien!='publie')
			return false;
	}
	if ($statut!==$statut_ancien){
		sql_updateq('spip_documents',array('statut'=>$statut),'id_document='.intval($id_document));
		return true;
	}
	return false;
}

?>