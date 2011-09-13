<?php

/**
teste si une copie locale existe pour les images principales
obtenues à partir de l'archive zip
**/
function url_image_locale($url='',$chemin=''){
	if (!$url) return;
	$path_parts = pathinfo($url);
	$fichier_image = strtolower($path_parts['basename']);
	$fichier_image = $chemin.SITRA_CHEMIN_IMAGES.$fichier_image;
	
	if (file_exists($fichier_image))
		return $fichier_image;
	else
		return;
}

/**
Pour les images distantes
teste si une copie locale existe sinon la force
retourne l'url de la copie locale
**/
function url_image_distante($url=''){
	if (!$url) return;
	$result = copie_locale($url,'test');
	if (!$result)
		$result = copie_locale($url,'force');
	return $result;
}

/**
copie locale ou distante en fonction du type image distante (O ou N)
$chemin a utiliser pour la partie privée
**/
function url_image_sitra($url='', $lien='', $chemin=''){
	switch ($lien){
		// image importée par zip sitra
		case 'N': return url_image_locale($url,$chemin); break;
		// image distante
		case 'O': return $chemin.url_image_distante($url); break;
		default : return; break;
	}
}

/***
remplace les , par des . pour lattitude, longitude
***/
function sitra_lat_lon($data){
	return str_replace(',', '.', $data);
}


/**
balise #SITRA_AUJOURDHUI
**/
function sitra_aujourdhui() {
	return date('Y-m-d 00:00:00');
}

function balise_SITRA_AUJOUDHUI_dist($p) {
	$p -> code = 'sitra_aujourdhui()';
	return $p;
}

