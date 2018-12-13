<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailsubscriber?lang_cible=ru
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_importer' => 'Импорт',
	'bouton_previsu_importer' => 'Предварительный просмотр',

	// C
	'confirmsubscribe_sujet_email' => '[@NOM_SITE_SPIP@] подтверждение регистрации',
	'confirmsubscribe_texte_email_1' => 'Вы подписались на рассылку @nom_site_spip@. Ваш е-ммейл @email@.',
	'confirmsubscribe_texte_email_2' => ' Чтобы подтвердить регистрацию перейдите по ссылке: 
@url_confirmsubscribe@',
	'confirmsubscribe_texte_email_3' => 'Проигнорируйте это письмо, если вы не подписывались на рассылку :',
	'confirmsubscribe_texte_email_envoye' => 'На вашу почту отправлен лист с подтверждением',
	'confirmsubscribe_titre_email' => 'Подтверждение регистрации',

	// E
	'erreur_adresse_existante' => 'Этот е-мейл уже ксть в базе',

	// I
	'icone_creer_mailsubscriber' => 'Добавить подписчика',
	'icone_modifier_mailsubscriber' => 'Изменить',
	'info_1_adresse_a_importer' => 'Импортировать 1 емейл',
	'info_1_mailsubscriber' => 'Подписан 1 человек',
	'info_aucun_mailsubscriber' => 'Нет подписчиков',
	'info_nb_adresses_a_importer' => '@nb@ емейлов для импорта',
	'info_nb_mailsubscribers' => 'Подписано @nb@ человек',
	'info_statut_poubelle' => 'Корзина',
	'info_statut_prepa' => 'Не зарегестрировано',
	'info_statut_prop' => 'Ожидание',
	'info_statut_refuse' => 'Не определено',
	'info_statut_valide' => 'Зарегистрировано',

	// L
	'label_desactiver_notif_1' => 'Отключить уведомления импорта',
	'label_email' => 'Email',
	'label_file_import' => 'Импортировать файл',
	'label_lang' => 'Язык',
	'label_listes' => 'Доступные базы',
	'label_listes_diffusion_option_statut' => 'Статус',
	'label_mailsubscriber_optin' => 'Хочу получать рассылку',
	'label_nom' => 'Имя',
	'label_optin' => 'Opt-in',
	'label_statut' => 'Статус',
	'label_toutes_les_listes' => 'Все',
	'label_vider_table_1' => 'Удалить все данные в базе перед импортом',

	// M
	'mailsubscribers_poubelle' => 'Удалено',
	'mailsubscribers_prepa' => 'Не зарегестрировано',
	'mailsubscribers_prop' => 'Для подтверждения',
	'mailsubscribers_refuse' => 'Отписались',
	'mailsubscribers_tous' => 'Все',
	'mailsubscribers_valide' => 'Зарегистрировано',

	// S
	'subscribe_deja_texte' => '@email@ уже подписан на эту рассылку', # MODIF
	'subscribe_sujet_email' => '[@nom_site_spip@] Подписаться на рассылку',
	'subscribe_texte_email_1' => 'Ваш емейл @email@ подписан на рассылку.',
	'subscribe_texte_email_2' => 'Спасибо за проявленный интерес @nom_site_spip@.',
	'subscribe_texte_email_3' => 'Для того чтобы отписаться от рассылки перейдите по ссылке:
@url_unsubscribe@',
	'subscribe_titre_email' => 'Подписаться',

	// T
	'texte_ajouter_mailsubscriber' => 'Добавить подписчиков',
	'texte_avertissement_import' => 'A <tt>status column</tt> is supplied, the data will be imported as is , overwriting those that may already exist for some email.',
	'texte_changer_statut_mailsubscriber' => 'Статус :',
	'texte_statut_en_attente_confirmation' => 'ожидаю подтверждения',
	'texte_statut_pas_encore_inscrit' => 'не зарегестрировано',
	'texte_statut_refuse' => 'отказался от рассылки',
	'texte_statut_valide' => 'подтвержден',
	'titre_export_mailsubscribers' => 'Экспорт подписанных на рассылку',
	'titre_export_mailsubscribers_all' => 'Экспортировать всех',
	'titre_import_mailsubscribers' => 'Импортировать',
	'titre_langue_mailsubscriber' => 'Язык пользователя',
	'titre_listes_de_diffusion' => 'Базы данных',
	'titre_logo_mailsubscriber' => 'Логотип пользователя',
	'titre_mailsubscriber' => 'Подписано на рассылку',
	'titre_mailsubscribers' => 'База получателей рассылки',

	// U
	'unsubscribe_deja_texte' => '@email@ отсутствует в базе подписчиков.', # MODIF
	'unsubscribe_sujet_email' => '[@nom_site_spip@] Отписаться',
	'unsubscribe_texte_email_1' => '@email@ удален из базы рассылки.', # MODIF
	'unsubscribe_texte_email_2' => 'Ждем вас еще @nom_site_spip@.',
	'unsubscribe_texte_email_3' => 'Для оформления повторной подписки перейдите по ссылке:
@url_subscribe@',
	'unsubscribe_titre_email' => 'Отписаться'
);
