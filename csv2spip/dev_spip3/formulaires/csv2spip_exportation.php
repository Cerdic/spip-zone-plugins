<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_csv2spip_exportation_charger_dist(){

    $nom_champs=csv2spip_exportation();
    //var_dump($nom_champs);
    $valeurs = array(
        "choix_statut"=>"6forum",
        "nom_champs" => $nom_champs,
    );
return $valeurs;
}

function formulaires_csv2spip_exportation_verifier_dist(){
        
    $erreurs = array();
    //champs obligatoire
    //$champs_obligatoire= _T('csv2spip:obligatoire');

    return $erreurs;
}

function formulaires_csv2spip_exportation_traiter_dist(){
    $nom_champs   = _request('nom_champs');
    $choix_statut = _request('choix_statut');
    $retour = array();

    // creation du fichier csv pour l'exportation
    $date_du_jour=date(d_m_Y);
    $nom_fichier_csv = "export_table_auteurs_$date_du_jour.csv";
    $fichier_csv = _DIR_SESSIONS.basename($nom_fichier_csv);
    $fp = fopen($fichier_csv, 'w');
    $statut = array(
        "0minirezo" => "administrateur",
        "1comite"   => "redacteur",
        "6forum"    => "visiteur",
    
    );
    // récupération des données dans la tables spip_auteurs que l'on place dans le champ "ss_groupe"
    $i=0;
    if ($res = sql_select('id_auteur,nom,login,bio,email,statut,webmestre', 'spip_auteurs')){
        while ($row = sql_fetch($res)){
            if (in_array($row[statut],$choix_statut)){
                $tableau_csv[$i]["nom"]       = $row[nom];
                $tableau_csv[$i]["bio"]       = $row[bio];
                $tableau_csv[$i]["email"]     = $row[email];
                $tableau_csv[$i]["login"]     = $row[login];
                $tableau_csv[$i]["statut"]    = $statut[$row[statut]];
                $tableau_csv[$i]["webmestre"] = $row[webmestre];
                // on selectionne les noms des rubriques pour les admins restreints
                if($res2 = sql_select(
                    array(
                        "rub.titre AS titre"),
                    array(
                        "spip_rubriques AS rub",
                        "spip_auteurs_liens AS lien"),
                    array(
                        "rub.id_rubrique = lien.id_objet",
                        "lien.id_auteur  = $row[id_auteur]",
                        "lien.objet      = 'rubrique'")
                    )) {

                        $j=0;
                        while ($row2 = sql_fetch($res2)) {
                            $input[$row[nom]][$j]=$row2[titre];
                            $j++;
                        }
                        if ($input[$row[nom]]){
                            $tableau_csv[$i]["ss_groupe"]=implode('|',$input[$row[nom]]);
                        }
					    else 
						    $tableau_csv[$i]["ss_groupe"]= "";
                    }
            }
            $i++;
        }
    }
	$a_ecrire = '';
	foreach ($tableau_csv as $ligne) {
		$a_ecrire .= implode('~', $ligne);
		$a_ecrire .= "\r\n";
	}
	
    // creation du fichier csv
 /*   foreach ($tableau_csv as $fields) {
        fputcsv($fp, $fields, $delimiteur);
    }
    fclose($fp);
    //$retour['editable'] = true;
*/
    // lancement du telechargement
    //$charset = 'utf-8';
    //Header("Content-Type: application/octet-stream; charset=$charset");
    //Header("Content-Type: text/comma-separated-values");
    //header("Content-type: application/vnd.ms-excel"); 
//    header('Content-Type: text/csv');



    //header("Content-Type: application/download");
    //header("Content-Disposition: attachment; filename=$nom_fichier_csv");
    //header("Content-Length: ".strlen($a_ecrire));
	//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	//header('Pragma: public');
    //
    //
    //
//    readfile($fichier_csv);
/*echo '<pre>';
var_dump($a_ecrire);return $retour;
echo '</pre>';
*/
	echo $a_ecrire;

//    $retour['message_ok'] = "bravo !!";
    return $retour;
}



function csv2spip_exportation(){

    //récupération des noms des champs
    $nom_champs= array();
    $champ_supprimer = array(0,8,15,16,17,18,19);
    $desc = sql_showtable('spip_auteurs',true);
    foreach ($desc[field] as $cle => $valeur) $nom_champs[]=$cle;
    foreach ($champ_supprimer as $cle){
        unset($nom_champs[$cle]);
    }
    return $nom_champs;
}





?>


