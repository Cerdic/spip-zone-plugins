<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function urls_propres($i, $entite, $args='', $ancre=''){
	include_spip("urls/propres");
	if ($entite=='' and is_numeric($i)){
		$url = array(
		   array('id_article'=>$i),
		   'article',
		   null,
		   null
		   );
		}
	else{
		$url = urls_propres_dist($i, $entite, $args='', $ancre='');
		// Supprimer les tirets des urls purement numériques (SPIP 3.1 et >)
		if ($entite=="article"){//pas de - autour des numéros d'article
			$url = str_replace("-","",$url);
			}
		// pour que urls_decoder_url fonctionne correctement
	}
	return $url;
	}



