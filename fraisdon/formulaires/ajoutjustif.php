<?php

include_spip('base/abstract_sql');
include_spip('base/db_mysql.php');
include_spip('inc/utils.php');
include_spip('inc/autoriser');
 
function formulaires_ajoutjustif_charger_dist($id_fraisdon=0){
	$valeurs = array();

	$valeurs['id_fraisdon']= $id_fraisdon;
	$valeurs['_hidden'] = "<input type='hidden' name='id_fraisdon_modif' value='$id_fraisdon' />";

	return $valeurs;
}

function formulaires_ajoutjustif_traiter_dist(){
	$id_fraisdon= _request('id_fraisdon_modif');

	// ajoute un nouveau document a un article
	if (isset($_FILES['justificatif'])) {
		$msg= _T('fraisdon:justif_enreg');
		// supprimer l'ancien document
		$supprimer_document = charger_fonction('supprimer_document','action');
		$result= spip_query("SELECT * FROM spip_documents_liens WHERE objet='fraisdon' and id_objet=$id_fraisdon");
		if (sql_count($result) > 0) {
			$row = spip_fetch_array($result);
			$id_document= $row['id_document'];
			spip_query("DELETE FROM spip_documents_liens WHERE id_document=$id_document");
			$supprimer_document($id_document);
		}

		// importer le nouveau
		$doc = &$_FILES['justificatif'];
		$msg.= " ". $doc['name'];
		$ajouter_documents = charger_fonction('ajouter_documents','inc');
		$files= array();
		$id_document = $ajouter_documents($doc['tmp_name'], $doc['name'], "fraisdon", $id_fraisdon, 'document', 0, $files);
		// modifie le contenu du document cree
		$c = array(
			'titre'=>'Justificatif',
			'descriptif'=>'Note de frais'
		);
		revision_document($id_document, $c);

	} else {
		$msg= _T('fraisdon:justif_absent');
	}

	return array('message_ok'=>$msg);
}

?>
