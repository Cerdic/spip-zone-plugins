<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Specific to ACS plugin - sp&eacute;cifique au plugin ACS
  include_spip('lib/composant/composants_ajouter_langue');

// Lang file is build with components public lang files
if (_DIR_RESTREINT != '') {
  // Ajoute les fichiers de langue des composants (partie publique)
  $GLOBALS[$GLOBALS['idx_lang']] = array( // Espace public
  // L'upload direct depuis l'espace ecrire de spip &eacute;tant interdit, cette traduction se retrouve ici
  'effacer_image' => 'Effacer DEFINITIVEMENT cette image du serveur ???',
  'impossible_ouvrir_dossier' => 'Impossible d\'ouvrir le dossier',
  'err_del_file' => 'Impossible d\'effacer le fichier',
  );
  composants_ajouter_langue();
}
else {
  $GLOBALS[$GLOBALS['idx_lang']] = array( // Espace ecrire

  'configurer_site' => 'Configurer le site',
  'documentation' => 'Documentation',

  'assistant_configuration_squelettes' => 'Assistant de Configuration du Site',
  'acs' => 'ACS',

  'model_actif' => 'Modèle ACS actif: <b>@model@</b>',
  'overriden_by' => ', surcharg&eacute; par les squelettes de <u>@over@</u>',
  'model_actif2' => '.',

  'onglet_pages_info' => 'Configure le graphisme et le comportement du site. ACS ajoute à spip des modèles de pages personnalisables par assemblage de composants eux-même personnalisables.',
  'onglet_pages_help' => 'Dans la liste des pages, les pages soulign&eacute;es sont lues dans le dossier de squelettes en <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: underline">surcharge</span> d\'ACS, les pages du <span style="color: darkgreen; font-weight: bold; font-style: normal; text-decoration: none">modèle ACS</span> sont en gras, celles des <span style="color: darkgreen; font-weight: normal; font-style: italic; text-decoration: none">plugins</span> en italique, et celles de la <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: none">distribution spip</span> sans d&eacute;coration.
 <br /><br />
 Le sch&eacute;ma de la page pr&eacute;sente les boucles spip et les &eacute;l&eacute;ments inclus.
 <br /><br />
 Le petit triangle noir permet d\'afficher un sch&eacute;ma plus d&eacute;taill&eacute;, avec en particulier les paramètres des inclusions et des boucles et les commentaires spip de la page.
<br /><br />
Pour configurer le site, cliquez sur l\'onglet "Composants" et personnalisez les composants, en commençant par "Fond", qui d&eacute;finit les valeurs par d&eacute;faut.<br /><br />La liste des variables affiche les paramètres de tous les composants du modèle ACS actif, utilis&eacute;s ou non.
',

  'page' => 'Page',
  'pages' => 'Pages',
  'page_rien_a_signaler' => 'Ni variables, ni boucles, ni inclusions.',
  'source_page' => 'Code source',
  'source' => 'Source',
  'schema' => 'Sch&eacute;ma',

  'variable' => 'Variable',
  'variables' => 'Variables',
  'boucle' => 'Boucle',
  'toutes_les_variables' => 'Toutes les variables',
  'undefined' => 'Non d&eacute;fini',
  'composant' => 'Composant',
  'composants' => 'Composants',
  'container' => 'Conteneur',
  'containers' => 'Conteneurs',
  'modele' => 'Mod&egrave;le',
  'modeles' => 'Mod&egrave;les',
  'formulaire' => 'Formulaire',
  'formulaires' => 'Formulaires',
  'adm' => 'Administration',
  'public' => 'Public',
  'ecrire' => 'Ecrire',
  'includes' => 'Inclusions',
  'structure_page' => 'Structure de la page',
  'err_fichier_absent' => 'Fichier @file@ introuvable',


  'onglet_adm_description' => 'Choix du modèle et gestion des droits.',
  'onglet_adm_info' => 'Le modèle est un jeu de squelettes Spip basés sur des composants ACS.',
  'onglet_adm_help' => 'Squelette(s) est optionnel, et sert à surcharger le modèle et/ou ses composants. Pour avoir plusieurs niveaux d\'override, on sépare les chemins par deux points (<b>:</b>).<br /><br />Seuls les administrateurs ACS sont autoris&eacute;s à configurer le site. Les pages de configuration du site et de certains plugins ne sont plus accessibles aux autres administrateurs.<br /><br />ACS permet &eacute;galement de verrouiller séparément l\'accès à d\'autres pages de l\'espace "ecrire" de spip: Pour celà, créer un nouveau groupe, rep&eacute;rez dans l\'url de la page à contrôler le paramètre exec=truc, ajoutez "truc" aux pages protégées du groupe (s&eacute;par&eacute;es par des virgules), puis choisssez leurs administrateurs.<br />',

  'admins' => 'Administrateurs',
  'groupes' => 'Groupes',
  'lien_retirer_admin' => 'Retirer des admins',
  'locked_pages' => 'Pages protégées',
  'model' => 'Mod&egrave;le',
  'squelette' => 'Squelette(s)',
  'voir_pages_composants' => 'Afficher les pages des composants',

  'acsDerniereModif' => 'Mis &agrave; jour le',

  'dev_infos' => 'Infos d&eacute;veloppeur',
  'use' => 'Utiliser',
  'si_composant_actif' => 'Si le composant est utilis&eacute;',
  'composant_non_utilise' => 'Composant non utilis&eacute;',
  'references_autres_composants' => 'Valeurs par d&eacute;faut',
  'choix_couleur' => 'Choix de couleur',
  'choix_image' => 'Choisir une image',
  'afterUpdate_not_callable' => 'M&eacute;thode afterUpdate introuvable',
  'echec_afterUpdate' => 'Echec afterUpdate',
  'err_aucun_composant' => 'Aucun composant actif pour ',
  'spip_trop_ancien' => 'Ne fonctionne pas correctement avec spip < @min@',
  'spip_non_supporte' => 'Non testé avec spip > @max@',

  'bordlargeur' => 'largeur de bordure (en pixels)',
  'bordstyle' => 'style de bordure',
  'parent' => 'valeur par d&eacute;faut',
  'none' => 'pas de bordure, &eacute;quivaut à border-width:0',
  'solid' => 'trait plein',
  'dashed' => 'tirets',
  'dotted' => 'pointill&eacute;s',
  'double' => 'double traits pleins',
  'groove' => 'grav&eacute; (inverse de ridge)',
  'ridge' => 'sort de la page (inverse de groove)',
  'inset' => 'incrust&eacute; dans la page (inverse de outset)',
  'outset' => 'extrud&eacute; de la page (inverse de inset)',

/* Page publications */
  'publications' => 'Publications',
  'pub_description' => 'Dernières actions des administrateurs du site.',
  'pub_help' => 'En cas de difficulté avec l\'une de vos publications, envoyez un message à un administrateur.',
  'publie' => 'publie',
  'prop' => 'propose',
  'prepa' => 'met en r&eacute;daction',
  'refuse' => 'refuse',
  'poubelle' => 'met  à la poubelle',
  'modif' => 'modifie',

  'spip_articles' => 'l\'article',
  'spip_rubriques' => 'la rubrique',
  'spip_mots' => 'le mot-cl&eacute;',
  'spip_auteurs' => 'l\'auteur',
  'spip_forum' => 'le message'
// '' => '',


  );
  composants_ajouter_langue('ecrire');
}
?>