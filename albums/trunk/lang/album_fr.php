<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/albums/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_associer' => 'Ajouter cet album',
	'bouton_dissocier' => 'Détacher',
	'bouton_editer_texte_album' => 'Editer le texte',
	'bouton_supprimer' => 'Supprimer',
	'bouton_valider_deplacer_documents' => 'Enregistrer les changements',
	'bouton_vider' => 'Vider',

	// C
	'c_albumotheque_filtres' => 'Les filtres latéraux permettent d’activer certains critères
		afin de restreindre la sélection des albums. Ils apparaitront et s’étofferont en fonction du nombre de vos albums et de leurs utilisations.
		<br>Un clic sur une entrée active le filtre, un autre clic le désactive.
		En les combinant, on peut facilement retrouver n’importe quel album.
		<br>Quand les listes sont trop longues, des champs de recherche permettent de retrouver des objets précis.',
	'c_albumotheque_presentation' => 'Bienvenue dans l’albumothèque !<br>
		Vous pouvez créer des albums « autonomes » sur cette page et depuis la barre d’outils rapides,
		ou créer des albums liés aux objets éditoriaux depuis leurs pages respectives.
		<br>Chaque album est éditable sur place (édition du texte & manipulation des documents), ou bien en vous rendant sur sa fiche.',
	'c_albumotheque_titre_filtres' => 'Filtrer la sélection',
	'c_albumotheque_titre_presentation' => 'Les albums',
	'cfg_titre_albums' => 'Albums',

	// E
	'erreur_deplacement' => 'Le traitement n’a pas pu être effectué',
	'explication_album_numero' => 'Numéros séparés par des virgules',
	'explication_deplacer_documents' => '<strong>Expérimental</strong> : il est possible de déplacer des documents entre albums par cliquer-glisser.
		Si vous disposez des droits nécessaires, le curseur change à leur survol pour signaler qu’une action est possible.
		Une fois les déplacements effectués, un formulaire apparaît en bas de la liste pour enregistrer les changements.',

	// F
	'filtre_extensions' => 'Extensions',
	'filtre_medias' => 'Types de Documents',
	'filtre_non_vus' => 'Non insérés dans un texte',
	'filtre_orphelins' => 'Orphelins',
	'filtre_types_utilisations' => 'Types d’utilisations',
	'filtre_utilisations' => 'Utilisations',
	'filtre_vus' => 'Insérés dans un texte',

	// I
	'icone_ajouter_album' => 'Ajouter un album',
	'icone_creer_album' => 'Créer un nouvel album',
	'icone_modifier_album' => 'Modifier cet album',
	'info_1_album' => '1 album',
	'info_1_utilisation' => '1 Utilisation',
	'info_aucun_album' => 'Aucun album',
	'info_nb_albums' => '@nb@ albums',
	'info_nb_utilisations' => '@nb@ utilisations',
	'info_nouvel_album' => 'Nouvel album',

	// L
	'label_activer_album_objets' => 'Activer les albums pour les contenus :',
	'label_activer_deplacer_documents' => 'Cliquer-glisser',
	'label_album_numero' => 'Numéro(s)',
	'label_case_deplacer_documents' => 'Déplacement de documents entre albums par cliquer-glisser',
	'label_case_utiliser_titre_defaut' => 'Par défaut, proposer le titre de l’objet lié',
	'label_descriptif' => 'Descriptif',
	'label_modele_alias_liste' => 'Liste',
	'label_modele_alias_vignettes' => 'Vignettes',
	'label_modele_alignement' => 'Alignement',
	'label_modele_alignement_centre' => 'Centré',
	'label_modele_alignement_droite' => 'Droite',
	'label_modele_alignement_gauche' => 'Gauche',
	'label_modele_choisir' => 'Choix du modèle',
	'label_modele_defaut' => 'Défaut',
	'label_modele_descriptif' => 'Afficher le descriptif',
	'label_modele_description_liste' => 'Vue des documents sous forme de liste',
	'label_modele_description_vignettes' => 'Vue d’images sous forme de vignettes',
	'label_modele_hauteur_images' => 'Hauteur maximale des images',
	'label_modele_identifiant' => 'Numéro de l’album',
	'label_modele_labels_images' => 'Afficher les labels des images',
	'label_modele_largeur_images' => 'Largeur maximale des images',
	'label_modele_meta_dimensions' => 'Dimensions',
	'label_modele_meta_extension' => 'Extension',
	'label_modele_meta_taille' => 'Taille',
	'label_modele_metas' => 'Informations sur le document :',
	'label_modele_nom_liste' => 'un album (liste)',
	'label_modele_nom_vignettes' => 'un album (vignettes)',
	'label_modele_parcourir_albums' => 'Parcourir les albums',
	'label_modele_placeholder_dimension' => 'Taille en px, sans l’unité',
	'label_modele_recadrer_images' => 'Recadrer les images',
	'label_modele_titre_perso' => 'Titre personnalisé',
	'label_modele_tri_date' => 'Date',
	'label_modele_tri_id' => 'N° du document',
	'label_modele_tri_media' => 'Type du document',
	'label_modele_tri_titre' => 'Titre',
	'label_modele_trier' => 'Trier par :',
	'label_onglet_ajouter_choisir' => 'Associer des albums existants',
	'label_onglet_ajouter_creer' => 'Créer et associer un album',
	'label_titre' => 'Titre',
	'label_utiliser_titre_defaut' => 'Titre d’un nouvel album',

	// M
	'message_1_album_ajoute' => '1 album a été ajouté.',
	'message_activer_cfg_documents' => 'Dans le formulaire de configuration des documents joints, cochez la case « Albums ».',
	'message_album_non_editable' => 'Cet album n’est pas éditable : il est utilisé par un ou pusieurs objets que vous ne pouvez pas modifier.',
	'message_avertissement_cfg_documents' => 'Attention ! L’ajout de documents aux albums est désactivé. L’activation est nécessaire au bon fonctionnement des albums.',
	'message_balise_inseree_succes' => 'La balise a été insérée dans le texte',
	'message_id_album_ajoute' => 'L’album N° @id_album@ a été ajouté.',
	'message_nb_albums_ajoutes' => '@nb@ albums ont été ajoutés.',
	'message_supprimer' => 'Supprimer définitivement ?',
	'message_vider' => 'Retirer tous les docments ?',

	// O
	'onglet_ajouter_choisir' => 'Choisir album(s)',
	'onglet_ajouter_creer' => 'Nouvel album',
	'onglet_configurer_options' => 'Options',
	'onglet_configurer_outils' => 'Outils',

	// T
	'texte_activer_ajout_albums' => 'Vous pouvez activer l’interface d’ajout d’albums aux articles, rubriques et autres.
		Comme les documents des portfolios, les albums peuvent ensuite être référencés dans le texte, ou affichés séparément.',
	'texte_changer_statut' => 'Modifier le statut',
	'texte_creer_album' => 'Créer un nouvel album',
	'texte_double_clic_inserer_balise' => 'Double-clic pour insérer la balise dans le texte',
	'texte_modifier' => 'Modifier',
	'texte_personnaliser_balise_album' => 'Personnaliser la balise',
	'texte_statut_poubelle' => 'à la poubelle',
	'texte_statut_prepa' => 'non publié',
	'texte_statut_publie' => 'publié en ligne',
	'titre_album' => 'Album',
	'titre_albums' => 'Albums',
	'titre_documents_deplaces' => 'Documents déplacés',
	'titre_logo_album' => 'Logo',
	'titre_page_configurer_albums' => 'Configurer les Albums'
);

?>
