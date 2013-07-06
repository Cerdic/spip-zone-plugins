<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/autorite?lang_cible=ar
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'تفعيل الإدارة بالمفاتيح',
	'admin_complets' => 'المدراء الكاملون',
	'admin_restreints' => 'المدراء المحدودون؟',
	'admin_tous' => 'جميع المدراء (بما فيهم المحدودين)',
	'administrateur' => 'مدير',
	'admins' => 'المدراء',
	'admins_redacs' => 'مدراء ومحررين',
	'admins_rubriques' => 'المدراء المقترنون بأقسام يتمتعون بـ:',
	'attention_crayons' => '<small><strong>تنبيه.</strong> لا يمكن تفعيل الإعدادات أدناه إلا في حال استخدام ملحق يتيح واجهة تحرير (كما هي الحال مثلاً مع <a href=\\"http://www.spip-contrib.net/Les-Crayons\\">القلم</a>).</small>',
	'attention_version' => 'تنبيه. قد لا تعمل الخيارات التالية مع إصدار SPIP لديك:',
	'auteur_message_advitam' => 'كاتب الرسالة، دائماً',
	'auteur_message_heure' => 'كاتب الرسالة، لمدة ساعة واحدة',
	'auteur_modifie_article' => '<strong>المؤلف يعدّل المقال</strong>: كل محرر يمكنه تعديل مقالاته المنشورة (وبالتالي تعديل المنتدبات والعرائض المرتبطة بها).
<br />
ملاحظة: ينطبق هذا الخيار أيضاً على الزوار المسجلين، اذا كانوا مؤلفين وإذا توافرت لهم واجهة مناسبة.', # MODIF
	'auteur_modifie_email' => '<strong>المحرر يعدل البريد الإلكتروني</strong>: كل محرر يمكنه تعديل بريده الإلكتروني في صفحة معلوماته الشخصية.',
	'auteur_modifie_forum' => '<strong>المؤلف يعدل المنتدى</strong>: كل محرر يمكنه تعديل منتدى مقالاته.',
	'auteur_modifie_petition' => '<strong>المؤلف يعدل العريضة</strong>: كل محرر يمكنه تعديل العريضة المرتبطة بمقالاته.',

	// C
	'config_auteurs' => 'إعداد المؤلفين',
	'config_auteurs_rubriques' => 'أي نوع من المؤلفين يمكن <b>ربطه بالأقسام</b>.',
	'config_auteurs_statut' => 'لدى إنشاء مؤلف، ما هو <b>وضعه الافتراضي</b>؟',
	'config_plugin_qui' => 'من يمكنه <strong>تعديل إعداد</strong> الملحقات (تفعيل...)؟',
	'config_site' => 'إعداد الموقع',
	'config_site_qui' => 'من يمكنه <strong>تغيير إعداد</strong> الموقع؟',
	'crayons' => 'القلم',

	// D
	'deja_defini' => 'الأذونات التالية سبق تحديدها في مكان آخر:',
	'deja_defini_suite' => 'لا يمكن لملحق «السلطة» تعديل بعض الإعدادات بالتالي فإنها قد لا تعمل.
<br />لحل هذه المشكلة، يجب التأكد من ان ملف <tt>mes_options.php</tt> (أو ملحق آخر نشط) قد حدد هذه الوظائف.',
	'descriptif_1' => 'صفحة الإعداد هذه محصورة بالمشرف على الموقع:',
	'descriptif_2' => '<hr />
