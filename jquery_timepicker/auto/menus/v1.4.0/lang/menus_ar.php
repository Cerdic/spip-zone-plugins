<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/menus?lang_cible=ar
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'confirmer_supprimer_entree' => 'هل تريد فعلاً حذف هذا البند؟',

	// D
	'description_menu_accueil' => 'رابط الى الصفحة الأساسية في الموقع.',
	'description_menu_articles_rubrique' => 'عرض لائحة المقالات في قسم.',
	'description_menu_deconnecter' => 'اذا كان هناك زائر متصل، يضيف بنداً يعرض عليه الخروج.',
	'description_menu_espace_prive' => 'Lien permettant de se connecter au site si on ne l\'est pas déjà, puis d\'aller dans l\'espace privé si l\'on y est autorisé.', # NEW
	'description_menu_groupes_mots' => 'Affiche automatiquement un menu listant les mots du groupe et les articles liés. Par défaut, affiche la liste des groupes de mots et les mots liés. Si un squelette groupes_mots.html existe, le lien vers le groupe est utilisé.', # NEW
	'description_menu_lien' => 'Ajoute un lien arbitraire, en interne (URL relative) ou externe (http://...).', # NEW
	'description_menu_mapage' => 'Si le visiteur est connecté, ajoute un lien vers sa page auteur.', # NEW
	'description_menu_mots' => 'Affiche automatiquement un menu listant les articles liés au mot clé.', # NEW
	'description_menu_objet' => 'Crée un lien vers un objet de SPIP : article, rubrique ou autre. Par défaut, l\'entrée aura le titre de l\'objet.', # NEW
	'description_menu_page_speciale' => 'Ajoute un lien vers un squelette page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ces pages sont souvent fournies par des plugins.', # NEW
	'description_menu_page_speciale_zajax' => 'Ajoute un lien vers un bloc d\'une page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ceci nécéssite une squelette de type Z et le plugin <a href="http://www.spip-contrib.net/MediaBox">médiabox</a>.', # NEW
	'description_menu_rubriques_articles' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques et les articles sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement). Les articles sont placés systématiquement après les rubriques.', # NEW
	'description_menu_rubriques_completes' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement).', # NEW
	'description_menu_secteurlangue' => 'Cette entrée est spécifique aux sites utilisant un secteur par langue. Elle affiche automatiquement un menu listant les rubriques du secteur correspondant à la langue de la page et, si on veut, les sous-rubriques sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement).', # NEW
	'description_menu_texte_libre' => 'Simplement le texte que vous souhaitez, ou un code de langue SPIP (<:...:>)', # NEW

	// E
	'editer_menus_editer' => 'تعديل هذه القائمة',
	'editer_menus_explication' => 'إنشاء قوائم الموقع وإعدادها.',
	'editer_menus_exporter' => 'نقل هذه القائمة',
	'editer_menus_nouveau' => 'إنشاء قائمة جديدة',
	'editer_menus_titre' => 'قوائم الموقع',
	'entree_afficher_articles' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)', # NEW
	'entree_afficher_item_suite' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)', # NEW
	'entree_articles_max' => 'Si oui, afficher les articles seulement si la rubrique contient au maximum xx articles ? (mettre le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_articles_max_affiches' => 'Si oui, limiter le nombre d\'articles listés à xx maximum (suivis d\'un item "... Tous les articles" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_aucun' => 'بدون',
	'entree_bloc' => 'كتلة Zpip',
	'entree_choisir' => 'اختر نوع البند الذي تريد إضافته:',
	'entree_classe_parent' => 'Classe des liens des éléments parents. Cette classe sera rajoutée aux li>a ayant une suite ul/li. Par exemple, si vous saisissez "daddy", cela vous permet d\'utiliser le plugin menu deroulant 2 pour la mise en forme du menu.', # NEW
	'entree_connexion_objet' => 'Obliger à être connecté (mettre "session") ou déconnecté (mettre "nosession") pour voir l\'objet', # NEW
	'entree_contenu' => 'المحتوى',
	'entree_css' => 'Classes CSS de l\'entrée (du conteneur)', # NEW
	'entree_css_lien' => 'Classes CSS du lien', # NEW
	'entree_id_groupe' => 'رقم مجموعة المفاتيح',
	'entree_id_mot' => 'رقم المفتاح',
	'entree_id_objet' => 'رقم',
	'entree_id_rubrique' => 'رقم القسم الحاوي',
	'entree_id_rubrique_ou_courante' => 'Numéro de la rubrique parente ou "courante" si la rubrique parente est la rubrique courante du contexte', # NEW
	'entree_id_rubriques_exclues' => 'Numéros des rubriques à exclure, séparés par des virgules', # NEW
	'entree_id_secteur_exclus' => 'Numéros des secteurs à exclure, séparés par des virgules', # NEW
	'entree_infini' => 'الى ما لا نهاية',
	'entree_mapage' => 'صفحتي الشخصية',
	'entree_masquer_articles_uniques' => 'Si oui et si une rubrique contient un seul article, le masquer ? (mettre "oui" pour cela)', # NEW
	'entree_niveau' => 'مستوى القوائم الفرعية',
	'entree_nombre_articles' => 'العدد الأقصى للمقالات (صفر افتراضياً)',
	'entree_page' => 'اسم الصفحة',
	'entree_parametres' => 'لائحة المتغيرات',
	'entree_rubriques_max_affichees' => 'Si oui, limiter le nombre de rubriques listés à xx maximum (suivis d\'un item "... Toutes les rubriques" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum de rubriques, laissez vide pour afficher toutes les rubriques)', # NEW
	'entree_sousrub_cond' => 'N\'afficher que les sous-rubriques de la rubrique en cours (mettre "oui", sinon laisser vide)', # NEW
	'entree_suivant_connexion' => 'Restreindre cette entrée suivant la connexion (mettre "connecte" pour afficher seulement si le visiteur est connecté, "deconnecte" pour le cas contraire, ou laisser vide pour toujours afficher)', # NEW
	'entree_suivant_connexion_connecte' => 'اذا متصل فقط',
	'entree_suivant_connexion_deconnecte' => 'اذا غير متصل فقط',
	'entree_sur_n_articles' => '@n@ مقال معروض',
	'entree_sur_n_mots' => '@n@ مفتاح معروض',
	'entree_sur_n_niveaux' => 'على @n@ مستوى',
	'entree_titre' => 'العنوان',
	'entree_titre_connecter' => 'العنوان للوصول الى استمارة التعريف',
	'entree_titre_prive' => 'العنوان للدخول الى المجال الخاص',
	'entree_traduction_articles_rubriques' => 'Dans la mesure du possible, afficher les articles de la rubrique dans la langue du contexte (mettre "trad" pour cela)', # NEW
	'entree_traduction_objet' => 'Dans le cas d\'un article, choisir la traduction en fonction du contexte (mettre "trad" pour cela)', # NEW
	'entree_tri_alpha' => 'Critère de tri des rubriques (alphabétique). Si vous saisissez "date", le critère ajouté sera {par date} et les rubriques seront triées par date', # NEW
	'entree_tri_alpha_articles' => 'Critère de tri des articles (alphabétique). Si vous saisissez "date", le critère ajouté sera {par date} et les articles seront triés par date', # NEW
	'entree_tri_alpha_articles_inverse' => 'Inverser le critère de tri alphabétique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_alpha_inverse' => 'Inverser le critère de tri alphabétique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_num' => 'Critère de tri des rubriques (numérique). Si vous saisissez "titre", le critère ajouté sera {par num titre} et les rubriques seront triées par numéro de titre', # NEW
	'entree_tri_num_articles' => 'Critère de tri des articles (numérique). Si vous saisissez "titre", le critère ajouté sera {par num titre} et les articles seront triés par numéro de titre', # NEW
	'entree_tri_num_articles_inverse' => 'Inverser le critère de tri numérique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_num_inverse' => 'Inverser le critère de tri numérique ? (mettre "oui" pour cela)', # NEW
	'entree_type_objet' => 'نوع العنصر',
	'entree_url' => 'العنوان',
	'entree_url_public' => 'Adresse de retour après la connexion', # NEW
	'erreur_aucun_type' => 'لم يتم العثور على اي نوع بنود.',
	'erreur_autorisation' => 'غير مسموح لك تعديل القوائم.',
	'erreur_identifiant_deja' => 'Cet identifiant est déjà utilisé par un menu.', # NEW
	'erreur_identifiant_forme' => 'L\'identifiant ne doit contenir que des lettres, des chiffres ou le caractère souligné.', # NEW
	'erreur_menu_inexistant' => 'لا وجود للقائمة رقم @id@ المطلوبة.',
	'erreur_mise_a_jour' => 'Une erreur s\'est produite pendant la mise à jour de la base de donnée.', # NEW
	'erreur_parametres' => 'Il y a une erreur dans les paramètres de la page', # NEW
	'erreur_type_menu' => 'يجب اختيار نوع قوائم',
	'erreur_type_menu_inexistant' => 'Ce type de menu n\'est pas/plus disponible', # NEW

	// F
	'formulaire_ajouter_entree' => 'إضافة بند',
	'formulaire_ajouter_sous_menu' => 'إنشاء قائمة فرعية',
	'formulaire_css' => 'Classes CSS', # NEW
	'formulaire_css_explication' => 'Vous pouvez ajouter au menu d\'éventuelles classes CSS supplémentaires.', # NEW
	'formulaire_deplacer_bas' => 'نقل الى الأسفل',
	'formulaire_deplacer_haut' => 'نقل الى الأعلى',
	'formulaire_facultatif' => 'اختياري',
	'formulaire_identifiant' => 'المعرف',
	'formulaire_identifiant_explication' => 'Donnez un mot-clé unique qui vous permettra d\'appeler votre menu facilement.', # NEW
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Choisissez quel(s) menu(s) vous souhaitez importer.', # NEW
	'formulaire_ieconfig_importer' => 'جلب',
	'formulaire_ieconfig_menu_meme_identifiant' => 'ATTENTION : un menu avec le même identifiant existe déjà sur votre votre site !', # NEW
	'formulaire_ieconfig_menus_a_exporter' => 'قوائم للنقل:',
	'formulaire_ieconfig_ne_pas_importer' => 'عدم الجلب',
	'formulaire_ieconfig_remplacer' => 'Remplacer le menu actuel par le menu importé', # NEW
	'formulaire_ieconfig_renommer' => 'إعادة تسمية هذه القائمة قبل جلبها',
	'formulaire_importer' => 'جلب قائمة',
	'formulaire_importer_explication' => 'Si vous avez exporté un menu dans un fichier, vous pouvez l\'importer maintenant.', # NEW
	'formulaire_modifier_entree' => 'تعديل هذا البند',
	'formulaire_modifier_menu' => 'تعديل القائمة',
	'formulaire_nouveau' => 'قائمة جديدة',
	'formulaire_partie_construction' => 'بناء قائمة',
	'formulaire_partie_identification' => 'تعريف القائمة',
	'formulaire_supprimer_entree' => 'حذف هذا البند',
	'formulaire_supprimer_menu' => 'حذف القائمة',
	'formulaire_supprimer_sous_menu' => 'حذف هذه القائمة الفرعية',
	'formulaire_titre' => 'العنوان',

	// I
	'info_afficher_articles' => 'Les articles seront inclus dans le menu.', # NEW
	'info_articles_max' => 'Seulement si la rubrique contient au plus @max@ articles', # NEW
	'info_articles_max_affiches' => 'Affichage limité à @max@ articles', # NEW
	'info_classe_parent' => 'Classe des éléments parents : ', # NEW
	'info_connexion_obligatoire' => 'Connexion obligatoire', # NEW
	'info_deconnexion_obligatoire' => 'Uniquement déconnecté', # NEW
	'info_masquer_articles_uniques' => 'Articles uniques masqués', # NEW
	'info_numero_menu' => 'قائمة رقم:',
	'info_page_speciale' => 'Lien vers la page « @page@ »', # NEW
	'info_page_speciale_zajax' => 'Modalbox de la page « @page@ » pour le bloc « @bloc@ &#187', # NEW
	'info_rubrique_courante' => 'القسم الحالي',
	'info_rubriques_exclues' => ' / sauf rubrique(s) @id_rubriques@', # NEW
	'info_rubriques_max_affichees' => 'Affichage limité à @max@ rubriques', # NEW
	'info_secteur_exclus' => ' / sauf secteur(s) @id_secteur@', # NEW
	'info_sousrub_cond' => 'Seules les sous-rubriques de la rubriques en cours sont affichées.', # NEW
	'info_tous_groupes_mots' => 'Tous les groupes de mots', # NEW
	'info_traduction_recuperee' => 'Le contexte décidera de la traduction choisie', # NEW
	'info_tri' => 'فرز الأقسام:',
	'info_tri_alpha' => '(أبجدي)',
	'info_tri_articles' => 'فرز المقالات:',
	'info_tri_num' => '(رقمي)',

	// N
	'noisette_description' => 'Insère un menu défini avec le plugin Menus.', # NEW
	'noisette_label_afficher_titre_menu' => 'عرض عنوان القائمة؟',
	'noisette_label_identifiant' => 'Menu à afficher :', # NEW
	'noisette_nom_noisette' => 'القائمة',
	'nom_menu_accueil' => 'Accueil', # NEW
	'nom_menu_articles_rubrique' => 'مقالات قسم',
	'nom_menu_deconnecter' => 'خروج',
	'nom_menu_espace_prive' => 'Se connecter / lien vers espace privé', # NEW
	'nom_menu_groupes_mots' => 'Mots-clés et Articles d\'un Groupes de mots', # NEW
	'nom_menu_lien' => 'رابط اعتباطي',
	'nom_menu_mapage' => 'صفحتي',
	'nom_menu_mots' => 'مقالات مفتاح',
	'nom_menu_objet' => 'مقال، قسم او أحد عناصر SPIP الأخرى ',
	'nom_menu_page_speciale' => 'رابط الى صفحة نموذجية',
	'nom_menu_page_speciale_zajax' => 'Un bloc d\'une page Zpip', # NEW
	'nom_menu_rubriques_completes' => 'Liste ou arborescence de rubriques et d\'articles (avec beaucoup d\'options)', # NEW
	'nom_menu_rubriques_evenements' => 'Événements de rubriques', # NEW
	'nom_menu_secteurlangue' => 'أقسام اللغات',
	'nom_menu_texte_libre' => 'نص حرّ',

	// T
	'tous_les_articles' => 'كل المقالات',
	'toutes_les_rubriques' => 'كل الأقسام'
);

?>
