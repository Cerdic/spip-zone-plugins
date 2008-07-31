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

if (!defined("_ECRIRE_INC_VERSION")) return;

// Authentifie et retourne la ligne SQL decrivant l'utilisateur si ok

// http://doc.spip.org/@inc_auth_spip_dist
function inc_auth_spip_dist ($login, $pass, $hash="", $shanext="") {

  include_spip('lib/SHA-256/sha256.class');

  // si envoi non crypte, crypter maintenant
	if (!$hash AND $pass) {
		$row = sql_fetsel("alea_actuel, alea_futur", "spip_auteurs", "login=" . sql_quote($login));

		if ($row) {
			$hash = SHA256::hash($row['alea_actuel'] . $pass);
			$shanext = SHA256::hash($row['alea_futur'] . $pass);
		}
	}
	// login inexistant ou mot de passe vide
	if (!$hash) return array();

	$row = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login) . " AND pass=" . sql_quote($hash) . " AND statut<>'5poubelle'");

	// login/mot de passe incorrect
	if (!$row) return array(); 

	if ($row['statut'] == 'nouveau') {
		include_spip('inc/auth');
		$row['statut'] = acces_statut($row['id_auteur'], $row['statut'], $row['bio']);
	}

	// fait tourner le codage du pass dans la base
	if ($shanext) {
		include_spip('inc/acces'); // pour creer_uniqid
		@sql_update('spip_auteurs', array('alea_actuel' => 'alea_futur', 'pass' => sql_quote($shanext), 'alea_futur' => sql_quote(creer_uniqid())), "id_auteur=" . $row['id_auteur']);
		// En profiter pour verifier la securite de tmp/
		verifier_htaccess(_DIR_TMP);
	}
	return $row;
}

?>
