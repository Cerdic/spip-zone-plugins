<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=ja
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'config_info_enregistree' => '郵便屋さんの設定が記録されました。',
	'configuration_adresse_envoi' => 'デフォルトの送信アドレス',
	'configuration_facteur' => '郵便屋さん',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'SSL証明書の検証',
	'configuration_mailer' => '送信方法',
	'configuration_smtp' => 'メールの送信方法を選択してください。',
	'configuration_smtp_descriptif' => '不明な場合は、PHPメール機能を選択してください。',
	'corps_email_de_test' => 'これはテストのＥメールです。',

	// E
	'email_envoye_par' => 'サイト@site@による送信',
	'email_test_envoye' => 'テストメールが正しく送信されました。正しく表示されない場合は、サーバーの設定を確認するか、サーバー管理者に問い合わせてください。',
	'erreur' => 'エラー',
	'erreur_dans_log' => '：詳細は、ログファイルを確認してください。',
	'erreur_generale' => '設定エラーが最低１つあります。フォームの内容を確認してください。',
	'erreur_invalid_host' => 'このホスト名が正しくありません。',
	'erreur_invalid_port' => 'このポート番号が正しくありません。',

	// F
	'facteur_adresse_envoi_email' => 'Ｅメール：',
	'facteur_adresse_envoi_nom' => 'お名前：',
	'facteur_bcc' => 'ブラインドカーボンコピー（BCC）：',
	'facteur_cc' => 'カーボンコピー（CC）：',
	'facteur_copies' => 'コピー',
	'facteur_copies_descriptif' => '定義されたアドレスにＥメールのコピーを送信されます。',
	'facteur_email_test' => 'このアドレスにテストメールを送信する：',
	'facteur_filtre_accents' => 'アクセントをHTMLエンティティに変換します (特に Hotmail には便利です)。',
	'facteur_filtre_css' => '<head> と </head> の間に含まれるスタイルを 「オンラインスタイル」に変換し、オンラインスタイルが外部スタイルより優先されるため、web メールに便利です。',
	'facteur_filtre_images' => 'Ｅメールで参照される画像を埋め込む',
	'facteur_filtre_iso_8859' => 'ISO-8859-1に変換',
	'facteur_filtres' => 'フィルター',
	'facteur_filtres_descriptif' => '送信する時にＥメールにフィルタを適用する可能です。',
	'facteur_smtp_auth' => '認証が必須：',
	'facteur_smtp_auth_non' => 'いいえ',
	'facteur_smtp_auth_oui' => 'はい',
	'facteur_smtp_host' => 'ホスト：',
	'facteur_smtp_password' => 'パスワード：',
	'facteur_smtp_port' => 'ポート：',
	'facteur_smtp_secure' => '安全接続：',
	'facteur_smtp_secure_non' => 'いいえ',
	'facteur_smtp_secure_ssl' => 'SSL（非推奨）',
	'facteur_smtp_secure_tls' => 'TLS (推奨)',
	'facteur_smtp_sender' => 'エラー・リターン・アドレス (オプション)',
	'facteur_smtp_sender_descriptif' => 'メールヘッダ内のエラーのリターンメールアドレスを設定します。（または、リターンパス）',
	'facteur_smtp_tls_allow_self_signed_non' => 'SMTPサーバーのSSL証明書は、証明局によって発行されます。（推奨）',
	'facteur_smtp_tls_allow_self_signed_oui' => 'SMTPサーバーのSSL証明書は自己署名されています。',
	'facteur_smtp_username' => 'ユーザーネーム：',

	// L
	'label_facteur_forcer_from' => '</tt>form<tt> が同じドメインにない場合、この送信アドレスを強制する。',

	// M
	'message_identite_email' => '「郵便屋さん」というプラグインの設定は、Ｅメールを送信するためにこのメールアドレスをオーバーロードします。',

	// N
	'note_test_configuration' => 'このアドレスにＥメールを送信します。',

	// P
	'personnaliser' => '設定のカスタマイズする',

	// T
	'tester' => 'テスト',
	'tester_la_configuration' => '設定をテストする',

	// U
	'utiliser_mail' => 'PHPメール機能を使用する',
	'utiliser_reglages_site' => 'SPIPサイト設定を使用する： <br/>@from@',
	'utiliser_smtp' => 'SMTPを使用する',

	// V
	'valider' => '確認する',
	'version_html' => 'HTMLバージョン',
	'version_texte' => 'テキストバーション'
);
