<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_nettoyer_document_temporaire_dist($t) {
    spip_log('nettoyage des documents', 'uploadhtml');

    // On séléctionne les documents qui on le statut temporaire
    // ET qui sont vieux de plus de 24h.
    // On ce base sur le champ maj plus fiable
    $documents = sql_allfetsel(
        'id_document',
        'spip_documents',
        array(
            'statut='.sql_quote('tmp'),
            'maj <= DATE_SUB(NOW(), INTERVAL 1 DAY)'
        )
    );
    spip_log($documents, 'uploadhtml');

    // Charger la fonction de suppression de document
    $supprimer_document = charger_fonction('supprimer_document', 'action');

    // Supprimer tout les documents de la liste
    foreach ($documents as $document) {
        $id_document = $document['id_document'];
        autoriser_exception('supprimer', 'document', $id_document, true);
        $supprimer_document($id_document);
        autoriser_exception('supprimer', 'document', $id_document, false);
        spip_log('supprimer le document: '.$id_document, 'uploadhtml');
    }
}
