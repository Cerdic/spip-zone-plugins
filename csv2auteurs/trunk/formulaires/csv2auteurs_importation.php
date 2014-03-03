<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2auteurs_importation_charger_dist(){
    $annee=date("Y");
    $valeurs = array(
        "fichier_csv"                => "",
        "maj_utilisateur"            => "",
        "type_maj"                   => "ajouter",
        "abs_redac"                  => "",
        "abs_admin"                  => "",
        "abs_poubelle"               => "supprimer",
        "abs_visiteur"               => "",
        "traitement_article_efface"  => "rien_faire",
        "transfere_article"          => "",
        "id_rubrique_parent_archive"    => "0",
        "nom_rubrique_archive"       => "archive_$annee",
        "id_rubrique_parent"            => "0",
    );
        
return $valeurs;
}

function formulaires_csv2auteurs_importation_verifier_dist(){
    $erreurs = array();
    // seuls les webmestres ont le droit d'utiliser cet outil!
    if ($GLOBALS['visiteur_session']['webmestre'] != 'oui') {
        $erreurs['message_erreur'] = _T('csv2auteurs:non_autorise');
        return $erreurs;
    }
        
    //champs obligatoire 
    if (!($_FILES['fichier_csv']['name'])) {
        $erreurs['fichier_csv'] = _T('csv2auteurs:obligatoire');
    } else {
    //Transfert réussi 
        if ($_FILES['fichier_csv']['error'] > 0) $erreurs['fichier_csv'] = _T('csv2auteurs:transfert');
    //Taille max du fichier csv < 2Mo
        $maxsize=1000000;
        if ($_FILES['fichier_csv']['size'] > $maxsize) $erreurs['fichier_csv'] =_T('csv2auteurs:taille');
    //Extension csv
        $extensions_valides = array( 'csv','txt' );
        $extension_upload = strtolower(  substr(  strrchr($_FILES['fichier_csv']['name'], '.')  ,1)  );
        if (!in_array($extension_upload,$extensions_valides)) $erreurs['fichier_csv'] = _T('csv2auteurs:extension');
    }
    //Il y a des erreurs
    if (count($erreurs)) 
        $erreurs['message_erreur'] = _T('csv2auteurs:erreurs');

    return $erreurs;
}

