<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

function formulaires_guestbook_charger_dist() {
	$valeurs = array(
		'name'=>$nom,
		'maj'=>date('Y-m-d H:i:s');,
		'ville'=>$ville,
		'email'=>$email,
		'note'=>$note,
		'texte'=>$texte,
	);
	$ip = $_SERVER['REMOTE_ADDR'];
	return $valeurs;
}
function formulaires_guestbook_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}


function formulaires_guestbook_traiter_dist() {
	$nom	= _request('nom');
	$ville	= _request('ville');
	$email	= _request('ville');
	$note	= _request('note');
	$texte	= _request('texte');
	sql_insertq("spip_livre", array('email' => $email, 'nom' => $nom, 'ville' => $ville, 'maj' => $maj, 'note' => $note, 'texte' => $texte, 'ip' => $ip));
}


?>