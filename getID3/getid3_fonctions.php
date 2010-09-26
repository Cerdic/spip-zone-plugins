<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

function recuperer_id3_doc($id_document,$info = "", $mime = "",$retour='oui'){
	include_spip('inc/documents');

	$recuperer_id3 = charger_fonction('getid3_recuperer_infos','inc');
	$id3_content = $recuperer_id3($id_document);

	if($retour == 'oui'){
		$output = '';
		foreach($id3_content as $cle => $val){
			if(preg_match('/cover/',$cle)){
				$output .= ($val) ? '<img src='.$val.' /><br />' : '';
			}else{
				$output .= ($val) ? _T('getid3:info_'.$cle).' : '.$val.'<br />' : '';
			}
		}
	}else{
		$output = $id3_content;
	}
	return $output;
}

/**
 * Converti une durée en secondes en une durée affichable et lisible hh:mm:ss ou mm:ss
 * @param int/float $temps_secondes le nombre de secondes
 */
function getid3_duree($temps_secondes){
	$diff_hours    = floor($temps_secondes/3600);
	$temps_secondes -= $diff_hours   * 3600;
	$diff_hours = (($diff_hours >= 0) && ($diff_hours < 10)) ? '0'.$diff_hours : $diff_hours;

	$diff_minutes  = floor($temps_secondes/60);
	$temps_secondes -= $diff_minutes * 60;
	$diff_minutes = (($diff_minutes >= 0) && ($diff_minutes < 10)) ? '0'.$diff_minutes : $diff_minutes;

		$temps_secondes = (($temps_secondes >= 0) && ($temps_secondes < 10)) ? '0'.floor($temps_secondes) : floor($temps_secondes);

	$str = (($diff_hours > 0) ? $diff_hours.':':'').(($diff_minutes > 0) ? $diff_minutes:'00').':'.$temps_secondes;

	return $str;
}

function getid3_duree_iso8601($temps_secondes){
	$diff_hours    = floor($temps_secondes/3600);
	$temps_secondes -= $diff_hours   * 3600;
	$diff_hours = (($diff_hours >= 0) && ($diff_hours < 10)) ? '0'.$diff_hours : $diff_hours;

	$diff_minutes  = floor($temps_secondes/60);
	$temps_secondes -= $diff_minutes * 60;
	$diff_minutes = (($diff_minutes >= 0) && ($diff_minutes < 10)) ? '0'.$diff_minutes : $diff_minutes;

		$temps_secondes = (($temps_secondes >= 0) && ($temps_secondes < 10)) ? '0'.floor($temps_secondes) : floor($temps_secondes);

	$str = 'PT'.(($diff_hours > 0) ? $diff_hours.'H':'').(($diff_minutes > 0) ? $diff_minutes:'00').'M'.$temps_secondes.'S';

	return $str;
}
?>