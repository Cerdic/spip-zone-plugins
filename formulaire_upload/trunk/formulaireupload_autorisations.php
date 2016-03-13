<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function formulaireupload_autoriser(){}

function autoriser_joindredocumentupload_dist($faire, $type, $id, $qui, $opt){
	// par defaut, on reprend droits lies au plugin medias: admin et redacteur
	return  autoriser_joindredocument_dist($faire, $type, $id, $qui, $opt);

	// on peut modifier ici les autorisations 
	// si on veut etre plus laxiste: autoriser par ex. visiteurs, ... verifier la securite toutefois !
	// return true;

}


?>