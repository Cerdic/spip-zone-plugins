<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

// Override d'un bug de spip 1.9.2c (1.9207)

if (is_readable(_DIR_RESTREINT_ABS.'action/logout.php')) {
  include(_DIR_RESTREINT_ABS.'action/logout.php');
  action_logout_dist();
}
else {
  include_spip('inc/cookie');

  // Issued from http://doc.spip.org/@action_logout_dist with version spip = 1.9208 - 1.9.2.d)
  function action_logout()
  {
	  global $auteur_session, $ignore_auth_http;
	  $logout =_request('logout');
	  $url = _request('url');
	  acs_log("logout $logout $url" . $auteur_session['id_auteur']);
	  // cas particulier, logout dans l'espace public
	  if ($logout == 'public' AND !$url)
		  $url = url_de_base();

	  // seul le loge peut se deloger (mais id_auteur peut valoir 0 apres une restauration avortee)
	  if (is_numeric($auteur_session['id_auteur'])) {
		  spip_query("UPDATE spip_auteurs SET en_ligne = DATE_SUB(NOW(),INTERVAL 15 MINUTE) WHERE id_auteur = ".$auteur_session['id_auteur']);
	  // le logout explicite vaut destruction de toutes les sessions
		  if ($_COOKIE['spip_session']) {
			  $session = charger_fonction('session', 'inc');
			  $session($auteur_session['id_auteur']);
			  preg_match(',^[^/]*//[^/]*(.*)/$,',
				    url_de_base(),
				    $r);
			  spip_setcookie('spip_session', '', -1,$r[1]);
			  spip_setcookie('spip_session', '', -1);
		  }
		  if ($_SERVER['PHP_AUTH_USER'] AND !$ignore_auth_http) {
			  include_spip('inc/actions');
			  if (verifier_php_auth()) {
			    ask_php_auth(_T('login_deconnexion_ok'),
				        _T('login_verifiez_navigateur'),
				        _T('login_retour_public'),
				          "redirect=". _DIR_RESTREINT_ABS,
				        _T('login_test_navigateur'),
				        true);
			    exit;
			  }
		  }
	  }
	  redirige_par_entete($url ? $url : generer_url_public('login'));
  }
}
?>
