<?php
/*
 * Plugin gestion des profils
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

include_spip('base/abstract_sql');
include_spip('inc/filtres');
/**
 * Connecter un profil
 *
 * @param int $id_auteur
 * @param string $auth_source
 */
function inc_profil_connecter_dist($id_auteur,$auth_source='spip'){
	
	if (!test_espace_prive() && (!$GLOBALS['visiteur_session'] /*|| ($GLOBALS['visiteur_session']['id_auteur']!=$id_auteur)*/)) {
		$session = charger_fonction('session','inc');
		$res = sql_select("*",'spip_auteurs','id_auteur='.intval($id_auteur));
		if ($row = sql_fetch($res)){
			// signaler la source de l'authent
			$row['auth'] = $auth_source;
			// creer la session
			$spip_session = $session($row);
			// creer le cookie
			$_COOKIE['spip_session'] = $spip_session;
			preg_match(',^[^/]*//[^/]*(.*)/$,',
				   url_de_base(),
				   $r);
			include_spip('inc/cookie');
			spip_setcookie('spip_session', $spip_session, time() + 3600 * 24 * 14, $r[1]);
			// authentifier le nouveau compte a la volee
			$auth = charger_fonction('auth','inc');
			$auth();
			
			// mettre a jour la liste des amis si besoin (ie si connexion via autre source que spip)
			// il n'est pas autorise de stocker la liste des amis fb
			/*if ($auth_source!=='spip'){
				if ($amis_update_liste = charger_fonction('amis_update_liste','inc',true))
					$amis_update_liste($id_auteur);
			}*/
		}
	}
}

?>