function formulaires_csv2auteurs_importation_traiter_dist(){
    $maj_utilisateur = _request('maj_utilisateur');
    $abs_redacs = _request('abs_redac');
    $abs_admins = _request('abs_admin');
    $abs_visiteurs = _request('abs_visiteur');
    $abs_poubelle = _request('abs_poubelle');
    $suppression_article_efface = _request('suppression_article_efface');
    $traitement_article_efface = _request('traitement_article_efface');
    $nom_rubrique_archive = _request('nom_rubrique_archive');
    $type_maj=_request('type_maj');

    // recuperation de l'id de la rubrique parent des rubriques admins
    $id_rubrique_parent_admin = _request('rubrique_parent');
    $id_rubrique_parent_admin = explode('|',$id_rubrique_parent_admin[0]);
    $id_rubrique_parent_admin = $id_rubrique_parent_admin[1];

    //récupération de l'id de la rubrique parent archive
    $id_rubrique_parent_archive = _request('rubrique_parent_archive');
    $id_rubrique_parent_archive = explode('|',$id_rubrique_parent_archive[0]);
    $id_rubrique_parent_archive = $id_rubrique_parent_archive[1];
    
    $retour = array();

    include_spip('action/editer_rubrique');
    if (test_plugin_actif("accesrestreint"))
        include_spip('action/editer_zone');
    include_spip('action/editer_auteur');
    
    if ($abs_redacs OR $abs_admins OR $abs_visiteurs OR $abs_poubelle == 'supprimer'){
        include_spip('action/editer_objet');
        include_spip('action/editer_liens');
        include_spip('action/editer_zone');
    }

    // récupération du fichier csv
    include_spip('inc/config');
    $separateur  = lire_config("csv2auteurs_separateur");
    $tmp_name    = $_FILES['fichier_csv']['tmp_name'];
    $destination = _DIR_TMP.basename($tmp_name);
    $resultat    = move_uploaded_file($tmp_name,$destination);
    if (!$resultat) {
        $retour['message_erreur'] = _T('csv2auteurs:transfert');
    } else{
        $retour['message_ok'] = _T('csv2auteurs:bravo');
    }

    // transformation du fichier csv en 4 array : 
    // $en_tete = ligne entete 
    // pour les 3 tableaux suivant, la cle est soit le login et s'il n'existe pas on prend le mail
    // $tableau_csv_visiteurs
    // $tableau_csv_redacs
    // $tableau_csv_admins
    $tableau_csv_visiteurs = $tableau_csv_redacs = $tableau_csv_admins = array();
    $tableau_csv_rubriques_admins = array();
    $fichiercsv= fopen($destination, "r");
    $i=0;
    
    // correspondance statut spip / statut csv
    $Tcorrespondances = array('administrateur'=>'0minirezo', 'redacteur'=>'1comite', 'visiteur'=>'6forum', 'poubelle' => '5poubelle');
        
    // tableau de tous les admins
    $result = sql_select(array('login'), 'spip_auteurs', array('statut = "0minirezo"'));
    while ($r = sql_fetch($result)) {
        $Tadmin_tous[] = $r['login'];
    }
    // tableau des admins restreints
    $Tadmin_restreint=array();
    $from = array( 
        "spip_auteurs AS auteurs",
        "spip_auteurs_liens AS liens");
    $where = array(
        "auteurs.statut = '0minirezo'",
        "liens.objet = 'rubrique'",
        "liens.id_auteur = auteurs.id_auteur",
        'login!=""');
    $result = sql_select(array('login'),$from, $where);
    while ($r = sql_fetch($result)) {
        $Tadmin_restreint[] = $r['login'];
    }
    // tableau admins complets
    $Tadmin_complet = array_diff($Tadmin_tous, $Tadmin_restreint);
    // traiter fichier CSV
    $num_statut = $num_login = -1;
    while (($data= fgetcsv($fichiercsv,"$separateur")) !== FALSE){
       // petit hack car fgetcsv ne reconnait pas le ~ comme séparateur !!!
       $data           = implode("$separateur",$data);
       $data           = explode("$separateur",$data);
       $nombre_elements = count($data);
        if ($i==0) {
            for ($j = 0; $j < $nombre_elements; $j++) {
                $en_tete[$j] = strtolower($data[$j]);    //Récupération de la ligne d'entete
                if ($en_tete[$j] == 'statut')
                    $num_statut = $j;
                if ($en_tete[$j] == 'login')
                    $num_login = $j;
           }
            if ($num_statut < 0 OR $num_login < 0){
                $retour['message_erreur'] = _T('csv2auteurs:champ_manquant').' login:'.$num_login.' statut'.$num_statut;
                return  $retour;
            }
       } 
       else {
            for ($j = 0; $j < $nombre_elements; $j++) {
                // on ne veut pas les auteurs du CSV ayant login égal à celui d'un admin complet
                if (($data[$num_login] AND !in_array($data[$num_login], $Tadmin_complet))) {
                    // creation du tableau contenant l'ensemble des données à importer
                   if ($Tcorrespondances[strtolower($data[$num_statut])] == '6forum')
                        $tableau_csv_visiteurs[$data[$num_login]][$en_tete[$j]] = $en_tete[$j] == "statut" ? "6forum" : $data[$j];
                   if ($Tcorrespondances[strtolower($data[$num_statut])] == '1comite')
                        $tableau_csv_redacs[$data[$num_login]][$en_tete[$j]] = $en_tete[$j] == "statut" ? "1comite" : $data[$j];
                   if ($Tcorrespondances[strtolower($data[$num_statut])] == '0minirezo') {
                        $tableau_csv_admins[$data[$num_login]][$en_tete[$j]] = $en_tete[$j] == "statut" ? "0minirezo" : $data[$j];
                        if ($en_tete[$j] == 'ss_groupe' AND $data[$j]) {
                            $Trub = explode('|', $data[$j]);
                            foreach($Trub as $rub)
                                if (!in_array($rub, $tableau_csv_rubriques_admins))
                                    $tableau_csv_rubriques_admins[] = $rub;
                        }
                    }
                }
            }
        }
        $i++;
    }
    fclose($fichiercsv);
    unlink($destination);

    // tableau CSV total
    $tableau_csv_total = $tableau_csv_visiteurs + $tableau_csv_redacs + $tableau_csv_admins;
//    echo "<pre>";
//    print_r($tableau_csv_visiteurs);
//    echo "admi";
//    print_r($tableau_csv_admins);
//    echo "red";
//    print_r($tableau_csv_redacs);
//    print_r($tableau_csv_total);die;

//spip_log("tableau csv total","csvspip");
//spip_log($tableau_csv_total,"csvspip");

    //récupération des auteurs de la bdd en 4 array
    // on ne prend pas les auteurs sans login
    // $poubelle_bdd = les auteurs à la poubelle
    // $visiteur_bdd = les visiteurs
    // $redacteur_bdd
    // $admin_restreint_bdd
    // la cle de chaque tableau est le login
    $poubelle_bdd=$visiteur_bdd=$redacteur_bdd=$admin_restreint_bdd=array();
/*    $poubelle_bdd_req        = sql_allfetsel('*', 'spip_auteurs',array('statut="5poubelle"','(login!="")'));
    foreach ($poubelle_bdd_req as $key) {
        $poubelle_bdd[$key['login']]=$key;
    }    
*/
    $visiteur_bdd_req        = sql_allfetsel('*', 'spip_auteurs',array('statut="6forum"','(login!="")'));
    foreach ($visiteur_bdd_req as $key) {
        $visiteur_bdd[$key['login']]=$key;
    }
    $redacteur_bdd_req       = sql_allfetsel('*', 'spip_auteurs', array('statut="1comite"','(login!="")'));
    foreach ($redacteur_bdd_req as $key) {
        $redacteur_bdd[$key['login']]=$key;
    }
    //on récupère seulement les admins restreints !!!
    $from = array( 
        "spip_auteurs AS auteurs",
        "spip_auteurs_liens AS liens");
    $where = array(
        "auteurs.statut = '0minirezo'",
        "liens.objet = 'rubrique'",
        "liens.id_auteur = auteurs.id_auteur",
        '(login!="")');
    $admin_restreint_bdd_req       = sql_allfetsel("DISTINCT auteurs.*" ,$from, $where);
    foreach ($admin_restreint_bdd_req as $key) {
        $admin_restreint_bdd[$key['login']]=$key;
    }

    // tableau BDD total
    $tableau_bdd_total = $poubelle_bdd + $visiteur_bdd +  $redacteur_bdd + $admin_restreint_bdd;

    // traitement rubriques admin
    // construction du tableau de correspondance nom_rubrique avec leur id
    // création des rubriques n'existant pas
    $tableau_bdd_rubriques_admins = array();
    $result = sql_select(array('id_rubrique', 'titre'), 'spip_rubriques');
    while ($row = sql_fetch($result)){
        $tableau_bdd_rubriques_admins[$row['id_rubrique']] = strtolower($row['titre']);
    }

    // traitement zones
    // construction du tableau de correspondance nom_zone avec leur id
    $tableau_bdd_zones_admins = array();
    if (test_plugin_actif("accesrestreint")){
        $result = sql_select(array('id_zone', 'titre'), 'spip_zones');
        while ($row = sql_fetch($result)){
            $tableau_bdd_zones_admins[$row['id_zone']] = strtolower($row['titre']);
        }
    }
    spip_log($tableau_bdd_zones_admins,"csvspip");
    // créer les rubriques admins du csv n'existant pas et les indexer
    foreach($tableau_csv_rubriques_admins as $id_rub=>$rub){
        if (!in_array(strtolower($rub), $tableau_bdd_rubriques_admins)) {
            $set = array('titre'=>$rub);
            $id_rub = rubrique_inserer($id_rubrique_parent_admin);
            rubrique_modifier($id_rub, $set);
            $tableau_bdd_rubriques_admins[$id_rub] = strtolower($rub);
        }
    }

    //Récuperer les champs de la table auteurs
    $Tnom_champs_bdd=array();
    $desc = sql_showtable('spip_auteurs',true);
    foreach ($desc['field'] as $cle => $valeur)
        $Tnom_champs_bdd[] = $cle;

    
    // PARTIE I : maj ou ajout des auteurs
    $tableau_nouveaux_auteurs = array_diff_key($tableau_csv_total, $tableau_bdd_total);
    // si maj demandée
    if ($maj_utilisateur) {
        // construire le tableau des utilisateurs à mettre à jour, indexé sur le login
        $tableau_maj_auteurs = array_diff_key($tableau_csv_total, $tableau_nouveaux_auteurs);
        
        // construire le tableau de correspondance login csv => id_auteur bdd
        $tableau_maj_auteurs_id = array();
        $Tlogins = array_keys($tableau_maj_auteurs);
        $chaine_in = implode('","', $Tlogins);
        $chaine_in = '"'.$chaine_in.'"';
        $res = sql_select('id_auteur, login', 'spip_auteurs', array('login IN ('.$chaine_in.')'));
        while ($row = sql_fetch($res)) {
            $tableau_maj_auteurs_id[$row['login']] = $row['id_auteur'];
        }

        // si remplacer les données zones et rubriques administrées: supprimer les liens existant
        if ($type_maj == 'remplacer' AND test_plugin_actif("accesrestreint")) {
            // suppression des liens des rubriques administrées
            objet_dissocier(array("auteur"=>array_values($tableau_maj_auteurs_id)), array("rubrique"=>"*"));    
            // suppression des zones des auteurs
            zone_lier('',"auteur",array_values($tableau_maj_auteurs_id),'del');
        }
//spip_log("table zone admin","csvspip");

//spip_log($tableau_bdd_zones_admins,"csvspip");

        // maj des données des auteurs
        foreach($tableau_maj_auteurs as $login => $Tauteur)
            csv2auteurs_ajout_utilisateur($login, $Tauteur, $Tnom_champs_bdd, $Tcorrespondances, $tableau_bdd_rubriques_admins, $tableau_bdd_zones_admins, $tableau_maj_auteurs_id[$login]);

/*      
echo '<br><pre>';
var_dump($tableau_maj_auteurs_id);
*/
        
    }
    
    
    // dans tous les cas ajout des nouveaux
    foreach($tableau_nouveaux_auteurs as $login => $Tauteur)
        csv2auteurs_ajout_utilisateur($login,$Tauteur,$Tnom_champs_bdd,$Tcorrespondances, $tableau_bdd_rubriques_admins, $tableau_bdd_zones_admins);

        
    



    // PARTIE II : Suppressions des absents (changer le statut des auteurs en 5.poubelle)  avec 3 choix pour la gestion des articles associés
    // 1. ras
    // 2. supprimer les articles 
    // 3. transferer les articles dans une rubrique d'archivage

    // Si choix3 : transferer les articles , création de la rubrique d'archive (en tenant compte d'une rubrique parent)
    if($traitement_article_efface == "transferer_articles"){
        if(!$id_rubrique_archive = sql_fetsel('id_rubrique','spip_rubriques',array('titre ="'.$nom_rubrique_archive.'"',"id_parent=$id_rubrique_parent_archive"))){
            $objet = 'rubrique';
            $set = array('titre'=>$nom_rubrique_archive);
            $id_rubrique_archive = objet_inserer($objet,$id_rubrique_parent_archive);
            objet_modifier($objet, $id_rubrique_archive, $set);
        }
    }    
    
    // si l'option auteurs sans articles = suppression complète 
    // alors on supprime aussi tous les auteurs à la poubelle (sans articles)
    if ($abs_poubelle == 'supprimer') {
		// récupérer les auteurs à la poubelle avec articles
		$not_in = sql_allfetsel('auteurs.id_auteur', 
			array('spip_auteurs_liens AS liens','spip_auteurs AS auteurs'), 
			array('liens.id_auteur = auteurs.id_auteur', 'liens.objet="article"', 'auteurs.statut="5poubelle"'),
			array('liens.id_auteur'));		
		$Tnot_in = 	array();
		foreach ($not_in as $index => $Tid_auteur) 
			$Tnot_in[] = $Tid_auteur['id_auteur'];
		$not_in = sql_in('id_auteur', $Tnot_in, 'NOT');
		// récupérer les auteurs à la poubelle sans articles
		$Tabs_poubelle = sql_allfetsel('id_auteur', 'spip_auteurs',array('statut="5poubelle"', $not_in));
		$Ta_suppr = array();
		foreach ($Tabs_poubelle as $index => $Tid_auteur) 
			$Ta_suppr[] = $Tid_auteur['id_auteur'];
		// effacer définitevement ces auteurs
		$in = sql_in('id_auteur', $Ta_suppr);
		sql_delete('spip_auteurs', $in);
	}		
//        $Tid_poubelle = csv2auteurs_diff_absents($poubelle_bdd);
//        csv2auteurs_supprimer_auteurs($Tid_poubelle, '5poubelle', $traitement_article_efface,$id_rubrique_parent_archive);

    if ($abs_visiteurs) {
        $Tid_visiteurs = csv2auteurs_diff_absents($visiteur_bdd, $tableau_csv_visiteurs);
        csv2auteurs_supprimer_auteurs($Tid_visiteurs, '6forum', $traitement_article_efface,$id_rubrique_parent_archive);
    }
    if ($abs_redacs) {
        $Tid_redacs = csv2auteurs_diff_absents($redacteur_bdd, $tableau_csv_redacs);
        csv2auteurs_supprimer_auteurs($Tid_redacs, '1comite',$traitement_article_efface,$id_rubrique_parent_archive);
    }
    if ($abs_admins) {
        $Tid_admins = csv2auteurs_diff_absents($admin_restreint_bdd, $tableau_csv_admins);
        csv2auteurs_supprimer_auteurs($Tid_admins, '0minirezo',$traitement_article_efface,$id_rubrique_parent_archive);
    }

    // PARTIE III : maj des existants 
    // 1. ras
    // 2. supprimer les articles 
    // 3. transferer les articles dans une rubrique d'archivage

    
/*
echo '<pre>$visiteur_bdd';
var_dump($visiteur_bdd);
echo '<br>$redacteur_bdd:';
var_dump($redacteur_bdd);
echo '<br>$admin_restreint';
var_dump($admin_restreint);
echo '</pre>';
die;
*/


//  $retour['redirect'] = 'index.php?exec=csv2auteurs';
    return $retour;
}

