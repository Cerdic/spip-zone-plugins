<?php
/**
 * Fonction de conversion directe de document
 * 
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'encodage direct
 * 
 * Si on a fsockopen
 * On essaie de relancer un encodage directement si aucun encodage en cours
 * On force l'execution comme étant publique
 * 
 * @return void
 */
function inc_facd_convertir_direct_dist(){
	if(function_exists('fsockopen')){
		$nb_conversions = sql_countsel('spip_facd_conversions', "statut='en_cours'");
		if($nb_conversions == 0){
			$url = generer_url_action('facd_traiter_conversion','','',true);
			$parts=parse_url($url);
			$fp = fsockopen($parts['host'],
				isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);
			if ($fp) {
				$out = "GET ".$parts['path']."?".$parts['query']." HTTP/1.1\r\n";
				$out.= "Host: ".$parts['host']."\r\n";
				$out.= "Connection: Close\r\n\r\n";	
				fwrite($fp, $out);
				fclose($fp);
			}else
				spip_log('fsockopen ne semble pas fonctionner','facd');
		}else
			spip_log('Une conversion est déjà en cours','facd');
	}else
		spip_log('fsockopen ne semble pas disponible','facd');
	return;
}

?>