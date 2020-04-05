<?php 

function balise_ID_SECTEUR_COURANT_dist($p) {
    $p->code = "sinon(intval(sql_getfetsel('id_secteur','spip_rubriques','host = \"http'.(\$_SERVER['HTTPS']?'s':null).'://'.\$_SERVER['HTTP_HOST'].'/\"',null,null,1)),0)";  
	return $p;
}

?>