<?php

class cls_objet { 
	var $id;		//valeur de $id_nom
    var $id_nom;	//nom de la clef
	var $nature;	//nature de l'objet
	var $id_parent; //identifiant du parent
	var $table;		//nom de la table contenant l'objet

	//constructeur //
	//l'objet spip est initialisé selon les paramétres donnés 	
	function cls_objet($objet_nature, $id_objet) {
		//sauvegarde la nature de l'objet
		$this->nature = $objet_nature;
		//sauvegarde son identifiant
		$this->id = $id_objet;
		//initialise l'objet en fonction de sa nature
        switch($this->nature) {
            case rubrique:
                $this->table = "spip_rubriques";
                $this->id_nom = "id_rubrique";
                break;
            case article:
                $this->table = "spip_articles";
                $this->id_nom = "id_article";
                break;
            default :
                return 0;
        }	
	}
	
	function toggle_statut_archive() {
		//on determine si l'objet est archivé ou non
		$sql = "SELECT archive FROM ".$this->table." WHERE ".$this->id_nom."=".$this->id.";";
		$n = spip_query($sql);
		if (!$n) die($sql."<br/>Ne sais pas si l'objet d'id : ".$this->id." et de nature : ".$this->nature." est archivé ou non");
		
		$array_archive = sql_fetch($n);
		$archive = $array_archive['archive'];
	
		switch ($archive) {
			case NULL:
				$archiver = 1;
				$date = "Now()";
				break;
			case 1 :
				$archiver = 'NULL';
				$date = 'NULL';
				break;
			default :				
		}
		
		//met à jour l'objet
		$sql = "UPDATE ".$this->table." SET archive = ".$archiver.",archive_date = ".$date." WHERE ".$this->id_nom."=".$this->id;
		$n = spip_query($sql);
		if (!$n) die($sql."Changement de statut archive impossible, objet : ".$this->table.",".$this->id);
		
		//vide le cache
		//issu de ecrire/action/purger.php ('case cache')
		include_spip('inc/invalideur');
		supprime_invalideurs();
		spip_unlink(_CACHE_RUBRIQUES);
		purger_repertoire(_DIR_CACHE);
	}
}

if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');   // for spip presentation functions
	include_spip('base/abstract_sql'); 	// for sql request
	include_spip('inc/utils');          // for _request function
	include_spip('inc/plugin');         // xml function
	include_spip('inc/distant');         //
	include_spip('inc/flock');         //
	
	include_spip('base/compat193');		//créé à la volé les fonctions sql pour 192

	//initialise l'objet spip à archiver	
	$objet = new cls_objet(_request('objet_nature'), _request('id_objet'));
	
	//change le statut archive de l'objet
	$objet->toggle_statut_archive();
		
	//met Ó jour les index de la table articles
	include_spip('inc/indexation');
	//en 193 l'indexation est sortie du core, du coup on teste l'existance de l'indexation avant d'indexer
	if ($GLOBALS['meta']['activer_moteur'] == 'oui' && function_exists('marquer_indexer')) {
		marquer_indexer($objet->table, $objet->id_nom);
	}
	
	//vide le cache
	//issu de ecrire/action/purger.php ('case cache')
	include_spip('inc/invalideur');
	supprime_invalideurs();
	if (function_exists('spip_unlink')) {
		spip_unlink(_CACHE_RUBRIQUES);
	}
	purger_repertoire(_DIR_CACHE);

	//relance la page appelante
	//$url = generer_url_ecrire("articles", "id_article=$id_article", true, true);
	//$url = "http://".$_SERVER['HTTP_HOST']."/ecrire/".$url;

	header("Location: ".$_SERVER['HTTP_REFERER']);
	//echo recuperer_page($url);
?>
