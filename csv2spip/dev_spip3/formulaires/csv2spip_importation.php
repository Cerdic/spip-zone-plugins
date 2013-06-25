<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2spip_importation_charger_dist(){
    $valeurs = array(
        "fichier_csv"                => "",
        "maj_utilisateur"            => "",
        "abs_redac"                  => "",
        "abs_admin"                  => "",
        "abs_visiteur"               => "",
        "suppression_article_efface" => "",
        "transfere_article"          => "",
        "rubrique_parent_archive"    => "",
        "nom_rubrique_archive"       => "",
        "rubrique_parent" => "",
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
    $retour = array();

// récupération du fichier csv
    $tmp_name    = $_FILES['fichier_csv']['tmp_name'];
    $destination = _DIR_TMP.basename($tmp_name);
    $resultat    = move_uploaded_file($tmp_name,$destination);
    if (!$resultat) {
        $retour['message_erreur'] = _T('csv2spip:transfert');
    }else{
        $retour['message_ok'] = _T('csv2spip:bravo');
    }

// transformation du fichier csv en array


    $fichiercsv= fopen($destination, "r");

    $i=0;
    while (($data= fgetcsv($fichiercsv,",")) !== FALSE){
        $tableau_csv[$i]["login"]     = $data[0];
        $tableau_csv[$i]["nom"]       = $data[1];
        $tableau_csv[$i]["prenom"]    = $data[2];
        $tableau_csv[$i]["pass"]      = $data[3];
        $tableau_csv[$i]["bio"]       = $data[4];
        $tableau_csv[$i]["email"]     = $data[5];
        $tableau_csv[$i]["groupe"]    = $data[6];
        $tableau_csv[$i]["ss_groupe"] = $data[7];
        $i++;
    }
    fclose($fichiercsv);
    //var_dump($tableau_csv);
    return $retour;
}
?>
