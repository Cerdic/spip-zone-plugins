<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_autosave(){

$texte = _request('texte');
$id_article = _request('id_article');

spip_query("update spip_articles set texte='$texte' where id_article='$id_article'");	
	echo "enregistrement a ".date('h:i:s');	
 
}

?>