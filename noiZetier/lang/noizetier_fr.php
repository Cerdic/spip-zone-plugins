<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'choisir_noisette' => 'Choisissez la noisette que vous voulez ajouter :',
	'compositions_non_installe' => '<b>Plugin compositions :</b> ce plugin n\'est pas installés sur votre site. Il n\'est pas nécessaire au fonctionnement du noizetier. Cependant, s\'il est activé, vous pourrez déclarer des compositions directement dans le noizetier.',

	// D
	'description_bloc_contenu' => 'Contenu principal de chaque page.',
	'description_bloc_navigation' => 'Informations de navigation propres à chaque page.',
	'description_bloc_extra' => 'Informations extra contextuelles pour chaque page.',
	
	'description_bloctexte' => 'Le titre est optionnel. Pour le texte, vous pouvez utiliser les raccourcis typographiques de SPIP.',

	// E
	'editer_noizetier_titre' => 'noiZetier',
	'editer_noizetier_explication' => 'Configurer ici les noisettes à ajouter aux pages de votre site.',
	'editer_noizetier_compositions_titre' => 'Compositions du noiZetier',
	'editer_noizetier_compositions_explication' => 'Vous pouvez créer ici des compositions qui ne différeront que par les noisettes qui leurs seront ajoutés.',
	'editer_configurer_page' => 'Configurer les noisettes de cette page',
	'editer_supprimer_noisettes' => 'Supprimer les noisettes définies pour cette page',
	'editer_exporter_configuration' => 'Exporter la configuration',
	'editer_compositions' => 'Gérer les compositions du noiZetier',
	'editer_importer_configuration' => 'Importer une config.',
	'editer_nouvelle_composition'=> 'Créer une nouvelle composition',
	'editer_composition' => 'Modifier cette composition',
	'editer_composition_heritages' => 'Définir les héritages',
	'editer_noizetier_importer_configuration' => 'Importer une configuration',
	'editer_noizetier_importer_configuration_explication' => 'Vouz pouvez importer une configuration du noizetier que vous auriez préalablement exportée ou bien une configuration fournie par un plugin.',
	
	'erreur_aucune_noisette' => 'Aucune noisette trouvée.',
	'erreur_doit_choisir_noisette' => 'Vous devez choisir une noisette.',
	'erreur_mise_a_jour' => 'Une erreur s\'est produite pendant la mise à jour de la base de donnée.',

	'explications_heritages_comosition' => 'Vous pouvez définir ici les compositions qui seront héritées par les objets de la branche.',
	'explication_noizetier_css' => 'Vous pouvez ajouter à la noisette d\'éventuelles classes CSS supplémentaires.',
	'explication_raccourcis_typo' => 'Vous pouvez utiliser les raccourcis typographiques de SPIP.',

	// F
	'formulaire_ajouter_noisette' => 'Ajouter une noisette',
	'formulaire_modifier_noisette' => 'Modifier cette noisette',
	'formulaire_supprimer_noisette' => 'Supprimer cette noisette',
	'formulaire_supprimer_noisettes_page' => 'Supprimer les noisettes de cette page',
	'formulaire_deplacer_bas' => 'Déplacer vers le bas',
	'formulaire_deplacer_haut' => 'Déplacer vers le haut',
	'formulaire_configurer_page' => 'Configurer la page :',
	'formulaire_configurer_bloc' => 'Configurer le bloc :',
	'formulaire_obligatoire' => 'Champs obligatoire',
	'formulaire_noisette_sans_parametre' => 'Cette noisette ne propose pas de paramètre.',
	'formulaire_explication_oui_non' => '(saisir <i>on</i> ou laisser vide)',
	'formulaire_explication_oui_ou_non' => '(saisir <i>oui</i> ou <i>non</i>)',
	'formulaire_modifier_composition' => 'Modifier cette composition :',
	'formulaire_modifier_composition_heritages' => 'Modifier les héritages de cette composition :',
	'formulaire_supprimer_composition' => 'Supprimer cette composition',
	'formulaire_nouvelle_composition' => 'Nouvelle composition',
	'formulaire_type' => 'Type de composition',
	'formulaire_type_explication' => 'Indiquez sur quel objet / quelle page porte cette composition.',
	'formulaire_composition' => 'Identifiant de composition',
	'formulaire_composition_explication' => 'Indiquez un mot-clé unique (minuscules, sans espace, sans tiret (-) et sans accent) permettant d\'identifier cette composition.<br />Par exemple : <i>macompo</i>.',
	'formulaire_nom' => 'Titre',
	'formulaire_nom_explication' => 'Vous pouvez utilisez la balise  &lt;multi&gt;.',
	'formulaire_description' => 'Description',
	'formulaire_description_explication' => 'Vous pouvez utilisez les raccourcis SPIP usuels, notamment la balise  &lt;multi&gt;.',
	'formulaire_icon' => 'Icône',
	'formulaire_icon_explication' => 'Vous pouvez saisir le chemin relatif vers une icône (par exemple : <i>images/objet-liste-contenus.png</i>). Pour voir une liste d\'images installèes dans les répertoires les plus courants, vous pouvez <a href="../spip.php?page=icones_preview">consulter cette page</a>.',
	'formulaire_identifiant_deja_pris' => 'Cet identifiant est déjà utilisé !',
	'formulaire_erreur_format_identifiant' => 'L\'identifiant ne peut contenir que des minuscules sans accent, des chiffres et le caractère _ (underscore).',
	'formulaire_composition_mise_a_jour' => 'Composition mise à jour',
	'formulaire_importer' => 'Importer une configuration',
	'formulaire_importer_explication' => 'Fichier de configuration au format YAML.',
	'formulaire_import_local' => 'Configurations disponibles localement',
	'formulaire_import_local_explication' => 'Liste des configurations détectées dans un sous-répertoire <i>config_noizetier</i>.',
	'formulaire_bouton_importer' => 'Importer',
	'formulaire_choix_fichier' => 'Choix du fichier à importer',
	'formulaire_fichier_import_manquant' => 'Vous devez choisir un fichier.',
	'formulaire_fichier_vide' => 'Le fichier ne contient pas de données.',
	'formulaire_options_importation' => 'Options d\'importation',
	'formulaire_liste_pages_config' => 'Ce fichier de configuration définis des noisettes sur les pages suivantes :',
	'formulaire_liste_compos_config' => 'Ce fichier de configuration définis les compositions du noizetier suivantes :',
	'formulaire_fichier_config' => 'Fichier de configuration :',
	'formulaire_type_import' => 'Type d\'importation',
	'formulaire_type_import_explication' => 'Vous pouvez fusionner le fichier de configuration avec votre configuration actuelle (les noisettes de chaque page seront ajoutées à vos noisettes déjà définies) ou bien remplacer votre configuration par celle-ci.',
	'formulaire_import_fusion' => 'Fusionner avec la configuration actuelle',
	'formulaire_import_remplacer' => 'Remplacer la configuration actuelle',
	'formulaire_import_compos' => 'Importer les compositions du noizetier',
	'formulaire_config_importee' => 'La configuration a été importée.',

	// I
	'ieconfig_probleme_import_config' => 'Un problème a été rencontré lors de l\'importation de la configuration du noiZetier.',
	'ieconfig_ne_pas_importer' => 'Ne pas importer',
	'ieconfig_noizetier_export_explication' => 'Exportera la configuration des noisettes et les compositions du noiZetier.',
	'ieconfig_noizetier_export_option' => 'Inclure dans l\'export ?',
	'info_page' => 'PAGE :',
	'info_composition' => 'COMPOSITION :',
	'installation_tables' => 'Tables du plugin noiZetier installées.<br />',
	'item_titre_perso' => 'titre personnalisé',

	// L
	'label_afficher_titre_noisette' => 'Afficher un titre de noisettes ?',
	'label_niveau_titre' => 'Niveau du titre :',
	'label_noizetier_css' => 'Classes CSS :',
	'label_texte' => 'Texte :',
	'label_titre' => 'Titre :',
	'label_titre_noisette' => 'Titre de la noisette :',
	'label_titre_noisette_perso' => 'Titre personnalisé:',
	'liste_icones' => 'Liste d\'icônes',

	// M
	'msg_erreur_import' => 'Une erreur technique a eu lieu, l\'import a &eacute;chou&eacute;',
	'msg_non_autorise_import' => 'Import non autoris&eacute;',

	// N
	'nom_bloc_contenu' => 'Contenu',
	'nom_bloc_navigation' => 'Navigation',
	'nom_bloc_extra' => 'Extra',
	'ne_pas_definir_d_heritage' => 'Ne pas définir d\'héritage',
	'noisettes_page' => 'Noisettes spécifiques à la page <i>@type@</i> :',
	'noisettes_composition' => 'Noisettes spécifiques à la composition <i>@composition@</i> :',
	'noisettes_toutes_pages' => 'Noisettes communes à toutes les pages :',
	'nom_bloctexte' => 'Bloc de texte libre',
	'non' => 'Non',
	'notice_enregistrer_rang' => 'Cliquez sur Enregistrer pour sauvegarder l\'ordre des noisettes.',

	// O
	'oui' => 'Oui',
	
	// P
	'page' => 'Page',

	// W
	'warning_noisette_plus_disponible' => 'ATTENTION : cette noisette n\'est plus disponible.',
	'warning_noisette_plus_disponible_details' => 'Le squelette de cette noisette (<i>@squelette@</i>) n\'est plus accessible. Il se peut qu\'il s\'agisse d\'une noisette nécessitant un plugin que vous avez désactivé ou désinstallé.',

);

?>
