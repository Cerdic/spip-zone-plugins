<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/menus/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'description_menu_accueil' => 'Lien vers la page d\'accueil du site.',
	'description_menu_articles_rubrique' => 'Affiche la liste des articles d\'une rubrique.',
	'description_menu_deconnecter' => 'Si le visiteur est connecté, ajoute une entrée lui proposant la déconnexion.',
	'description_menu_espace_prive' => 'Lien permettant de se connecter au site si on ne l\'est pas déjà, puis d\'aller dans l\'espace privé si l\'on y est autorisé.',
	'description_menu_groupes_mots' => 'Affiche automatiquement un menu listant les mots du groupe et les articles liés. Par d&eacute;faut, affiche la liste des groupes de mots et les mots li&eacute;s. Si un squelette groupes_mots.html existe, le lien vers le groupe est utilis&eacute;.',
	'description_menu_lien' => 'Ajoute un lien arbitraire, en interne (URL relative) ou externe (http://...).',
	'description_menu_mapage' => 'Si le visiteur est connecté, ajoute un lien vers sa page auteur.',
	'description_menu_mots' => 'Affiche automatiquement un menu listant les articles liés au mot clé.',
	'description_menu_objet' => 'Crée un lien vers un objet de SPIP : article, rubrique ou autre. Par défaut, l\'entrée aura le titre de l\'objet.',
	'description_menu_page_speciale' => 'Ajoute un lien vers un squelette page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ces pages sont souvent fournies par des plugins.',
	'description_menu_page_speciale_zajax' => 'Ajoute un lien vers un bloc d\'une page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ceci nécéssite une squelette de type Z et le plugin <a href="http://www.spip-contrib.net/MediaBox">médiabox</a>.',
	'description_menu_rubriques' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement).',
	'description_menu_rubriques_articles' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques et les articles sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement). Les articles sont placés systématiquement après les rubriques.',
	'description_menu_secteurlangue' => 'Cette entrée est spécifique aux sites utilisant un secteur par langue. Elle affiche automatiquement un menu listant les rubriques du secteur correspondant à la langue de la page et, si on veut, les sous-rubriques sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement).',
	'description_menu_texte_libre' => 'Simplement le texte que vous souhaitez',

	// E
	'editer_menus_editer' => 'Modifier ce menu',
	'editer_menus_explication' => 'Créez et configurez ici les menus de votre site.',
	'editer_menus_exporter' => 'Exporter ce menu',
	'editer_menus_nouveau' => 'Créer un nouveau menu',
	'editer_menus_titre' => 'Menus du site',
	'entree_afficher_articles' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)',
	'entree_afficher_item_suite' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)',
	'entree_articles_max' => 'Si oui, afficher les articles seulement si la rubrique contient au maximum xx articles ? (mettre le nombre maximum d\'articles, laissez vide pour afficher tous les articles)',
	'entree_articles_max_affiches' => 'Si oui, limiter le nombre d\'articles list&eacute;s &agrave; xx maximum (suivis d\'un item "... Tous les articles" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum d\'articles, laissez vide pour afficher tous les articles)',
	'entree_aucun' => 'Aucun',
	'entree_bloc' => 'Bloc Zpip',
	'entree_choisir' => 'Choisissez le type d\'entrée que vous voulez ajouter :',
	'entree_connexion_objet' => 'Obliger à être connecté (mettre "session") ou déconnecté (mettre "nosession") pour voir l\'objet',
	'entree_contenu' => 'Contenu',
	'entree_css' => 'Classes CSS de l\'entrée (du conteneur)',
	'entree_css_lien' => 'Classes CSS du lien',
	'entree_id_groupe' => 'Numéro du groupe de mot clé',
	'entree_id_mot' => 'Numéro du mot clé',
	'entree_id_objet' => 'Numéro',
	'entree_id_rubrique' => 'Numéro de la rubrique parente',
	'entree_infini' => 'À l\'infini',
	'entree_mapage' => 'Ma page perso',
	'entree_masquer_articles_uniques' => 'Si oui et si une rubrique contient un seul article, le masquer ? (mettre "oui" pour cela)',
	'entree_niveau' => 'Niveau des sous-rubriques',
	'entree_nombre_articles' => 'Nombre d\'articles au maximum (0 par défaut)',
	'entree_page' => 'Nom de la page',
	'entree_parametres' => 'Liste des paramètres',
	'entree_sousrub_cond' => 'N\'afficher que les sous-rubriques de la rubrique en cours (mettre "oui", sinon laisser vide)',
	'entree_sur_n_articles' => '@n@ articles affiché(s)',
	'entree_sur_n_mots' => '@n@ mots affiché(s)',
	'entree_sur_n_niveaux' => 'Sur @n@ niveau(x)',
	'entree_titre' => 'Titre',
	'entree_titre_connecter' => 'Titre pour l\'accès au formulaire d\'identification',
	'entree_titre_prive' => 'Titre pour accéder à l\'espace privé',
	'entree_traduction_articles_rubriques' => 'Dans la mesure du possible, afficher les articles de la rubrique dans la langue du contexte (mettre "trad" pour cela)',
	'entree_traduction_objet' => 'Dans le cas d\'un article, choisir la traduction en fonction du contexte (mettre "trad" pour cela)',
	'entree_tri_alpha' => 'Critère de tri (alphabétique)',
	'entree_tri_num' => 'Critère de tri (numérique)',
	'entree_type_objet' => 'Type de l\'objet',
	'entree_url' => 'Adresse',
	'entree_url_public' => 'Adresse de retour après la connexion',
	'erreur_aucun_type' => 'Aucun type d\'entrée n\'a été trouvé.',
	'erreur_autorisation' => 'Vous n\'êtes pas autorisé à modifier les menus.',
	'erreur_identifiant_deja' => 'Cet identifiant est déjà utilisé par un menu.',
	'erreur_identifiant_forme' => 'L\'identifiant ne doit contenir que des lettres, des chiffres ou le caractère souligné.',
	'erreur_menu_inexistant' => 'Le menu demandé numéro @id@ n\'existe pas.',
	'erreur_mise_a_jour' => 'Une erreur s\'est produite pendant la mise à jour de la base de donnée.',
	'erreur_parametres' => 'Il y a une erreur dans les paramètres de la page',
	'erreur_type_menu' => 'Vous devez choisir un type de menu',

	// F
	'formulaire_ajouter_entree' => 'Ajouter une entrée',
	'formulaire_ajouter_sous_menu' => 'Créer un sous-menu',
	'formulaire_css' => 'Classes CSS',
	'formulaire_css_explication' => 'Vous pouvez ajouter au menu d\'éventuelles classes CSS supplémentaires.',
	'formulaire_deplacer_bas' => 'Déplacer vers le bas',
	'formulaire_deplacer_haut' => 'Déplacer vers le haut',
	'formulaire_facultatif' => 'Facultatif',
	'formulaire_identifiant' => 'Identifiant',
	'formulaire_identifiant_explication' => 'Donnez un mot-clé unique qui vous permettra d\'appeler votre menu facilement.',
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Choisissez quel(s) menu(s) vous souhaitez importer.',
	'formulaire_ieconfig_importer' => 'Importer',
	'formulaire_ieconfig_menu_meme_identifiant' => 'ATTENTION : un menu avec le même identifiant existe déjà sur votre votre site !',
	'formulaire_ieconfig_menus_a_exporter' => 'Menus à exporter :',
	'formulaire_ieconfig_ne_pas_importer' => 'Ne pas importer',
	'formulaire_ieconfig_remplacer' => 'Remplacer le menu actuel par le menu importé',
	'formulaire_ieconfig_renommer' => 'Renommer ce menu avant import',
	'formulaire_importer' => 'Importer un menu',
	'formulaire_importer_explication' => 'Si vous avez exporté un menu dans un fichier, vous pouvez l\'importer maintenant.',
	'formulaire_modifier_entree' => 'Modifier cette entrée',
	'formulaire_modifier_menu' => 'Modifier le menu :',
	'formulaire_nouveau' => 'Nouveau menu',
	'formulaire_partie_construction' => 'Construction du menu',
	'formulaire_partie_identification' => 'Identification du menu',
	'formulaire_supprimer_entree' => 'Supprimer cette entrée',
	'formulaire_supprimer_menu' => 'Supprimer le menu',
	'formulaire_supprimer_sous_menu' => 'Supprimer ce sous-menu',
	'formulaire_titre' => 'Titre',

	// I
	'info_afficher_articles' => 'Les articles seront inclus dans le menu.',
	'info_articles_max' => 'Seulement si la rubrique contient au plus @max@ articles',
	'info_articles_max_affiches' => 'Affichage limit&eacute; &agrave; @max@ articles',
	'info_connexion_obligatoire' => 'Connexion obligatoire',
	'info_deconnexion_obligatoire' => 'Uniquement déconnecté',
	'info_masquer_articles_uniques' => 'Articles uniques masqu&eacute;s',
	'info_numero_menu' => 'MENU NUMÉRO :',
	'info_page_speciale' => 'Lien vers la page « @page@ »',
	'info_page_speciale_zajax' => 'Modalbox de la page « @page@ » pour le bloc « @bloc@ &#187',
	'info_sousrub_cond' => 'Seules les sous-rubriques de la rubriques en cours sont affichées.',
	'info_tous_groupes_mots' => 'Tous les groupes de mots',
	'info_traduction_recuperee' => 'Le contexte décidera de la traduction choisie',
	'info_tri' => 'Tri :',
	'info_tri_alpha' => '(alphabétique)',
	'info_tri_num' => '(numérique)',

	// N
	'noisette_description' => 'Insère un menu défini avec le plugin Menus.',
	'noisette_label_afficher_titre_menu' => 'Afficher le titre du menu ?',
	'noisette_label_identifiant' => 'Menu à afficher :',
	'noisette_nom_noisette' => 'Menu',
	'nom_menu_accueil' => 'Accueil',
	'nom_menu_articles_rubrique' => 'Articles d\'une rubrique',
	'nom_menu_deconnecter' => 'Se déconnecter',
	'nom_menu_espace_prive' => 'Se connecter / lien vers espace privé',
	'nom_menu_groupes_mots' => 'Mots-clés et Articles d\'un Groupes de mots',
	'nom_menu_lien' => 'Lien arbitraire',
	'nom_menu_mapage' => 'Ma page',
	'nom_menu_mots' => 'Articles d\'un Mot-clé',
	'nom_menu_objet' => 'Article, rubrique ou autre objet SPIP',
	'nom_menu_page_speciale' => 'Lien vers un squelette page',
	'nom_menu_page_speciale_zajax' => 'Un bloc d\'une page Zpip',
	'nom_menu_rubriques' => 'Liste ou arborescence de rubriques et d\'articles',
	'nom_menu_rubriques_evenements' => 'Événements de rubriques',
	'nom_menu_secteurlangue' => 'Secteurs de langue',
	'nom_menu_texte_libre' => 'Texte libre',
	
	// T
	'tous_les_articles' => '... Tous les articles'
);

?>
