<?php

/* Un mécanisme d'authentification qui ne vérifie rien, à part
   l'autorisation 'incarner' */
function auth_incarner_dist ($login, $password, $serveur='', $phpauth=false) {

	// retrouver le login
	$login = auth_spip_retrouver_login($login);
	// login inconnu, n'allons pas plus loin
	if (!$login) return array();

  include_spip('inc/autoriser');
  if ( ! autoriser('incarner')) return array();

	$row = sql_fetsel("*", "spip_auteurs",
                    "login=" . sql_quote($login,$serveur,'text') .
                    " AND statut<>'5poubelle'",'','','','',$serveur);

  $cle = base64_encode(openssl_random_pseudo_bytes(16));

  include_spip('inc/config');
  include_spip('inc/cookie');

  ecrire_config('incarner/cle', $cle);
  spip_setcookie('spip_cle_incarner', $cle);

	return $row;
}
