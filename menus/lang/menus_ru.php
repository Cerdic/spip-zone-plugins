<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'description_menu_accueil' => 'Ссылка на главную страницу.',
	'description_menu_articles_rubrique' => 'Affiche la liste des articles d\'une rubrique.', # NEW
	'description_menu_deconnecter' => 'Si le visiteur est connecté, ajoute une entrée lui proposant la déconnexion.', # NEW
	'description_menu_espace_prive' => 'Lien permettant de se connecter au site si on ne l\'est pas déjà, puis d\'aller dans l\'espace privé si l\'on y est autorisé.', # NEW
	'description_menu_groupes_mots' => 'Affiche automatiquement un menu listant les mots du groupe et les articles liés. Par d&eacute;faut, affiche la liste des groupes de mots et les mots li&eacute;s. Si un squelette groupes_mots.html existe, le lien vers le groupe est utilis&eacute;.', # NEW
	'description_menu_lien' => 'Ajoute un lien arbitraire, en interne (URL relative) ou externe (http://...).', # NEW
	'description_menu_mapage' => 'Si le visiteur est connecté, ajoute un lien vers sa page auteur.', # NEW
	'description_menu_mots' => 'Affiche automatiquement un menu listant les articles liés au mot clé.', # NEW
	'description_menu_objet' => 'Crée un lien vers un objet de SPIP : article, rubrique ou autre. Par défaut, l\'entrée aura le titre de l\'objet.', # NEW
	'description_menu_page_speciale' => 'Ajoute un lien vers un squelette page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ces pages sont souvent fournies par des plugins.', # NEW
	'description_menu_page_speciale_zajax' => 'Ajoute un lien vers un bloc d\'une page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ceci nécéssite une squelette de type Z et le plugin <a href="http://www.spip-contrib.net/MediaBox">médiabox</a>.', # NEW
	'description_menu_rubriques' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement).', # NEW
	'description_menu_secteurlangue' => 'Cette entrée est spécifique aux sites utilisant un secteur par langue. Elle affiche automatiquement un menu listant les rubriques du secteur correspondant à la langue de la page et, si on veut, les sous-rubriques sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement).', # NEW
	'description_menu_texte_libre' => 'Simplement le texte que vous souhaitez', # NEW

	// E
	'editer_menus_editer' => 'Редактировать это меню',
	'editer_menus_explication' => 'Создать и настроить меню для сайта',
	'editer_menus_exporter' => 'Экспортировать меню',
	'editer_menus_nouveau' => 'Создать новое меню',
	'editer_menus_titre' => 'Меню сайта',
	'entree_aucun' => 'Aucun', # NEW
	'entree_bloc' => 'Bloc Zpip', # NEW
	'entree_choisir' => 'Выбрать тип пункта меню:',
	'entree_connexion_objet' => 'Obliger à être connecté (mettre "session") ou déconnecté (mettre "nosession") pour voir l\'objet', # NEW
	'entree_contenu' => 'Contenu', # NEW
	'entree_css' => 'CSS класс', # MODIF
	'entree_css_lien' => 'Classes CSS du lien', # NEW
	'entree_id_groupe' => 'Numéro du groupe de mot clé', # NEW
	'entree_id_mot' => 'Numéro du mot clé', # NEW
	'entree_id_objet' => 'Номер',
	'entree_id_rubrique' => 'Номер родительского раздела',
	'entree_infini' => 'Бесконечность',
	'entree_mapage' => 'Моя страница',
	'entree_niveau' => 'Уровень подразделов',
	'entree_nombre_articles' => 'Nombre d\'articles au maximum (0 par défaut)', # NEW
	'entree_page' => 'Nom de la page', # NEW
	'entree_parametres' => 'Liste des paramètres', # NEW
	'entree_sur_n_articles' => '@n@ articles affiché(s)', # NEW
	'entree_sur_n_mots' => '@n@ mots affiché(s)', # NEW
	'entree_sur_n_niveaux' => 'На @n@ уровне',
	'entree_titre' => 'Название',
	'entree_titre_connecter' => 'Titre pour l\'accès au formulaire d\'identification', # NEW
	'entree_titre_prive' => 'Titre pour accéder à l\'espace privé', # NEW
	'entree_traduction_objet' => 'Dans le cas d\'un article, choisir la traduction en fonction du contexte (mettre "trad" pour cela)', # NEW
	'entree_tri_alpha' => 'Critère de tri (alphabétique)', # NEW
	'entree_tri_num' => 'Critère de tri (numérique)', # NEW
	'entree_type_objet' => 'Тип объекта',
	'entree_url' => 'Ссылка (URL)',
	'entree_url_public' => 'Adresse de retour après la connexion', # NEW
	'erreur_aucun_type' => 'Ничего не найдено.',
	'erreur_autorisation' => 'У вас нет прав для редактирования этого меню.',
	'erreur_identifiant_deja' => 'Этот идентификатор уже используется в другом меню.',
	'erreur_identifiant_forme' => 'Идентификатор может состоять из латинских букв, цифр и подчеркиваний.',
	'erreur_menu_inexistant' => 'Меню номер @id@ не существует.',
	'erreur_mise_a_jour' => 'Произошла ошибка при обновлении базы.',
	'erreur_parametres' => 'Il y a une erreur dans les paramètres de la page', # NEW
	'erreur_type_menu' => 'Vous devez choisir un type de menu', # NEW

	// F
	'formulaire_ajouter_entree' => 'Добавить пункт меню',
	'formulaire_ajouter_sous_menu' => 'Создать подменю',
	'formulaire_css' => 'CSS классы',
	'formulaire_css_explication' => 'Вы можете добавить дополнительный классы CSS к вашему меню.',
	'formulaire_deplacer_bas' => 'Вниз',
	'formulaire_deplacer_haut' => 'Вверх',
	'formulaire_facultatif' => 'Не обязательно',
	'formulaire_identifiant' => 'Идентификатор',
	'formulaire_identifiant_explication' => 'Назначьте меню уникальное ключевое слово, которое позволит в дальнейшем легко его вызывать.',
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Choisissez quel(s) menu(s) vous souhaitez importer.', # NEW
	'formulaire_ieconfig_importer' => 'Importer', # NEW
	'formulaire_ieconfig_menu_meme_identifiant' => 'ATTENTION : un menu avec le même identifiant existe déjà sur votre votre site !', # NEW
	'formulaire_ieconfig_menus_a_exporter' => 'Menus à exporter :', # NEW
	'formulaire_ieconfig_ne_pas_importer' => 'Ne pas importer', # NEW
	'formulaire_ieconfig_remplacer' => 'Remplacer le menu actuel par le menu importé', # NEW
	'formulaire_ieconfig_renommer' => 'Renommer ce menu avant import', # NEW
	'formulaire_importer' => 'Импортировать меню',
	'formulaire_importer_explication' => 'Если вы экспортировали меню в файл, вы можете импортировать его сейчас.',
	'formulaire_modifier_entree' => 'Редактировать этот пункт меню',
	'formulaire_modifier_menu' => 'Редактировать меню:',
	'formulaire_nouveau' => 'Новое меню',
	'formulaire_partie_construction' => 'Создание меню',
	'formulaire_partie_identification' => 'Идентификатор меню',
	'formulaire_supprimer_entree' => 'Удалить этот пункт меню',
	'formulaire_supprimer_menu' => 'Удалить меню',
	'formulaire_supprimer_sous_menu' => 'Удалить подменю',
	'formulaire_titre' => 'Название',

	// I
	'info_connexion_obligatoire' => 'Connexion obligatoire', # NEW
	'info_deconnexion_obligatoire' => 'Uniquement déconnecté', # NEW
	'info_numero_menu' => 'НОМЕР МЕНЮ:',
	'info_page_speciale' => 'Lien vers la page « @page@ »', # NEW
	'info_page_speciale_zajax' => 'Modalbox de la page « @page@ » pour le bloc « @bloc@ &#187', # NEW
	'info_tous_groupes_mots' => 'Tous les groupes de mots', # NEW
	'info_traduction_recuperee' => 'Le contexte décidera de la traduction choisie', # NEW
	'info_tri' => 'Tri :', # NEW
	'info_tri_alpha' => '(alphabétique)', # NEW
	'info_tri_num' => '(numérique)', # NEW

	// N
	'noisette_description' => 'Insère un menu défini avec le plugin Menus.', # NEW
	'noisette_label_afficher_titre_menu' => 'Afficher le titre du menu ?', # NEW
	'noisette_label_identifiant' => 'Menu à afficher :', # NEW
	'noisette_nom_noisette' => 'Menu', # NEW
	'nom_menu_accueil' => 'Главная страница',
	'nom_menu_articles_rubrique' => 'Articles d\'une rubrique', # NEW
	'nom_menu_deconnecter' => 'Se déconnecter', # NEW
	'nom_menu_espace_prive' => 'Se connecter / lien vers espace privé', # NEW
	'nom_menu_groupes_mots' => 'Mots-clés et Articles d\'un Groupes de mots', # NEW
	'nom_menu_lien' => 'Lien arbitraire', # NEW
	'nom_menu_mapage' => 'Ma page', # NEW
	'nom_menu_mots' => 'Articles d\'un Mot-clé', # NEW
	'nom_menu_objet' => 'Article, rubrique ou autre objet SPIP', # NEW
	'nom_menu_page_speciale' => 'Lien vers un squelette page', # NEW
	'nom_menu_page_speciale_zajax' => 'Un bloc d\'une page Zpip', # NEW
	'nom_menu_rubriques' => 'Liste ou arborescence de rubriques', # NEW
	'nom_menu_rubriques_evenements' => 'Événements de rubriques', # NEW
	'nom_menu_secteurlangue' => 'Secteurs de langue', # NEW
	'nom_menu_texte_libre' => 'Texte libre' # NEW
);

?>
