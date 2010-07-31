<?php

	$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	'titre_page' => 'Configure the selection of templates',
	'gros_titre' => 'Create rules to choose templates based on keywords',
	'help' => 'This page is only accessible to administrators. You can create here some rules to choose templates by keywords associated to an element in SPIP.

A rule specify:
-# a basic "fond" (the file that is used as template by default for this element),
-# the keyword group that will contain keywords specifying the template to use,
-# the type of element that this template displays.

The templates will then be named {{fond-keyword.html}}. The plugin search first for a template that match a keyword associated to the element. If not found (and if applicable), the plugin then search for a template matching a keyword associated to one of the subsuming section.

The author then only have to add a keyword from the right group to select the template used.',
									   'reglei' => 'rule @id@',
									   'nouvelle_regle' => 'new rule',
									   'fond' => 'Fond:',
									   'groupe' => 'Group:',
									   'type' => 'Type:',
									   'possibilites' => '@total_actif@ template(s) availlable.',
									   'avertissement' => 'The template @squelette@ doesn\'t exist. This rule will not work properly if the default template is not present.'

);
?>
