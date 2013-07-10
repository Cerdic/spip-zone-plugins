<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2spip_importation_charger_dist(){
	$annee=date("Y");
    $valeurs = array(
        "fichier_csv"                => "",
        "maj_utilisateur"            => "",
        "abs_redac"                  => "",
        "abs_admin"                  => "",
        "abs_visiteur"               => "",
        "traitement_article_efface"  => "rien_faire",
        "transfere_article"          => "",
        "rubrique_parent_archive"    => "0",
        "nom_rubrique_archive"       => "archive_$annee",
        "rubrique_parent"            => "0",
    );
        
return $valeurs;
}

function formulaires_csv2spip_importation_verifier_dist(){
        
    $erreurs = array();
	//champs obligatoire 
    if (!($_FILES['fichier_csv']['name'])) {
        $erreurs['fichier_csv'] = _T('csv2spip:obligatoire');
    } else {
    //Transfert réussi 
        if ($_FILES['fichier_csv']['error'] > 0) $erreurs['fichier_csv'] = _T('csv2spip:transfert');
    //Taille max du fichier csv < 2Mo
        $maxsize=1000000;
        if ($_FILES['fichier_csv']['size'] > $maxsize) $erreurs['fichier_csv'] =_T('csv2spip:taille');
    //Extension csv
        $extensions_valides = array( 'csv','txt' );
        $extension_upload = strtolower(  substr(  strrchr($_FILES['fichier_csv']['name'], '.')  ,1)  );
        if (!in_array($extension_upload,$extensions_valides)) $erreurs['fichier_csv'] = _T('csv2spip:extension');
    }
	//Il y a des erreurs
    if (count($erreurs)) $erreurs['message_erreur'] = _T('csv2spip:erreurs');

    return $erreurs;
}

