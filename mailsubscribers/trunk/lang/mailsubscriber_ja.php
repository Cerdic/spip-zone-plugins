<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailsubscriber?lang_cible=ja
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_actualiser_segments' => 'セグメントを最新にする',
	'bouton_importer' => '読み込む',
	'bouton_invitation' => 'ニュースレターに購読するように招待する',
	'bouton_previsu_importer' => 'プレビューを表示する',

	// C
	'confirmsubscribe_invite_texte_email_1' => '@invite_email_from@は、@email@様を@nom_site_spip@のニュースレターを購読するように誘います。',
	'confirmsubscribe_invite_texte_email_3' => 'こち側よりエラーが発生したか、このメールを無視してください。要求は自動的に取り消されます。',
	'confirmsubscribe_invite_texte_email_liste_1' => '@invite_email_from@は、メールアドレス@email@を使って、「@nom_site_spip@」サイトの「@titre_liste@」ニュースレターを購読するように招待します。', # MODIF
	'confirmsubscribe_sujet_email' => '「@nom_site_spip@」のニュースレターの登録確認',
	'confirmsubscribe_texte_email_1' => '電子メールアドレス@email@で、@nom_site_spip@のニュースレターを購読するように要求してくださいました。',
	'confirmsubscribe_texte_email_2' => '購読を確認するには、次のリンクをクリックしてください：
@url_confirmsubscribe@',
	'confirmsubscribe_texte_email_3' => 'こち側よりエラーが発生したか、ご意見が変わったかの場合には、このメールを無視してください。要求は自動的に取り消されます。',
	'confirmsubscribe_texte_email_envoye' => '確認していただくためにために、このアドレスにメールが送信されました。',
	'confirmsubscribe_texte_email_liste_1' => '@email@であるメールアドレスで「@nom_site_spip@」「の@titre_liste@」リストに購読するように要求してくださいました。', # MODIF
	'confirmsubscribe_titre_email' => 'ニュースレターの購読を確認する',
	'confirmsubscribe_titre_email_liste' => 'リスト「@titre_liste@」の購読をの確認する', # MODIF

	// D
	'defaut_message_invite_email_subscribe' => 'こんにちは、私は@nom_site_spip@のニュースレターを購読しています。あなたも登録することを提案する。',

	// E
	'erreur_adresse_existante' => 'このメールアドレスは既にリストに登録されています。',
	'erreur_adresse_existante_editer' => 'このメールアドレスは既に登録されています。<a href="@url@">ユーザーを編集する</a>',
	'erreur_technique_subscribe' => '技術的なエラーが起きて、登録が不可能です。',
	'explication_listes_diffusion_option_defaut' => 'コンマで区切られた最低1つのリストＩＤ',
	'explication_listes_diffusion_option_statut' => 'ステータースによってリストをソートする',
	'explication_to_email' => '次のアドレス（必要に応じてカンマで区切って複数のアドレス）に事前購読の電子メールを送信します。',

	// F
	'force_synchronisation' => '同期する',

	// I
	'icone_creer_mailsubscriber' => '購読を追加する',
	'icone_modifier_mailsubscriber' => '購読を編集する',
	'info_1_adresse_a_importer' => '書き込むアドレス１つ',
	'info_1_mailsubscriber' => '購読者は１名',
	'info_aucun_mailsubscriber' => '購読者はいません。',
	'info_email_inscriptions' => '@email@様の申し込み：',
	'info_email_limite_nombre' => '招待者は5名限定。',
	'info_email_obligatoire' => 'メールアドレスは必須です。',
	'info_emails_invalide' => 'メールは1つ無効です。',
	'info_nb_adresses_a_importer' => '書き込むＥメールアドレスは@nb@',
	'info_nb_mailsubscribers' => '購読者は@nb@名',
	'info_statut_poubelle' => 'ゴミ箱',
	'info_statut_prepa' => '購読されていません。',
	'info_statut_prop' => '処理中',
	'info_statut_refuse' => '停止中',
	'info_statut_valide' => '購読済み',

	// L
	'label_desactiver_notif_1' => '書き込まれた購読者へ通知を無効にする',
	'label_email' => 'メール',
	'label_file_import' => '書き込むファイル',
	'label_from_email' => '招待するメール',
	'label_informations_liees' => 'セグメント情報',
	'label_inscription' => '申し込み',
	'label_lang' => '言語',
	'label_listes' => 'リスト',
	'label_listes_diffusion_option_statut' => 'ステータス',
	'label_listes_import_subscribers' => 'リストを購読する',
	'label_mailsubscriber_optin' => 'ニュースレターを送って欲しいです。',
	'label_message_invite_email_subscribe' => '送信されたメールに付随されたメッセージ',
	'label_nom' => 'お名前',
	'label_optin' => 'オプトイン',
	'label_statut' => 'ステータス',
	'label_to_email' => '招待するべきメール',
	'label_toutes_les_listes' => 'すべて',
	'label_valid_subscribers_1' => '確認の要求なしで購読を有効する。',
	'label_vider_table_1' => '書き込む前にデータベースにある全アドレスを削除する',

	// M
	'mailsubscribers_poubelle' => '削除された',
	'mailsubscribers_prepa' => '購読されていません。',
	'mailsubscribers_prop' => '確認するべき',
	'mailsubscribers_refuse' => '購読解約',
	'mailsubscribers_tous' => '全',
	'mailsubscribers_valide' => '購読済み',

	// S
	'subscribe_deja_texte' => 'Ｅメールアドレス@email@は既にメーリングリストに登録されています。', # MODIF
	'subscribe_sujet_email' => '「@nom_site_spip@」ニュースレターの申し込み',
	'subscribe_texte_email_1' => 'ニュースレターを受け取るように、Ｅメールアドレス@email@は有効されました。',
	'subscribe_texte_email_2' => '@nom_site_spip@にご関心をお持ちいただき、ありがとうございます。',
	'subscribe_texte_email_3' => 'こち側よりエラーが発生したか、ご意見が変わったかの場合には、次のリンクを使用して@url_unsubscribe@、いつでも購読を取り消すことが出来ます。
