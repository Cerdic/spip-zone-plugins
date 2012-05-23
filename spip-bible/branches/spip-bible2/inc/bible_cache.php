<?php
function bible_lire_cache($param){

	include_spip('inc/flock');
	ksort($param);
	lire_fichier(_DIR_TMP.'bible_cache/'.md5(serialize($param)).'.txt',$fichier);
	return unserialize($fichier);
}
function bible_ecrire_cache($param,$tableau){
	include_spip('inc/flock');
	sous_repertoire(_DIR_TMP,'bible_cache');
	ksort($param);
	ecrire_fichier(_DIR_TMP.'bible_cache/'.md5(serialize($param)).'.txt',serialize($tableau));
}
?>