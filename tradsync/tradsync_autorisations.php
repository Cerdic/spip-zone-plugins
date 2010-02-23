<?php
/**
 * Plugin tradsync
 * Licence GPL (c) 2010 Matthieu Marcillaud
 * 
 */

function tradsync_autoriser(){}

// autoriser('synchronisermots')
// autoriser('synchronisermots', 'article')
// autoriser('synchronisermots', 'article', $id_article)
function autoriser_synchronisermots_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	if ($qui['statut'] == '0minirezo') {
		return true;
	}
	// si type et id...
	if ($type and $id) {
		$type = objet_type($type);
		return autoriser('modifier', $type, $id, $qui, $opt);
	}
	return false;
}


?>
