<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucunmodule' => 'لا توجد اي وحدة.',

	// B
	'bouton_activer_lang' => 'تفعيل اللغة "@lang@" لهذه الوحدة',
	'bouton_supprimer_module' => 'حذف هذه الوحدة',
	'bouton_traduire' => 'ترجمة',

	// C
	'cfg_form_tradlang_autorisations' => 'الأذونات',
	'cfg_inf_type_autorisation' => 'اذا اخترت حسب الوضعية او المؤلف، سيطلب منك أدناه خيارك للوضعية او المؤلف.',
	'cfg_lbl_autorisation_auteurs' => 'السماح حسب قائمة المؤلفين',
	'cfg_lbl_autorisation_statuts' => 'السماح حسب وضعية المؤلفين',
	'cfg_lbl_autorisation_webmestre' => 'السماح للمشرفين على الموقع فقط',
	'cfg_lbl_liste_auteurs' => 'مؤلفو الموقع',
	'cfg_lbl_statuts_auteurs' => 'الوضعيات المتاحة',
	'cfg_lbl_type_autorisation' => 'أسلوب السماح',
	'cfg_legend_autorisation_configurer' => 'إدارة الملحق',
	'cfg_legende_autorisation_modifier' => 'تعديل الترجمات',
	'cfg_legende_autorisation_voir' => 'عرض واجهة الترجمة',
	'codelangue' => 'رمز اللغة',
	'crayon_changer_statut' => 'تنبيه! قمت بتعديل محتوى السلسلة دون تغيير وضعيتها.',

	// E
	'entrerlangue' => 'إضافة رمز لغة',
	'erreur_aucun_item_langue_mere' => 'لا تحتوي اللغة الأم "@lang_mere@" على أي بند لغة.',
	'erreur_aucun_module' => 'لا توجد اي وحدة متاحة في قاعدة البيانات.',
	'erreur_autorisation_modifier_modules' => 'لا يحق لك ترجمة وحدات اللغات.',
	'erreur_autoriser_profil' => 'Vous n\'êtes pas autorisé à modifier ce profil.', # NEW
	'erreur_choisir_lang_cible' => 'اختر لغة تكون هدفاً للترجمة.',
	'erreur_choisir_lang_orig' => 'اختر لغة تكون مصدراً للترجمة.',
	'erreur_choisir_module' => 'اختر وحدة للترجمة.',
	'erreur_code_langue_existant' => 'يوجد هذا النوع من اللغة لهذه الوحدة ',
	'erreur_code_langue_invalide' => 'هذا رمز لغة غير صالح',
	'erreur_langues_autorisees_insuffisantes' => 'يجب تحديد لغتين على الأقل',
	'erreur_langues_differentes' => 'اختر لغة هدف مختلفة عن اللغة الأصلية',
	'erreur_module_inconnu' => 'هذه الوحدة غير متوافرة',
	'erreur_pas_langue_cible' => 'اختر لغة لتكون هدفاً',
	'erreur_repertoire_local_inexistant' => 'تنبيه: مجلد "squelettes/lang" لحفظ النسخة المحلية غير موجود',
	'explication_langue_cible' => 'اللغة التي تترجم اليها.',
	'explication_langue_origine' => 'اللغة التي تترجم منها (اللغات الكاملة ١٠٠٪ هي الوحيدة المتوافرة).',
	'explication_langues_autorisees' => 'لا يمكن للمستخدمين إنشاء ترجمات جديدة الا للغات المحددة.',
	'explication_limiter_langues_bilan' => 'Par défaut, @nb@ langues seront affichées, si les utilisateurs n\'ont pas sélectionné de langues préférées dans leur profil.', # NEW
	'explication_limiter_langues_bilan_nb' => 'Combien de langues seront affichées par défaut (les langues les plus traduites seront sélectionnées).', # NEW
	'explication_sauvegarde_locale' => 'سيحفظ الملفات في مجلد squelettes في الموقع',
	'explication_sauvegarde_post_edition' => 'سيحفظ الملفات المؤقتة لدى كل تعديل في سلسلة لغة',

	// I
	'icone_modifier_tradlang' => 'تعديل سلسلة اللغة هذه',
	'icone_modifier_tradlang_module' => 'تعديل وحدة اللغة هذه',
	'importer_module' => 'جلب وحدة لغة جديدة',
	'importermodule' => 'جلب وحدة',
	'info_1_tradlang' => '@nb@ سلسلة لغة',
	'info_1_tradlang_module' => 'سلسلة لغة واحدة',
	'info_aucun_tradlang_module' => 'لا توجد اي سلسلة لغة',
	'info_chaine_jamais_modifiee' => 'لم يتم تعديل هذه السلسلة أبداً.',
	'info_chaine_originale' => 'هذه السلسلة هي السلسلة الأصلية',
	'info_filtrer_status' => 'الترشيح حسب الوضعية:',
	'info_langue_mere' => '(اللغة الأم)',
	'info_langues_non_preferees' => 'Autres langues :', # NEW
	'info_langues_preferees' => 'Langue(s) préférée(s) :', # NEW
	'info_module_traduction' => '@total@ @statut@ (@percent@%)',
	'info_module_traduit_pc' => 'وحدة مترجمة بنسبة @pc@٪',
	'info_module_traduit_pc_lang' => 'Module "@module@" traduit à @pc@% en @lang@ (@langue_longue@)', # NEW
	'info_modules_priorite_traduits_pc' => 'Les modules de priorité "@priorite@" sont traduits à @pc@% en @lang@', # NEW
	'info_nb_items_module' => '@nb@ items dans le modules "@module@"', # NEW
	'info_nb_items_module_modif' => '@nb@ items du module "@module@" sont modifiés et à vérifier en @lang@ (@langue_longue@)"', # NEW
	'info_nb_items_module_modif_aucun' => 'Aucun item du module "@module@" n\'est modifié et à vérifier en @lang@ (@langue_longue@)', # NEW
	'info_nb_items_module_modif_un' => 'Un item du module "@module@" est modifié et à vérifier en @lang@ (@langue_longue@)"', # NEW
	'info_nb_items_module_new' => '@nb@ items du module "@module@" sont à traduire en @lang@ (@langue_longue@)"', # NEW
	'info_nb_items_module_new_aucun' => 'Aucun item du module "@module@" n\'est à traduire en @lang@ (@langue_longue@)', # NEW
	'info_nb_items_module_new_un' => 'Un item du module "@module@" est à traduire en @lang@ (@langue_longue@)"', # NEW
	'info_nb_items_module_ok' => '@nb@ items du module "@module@" sont traduits en @lang@ (@langue_longue@)"', # NEW
	'info_nb_items_module_ok_aucun' => 'Aucun item du module "@module@" n\'est traduit en @lang@ (@langue_longue@)', # NEW
	'info_nb_items_module_ok_un' => 'Un item du module "@module@" est traduit en @lang@ (@langue_longue@)"', # NEW
	'info_nb_items_priorite' => 'Les modules de priorité "@priorite@" ont @nb@ items', # NEW
	'info_nb_items_priorite_modif' => '@pc@% des items de priorité "@priorite@" sont modifiés et à vérifier en @lang@ (@langue_longue@)', # NEW
	'info_nb_items_priorite_new' => '@pc@% des items de priorité "@priorite@" sont nouveaux en @lang@ (@langue_longue@)', # NEW
	'info_nb_items_priorite_ok' => 'Les modules de priorité "@priorite@" sont traduits à @pc@% en @lang@ (@langue_longue@)', # NEW
	'info_nb_tradlang' => '@nb@ سلسلة لغة',
	'info_nb_tradlang_module' => '@nb@ وحدة لغة',
	'info_percent_chaines' => '@traduites@ / @total@ chaines traduites', # NEW
	'info_status_ok' => 'موافق',
	'info_str' => 'نص سلسلة اللغة',
	'info_traduire_module_lang' => 'Traduire le module "@module@" en @langue_longue@ (@lang@)', # NEW
	'infos_trad_module' => 'معلومات حول الترجمات',
	'item_creer_langue_cible' => 'إنشاء هدف جديد',
	'item_langue_cible' => 'اللغة الهدف: ',
	'item_langue_origine' => 'اللغة المصدر:',
	'item_manquant' => 'هناك بند واحد ناقص في هذه اللغة (بالنسبة الى اللغة الأم)',
	'items_en_trop' => 'هناك @nb@ بند زائد في هذه اللغة (بالنسبة الى اللغة الأم)',
	'items_manquants' => 'هناك @nb@ بند ناقص في هذه اللغة (بالنسبة الى اللغة الأم)',
	'items_modif' => 'البنود المعدلة',
	'items_new' => 'البنود الجديدة',
	'items_total_nb' => 'العدد الإجمالي للبنود',

	// L
	'label_id_tradlang' => 'معرّف السلسلة',
	'label_idmodule' => 'رقم الوحدة',
	'label_lang' => 'اللغة',
	'label_langue_mere' => 'اللغة الأم',
	'label_langues_autorisees' => 'عدم السماح الا بلغات محددة',
	'label_langues_preferees_auteur' => 'Vos ou votre langue(s) préférée(s)', # NEW
	'label_langues_preferees_autre' => 'Ses ou sa langue(s) préférée(s)', # NEW
	'label_limiter_langues_bilan' => 'Limiter le nombre de langues visibles dans le bilan', # NEW
	'label_limiter_langues_bilan_nb' => 'Nombre de langues', # NEW
	'label_nommodule' => 'اسم الوحدة',
	'label_priorite' => 'الأولوية',
	'label_proposition_google_translate' => 'اقتراح ترجمة غوغل',
	'label_recherche_module' => 'في الوحدة: ',
	'label_recherche_status' => 'بوضعية: ',
	'label_repertoire_module_langue' => 'مجلد الوحدة',
	'label_sauvegarde_locale' => 'السماح بحفظ الملفات محلياً',
	'label_sauvegarde_post_edition' => 'حفظ الملفات لدى كل تعديل',
	'label_synchro_base_fichier' => 'مزامنة قاعدة البيانات والملفات',
	'label_texte' => 'وصف الوحدة',
	'label_tradlang_comm' => 'تعليق',
	'label_tradlang_status' => 'وضعية الترجمة',
	'label_tradlang_str' => 'سلسلة مترجمة',
	'label_update_langues_cible_mere' => 'تحديث هذه اللغة في قاعدة البيانات',
	'label_version_originale' => 'السلسلة الأصلية (@lang@)',
	'label_version_originale_comm' => 'تعليق النسخة الأصلية (@lang@)',
	'label_version_selectionnee' => 'سلسلة في اللغة المحددة (@lang@)',
	'label_version_selectionnee_comm' => 'تعليق في اللغة المحددة (@lang@)',
	'languesdispo' => 'اللغات المتوافرة',
	'legend_conf_bilan' => 'Affichage du bilan', # NEW
	'lien_accueil_interface' => 'الصفحة الأساسية لواجهة الترجمة',
	'lien_aide_recherche' => 'تعليمات البحث',
	'lien_aucun_status' => 'لا يوجد',
	'lien_bilan' => 'جردة بالترجمات الحالية.',
	'lien_code_langue' => 'رمز لغة غير صالح. يجب ان يحتوي رمز اللغة على حرفين على الأقل (مقياس ISO-631)',
	'lien_confirm_export' => 'التأكيد على نقل الملف الحالي (يعني انه يحل مكان الملف @fichier@)',
	'lien_editer_chaine' => 'تعديل',
	'lien_export' => 'نقل الملف الحالي آلياً.',
	'lien_page_depart' => 'رجوع الى الصفحة الأساسية؟',
	'lien_profil_auteur' => 'Votre profil', # NEW
	'lien_profil_autre' => 'Son profil', # NEW
	'lien_proportion' => 'نسبة السلاسل المعروضة',
	'lien_recharger_page' => 'إعادة تحميل الصفحة.',
	'lien_recherche_avancee' => 'بحث متطور',
	'lien_retour' => 'رجوع',
	'lien_retour_module' => 'رجوع الى الوحدة "@module@"',
	'lien_retour_page_auteur' => 'Revenir à votre page', # NEW
	'lien_retour_page_auteur_autre' => 'Revenir à sa page', # NEW
	'lien_revenir_traduction' => 'رجوع الى صفحة الترجمة',
	'lien_sauvegarder' => 'حفظ/استرجاع الملف الحالي.',
	'lien_telecharger' => '[تحميل]',
	'lien_traduction_module' => 'وحدة ',
	'lien_traduction_vers' => ' نحو ',
	'lien_trier_langue_non' => 'عرض الجردة الاجمالية.',
	'lien_utiliser_google_translate' => 'استخدام هذا الاصدار',
	'lien_voir_toute_chaines_module' => 'عرض كل سلاسل الوحدة.',

	// M
	'menu_info_interface' => 'يعرض رابط يقود الى واجهة الترجمة',
	'menu_titre_interface' => 'واجهة الترجمة',
	'message_aucun_resultat_chaine' => 'لم تسترجع معايير بحثك في سلاسل اللغة أية نتائج.',
	'message_aucun_resultat_statut' => 'لا توجد اي سلسلة تناسب الوضعية المطلوبة.',
	'message_aucune_nouvelle_langue_dispo' => 'هذه الوحدة متوافرة في كل اللغات الممكنة',
	'message_confirm_redirection' => 'ستتم إعادة توجيهك الى تعديل الوحدة',
	'message_demande_update_langues_cible_mere' => 'يمكنك الطلب من مدير إعادة تزامن هذه اللغة مع اللغة الأساسية.',
	'message_info_choisir_langues_profiles' => 'Vous pouvez séléctionner vos langues préférées <a href="@url_profil@">dans votre profil</a> pour les afficher par défaut.', # NEW
	'message_langues_choisies_affichees' => 'Seules les langues que vous avez choisies sont affichées : @langues@', # NEW
	'message_langues_preferees_affichees' => 'Seules vos langues préférées sont affichées : @langues@', # NEW
	'message_langues_utilisees_affichees' => 'Seules les @nb@ langues les plus utilisées sont affichées : @langues@', # NEW
	'message_module_langue_ajoutee' => 'تمت إضافة اللغة "@langue@" الى الوحدة "@module@".',
	'message_module_updated' => 'تم تحديث وحدة اللغة "@module@".',
	'message_passage_trad' => 'ننتقل الى الترجمة',
	'message_passage_trad_creation_lang' => 'ننشئ اللغة @lang@ ثم ننتقل الى الترجمة',
	'message_suppression_module_ok' => 'تم حذف الوحدة @module@.',
	'message_suppression_module_trads_ok' => 'تم حذف الوحدة @module@. تم أيضاً حذف @nb@ بند ترجمة عائد لها.',
	'message_synchro_base_fichier_ok' => 'تم تزامن الملف وقاعدة البيانات.',
	'message_synchro_base_fichier_pas_ok' => 'لا يوجد اي تزامن بين الملف وقاعدة البيانات.',
	'module_deja_importe' => 'تم جلب الوحدة "@module@" مسبقاً',
	'moduletitre' => 'الوحدات المتوافرة',

	// N
	'nb_items_langue_cible' => 'تحتوي اللغة الهدف "@langue@" على @nb@ بند محدد من اللغة الأم.',
	'nb_items_langue_en_trop' => 'هناك @nb@ بند زائد في اللغة "@langue@".',
	'nb_items_langue_inexistants' => 'هناك @nb@ بند لا وجود لها في اللغة "@langue@".',
	'nb_items_langue_mere' => 'تحتوي اللغة الأساسية لهذه الوحدة على @nb@ بند.',

	// R
	'readme' => 'يتيح هذا الملحق إدارة ملفات اللغات',

	// S
	'str_status_modif' => 'معدْل (MODIF)',
	'str_status_new' => 'جديد (NEW)',
	'str_status_traduit' => 'مترجم',

	// T
	'texte_contacter_admin' => 'اتصل بمدير اذا كنت راغباً في المشاركة.',
	'texte_erreur' => 'خطأ',
	'texte_erreur_acces' => '<b>:تنبيه</b> لا يمكن الكتابة في الملف <tt>@fichier_lang@</tt>. تأكد من أذونات الدخول.',
	'texte_existe_deja' => ' موجود مسبقاً.',
	'texte_explication_langue_cible' => 'في ما يتعلق باللغة الهدف، يجب ان تختار اذا كنت تترجم الى لغة موجودة او اذا كنت تنشئ لغة جديدة.',
	'texte_export_impossible' => 'لا يمكن نقل الملف. تأكد من أذونات الكتابة في الملف @cible@',
	'texte_filtre' => 'المرشح (بحث)',
	'texte_inscription_ou_login' => 'يجب انشاء حساب في الموقع او التعريف بنفسك للوصول الى الترجمة.',
	'texte_interface' => 'واجهة الترجمة: ',
	'texte_interface2' => 'واجهة الترجمة',
	'texte_langue' => 'اللغة:',
	'texte_langue_cible' => 'اللغة الهدف التي هي اللغة التي تترجم اليها؛',
	'texte_langue_origine' => 'اللغة المصدر التي ستكون النموذج (فضّل اللغة الأم اذا امكن)؛',
	'texte_langues_differentes' => 'يجب ان تكون اللغة الهدف واللغة المصدر مختلفتين.',
	'texte_modifier' => 'تعديل',
	'texte_module' => 'وحدة اللغة المطلوب ترجمتها؛',
	'texte_module_traduire' => 'الوحدة المطلوب ترجمتها:',
	'texte_non_traduit' => 'غير مترجم ',
	'texte_operation_impossible' => 'عملية مستحيلة. عندما تكون خانة "تحديد الكل" محددة،<br>يجب تنفيذ عمليات من نوع "معاينة".',
	'texte_pas_autoriser_traduire' => 'لا تتمتع بالأذونات الضرورية للوصول الى الترجمات.',
	'texte_pas_de_reponse' => '...لا توجد ردود',
	'texte_recapitulatif' => 'موجز الترجمات',
	'texte_restauration_impossible' => 'لا يمكن استرجاع الملف',
	'texte_sauvegarde' => 'واجهة الترجمة، حفظ/استرجاع الملف',
	'texte_sauvegarde_courant' => 'نسخة احتياطية من الملف الحالي:',
	'texte_sauvegarde_impossible' => 'لا يمكن حفظ الملف ',
	'texte_sauvegarder' => 'حفظ',
	'texte_selection_langue' => 'لعرض ملف لغة مترجم او قيد الترجمة، الرجاء تحديد اللغة: ',
	'texte_selectionner' => 'للبدء بأعمال الترجمة يجب الاختيار:',
	'texte_selectionner_version' => 'اختر إصدار الملف ثم انقر الزر التالي.',
	'texte_seul_admin' => 'لا يمكن الا للمدراء الوصول الى هذه المرحلة.',
	'texte_total_chaine' => 'عدد السلاسل:',
	'texte_total_chaine_conflit' => 'عدد السلاسل الاكثر استخداماً:',
	'texte_total_chaine_modifie' => 'عدد السلاسل المطلوب تحديثها:',
	'texte_total_chaine_non_traduite' => 'عدد السلاسل غير المترجمة:',
	'texte_total_chaine_traduite' => 'عدد السلاسل المترجمة:',
	'texte_tout_selectionner' => 'تحديد الكل',
	'texte_type_operation' => 'نوع العملية',
	'texte_voir_bilan' => 'عرض <a href="@url@" class="spip_in">جردة الترجمات</a>.',
	'tfoot_total' => 'Total', # NEW
	'th_avancement' => 'التقدم',
	'th_comm' => 'التعليق',
	'th_items_modifs' => 'البنود المعدلّة',
	'th_items_new' => 'البنود الجديدة',
	'th_items_traduits' => 'البنود المترجمة',
	'th_langue' => 'اللغة',
	'th_langue_mere' => 'اللغة الأم',
	'th_langue_origine' => 'نص لغة المصدر',
	'th_module' => 'الوحدة',
	'th_status' => 'الوضعية',
	'th_total_items_module' => 'عدد البنود الإجمالي',
	'th_traduction' => 'الترجمة',
	'titre_bilan' => 'جردة الترجمات',
	'titre_bilan_langue' => 'جردة ترجمات اللغة "@lang@"',
	'titre_bilan_module' => 'جردة ترجمات الوحدة "@module@"',
	'titre_changer_langue_selection' => 'Changer la langue sélectionnée', # NEW
	'titre_changer_langues_affichees' => 'Changer les langues affichées', # NEW
	'titre_commentaires_chaines' => 'تعليق على هذه السلسلة',
	'titre_logo_tradlang_module' => 'شعار الوحدة',
	'titre_modifications_chaines' => 'أحدث تعديلات هذه السلسلة',
	'titre_modifier' => 'Modifier', # NEW
	'titre_page_configurer_tradlang' => 'إعداد ملحق Trad-lang',
	'titre_page_tradlang_module' => 'الوحدة #@id@ : @module@',
	'titre_profil_auteur' => 'Éditer votre profil', # NEW
	'titre_profil_autre' => 'Éditer son profil', # NEW
	'titre_recherche_tradlang' => 'سلاسل اللغة',
	'titre_revisions_sommaire' => 'أحدث التعديلات',
	'titre_tradlang' => 'Trad-lang',
	'titre_tradlang_chaines' => 'سلاسل اللغة في Trad-lang', # MODIF
	'titre_tradlang_module' => 'وحدة لغة',
	'titre_tradlang_modules' => 'وحدات لغات Trad-lang', # MODIF
	'titre_traduction' => 'الترجمات',
	'titre_traduction_chaine_de_vers' => 'ترجمة سلسلة «@chaine@» في وحدة «@module@» من <abbr title="@lang_orig_long@">@lang_orig@</abbr> الى <abbr title="@lang_cible_long@">@lang_cible@</abbr>',
	'titre_traduction_de' => 'ترجمة ',
	'titre_traduction_module_de_vers' => 'ترجمة وحدة «@module@» من <abbr title="@lang_orig_long@">@lang_orig@</abbr> الى <abbr title="@lang_cible_long@">@lang_cible@</abbr>',
	'titre_traduire' => 'ترجمة',
	'tradlang' => 'Trad-Lang',
	'traduction' => 'ترجمة @lang@',
	'traductions' => 'ترجمات'
);

?>