/*
 * générer l"array des id auteurs absents à supprimer
 * @param $Tbdd: l'array indexé login/mail extrait de la base
 * @param $Tcsv: l'array indexé login/mail extrait du csv
 * @return l'array des id_auteurs
 */
function csv2auteurs_diff_absents($Tbdd, $Tcsv=array()){
    $Tid = array();
    $T = array_diff_key($Tbdd, $Tcsv);
    foreach ($T as $val)
        $Tid[] = $val['id_auteur'];

    return $Tid;
}


/*
 * ajout d'un utilisateur
 * @param login de l'auteur
 * @param array associatif CSV: Tauteur_csv  nom_champ : valeur
 */
function csv2auteurs_ajout_utilisateur($login,$Tauteur_csv,$Tnom_champs_bdd,$Tcorrespondances, $tableau_bdd_rubriques_admins, $tableau_bdd_zones_admins, $id_auteur=0){
    $set = $Tzones = $Trubadmin = array();
    foreach($Tauteur_csv as $champ => $valeur){
        if($champ == "ss_groupe"){
            $T = explode('|',$valeur);
            foreach($T as $rub){
                $Trubadmin[] = array_search(strtolower($rub),$tableau_bdd_rubriques_admins);
            }
        }
        if($champ == "zone"){
            $T = explode('|',$valeur);
            foreach($T as $zone){
                $Tzones[] = array_search(trim(rtrim(strtolower($zone))),$tableau_bdd_zones_admins);
            }
//spip_log("tzone de id_auteur $id_auteur","csvspip");
//spip_log($Tzones,"csvspip");

        }
        if(in_array($champ,$Tnom_champs_bdd)){
            $set[$champ] = ($champ == "statut" AND array_key_exists($valeur,$Tcorrespondances)) ? $Tcorrespondances[$valeur] : $valeur;
        }
    }

    //inserer l'auteur si il n'y a pas d'id_auteur transmis
    if (!$id_auteur)
        $id_auteur = auteur_inserer();
    
    // remplir les champs ou les maj
    auteur_modifier($id_auteur,$set);

    //liaison des rubriques
    if(count($Trubadmin) AND $set["statut"] == "0minirezo")
        objet_associer(array("auteur"=>$id_auteur),array("rubrique"=>$Trubadmin));
        
    //liaison des zones
    if(count($Tzones) AND test_plugin_actif("accesrestreint") AND test_plugin_actif("accesrestreint"))
        zone_lier($Tzones, 'auteur', $id_auteur, 'add');

}


