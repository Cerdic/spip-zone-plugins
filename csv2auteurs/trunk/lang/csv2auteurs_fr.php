<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(
	// A
	'abs_redac' => 'Rédacteurs absents',
	'abs_admin' => 'Administrateurs restreints absents',
	'abs_visiteur' => 'Visiteurs absents',
	'abs_poubelle' => 'Traitement des auteurs absents sans articles',
	'administrateurs' => 'Administrateurs',
	'admin_a_zero' => 'Réinitialisation des rubriques administrées :',
	'auteur_poubelle'=>'mettre les auteurs "à la poubelle" sans les supprimer complètement',
	'auteur_nouveau'=>'supprimer les auteurs et attribuer leurs articles à l\'auteur',
	'avenir_auteurs'=>'Traitement des comptes supprimés pour les auteurs d\'articles :',
	'admin_sans_rub' => '<strong class="erreur">Attention !</strong> l\'administrateur restreint <strong>@login_auteur@</strong> n\'a pas de rubrique associée: son compte est rétrogradé en rédacteur.',

	// B
	'bravo' => 'Bravo !!',

	// C
	'creation_rubrique' => 'Création de rubriques pour les sous-groupes administrateurs:',
	'creer_rubrique' => 'Créer une rubrique par sous-groupe d\'admins:',
	'creer_article'=> 'Créer un article dans chaque rubrique admin:',
	'csv2auteurs_titre' => 'csv2auteurs',
	'choix_statut'=> 'Choisissez les types de statut que vous désirez exporter',
	'champ_manquant' => 'Les champs : login, statut et email sont obligatoires !!',
	'chargement_fichier_CSV_OK' => 'Chargement du fichier <strong>@nom_fichier@</strong>: OK',
	'choix_format_rub_zones' => 'Format pour les rubriques des admins (champ <strong>ss_groupe</strong>) et les zones d\'accés restreint (champ <strong>zone</strong>)',
	'choix_manip' => 'Choix de l\'opération',

	// D
	'delimiteur'=>'Choisissez le type de délimiteur que vous désirer pour créer votre fichier csv',

	// E
	'envoyer'=>'Lancer la moulinette',
	'erreurs' => 'Votre saisie contient des erreurs !',
	'extension' => 'Extension incorrect !',
	'exporter'=>'Exportation de comptes utilisateurs',
	'export_fichier_compatible_import' => 'Exporter un fichier directement réutilisable pour une importation ultérieure (= modèle)',

	// F
	'fichier_uploader' => 'Fichier à téléverser',
	'format_titres_rub_zone' => 'Titres complets',
	'format_id_rub_zone' => 'Identifiants numériques',

	// I
	'importer'=>'Importation de comptes utilisateurs',

	// L
	'login_idem_admin_non_traite' => '<strong class="erreur">Attention!</strong> Le compte ayant pour login <strong>@login_refuse@</strong> n\'a pas été traité car cet identifiant est celui d\'un administrateur complet',

	// M
	'mettre_a_jour_utilisateurs_existants' => 'Mettre à jour les utilisateurs existants :',
	'mise_a_jour_utilisateur' => 'Mise à jour des utilisateurs existant déja dans SPIP :',
	'maj_infoperso' => 'Mise à jour des infos personnelles (mail, pseudo, pass):',
	'type_maj_remplacer'=>'Remplacer',
	'type_maj_ajouter'=>'Ajouter',
	'type_maj_label'=>'Gestion des rubriques administrées et des zones d\'accès des utilisateurs existants',

	// N
	'nom_champs'=>'Sélectionnez les champs que vous souhaitez exporter',
	'nom_rubrique_archive'=>'Nom de la rubrique d\'archives :',
	'nom_nouvel_auteur'=>'(mot de passe idem login)',
	'non_autorise' => 'Seuls les webmestres ont le droit d\'utiliser csv2auteurs: veuillez vous reconnecter avec un compte adapté',
	'nouveau_separateur' => 'Le nouveau séparateur de champs est : @separateur@',
	'nbe_auteurs_a_traiter' => 'Nombre de comptes utilisateurs à traiter: @nb_auteurs@',
	'nbe_rubriques_admin_crees' => 'Nombre de rubriques créées pour les admins restreints: @nb_rubriques@',
	'nb_auteurs_maj' => 'Nombre de comptes utilisateurs mis à jour: @nb_auteurs_maj@',
	'nb_auteurs_crees' => 'Nombre de nouveaux utilisateurs  créés: @nb_auteurs_crees@',
	'nb_auteurs_poubelle_effaces' => 'Nombre d\'utilisateurs à la poubelle supprimés: @nb_auteurs_poubelle_effaces@',
	'nb_visiteurs_effaces' => 'Nombre de visiteurs supprimés: @nb_visiteurs_effaces@',
	'nb_auteurs_effaces' => 'Nombre de rédacteurs supprimés: @nb_auteurs_effaces@',
	'nb_admins_restreints_effaces' => 'Nombre d\'admins restreints supprimés: @nb_admins_restreints_effaces@',
	'nb_champ_passes' => '<strong>NB</strong>: les mots de passes n\'étant pas récupérables en clair, le champ <strong>pass</strong> sera toujours vide', 

	// O
	'obligatoire' => 'Ce champs est obligatoire !',

	// P
	'parametres_fichier_csv' => 'Paramètres du fichier CSV',
	'presentation_formulaire' => 'CSV2AUTEURS : gestion des utilisateurs de SPIP à partir de fichiers CSV',
	'point_virgule'=>'Point-virgule ";"',
	'passage_poubelle' => 'passer à la poubelle',
	'probleme_creation_maj_compte' => '<strong class="erreur">Erreur !</strong> La création/modification du compte <strong>@login_auteur@</strong> retourne l\'erreur: ',
	'pas_nouveau_compte_sans_mdp' => '<strong class="erreur">Erreur !</strong> La création du compte <strong>@login_auteur@</strong> est annulée: il manque le mot de passe.',

	// R
	'redacteurs' => 'Rédacteurs',
	'rubrique_defaut' => 'Rubrique par défaut des admins restreints:',
	'rubrique_parent_archive'=>'Choisissez la rubrique parente pour votre répertoire d\'archive',
	'rubrique_parent'=>'Choisissez la rubrique parente',
	'rien_faire' => 'Ne rien faire',
	'rubrique_archive_cree' => 'La rubrique <strong>@titre_rubrique_archive@</strong> a été créée pour archiver les articles auteurs supprimés',
	'raz_rubriques_admins_zones' => 'Remise à zéro des rubriques administrées et des zones d\'accès restreint pour les utilisateurs existant',
	'rubrique_admin_pas_trouvee' => '<strong class="erreur">Attention !</strong> auteur admin restreint <strong>@login_auteur@</strong>: la rubrique <strong>@rub_pas_trouvee@</strong> n\'existe pas.',

	// S
	'suppression_absents' => 'Suppression des absents:',
	'suppression_article_efface' => 'Traitement des articles des auteurs effacés :',
	'supprimer_articles' =>'Supprimer les articles des auteurs effacés',
	'supprimer_auteur' => 'suppression complète (y compris ceux à la poubelle)',
	'separateur' => 'Choix du caractère de séparation de champs',
	'submit_format_import' => 'Exporter au format d\'importation',
	'submit_export_' => 'Exporter les champs sélectionnés',
	'statut_absent' => '<strong class="erreur">Attention !</strong> Le compte <strong>@login_auteur@</strong> n\'a pas de statut défini: il est intégré comme visiteur.',

	// T
	'transfert' => 'Erreur lors du transfert !',
	'transferer_articles' => 'Transférer les articles dans une rubrique d\'archives: ',
	'taille' => 'Le fichier est trop gros !',
	'titre_choix_statuts' => 'Choix des catégories d\'utilisateurs à exporter',
	'titre_choix_formats' => 'Choix de la forme des champs supplémentaires',
	'titre_choix_champs' => 'Choix du type d\'exportation',

	// V
	'visiteurs' => 'Visiteurs',
	'virgule'=>'virgule ","',
	'vidage_poubelle' => 'Vider la poubelle',

	// Z
	'zone_pas_trouvee' => '<strong class="erreur">Attention !</strong> auteur @login_auteur@: la zone <strong>@zone_pas_trouvee@</strong> n\'existe pas.',
);
?>