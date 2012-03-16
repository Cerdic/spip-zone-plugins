<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/iextras?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_associer' => 'manage this field',
	'action_associer_title' => 'Manage the display of this extra field',
	'action_desassocier' => 'disassociate',
	'action_desassocier_title' => 'Don\'t manage the display of this extra field',
	'action_descendre' => 'down',
	'action_descendre_title' => 'Move the field down one position lower',
	'action_modifier' => 'modify',
	'action_modifier_title' => 'Modify the parameters of the extra field',
	'action_monter' => 'up',
	'action_monter_title' => 'Move the field up one position higher',
	'action_supprimer' => 'delete',
	'action_supprimer_title' => 'Totally delete the field from the database',

	// C
	'caracteres_autorises_champ' => 'Possible characters: letters without accents, numerals, - and _',
	'caracteres_interdits' => 'Some characters used are inappropriate for this field.',
	'champ_deja_existant' => 'A field with the same name already exists for this table.',
	'champ_sauvegarde' => 'Extra field saved!',
	'champs_extras' => 'Extra Fields',
	'champs_extras_de' => 'Champs Extras de : @objet@', # NEW

	// E
	'erreur_action' => 'Action @action@ unknown.',
	'erreur_enregistrement_champ' => 'Problem creating the extra field.',

	// I
	'icone_creer_champ_extra' => 'Create a new extra field',
	'info_description_champ_extra' => 'This page is used to manage the extra fields, 
						these being supplementary fields added to SPIP\'s default database tables,
						taken into account in the object entry and modification forms.',
	'info_description_champ_extra_creer' => 'You can create new fields which will then be displayed on this page
						under the heading of "List of extra fields", as well as in the forms.',
	'info_description_champ_extra_presents' => 'Finally, if there are already extra fields in your database,
						but which have not been declared (by a plugin or set of templates), then you
						can ask this plugin to manage them for you. These fields, if there are any,
						will appear under the heading of "List of existing fields not managed".',
	'info_modifier_champ_extra' => 'Modify an extra field',
	'info_nouveau_champ_extra' => 'New extra field',
	'info_saisie' => 'Saisie :', # NEW

	// L
	'label_attention' => 'Very important help',
	'label_champ' => 'Field name',
	'label_class' => 'CSS classes',
	'label_datas' => 'Liste de valeurs', # NEW
	'label_explication' => 'Data entry help',
	'label_label' => 'Data entry label',
	'label_li_class' => 'CSS classes of the &lt;li&gt; parent',
	'label_obligatoire' => 'Compulsory field?',
	'label_rechercher' => 'Search',
	'label_rechercher_ponderation' => 'Pondération de la recherche', # NEW
	'label_restrictions_auteur' => 'Par auteur', # NEW
	'label_restrictions_branches' => 'Par branche', # NEW
	'label_restrictions_groupes' => 'Par groupe', # NEW
	'label_restrictions_secteurs' => 'Par secteur', # NEW
	'label_saisie' => 'Type de saisie', # NEW
	'label_sql' => 'SQL definition',
	'label_table' => 'Object',
	'label_traitements' => 'Automatic processes',
	'legend_declaration' => 'Declaration',
	'legend_options_saisies' => 'Data entry options',
	'legend_options_techniques' => 'Technical options',
	'legend_restriction' => 'Restriction', # NEW
	'legend_restrictions_modifier' => 'Modifier la saisie', # NEW
	'legend_restrictions_voir' => 'Voir la saisie', # NEW
	'liste_des_extras' => 'List of extra fields',
	'liste_des_extras_possibles' => 'List of existing fields not managed',
	'liste_objets_applicables' => 'Liste des objets éditoriaux', # NEW

	// N
	'nb_element' => '1 élément', # NEW
	'nb_elements' => '@nb@ éléments', # NEW

	// P
	'precisions_pour_attention' => 'To be used for VERY important details.
		To be used with moderation!
		May be a "plugin:stringname" idiom..',
	'precisions_pour_class' => 'Add CSS classes for the element,
		separated by a space. Example: "inserer_barre_edition" for a block
		with the Porte Plume plugin',
	'precisions_pour_datas' => 'Certains types de champ demandent une liste des valeurs acceptées : indiquez-en une par ligne, suivie d\'une virgule et d\'une description. Une ligne vide pour la valeur par défaut. La description peut être une chaîne de langue.', # NEW
	'precisions_pour_explication' => 'You can provide more information about the data field. 
		May be a "plugin:stringname" idiom..',
	'precisions_pour_label' => 'May be a "plugin:stringname" idiom.',
	'precisions_pour_li_class' => 'Add CSS classes for the &lt;li&gt; parent,
		separated by a space. Example: "haut" to use the whole width
		of the form',
	'precisions_pour_nouvelle_saisie' => 'Permet de changer le type de saisie utilisée pour ce champ', # NEW
	'precisions_pour_nouvelle_saisie_attention' => 'Attention cependant, un changement de type de saisie perd les options de configuration de la saisie actuelle qui ne sont pas communes avec la nouvelle saisie sélectionnée !', # NEW
	'precisions_pour_rechercher' => 'Include this field in the search engine?',
	'precisions_pour_rechercher_ponderation' => 'SPIP pondère une recherche dans une colonne par un coefficient de ponderation.
		Celui-ci permet de mettre en avant les colonnes les plus pertinentes (titre par exemple) par rapport à d\'autres qui le sont moins.
		Le coefficient appliqué sur les champs extras est par défaut 2. Pour vous donner un ordre d\'idée, notez que SPIP utilise 8 pour le titre, 1 pour le texte.', # NEW
	'precisions_pour_restrictions_branches' => 'Identifiants de branches à restreindre (séparateur «:»)', # NEW
	'precisions_pour_restrictions_groupes' => 'Identifiants de groupes à restreindre (séparateur «:»)', # NEW
	'precisions_pour_restrictions_secteurs' => 'Identifiants de secteurs à restreindre (séparateur «:»)', # NEW
	'precisions_pour_saisie' => 'Afficher une saisie de type :', # NEW
	'precisions_pour_traitements' => 'Automatically apply a process
		for the resulting #FIELD_NAME field:',

	// R
	'radio_restrictions_auteur_admin' => 'Seulement les administrateurs', # NEW
	'radio_restrictions_auteur_aucune' => 'Tout le monde peut', # NEW
	'radio_restrictions_auteur_webmestre' => 'Seulement les webmestres', # NEW
	'radio_traitements_aucun' => 'None',
	'radio_traitements_raccourcis' => 'SPIP shortcut processes (clean)',
	'radio_traitements_typo' => 'Only typographical processes (typo)',

	// S
	'saisies_champs_extras' => 'From "Extra Fields"',
	'saisies_saisies' => 'From "Saisies"',
	'supprimer_reelement' => 'Delete this field?',

	// T
	'titre_iextras' => 'Extras Fields',
	'titre_page_iextras' => 'Extra Fields',

	// V
	'veuillez_renseigner_ce_champ' => 'Please enter this field!'
);

?>
