<?php

	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Yohann Prigent (potter64) repris des travaux de Bernard Blazin (http://www.plugandspip.com )
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/

function formulaires_guestbook_charger_dist() {
	$valeurs = array(
		'name'=>$nom,
		'ville'=>$ville,
		'email'=>$email,
		'note'=>$note,
		'texte'=>$texte,
	);
	return $valeurs;
}
function formulaires_guestbook_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}


function formulaires_guestbook_traiter_dist() {
	include_spip('base/abstract_sql');
	$ip = $GLOBALS['ip'];
	$texte	= _request('texte');
	$email	= _request('email');
	$nom	= _request('nom');
	$ville	= _request('ville');
	$post_stat = 'publie';
	$note	= _request('note');
	$date = date('Y-m-d H:i:s');
	sql_insertq("spip_guestbook", array('id_message' => "", 'message' => $texte, 'email' => $email, 'nom' => $nom, 'ville' => $ville, 'statut' => $post_stat, 'ip' => $ip, 'note' => $note, 'date' => $date));
	$message = 'Merci pour le message !';
	return $message;
	}


?>