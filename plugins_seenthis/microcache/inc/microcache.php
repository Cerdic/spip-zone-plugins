<?php

function _fichier_microcache($id, $fond) {
	$fond = str_replace("/", "-", $fond);
	$cle = "$id-microcache";
	$dossier_microcache = sous_repertoire(_NOM_TEMPORAIRES_ACCESSIBLES, "microcache");
	$dossier_microcache = sous_repertoire($dossier_microcache, $fond);
	
	$microcache = sous_repertoire($dossier_microcache, ($id%1000)).$cle;

	return $microcache;
}

function _supprimer_microcache($id, $fond) {
	//	echo "<li>$id - $fond</li>";
	$microcache = _fichier_microcache($id, $fond);
	@unlink($microcache);	
}

function _esi_microcache($id, $fond) {
	$microcache = _fichier_microcache($id, $fond);

	if ($calcul
	OR in_array($_GET['var_mode'], array('recalcul', 'debug'))
	OR !@file_exists($microcache)
	OR filemtime($microcache) < time() - 60*60*24*7) {
		$contenu = recuperer_fond($fond, array('id'=>$id));
		ecrire_fichier($microcache, $contenu);
	}
	
	return "<esi:include src=\"$microcache\"/>";
}

function _microcache($id, $fond, $calcul=false) {
	$microcache = _fichier_microcache($id, $fond);

	if ($calcul
	OR in_array($_GET['var_mode'], array('recalcul', 'debug'))
	OR !@file_exists($microcache)
	OR filemtime($microcache) < time() - 60*60*24*7) {
		$contenu = recuperer_fond($fond, array('id'=>$id));
		ecrire_fichier($microcache, $contenu);
	} else {
		lire_fichier($microcache, $contenu);
	}

	return $contenu;
}

?>