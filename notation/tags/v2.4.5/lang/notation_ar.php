<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/notation?lang_cible=ar
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'acces' => 'سهولة الوصول',
	'afficher_tables' => 'عرض التقييمات',
	'aide' => 'مساعدة',
	'articles' => 'مقالات',
	'auteur' => 'مؤلف',

	// B
	'bouton_radio_fermee' => 'مغلقة',
	'bouton_radio_ouvert' => 'مفتوحة',
	'bouton_voter' => 'تقييم',

	// C
	'change_note_label' => 'السماح للناخبين بتغيير تقييمهم',
	'configuration_notation' => 'ضبط التقييمات',
	'creation' => 'إنشاء جداول البيانات',
	'creation_des_tables_mysql' => 'إنشاء جداول البيانات',
	'cree' => 'تم إنشاء الجداول',
	'creer_tables' => 'إنشاء جداول البيانات',

	// D
	'date' => 'تاريخ',
	'derniers_votes' => 'أحدث التقييمات',
	'destruction' => 'تدمير جداول البيانات',
	'detruire' => '<strong style="color:red">تحذير ، سيؤدي هذا الأمر إلى تدمير جداول البرنامج المساعد!</strong><br />يجب استخدامه إذا كنت ترغب في تعطيل البرنامج المساعد ......',
	'detruit' => 'الجداول دمرت ...',

	// E
	'effacer_tables' => 'مسح جداول البيانات',
	'err_balise' => '[ NOTATION_ERR : العلامة خارج المقال ]',
	'err_db_notation' => '[خطأ في التقييم: تدوين واحد لكل مقالة]',
	'exemple' => 'توزيع التقييم (القيمة = 5 ، عامل الترجيح = @ponderation@):',
	'explication_accepter_note' => 'إذا كان "مغلقة" ، سيتم تفعيل التقييم على أساس كل حالة على حدة على العناصر التي لها هذه الوظيفة.',

	// I
	'info_acces' => 'فتح التقييم:',
	'info_etoiles' => 'هذا الإعداد يسمح لك بتغيير الحد الأقصى لقيمة التقييم (عدد النجوم، 1-10، و 5 افتراضيا).<br />
                    <strong style="color:red">/ !\\ انتباه</strong> : يجب أن لا تلمس هذا الإعداد بعد إجراء تقييم لأنه لن يتم إعادة حساب القيم وهذا يمكن أن يؤدي إلى عدم تناسق في التقييم ...<br />
                    يجب أن يكون هذا الإعداد ثابتًا عند إنشاء التقييمات..',
	'info_fonctionnement_note' => 'كيف يعمل التقييم',
	'info_ip' => 'كون سهلة الاستخدام قدر الإمكان ، يتم تثبيت التقييم على عنوان IP للناخب ، والذي يتجنب إجراء تقييمين متتاليين في قاعدة البيانات ، مع بعض العيوب ... خاصة إذا كنت تدير تقييمات المؤلفين.<br />
                في هذه الحالة ، نقوم بإصلاح الملاحظة على معرف المستخدم (عندما يتم تسجيله ، بالطبع).',
	'info_methode_id' => 'التحقق من تفرد التقييمات',
	'info_modifications' => 'تغييرات التقييم',
	'info_ponderation' => 'يعطي عامل الترجيح مزيدًا من الوزن للمقالات التي حصلت على تقييمات كافية. <br /> أدخل أدناه عدد التقييمات التي تعتقد أن التقييم موثوق بها.',
	'info_vote_unique_auteur' => 'إذا كنت ترغب في ضمان تفرد التقييم ، فحد من التقييم للأشخاص المسجلين <b>فقط</b>.',
	'ip' => 'IP',
	'item_adm' => 'للمديرين',
	'item_all' => 'للجميع',
	'item_aut' => 'للمؤلفين',
	'item_id' => 'تقييم واحد فقط لكل مستخدم',
	'item_ide' => 'للمسجلين',
	'item_ip' => 'تقييم واحد عن طريق IP',
	'item_methode_id_cookie' => 'بواسطة الكوكي',
	'item_methode_id_hash' => 'بواسطة بصمة المتصفح',
	'item_methode_id_ip' => 'بواسطة عنوان IP',

	// J
	'jaidonnemonavis' => 'أنا قدمت رأيي!',
	'jaime' => 'أحب',
	'jaimepas' => 'لاأحب',
	'jaimeplus' => 'لم أعد أحب',
	'jechangedavis' => 'أنا أسحب رأيي',

	// L
	'label_accepter_note' => 'حالة التقييم على جميع العناصر',

	// M
	'moyenne' => 'المتوسط',
	'moyennep' => 'المتوسط المرجح',

	// N
	'nb_etoiles' => 'قيمة التقييمات',
	'nbobjets_note' => 'عدد العناصر التي لها تقييم:',
	'nbvotes' => 'عدد التقييمات',
	'nbvotes_moyen' => 'متوسط عدد التقييمات لكل عنصر',
	'nbvotes_total' => 'إجمالي عدد التقييمات على الموقع:',
	'notation' => 'التقييمات',
	'note' => 'التقييم',
	'note_1' => 'التقييم: 1',
	'note_10' => 'التقييم: 10',
	'note_2' => 'التقييم: 2',
	'note_3' => 'التقييم: 3',
	'note_4' => 'التقييم: 4',
	'note_5' => 'التقييم: 5',
	'note_6' => 'التقييم: 6',
	'note_7' => 'التقييم: 7',
	'note_8' => 'التقييم: 8',
	'note_9' => 'التقييم: 9',
	'note_pond' => 'التقييمات المرجحة',
	'notes' => 'التقييمات',

	// O
	'objets' => 'عناصر',

	// P
	'param' => 'ضبط',
	'ponderation' => 'ترجيح التقييم',

	// T
	'titre_ip' => 'طريقة عمل:',
	'topnb' => 'العناصر العشرة الأكثر تقييمًا',
	'topten' => 'أفضل 10 التقييمات',
	'toptenp' => 'أفضل 10 التقييمات (المرجحة)',
	'totaux' => 'الإجماليات',

	// V
	'valeur_nb_etoiles' => 'التقييمات من 1 إلى',
	'valeur_ponderation' => 'عامل الترجيح',
	'vos_notes' => 'لديك أفضل 5 تقييمات',
	'vote' => 'رأي',
	'voter' => 'إبداء الرأي:',
	'votes' => 'اراء',
	'votre_note' => 'نتيجتك'
);
