<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/emballe_medias/media_collections/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_lien_collection' => 'Ajouter cette collection',

	// B
	'bouton_diogene_statut_poubelle_normal' => 'Mettre votre collection à la poubelle',
	'bouton_diogene_statut_poubelle_normal_pas_auteur' => 'Mettre cette collection à la poubelle',
	'bouton_dissocier' => 'Dissocier',
	'bouton_fermer_collection' => 'Rendre cette collection personnelle',
	'bouton_media_associer' => 'Associer à une collection',
	'bouton_ouvrir_collection' => 'Rendre cette collection collaborative',
	'bouton_quitter_collection' => 'Ne plus collaborer à cette collection',
	'bouton_rejoindre_collection' => 'Collaborer à cette collection',

	// C
	'collections_titre' => 'Les collections',

	// D
	'description_page_collections' => 'L\'ensemble des collections du site.',
	'diogene_champ_collection' => 'Ajout à une collection',

	// E
	'erreur_association_collection' => 'L\'association à la collection a échoué.',
	'erreur_autorisation_statut_modifier' => 'Vous n\'êtes pas autorisé à modifier cette collection.',
	'erreur_collection_non_existante' => 'Cette collection n\'existe pas.',
	'erreur_media_document' => 'Attention. Le type de ce document ne correspond pas au genre de la collection.',
	'erreur_selectionner_un_media' => 'Veuillez sélectionner au moins un média.',
	'explication_genre' => 'Quels types de médias peuvent être attachés à cette collection ?',
	'explication_genre_defaut' => 'Valeur par défaut pour le types de médias pouvant être attaché (utile lors de la création rapide).',
	'explication_medias_ajouter' => 'Les médias ci-dessous ne sont pas associés à la collection et peuvent correspondre à son genre. Sélectionnez ceux que vous souhaitez ajouter.',
	'explication_medias_modifier' => 'Les médias ci-dessous sont associés à la collection. Pour en dissocier certains, sélectionnez les et appuyez sur le bouton de suppression.',
	'explication_medias_modifier_drag' => 'Vous pouvez les réordonner via glisser / déposer.',
	'explication_participer' => 'Cliquez sur le bouton ci-dessous pour participer à cette collection.',
	'explication_participer_non' => 'Cliquez sur le bouton ci-dessous pour ne plus participer à cette collection.',
	'explication_type' => 'Quel est l\'usage de la collection ?',
	'explication_type_defaut' => 'Valeur par défaut de l\'usage de la collection (utile lors de la création rapide).',

	// G
	'genre_mixed' => 'Mix (tous les types)',
	'genre_musique' => 'Sons (uniquement de type sonore : musique...)',
	'genre_photo' => 'Images (uniquement de type image : photos, icones...)',
	'genre_video' => 'Vidéos (uniquement de type vidéo)',

	// I
	'icone_creer_collection' => 'Créer une collection',
	'icone_modifier_collection' => 'Modifier cette collection',
	'info_1_collection' => 'Une collection',
	'info_aucun_collection' => 'Aucune collection disponible',
	'info_collaborateurs' => 'Les collaborateurs :',
	'info_collection_creation' => 'Collection créée le <abbr title="@date_abbr@">@date@</abbr> <span class="auteurs">par @auteurs@</span>.',
	'info_collection_maj' => 'Mise à jour : ',
	'info_collections_auteur' => 'Les collections de cet auteur',
	'info_media_dans_collection' => 'Dans la collection : ',
	'info_media_dans_collections' => 'Dans les @nb@ collections : ',
	'info_medias_1' => 'Un média',
	'info_medias_aucun' => 'Aucun média',
	'info_medias_nb' => '@nb@ médias',
	'info_nb_collections' => '@nb@ collections',
	'info_non_autorise_creer_collection' => 'L\'auteur "@id_auteur@" n\'est pas autorisé à créer une collection.',
	'info_non_autorise_modifier_collection' => 'L\'auteur "@id_auteur@" n\'est pas autorisé à modifier la collection @id@.',
	'info_rang_collection' => 'Rang dans la collection',

	// L
	'label_choix_collection' => 'Lier à la collection',
	'label_collections_ajouter' => 'Ajouter à la collection',
	'label_collections_retirer' => 'Retirer de la collection',
	'label_date' => 'Date',
	'label_date_point' => 'Date :',
	'label_descriptif' => 'Descriptif',
	'label_descriptif_point' => 'Descriptif :',
	'label_genre' => 'Types de médias',
	'label_genre_defaut' => 'Types de médias par défaut',
	'label_genre_point' => 'Types de médias :',
	'label_id_admin' => 'Administrateur de la collection',
	'label_id_admin_point' => 'Administrateur de la collection :',
	'label_mediabox' => 'MediaBox',
	'label_mediabox_long' => 'Ne pas utiliser la MediaBox pour afficher le formulaire d\'association de média à une collection.',
	'label_titre' => 'Titre',
	'label_titre_creer_rapide' => 'Titre de la collection',
	'label_titre_point' => 'Titre :',
	'label_type' => 'Usage',
	'label_type_defaut' => 'Usage par défaut',
	'label_type_point' => 'Usage :',
	'legende_collection' => 'Collections',
	'legende_medias_collection_ajouter' => 'Ajouter des médias',
	'legende_medias_collection_modifier' => 'Modifier la liste des médias',
	'lien_editer_collection' => 'Éditer cette collection',
	'lien_toutes_collections' => 'Toutes les collections',

	// M
	'message_aucune_collection_critere' => 'Aucune collection ne correspond à vos critères.', # Utile pour des squelettes
	'message_aucune_collection_publiee' => 'Aucune collection n\'est actuellement publiée.', # Utile pour des squelettes
	'message_collection_aucun_media_publie' => 'Aucun média dont vous êtes l\'auteur et correspondant au genre de la collection n\'est disponible.',
	'message_collection_media_utiliser_autres_auteurs' => 'Vous pouvez lier les médias d\'autres auteurs en utilisant le formulaire d\'ajout sur chaque page de média.',
	'message_collection_media_utiliser_interface' => 'Publiez un ou plusieurs médias via l\'interface dédiée afin de pouvoir les ajouter à cette collection.',
	'message_collection_reorganisee' => 'La collection a été réorganisée.',
	'message_collection_vide' => 'Cette collection est vide',
	'message_nombre_ajoute' => '1 média a été associé à la collection.',
	'message_nombre_ajoutes' => '@nb@ médias on été associés à la collection.',
	'message_nombre_dissocie' => '1 média a été dissocié de la collection.',
	'message_nombre_dissocies' => '@nb@ médias ont été dissociés de la collection.',

	// O
	'optgroup_collections_autres' => 'Les autres collections (collaboratives)',
	'optgroup_collections_votre' => 'Vos collections',
	'option_erreur_genre' => 'Le type ne correspond pas au média : @genre@',

	// P
	'publier_une_collection' => 'Une collection',
	'publier_une_collection_desc' => 'Créer une collection de médias.',

	// R
	'retirer_lien_collection' => 'Retirer cette collection',
	'retirer_tous_liens_collections' => 'Retirer toutes les collections',

	// T
	'tab_associer_collection' => 'Médias de la collection',
	'tab_editer_collection' => 'Édition de la collection',
	'texte_ajouter_collection' => 'Ajouter une collection',
	'texte_changer_statut_collection' => 'Cette collection est :',
	'texte_creer_associer_collection' => 'Créer et associer une collection',
	'texte_statut_publie' => 'publiée en ligne',
	'title_voir_media' => 'Voir le média : @titre@',
	'titre_collection' => 'Collection',
	'titre_collections' => 'Collections',
	'titre_collections_rubrique' => 'Rubriques de la rubrique',
	'titre_creer_collection_rapide' => 'Créer une collection',
	'titre_langue_collection' => 'Langue de cette collection',
	'titre_logo_collection' => 'Logo de cette collection',
	'titre_media' => 'Média #@rang@ : @titre@',
	'titre_modifier_collection' => 'Modifier la collection : @titre@',
	'titre_modifier_collection_diogene' => 'Modifier cette collection',
	'titre_page_collections' => 'Les collections',
	'titre_page_configurer_collections' => 'Configurer les collections',
	'titre_participer' => 'Participer',
	'type_coop' => 'Collaboratif',
	'type_perso' => 'Personnel'
);

?>
