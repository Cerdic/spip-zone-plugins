<?php
// quelques fonctions autour de la gestion de documents

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajoute un document et retourne l'identifiant cree
 *
 * @param mixed $arg ressource de $_FILES
 * @param string $objet objet lie au document insere
 * @param string $id_objet identifiant de l'objet lie au document insere
 * @param string $mode mode d'insertion du document (choix, image, document, vignette...)
 * @param string $id_doc_parent id du document parent en cas de vignette
 */
function ajaxform_creer_document($arg, $objet='', $id_objet='', $mode='choix', $id_doc_parent=0) {
	// pas de document, partir !
	if ($arg['error'] == 4) return false;
	
	// verifier l'extension du fichier en fonction de son type mime
	$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
	list($extension,$arg['name']) = fixer_extension_document($arg);
	return $ajouter_documents($arg['tmp_name'], $arg['name'], 
				objet_type($objet), $id_objet, $mode, $id_doc_parent, $actifs=array());
}

/**
 * Modifie le contenu d'un document en recuperant
 * une liste de champs a modifier dans l'environnement poste
 * 
 * @param int $id_document identifiant du document
 * @param array $champs liste de noms de champs a recuperer
 * 
 * @return null
 */
function ajaxform_modifier_document($id_document, $champs){
		// on recupere chaque champ demande dans request
		$c = array();
		foreach($champs as $n)
			$c[$n] = _request($n);

		include_spip('inc/modifier');
		revision_document($id_document, $c);
}



/** 
 * Tres fortement inspire/copiee des crayons :)
 * pour modifier un fichier de document existant : on en cree un nouveau
 * puis on remplace son contenu sur l'ancien document
 * enfin, on supprime le nouveau.
 *
 * @param int $id identifiant du document remplace
 * @param mixed $upload_file ressource $FILES du fichier qui remplace l'actuel
 * @return true/false
 */
function ajaxform_remplacer_document_par($id, $upload_file, $mode='choix') {
	include_spip('base/abstract_sql');
	if (!$t = sql_fetsel("*","spip_documents","id_document=".sql_quote($id)))
		return false;

	// Chargement d'un nouveau doc ?
	if ($upload_file) {
		$id_new = ajaxform_creer_document($upload_file);

		// on recopie les donnees interessantes dans l'ancien
		if ($id_new
		AND $new = sql_fetsel(array('fichier', 'taille', 'largeur', 'hauteur', 'extension', 'distant'),'spip_documents', 'id_document='.sql_quote($id_new))) {

			// Une vignette doit rester une image
			if ($t['mode'] == 'vignette'
			AND !in_array($new['extension'], array('jpg', 'gif', 'png')))
				return false;

			// Maintenant on est bon, on recopie les nouvelles donnees
			// dans l'ancienne ligne spip_documents
			include_spip('inc/modifier');
			revision_document($id, $new);

			// supprimer l'ancien document (sauf s'il etait distant)
			if ($t['distant'] != 'oui'
			AND file_exists(get_spip_doc($t['fichier'])))
				supprimer_fichier(get_spip_doc($t['fichier']));

			// Effacer la ligne temporaire de spip_document
			sql_delete("spip_documents","id_document=".sql_quote($id_new));

			// oublier id_document temporaire (ca marche chez moi, sinon bof)
			sql_alter("TABLE spip_documents AUTO_INCREMENT=".sql_quote($id_new));

			return true;
		}
	}
}
?>
