<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://trac.rezo.net/spip/spip/ecrire/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'item_administrateur_2' => 'Niveau 1',
	'intem_redacteur' => 'Niveau 2',
	'item_visiteur' => 'Niveau 3',

	'info_auteurs' => 'Les utilisateurs',
	'info_modifier_auteur' => 'Modifier l\'utilisateur :',
	'entree_nom_site_2' => 'Nom du site de l\'utilisateur',
	'entree_infos_perso_2' => 'Qui est cet utilisateur ?',
	'info_nb_auteurs' => '@nb@ utilisateurs',
	'info_1_auteur' => '1 utilisateur',
	'auteur' => 'Utilisateur :',
	'creer_et_associer_un_auteur' => 'Créer et associer un utilisateur',
	'icone_afficher_auteurs' => 'Afficher les niveaux 0 à 2',
	'icone_afficher_visiteurs' => 'Afficher les niveaux 3',
	'icone_creer_auteur' => 'Créer un nouvel utilisateur et l’associer à cet article',
	'info_articles_auteur' => 'Les articles de cet utilisateur',
	'info_aucun_auteur' => 'Aucun utilisateur',
	'info_auteur_gere_rubriques' => 'Cet utilisateur gère les rubriques suivantes :',
	'info_auteur_gere_toutes_rubriques' => 'Cet utilisateur gère <b>toutes les rubriques</b>',
	'info_auteur_gere_toutes_rubriques_2' => 'Je gère <b>toutes les rubriques</b>',
	'info_auteurs_par_tri' => 'Utilisateurs@partri@',
	'info_auteurs_trouves' => 'Utilisateurs trouvés',
	'info_gauche_auteurs' => 'Vous trouverez ici tous les utilisateurs du site. Leur statut est indiqué par la couleur de leur icone (niveau 1 = vert ; niveau 2 = jaune).',
	'info_gauche_auteurs_exterieurs' => 'Les utilisateurs extérieurs, sans accès au site, sont indiqués par une icone bleue ;
		les utilisateurs effacés par une icone grise.',
	'info_preview_texte' => 'Il est possible de prévisualiser les différents éléments éditoriaux du site ayant au moins le statut « proposé », ainsi que les éléments en cours de rédaction dont on est l’auteur. Cette fonctionnalité doit-elle être disponible pour les utilisateurs de niveau 1, de niveau 2, ou personne ?',
	'info_qui_edite' => '@nom_auteur_modif@ a travaillé sur ce contenu il y a @date_diff@ minutes',
	'info_recherche_auteur_zero' => 'Aucun résultat pour « @cherche_auteur@ ».',
	'info_statut_auteur' => 'Statut de cet utilisateur :',
	'info_statut_auteur_2' => 'Je suis',
	'info_statut_auteur_a_confirmer' => 'Inscription à confirmer',
	'info_statut_auteur_autre' => 'Autre statut :',
	'info_statut_utilisateurs_2' => 'Choisissez le statut qui est attribué aux personnes présentes dans l’annuaire LDAP lorsqu’elles se connectent pour la première fois. Vous pourrez par la suite modifier cette valeur pour chaque utilisateur au cas par cas.',
	'item_nouvel_auteur' => 'Nouvel utilisateur',
	'lien_ajouter_auteur' => 'Ajouter cet utilisateur',
	'lien_retirer_auteur' => 'Retirer l’utilisateur',
	'lien_retirer_tous_auteurs' => 'Retirer tous les utilisateurs',
	'logo_auteur' => 'Logo de l’utilisateur',
	'texte_ajout_auteur' => 'L’utilisateur suivant a été ajouté à l’article :',
	'texte_aucun_resultat_auteur' => 'Aucun résultat pour "@cherche_auteur@"',
	'texte_auteur_messagerie' => 'Ce site peut vous indiquer en permanence la liste des utilisateurs connectés, ce qui vous permet d’échanger des messages en direct. Vous pouvez décider de ne pas apparaître dans cette liste (vous êtes « invisible » pour les autres utilisateurs).',
	'texte_auteurs' => 'LES UTILISATEURS',
	'texte_fichier_authent' => '<b>SPIP doit-il créer les fichiers spéciaux
<tt>.htpasswd</tt> et <tt>.htpasswd-admin</tt> dans le répertoire @dossier@ ?</b>
<p>Ces fichiers peuvent vous servir à restreindre l’accès aux auteurs et administrateurs en d’autres endroits de votre site (programme externe de statistiques, par exemple).</p>
<p>Si vous n’en avez pas l’utilité, vous pouvez laisser cette option à sa valeur par défaut (pas de création des fichiers).</p>',
	'texte_plusieurs_articles' => 'Plusieurs utilisateurs trouvés pour "@cherche_auteur@" :',
	'texte_travail_article' => '@nom_auteur_modif@ a travaillé sur cet article il y a @date_diff@ minutes',
	'titre_ajouter_un_auteur' => 'Ajouter un utilisateur',
	'titre_cadre_ajouter_auteur' => 'AJOUTER UN UTILISATEUR :',
	'titre_cadre_numero_auteur' => 'UTILISATEUR NUMÉRO',

);
