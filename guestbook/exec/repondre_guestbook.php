<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
function exec_repondre_guestbook(){
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('guestbook:titre'), "", "");
	if (strlen(_request('reponse'))<=1) {
		if (_request('reponse') AND strlen(_request('reponse'))<=1) {
			$message_err = _T('guestbook:erreur_champ_remplir');
		}
		else {
			$message_err = 'none';
		}
		echo recuperer_fond('prive/repondre_guestbook',array(
			'stat_page' => 'rien',
			'no_message' => $message_err,
			'id_message' => _request('id_message')
		));
	}
	if (strlen(_request('reponse'))>1) {
		sql_insertq('spip_guestbook_reponses', array(
			'id_reponse' => '',
			'id_message' => _request('id_message'),
			'date' => date('Y-m-d H:i:s'),
			'message' => _request('reponse'),
			'statut' => 'publie',
			'id_auteur' => _request('id_auteur')
		));
		echo "<p style='color: green; font-size: 24px;'>"._T('guestbook:reponse_ok')."</p>";
		echo "<p style='color: gray; font-size: 20px;'><a href='".generer_url_ecrire('controle_guestbook')."'>"._T('guestbook:continuer_moderation')."</a></p>";
	}
	echo fin_page();
}
?>