<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function zippeur_declarer_tables_principales($table){
	$table['spip_zippeur'] = array(
		'field'=>array(
			'id_zip'		=> "INT",
			'nom'			=> "text",
			'date_modif'	=> "datetime",
			'date_zip'		=> "datetime",
			'delai_suppression'=>"INT",
			'fichiers'=>"INT"
			),
			
		'key'=> array('PRIMARY KEY'=>'id_zip')
		
		);	
	return $table;
}

function zippeur_taches_generales_cron($taches){
	$taches['zippeur_effacer_zip'] = _ZIPPEUR_EFFACER_ZIP;	
	return $taches;
}
function zippeur_pre_liens($txt){
    $match = array();
    $regexp = "#\[(.*)->(zip_doc_article|zip_doc_album)(\d*)\]#";
    preg_match_all($regexp,$txt,$match,PREG_SET_ORDER);
    foreach ($match as $lien){
        // construires les ≠ paramètres
        $objet      = str_replace('zip_doc_','',$lien[2]);
        $id_objet   = $lien[3];
        $texte      = $lien[1]!=''?$lien[1] : generer_info_entite($id_objet,$objet,'titre',true) .' - '. _T('zippeur:ensemble_fichier');
        $nom_zip    = $objet."_".$id_objet;
       
        // constuire la liste des fichiers
        $fichiers   = array();
        $sql        = sql_select('maj,fichier','spip_documents INNER JOIN spip_documents_liens as L1',"spip_documents.statut='publie' AND L1.id_objet='$id_objet' AND L1.objet='$objet'",'','spip_documents.maj DESC');
        $first = True;
        while ($r = sql_fetch($sql)) {
            if ($first == True){
                $maj = $r['maj'];
                $first=False;}
            $fichiers[] = copie_locale(get_spip_doc($r['fichier']));
        }
        // construire le zip
        $url_zip    = zippeur($fichiers,$maj,lire_config('zippeur/zippeur_cmd'),$nom_zip);

        // constuitre le lien
        $replace    = "<a href='$url_zip' type='application/zip' class='spip_in zippeur' title='$texte (". taille_en_octets(filesize($url_zip)).")'>$texte</a>";
        $txt      = str_replace($lien[0],$replace,$txt);
        
    
    }
    return $txt;
}
?>