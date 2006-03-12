<?

function lire_parametrage_auth_bd_externe () {

	include_ecrire('inc_meta');
	lire_metas();
	
	$bd_externe['serveur']=$GLOBALS['meta']['auth_bd_externe_serveur'];
	if (!($bd_externe['hostname']=$GLOBALS['meta']['auth_bd_externe_hostname'])) $bd_externe['hostname']="localhost";
	if (!$bd_externe['login']=$GLOBALS['meta']['auth_bd_externe_login']) $bd_externe['login']="";
	if (!$bd_externe['password']=$GLOBALS['meta']['auth_bd_externe_password']) $bd_externe['password']="";
	if (!$bd_externe['database']=$GLOBALS['meta']['auth_bd_externe_database']) $bd_externe['database']="";

	$bd_externe['parametrage_serveur_ok']=FALSE;
	if (($bd_externe['login']) AND ($bd_externe['password']) AND ($bd_externe['database'])) $bd_externe['parametrage_serveur_ok']=TRUE;

	if (!($bd_externe['table']=$GLOBALS['meta']['auth_bd_externe_table'])) $bd_externe['table']="";
	if (!($bd_externe['champ_cle']=$GLOBALS['meta']['auth_bd_externe_champ_cle'])) $bd_externe['champ_cle']="";
	if (!($bd_externe['table_jointure']=$GLOBALS['meta']['auth_bd_externe_table_jointure'])) $bd_externe['table_jointure']="";
	
	if (!($bd_externe['champ_login_ext']=$GLOBALS['meta']['auth_bd_externe_champ_login_ext'])) $bd_externe['champ_login_ext']="";
	if (!($bd_externe['champ_passwd']=$GLOBALS['meta']['auth_bd_externe_champ_passwd'])) $bd_externe['champ_passwd']="";
	if (!($bd_externe['type_passwd']=$GLOBALS['meta']['auth_bd_externe_type_passwd'])) $bd_externe['type_passwd']="md5";
	if (!($bd_externe['champ_alea']=$GLOBALS['meta']['auth_bd_externe_champ_alea'])) $bd_externe['champ_alea']="";
	
	if (!($bd_externe['champ_prenom']=$GLOBALS['meta']['auth_bd_externe_champ_prenom'])) $bd_externe['champ_prenom']="";
	if (!($bd_externe['champ_nom']=$GLOBALS['meta']['auth_bd_externe_champ_nom'])) $bd_externe['champ_nom']="";
	if (!($bd_externe['champ_bio']=$GLOBALS['meta']['auth_bd_externe_champ_bio'])) $bd_externe['champ_bio']="";
	if (!($bd_externe['champ_email']=$GLOBALS['meta']['auth_bd_externe_champ_email'])) $bd_externe['champ_email']="";
	if (!($bd_externe['champ_nom_site']=$GLOBALS['meta']['auth_bd_externe_champ_nom_site'])) $bd_externe['champ_nom_site']="";
	if (!($bd_externe['champ_url_site']=$GLOBALS['meta']['auth_bd_externe_champ_url_site'])) $bd_externe['champ_url_site']="";
	if (!($bd_externe['champ_pgp']=$GLOBALS['meta']['auth_bd_externe_champ_pgp'])) $bd_externe['champ_pgp']="";
	
	if (!($bd_externe['champ_statut']=$GLOBALS['meta']['auth_bd_externe_champ_statut'])) {
		$bd_externe['champ_statut']="";
		$bd_externe['val_redacteur']="";
		$bd_externe['val_administrateur']="";
	} else {
		if (!($bd_externe['val_redacteur']=$GLOBALS['meta']['auth_bd_externe_val_redacteur'])) $bd_externe['val_redacteur']="";
		if (!($bd_externe['val_administrateur']=$GLOBALS['meta']['auth_bd_externe_val_administrateur'])) $bd_externe['val_administrateur']="";
	}
	
	
	return($bd_externe);

}

function ecrire_parametrage_auth_bd_externe ($bd_externe) {

	include_ecrire('inc_meta');	
  ecrire_meta('auth_bd_externe_serveur',$bd_externe['serveur']);
	ecrire_meta('auth_bd_externe_hostname',$bd_externe['hostname']);
	ecrire_meta('auth_bd_externe_login',$bd_externe['login']);
	ecrire_meta('auth_bd_externe_password',$bd_externe['password']);
	ecrire_meta('auth_bd_externe_database', $bd_externe['database']);
	
	ecrire_meta('auth_bd_externe_table', $bd_externe['table']);
	ecrire_meta('auth_bd_externe_champ_cle', $bd_externe['champ_cle']);
	ecrire_meta('auth_bd_externe_table_jointure', $bd_externe['table_jointure']);
	
	ecrire_meta('auth_bd_externe_champ_login_ext', $bd_externe['champ_login_ext']);
	ecrire_meta('auth_bd_externe_champ_passwd', $bd_externe['champ_passwd']);
	ecrire_meta('auth_bd_externe_type_passwd', $bd_externe['type_passwd']);
	ecrire_meta('auth_bd_externe_champ_alea', $bd_externe['champ_alea']);

	ecrire_meta('auth_bd_externe_champ_prenom', $bd_externe['champ_prenom']);
	ecrire_meta('auth_bd_externe_champ_nom', $bd_externe['champ_nom']);
	ecrire_meta('auth_bd_externe_champ_bio', $bd_externe['champ_bio']);
	ecrire_meta('auth_bd_externe_champ_email', $bd_externe['champ_email']);
	ecrire_meta('auth_bd_externe_champ_nom_site', $bd_externe['champ_nom_site']);
	ecrire_meta('auth_bd_externe_champ_url_site', $bd_externe['champ_url_site']);
	ecrire_meta('auth_bd_externe_champ_pgp', $bd_externe['champ_pgp']);
 	
 	ecrire_meta('auth_bd_externe_champ_statut', $bd_externe['champ_statut']);
 	ecrire_meta('auth_bd_externe_val_redacteur', $bd_externe['val_redacteur']);
 	ecrire_meta('auth_bd_externe_val_administrateur', $bd_externe['val_administrateur']);
 	
}
?>