<?php

// *********
// Fonctions
// *********

// pour debug

function sitra_debug($nom, $vars){
	if (is_array($vars)){
		echo '/// ',$nom,'<br />';
		foreach($vars as $key => $val){
			if (is_array($val))
				sitra_debug($key,$val);
			else
				echo '[',$key.'] => ',$val,'<br />';
		}
	} else {
		echo '[',$nom,'] => ',$vars,'<br />';
	}
}

// affichage eventuel des erreurs 
// enregistrement dans sitra_log
// préparation envoi du message si défini
function message($message = '',$erreur=false){
	if (!$message)
		return;
	if (SITRA_DEBUG)	
		echo $message,'<br />';
	
	spip_log($message,'sitra');

	if ($erreur){
		$GLOBALS['sitra_config']['erreur'] = true;
		$GLOBALS['sitra_config']['mail_objet'] .= 'Erreur : '.$message."\n";
	}
	$GLOBALS['sitra_config']['mail_message'] .= $message."\n";
}


// si $ajout n'est pas vide on ajoute à $array
// $array passe par référence
function ajoute_si_present (&$array, $ajout=''){
	$ajout = trim($ajout);
	if (!$ajout)
		return;
	if (!in_array($ajout, $array))
		$array[]= $ajout;
}

function serialize_non_vide($array){
	if (count($array))
		return serialize($array);
	else
		return '';
}


// date au format JJ/MM/AAAA [HH:MM]
function date_norme($date){
	if (!$date) {return;}
	$date = trim($date);
	$j = substr($date,0,2);
	$m = substr($date,3,2);
	$a = substr($date,6,4);
	$h = trim(substr($date,11,2));
	$min = trim(substr($date,14,2));
	if (!$h){$h = '00';}
	if (!$min){$min = '00';}
	return $a.'-'.$m.'-'.$j.' '.$h.':'.$min.':00';
}

// trouver le fichier avec un prefixe donné
function trouver_fichier_prefixe($dir, $prefix){
	$nbre_car_prefix = strlen($prefix);
	$le_fichier = '';
	if ($handle = opendir($dir)) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != ".." && (substr($file,0,$nbre_car_prefix) == $prefix)) {
	            $le_fichier = $file;
	        }
	    }
	    closedir($handle);
	}
	return $le_fichier;
}

// supprime un doc d'un repertoire
function suppr_doc($url_doc = ''){
	if ($url_doc){
		if (unlink($url_doc)) {
			message('Suppression document :'.$url_doc);
		} else {
			message('Probleme suppression document :'.$url_doc, 'erreur');
		}
	}
}

?>