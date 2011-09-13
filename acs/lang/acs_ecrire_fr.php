<?php

// Espace ecrire d'ACS

$GLOBALS[$GLOBALS['idx_lang']] = array(

  'configurer_site' => 'Conception du site',
  'documentation' => 'Documentation',

  'assistant_configuration_squelettes' => 'Assistant Conception du Site',
  'acs' => 'ACS',

  'model_actif' => 'Mod&egrave;le ACS actif: <b>@model@</b>',
  'overriden_by' => ', surcharg&eacute; par les squelettes de <u>@over@</u>',
  'model_actif2' => '.',
  'onglet_pages_info' => 'ACS ajoute à spip des mod&egrave;les de pages personnalisables par assemblage de composants eux-même personnalisables.',

  'pg_help' => 'Le <b>sch&eacute;ma</b> de la page pr&eacute;sente les <span class="col_BOUCLE">boucles spip</span> et les inclusions. Un clic sur le petit triangle noir affiche un <b>sch&eacute;ma d&eacute;taill&eacute;</b>.
<br /><br />
<b>Source</b> affiche le code source coloris&eacute;.
',
  'onglet_pages_help' => 'Les pages soulign&eacute;es sont lues dans le dossier de squelettes en <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: underline">surcharge</span> d\'ACS, les pages du <span style="color: darkgreen; font-weight: bold; font-style: normal; text-decoration: none">mod&egrave;le ACS</span> actif sont en gras, celles des <span style="color: darkgreen; font-weight: normal; font-style: italic; text-decoration: none">plugins</span> en italique, et celles de la <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: none">distribution spip</span> sans d&eacute;coration.
<br /><br />
Ce sont des <i>"noisettes"</i>, c\'est à dire des squelettes de blocs fonctionnels personnalisables à inclure dans les pages du site.
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
  'creer_composant' => 'Cr&eacute;er une nouvelle instance de ce composant',
  'del_composant' => 'Effacer cette instance de composant',
  'del_composant_confirm' => 'Voulez-vous vraiment effacer DEFINITIVEMENT l\'instance @nic@ du composant @c@ ?',
  'used_in' => 'Utilis&eacute; dans',
  'modele' => 'Mod&egrave;le',
  'modeles' => 'Mod&egrave;les',
  'formulaire' => 'Formulaire',
  'formulaires' => 'Formulaires',
  'adm' => 'Administration',
  'public' => 'Public',
  'ecrire' => 'Ecrire',
  'structure_page' => 'Structure de la page',
  'err_fichier_absent' => 'Fichier @file@ introuvable',
  'err_fichier_ecrire' => 'Impossible d\'&eacute;crire dans &quot;@file@&quot;',
  'err_cache' => 'Impossible de lire ou d\'&eacute;crire dans le cache ACS',

  'onglet_adm_description' => 'Configuration d\'ACS',
  'onglet_adm_info' => 'Choix du jeu de composants et de squelettes, gestion des droits, sauvegarder / restaurer.',
  'onglet_adm_help' => '',

  'admins' => 'Administrateurs',
  'admins_help' => 'Seuls les administrateurs ACS sont autoris&eacute;s à configurer le site. Les pages de configuration du site et de certains plugins ne sont plus accessibles aux autres administrateurs.
  <br /><br />
  ACS permet de verrouiller l\'acc&egrave;s à d\'autres pages de l\'espace "ecrire" de spip: Pour celà, créer un nouveau groupe, rep&eacute;rez dans l\'url de la page à contrôler le param&egrave;tre exec=truc, ajoutez "truc" aux pages protégées du groupe (s&eacute;par&eacute;es par des virgules), puis choisssez ses administrateurs.',
  'groupes' => 'Groupes',
  'lien_retirer_admin' => 'Retirer des admins',
  'locked_pages' => 'Pages protégées',
  'model' => 'Mod&egrave;le',
  'model_help' => 'Le mod&egrave;le est un jeu de squelettes Spip basés sur des composants ACS. Squelette(s) est optionnel, et sert à surcharger le mod&egrave;le et/ou ses composants. Pour avoir plusieurs niveaux d\'override, on sépare les chemins par deux points (<b>:</b>).<br />
Pour utiliser des composants ACS dans d\'autres squelettes que ceux du modèles ACS actif, vous devez y indiquer le dossier de vos squelettes pour qu\'ils surchargent ceux du modèle actif.',
  'squelette' => 'Squelette(s)',
  'voir_pages_composants' => 'Pages des composants',
  'voir_pages_composants_help' => 'Affiche les pages des composants dans l\'onglet "Pages" (squelette <i>un_composant</i>.html).',
  'voir_pages_preview_composants' => 'Pages de pr&eacute;visualisation',
  'voir_pages_preview_composants_help' => 'Affiche les pages de pr&eacute;visualisation des compsants dans l\'onglet "Pages" (squelette <i>un_composant</i>_preview.html).',
  'voir_onglet_vars' => 'Onglet Variables',
  'voir_onglet_vars_help' => 'L\'onglet variables affiche toutes les variables de tous les composants instanciés du mod&egrave;le ACS actif, utilis&eacute;s ou non.',
  'preview_background' => 'Fond de pr&eacute;visualisation',
  'preview_background_help' => 'Permet de choisir une couleur de fond différente de la couleur du fond de page pour la prévisualisation des composants.',
  'spip_admin_form_style' => 'Style du formulaire admin de SPIP',
  'spip_admin_form_style_help' => 'Le formulaire admin de SPIP, visible quand le cookie de correspondance est activé, peut parfois g&ecirc;ner. On peut définir ici des propriétés de style css pour le positionner au mieux. Exemple : right: 100px',
  'cache-spip_on' => 'D&eacute;sactiver le cache de SPIP',
  'cache-spip_off' => 'Cache SPIP d&eacute;sactiv&eacute;',
  'cache-spip_help' => 'Le cache de SPIP permet au serveur de répondre rapidement, mais il est souvent n&eacute;c&eacute;ssaire de le désactiver lors du développement du site pour pouvoir voir les changements.',
  'save' => 'Sauvegarder',
  'restore' => 'Restaurer',

  'acsDerniereModif' => 'Mis &agrave; jour le',

  'dev_infos' => 'Infos d&eacute;veloppeur',
  'si_composant_actif' => 'Si le composant est utilis&eacute;',
  'composant_non_utilise' => 'Composant non utilis&eacute;',
  'references_autres_composants' => 'Valeurs par d&eacute;faut',
  'choix_couleur' => 'Choix de couleur',
  'choix_image' => 'Choisir une image',
  'afterUpdate_not_callable' => 'M&eacute;thode afterUpdate introuvable',
  'echec_afterUpdate' => 'Echec afterUpdate',
  'err_aucun_composant' => 'Aucun composant actif pour ',
	'require' => '@class@ <b>@version@</b> n&eacute;cessite'

);
?>