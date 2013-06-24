<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_csv2spip_exportation_charger_dist(){

    $nom_champs=csv2spip_exportation();
    $valeurs = array(
        "choix_statut"=>"6forum",
        "nom_champs" => $nom_champs,
    );
    //var_dump($nom_champs);
    
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
    
    // creation du nom du fichier
    $date_du_jour=date(d_m_Y);
    $nom_fichier_csv = "export_table_auteurs_$date_du_jour.csv";

    $statut = array(
        "0minirezo" => "administrateur",
        "1comite"   => "redacteur",
        "6forum"    => "visiteur",
    );
    // récupération des données dans la tables spip_auteurs que l'on place dans le champ "ss_groupe"
    // Ecriture de la premiere ligne d'entete
    foreach ($nom_champs as $entete){
        $tableau_csv[0][$entete]=$entete;
    }
    // ajout de l'admin restreint
    $tableau_csv[0]["ss_groupe"]="ss_groupe";
    // ajout de l'acces restreint s'il existe
    if (test_plugin_actif("accesrestreint")){
    $tableau_csv[0]["zone"]="zone";
    }
    $i=1;
    
    if ($res = sql_select('*', 'spip_auteurs')){
        while ($row = sql_fetch($res)){
            // test les statuts demandés
            if (in_array($row[statut],$choix_statut)){
                // Prise en compte de tous les champs selectionnés
                foreach($nom_champs as $nom_champ){
                    // Prise en compte du champ statut
                    if ($nom_champ == "statut"){
                        $tableau_csv[$i]["statut"] = $statut[$row[statut]];
                    }else {
                        $tableau_csv[$i][$nom_champ]=$row[$nom_champ];
                    }
                };
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
                // Prise en compte des zones restreintes : plugin acces restreint si le plugin est installe
                // comme je ne sais pas faire une double jointure, je réalise 2 requetes
                // requete 1 => récupération de "id_zone"
                if (test_plugin_actif("accesrestreint")){
                    if ($res3 = sql_select(
                        array(
                            "lien.id_zone AS id_zone"),
                        array(
                            "spip_zones_liens AS lien"),
                        array(
                            "lien.objet = 'auteur'",
                            "lien.id_objet = $row[id_auteur]",
                        )
                    )) {
                        $k=0;
                        while ($row3 = sql_fetch($res3)){
                            // requete 2 => grace à id_zone, récupération des noms de rubriques
                            if ($res4 = sql_select(
                                array(
                                    "rub.titre AS titre"),
                                array(
                                    "spip_rubriques AS rub",
                                    "spip_zones_liens AS lien"),
                                array(
                                    "lien.id_zone = $row3[id_zone]",
                                    "lien.objet = 'rubrique'",
                                    "lien.id_objet = id_rubrique")
                                )) {
                                    while ($row4 = sql_fetch($res4)){
                                        $zones[$row[nom]][$k]=$row4[titre];
                                        $tableau_csv[$i]["zone"]=implode('|',$zones[$row[nom]]);
                                        $k++;
                                    }
                                }
                        }
                    }
                }

            }
            $i++;
        }
    }
    // création de la variable contenant l'intégralité des donnees
	$a_ecrire = '';
	foreach ($tableau_csv as $ligne) {
		$a_ecrire .= implode('~', $ligne);
		$a_ecrire .= "\r\n";
	}
	//var_dump($tableau_csv);

    // telechargement du fichier csv	
    header("Content-Type: application/download");
    header("Content-Disposition: attachment; filename=$nom_fichier_csv");
    header("Content-Length: ".strlen($a_ecrire));
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	echo $a_ecrire; 
    exit;

    return $retour;
}



function csv2spip_exportation(){

    //récupération des noms des champs
    $nom_champs= array();
    $champ_supprimer = array(0,8,15,16,17,18,19);
    $desc = sql_showtable('spip_auteurs',true);
    foreach ($desc[field] as $cle => $valeur) $nom_champs[$cle]="-> $cle";
    foreach ($champ_supprimer as $cle){
        unset($nom_champs[$cle]);
    }
    return $nom_champs;
}





?>
