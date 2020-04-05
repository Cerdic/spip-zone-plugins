<?php

/**
 * Suppression du PDF joint
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Suppression du doc PDF depuis l'espace public
 * 1- de la table spip_documents_liens
 * 2- de la table spip_documents si plus lie à aucun objet (c'est à priori toujours le cas ici)
 * 3- remise à 0 du champ  id_document_cv ou id_document_offre
 *
 * @return bool
 */
function action_supprimer_pdf_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode("-", $arg);
	list($id_document, $objet, $id) = $arg;

	$table 		= table_objet_sql($objet);
	$champ_doc 	= 'id_document_'.$objet;
	$cle_table 	= id_table_objet($objet);
	

	include_spip('action/dissocier_document');
	$supp = supprimer_lien_document($id_document, $objet, $id, true);

	// note: si le 4e arg vaut TRUE, le document est également supprimé de la table spip_document si plus lie à aucun objet
	// voir https://code.spip.net/autodoc/tree/plugins-dist/medias/action/dissocier_document.php.html#function_supprimer_lien_document
	
	// si la suppresion c'est bien faite dans spip_documents et spip_documents_liens,
	// on remet à 0 l'id du document dans la table ad-hoc
	$res = sql_updateq($table, array($champ_doc => 0), "$cle_table=".intval($id));

	return true;
	
}