/*
 * Suppression propre des auteurs 
 * changement de statut à la poubelle + traitement des liaisons spip_auteurs_liens et spip_zones_liens
 * gestion des articles des auteurs supprimés
 * @param $Tid array des id_auteurs à traiter
 * @param $statut des auteurs passent dans $Tid
 * $param $traitement : choix 2 (suppresion) ou 3 (transfere)
 * $param $id_rubrique_archive
 * 
 */
function csv2auteurs_supprimer_auteurs($Tid, $statut,$traitement="supprimer_articles",$id_rubrique_archive=1) {
    // passage à la poubelle
    $objet = 'auteur';
    $set = array('statut'=>'5poubelle');
    foreach ($Tid as $id){
        $Tarticles = sql_allfetsel('id_objet', 'spip_auteurs_liens', array('id_auteur='.$id, 'objet="article"'));

        // auteur sans article et demande de suppression: suppression complète
        if (count($Tarticles) == 0 AND _request('abs_poubelle') == 'supprimer')
            sql_delete('spip_auteurs', "id_auteur=$id");
        // passage à la poubelle
        else
            objet_modifier($objet, $id, $set);

        // traitement des articles de l'auteur
        if (count($Tarticles) != 0) {

            // supprimer les articles
            $table_idarticle = array();
            if ($traitement == 'supprimer_articles'){
                objet_dissocier(array('id_auteur'=>$id), array('article'=>$Tarticles));
                foreach ($Tarticles as $idarticle) {
                    $table_idarticle[]=$idarticle['id_objet'];
                }
                $inarticle = join(',',$table_idarticle);
                sql_delete('spip_articles', "id_article IN ($inarticle)");
            }
            // deplacer les articles dans la rubrique d'archivage
            if ($traitement == 'transferer_articles'){
                foreach($Tarticles as $idarticle)
                    objet_modifier('article', $idarticle['id_objet'], array('id_parent'=>$id_rubrique_archive));
            }
        }
            
        if (test_plugin_actif("accesrestreint")){
            // suppression des zones de l'auteur
            $Tzones = sql_allfetsel('id_zone', 'spip_zones_liens', array('id_objet='.$id, 'objet="auteur"'));
            foreach ($Tzones as $id_zone)
                zone_lier($id_zone, 'auteur', $id, 'del');
        }   
        // suppression des rubriques des admins restreints
        if ($statut == '0minirezo') {
            $Trubriques = sql_allfetsel('id_objet', 'spip_auteurs_liens', array('id_auteur='.$id, 'objet="rubrique"'));
            objet_dissocier(array('id_auteur'=>$id), array('rubrique'=>$Trubriques));
        }
    }
}

?>