function formulaires_csv2spip_importation_traiter_dist(){
    $maj_utilisateur = _request('maj_utilisateur');
    $abs_redacs = _request('abs_redac');
    $abs_admins = _request('abs_admin');
    $abs_visiteurs = _request('abs_visiteur');
    $suppression_article_efface = _request('suppression_article_efface');
    $traitement_article_efface = _request('traitement_article_efface');
    $id_rubrique_parent = intval(_request('rubrique_parent_archive'));
    $nom_rubrique_archive = _request('nom_rubrique_archive');
    $rubrique_parent = _request('rubrique_parent');
    
    $retour = array();

	if ($abs_redacs OR $abs_admins OR $abs_visiteurs){
		include_spip('action/editer_objet');
		include_spip('action/editer_liens');
		include_spip('action/editer_zone');
	}

	// récupération du fichier csv
    $tmp_name    = $_FILES['fichier_csv']['tmp_name'];
    $destination = _DIR_TMP.basename($tmp_name);
    $resultat    = move_uploaded_file($tmp_name,$destination);
    if (!$resultat) {
        $retour['message_erreur'] = _T('csv2spip:transfert');
    } else{
        $retour['message_ok'] = _T('csv2spip:bravo');
    }

    // transformation du fichier csv en 4 array : 
    // $en_tete = ligne entete 
    // pour les 3 tableaux suivant, la cle est soit le login et s'il n'existe pas on prend le mail
    // $tableau_csv_visiteurs
    // $tableau_csv_redacs
    // $tableau_csv_admins
    $tableau_csv_visiteurs = $tableau_csv_redacs = $tableau_csv_admins = array();
    $fichiercsv= fopen($destination, "r");
    $i=0;
    while (($data= fgetcsv($fichiercsv,"~")) !== FALSE){
       // petit hack car fgetcsv ne reconnait pas le ~ comme séparateur !!!
       $data           = implode("~",$data);
       $data           = explode("~",$data);
       $nombre_elements = count($data);

		if ($i==0) {
			for ($j = 0; $j < $nombre_elements; $j++) {
				$en_tete[$j]=$data[$j];    //Récupération de la ligne d'entete
				if ($en_tete[$j] == 'statut')
					$num_statut = $j;
				if ($en_tete[$j] == 'login')
					$num_login = $j;
				if ($en_tete[$j] == 'email')
					$num_email = $j;
		   }
			if (!$num_statut OR !$num_login OR !$num_email){
				$retour['message_erreur'] = _T('csv2spip:champ_manquant').'email:'.$num_email.' login:'.$num_login.' statut'.$num_statut;
				return  $retour;
			}
	   } else {
			for ($j = 0; $j < $nombre_elements; $j++) {
				if ($data[$num_login] OR $data[$num_email]) { 	//creation du tableau contenant l'ensemble des données à importer
				   if ($data[$num_statut] == '6forum')
						$tableau_csv_visiteurs[$data[$num_login]?$data[$num_login]:$data[$num_email]][$en_tete[$j]] = $data[$j];
				   if ($data[$num_statut] == '1comite')
						$tableau_csv_redacs[$data[$num_login]?$data[$num_login]:$data[$num_email]][$en_tete[$j]] = $data[$j];
				   if ($data[$num_statut] == '0minirezo')
						$tableau_csv_admins[$data[$num_login]?$data[$num_login]:$data[$num_email]][$en_tete[$j]] = $data[$j];
				}
			}
		}
        $i++;
    }
    fclose($fichiercsv);

    //récupération des auteurs de la bdd en 3 array 
    // $visiteur_bdd
    // $redacteur_bdd
    // $admin_restreint_bdd
    // la cle de chaque tableau est le login et s'il n'existe pas le mail
    $visiteur_bdd_req        = sql_allfetsel('*', 'spip_auteurs',array('statut="6forum"','(login!="" OR email!="")'));    
    foreach ($visiteur_bdd_req as $key) {
        $visiteur_bdd[$key['login']?$key['login']:$key['email']]=$key;
    }
    $redacteur_bdd_req       = sql_allfetsel('*', 'spip_auteurs', array('statut="1comite"','(login!="" OR email!="")'));
    foreach ($redacteur_bdd_req as $key) {
        $redacteur_bdd[$key['login']?$key['login']:$key['email']]=$key;
    }
    //on récupère seulement les admins restreints !!!
    $from = array( 
        "spip_auteurs AS auteurs",
        "spip_auteurs_liens AS liens");
    $where = array(
        "auteurs.statut = '0minirezo'",
        "liens.objet = 'rubrique'",
        "liens.id_auteur = auteurs.id_auteur",
        '(login!="" OR email!="")');
    $admin_restreint_bdd_req       = sql_allfetsel("DISTINCT auteurs.*" ,$from, $where);
    foreach ($admin_restreint_bdd_req as $key) {
        $admin_restreint_bdd[$key['login']?$key['login']:$key['email']]=$key;
    }
    
    // PARTIE II : Suppresions des absents (changer le statut des auteurs en 5.poubelle)  avec 3 choix pour la gestion des articles associés
    // 1. ras
    // 2. supprimer les articles 
    // 3. transferer les articles dans une rubrique d'archivage

    // Si choix3 : transferer les articles , création de la rubrique d'archive (en tenant compte d'une rubrique parent)
	if($traitement_article_efface == "transferer_articles"){
		if(!$id_rubrique_archive = sql_fetsel('id_rubrique','spip_rubriques',array('titre ="'.$nom_rubrique_archive.'"',"id_parent=$id_rubrique_parent"))){
			$objet = 'rubrique';
			$set = array('titre'=>$nom_rubrique_archive);
			$id_rubrique_archive = objet_inserer($objet,$id_rubrique_parent);
			objet_modifier($objet, $id_rubrique_archive, $set);
		}
	}	 
    
    if ($abs_visiteurs) {
		$Tid_visiteurs = csv2spip_diff_absents($visiteur_bdd_par, $tableau_csv_visiteurs);
		csv2spip_supprimer_auteurs($Tid_visiteurs, '6forum');
	}
    if ($abs_redacs) {
		$Tid_redacs = csv2spip_diff_absents($redacteur_bdd, $tableau_csv_redacs);
		csv2spip_supprimer_auteurs($Tid_redacs, '1comite',$traitement_article_efface,$id_rubrique_archive);
	}
    if ($abs_admins) {
		$Tid_admins = csv2spip_diff_absents($admin_restreint_bdd, $tableau_csv_admins);
		csv2spip_supprimer_auteurs($Tid_admins, '0minirezo',$traitement_article_efface,$id_rubrique_archive);
	}

    
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


//	$retour['redirect'] = 'index.php?exec=csv2spip';
    return $retour;
}

/*
 * générer l"array des id auteurs absents à supprimer
 * @param $Tbdd: l'array indexé login/mail extrait de la base
 * @param $Tfich: l'array indexé login/mail extrait du csv
 * @return l'array des id_auteurs
 */
function csv2spip_diff_absents($Tbdd, $Tfich){
	$Tid = array();
	$T = array_diff_key($Tbdd, $Tfich);
	foreach ($T as $val)
		$Tid[] = $val['id_auteur'];

	return $Tid;
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
function csv2spip_supprimer_auteurs($Tid, $statut,$traitement="",$id_rubrique_archive=1) {
	// passage à la poubelle
	$objet = 'auteur';
	$set = array('statut'=>'5poubelle');
	foreach ($Tid as $id){
		objet_modifier($objet, $id, $set);
		// suppression des zones de l'auteur
		$Tzones = sql_allfetsel('id_zone', 'spip_zones_liens', array('id_objet='.$id, 'objet="auteur"'));
		foreach ($Tzones as $id_zone)
			zone_lier($id_zone, 'auteur', $id, 'del');
			
		// suppression des rubriques des admins restreints
		if ($statut == '0minirezo') {
			$Trubriques = sql_allfetsel('id_objet', 'spip_auteurs_liens', array('id_auteur='.$id, 'objet="rubrique"'));
			objet_dissocier(array('id_auteur'=>$id), array('rubrique'=>$Trubriques));
		}
		
		// traitement des articles de l'auteur
		if (in_array($statut, array('0minirezo','1comite'))){
			$Tarticles = sql_allfetsel('id_objet', 'spip_auteurs_liens', array('id_auteur='.$id, 'objet="article"'));
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
	}
}

?>
