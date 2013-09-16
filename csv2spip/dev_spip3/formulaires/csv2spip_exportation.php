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
    return $erreurs;
}

function formulaires_csv2spip_exportation_traiter_dist(){
    $nom_champs   = _request('nom_champs');
    $choix_statut = _request('choix_statut');
    $retour = array();
    
    // creation du nom du fichier
    $date_du_jour=date(Y_m_d);
    $nom_fichier_csv = $date_du_jour.'_export_table_auteurs.csv';

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

    // création d'un array contenant tous les logins des admins restreints
    if(in_array("0minirezo",$choix_statut)){
        $r = sql_select("DISTINCT auteur.login AS login",array("spip_auteurs AS auteur","spip_auteurs_liens AS liens"),array("auteur.statut='0minirezo'","liens.id_auteur=auteur.id_auteur","liens.objet='rubrique'"));
        while($row = sql_fetch($r)){
            $login_restreint[]=$row['login'];
        }
    }

    if ($res = sql_select('*', 'spip_auteurs AS auteur')){
        while ($row = sql_fetch($res)){
            // test les statuts demandés
            if (in_array($row[statut],$choix_statut)){
                // si c'est un admin, on ne selectionne que les admins restreints !!!
                if ((($row['statut'] == "0minirezo") AND (in_array($row['login'],$login_restreint))) OR $row['statut']=="1comite" OR $row['statut'] == "6forum"){
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
                    if (test_plugin_actif("accesrestreint")){
                        $k=0;
                        if ($res3 = sql_select(
                            array(
                                "rub.titre AS titre"),
                            array(
                                "spip_zones_liens AS zone_auteur",
                                "spip_zones_liens AS zone_rubrique",
                                "spip_rubriques AS rub"),
                            array(
                                "zone_auteur.id_zone = zone_rubrique.id_zone",
                                "zone_auteur.objet='auteur' AND zone_auteur.id_objet =$row[id_auteur]",
                                "zone_rubrique.objet='rubrique'AND zone_rubrique.id_objet = rub.id_rubrique")
                            )) {
                                while ($row3 = sql_fetch($res3)){
                                    $zones[$row[nom]][$k]=$row3[titre];
                                    $tableau_csv[$i]["zone"]=implode('|',$zones[$row[nom]]);
                                    $k++;

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
