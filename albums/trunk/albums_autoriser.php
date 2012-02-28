<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/* Pour que le pipeline de rale pas ! */
function albums_autoriser(){}

function autoriser_ajouteralbum_dist($faire, $type, $id, $qui, $opt){
	return
		(autoriser('modifier', $type, $id, $qui, $opt)
			OR (
				$id<0
				AND abs($id) == $qui['id_auteur']
				AND autoriser('ecrire', $type, $id, $qui, $opt)
			)
		)
		AND
		(
			//$type=='article' OR in_array(table_objet_sql($type),explode(',',$GLOBALS['meta']['albums']['objets']))
			$type=='article' OR in_array(table_objet_sql($type),lire_config('albums/objets'))
		);
}

/**
 * Auto-association d'albums a du contenu editorial qui le reference
 * par defaut true pour tous les objets
 */
function autoriser_autoassocieralbum_dist($faire, $type, $id, $qui, $opts) {
	return true;
}


?>
