<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Upgrade de la base
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function openid_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;

	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/openid');
		if ($current_version==0.0){
			include_spip('base/create');
			maj_tables('spip_auteurs');
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,"0.2","<")){
			$res = sql_select('id_auteur,openid','spip_auteurs',"openid<>''");
			while ($row = sql_fetch($res)){
				$openid = rtrim($row['openid'],'/');
				// si pas de protocole, mettre http://
				if ($openid  AND !preg_match(';^[a-z]{3,6}://;i',$openid ))
					$openid = "http://".$openid;
				if ($openid!==$row['openid']){
					sql_updateq('spip_auteurs',array('openid'=>$openid),'id_auteur='.intval($row['id_auteur']));
				}
			}
			ecrire_meta($nom_meta_base_version,$current_version="0.2",'non');
		}
		if (version_compare($current_version,"0.3","<")){
			// un index ne peut pas etre mis sur un champ de type texte (dixit mysql)
			sql_alter("TABLE spip_auteurs DROP INDEX openid");
			ecrire_meta($nom_meta_base_version,$current_version="0.3",'non');
		}		
	}
}

/**
 * Desinstallation du plugin
 *
 * @param string $nom_meta_base_version
 */
function openid_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_auteurs DROP openid");
	effacer_meta($nom_meta_base_version);
}
	

?>
