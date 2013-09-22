<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/noizetier/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'apercu' => 'Aperçu',

	// B
	'bloc_sans_noisette' => 'Ce bloc ne contient pas de noisette.',

	// C
	'choisir_noisette' => 'Choisissez la noisette que vous voulez ajouter :',
	'compositions_non_installe' => '<b>Plugin compositions :</b> ce plugin n’est pas installé sur votre site. Il n’est pas nécessaire au fonctionnement du noizetier. Cependant, s’il est activé, vous pourrez déclarer des compositions directement dans le noizetier.',

	// D
	'description_bloc_contenu' => 'Contenu principal de chaque page.',
	'description_bloc_extra' => 'Informations extra contextuelles pour chaque page.',
	'description_bloc_navigation' => 'Informations de navigation propres à chaque page.',
	'description_bloctexte' => 'Le titre est optionnel. Pour le texte, vous pouvez utiliser les raccourcis typographiques de SPIP.',

	// E
	'editer_composition' => 'Modifier cette composition',
	'editer_composition_heritages' => 'Définir les héritages',
	'editer_configurer_page' => 'Configurer les noisettes de cette page',
	'editer_exporter_configuration' => 'Exporter la configuration',
	'editer_importer_configuration' => 'Importer une config.',
	'editer_noizetier_explication' => 'Sélectionnez la page dont vous souhaitez configurer les noisettes.',
	'editer_noizetier_titre' => 'Gérer les noisettes',
	'editer_nouvelle_page' => 'Créer une nouvelle page / composition',
	'erreur_aucune_noisette_selectionnee' => 'Vous devez sélectionner une noisette !',
	'erreur_doit_choisir_noisette' => 'Vous devez choisir une noisette.',
	'erreur_mise_a_jour' => 'Une erreur s’est produite pendant la mise à jour de la base de donnée.',
	'explication_glisser_deposer' => 'Vous pouvez ajouter une noisette ou les réordonner par simple glisser/déposer.',
	'explication_heritages_composition' => 'Vous pouvez définir ici les compositions qui seront héritées par les objets de la branche.',
	'explication_noizetier_css' => 'Vous pouvez ajouter à la noisette d’éventuelles classes CSS supplémentaires.',
	'explication_raccourcis_typo' => 'Vous pouvez utiliser les raccourcis typographiques de SPIP.',

	// F
	'formulaire_ajouter_noisette' => 'Ajouter une noisette',
	'formulaire_composition' => 'Identifiant de composition',
	'formulaire_composition_explication' => 'Indiquez un mot-clé unique (minuscules, sans espace, sans tiret et sans accent) permettant d’identifier cette composition.<br />Par exemple : <i>macompo</i>.',
	'formulaire_composition_mise_a_jour' => 'Composition mise à jour',
	'formulaire_configurer_bloc' => 'Configurer le bloc :',
	'formulaire_configurer_page' => 'Configurer la page :',
	'formulaire_deplacer_bas' => 'Déplacer vers le bas',
	'formulaire_deplacer_haut' => 'Déplacer vers le haut',
	'formulaire_description' => 'Description',
	'formulaire_description_explication' => 'Vous pouvez utilisez les raccourcis SPIP usuels, notamment la balise  &lt;multi&gt;.',
	'formulaire_erreur_format_identifiant' => 'L’identifiant ne peut contenir que des minuscules sans accent, des chiffres et le caractère _ (underscore).',
	'formulaire_icon' => 'Icône',
	'formulaire_icon_explication' => 'Vous pouvez saisir le chemin relatif vers une icône (par exemple : <i>images/objet-liste-contenus.png</i>).',
	'formulaire_identifiant_deja_pris' => 'Cet identifiant est déjà utilisé !',
	'formulaire_import_compos' => 'Importer les compositions du noizetier',
	'formulaire_import_fusion' => 'Fusionner avec la configuration actuelle',
	'formulaire_import_remplacer' => 'Remplacer la configuration actuelle',
	'formulaire_liste_compos_config' => 'Ce fichier de configuration définis les compositions du noizetier suivantes :',
	'formulaire_liste_pages_config' => 'Ce fichier de configuration définis des noisettes sur les pages suivantes :',
	'formulaire_modifier_composition' => 'Modifier cette composition :',
	'formulaire_modifier_composition_heritages' => 'Modifier les héritages',
	'formulaire_modifier_noisette' => 'Modifier cette noisette',
	'formulaire_modifier_page' => 'Modifier cette page',
	'formulaire_noisette_sans_parametre' => 'Cette noisette ne propose pas de paramètre.',
	'formulaire_nom' => 'Titre',
	'formulaire_nom_explication' => 'Vous pouvez utilisez la balise  &lt;multi&gt;.',
	'formulaire_nouvelle_composition' => 'Nouvelle composition',
	'formulaire_obligatoire' => 'Champs obligatoire',
	'formulaire_supprimer_noisette' => 'Supprimer cette noisette',
	'formulaire_supprimer_noisettes_page' => 'Supprimer les noisettes de cette page',
	'formulaire_supprimer_page' => 'Supprimer cette page',
	'formulaire_type' => 'Type de page',
	'formulaire_type_explication' => 'Indiquez sur quel objet porte cette composition ou si vous souhaitez créer une page autonome.',
	'formulaire_type_import' => 'Type d’importation',
	'formulaire_type_import_explication' => 'Vous pouvez fusionner le fichier de configuration avec votre configuration actuelle (les noisettes de chaque page seront ajoutées à vos noisettes déjà définies) ou bien remplacer votre configuration par celle-ci.',

	// I
	'icone_introuvable' => 'Icône introuvable !',
	'ieconfig_ne_pas_importer' => 'Ne pas importer',
	'ieconfig_noizetier_export_explication' => 'Exportera la configuration des noisettes et les compositions du noiZetier.',
	'ieconfig_noizetier_export_option' => 'Inclure dans l’export ?',
	'ieconfig_non_installe' => '<b>Plugin Importeur/Exporteur de configurations :</b> ce plugin n’est pas installé sur votre site. Il n’est pas nécessaire au fonctionnement du noizetier. Cependant, s’il est activé, vous pourrez exporter et importer des configurations de noisettes dans le noizetier.',
	'ieconfig_probleme_import_config' => 'Un problème a été rencontré lors de l’importation de la configuration du noiZetier.',
	'info_composition' => 'COMPOSITION :',
	'info_page' => 'PAGE :',
	'installation_tables' => 'Tables du plugin noiZetier installées.<br />',
	'item_titre_perso' => 'titre personnalisé',

	// L
	'label_afficher_titre_noisette' => 'Afficher un titre de noisettes ?',
	'label_niveau_titre' => 'Niveau du titre :',
	'label_noizetier_css' => 'Classes CSS :',
	'label_texte' => 'Texte :',
	'label_texte_introductif' => 'Texte introductif (optionnel) :',
	'label_titre' => 'Titre :',
	'label_titre_noisette' => 'Titre de la noisette :',
	'label_titre_noisette_perso' => 'Titre personnalisé :',
	'liste_icones' => 'Liste d’icônes',
	'liste_pages' => 'Liste des pages',

	// M
	'masquer' => 'Masquer',
	'mode_noisettes' => 'Éditer les noisettes',
	'modif_en_cours' => 'Modifications en cours',
	'modifier_dans_prive' => 'Modifier dans l’espace privé',

	// N
	'ne_pas_definir_d_heritage' => 'Ne pas définir d’héritage',
	'noisette_numero' => 'noisette numéro :',
	'noisettes_composition' => 'Noisettes spécifiques à la composition <i>@composition@</i> :',
	'noisettes_disponibles' => 'Noisettes disponibles',
	'noisettes_page' => 'Noisettes spécifiques à la page <i>@type@</i> :',
	'noisettes_toutes_pages' => 'Noisettes communes à toutes les pages :',
	'noizetier' => 'noiZetier',
	'nom_bloc_contenu' => 'Contenu',
	'nom_bloc_extra' => 'Extra',
	'nom_bloc_navigation' => 'Navigation',
	'nom_bloctexte' => 'Bloc de texte libre',
	'non' => 'Non',
	'notice_enregistrer_rang' => 'Cliquez sur Enregistrer pour sauvegarder l’ordre des noisettes.',

	// O
	'operation_annulee' => 'Opération annulée.',
	'oui' => 'Oui',

	// P
	'page' => 'Page',
	'page_autonome' => 'Page autonome',
	'probleme_droits' => 'Vous n’avez pas les droits nécessaires pour effectuer cette modification.',

	// Q
	'quitter_mode_noisettes' => 'Quitter l’édition des noisettes',

	// R
	'retour' => 'Retour',

	// S
	'suggestions' => 'Suggestions',

	// W
	'warning_noisette_plus_disponible' => 'ATTENTION : cette noisette n’est plus disponible.',
	'warning_noisette_plus_disponible_details' => 'Le squelette de cette noisette (<i>@squelette@</i>) n’est plus accessible. Il se peut qu’il s’agisse d’une noisette nécessitant un plugin que vous avez désactivé ou désinstallé.'
);

?>
