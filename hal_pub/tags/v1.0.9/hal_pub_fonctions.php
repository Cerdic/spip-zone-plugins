<?php
/**
 * Fonctions utiles au plugin Publications HAL
 *
 * @plugin     Publications HAL
 * @copyright  2016
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Hal_pub\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// trier un tableau et le retourner
//
function hal_pub_spip_sort($array,$tri="asort") {
  if (is_array($array)) {
	if ($tri=="asort")
				asort($array);
	if ($tri=="rsort")
				rsort($array);
	if ($tri=="krsort")
				krsort($array);
	if ($tri=="ksort")
				ksort($array);

	foreach($array as $k => $v) {
	  $array_order[$k] = $v;
	}
	return $array_order;

  }
  return $array;
}


//  tableau HAL sont le tableau  valeur1 (string),compteur1 (int),valeur2,compteur
//  les transformes en tableau [valeur1,valeur2] ou [valeur1]= valeur 1   (compteur1)
//
function hal_pub_traite_tableau($array) {
	$array_output = array();
	  if (is_array($array)) {
			$i = 1;
			$last_key = "";
			   foreach($array as $k => $v) {
				   if (is_string($v)) {
							 if (_T("hal_pub:type_pub_".$v,'',array('force' => false)))
									 $array_output["item".$v]  = _T("hal_pub:type_pub_".$v);  // traduction du type  fourni par le ficher de langue de SPIP
							   else
									$array_output["item".$v] = "$v";
							 $last_key = "item".$v;
				   } else if (is_int($v)) {
							 if ($v>0) {
								$array_output[$last_key] .=  " <span>($v)</span>";
							 } else {
								unset($array_output[$last_key]);  // retirer les valeurs vides
							 }
				   }
				   $i++;
			}

	}

	// var_dump($array);
	// var_dump($array_output);

	return $array_output;
}

//  enleve les prefixes d'un tableau spip [itemXXX]= ... -> [XXX]=
function hal_supprime_prefixe($array,$prefixe="item") {
	$array_output = array();
	$prefixe_length = strlen($prefixe);
	if (is_array($array)) {
		foreach($array as $k => $v) {
			$array_output[substr($k,$prefixe_length)] = $v;
		}
	   }
	   return $array_output;
}

// supprimer les valeurs invalides d'un array d'annee (année < 1900)
function hal_nettoie_annee_invalide($array) {
   $array_output = array();
   foreach($array as $k => $v) {
		if ($k>1900)
				$array_output[$k] = $v;
   }
   return $array_output;
}

// extrait d'un champs XML label_xml la valeur de publication brute
function hal_extraire_pub($xml) {
	$pattern = '/<idno type="halRef">(.*?)<\/idno>/';
	preg_match($pattern, $xml, $matches);
	if (isset($matches[1])) {
		 $match = $matches[1];
		 return  str_replace("&amp;", "&", $match);
	}
	return;
}

