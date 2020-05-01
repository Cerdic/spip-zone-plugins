<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/abonnement-abonnements?lang_cible=ja
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_lien_abonnement' => 'この定期購読を追加',

	// C
	'champ_date_au_label' => '終了：',
	'champ_date_debut_label' => 'この定期購読の開始日',
	'champ_date_du_label' => '開始：',
	'champ_date_echeance_label' => '直近の期限',
	'champ_date_fin_allonger_label' => '終了日の修正が可能です。',
	'champ_date_fin_label' => 'この定期購読の終了日',
	'champ_dates_debut_label' => '定期購読の開始日',
	'champ_dates_fin_label' => '定期購読の終了日',
	'champ_id_abonnements_offre_label' => '定期購読プラン',
	'champ_id_auteur_label' => 'ユーザー',
	'champ_notifier_statut_label' => '定期購読のステータス',

	// E
	'erreur_id_abonnements_offre' => '定期購読プランから選択してください',

	// I
	'icone_creer_abonnement' => '定期購読を申し込む',
	'icone_modifier_abonnement' => 'この定期購読を変更する',
	'icone_renouveler_abonnement' => 'この定期購読を更新する。',
	'info_1_abonnement' => '1件の定期購読',
	'info_1_abonnement_actif' => '1件の有効な定期購読',
	'info_1_abonnement_inactif' => '1件の無効な定期購読',
	'info_1_abonnement_notifier' => '通知が必要な定期購読',
	'info_abonnements_auteur' => 'この人の全定期購読', # RELIRE
	'info_aucun_abonnement' => '定期購読はありません',
	'info_aucun_abonnement_actif' => '有効な定期購読はありません',
	'info_aucun_abonnement_inactif' => '無効な定期購読はありません',
	'info_aucun_abonnement_notifier' => '通知が必要な定期購読はありません',
	'info_date_fin' => '終了日：@date@',
	'info_nb_abonnements' => '@nb@ 件の定期購読',
	'info_nb_abonnements_actifs' => '@nb@ 件の有効な定期購読',
	'info_nb_abonnements_inactifs' => '@nb@ 件の無効な定期購読',
	'info_nb_abonnements_notifier' => '通知の必要な定期購読が@nb@件あります',
	'info_numero_abbr' => '番号',
	'info_numero_abbr_maj' => '番号',

	// J
	'job_desactivation' => '定期購読 @id@　を無効にする',

	// L
	'label_date_a_partir' => '開始：',
	'label_date_depuis' => '開始日：',
	'label_dates' => '日付',
	'label_duree' => '期間',
	'label_montant' => '合計',
	'label_statut' => 'ステータス',

	// N
	'notification_echeance_chapo' => '<p>こんにちは @nom@さん,</p>',
	'notification_echeance_corps' => '<p>@nom@さん、こんにちは</p>
		<p>@nom_site_spip@を "@offre@"プランで定期購読されている方にこのメールを配信しています。</p>
		<p>この定期購読は<strong>@echeance@</strong>で期限が切れます。<br/>
		期限が切れる前に更新をおすすめします。</p>
		<p>ご不明な点がありましたらご連絡ください。よろしくお願い致します。</p>',
	'notification_echeance_corps_apres' => '<p>@nom_site_spip@を« @offre@ »プランで定期購読されていた方にメールを差し上げています。</p>
	<p>この定期購読は : 
<strong>@echeance@</strong>前に期限切れになりました。<br/>
	更新をおすすめします。</p>',
	'notification_echeance_corps_avant' => '<p>@nom_site_spip@を« @offre@ »プランで定期購読されている方にこのメールを配信しています。</p>
	<p>この定期購読は : 
<strong>@echeance@</strong>後に期限切れになります。<br/>
	期限が切れる前の更新をおすすめします。</p>',
	'notification_echeance_corps_pendant' => '<p>@nom_site_spip@を« @offre@ »プランで定期購読されている方にこのメールをお送りしています。</p>
	<p>この定期購読の期限は今日までです。<br/>
	期限が切れる前の更新をおすすめします。</p>',
	'notification_echeance_signature' => '<p>ご不明な点がございましたら、ご連絡ください。よろしくお願い致します。</p>',
	'notification_echeance_sujet_jours_apres' => 'あなたの定期購読は @duree@ 日前に終了しています !',
	'notification_echeance_sujet_jours_avant' => 'あなたの定期購読は あと@duree@日で終了します !',
	'notification_echeance_sujet_jours_pendant' => 'あなたの定期購読は今日終了します !',
	'notification_echeance_sujet_mois_apres' => 'あなたの定期購読は @duree@ か月前に終了しました !',
	'notification_echeance_sujet_mois_avant' => 'あなたの定期購読はあと@duree@か月で終了します !',
	'notification_echeance_sujet_mois_pendant' => 'あなたの定期購読は今月で終了します !',

	// R
	'retirer_lien_abonnement' => 'この定期購読を取り消す',
	'retirer_tous_liens_abonnements' => 'すべての定期購読を取り消す',

	// S
	'statut_actif' => '有効な',
	'statut_actifs' => '有効な',
	'statut_inactif' => '無効な',
	'statut_inactifs' => '無効な',
	'statut_tous' => '全', # RELIRE

	// T
	'texte_ajouter_abonnement' => '定期購読を追加する',
	'texte_changer_statut_abonnement' => 'この定期購読は :',
	'texte_creer_associer_abonnement' => '定期購読を作成して紐付ける', # RELIRE
	'titre_abonnement' => '定期購読',
	'titre_abonnements' => '定期購読',
	'titre_abonnements_rubrique' => '見出しの購読',
	'titre_abonnements_suivre' => '定期購読を続ける', # RELIRE
	'titre_langue_abonnement' => 'この定期購読の使用言語',
	'titre_logo_abonnement' => 'この定期購読のロゴ'
);
