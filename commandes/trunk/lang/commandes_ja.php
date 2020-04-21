<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/commandes?lang_cible=ja
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'abbr_hors_taxe' => '税別',
	'abbr_prix_unitaire' => '単価',
	'abbr_quantite' => '数量',
	'abbr_total_ht' => '税別合計',
	'abbr_toutes_taxes_comprises' => '税込み合計価格',
	'action_facture' => '計算書', # MODIF
	'action_modifier' => '修正する',
	'action_supprimer' => '削除する',

	// B
	'bonjour' => 'こんにちは', # RELIRE

	// C
	'commande_client' => '顧客', # RELIRE
	'commande_creer' => '注文する',
	'commande_date' => '日付',
	'commande_date_paiement' => '支払日',
	'commande_echeances_date_debut' => '定期支払の開始日',
	'commande_echeances_paiements_infini' => '残りの支払い',
	'commande_echeances_paiements_previsions' => '予定されている支払',
	'commande_echeances_paiements_tous' => '全支払い',
	'commande_echeances_type' => '支払方法',
	'commande_echeances_type_annee' => '年払い',
	'commande_echeances_type_mois' => '月払い',
	'commande_echeances_type_vide' => '一回払い',
	'commande_editer' => '注文を編集する', # RELIRE
	'commande_modifier' => '注文を修正する',
	'commande_montant' => '合計',
	'commande_nouvelle' => '新たな注文',
	'commande_numero' => '注文',
	'commande_reference' => 'レファレンス',
	'commande_reference_numero' => 'レファレンス番号',
	'commande_statut' => 'ステータス',
	'commande_titre' => '注文',
	'commandes_titre' => '注文', # RELIRE
	'configurer_notifications_commandes' => '通知を設定する',
	'configurer_titre' => '注文のプラグインを設定する',
	'confirmer_supprimer_commande' => '本当に注文を取り消しますか？',
	'confirmer_supprimer_detail' => '本当にこの注文詳細を取り消しますか？',
	'contact_label' => '連絡先 :',
	'contenu' => '内容',

	// D
	'date_commande_label' => '作成日',
	'date_commande_label_court' => '作成',
	'date_envoi_label' => '送信日',
	'date_envoi_label_court' => '送信',
	'date_maj_label' => '最終更新日',
	'date_paiement_label' => '支払日',
	'date_paiement_label_court' => '支払',
	'designation' => '名称', # RELIRE
	'detail_ajouter' => '注文の詳細を追加する',
	'detail_champ_descriptif_explication' => 'SPIPの既存データを指定すればブランクのままで構いません。その場合、そのデータのタイトルが自動的に使われます。', # RELIRE
	'detail_champ_descriptif_label' => '説明',
	'detail_champ_id_objet_label' => 'SPIPデータの識別子', # RELIRE
	'detail_champ_objet_label' => 'SPIPデータのタイプ', # RELIRE
	'detail_champ_prix_unitaire_ht_label' => '税別単価',
	'detail_champ_quantite_label' => '数量',
	'detail_champ_reduction_label' => '割引', # RELIRE
	'detail_champ_taxe_label' => '税',
	'detail_creer' => '注文の詳細を作成する',
	'detail_modifier' => 'この注文の詳細を修正する',
	'detail_titre' => '注文の詳細',
	'details_commande' => '注文の詳細 :',
	'details_titre' => '注文の詳細',

	// E
	'erreur_reference_existante' => '同じレファレンスの注文が既に存在します。',
	'erreur_reference_inexistante' => 'このレファレンスの注文はありません。',
	'etat' => '条件', # RELIRE
	'explication_accueil_encours' => 'トップページに有効な注文を表示しますか ?',
	'explication_bank_uid' => '定期購読の金融機関用固有識別子', # RELIRE
	'explication_choix_statuts_actifs' => '有効な注文に対応するステータス', # RELIRE
	'explication_statuts_actifs' => 'ステータスによっては注意が必要な注文もあります。こちらからのアクションを必要とする« 有効 »な注文です。そのような注文をトップページ上の承認待ちリスト内に表示することができます。', # RELIRE
	'explications_notifications_statuts' => '通知を送信する条件:',
	'explications_notifications_statuts_aucune' => '通知は無効になっています',

	// F
	'facture_date' => '日付 : <span>@date@</span>',
	'facture_num' => '計算書 番号<span>@num@</span>', # MODIF
	'facture_titre' => '計算書', # RELIRE
	'facture_voir' => '計算書を見る',

	// I
	'info_1_commande' => '1 件の注文',
	'info_1_commande_active' => '1 件の有効な注文',
	'info_1_commande_statut_abandonne' => '中断された注文', # RELIRE
	'info_1_commande_statut_attente' => '承認待ちの注文',
	'info_1_commande_statut_encours' => '作成中の注文',
	'info_1_commande_statut_envoye' => '送信された注文',
	'info_1_commande_statut_erreur' => 'エラーの注文',
	'info_1_commande_statut_partiel' => '一部支払い済みの注文',
	'info_1_commande_statut_paye' => '支払い済みの注文',
	'info_1_commande_statut_retour' => '戻された注文', # RELIRE
	'info_1_commande_statut_retour_partiel' => '一部戻された注文', # RELIRE
	'info_1_detail' => '注文の詳細',
	'info_aucun_client' => 'この注文にはだれも紐づけされていません', # RELIRE
	'info_aucun_commande' => '注文はありません',
	'info_aucun_detail' => '注文の詳細はありません',
	'info_commande_vide' => 'この注文には項目がありません', # RELIRE
	'info_commandes' => '注文',
	'info_date_envoi_vide' => '送信されていない注文', # RELIRE
	'info_date_non_definie' => '未定義',
	'info_date_paiement_vide' => '未払いの注文',
	'info_nb_commandes' => '@nb@ 件の注文',
	'info_nb_commandes_actives' => '@nb@ 件の有効な注文',
	'info_nb_commandes_statut_abandonne' => '@nb@ 件の中断された注文', # RELIRE
	'info_nb_commandes_statut_attente' => '@nb@ 件の注文が承認待ちです',
	'info_nb_commandes_statut_envoye' => '@nb@ 件の注文が送信されました', # RELIRE
	'info_nb_commandes_statut_erreur' => '@nb@ 件のエラー注文', # RELIRE
	'info_nb_commandes_statut_partiel' => '@nb@ 件の一部支払い済みの注文',
	'info_nb_commandes_statut_paye' => '@nb@ 件の支払い済みの注文',
	'info_nb_commandes_statut_retour' => '@nb@ 件の指し戻された注文',
	'info_nb_commandes_statut_retour_partiel' => '@nb@ 件の一部差し戻された注文',
	'info_nb_commandse_statut_encours' => '@nb@ 件の手続き中の注文',
	'info_nb_details' => '@nb@ 件の注文詳細',
	'info_numero' => '注文番号 :',
	'info_numero_commande' => '注文番号 :',
	'info_sans_descriptif' => '説明なし',
	'info_toutes_commandes' => 'すべての注文',

	// L
	'label_actions' => 'アクション',
	'label_commande_dates' => '日付',
	'label_dont_taxe' => '内、税金分', # RELIRE
	'label_filtre_clients' => '顧客', # RELIRE
	'label_filtre_dates' => '日付',
	'label_filtre_echeances_type' => '支払方法',
	'label_filtre_paiement' => '支払方法',
	'label_filtre_tous' => 'すべて', # RELIRE
	'label_filtre_tous_clients' => 'すべての顧客', # RELIRE
	'label_filtre_tous_echeances_type' => 'すべての支払い方法',
	'label_filtre_tous_mode_paiements' => 'すべての方法',
	'label_filtre_toutes' => 'すべて', # RELIRE
	'label_filtre_toutes_dates' => 'すべての日付',
	'label_infos' => 'お知らせ', # RELIRE
	'label_montant_ttc' => '税込み合計',
	'label_prix' => '価格',
	'label_prix_unitaire' => '税別単価',
	'label_quantite' => '数量',
	'label_recherche' => 'サーチする',
	'label_reduction' => '割引', # RELIRE
	'label_statuts_actifs' => 'ステータス',
	'label_taxe' => '税',
	'label_total_ht' => '税別合計',

	// M
	'merci_de_votre_commande' => 'ご注文を確かに受け取りました。ありがとうございました。',
	'merci_de_votre_commande_paiement' => '<b>@reference@</b>の注文を確かに受け取りました。しばらくお待ちください。', # RELIRE
	'modifier_commande_statut' => 'この注文は :',
	'montant' => '合計',

	// N
	'nom_bouton_plugin' => '注文',
	'notifications_activer_explication' => '注文の通知をメールで送信しますか ?',
	'notifications_activer_label' => '有効にする',
	'notifications_cfg_titre' => '通知',
	'notifications_client_explication' => '顧客に通知を送信しますか ?',
	'notifications_client_label' => '顧客',
	'notifications_expediteur_administrateur_label' => 'アドミニストレータを選ぶ :',
	'notifications_expediteur_choix_administrateur' => 'アドミニストレータ',
	'notifications_expediteur_choix_email' => 'メール',
	'notifications_expediteur_choix_webmaster' => 'ウェブ管理者',
	'notifications_expediteur_email_label' => '発信者のメール :',
	'notifications_expediteur_explication' => 'この販売者と購入者に対する通知の発信者を選ぶ',
	'notifications_expediteur_label' => '発信者',
	'notifications_expediteur_webmaster_label' => 'ウェブ管理者を選ぶ :',
	'notifications_parametres' => '通知のパラメーター',
	'notifications_quand_explication' => 'どのようなステータスの変化で通知を送信しますか ?',
	'notifications_quand_label' => '送信条件', # RELIRE
	'notifications_vendeur_administrateur_label' => 'アドミニストレータの選択（複数可） :',
	'notifications_vendeur_choix_administrateur' => 'アドミニストレータ',
	'notifications_vendeur_choix_email' => 'メール',
	'notifications_vendeur_choix_webmaster' => 'ウェブ管理者',
	'notifications_vendeur_email_explication' => 'カンマで区切られたメールを取り出す :', # RELIRE
	'notifications_vendeur_email_label' => '販売者のメール :',
	'notifications_vendeur_explication' => '販売者へ送信する通知の受信者を選択する', # RELIRE
	'notifications_vendeur_label' => '販売者',
	'notifications_vendeur_webmaster_label' => 'ウェブ管理者を選択する（複数可） :',

	// P
	'parametres_cfg_titre' => 'パラメーター',
	'passer_la_commande' => '注文を出す', # RELIRE

	// R
	'reference' => 'レファレンス',
	'reference_label' => 'レファレンス :',
	'reference_ref' => 'リファレンス @ref@',

	// S
	'statut_abandonne' => '中断された',
	'statut_attente' => '承認待ち',
	'statut_encours' => '進行中', # RELIRE
	'statut_envoye' => '送信された',
	'statut_erreur' => 'エラー',
	'statut_label' => 'ステータス :',
	'statut_partiel' => '一部支払い済み',
	'statut_paye' => '支払い済み',
	'statut_poubelle' => 'ゴミ箱',
	'statut_retour' => '差し戻された',
	'statut_retour_partiel' => '一部差し戻し',
	'supprimer' => '削除する',

	// T
	'texte_changer_statut_commande' => 'この注文は :',
	'texte_changer_statut_commande_detail' => 'この注文の詳細は :',
	'titre_adresse_client' => '顧客のメールアドレス',
	'titre_adresse_commande' => 'この注文に関連するメールアドレス',
	'titre_adresse_contact' => '連絡先のメールアドレス',
	'titre_adresses_associees' => '関連するメールアドレス', # RELIRE
	'titre_adresses_client' => '顧客の住所',
	'titre_adresses_commande' => 'この注文に関連するメールアドレス',
	'titre_commandes_actives' => '有効な注文',
	'titre_contenu_commande' => '注文の内容',
	'titre_informations_client' => '顧客',
	'titre_statuts_actifs_parametres' => '有効な注文',
	'type_adresse_livraison' => '商品の発送',

	// U
	'une_commande_sur' => '@nom@ 様の注文',

	// V
	'votre_commande_sur' => '@nom@ 様の注文'
);
