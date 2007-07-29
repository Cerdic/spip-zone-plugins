<?php
function CompteurGraphique_install($action){
	$CompteurGraphiqueTable = 'ext_compteurgraphique';
	switch ($action){
	
	case 'test':
	$CG_verif = spip_query('SELECT id_compteur FROM '.$CompteurGraphiqueTable.' WHERE statut = 10');
	$CG_ver_tab = spip_fetch_array($CG_verif);
	$CG_id_compteur = $CG_ver_tab['id_compteur'];
	if (!isset($CG_id_compteur)) {return false;}
	else {return true;}
	break;

	case 'install':
	$CG_verif = spip_query('SELECT id_compteur FROM '.$CompteurGraphiqueTable.' WHERE statut = 10');
	$CG_ver_tab = spip_fetch_array($CG_verif);
	$CG_id_compteur = $CG_ver_tab['id_compteur'];
	if (!isset($CG_id_compteur)) {
	$createTableQuery = 'CREATE TABLE IF NOT EXISTS '.$CompteurGraphiqueTable.'
	(id_compteur INTEGER NOT NULL AUTO_INCREMENT,
	decompte INTEGER DEFAULT NULL,
	id_article INTEGER DEFAULT NULL,
	id_rubrique INTEGER DEFAULT NULL,
	statut INTEGER DEFAULT NULL,
	longueur INTEGER DEFAULT NULL,
	habillage INTEGER DEFAULT NULL,
	PRIMARY KEY (id_compteur)
	)';
	spip_query($createTableQuery);
	spip_query("INSERT INTO ".$CompteurGraphiqueTable." VALUES (NULL,0,NULL,NULL,10,NULL,NULL)");
	}
	break;
       
	case 'uninstall':
	spip_query('DROP TABLE ext_compteurgraphique');
	break;
	}
}
?>