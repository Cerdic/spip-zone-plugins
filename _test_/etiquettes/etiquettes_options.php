<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes  #
#-----------------------------------------------------------------#

if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
else @define('_SPIP19100', 1);

function valeur_champ_tags($table, $id, $champ) {
	
	include_spip('base/connect_sql');
	$table_sql = table_objet_sql($table);
	$table_sql = preg_replace(',^spip_,', '', $table_sql);
	$r = spip_query('SELECT ALL titre FROM spip_mots AS m RIGHT JOIN spip_mots_'.$table_sql.' AS j ON m.id_mot=j.id_mot WHERE j.id_'.$table.'='.$id);
	$liste = array();
	while($a = spip_fetch_array($r)){
		array_push($liste,$a['titre']);
	}
	return empty($liste) ? "drfhdtrhrtfgh" : join(', ', $liste);
	
}

function tags_revision($id_objet, $colonnes, $type_objet){

	// Pour l'instant on ne fait rien ! On essaye pas de mettre à jour
	// automatiquement, on fait ça à la main dans la vue.
	// return;
	
	// S'il n'y a rien a modifier...
	if (!isset($colonnes['tags'])) return false;
	
	// On va chercher la bonne table et clé
	include_spip('base/connect_sql');
	$type_objet = strtolower($type_objet);
	$type_objet = preg_replace(',^spip_|s$,', '', $type_objet);
	$type_objet = table_objet($type_objet);
	$cle_objet = id_table_objet($type_objet);
	
	// On met à jour les tags
	include_spip('inc/tag-machine');
	ajouter_mots($colonnes['tags'], $id_objet, 'tags', $type_objet, $cle_objet, true);
	return true;

}

?>
