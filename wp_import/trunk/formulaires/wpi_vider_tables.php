<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_wpi_vider_tables_charger_dist() {

    $valeurs['mes_saisies'] =
        array(
            array(
                'saisie' => 'case',
                'options' => array(
                    'nom' => 'menage',
                    'explication' => _T('wp_import:menage_explication')
                ))
        );
    return $valeurs;
}

function formulaires_wpi_vider_tables_traiter_dist() {

	// si demande de faire le ménage, on commence par ça
	$menage = _request('menage');
	if ($menage) {
		spip_log("Vidage des tables ", "wp_import" . _LOG_INFO_IMPORTANTE);
		
		// vider la table spip_auteurs, sauf l'auteur N°1
		sql_delete('spip_auteurs', 'id_auteur > 1');
		sql_alter('TABLE spip_auteurs AUTO_INCREMENT = 2');
		sql_delete('spip_auteurs_liens');

		// les articles et forums
		sql_query('TRUNCATE `spip_articles`');
		sql_query('TRUNCATE `spip_forum`');

		// Les documents
		sql_query('TRUNCATE `spip_documents`');
		sql_delete('spip_documents_liens');
		
		// Les mots clés
		sql_query('TRUNCATE `spip_groupes_mots`');
		sql_query('TRUNCATE `spip_mots`');
		sql_delete('spip_mots_liens');

		// Les urls
		sql_delete('spip_urls');

		// on ne touche pas à la table rubriques
		
	} else {
		spip_log("Pas de ménage", "wp_import" . _LOG_INFO_IMPORTANTE);
	}
}
