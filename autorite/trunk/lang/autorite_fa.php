<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/autorite?lang_cible=fa
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'فعال سازي مديريت با كليد‌واژه‌ها',
	'admin_complets' => 'مديران كامل',
	'admin_restreints' => 'مديران محدود؟',
	'admin_tous' => 'تمام مديران (شامل محدوده‌ها)',
	'administrateur' => 'مدير',
	'admins' => 'مديران',
	'admins_redacs' => 'مديران و نويسندگان',
	'admins_rubriques' => 'مديران مربطو به بخش‌ها عبارتند از: ',
	'attention_crayons' => '<small><strong>توجه..</strong> تنظيم‌هاي زير ني‌توانند كاركردي داشته باشند مگر آنكه شما پلاگيني را مورد استفاده قرار دهيد كه يك واسطه‌ي ويرايش ارايه دهد (به عنوان نمونه مانند <a href="http://contrib.spip.net/Les-Crayons">les Crayons</a>).</small>', # MODIF
	'attention_version' => 'توجه گزينه‌هاي بعدي با نسخه‌ي اسپيپ شما كاركرد ندارد: ',
	'auteur_message_advitam' => 'نويسنده‌ي پيام، ad vitam',
	'auteur_message_heure' => 'نوسينده‌ي پيام، طي يك ساعت ',
	'auteur_modifie_article' => '<strong>نويسنده مقاله را تعديل مي‌كند</strong> : 
هر نويسنده مي‌تواند مقالات منتشر شده (و در نتيجه، سخنگاه و طومار وابسته به آن) را كه نويسنده‌اشان تعديل كند 
	<br />
	<i>توجه:اين گزينه همچنين در مورد بازديدكنندگان ثبت نامه شده نيز به كار مي‌رود، اگر آنان نوسنده باشند و اگر يك وساط خاص پيش‌بيني شده باشد.</i>', # MODIF
	'auteur_modifie_email' => '<strong>نويسنده ايميل را اصلاح مي‌كند</strong> :هر نويسنده مي‌تواند ايميل خودش را در فيش اطلاعات شخصي خودش اصلاح كند.',
	'auteur_modifie_forum' => '<strong>مؤلف سخنگاه را تعديل مي‌كند</strong> :هر مؤلف مي‌تواند سخنگاه مقالات خودش را تعديل كند. .',
	'auteur_modifie_petition' => '<strong>نويسنده طومار را تعديل مي‌‌كند</strong> :هر نويسنده مي‌تواند طومار مقالات خودش را تعديل كند.',

	// C
	'config_auteurs' => 'پيكربندي مؤلفان',
	'config_auteurs_rubriques' => 'چه نوع مؤلفي مي‌تواند <b>در بخش‌ها شركت كنند؟</b> ?',
	'config_auteurs_statut' => 'هنگام ايجاد مؤلف، وضعيت پيش‌گزيده چيست؟</b> ',
	'config_plugin_qui' => 'چه كسي مي‌تواند  <strong>پيكربندي پلاگين‌ها را تعديل (فعال سازي ...) كند؟</strong>',
	'config_site' => 'پيكربندي سايت',
	'config_site_qui' => 'چه كسي مي‌تواند <strong>پيكربندي سايت را تعديل كند؟ </strong>',
	'crayons' => 'مدادها',

	// D
	'deja_defini' => 'مجوزهاي بعدي از جهات ديگر (از جاي ديگر) تعريف شده‌اند: ',
	'deja_defini_suite' => 'پلاگين «اجازه» نمي‌تواند بعضي از تنظيمات زير را تعديل كند بنابراين ممكن است كارنكند. <br />براي حل اين مشكل، بايد بررسي كنيد كه آيا پرونده‌ي <tt>mes_options.php</tt> (يا يك پلاگين فعال ديگر) اين كاركردها را تعريف كرده‌اند يا نه؟ 
