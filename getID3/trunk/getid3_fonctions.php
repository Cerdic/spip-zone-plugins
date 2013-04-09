<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 * Les fonctions du plugin
 * 
 * @package SPIP\GetID3\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Converti une durée en secondes en une durée affichable et lisible hh:mm:ss ou mm:ss
 * 
 * @param int|float $temps_secondes 
 * 		le nombre de secondes
 * @param string $format
 * 		Le format de retour, par défaut false, peut prendre comme valeur iso8601
 */
function getid3_duree($temps_secondes,$format=false){
	if(is_numeric($temps_secondes)){
		$diff_hours = floor($temps_secondes/3600);
		$temps_secondes -= $diff_hours * 3600;
		$diff_hours = (($diff_hours >= 0) && ($diff_hours < 10)) ? '0'.$diff_hours : $diff_hours;
	
		$diff_minutes = floor($temps_secondes/60);
		$temps_secondes -= $diff_minutes * 60;
		$diff_minutes = (($diff_minutes >= 0) && ($diff_minutes < 10)) ? '0'.$diff_minutes : $diff_minutes;
		
		$temps_secondes = (($temps_secondes >= 0) && ($temps_secondes < 10)) ? '0'.floor($temps_secondes) : floor($temps_secondes);
	
		if($format == 'iso8601')
			return 'PT'.(($diff_hours > 0) ? $diff_hours.'H':'').(($diff_minutes > 0) ? $diff_minutes:'00').'M'.$temps_secondes.'S';
		return $str = (($diff_hours > 0) ? $diff_hours.':':'').(($diff_minutes > 0) ? $diff_minutes:'00').':'.$temps_secondes;;
	}
	return false;
}
?>