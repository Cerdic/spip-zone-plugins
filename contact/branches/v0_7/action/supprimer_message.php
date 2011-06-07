<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

/**
 * Suppression d'un message du formulaire de contact
 */
function action_supprimer_message() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_message = $securiser_action();
	/**
	 * Vérifions si nous avons un document
	 */
	if($docs = sql_select('id_document','spip_documents_liens','id_objet='.intval($id_message).' AND objet="message"')){
		include_spip('action/documenter');
		while($id_doc = sql_fetch($docs)){
			supprimer_lien_document($id_doc['id_document'], "message", $id_message);
		}
	}
	sql_delete("spip_messages", "id_message=".sql_quote($id_message));
	sql_delete("spip_auteurs_messages", "id_message=".sql_quote($id_message));
}

?>
