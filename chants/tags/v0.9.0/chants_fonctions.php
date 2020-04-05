<?php
function chants_dir($objet, $id_objet) {

	if ($p = sous_repertoire(_DIR_VAR, 'chants')
	AND $p = sous_repertoire($p,$objet))  # on pourrait organiser par rubrique
	return $p;

}
function chants_creer_xml($squel,$nom,$options=array()){

	$contenu = recuperer_fond($squel,$options);
	ecrire_fichier(_DIR_CHANTS.$nom,$contenu);
	return _DIR_CHANTS.$nom;

}

?>