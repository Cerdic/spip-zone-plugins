<?php
/*
Plugin galuse
réalisation: Thom 2010
Sur la base du plugin de B. Blanzin
Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*/

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
global $tables_principales;
//global $tables_auxiliaires;

$spip_galuse = array(
    "id_image"      => "BIGINT(21) NOT NULL AUTO_INCREMENT",
    "id_vignette"   => "BIGINT(21) NOT NULL default '0'",
    "id_auteur"     => "BIGINT(21) NOT NULL",
    "extension"     => "VARCHAR(10)",
    "titre"         => "TEXT NOT NULL",
    "date"          => "DATETIME NOT NULL default 0000-00-00 00:00:00",
    "descriptif"    => "TEXT",
    "fichier"       => "VARCHAR(255) NOT NULL",
    "taille"        => "INT(11) default NULL",
    "largeur"       => "INT(11) default NULL",
    "hauteur"       => "INT(11) default NULL",
    "mode"          => "ENUM('vignette', 'image', 'document') NOT NULL default 'document'",
    "distant"       => "VARCHAR(3) default 'non'",
    "maj"           => "TIMESTAMP NOT NULL CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP",
    "statut"        => "VARCHAR(10) NOT NULL default '0'",
    "date_publication"  => "DATETIME NOT NULL default 0000-00-00 00:00:00",
    "brise"         => "TINYINT(4) default '0'",
    "credits"       => "VARCHAR(255) NOT NULL"
    );
$spip_galuse_key = array("PRIMARY KEY" => "id_image");

$spip_galuse_liens=array(
    "id_image"   => "BIGINT(21) NOT NULL default '0'",
    "id_objet"      => "BIGINT(21) NOT NULL default '0'",
    "objet"         => "VARCHAR(25) NOT NULL",
    "vu"            => "ENUM('non','oui') NOT NULL default 'non'");
$spip_galuse_liens_key = array("PRIMARY KEY" => "id_image");
					
	
	$tables_principales[$GLOBALS['table_prefix']."_galuse"] =
		array('field' => &$spip_galuse, 'key' => &$spip_galuse_key);
	$tables_principales[$GLOBALS['table_prefix']."_galuse_liens"] =
		array('field' => &$spip_galuse_liens, 'key' => &$spip_galuse__liens_key);
		
// Declarer dans la table des tables pour sauvegarde
global $table_des_tables;
$table_des_tables['galuse']  = $GLOBALS['table_prefix']."_galuse";
$table_des_tables['galuse_liens']  = $GLOBALS['table_prefix']."_galuse_liens";

//boucle
function boucle_SPIP_GALUSE_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  $GLOBALS['table_prefix']."_galuse";
// a modifier de façon à inclure l auteur et l article !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	        return calculer_boucle($id_boucle, $boucles); 
	}

//
// <BOUCLE(DOCUMENTS)>
//
// http://doc.spip.org/@boucle_DOCUMENTS_dist
function boucle_GALUSE_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
    $boucle->from[$id_table] =  $GLOBALS['table_prefix']."_galuse";

	// on ne veut pas des fichiers de taille nulle,
	// sauf s'ils sont distants (taille inconnue)
	array_unshift($boucle->where,array("'($id_table.taille > 0 OR $id_table.distant=\\'oui\\')'"));

	// Supprimer les vignettes
	if (!isset($boucle->modificateur['criteres']['mode'])
	AND !isset($boucle->modificateur['criteres']['tout'])) {
		array_unshift($boucle->where,array("'!='", "'$id_table.mode'", "'\\'vignette\\''"));
	}

	// Pour une boucle generique (DOCUMENTS) sans critere de lien, verifier
	// qu notre document est lie a un element publie
	// (le critere {tout} permet de les afficher tous quand meme)
	// S'il y a un critere de lien {id_article} par exemple, on zappe
	// ces complications (et tant pis si la boucle n'a pas prevu de
	// verification du statut de l'article)
	if ((!isset($boucle->modificateur['tout']) OR !$boucle->modificateur['tout'])
	AND (!isset($boucle->modificateur['criteres']['id_objet']) OR !$boucle->modificateur['criteres']['id_objet'])
	) {
		# Espace avant LEFT JOIN indispensable pour insertion de AS
		# a refaire plus proprement

		## la boucle par defaut ignore les documents de forum
		$boucle->from[$id_table] = $GLOBALS['table_prefix']."_galuse LEFT JOIN ".$GLOBALS['table_prefix']."_galuse_liens AS l
			ON $id_table.id_document=l.id_document
			LEFT JOIN spip_articles AS aa
				ON (l.id_objet=aa.id_article AND l.objet=\'article\')
			LEFT JOIN spip_breves AS bb
				ON (l.id_objet=bb.id_breve AND l.objet=\'breve\')
			LEFT JOIN spip_rubriques AS rr
				ON (l.id_objet=rr.id_rubrique AND l.objet=\'rubrique\')
			LEFT JOIN spip_forum AS ff
				ON (l.id_objet=ff.id_forum AND l.objet=\'forum\')
		";
		$boucle->group[] = "$id_table.id_document";

		if ($GLOBALS['var_preview']) {
			array_unshift($boucle->where,"'(aa.statut IN (\'publie\',\'prop\') OR bb.statut  IN (\'publie\',\'prop\') OR rr.statut IN (\'publie\',\'prive\') OR ff.statut IN (\'publie\',\'prop\'))'");
		} else {
			$postdates = ($GLOBALS['meta']['post_dates'] == 'non')
				? ' AND aa.date<=\'.sql_quote(quete_date_postdates()).\''
				: '';
			array_unshift($boucle->where,"'((aa.statut = \'publie\'$postdates) OR bb.statut = \'publie\' OR rr.statut = \'publie\' OR ff.statut=\'publie\')'");
		}
	}


	return calculer_boucle($id_boucle, $boucles);
}


?>