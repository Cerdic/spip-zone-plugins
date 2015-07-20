<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

    // A
    'activation_horaires_non' => 'Régulièrement dans la journée <em>(peut ralentir votre site)</em>',
    'activation_horaires_oui' => 'Tranche horaire <strong>(recommandé)</strong>',
    'aucun_document_orphelin' => 'Il n\'y a aucun document orphelin.',
    'aucun_logo' => 'Il n\'y a aucun logo.',
    'aucun_repertoire' => 'Il n\'y a aucun répertoire.',
    'actions' => 'Actions',

    // C
    'cfg_titre_parametrages' => 'Paramétrages',

    // D
    'des_documents' => '@nb@ documents',
    'des_repertoires' => '@nb@ répertoires',

    // E
    'en_bdd' => 'En BDD',
    'explication_activation_horaires' => 'Désirez-vous que le nettoyage se fasse à tout moment de la
    journée ? Ou juste dans une tranche horaire pour ne pas pénaliser les performances du site ?',
    'explication_horaires' => 'Veuillez sélectionner la tranche horaire dans laquel le nettoyage de la
    médiathèque pourra se faire. <em>(Par défaut entre 00h00 et 06h00)</em>',

    // H
    'horaire_00_06' => '00h00 - 06h00',
    'horaire_06_12' => '06h00 - 12h00',
    'horaire_12_18' => '12h00 - 18h00',
    'horaire_18_24' => '18h00 - 24h00',

    // I
    'info_nom' => 'Nom',
    'info_nombre' => 'Nombre',
    'info_nombre_abbr' => 'Nb',
    'info_taille' => 'Taille',
    'info_technique' => 'Infos techniques',
    'info_nombre_documents_bdd' => 'Documents non-distants',
    'info_nombre_documents_bdd_orphelins' => 'Documents non-distants sans fichiers physiques',
    'info_nombre_documents_bdd_complet' => 'Tous les documents',
    'info_nombre_documents_repertoire' => 'Fichiers dans les répertoires d\'extensions',
    'info_nombre_documents_repertoire_complet' => 'Tous les fichiers',
    'info_nombre_documents_repertoire_orphelins' => 'Fichiers orphelins avec extensions (sauf logos)',
    'info_nombre_logos_fichiers' => 'Logos des objets de SPIP',
    'info_nombre_logos_fichiers_off' => 'Logos en mode "off"',
    'info_nombre_logos_fichiers_on' => 'Logos en mode "on"',
    'info_nombre_repertoires_racine' => 'Répertoires à la racine',
    'info_titre_bdd' => 'En base de données',
    'info_titre_logos_img' => 'Les logos dans IMG/',
    'info_titre_repertoire_img' => 'Répertoire IMG/',
    'info_titre_repertoire_orphelins' => 'Répertoire IMG/orphelins',

    // L
    'label_activation_horaires' => 'Activer le nettoyage par tranche horaires',
    'label_horaires' => 'Tranches horaires',

    // M
    'medias_nettoyage_nom' => 'Nettoyer la médiathèque',
    'message_log_supprimer_orphelins' => '@date@ : le fichier @fichier@ a été supprimé par l\'auteur \'@auteur@\'
    (@id_auteur@).',
    'message_log_tranche_actif_horaire_undefined' => '@date@ : Le CRON par tranche horaire est actif mais non défini.
    On lance le script @fonction@',
    'message_log_tranche_actif_horaire_defini' => '@date@ : Le CRON par tranche horaire est actif et défini.
    On lance le script @fonction@ entre @debut@ et @fin@',
    'message_log_tranche_defaut' => '@date@ : On est dans la tranche horaire par défaut.
    On lance le script @fonction@',
    'message_log_tranche_desactivee' => '@date@ : Le CRON par tranche horaire est désactivé.
    On lance le script @fonction@ toutes les 5h.',
    'mode_off' => 'Mode \'off\'',
    'mode_on' => 'Mode \'on\'',
    'mode_tous' => 'Tous',

    // N
    'non' => 'Non',

    // O
    'onglet_titre_logos_img' => 'Les logos dans IMG/',
    'onglet_titre_rep_orphelins' => 'Documents orphelins',
    'onglet_titre_rep_img' => 'Répertoire IMG/',
    'onglet_titre_tabbord' => 'Tableau de bord',
    'oui' => 'Oui',

    // S
    'supprimer' => 'Supprimer',
    // T
    'titre_configurer_medias_nettoyage' => 'Configurer le nettoyage de la médiathèque',
    'titre_page_configurer_medias_nettoyage' => 'Configurer le nettoyage de la médiathèque',
    'titre_page_medias_logos_img' => 'Les logos dans IMG/',
    'titre_page_medias_rep_orphelins' => 'Documents dans le répertoire "orphelins"',
    'titre_page_medias_rep_img' => 'Liste des répertoires dans IMG/',
    'titre_page_medias_tabbord' => 'Tableau de bord - Documents',
    'titre_tableau_documents_orphelins' => 'Documents orphelins',
    'titre_tableau_documents_repertoire_orphelins' => 'Contenu dans IMG/orphelins',

    // U
    'un_document' => 'Un document',
    'un_repertoire' => 'Un répertoire',
);
?>