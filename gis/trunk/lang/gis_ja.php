<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/gis?lang_cible=ja
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_gis' => 'ポイントなし',
	'aucun_objet' => 'オブジェクトなし',

	// B
	'bouton_annuler_title' => '修正をキャンセルする。（すべての変更が消去されます）',
	'bouton_enregistrer_title' => '変更を保存します。',
	'bouton_lier' => 'このポイントを追加する',
	'bouton_supprimer_gis' => 'このポイントを完全に削除する',
	'bouton_supprimer_lien' => 'このリンクを削除する',

	// C
	'cfg_descr_gis' => '地理情報システム<br /><a href="https://contrib.spip.net/4189" class="spip_out">取り扱い説明書にアクセスする</a>',
	'cfg_inf_adresse' => '追加の住所フィールド（国、都市、地域、住所など）を表示する。',
	'cfg_inf_bing' => 'Bingの航空レイヤーには<a href=\'@url@\' class="spip_out">、Bingサイト</a>で作成するキーが必要です。',
	'cfg_inf_geocoder' => 'ジオコーダーの機能を有効にする（住所から検索し、座標から住所を取得する）',
	'cfg_inf_geolocaliser_user_html5' => 'ブラウザで許可されている場合、ポイントの作成時に、ユーザーの地理的位置の概算
が回復され、既定の位置が指定されます。',
	'cfg_inf_google' => 'Google レイヤーは GoogleMaps サイトで作成するキー<a href=\'@url@\' class="spip_out">を必要とする</a>',
	'cfg_inf_styles' => 'スタイルの追加フィールド（色、不透明度、厚さ）を表示します。',
	'cfg_lbl_activer_objets' => 'コンテンツの位置情報を有効にする',
	'cfg_lbl_adresse' => '住所フィールドの表示する',
	'cfg_lbl_api' => 'マッピングAPI',
	'cfg_lbl_api_key_bing' => 'Bingキー',
	'cfg_lbl_api_key_google' => 'グーグルマップキー',
	'cfg_lbl_api_microsoft' => 'マイクロソフトBing',
	'cfg_lbl_geocoder' => 'ジオコーダする',
	'cfg_lbl_geolocaliser_user_html5' => '作成時にユーザーの位置をマップにフォーカスする',
	'cfg_lbl_layer_defaut' => 'デフォルトレイヤー',
	'cfg_lbl_layers' => '提案されたレイヤー',
	'cfg_lbl_maptype' => 'マップの背景',
	'cfg_lbl_plugins_desactives' => '一部のプラグインを無効にする',
	'cfg_lbl_styles' => 'スタイルのフィールドを表示する',
	'cfg_titre_gis' => 'GISの設定',

	// E
	'editer_gis_editer' => 'このポイントを修正する',
	'editer_gis_nouveau' => '新規ポイントを作成する',
	'editer_gis_titre' => '位置ベースのポイント',
	'erreur_geocoder' => '検索結果なし',
	'erreur_recherche_pas_resultats' => '検索のうえ、当たるポイントはありません。',
	'erreur_xmlrpc_lat_lon' => '緯度と経度は引数に渡されなければならぬ',
	'explication_api_forcee' => 'API は、別のプラグインまたはスケルトンによって強いらる。',
	'explication_color' => '線の色は、CSSで設定される（既定：#0033FF）',
	'explication_fillcolor' => '塗りつぶし色は、CSSで設定される（既定：線の色から継承）',
	'explication_fillopacity' => '0〜1 充填不透明度（既定：0.2）',
	'explication_import' => 'GPX または KML 形式のファイルを書き込む。',
	'explication_layer_forcee' => 'レイヤーは別のプラグインまたはスケルトンによって強いらる。',
	'explication_layers' => '現在構成に記録されたレイヤーは<b>@nb@</b>層ある。',
	'explication_layers_un' => '現在構成に記録されたレイヤーは<b>1</b>層ある。',
	'explication_maptype_force' => 'マップの背景は、別のプラグインまたはスケルトンによって強いられる。',
	'explication_opacity' => 'ラインの不透明度は、0〜1（デフォルト； 0.5）',
	'explication_plugins_desactives' => 'これらのプラグインによって提供されるいくつかの機能は、もはや動作しなくなるの
