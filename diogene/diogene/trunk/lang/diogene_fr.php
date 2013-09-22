<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/diogene/diogene/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_editer_diogene' => 'Templates de formulaires',
	'bouton_supprimer_diogene' => 'Supprimer ce template',

	// C
	'champ_date_publication' => 'Date de publication',
	'champ_date_publication_anterieure' => 'Date de publication antérieure',
	'champ_forum' => 'Activer / désactiver les forums',
	'choix_langue' => 'Langue',

	// D
	'diogene' => 'Template de formulaire',
	'diogene_statuts' => 'Diogène (Statuts)',
	'diogenes' => 'Templates de formulaires',

	// E
	'editer_diogene_explication' => 'Diogène permet de modifier les formulaires d’édition classiques en enlevant ou rajoutant certains champs.',
	'editer_diogenes_titre' => 'Les templates de formulaires',
	'erreur' => 'Erreur',
	'erreur_autorisation_login_publier' => 'Vous devez <a href="@url@" class="spip_in">être identifié pour publier ici</a>.',
	'erreur_autorisation_modifier_site' => 'Votre statut ne vous autorise pas à modifier ce site',
	'erreur_autorisation_statut_publier' => 'Votre statut ne vous autorise pas à publier ici.',
	'erreur_autorisation_statut_publier_limite' => 'Vous avez atteint la limite d’objet pouvant être <a href="@url@">en attente de publication</a> (@nb@)',
	'erreur_diogene_multiple_page' => 'Vous ne pouvez avoir qu’un seul template "page" sur ce site',
	'erreur_droits_objet_publier' => 'Vos droits actuels ne vous autorisent pas à publier ce type d’objet.',
	'erreur_forums' => 'Erreur dans le choix de la configuration des forums',
	'erreur_id_parent_id_rubrique' => 'Vous ne pouvez pas mettre cette rubrique dans elle-même.',
	'erreur_identifiant_existant' => 'Cet identifiant est déjà existant',
	'erreur_objet_diogene_max' => 'Le nombre de masques maximum (@max@) pour ce type d’objet (@objet@) est déjà atteint.',
	'erreur_objet_non_diogene' => 'Ce type d’objet n’est pas pris en compte (@objet@).',
	'erreur_objet_publier' => 'Le type d’objet que vous souhaitez publier n’existe pas.',
	'erreur_secteur_diogene_inexistant' => 'Attention, la rubrique concernant ce template de formulaire est inexistante.',
	'erreur_valeur_float' => 'La valeur "@champ@" doit être un nombre',
	'erreur_valeur_int' => 'La valeur "@champ@" doit être un nombre fixe.',
	'explication_article_deja_traduit' => 'Cet article est déjà traduit en : ',
	'explication_info_type' => 'Cet identifiant doit être unique et ne comporter aucun caractère spécifiques',
	'explication_nombre_attente' => 'Nombre maximal d’objets en attente de publication (n’affecte pas les administrateurs, laisser 0 pour illimité).',
	'explication_rubrique_statut' => 'Une rubrique est considérée comme visible (publiée) lorsqu’elle contient au moins un autre objet publié.',
	'explication_statut_auteur' => 'Ce champ défini le statut minimal pour pouvoir créer un objet correspondant à ce template.',
	'explication_statut_auteur_publier' => 'Ce champ défini le statut minimal pour pouvoir publier définitiment un objet correspondant à ce template.',

	// F
	'formulaire_modifier_diogene' => 'Modifier le template (Diogène)',
	'formulaire_nouveau' => 'Nouveau template de formulaire',

	// I
	'icone_editer_diogene_nouveau' => 'Nouveau template (@type@)',
	'icone_modifier_diogene' => 'Modifier ce template',
	'icone_nouveau_diogene' => 'Créer un nouveau template',
	'info_1_diogene' => '1 template',
	'info_aucun_diogene' => 'Aucun template',
	'info_aucune_sous_rubrique' => 'Il n’existe pas encore de sous-rubrique correspondante.',
	'info_diogenes_rien_publie' => 'Vous n’avez encore rien publié.',
	'info_menu_diogene' => 'Élément de menu pour le plugin Diogène',
	'info_nb_diogenes' => '@nb@ templates',
	'info_numero_diogene' => 'Template numéro',
	'info_publier_rubrique' => 'Publier dans cette rubrique :',
	'info_referencement_automatise' => 'Les informations de référencement automatisé',
	'info_rubrique_new' => 'Nouvelle rubrique vide',
	'info_rubrique_publie' => 'Rubrique visible',
	'info_rubrique_vide' => 'Rubrique vide',
	'info_statut' => 'Statut : ',
	'info_syndication' => 'Syndication',
	'info_traduction_article' => 'Cet article est une traduction de « <a href="@url@">@titre@</a> ».',
	'info_type' => 'Identifiant',

	// L
	'label_cacher_heure' => 'Ne pas afficher l’heure pour les dates',
	'label_cextras_enleves' => 'Champs extras à ne pas afficher',
	'label_champs_ajoutes' => 'Champs à ajouter',
	'label_champs_caches' => 'Champs à ne pas afficher',
	'label_change_statut_normal' => 'Modifier le statut de votre article',
	'label_change_statut_normal_site' => 'Modifier le statut de ce site',
	'label_description' => 'Description',
	'label_limiter_rubriques' => 'Limiter aux rubriques',
	'label_limiter_secteur' => 'Limiter au secteur',
	'label_logo_site' => 'Logo du site',
	'label_menu' => 'Figurer dans le menu public',
	'label_nombre_attente' => 'Nombre maximal en attente',
	'label_polyhier' => 'Polyhiérarchie',
	'label_polyhier_desactiver' => 'Désactiver la polyhiérarchie',
	'label_statut_auteur' => 'Statut minimal des auteurs',
	'label_statut_auteur_publier' => 'Statut minimal des auteurs pouvant publier définitivement',
	'legende_champs_diogene' => 'Champs du formulaire',
	'legende_selecteur_statut' => 'Statut',
	'legende_statuts_diogene' => 'Statuts',
	'libelle_logo_diogene' => 'Logo du template',
	'lien_creer' => 'Créer : ',
	'lien_creer_version' => 'Créer une version en : @lang@',
	'lien_editer_publication' => 'Éditer cette publication',
	'lien_editer_rubrique' => 'Éditer cette rubrique',
	'lien_editer_site' => 'Éditer ce site',
	'lien_modifier_version' => 'Modifier la version en : @lang@',
	'lien_modifier_version_originale' => 'Modifier la version originale en : @lang@',
	'lien_publier_nouvelle_rubrique' => 'Publier une nouvelle rubrique (@type@)',
	'lien_version' => 'Version : @lang@',

	// M
	'message_article_traduit_en' => 'Cet article existe déjà en :',
	'message_confirm_depublier' => 'Êtes vous sûr de vouloir le dépublier ?',
	'message_confirm_poubelle' => 'Êtes vous sûr de vouloir le supprimer ?',
	'message_confirm_sup' => 'Êtes vous sûr de continuer la suppression ?',
	'message_diogene_update' => 'Le template de formulaire a été mis à jour',
	'message_erreur_general' => 'Le formulaire contient des erreurs, veuillez vérifier son contenu.',
	'message_id_parent_unique' => 'Cet objet ne peut être créé que dans la rubrique « @rubrique@ »',
	'message_objet_cree' => '"@titre@" a été créé.',
	'message_objet_mis_a_jour' => '"@titre@" a été mis à jour.',
	'message_objet_mis_a_jour_lien' => 'Vous pouvez le consulter en suivant <a href="@url@" class="spip_in">ce lien</a>.',
	'message_objet_supprime' => '"@titre@" a été supprimé.',
	'message_pas_auteur' => 'Attention. Vous n’êtes pas listé comme auteur de cet objet.',
	'message_valider_action' => 'Êtes vous sûr ?',
	'message_valider_suppression' => 'Êtes vous sûr de vouloir supprimer ce template ?',
	'modifier_rubriques' => 'Modifier les rubriques',
	'modifier_vos_objets' => 'Modifier vos publications',

	// O
	'option_statut_changer' => 'Changer le statut en : @statut@',
	'option_statut_laisser' => 'Laisser le statut en : @statut@',

	// P
	'publier_titre' => 'Publier : ',
	'publier_titre_hierarchie' => 'Publier',

	// S
	'statuts' => 'Statuts',

	// T
	'texte_choix_rubrique' => 'Publier dans la rubrique',
	'texte_statut_en_cours_poubelle_normal' => 'Laisser votre article à la poubelle',
	'texte_statut_en_cours_poubelle_site' => 'Laisser ce site à la poubelle',
	'texte_statut_en_cours_prop_normal' => 'Laisser votre article proposé à la publication',
	'texte_statut_en_cours_prop_site' => 'Laisser ce site proposé à la publication',
	'texte_statut_en_cours_publie_normal' => 'Laisser votre article publié',
	'texte_statut_en_cours_publie_site' => 'Laisser ce site publié',
	'texte_statut_en_cours_redaction' => 'Laisser votre article en cours de rédaction',
	'texte_statut_poubelle_normal' => 'Mettre votre article à la poubelle',
	'texte_statut_poubelle_normal_pas_auteur' => 'Mettre cet article à la poubelle',
	'texte_statut_poubelle_site' => 'Mettre ce site à la poubelle',
	'texte_statut_prop_normal' => 'Proposer votre article à la publication',
	'texte_statut_prop_site' => 'Proposer ce site à la publication',
	'texte_statut_publie_normal' => 'Publier définitivement votre article',
	'texte_statut_publie_site' => 'Publier définitivement ce site',
	'texte_statut_redaction' => 'Mettre votre article en cours de rédaction',
	'texte_statut_redaction_sans_statut' => 'Mettre votre article en cours de rédaction',
	'title_page_publier' => 'Publier',
	'title_page_publier_titre' => 'Publier : @titre@',
	'titre_lien_publier' => 'Publier',
	'titre_modification_article' => 'Modification de l’article : @titre@',
	'titre_modification_rubrique' => 'Modification de la rubrique : @titre@',
	'titre_modification_site' => 'Modification du site : @titre@',
	'titre_modifier_article' => 'Modifier cette publication',
	'titre_modifier_publication' => 'Modifier cette publication',
	'titre_modifier_rubrique' => 'Modifier cette rubrique',
	'titre_modifier_site' => 'Modifier ce site',
	'titre_publier_nouveau_page' => 'Un nouvel objet',
	'titre_publier_proposes' => 'Un objet proposé (@nb@)',
	'titre_publier_proposes_page' => 'Un objet proposé',
	'titre_publier_proposes_page_vous' => 'Vos objets proposés'
);

?>
