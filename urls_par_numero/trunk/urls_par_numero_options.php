<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function urls_propres($i, $entite, $args='', $ancre=''){
	//À partir de SPIP 3.1 on est obligé de surchargé, car les urls purement numériques sont encadrés par des guillemets
	include_spip("urls/propres");
	$url = urls_propres_dist($i, $entite, $args='', $ancre='');
	if ($entite=="article"){//pas de - autour des numéros d'article
		$url = str_replace("-","",$url);
		}
	return $url;
	}