で、ご注意してください。',
	'explication_weight' => '線の太さ（デフォルト： 5）',

	// F
	'formulaire_creer_gis' => '地理的に配置されたポイントを作成する。',
	'formulaire_modifier_gis' => '地理的に配置されたポイントを修正する。',

	// G
	'gis_pluriel' => '位置ベースのポイント',
	'gis_singulier' => '地理的に配置されたポイント',

	// I
	'icone_gis_tous' => '地理的に配置されたポイント',
	'info_1_gis' => '地理的に配置されたポイント',
	'info_1_objet_gis' => 'このポイントに関連するオブジェクトは、1つある。',
	'info_aucun_gis' => '地理的に配置されたポイントはありません。',
	'info_aucun_objet_gis' => 'このポイントに関連するオブジェクトはありません',
	'info_geolocalisation' => 'ジオロケーション',
	'info_id_objet' => '番号',
	'info_liste_gis' => '位置ベースのポイント',
	'info_nb_gis' => '地理的に配置されたポイントは@nb@層あります。',
	'info_nb_objets_gis' => 'このポイントに関連するオブジェクトは@nb@層あります。',
	'info_numero_gis' => 'ポイント番号',
	'info_objet' => '件名',
	'info_recherche_gis_zero' => '「@cherche_gis@」の結果はありません。',
	'info_supprimer_lien' => '切り離す',
	'info_supprimer_liens' => '全てのポイントを切り離す',
	'info_voir_fiche_objet' => 'カードを見る',

	// L
	'label_adress' => '住所',
	'label_code_pays' => '国コード',
	'label_code_postal' => '郵便番号',
	'label_color' => '色',
	'label_departement' => '県',
	'label_fillcolor' => '塗りつぶしの色',
	'label_fillopacity' => '塗りつぶしの不透明度',
	'label_import' => '書き込む',
	'label_inserer_modele_articles' => '記事に関連する',
	'label_inserer_modele_articles_sites' => '記事とサイトに関連する',
	'label_inserer_modele_auteurs' => '著者に関連する',
	'label_inserer_modele_centrer_auto' => '自動センタリングなし',
	'label_inserer_modele_centrer_fichier' => 'KLM/GPXファイルを中央に配置しない',
	'label_inserer_modele_controle' => 'コントロールを隠す',
	'label_inserer_modele_controle_type' => 'タイプを隠す',
	'label_inserer_modele_description' => '記述',
	'label_inserer_modele_documents' => 'ドキュメントに関連する',
	'label_inserer_modele_echelle' => 'スケール',
	'label_inserer_modele_fullscreen' => '全画面ボタン',
	'label_inserer_modele_gpx' => '重ね合わせするGPXファイル',
	'label_inserer_modele_hauteur_carte' => 'マップの高さ',
	'label_inserer_modele_identifiant' => 'ＩＤ',
	'label_inserer_modele_identifiant_opt' => 'ＩＤ（オプション）',
	'label_inserer_modele_identifiant_placeholder' => 'id_gis',
	'label_inserer_modele_kml' => '重ね合わせする KML ファイル',
	'label_inserer_modele_kml_gpx' => 'id_documentまたは URL',
	'label_inserer_modele_largeur_carte' => 'マップの幅',
	'label_inserer_modele_limite' => '最大ポイント数',
	'label_inserer_modele_localiser_visiteur' => '訪問者に焦点を当てる',
	'label_inserer_modele_mini_carte' => 'ミニシチュエーションマップ',
	'label_inserer_modele_molette' => 'ホイールをオフにする',
	'label_inserer_modele_mots' => '言葉に関連する',
	'label_inserer_modele_objets' => 'ポイントの種類',
	'label_inserer_modele_point_gis' => '保存されたポイントは一層だけ',
	'label_inserer_modele_point_libre' => 'フリーシングルポイント',
	'label_inserer_modele_points' => 'ポイントを隠す',
	'label_inserer_modele_rubriques' => 'セクションに関連する',
	'label_inserer_modele_sites' => 'サイトに関連する',
	'label_inserer_modele_titre_carte' => '地図の題名',
	'label_inserer_modele_tooltip' => 'ポイントフライオーバーでツールチップを表示する',
	'label_opacity' => '不透明度',
	'label_pays' => '国',
	'label_rechercher_address' => '住所を検索する',
	'label_rechercher_point' => 'ポイントを検索する',
	'label_region' => '地域',
	'label_ville' => '都市',
	'label_weight' => '厚さ',
	'lat' => '緯度',
	'libelle_logo_gis' => 'ポイントのロゴ',
	'lien_ajouter_gis' => 'このポイントを追加する',
	'lon' => '経度',

	// M
	'message_limite_atteinte' => '現在の表示制限よりも多くの位置ベースのポイントがあります。<br />全てを表示するには、<a href="@url@">このリンクをクリックしてください。</a>',

	// O
	'onglet_carte' => '地図',
	'onglet_liste' => 'リスト',

	// P
	'placeholder_geocoder' => '住所、都市、国、観光地.',

	// T
	'telecharger_gis' => '@format@形式でダウンロードする',
	'texte_ajouter_gis' => '地理的に配置されたポイントを追加する',
	'texte_creer_associer_gis' => '地理的に配置されたポイントを作成して付ける',
	'texte_creer_gis' => '地理的に配置されたポイントを作成する',
	'texte_modifier_gis' => '地理的に配置されたポイントを修正する',
	'texte_voir_gis' => '地理的に配置されたポイントを見る',
	'titre_bloc_creer_point' => '新規ポイントを付ける',
	'titre_bloc_points_lies' => '関連ポイント',
	'titre_bloc_rechercher_point' => 'ポイントを検索する',
	'titre_limite_atteinte' => '表示されたポイント数の制限に達した（@limite@）',
	'titre_nombre_utilisation' => '使用は1回',
	'titre_nombre_utilisations' => '使用は@nb@回',
	'titre_nouveau_point' => '新規ポイント',
	'titre_objet' => '題名',
	'toolbar_actions_title' => '図面を取り消す',
	'toolbar_buttons_circle' => '円を描く',
	'toolbar_buttons_marker' => 'ポイントを描く',
	'toolbar_buttons_polygon' => '多角形を描く',
	'toolbar_buttons_polyline' => '線を描く',
	'toolbar_buttons_rectangle' => '四角形を描く',
	'toolbar_edit_buttons_edit' => 'オブジェクトを修正する',
	'toolbar_edit_buttons_editdisabled' => '修正可能なオブジェクトはありません',
	'toolbar_edit_buttons_remove' => 'オブジェクトを削除する',
	'toolbar_edit_buttons_removedisabled' => '削除するオブジェクトはありません',
	'toolbar_edit_handlers_edit_tooltip_subtext' => '[キャンセル] をクリックして修正を削除する',
	'toolbar_edit_handlers_edit_tooltip_text' => 'ハンドルまたはマーカーを移動して、オブジェクトを修正する',
	'toolbar_edit_handlers_remove_tooltip_text' => '削除するのにオブジェクトをクリックする',
	'toolbar_finish_text' => '完了',
	'toolbar_finish_title' => '図面を完了する',
	'toolbar_handlers_marker_tooltip_start' => 'マーカーを配置するのにクリックする',
	'toolbar_handlers_polygon_tooltip_cont' => '多角形を描き続けるのにクリックする',
	'toolbar_handlers_polygon_tooltip_end' => '最初のポイントをクリックすると多角形を閉じる',
	'toolbar_handlers_polygon_tooltip_start' => '多角形を描き始めるのにクリックする',
	'toolbar_handlers_polyline_tooltip_cont' => '線を描き続けるのにクリックする',
	'toolbar_handlers_polyline_tooltip_end' => '最後の点をクリックすると線を終了する',
	'toolbar_handlers_polyline_tooltip_start' => '線を描き始めるのにクリックする',
	'toolbar_handlers_rectangle_tooltip_start' => '四角形を描き始めるのにクリックして移動する',
	'toolbar_handlers_simpleshape_tooltip_end' => '図面を描き終えるのにマウスを放す',
	'toolbar_undo_text' => '最後のポイントを削除する',
	'toolbar_undo_title' => '最後に描かれたポイントを削除する',

	// Z
	'zoom' => '拡大'
);
