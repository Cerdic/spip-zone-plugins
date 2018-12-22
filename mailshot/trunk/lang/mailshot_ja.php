<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailshot?lang_cible=ja
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_exemple' => '例',
	'cfg_exemple_explication' => '例の説明',
	'cfg_titre_parametrages' => '多数でメールの送信を設定する',

	// E
	'erreur_aucun_service_configure' => 'どんなメールサービスも設定されていません。 <a href="@url@">Configurer un service</a>',
	'erreur_envoi_mail_bloque_debug' => '<tt>_TEST_EMAIL_DEST</tt>はメールの送信を停止しました。', # MODIF
	'erreur_envoi_mail_force_debug' => '<tt>_TEST_EMAIL_DEST</tt>は強制的に@email@宛にを送信しました。', # MODIF
	'erreur_generation_newsletter' => 'ニュースレターの生成中にエラーが発生しました。', # MODIF
	'explication_boost_send' => 'この送信モードでは、メールはでできるだけ早く送信されます。 レート制限は考慮
されていません。',
	'explication_purger_historique' => '多数で各送信の場合、送信に関するステータスと宛先人の情報はデータベースに保管
されます。',
	'explication_rate_limit' => '１日あたりに送信される電子メールの最大数を指定してください。限界を設定しない
場合、空白のままに残してください。',

	// I
	'info_1_mailshot' => '送信は１つ',
	'info_1_mailshot_destinataire' => '宛先は１名',
	'info_1_mailsubscriber' => '購読者は１名',
	'info_annuler_envoi' => '送信を取り消す',
	'info_archiver' => 'アーカイブとされました。',
	'info_aucun_destinataire' => 'どんな宛先人も設定していません。',
	'info_aucun_envoi' => '送信はありません',
	'info_envoi_programme_1_destinataire' => '宛先人に１名設定された送信',
	'info_envoi_programme_nb_destinataires' => '宛先人に@nb@名設定された送信',
	'info_mailshot_no' => '送信@id@号',
	'info_nb_mailshots' => '送信は@nb@つ',
	'info_nb_mailshots_destinataires' => '宛先',
	'info_nb_mailsubscribers' => '登録済みの方：@nb@名',
	'info_statut_archive' => 'アーカイブ',
	'info_statut_cancel' => '取り消された',
	'info_statut_destinataire_clic' => 'クリック',
	'info_statut_destinataire_fail' => '失敗',
	'info_statut_destinataire_read' => 'オープン',
	'info_statut_destinataire_sent' => '送信済み',
	'info_statut_destinataire_spam' => '迷惑メール',
	'info_statut_destinataire_todo' => '送信するメール',
	'info_statut_end' => '終了',
	'info_statut_init' => '計画',
	'info_statut_pause' => '一時停止',
	'info_statut_poubelle' => 'ゴミ箱',
	'info_statut_processing' => '処理中',

	// L
	'label_avancement' => '進歩',
	'label_boost_send_oui' => '素早い送信',
	'label_control_pause' => '一時停止',
	'label_control_play' => 'やり直し',
	'label_control_stop' => '諦める',
	'label_date_fin' => '送信終了日',
	'label_date_start' => '送信の開始日',
	'label_envoi' => '送信',
	'label_from' => '差出人',
	'label_html' => 'HTMLバージョン',
	'label_listes' => '名簿',
	'label_mailer_defaut' => '他のメールと同じメールサービスを使用する。',
	'label_mailer_defaut_desactive' => '不可能：どんなメールサービスも設定されていないのです。',
	'label_mailer_mailjet' => 'Mailjet',
	'label_mailer_mandrill' => 'マンドリル',
	'label_mailer_smtp' => 'SMTP サーバー',
	'label_mailer_sparkpost' => 'Sparkpost',
	'label_mailjet_api_key' => 'Mailjet API キー',
	'label_mailjet_api_version' => 'APIバージョン',
	'label_mailjet_secret_key' => 'Mailjet 秘密キー',
	'label_mandrill_api_key' => 'Mandrill API キー',
	'label_purger_historique_delai' => 'より古い',
	'label_purger_historique_oui' => '古い送信の詳細を削除する。',
	'label_rate_limit' => '送信レートを制限する',
	'label_sparkpost_api_endpoint' => 'Endpoint API',
	'label_sparkpost_api_key' => 'Sparkpost API キー',
	'label_sujet' => '話題',
	'label_texte' => 'テキストバーション',
	'legend_configuration_adresse_envoi' => '送信宛先',
	'legend_configuration_historique' => '送信の歴史',
	'legend_configuration_mailer' => 'メールサービス',
	'lien_voir_newsletter' => 'ニュースレターを見る',

	// M
	'mailshot_titre' => 'MailShot',

	// T
	'texte_changer_statut_mailshot' => 'この送信は：',
	'texte_statut_archive' => 'アーカイブ済み',
	'texte_statut_cancel' => '取り消された',
	'texte_statut_end' => '終わり',
	'texte_statut_init' => '計画された',
	'texte_statut_pause' => '一時停止中',
	'texte_statut_processing' => '処理中',
	'titre_envois_archives' => 'アーカイブとされた送信',
	'titre_envois_destinataires_fail' => '送信失敗',
	'titre_envois_destinataires_init_encours' => 'どんな設定された宛先人もいません。（初期化中）',
	'titre_envois_destinataires_ok' => '送信済み',
	'titre_envois_destinataires_sent' => '送信済み',
	'titre_envois_destinataires_todo' => '送信すべきメール',
	'titre_envois_en_cours' => '送信中',
	'titre_envois_planifies' => '計画送信',
	'titre_envois_termines' => '送信済み',
	'titre_mailshot' => '多数で送信',
	'titre_mailshots' => '多数で送信',
	'titre_menu_mailshots' => 'メールは多数で送信されたことを検査する',
	'titre_page_configurer_mailshot' => 'MailShot'
);
