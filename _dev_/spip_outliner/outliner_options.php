<?php
/*
 * Spip-Outliner
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function autoriser_outline_administrer($faire, $type, $id, $qui, $opt) {
	return
		( 
		($qui['statut'] == '0minirezo')
		AND (!$qui['restreint'])
		AND $GLOBALS["options"]=="avancees" 
		);
}
function autoriser_outline_donnee_instituer($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	OR !isset($opt['nouveau_statut'])
	#OR in_array($opt['nouveau_statut'],array('prop','publie','refuse')) 
	#OR !in_array($opt['statut'],array('prepa'))
	) return false;
	return true;
}
function autoriser_outline_donnee_modifier($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	#OR ($opt['statut'] != 'prepa')
	)return false;
	return true;
}
?>