<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailshot?lang_cible=ar
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_exemple' => 'مثال',
	'cfg_exemple_explication' => 'تفسير هذا المثال',
	'cfg_titre_parametrages' => 'إعداد إرسال البريد بالجملة',

	// E
	'erreur_aucun_service_configure' => 'لا توجد اية خدمة إرسال معدّة. <a href="@url@">إعداد خدمة</a>',
	'erreur_envoi_mail_bloque_debug' => 'إرسال البريد مصدود من <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_envoi_mail_force_debug' => 'إرسال بريد مفروض الى @email@ من <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_generation_newsletter' => 'حصل خطأ لدى انتاج النشرة البريدية', # MODIF
	'explication_boost_send' => 'في هذه الوضعية، يتم إرسال الرسائل في أسرع وقت ممكن. لا توجد أية حدود لوتيرة الإرسال.
										الإسال السريع غير مستحب لأنه يرفع من احتمال وصم خادم البريد بالخنزرة.',
	'explication_purger_historique' => 'لكل إرسال بالجملة، يتم حفظ جميع المرسل اليهم في قاعدة البيانات مع معلومات وضعية الإرسال.
	قد يشكل ذلك حجم بيانات كبير اذا كانت وتيرة الإرسال مرتفعة ويستحسن تفريغ تفاصيل الإرسالات القديمة. ',
	'explication_rate_limit' => 'تحديد الحد الأقصى لعدد الرسائل المرسلة في اليوم الواحد او ترك الحقل فارغ لعدد لا محدود',

	// I
	'info_1_mailshot' => 'إرسال واحد',
	'info_1_mailshot_destinataire' => 'مرسل اليه واحد',
	'info_1_mailsubscriber' => 'مسجل واحد',
	'info_annuler_envoi' => 'إلغاء الإرسال',
	'info_archiver' => 'أرشفة',
	'info_aucun_destinataire' => 'لا يوجد اي مرسل اليه',
	'info_aucun_envoi' => 'لا يوجد اي إرسال',
	'info_envoi_programme_1_destinataire' => 'إرسال مبرمج الى مرسل اليه واحد',
	'info_envoi_programme_nb_destinataires' => 'إرسال مبرمج  الى @nb@ مرسل اليه',
	'info_mailshot_no' => 'إرسال رقم @id@',
	'info_nb_mailshots' => '@nb@ إرسال',
	'info_nb_mailshots_destinataires' => '@nb@ مرسل اليه',
	'info_nb_mailsubscribers' => '@nb@ مسجل',
	'info_statut_archive' => 'أرشيف',
	'info_statut_cancel' => 'ألغي',
	'info_statut_destinataire_clic' => 'تم نقره',
	'info_statut_destinataire_fail' => 'فشل',
	'info_statut_destinataire_read' => 'فُتح',
	'info_statut_destinataire_sent' => 'أُرسل',
	'info_statut_destinataire_spam' => '>خنزرة',
	'info_statut_destinataire_todo' => 'قيد الإرسال',
	'info_statut_end' => 'انتهى',
	'info_statut_init' => 'مبرمج',
	'info_statut_pause' => 'توقف مؤقت',
	'info_statut_poubelle' => 'المهملات',
	'info_statut_processing' => 'جاري',

	// L
	'label_avancement' => 'حال الإرسال',
	'label_boost_send_oui' => 'إرسال سريع',
	'label_control_pause' => 'توقف مؤقت',
	'label_control_play' => 'إعادة الإرسال',
	'label_control_stop' => 'التخلي عن الإرسال',
	'label_date_fin' => 'تاريخ انتهاء الارسال',
	'label_date_start' => 'تاريخ بدء الإرسال',
	'label_envoi' => 'إرسال',
	'label_from' => 'المرسِل',
	'label_html' => 'نسخة HTML',
	'label_listes' => 'اللوائح',
	'label_mailer_defaut' => 'استخدام خدمة الإرسال نفسها لباقي الرسائل',
	'label_mailer_defaut_desactive' => 'غير ممكن: لا توجد اية خدمة ارسال معدّة',
	'label_mailer_mailjet' => 'خدمة Mailjet',
	'label_mailer_mandrill' => 'خدمة Mandrill',
	'label_mailer_smtp' => 'خادم SMTP',
	'label_mailer_sparkpost' => 'خدمة Sparkpost',
	'label_mailjet_api_key' => 'مفتاح Mailjet',
	'label_mailjet_api_version' => 'API إصدار وحدة',
	'label_mailjet_secret_key' => 'مفتاح Mailjet السري',
	'label_mandrill_api_key' => 'مفتاح Mandrill',
	'label_purger_historique_delai' => 'أقدم من',
	'label_purger_historique_oui' => 'حذف تفاصيل الإرسالات القديمة',
	'label_rate_limit' => 'حد وتيرة الإرسال',
	'label_sparkpost_api_key' => 'مفتاح Sparkpost',
	'label_sujet' => 'الموضوع',
	'label_texte' => 'النسخ النصية',
	'legend_configuration_adresse_envoi' => 'عنوان الإرسال',
	'legend_configuration_historique' => 'جردة الإرسالات',
	'legend_configuration_mailer' => 'خدمة إرسال البريد الالكتروني',
	'lien_voir_newsletter' => 'عرض النشرة البريدية',

	// M
	'mailshot_titre' => 'البريد الدعائي',

	// T
	'texte_changer_statut_mailshot' => 'هذا الإرسال هو:',
	'texte_statut_archive' => 'مؤرشف',
	'texte_statut_cancel' => 'ملغى',
	'texte_statut_end' => 'انتهى',
	'texte_statut_init' => 'مبرمج',
	'texte_statut_pause' => 'متوقف مؤقتاً',
	'texte_statut_processing' => 'جاري',
	'titre_envois_archives' => 'الإرسالات المؤرشفة',
	'titre_envois_destinataires_fail' => 'الإرسالات التي فشلت',
	'titre_envois_destinataires_init_encours' => 'لا يوجد اي مرسل اليه مبرمج (جاري التأصيل)',
	'titre_envois_destinataires_ok' => 'الإرسالات الناجحة',
	'titre_envois_destinataires_sent' => 'الإرسالات الناجحة',
	'titre_envois_destinataires_todo' => 'إراسلات قادمة',
	'titre_envois_en_cours' => 'إرسالات حالية',
	'titre_envois_planifies' => 'إرسالات مبرمجة',
	'titre_envois_termines' => 'إرسالات منتهية',
	'titre_mailshot' => 'إرسال بالجملة',
	'titre_mailshots' => 'إرسالات بالجملة',
	'titre_menu_mailshots' => 'متابعة إرسال البريد بالجملة',
	'titre_page_configurer_mailshot' => 'البريد الدعائي'
);
