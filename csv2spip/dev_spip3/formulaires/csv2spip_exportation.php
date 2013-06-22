<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2spip_exportation_charger_dist(){
    $toto=array(
        "toto"=>"toto",
        "tata"=>"tata"
    );
    $nom_champ= array();
    $desc = sql_showtable('spip_auteurs',true);
    foreach ($desc[field] as $cle => $valeur){
        //$nom_champ[$i]=$cle;
        $nom_champ[]=$cle;
    }
    //var_dump($desc[field]);
    //var_dump($nom_champ);


    $valeurs = array(
        "delimiteur"  => "point-virgule",
        "toto" => $nom_champ,
//        "delimiteur"  => "",
    );
return $valeurs;
}

function formulaires_csv2spip_exportation_verifier_dist(){
    $delimiteur= _request('delimiteur');
        
    $erreurs = array();
    //champs obligatoire
    $champs_obligatoire= _T('csv2spip:obligatoire');
    if (!$delimiteur) $erreurs['delimiteur'] = "$champs_obligatoire";

    return $erreurs;
}

function formulaires_csv2spip_exportation_traiter_dist(){
    $delimiteur   = _request('delimiteur');
    $choix_delimiteur = array(
        "point-virgule"=>';',
        "virgule"=>",",
    ); 
    $delimiteur=$choix_delimiteur[$delimiteur];
    $retour = array();

    // creation du fichier csv pour l'importation
    $date_du_jour=date(d_m_Y);
    $nom_fichier_csv = "export_table_auteurs_$date_du_jour.csv";
    $fichier_csv = _DIR_SESSIONS.basename($nom_fichier_csv);
    $fp = fopen($fichier_csv, 'w');
    //$delimiteur=";";
    $statut = array(
        "0minirezo" => "administrateur",
        "1comite"   => "redacteur",
        "6forum"    => "visiteur",
    
    );
    // récupération des données dans la tables spip_auteurs que l'on place dans le champ "ss_groupe"
    $i=0;
    if ($res = sql_select('id_auteur,nom,login,bio,email,statut,webmestre', 'spip_auteurs')){
        while ($row = sql_fetch($res)){
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
                        $tableau_csv[$i]["ss_groupe"]=implode('||',$input[$row[nom]]);
                    }
					else 
						$tableau_csv[$i]["ss_groupe"]= "";
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
    $charset = 'utf-8';
    //Header("Content-Type: application/octet-stream; charset=$charset");
    //Header("Content-Type: text/comma-separated-values");
    //header("Content-type: application/vnd.ms-excel"); 
//    header('Content-Type: text/csv');
    header("Content-Type: application/download");
    header("Content-Disposition: attachment; filename=$nom_fichier_csv");
    header("Content-Length: ".strlen($a_ecrire));
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
//    readfile($fichier_csv);
/*echo '<pre>';
var_dump($a_ecrire);return $retour;
echo '</pre>';
*/
	echo $a_ecrire;

//    $retour['message_ok'] = "bravo !!";
    return $retour;
}
?>