',
	'descriptif_1' => 'اين صفحه‌ي پيكربندي براي وب‌ مسترهاي سايت محفوظ اشت: ',
	'descriptif_2' => '<p> اگر مي‌خواهيد اين فهرست را ويرايش كنيد، لطفاً پرونده‌ي  <tt>config/mes_options.php</tt> را ويرايش كنيد (و در صورت لزوم ايجاد نماييد) و فهرست شناسه‌ي وب‌ مستر‌ها را، در فرم زير قيد نماييد.:</p>
<pre>&lt;?php
  define(
    \'_ID_WEBMESTRES\',
    \'1:5:8\');
?&gt;</pre>
<p>از اسپيپ 2.1.دادن حق وب مستر‌ها به مديران از طريق صفحه ويرايش نويسندگان ممكن شده است.</p>
<p>توجه: وب‌ مسترهايي تعريف شده به اين شيوه نيازي به مجوز اف.تي.پي براي فعاليت‌هاي حساس (به هنگام سازي پايگاه داده‌ها، به عنوان نمونه) ندارند</p>

<a href=\'http://contrib.spip.net/Autorite\' class=\'spip_out\'>Cf. documentation</a>', # MODIF
	'details_option_auteur' => '<small><br />در اين لحظه، گزينه‌ي «مؤلف» فقط براي مؤلف‌هاي ثبت نام شده كار مي‌كند (به عنوان نمونه، سخنگاه‌هاي مشتركين). و اگر فعال باشد، مديران سايت نيز توانايي ويراش سخنگاه‌ها را خواهند داشت. 
</small>
',
	'droits_des_auteurs' => 'حقوق مؤلفان',
	'droits_des_redacteurs' => 'حقوق نويسندگان ',
	'droits_idem_admins' => 'همان حقوق تمام مديران',
	'droits_limites' => 'حقوق محدود شده به اين بخش‌ها',

	// E
	'effacer_base_option' => '<small><br />گزينه‌ي توصيه شده «هيچكس» است، گزينه‌ي استاندارد اسپيپ «مديران»‌ است (اما هميشه همراه با يك تأييديه توسط اف.تي.پي). </small>',
	'effacer_base_qui' => 'چه كسي مي‌تواند پايگاه داده‌هاي اين سايت را <strong></strong>پاك كند؟',
	'espace_publieur' => 'فضاي نشر آزاد',
	'espace_publieur_detail' => 'در زير يك بخش (در ريشه سايت) را به عنوان فضاي نشر آزاد براي نويسندگان و/يا بازديدكنندگان ثبت‌نام شده (به شرط داشتن واسطي، به عنوان نمونه مداد‌ها و يك فرم براي ارايه‌ي مقاله)، انتخاب كنيد',
	'espace_publieur_qui' => 'مايليد نشر را باز كنيد- وراي مديران: ',
	'espace_wiki' => 'فضاي ويكي',
	'espace_wiki_detail' => 'در زير يك بخش (در ريشه سايت)‌را براي آنكه مانند ويكي عمل كند، يعني براي هركس از فضاي همگاني قابل ويرايش باشد(به عنوان نمونه، به شرط داشتن واسطي مانند مدادها)، انتخاب نماييد، ',
	'espace_wiki_mots_cles' => 'فضاي ويكي با كلد‌واژه‌ها',
	'espace_wiki_mots_cles_detail' => 'در زير كليد‌واژه‌هايي را كه حالت ويكي، يعني ويرايش‌ پذير توسط هر كس از فضاي همگاني (به شرط داشتن واسطي،‌ به عنوان نمونه مدادها)، را فعال مي‌سازند، انتخاب كنيد. ',
	'espace_wiki_mots_cles_qui' => 'مي‌خواهيد اين ويكي را وراي مديران باز كنيد: ',
	'espace_wiki_qui' => 'مي‌خواهيد اين ويكي را - وراي مديران باز كنيد: ',

	// F
	'forums_qui' => '<strong>فرم‌ها :</strong> كه مي‌توانند محتواي سخنگاه‌ها را ويرايش كنند:',

	// I
	'icone_menu_config' => 'دسترسي محدود',
	'infos_selection' => '(مي‌توانيد بخش‌هاي چندگانه را با كليد شيفت انتخاب كنيد)',
	'interdire_admin' => 'چارگوش‌هاي زير را علامت بزنيد براي جلوگيري مديران از ايجاد ',

	// M
	'mots_cles_qui' => '<strong>كليدواژه‌ها:</strong> كه مي‌توانند كليد‌واژه‌ها را ايجاد و ويرايش كنند :',

	// N
	'non_webmestres' => 'اين تنظيم براي وب‌مستر‌ها كاربست ندارد.',
	'note_rubriques' => '(توجه كنيد كه فقط مديران مي‌توانند بخش‌ها را ايجاد كنند، و، مديران محدود، در بخش‌هاي خودشان مي‌توانند بخش بسازند)',
	'nouvelles_rubriques' => 'از بخش‌هاي جديد در ريشه‌ي سايت',
	'nouvelles_sous_rubriques' => 'زيربخش‌هاي جديد در درخت',

	// O
	'ouvrir_redacs' => 'بازگشانيي به روي نويسندگان سايت: ',
	'ouvrir_visiteurs_enregistres' => 'گشايش به روي بازديد‌كنندگان ثبت‌نام كرده: ',
	'ouvrir_visiteurs_tous' => 'گشايش به روي تمام بازديدكنندگان سايت:',

	// P
	'pas_acces_espace_prive' => '<strong>عدم دسترسي به قسمت شخصي:</strong> تويسندگان به قسمت شخصي دسترسي ندارند.',
	'personne' => 'هيچكس',
	'petitions_qui' => '<strong>امضا كنندگان::</strong> كسي كه مي‌تواند امضا كنندگان طومار را تعديل كند:',
	'publication' => 'نشر',
	'publication_qui' => 'كسي كه مي‌تواند روي سايت منتشر كند:‌',

	// R
	'redac_tous' => 'تمام ويراستاران',
	'redacs' => 'به ويراستان سايت',
	'redacteur' => 'ويراستار',
	'redacteur_lire_stats' => '<strong>ويراستار وضعيت را مي‌بيند</strong> : ويراستاران مي‌توانند آمارها را مشاهده كنند.',
	'redacteur_modifie_article' => '<strong>ويراستار پيشنهادها را تعديل مي‌كند</strong>: هر ويراستار مي‌تواند يك مقاله‌ي پيشنهادي براي انتشار را تعديل كند، حتي اگر نويسنده نباشد.',
	'refus_1' => '<p>فقط وب مسترهاي سايت ',
	'refus_2' => 'مجازاند اين پارامتر‌ها را تعديل كنند.</p>
<p>براي آگاهي بيشتر، بنگريد <a href="http://contrib.spip.net/Autorite">مستندات </a>.</p>', # MODIF
	'reglage_autorisations' => 'تنظيم مجوز‌ها',

	// S
	'sauvegarde_qui' => 'كي‌ مي‌تواند  <strong>بك آپ بگيرد</strong>?',

	// T
	'tous' => 'همه',
	'tout_deselectionner' => 'رد انتخاب همه',

	// V
	'valeur_defaut' => '(ارزش پيش‌گزيده)',
	'visiteur' => 'بازديدكننده',
	'visiteurs_anonymes' => 'بازديدكنندگان ناشناس مي‌توانند صفحه‌هاي جديد ايجاد كنند.',
	'visiteurs_enregistres' => 'براي بازديد‌كنندگان ثبت نام كرده',
	'visiteurs_tous' => 'براي تمام بازديدكنندگان سايت.',

	// W
	'webmestre' => 'وب مستر',
	'webmestres' => 'وب مستر‌ها'
);
