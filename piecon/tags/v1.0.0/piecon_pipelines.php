<?php
/**
 * Piecon
 * Insertion de la librairie Piecon dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2012 - Distribué sous licence MIT
 * 
 * Fichier de définition des pipelines
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * On ajoute la librairie js dans le privé et public
 * 
 * @param $flux array
 * 		L'array des plugins déjà inséré
 * @return $flux array
 * 		L'array des plugins mis à jour
 */
function piecon_jquery_plugins($plugins){
	$plugins[] = 'javascript/piecon.js';
	return $plugins;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * On ajoute la configuration de la librairie dans le head
 * 
 * @param $flux array
 * 		Le contexte du pipeline
 * @return $flux array
 * 		Le contexte du pipeline modifié
 */
function piecon_insert_head($flux){
	include_spip('inc/config');
	$config_piecon = lire_config('piecon');
	$config = false;
	if(
		(isset($config_piecon['color']) && $config_piecon['color'] != '')
		OR (isset($config_piecon['background']) && $config_piecon['background'] != '')
		OR (isset($config_piecon['shadow']) && $config_piecon['shadow'] != '')
		OR (isset($config_piecon['fallback']) && $config_piecon['fallback'] != 'false')){
		$flux .= "\n\n";
		$flux .= '<script type="text/javascript">
if(window.Piecon){
	Piecon.setOptions({';
		if(isset($config_piecon['color']) && $config_piecon['color'] != ''){
			$flux .= "\n";
			$flux .= '		color : "'.$config_piecon['color'].'"';
			$config = true;
		}
		if(isset($config_piecon['background']) && $config_piecon['background'] != ''){
			$flux .= $config ? ",\n":'';
			$flux .= '		background : "'.$config_piecon['background'].'"';
			$config = true;
		}
		if(isset($config_piecon['shadow']) && $config_piecon['shadow'] != ''){
			$flux .= $config ? ",\n":'';
			$flux .= '		shadow : "'.$config_piecon['shadow'].'"';
			$config = true;
		}
		if(isset($config_piecon['fallback']) && $config_piecon['fallback'] != 'false'){
			$flux .= $config ? ",\n":'';
			$flux .= '		fallback : ';
			$flux .= ($config_piecon['fallback'] == 'force') ? '"force"' : $config_piecon['fallback'];
		}
$flux .= "\n	});
}
</script>\n";
	}
	return $flux;
}
?>