<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;


$GLOBALS[$GLOBALS['idx_lang']] = array(

  // A
  'article_license' => '<br />Licence de l\'article:',
  'action_syndic' => 'syndiquer manuellement',
  'aucun_article' => 'aucun article &agrave; syndiquer actuellement',
  'avis_echec_syndication' => 'Erreur: Impossible de charger le flux',
  'avis_echec_syndication_01' => 'Erreur: Flux mal formé',

  // B
  'back' => 'retour',

  // C
  'copy_spip2spip' => 'Spip2Spip: Copie SPIP &agrave; SPIP',
  'config_spip2spip' => 'Configurer',
  'cfg_import_statut'=> 'Statut des articles import&eacute;s',
  'cfg_email_alerte'=> 'Pr&eacute;venir par email à chaque nouvelle syndication ?',
  'cfg_email_suivi' => 'Si oui, sur quel email ?',
  'cfg_citer_source' => 'Citer l\'URL de l\'article d\'origine dans l\'article import&eacute;',
  'cfg_creer_thematique_article' => 'Les thématiques du site SPIP2SPIP',
  'cfg_creer_thematique_article_explication' => 'Si la thématique d\'un article est inexistante, que faire ?',
  'cfg_creer_thematique_article_non' => 'Un des administrateurs le créera manuellement.',
  'cfg_creer_thematique_article_oui' => 'Créer automatiquement le mot-clé correspondant.',
  'cfg_export' => 'Export des articles',
  'cfg_export_motcle' => 'Export des mots-clés',
  'cfg_export_mot_article' => 'Exporter les mots-clés des articles',
  'cfg_export_mot_evnt' => 'Exporter les mots-clés des événements',
  'cfg_export_mot_groupe' => 'Choisissez les groupes dont vous voulez exporter les mots-clés',
  'cfg_import' => 'Import des articles',
  'cfg_import_date_article' => 'Quelle date pour les articles importés ?',
  'cfg_import_date_article_oui' => 'Date de publication de l\'article original',
  'cfg_import_date_article_non' => 'Date de la syndication',
  'cfg_import_motcle' => 'Import des mots-clés',
  'cfg_import_mot_article' => 'Importer les mots-clés des articles',
  'cfg_import_mot_evnt' => 'Importer les mots-clés des événements',
  'cfg_import_mot_groupe_creer' => 'Si oui, où placer les mots-clés importés ?',
  'cfg_import_mot_groupe_creer_oui' => 'Récréer les groupes des mots-clés d\'origine',
  'cfg_import_mot_groupe_creer_non' => 'Placer les mots-clés dans le groupe',
  'cfg_intervalle_cron' => 'Intervalle de passage du CRON (en minutes)',
  'cfg_titre_parametrages' => 'Options d\'import et d\'export des articles d\'un réseau Spip2spip',

  // E
  'erreur_obligatoire' => 'Champ obligatoire',
  'erreur_flux_inconnu' => 'Impossible de charger ce flux',

  // F
  'form_s2s_1' => 'Titre du site',
  'form_s2s_2' => 'URL du fil au format SPIP2SPIP',
  'form_s2s_3' => 'Ajouter ce site',

  // H
  'how_to' => 'Pensez &agrave; bien attribuer les mots-cl&eacute;s du groupe - spip2spip - [<a href=\'?exec=mots\'>Voir le groupe</a>]<br /><br />
  - attribuez les mots cl&eacute;s de ce groupe aux <strong>articles</strong> dont vous voulez envoyer le contenu vers les autres spip2spip<br /><br />
  - attribuez les mots cl&eacute;s de ce groupe aux <strong>rubriques</strong> dans lequelles vous voulez importer les articles spip2spip li&eacute;s &agrave; une th&eacute;matique donn&eacute;e.',

  // I
  'icone_creer_spip2spip' => 'Ajouter un site spip2spip',
  'icone_modifier_spip2spip' => 'Modifier ce site spip2spip',
  'intro_spip2spip' => 'Permet de recopier automatiquement des articles d\'un SPIP &agrave; l\'autre.',
  'install_spip2spip' => 'Installation des tables de spip2spip',
  'install_spip2spip_1' => 'Cr&eacute;ation de la table SQL',
  'install_spip2spip_2' => 'Ajout des flux backends',
  'install_spip2spip_groupe_mot' => 'Cr&eacute;ation du groupe de mots cl&eacute;s - spip2spip -',
  'install_spip2spip_4' => 'groupe spip2spip pour d&eacute;signer les articles et rubriques a synchroniser.',
  'install_spip2spip_5' => '{{mode d&#039;emploi:}}
  -* attribuez les mots cl&eacute;s de ce groupe aux {{articles}} que vous voulez envoyer vers les sites utilisant spip2spip.
  -* attribuez les mots cl&eacute;s de ce groupe aux {{rubriques}} dans lequelles vous voulez importer les articles spip2spip sur cette th&eacute;matique',
  'install_spip2spip_99' => '<p>Installation de SPIP2SPIP compl&egrave;te !</p><a href=\'?exec=spip2spip\'>Retourner sur l\'interface principale de SPIP2SPIP</a>',
  'imported_already' => 'Article d&eacute;j&agrave; import&eacute;',
  'imported_new' => 'Nouvel article',
  'imported_update' => 'Article mis &agrave; jour  ',
  'event_ok' => 'Ajout d\'un &eacute;v&eacute;nement ',
  'imported_view' => 'Consulter l\'article import&eacute;',
  'installed' => 'spip2spip est install&eacute;. cette page ne sert plus &agrave; rien</p>',
  'info_aucun_spip2spip' => 'Aucun site actuellement enregistré',
  'icone_creer_spip2spipicone_creer_spip2spip' => 'Ajouter un nouveau site Spip2spip',

  // L
  'label_site_titre' => 'Nom du site',
  'label_site_rss' => 'Adresse du Flux Spip2spip',
  'label_maj' => 'Dernière synchronisation',
  'label_log' => 'Rapport de syndication',
  'label_thematique' => 'Thématique',

  // M
  'maj' => 'Derni&egrave;re syndication',

  // N
  'no_target' => 'aucune rubrique li&eacute;e &agrave; ce mot cl&eacute;',
  'not_installed' => 'spip2spip n\'est pas encore install&eacute;.<p><a href=\'?exec=spip2spip_install\'>installer spip2spip</a></p>',

  // O
  'origin_url' => 'L\'adresse originale de cet article est',

  // R
  'retour_spip2spip' => 'Retour',


  // T
  'titre' => 'Spip2Spip',
  'titre_spip2spip' => 'Site',
  'titre_spip2spips' => 'Sites Spip2Spip',
  'titre_page_configurer_spip2spip' => 'Spip2Spip',
  'titre_mail' => 'Syndication automatique SPIP2SPIP',
  'titre_logo_spip2spip' => 'Logo du site SPIP2SPIP',

  // V
  'voir_thematique' => 'Voir le mot-clé',

);

?>