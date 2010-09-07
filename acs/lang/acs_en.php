<?php // This is a SPIP language file. Specific to ACS plugin

require_once _DIR_ACS.'lib/composant/composants_ajouter_langue.php';

// traductions génériques utilisées dans la partie privée ET par les pinceaux (crayons des composants)
$traductions_acs = array(
  'use' => 'Use',

	'fond' => 'Background',

  'bordcolor' => 'Border',
  'bordlargeur' => 'border width',
  'bordstyle' => 'border style',
  'parent' => 'default value',
  'none' => 'no border. Equivalent to border-width: 0',
  'solid' => 'solid',
  'dashed' => 'dashed',
  'dotted' => 'dotted',
  'double' => 'double',
  'groove' => 'groove',
  'ridge' => 'ridge',
  'inset' => 'inset',
  'outset' => 'outset',

  'align' => 'Alignment',
	'valign' => 'vertical alignment',
	'margin' => 'Margin',
	'left' => 'left',
	'center' => 'center',
	'right' => 'right',
	'top' => 'top',
	'bottom' => 'bottom',

	'font' => 'Font(s)',
	'fontsize' => 'Size',
	'fontfamily' => 'Font family'
);

// Lang file is build with components public lang files
if (_DIR_RESTREINT != '') {
  // Add components lang files (public)
  $GLOBALS[$GLOBALS['idx_lang']] = array( // public area
  // L'upload direct depuis l'espace ecrire de spip étant interdit, cette traduction se retrouve ici
  'effacer_image' => 'DEFINTIVELY delete these image from server ???',
  'impossible_ouvrir_dossier' => 'Unable to open directory',
  'err_del_file' => 'Unable to delete file',
  );
  composants_ajouter_langue();
  if (_request('action') == 'crayons_html') { // Add translations for crayons plugin
    $GLOBALS[$GLOBALS['idx_lang']] = array_merge($traductions_acs, $GLOBALS[$GLOBALS['idx_lang']]);
    composants_ajouter_langue('ecrire');
  }
}
else {
  $GLOBALS[$GLOBALS['idx_lang']] = array( // Area ecrire

  'configurer_site' => 'Configure website',
  'documentation' => 'Documentation',

  'assistant_configuration_squelettes' => 'Site configuration wizard',
  'acs' => 'ACS',

  'model_actif' => 'ACS active model: <b>@model@</b>',
  'overriden_by' => ', overriden by skeletons from <u>@over@</u>',
  'model_actif2' => '.',

  'onglet_pages_description' => 'Schema and source.',
  'onglet_pages_info' => 'In pages list, underlined ones are read in the <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: underline">override</span> directory, bolded in current <span style="color: darkgreen; font-weight: bold; font-style: normal; text-decoration: none">ACS model</span>, italicized from <span style="color: darkgreen; font-weight: normal; font-style: underline; text-decoration: none">plugins</span>, and thoses without any font decoration from <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: none">spip distribution</span>.',
  'onglet_pages_help' => 'ACS add to spip customizables pages made with customizables components.<br /><br />Click on the little black triangle to display a more detailed schema.
<br /><br />
<b>Source</b> display colorized source code of the page.
<br /><br />
To setup your website, customize components.<br/>A new instance of a component is created the first time you click on this component from the page which contain it.<br /><br />These page display customizable ACS plugin elements: pages, components, spip models and forms available in active ACS model, and default pages from spip distribution and installed plugins (even not used pages).<br /><br />ACS model elements may be eventually overriden by equivalents from skeleton directory optionnaly defined (Administration pane).',

  'page' => 'Page',
  'pages' => 'Pages',
  'page_rien_a_signaler' => 'No variables, no loops, no inclusions.',
  'source_page' => 'Source code',
  'source' => 'Source',
  'schema' => 'Schema',

  'variable' => 'Variable',
  'variables' => 'Variables',
  'boucle' => 'Loop',
  'toutes_les_variables' => 'All variables',
  'undefined' => 'Not defined',
  'composant' => 'Component',
  'composants' => 'Components',
  'creer_composant' => 'Create a new instance of these component',
  'del_composant' => 'Delete these instance',
  'container' => 'Container',
  'containers' => 'Containers',
  'modele' => 'Model',
  'modeles' => 'Models',
  'formulaire' => 'Form',
  'formulaires' => 'Forms',
  'adm' => 'Administration',
  'public' => 'Public',
  'ecrire' => 'Ecrire',
  'includes' => 'Inclut',
  'structure_page' => 'page structure',
  'err_fichier_absent' => 'File @file@ not found',
  'err_fichier_ecrire' => 'Unable to write file &quot;@file@&quot;',
  'err_cache' => 'Unable to read or write ACS cache',

  'onglet_adm_description' => 'Configuration',
  'onglet_adm_info' => 'ACS model choice and administration.',
  'onglet_adm_help' => '<b>Model</b>:<br />Model is a set of ACS components-based spip skeletons (templates). Skeleton(s) is optionnal. It override the model and/or its components. If more than one is needed, separator is ":"  between paths.<br /><br /><b>ACS administrators</b>:<br />ACS administrators only are authorized to configure the website. Configuration pages are no more accessible to other administrators.<br /><br /><b>ACS access control</b>:<br />You can lock access to other spip "ecrire" area pages: creat a new group, look for the exec=dosomething parameter in the page-to-control url, add "something" in the "ACS administrated" list (separated by comma), validate, and add the page-to-control administrator(s).<br /><br /><b>Display all variables</b>:<br /> All variables display created components variables, whenever components are used or not.<br /><br /><b>Display component\'s pages</b>:<br />Display component\'s pages in "Pages" pane.',

  'admins' => 'Administrators',
  'groupes' => 'Groups',
  'lien_retirer_admin' => 'Retire from admins',
  'locked_pages' => 'Protected pages',
  'model' => 'Model',
  'squelette' => 'Skeleton(s)',
  'voir_pages_composants' => 'Display components pages',
  'voir_pages_preview_composants' => 'Components preview pages',
  'voir_onglet_vars' => 'Show Variables pane.',

  'acsDerniereModif' => 'Updated',

  'dev_infos' => 'Developper infos',
  'composant_non_utilise' => 'Unused component.',
  'references_autres_composants' => 'Default values',
  'choix_couleur' => 'Color choice',
  'choix_image' => 'Choose an image',
  'effacer_image' => 'DEFINITIVELY delete this image from server ???',
  'afterUpdate_not_callable' => 'afterUpdate method not found',
  'echec_afterUpdate' => 'afterUpdate failure',
  'err_aucun_composant' => 'No active component for ',
  'spip_trop_ancien' => 'Do not fit with spip < @min@',
  'spip_non_supporte' => 'Not approved yet with spip > @max@',
  );
  $GLOBALS[$GLOBALS['idx_lang']] = array_merge($traductions_acs, $GLOBALS[$GLOBALS['idx_lang']]);  
  composants_ajouter_langue('ecrire');
}
?>
