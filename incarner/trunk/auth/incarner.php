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

  /* mise à jour de la clé aléatoire utilisée pour ce webmestre */
  include_spip('inc/config');
  include_spip('inc/cookie');

  $cle_actuelle = $_COOKIE['spip_cle_incarner'];
  $cles = lire_config('incarner/cles') ? lire_config('incarner/cles') : array();

  $nouvelle_cle = urlencode(openssl_random_pseudo_bytes(16));

  if (autoriser('webmestre')) {
    $cles[$row['id_auteur']] = $nouvelle_cle;
  } else {
    $i = array_search($cle_actuelle, $cles);
    $cles[$i] = $nouvelle_cle;
  }

  ecrire_config('incarner/cles', $cles);
  spip_setcookie('spip_cle_incarner', $nouvelle_cle);

	return $row;
}
