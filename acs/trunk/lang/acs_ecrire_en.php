<?php

// Area ecrire (private)

$GLOBALS[$GLOBALS['idx_lang']] = array(

  'documentation' => 'Documentation',

  'acs' => 'ACS',
	'acs_description' => 'Site design wizard',
	
  'set_actif' => 'ACS active set: <b>@set@</b>',
  'overriden_by' => ', overriden by skeletons from <u>@over@</u>',
  'set_actif2' => '.',

  'onglet_pages_description' => 'Schema and source.',
  'onglet_pages_info' => 'In pages list, underlined ones are read in the <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: underline">override</span> directory, bolded in current <span style="color: darkgreen; font-weight: bold; font-style: normal; text-decoration: none">ACS set</span>, italicized from <span style="color: darkgreen; font-weight: normal; font-style: underline; text-decoration: none">plugins</span>, and thoses without any font decoration from <span style="color: darkgreen; font-weight: normal; font-style: normal; text-decoration: none">spip distribution</span>.',
  'onglet_pages_help' => 'ACS add to spip customizables pages made with customizables components.
<br /><br />Click on the little black triangle to display a more detailed schema.
<br /><br />
<b>Source</b> display colorized source code of the page.
',

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
  'used_in' => 'Used in',
  'modele' => 'Model',
  'modeles' => 'Models',
  'formulaire' => 'Form',
  'formulaires' => 'Forms',
  'adm' => 'Administration',
  'public' => 'Public',
  'ecrire' => 'Ecrire',
  'structure_page' => 'page structure',
  'err_fichier_absent' => 'File @file@ not found',
  'err_fichier_ecrire' => 'Unable to write in &quot;@file@&quot;',
  'err_cache' => 'Unable to read or write ACS cache',

  'onglet_adm_description' => 'Configuration',
  'onglet_adm_info' => 'ACS components set and templates choice, administration, backup / restore.',
  'onglet_adm_help' => '<b>Set</b>:<br />A set of ACS components-based spip skeletons (templates). Skeleton(s) is optionnal. It override the set and/or its components. If more than one is needed, separator is ":"  between paths.
<br />
To use some components from an ACS set in your own SPIP templates, you need to fill in this field with your SPIP templates directory.
<br />
<br />
<b>ACS administrators</b>:<br />ACS administrators only are authorized to configure the website. Configuration pages are no more accessible to other administrators.<br /><br /><b>ACS access control</b>:<br />You can lock access to other spip "ecrire" area pages: creat a new group, look for the exec=dosomething parameter in the page-to-control url, add "something" in the "ACS administrated" list (separated by comma), validate, and add the page-to-control administrator(s).<br /><br /><b>Display all variables</b>:<br /> All variables display created components variables, whenever components are used or not.<br /><br /><b>Display component\'s pages</b>:<br />Display component\'s pages in "Pages" pane.',

  'admins' => 'Administrators',
  'groupes' => 'Groups',
  'lien_retirer_admin' => 'Retire from admins',
  'locked_pages' => 'Protected pages',
  'set' => 'Set',
  'voir_pages_composants' => 'Components pages',
  'voir_pages_preview_composants' => 'Preview pages',
  'voir_onglet_vars' => 'Variables pane.',
  'preview_background' => 'Preview background',
  'spip_admin_form_style' => 'SPIP admin form style',
  'cache-spip_on' => 'Disable SPIP cache',
  'cache-spip_off' => 'SPIP cache disabled',
  'save' => 'Save ACS config',
  'restore' => 'Restore ACS config',
  'restored' => 'Archive @file@ restored.',

  'acsdernieremodif' => 'Updated',

  'dev_infos' => 'Developper infos',
  'composant_non_utilise' => 'Unused component.',
  'references_autres_composants' => 'Default values',
  'choix_couleur' => 'Color choice',
  'choix_image' => 'Choose an image',
  'effacer_image' => 'DEFINITIVELY delete this image from server ???',
  'afterUpdate_not_callable' => 'afterUpdate method not found',
  'echec_afterUpdate' => 'afterUpdate failure',
  'err_aucun_composant' => 'No active component for ',
	'require' => 'require'
);
?>