// normalise les noms de categorie et de selection et autres
function normalise_nom($text){
	// passe en minuscules
	$text = mb_strtolower($text,'UTF-8');
	// supprime les accents
	$car1 = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ç', 'ñ' );
	$car2 = array('a', 'a', 'a', 'a', 'a', 'a','ae', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o','oe', 'u', 'u', 'u', 'u', 'c', 'n' );
	$text = str_replace($car1, $car2, $text);
	$text = preg_replace('`[^._=-\w\d]+`', '-',$text);
	return $text;
}

function sitra_norme_heure($hm = '00:00') {
	if ($hm == '00:00' )
		return;
	$h = substr($hm, 0, 2);
	$m = substr($hm, 3, 2);
	if ($m == '00')
		$m = '';
	if (substr($hm,0,1)== '0')
		$h = substr($hm,1,1);
	
	return $h.'h'.$m;
}

// tente de masquer les adresses emails des robots
function sitra_code_email($mail=''){
	if (!$mail)
		return;
	$mail = str_replace(array('.','@'), array(' (.) ',' (at) '), $mail);
	return $mail;
}


// affichage normalise des telephones
function sitra_norme_tel($num, $sep=' ') {
	$num = preg_replace('`[^\d]`', '', $num);
	if (!$num)
		return;
	$num = substr($num,0,2).$sep.
		substr($num,2,2).$sep.
		substr($num,4,2).$sep.
		substr($num,6,2).$sep.substr($num,8,2);
	return $num;
}

function sitra_norme_web ($web){
	if (!$web)
		return;
	if (substr($web,0,7) != 'http://' )
		$web = 'http://'.$web;
	return $web;
}

// transforme une liste séparée par des | en liste avec delimiteur personnalisé
function sitra_expand_liste($liste, $delim = ' - '){
	if (!$liste)
		return;
	$items = unserialize($liste);
	return implode($delim, $items);
}

function sitra_expand_telephone($liste, $delim = ' - '){
	if (!$liste)
		return;
	$items = unserialize($liste);
	foreach ($items as $key => $val){
		$val = sitra_norme_tel($val);
		$items[$key] = $val;
	}
	return implode($delim, $items);
}

// traitement des emails
function sitra_expand_email($liste, $delim = ' - ', $code=0){
	if (!$liste)
		return;
	$items = unserialize($liste);
	foreach ($items as $key => $val){
		if ($code)
			$adr = sitra_code_email($val);
		else
			$adr = $val;
		$val = '<a class="spip_mail" href="mailto:'.$val.'">'.$adr.'</a>';
		$items[$key] = $val;
	}
	return implode($delim, $items);
}

// les sites
function sitra_expand_web($liste, $delim = ' - '){
	if (!$liste)
		return;
	$items = unserialize($liste);
	foreach ($items as $key => $val){
		$val = '<a  class="spip_out" href="'.sitra_norme_web($val).'" rel="external">'.couper($val,40).'</a>';
		$items[$key] = $val;
	}
	return implode($delim, $items);
}

/**
pour les horaires
**/
function sitra_heure_manif($date){
	$h = 1*heures($date);
	$m = minutes($date);
	if ($m == '00')
		$m = '';
		
	return $h.'h'.$m;
}


/** 
afficher les dates correctement
**/
function sitra_date_debut_fin($date_debut = '0000-00-00 00:00:00', $date_fin = '0000-00-00 00:00:00', $format='court', $horaire = 'oui'){
	// verifier la date de debut
	if ($date_debut == '0000-00-00 00:00:00')
		return;
	$debut = affdate($date_debut,'Y-m-d');// aaaa-mm-jj
	$annee_debut = annee($date_debut);
	$mois_debut = mois($date_debut);
	$jour_debut = jour($date_debut);
	// fin
	$fin = affdate($date_fin,'Y-m-d'); // aaaa-mm-jj
	$annee_fin = annee($date_fin);
	$mois_fin = mois($date_fin);
	$jour_fin = jour($date_fin);

	$text_debut = $text_fin = '';
	// si fin pas renseigné debut = fin
	if ($fin == '0000-00-00')
		$fin = $debut;
	// même jour
	if ($debut == $fin) {
		if ($format == 'complet')
			$text_debut = _T('sitra:date_le').' '.affdate($date_debut);
		else
			$text_debut = _T('sitra:date_le').' '.affdate_jourcourt($date_debut);
	} else {
		if ($mois_debut == $mois_fin and $annee_debut == $annee_fin ) 
			$mois_debut = '';
		else
			$mois_debut = ' '.nom_mois($date_debut);
		if ($annee_debut == $annee_fin)
			$annee_debut = '';
		else
			$annee_debut = ' '.$annee_debut;
			
		$text_debut = _T('sitra:date_du').' '.jour($date_debut).$mois_debut.$annee_debut;
		if ($format == 'complet')
			$text_fin = _T('sitra:date_au').' '.affdate($date_fin);
		else
			$text_fin = _T('sitra:date_au').' '.affdate_jourcourt($date_fin);
	}
	
	$h_debut = $h_fin = '';
	// horaires
	if ($horaire == 'oui'){ 
		$h_debut = sitra_heure_manif($date_debut);
		$h_fin = sitra_heure_manif($date_fin);
		
		if ($h_debut != '0h'){
			if ($h_fin == '0h')
				$h_fin = $h_debut;
			if ($h_debut == $h_fin)
				$text_fin = _T('sitra:heure_debut_a').' '.$h_debut;
			else {
				$h_debut = _T('sitra:heure_debut_de').' '.$h_debut;
				$h_fin = _T('sitra:heure_fin_a').' '.$h_fin;
				$text_fin .= ' '.$h_debut.' '.$h_fin;
			}
		}
	}
	
	return $text_debut.' '.$text_fin;
}

/**
Pour traiter correctement les dates au format ical
**/
function sitra_date_ical($date_debut = '0000-00-00 00:00:00', $date_fin = '0000-00-00 00:00:00'){
	if ($date_debut == '0000-00-00 00:00:00')
		return;
	$debut = affdate($date_debut,'Y-m-d');// aaaa-mm-jj
	$h_debut = affdate($date_debut,'H:i:s');
	// fin
	$fin = affdate($date_fin,'Y-m-d'); // aaaa-mm-jj
	$h_fin = affdate($date_fin,'H:i:s');


	if ($fin == '0000-00-00')
		$fin = $debut;
		
	if ($h_fin == '00:00:00')
		$h_fin = $h_debut;
		
	if ($h_debut == '00:00:00'){
		// jour entier
		$annee = annee($date_fin);
		$mois = mois($date_fin);
		$jour = jour($date_fin);
		$lendemain = mktime(0, 0, 0, $mois  , $jour+1, $annee);
		$result = 'DTSTART:'.affdate($date_debut,'Ymd')."\n";
		$result .= 'DTEND:'.date('Ymd',$lendemain)."\n";
	} else {
		$result = 'DTSTART:'.date_ical($date_debut)."\n";
		$result .= 'DTEND:'.date_ical($fin.' '.$h_fin)."\n";
	}
	return $result;
}


function date_UTC($date = ''){
	if (!$date) return;
	$s = affdate($date,'U');
	$decal = date('Z');
	$date = $s - $decal;
	return date('Ymd\THis\Z',$date);
}


function sitra_date_UTC($date_debut = '0000-00-00 00:00:00', $date_fin = '0000-00-00 00:00:00'){
	if ($date_debut == '0000-00-00 00:00:00')
		return;
	$debut = affdate($date_debut,'Y-m-d');// aaaa-mm-jj
	$h_debut = affdate($date_debut,'H:i:s');
	// fin
	$fin = affdate($date_fin,'Y-m-d'); // aaaa-mm-jj
	$h_fin = affdate($date_fin,'H:i:s');

	if ($fin == '0000-00-00')
		$fin = $debut;
		
	if ($h_fin == '00:00:00')
		$h_fin = $h_debut;
	
	if ($h_debut == '00:00:00'){
		// jour entier
		$annee = annee($date_fin);
		$mois = mois($date_fin);
		$jour = jour($date_fin);
		$lendemain = mktime(0, 0, 0, $mois  , $jour+1, $annee);
		$date_fin = date('Ymd',$lendemain);
		$result = affdate($date_debut,'Ymd').'/'.$date_fin;
	} else {
		$result = date_UTC($date_debut).'/'.date_UTC($fin.' '.$h_fin);
	}
	return $result;
}
/**
détermine une date dans le passé ou l'avenir à partir d'une autre
$date doit etre au format YYYY-MM-DD HH:MM:SS
par défaut on ajoute des jours
possibilité d'avoir une date dans le passé avec une valeur négative
**/
function sitra_date_passe_avenir($date, $ecart=0, $type='d'){

	$y = substr($date, 0, 4);
	$m = substr($date, 5, 2);
	$d = substr($date, 8, 2);
	$h = substr($date, 11, 2);
	$min = substr($date, 14, 2);
	$s = substr($date, 17, 2);
	if (!checkdate ($m, $d, $y )){ return;}
	switch ($type) {
		case 'y': $date = mktime($h, $min, $s, $m, $d, $y+$ecart); break;
		case 'm': $date = mktime($h, $min, $s, $m+$ecart, $d, $y); break;
		case 'h': $date = mktime($h+$ecart, $min, $s, $m, $d, $y); break;
   		case 'm': $date = mktime($h, $min+$ecart, $s, $m, $d, $y); break;
   		default: $date = mktime($h, $min, $s, $m, $d+$ecart, $y); break;
   }
   $date = date('Y-m-d H:i:s',$date);
   return $date;
}


?>