',
	'subscribe_texte_email_liste_1' => 'リスト「@titre_liste@」に参加するように、Ｅメールアドレス@email@は有効されまし
た。', # MODIF
	'subscribe_titre_email' => 'ニュースレター購読',
	'subscribe_titre_email_liste' => 'リスト「@titre_liste@」購読', # MODIF

	// T
	'texte_ajouter_mailsubscriber' => 'ニュースレターに購読者を追加する。',
	'texte_avertissement_import' => '<b>ステータス</b>列が提供され、データはそのまま書き込まれますが、いくつかのＥメールが既に存在するかもしれないものを上書きします。',
	'texte_changer_statut_mailsubscriber' => 'ニュースレターのこの購読者は：',
	'texte_import_export_bonux' => 'リストを書き込み、または書き出すには、プラグイン「<a href="https://plugins.spip.net/spip_bonux">SPIP-Bonux</a>」をインストールしてください。',
	'texte_statut_en_attente_confirmation' => '確認中',
	'texte_statut_pas_encore_inscrit' => '購読されていない',
	'texte_statut_refuse' => '停止中',
	'texte_statut_valide' => '有効されました。',
	'texte_vous_avez_clique_vraiment_tres_vite' => '確認ボタンをクリックしたのは非常に速かったのです。本当に人間ですか？',
	'titre_bonjour' => 'こんにちは',
	'titre_export_mailsubscribers' => '購読者を書き出す。',
	'titre_export_mailsubscribers_all' => '全Ｅメールアドレスを書き出す。',
	'titre_import_mailsubscribers' => 'Ｅメールアドレスを書き込む。',
	'titre_langue_mailsubscriber' => '購読者の言語',
	'titre_listes_de_diffusion' => 'メーリングリスト',
	'titre_logo_mailsubscriber' => '購読者のロゴ',
	'titre_mailsubscriber' => 'ニュースレターに購読',
	'titre_mailsubscribers' => 'Ｅメール送信の購読者',

	// U
	'unsubscribe_deja_texte' => 'メールアドレス@email@はメーリングリストに登録されていません。', # MODIF
	'unsubscribe_sujet_email' => '「@nom_site_spip@」ニュースレターの購読を停止する',
	'unsubscribe_texte_confirmer_email_1' => 'メール@email@の購読停止を確認するには、ボタンをクリックしてください。',
	'unsubscribe_texte_confirmer_email_liste_1' => 'ボタンをクリックして、メーリングリスト「@titre_liste@」の退会メールアドレス@email@を確認してください。', # MODIF
	'unsubscribe_texte_email_1' => 'Ｅメールアドレス@email@はメーリングリストから削除されました。', # MODIF
	'unsubscribe_texte_email_2' => 'また「@nom_site_spip@」にお会いしましょう。',
	'unsubscribe_texte_email_3' => 'こち側よりエラーが発生したか、ご意見が変わったかの場合には、次のリンクを使用して@url_subscribe@、いつでも再購読することが出来ます。',
	'unsubscribe_texte_email_liste_1' => 'Ｅメールアドレス@email@はメーリングリスト「@titre_liste@」より削除されまし
た。', # MODIF
	'unsubscribe_titre_email' => 'ニュースレターの購読を停止する',
	'unsubscribe_titre_email_liste' => 'メーリングリスト「@titre_liste@」の購読を停止する' # MODIF
);
