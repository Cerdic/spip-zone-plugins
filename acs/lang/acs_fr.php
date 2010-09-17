<?php // Ceci est un fichier langue de SPIP specifique au plugin ACS

require_once _DIR_ACS.'lib/composant/composants_ajouter_langue.php';

// traductions generiques utilisees dans la partie privee ET par les pinceaux (crayons des composants)
$traductions_acs = array(
  'use' => 'Utiliser',

	'fond' => ' Fond', // Non traduit sans l'espace devant, car identique

  'bordcolor' => 'Bordure',
  'bordlargeur' => 'largeur de bordure',
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

  'align' => 'Alignement',
  'valign' => 'Alignement vertical',
	'margin' => 'Marge',
	'left' => 'gauche',
	'center' => 'centr&eacute;',
	'right' => 'droit',
	'top' => 'haut',
	'bottom' => 'bas',

	'font' => 'Fonte(s)',
	'fontsize' => 'Taille',
	'fontfamily' => 'Famille de fonte',
	'text' => "Texte",
	'link' => "Lien",
	'linkhover' => "Au survol",

	'shadow' => 'Ombre',
  'shadowsize' => 'Taille',
  'shadowblur' => 'Flou',

	'comment' => 'Commentaire'
);

// Lang file is build with components lang files
if (_DIR_RESTREINT != '') {
  // Ajoute les fichiers de langue des composants (partie publique)
  $GLOBALS[$GLOBALS['idx_lang']] = array( // Espace public
  // L'upload direct depuis l'espace ecrire de spip &eacute;tant interdit, cette traduction se retrouve ici
  'effacer_image' => 'Effacer DEFINITIVEMENT cette image du serveur ???',
  'impossible_ouvrir_dossier' => 'Impossible d\'ouvrir le dossier',
  'err_del_file' => 'Impossible d\'effacer le fichier',
  );
  composants_ajouter_langue();
  if (_request('action') == 'crayons_html') { // On ajoute les traductions pour les crayons
    $GLOBALS[$GLOBALS['idx_lang']] = array_merge($traductions_acs, $GLOBALS[$GLOBALS['idx_lang']]);
    composants_ajouter_langue('ecrire');
  }
}
else {
  $GLOBALS[$GLOBALS['idx_lang']] = array( // Espace ecrire

  'configurer_site' => 'Configurer le site',
  'documentation' => 'Documentation',

  'assistant_configuration_squelettes' => 'Assistant Configuration du Site',
  'acs' => 'ACS',

  'model_actif' => 'Mod&egrave;le ACS actif: <b>@model@</b>',
  'overriden_by' => ', surcharg&eacute; par les squelettes de <u>@over@</u>',
  'model_actif2' => '.',

  'onglet_pages_info' => 'Dans la liste des pages, les pages soulign&eacute;es sont lues dans le dossier de squelettes en <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: underline">surcharge</span> d\'ACS, les pages du <span style="color: darkgreen; font-weight: bold; font-style: normal; text-decoration: none">mod&egrave;le ACS</span> sont en gras, celles des <span style="color: darkgreen; font-weight: normal; font-style: italic; text-decoration: none">plugins</span> en italique, et celles de la <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: none">distribution spip</span> sans d&eacute;coration.',
  'onglet_pages_help' => 'ACS ajoute à spip des mod&egrave;les de pages personnalisables par assemblage de composants eux-même personnalisables.
 <br /><br />
 Le <b>sch&eacute;ma</b> de la page pr&eacute;sente les boucles spip et les &eacute;l&eacute;ments inclus. Cliquer sur le petit triangle noir permet d\'afficher un sch&eacute;ma plus d&eacute;taill&eacute;.
<br /><br />
<b>Source</b> affiche le code source coloris&eacute; de la page.
<br /><br />
Pour personnaliser votre site, configurez ses composants.<br />Une nouvelle instance num&eacute;rot&eacute;e d\'un composant est cr&eacute;e la première fois que l\'on clique sur ce composant depuis la page qui le contient.
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
  'err_fichier_ecrire' => 'Impossible d\'&eacute;crire le fichier &quot;@file@&quot;',
  'err_cache' => 'Impossible de lire ou d\'&eacute;crire dans le cache ACS',

  'onglet_adm_description' => 'Configuration d\'ACS',
  'onglet_adm_info' => 'Choix du mod&egrave;le, gestion des droits, ..',
  'onglet_adm_help' => '<b>Mod&egrave;le</b>:<br>Le mod&egrave;le est un jeu de squelettes Spip basés sur des composants ACS. Squelette(s) est optionnel, et sert à surcharger le mod&egrave;le et/ou ses composants. Pour avoir plusieurs niveaux d\'override, on sépare les chemins par deux points (<b>:</b>).<br /><br /><b>Administrateurs ACS</b>:<br />Seuls les administrateurs ACS sont autoris&eacute;s à configurer le site. Les pages de configuration du site et de certains plugins ne sont plus accessibles aux autres administrateurs.<br /><br /><b>Administration avec ACS</b>:<br />ACS permet &eacute;galement de verrouiller séparément l\'acc&egrave;s à d\'autres pages de l\'espace "ecrire" de spip: Pour celà, créer un nouveau groupe, rep&eacute;rez dans l\'url de la page à contrôler le param&egrave;tre exec=truc, ajoutez "truc" aux pages protégées du groupe (s&eacute;par&eacute;es par des virgules), puis choisssez leurs administrateurs.<br /><br /><b>Afficher l\'onglet variables</b>:<br /> La liste des variables affiche toutes les variables de tous les composants instanciés du mod&egrave;le ACS actif, utilis&eacute;s ou non.<br /><br /><b>Afficher les pages des composants</b>:<br />Affiche les pages des composants dans l\'onglet "Pages".',

  'admins' => 'Administrateurs',
  'groupes' => 'Groupes',
  'lien_retirer_admin' => 'Retirer des admins',
  'locked_pages' => 'Pages protégées',
  'model' => 'Mod&egrave;le',
  'squelette' => 'Squelette(s)',
  'voir_pages_composants' => 'Afficher les pages des composants',
  'voir_pages_preview_composants' => 'Pages de pr&eacute;visualisation des composants',
  'voir_onglet_vars' => 'Afficher l\'onglet Variables',

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
  'spip_trop_ancien' => 'Ne fonctionne pas correctement avec spip < @min@',
  'spip_non_supporte' => 'Non valid&eacute; avec spip > @max@',  

  );
  $GLOBALS[$GLOBALS['idx_lang']] = array_merge($traductions_acs, $GLOBALS[$GLOBALS['idx_lang']]);
  composants_ajouter_langue('ecrire');
}
?>