<p><small>إذا كنت تريد تغيير هذه القائمة، الرجاء تحرير الملف config/mes_options.php (أمشاءه إذا لم يكن موجوداً) وتحديد قائمة معرفات المشرفين، على النحو التالي:</small></p>
<html><pre>&lt;?php
  define (\'_ID_WEBMESTRES\',
  \'1:5:8\');
?&gt;</pre></html>
<p><small>ملاحظة : لا يعود المشرفون المحددون بهذه الطريقة يحتاجون إلى التعريف عن نفسهم بواسطة FTP لتنفيذ العمليات الحساسة (كترقية قاعدة البيانات، مثلاً).</small></p>

<a href=\'http://www.spip-contrib.net/-Autorite-\' class=\'spip_out\'>انظر التعليمات</a>
',
	'details_option_auteur' => '<small><br />حالياًو لا يعمل خيار «المؤلف» الا للمؤلفين المسجلين (منتديات بالاستراك مثلاً). واذا تم تفعيل الخيار، يتمكن مدراء الموقع أيضاً من تحرير المنتديات.
	</small>',
	'droits_des_auteurs' => 'حقوق المؤلفين',
	'droits_des_redacteurs' => 'حقوق المحررين',
	'droits_idem_admins' => 'الحقوق ذاتها التي يتمنع بها جميع المدراء',
	'droits_limites' => 'حقوق مقتصرة على هذه الأقسام',

	// E
	'effacer_base_option' => '<small><br />الخيار المستحسن هو «لا أحد»، بينما خيار SPIP القياسي هو «المدراء» (ولكن دائماً بتثبّت بواسطة FTP).</small>',
	'effacer_base_qui' => 'من يمكنه <strong>حذف</strong> قاعدة بيانات الموقع؟',
	'espace_publieur' => 'مجال النشر المفتوح',
	'espace_publieur_detail' => 'اختر أدناه، قسماً أساسياً لاستخدامه كمجال نشر مفتوح للمحررين والزوار المسجلين (بشرط ان يناح لهم واجهة مقل الأقلام واستمارة لإدخال المقالات):',
	'espace_publieur_qui' => 'هل تريد توفير النشر لغير المدراء:',
	'espace_wiki' => 'فضاء ويكي',
	'espace_wiki_detail' => 'أختر أدناه قسم أساسي لتحويله الى ويكي، يعني أنه قابل للتحرير من الجميع في الموقع العمومي (بشرط توافر واجهة لذلك مثل القلم):',
	'espace_wiki_mots_cles' => 'فضاء ويكي بالمفاتيح',
	'espace_wiki_mots_cles_detail' => 'إختر أدناه المفاتيح التي ستقوم بتفعيل وضعية ويكي، يعني صفحات قابلة للتحرير من الجميع في الموقع العمومي (بشرط توافر واجهة لذلك مثل القلم)',
	'espace_wiki_mots_cles_qui' => 'هل تريد فتح ويكي لغير المدراء:',
	'espace_wiki_qui' => 'هل تريد فتح ويكي لغير المدراء:',

	// F
	'forums_qui' => '<strong>المنتديات:</strong> من يمكنه تعديل محتوى المنتديات:',

	// I
	'icone_menu_config' => 'السلطة',
	'infos_selection' => '(يمكن تحديد عدة أقسام رئيسية بالضغط على مفتاح العالي)',
	'interdire_admin' => 'حدد الخانات ادناه لمنع المدراء من إنشاء',

	// M
	'mots_cles_qui' => '<strong>المفاتيح:</strong> من يمكنه إنشاء وتحريرالمفاتيح:',

	// N
	'non_webmestres' => 'هذا الإعداد لا ينطبق على مشرفي المواقع.',
	'note_rubriques' => '<small><br />(نذكر أن المدراء فقط يمكنهم إنشاء أقسام، أما المدراء المحدودون، فلا يمكنهم ذلك الا في أقسامهم.)</small>',
	'nouvelles_rubriques' => 'أقسام جديدة في أصل الموقع',
	'nouvelles_sous_rubriques' => 'أقسام فرعية جديدة في الهرمية.',

	// O
	'ouvrir_redacs' => 'لمحرري الموقع:',
	'ouvrir_visiteurs_enregistres' => 'للزوار المسجلين:',
	'ouvrir_visiteurs_tous' => 'لجميع زوار الموقع:',

	// P
	'pas_acces_espace_prive' => '<strong>منع الدخول الى المجال الخاص:</strong> ممنوع على المحررين الدخول الى المجال الخاص.',
	'personne' => 'لا أحد',
	'petitions_qui' => '<strong>التوقيعات:</strong> من يمكنه تعديل توقيعات العرائض:',
	'publication' => 'النشر',
	'publication_qui' => 'من يمكنه النشر في الموقع:',

	// R
	'redac_tous' => 'جميع المحررين',
	'redacs' => 'لمحرري الموقع',
	'redacteur' => 'محرر',
	'redacteur_lire_stats' => '<strong>المحرر يرى الإحصاءات</strong>: المحررون يمكنهم الاطلاع على الإحصاءات.',
	'redacteur_modifie_article' => '<strong>المحرر يعدّل المقالات المعروضة</strong>: كل محرر يمكنه تعديل مقال معروض للنشر، حتى إذا لم يكن مؤلفه.',
	'refus_1' => '<p>مشرفو الموقع فقط',
	'refus_2' => 'مخولون تعديل هذه الإعدادات.</p>
<p>للمزيد من المعلومات، انظر <a href="http://www.spip-contrib.net/-Autorite-">التعليمات</a>.</p>',
	'reglage_autorisations' => 'إعداد الأذونات',

	// S
	'sauvegarde_qui' => 'من يمكنه إنشاء <strong>نسخ احتياطية</strong>؟',

	// T
	'tous' => 'الكل',
	'tout_deselectionner' => ' إلغاء تحديد الكل',

	// V
	'valeur_defaut' => '(القيمة الافتراضية)',
	'visiteur' => 'زائر',
	'visiteurs_anonymes' => 'يمكن للزوار المجهولين إنشاء صفحات جديدة.',
	'visiteurs_enregistres' => 'للزوار المسجلين',
	'visiteurs_tous' => 'لجميع زوار الموقع.',

	// W
	'webmestre' => 'مشرف الموقع',
	'webmestres' => 'مشرفو الموقع'
);

?>
