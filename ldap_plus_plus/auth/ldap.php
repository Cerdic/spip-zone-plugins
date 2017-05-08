<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
// Authentifie via LDAP et retourne la ligne SQL decrivant l'utilisateur si ok

// https://code.spip.net/@inc_auth_ldap_dist
function auth_ldap ($login, $pass) {
	include_spip('base/abstract_sql');
	if (!spip_connect_ldap())
		return false;
	#spip_log("ldap $login " . ($pass ? "mdp fourni" : "mdp absent"));
	// Securite contre un serveur LDAP laxiste
	if (!$login || !$pass) return array();
	// Utilisateur connu ?
	if (!($dn = auth_ldap_search($login, $pass))) return array();
	// Si l'utilisateur figure deja dans la base, y recuperer les infos
	$result = sql_fetsel("*", "spip_auteurs", array("login=" . sql_quote($login) . " AND source='ldap'"));
	if ($result) {
		$id_auteur = $result['id_auteur'];
		//On reverifie le memberOf
		if(defined('_DIR_PLUGIN_GROUPES')) {
			include_spip('formulaires/auteur_ajouter');
			//On efface les anciens liens
			sql_delete('spip_groupes_auteurs', 'id_auteur='.$id_auteur);
			if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
				sql_delete('spip_zones_auteurs', 'id_auteur='.$id_auteur);
			}
			
			//On recupere les valeurs			
			$val = auth_ldap_retrouver($dn);
			$memberOf = explode(',',$val['memberOf']);
			$liens = unserialize(lire_meta('ldaplus_memberof'));
			if(is_array($liens)>0) {
				foreach($liens as $k=>$v) {
					if(in_array($k, $memberOf)) {
						foreach($v as $k1=>$v1) {
							ajouter_auteur_groupe_func($v1, $id_auteur);
							if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
								ajouter_auteur_zone_func($v1,$id_auteur);
							}
						}
					}
				}
			}
		}
		return $result;
	}

	// sinon importer les infos depuis LDAP, 
	// avec le statut par defaut a l'install

	$n = auth_ldap_inserer($dn, $GLOBALS['meta']["ldap_statut_import"], $login);
	if ($n)	return sql_fetsel("*", "spip_auteurs", "id_auteur=$n");
	spip_log("Creation de l'auteur '$login' impossible");
	return array();
}

// https://code.spip.net/@auth_ldap_search
function auth_ldap_search($login, $pass)
{
	$ldap = spip_connect_ldap();
	$ldap_link = $ldap['link'];
	$ldap_base = $ldap['base'];
	
	
	$att = recuperer_attributs_ldap();
	$att = $att['login'];
	$login_search = preg_replace("/[^-@._\s\d\w]/", "", $login);
	
	
	$result = @ldap_search($ldap_link, $ldap_base, "$att=$login_search", array("dn"));
	$info = @ldap_get_entries($ldap_link, $result);
	if (is_array($info) AND $info['count'] == 1) {
		$dn = $info[0]['dn'];
		if (@ldap_bind($ldap_link, $dn, $pass)) return $dn;
	}
	return '';
}

function auth_ldap_retrouver($dn, $desc='')
{
	if (!$desc) $desc = recuperer_attributs_ldap();

	$ldap_link = spip_connect_ldap();
	$ldap_link = $ldap_link['link'];
	$result = @ldap_read($ldap_link, $dn, "objectClass=*", array_values($desc));

	if (!$result) return array();
	// Recuperer les donnees du premier (unique?) compte de l'auteur
	$val = @ldap_get_entries($ldap_link, $result);
	if (!is_array($val) OR !is_array($val[0])) return array();
	$val = $val[0];

	// Convertir depuis UTF-8 (jeu de caracteres par defaut)
	include_spip('inc/charsets');

	foreach ($desc as $k => $v)
		$desc[$k] = importer_charset($val[strtolower($v)][0], 'utf-8');
	return $desc;
}


// https://code.spip.net/@auth_ldap_inserer
// Ajout du paramètre $login
function auth_ldap_inserer($dn, $statut, $login='', $desc='')
{
	// refuser d'importer n'importe qui 
	if (!$statut) return array();

	$val = auth_ldap_retrouver($dn);
	if (!$val) return array();

	$spip_auteur = array('nom'=>'', 'bio'=>'', 'email'=>'', 'nom_site'=>'', 'url_site'=>'', 'login'=>'', 'pgp'=>'');
	
	foreach($spip_auteur as $k=>$v) {
		$spip_auteur[$k] = $val[$k];
	}

	$spip_auteur['source'] = 'ldap';
	$spip_auteur['pass'] = '';
	if($login != '') 
		$spip_auteur['login'] = $login;
	$spip_auteur['statut'] = $statut;
	
	$id_auteur = sql_insertq('spip_auteurs', $spip_auteur);

	//AJOUT DANS AUTEUR ELARGI SI LE PLUGIN INSCRIPTION 2 EST PRESENT
	if(defined('_DIR_PLUGIN_INSCRIPTION2')) {
			include_spip('ldaplus_fonctions');
			$auteurs_elargis = lister_champs_auteurs_elargis();
			foreach($auteurs_elargis as $k=>$v) {
				$auteurs_elargis[$k] = $val[$k];
			}
			$auteurs_elargis = array_merge($auteurs_elargis, array('id_auteur'=>$id_auteur));
			sql_insertq('spip_auteurs_elargis', $auteurs_elargis);
	}	
	
	if(defined('_DIR_PLUGIN_GROUPES')) {
		include_spip('formulaires/auteur_ajouter');
		$memberOf = explode(',',$val['memberOf']);
		$liens = unserialize(lire_meta('ldaplus_memberof'));
		if(is_array($liens)) {
			foreach($liens as $k=>$v) {
				if(in_array($k, $memberOf)) {
					foreach($v as $k1=>$v1) {
						spip_log('$k1 = '.$k1.' $v1 = '.$v1, 'groupes');
						ajouter_auteur_groupe_func($v1, $id_auteur);
						if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
							ajouter_auteur_zone_func($v1,$id_auteur);
						}
					}
				}
			}
		}
	}
	
	return $id_auteur;
}


function recuperer_attributs_ldap() {
	$attributs=unserialize(lire_meta('ldaplus_chp_auteur'));
	if(!$attributs) {
		echo "VOUS DEVEZ D'ABORD CONFIGURER LDAPLUS";
		exit();
	}
	if(defined('_DIR_PLUGIN_INSCRIPTION2')) {
		$attributs = array_merge($attributs, unserialize(lire_meta('ldaplus_chp_elargis')));
		unset($attributs['id_auteur']);
	}
	$attributs['memberOf'] = 'memberOf';

	return $attributs;
}

?>
