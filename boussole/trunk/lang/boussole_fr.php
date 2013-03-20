<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/boussole/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_boussole' => 'Boussole',
	'bouton_supprimer' => 'Supprimer',
	'bulle_afficher' => 'Afficher dans les modèles',
	'bulle_aller_site' => 'Se rendre sur la page du site référencé',
	'bulle_cacher' => 'Ne pas afficher dans les modèles',
	'bulle_deplacer_bas' => 'Déplacer vers le bas',
	'bulle_deplacer_haut' => 'Déplacer vers le haut',

	// C
	'colonne_alias' => 'Alias',
	'colonne_nbr_sites' => 'Contient',
	'colonne_titre' => 'Titre',
	'colonne_version' => 'Version',

	// D
	'description_noisette_boussole' => 'Affichage standard d\'une boussole. Vous pouvez choisir le modèle d\'affichage (liens textuels, logos...) ainsi que sa configuration précise',
	'description_noisette_boussole_actualite' => 'Affichage des articles syndiqués des sites d\'une boussole selon le modèle d\'affichage <code>boussole_liste_actualite</code>.',
	'description_noisette_boussole_contenu_z' => 'Affichage de toutes les informations d\'une boussole comme contenu principal d\'une page Z et selon le modèle d\'affichage <code>boussole_contenu_z</code>.',
	'description_noisette_boussole_fil_ariane' => 'Affichage du fil d\'ariane d\'une boussole.',
	'description_page_boussole' => 'Page des informations détaillées d\'une boussole',

	// I
	'info_ajouter_boussole' => 'En ajoutant des boussoles à votre base de données, vous aurez la possiblité d\'utiliser les modèles fournis pour les afficher dans vos pages publiques.<br />Si la boussole existe déjà ce formulaire permettra de la mettre à jour en conservant la configuration d\'affichage.',
	'info_boite_boussoles_gerer' => '<strong>Cette page est uniquement accessible aux responsables du site.</strong><p>Elle permet l’ajout, la mise à jour et la suppression des boussoles. Il est aussi possible de se rendre sur la page de configuration de l\'affichage de chaque boussole.</p>',
	'info_configurer_boussole' => 'Ce formulaire vous permet de configurer l\'affichage de la boussole en choisissant les sites à afficher ou pas et l\'ordre d\'affichage dans un groupe. Les sites non affichés sont repérés par un fond hachuré et une police grise.',
	'info_fichier_boussole' => 'Saisissez l\'url du fichier de description de votre boussole',
	'info_liste_aucune_boussole' => 'Aucune boussole n\'a encore été chargée dans votre base. Utilisez le formulaire ci-dessous pour en ajouter.',
	'info_site_boussole' => 'Ce site fait partie de la boussole :',
	'info_site_boussoles' => 'Ce site fait partie des boussoles :',

	// L
	'label_1_boussole' => '@nb@ boussole',
	'label_1_site' => '@nb@ site',
	'label_a_class' => 'Classe de l\'ancre englobant le logo',
	'label_actualise_le' => 'Actualisée le',
	'label_affiche' => 'Affiché ?',
	'label_afficher_descriptif' => 'Afficher le descriptif des sites ?',
	'label_afficher_lien_accueil' => 'Afficher le lien vers la page d\'accueil ?',
	'label_afficher_slogan' => 'Afficher le slogan des sites ?',
	'label_ariane_separateur' => 'Séparateur :',
	'label_boussole' => 'Boussole à afficher',
	'label_cartouche_boussole' => 'Afficher le cartouche de la boussole ?',
	'label_demo' => 'Retrouvez la page de démo de cette boussole à l\'adresse',
	'label_descriptif' => 'Descriptif',
	'label_div_class' => 'Classe du div englobant',
	'label_div_id' => 'Id du div englobant',
	'label_fichier_xml' => 'Fichier XML',
	'label_li_class' => 'Classe de chaque balise li de la liste',
	'label_logo' => 'Logo',
	'label_max_articles' => 'Nombre max d\'articles affichés par site',
	'label_max_sites' => 'Nombre max de sites',
	'label_mode' => 'Choisissez une boussole',
	'label_mode_standard' => '« @boussole@ », boussole officielle des sites SPIP',
	'label_modele' => 'Modèle d\'affichage',
	'label_n_boussoles' => '@nb@ boussoles',
	'label_n_sites' => '@nb@ sites',
	'label_nom' => 'Nom',
	'label_p_class' => 'Classe du paragraphe englobant le descriptif',
	'label_sepia' => 'Code de la couleur de sépia (sans #)',
	'label_slogan' => 'Slogan',
	'label_taille_logo' => 'Taille max du logo (en pixels)',
	'label_taille_logo_boussole' => 'Taille max du logo de la boussole (en pixels)',
	'label_taille_titre' => 'Taille max du titre d\'une boussole',
	'label_titre_actualite' => 'Afficher le titre du bloc d\'actualité ?',
	'label_titre_boussole' => 'Afficher le titre de la boussole ?',
	'label_titre_groupe' => 'Afficher le titre du groupe ?',
	'label_titre_site' => 'Afficher le titre des sites ?',
	'label_type_bulle' => 'Information affichée dans la bulle de chaque lien',
	'label_type_description' => 'Description affichée à coté du logo',
	'label_ul_class' => 'Classe de la balise ul de la liste',
	'label_url' => 'URL',
	'label_version' => 'Version',

	// M
	'message_nok_boussole_inconnue' => 'Aucune boussole ne correspond &agrave l\'alias « @alias@ »',
	'message_nok_champ_obligatoire' => 'Ce champ est obligatoire',
	'message_nok_ecriture_bdd' => 'Erreur d\'écriture en base de données (table @table@)',
	'message_nok_xml_introuvable' => 'Le fichier « @fichier@ » est introuvable',
	'message_nok_xml_invalide' => 'Le fichier XML « @fichier@ » de description de la boussole n\'est pas conforme à la DTD',
	'message_ok_boussole_actualisee' => 'La boussole « @fichier@ » a été mise à jour.',
	'message_ok_boussole_ajoutee' => 'La boussole « @fichier@ » a été ajoutée.',
	'modele_boussole_liste_avec_logo' => 'Liste de liens avec noms, logos et description',
	'modele_boussole_liste_par_groupe' => 'Liste de liens textuels par groupe',
	'modele_boussole_liste_simple' => 'Liste simple de liens textuels',
	'modele_boussole_panorama' => 'Galerie des logos',
	'modele_boussole_panorama_sepia' => 'Galerie des logos avec effet sépia',

	// O
	'option_aucune_description' => 'Aucune description',
	'option_descriptif_site' => 'Descriptif du site',
	'option_nom_site' => 'Nom du site',
	'option_nom_slogan_site' => 'Nom et slogan du site',
	'option_slogan_site' => 'Slogan du site',

	// T
	'titre_boite_autres_boussoles' => 'Autres boussoles',
	'titre_boite_infos_boussole' => 'BOUSSOLE D\'ALIAS',
	'titre_boite_logo_boussole' => 'LOGO DE LA BOUSSOLE',
	'titre_form_ajouter_boussole' => 'Ajouter ou mettre à jour une boussole',
	'titre_formulaire_configurer' => 'Configuration de l\'affichage de la boussole',
	'titre_liste_boussoles' => 'Liste des boussoles disponibles',
	'titre_page_boussole' => 'Gestion des boussoles'
);

?>
