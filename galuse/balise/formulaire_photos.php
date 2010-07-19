<?php
/*
Plugin galuse
réalisation: Thom 2010
Sur la base du plugin de B. Blanzin
Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*/
if (!defined("_ECRIRE_INC_VERSION")) return;
function balise_FORMULAIRE_PHOTOS($p) {
    return calculer_balise_dynamique($p, 'FORMULAIRE_PHOTOS', array());
	
}

function balise_FORMULAIRE_PHOTOS_dyn() {
return array('formulaires/formulaire_photos', 0, 
		array(
		));
}

//Définition des variables
$dir        = $_DIR_IMG . "galuse/";
$types_autorises    = lire_config('galuse/types_image');      // types d'images supportés !!!! attention extensions est un tableau .... est ce que ça va marcher ????
$max_size   = lire_config('galuse/poids');     // Taille max en octets du fichier
$width_max  = lire_config('galuse/largeur');        // Largeur max de l'image en pixels
$height_max = lire_config('galuse/hauteur');        // Hauteur max de l'image en pixels
$width_redim  = lire_config('galuse/largeur_redim');        // Largeur de redimensionnement de l'image en pixels
$height_redim = lire_config('galuse/hauteur_redim');        // Hauteur de redimensionnement de l'image en pixels
$comp_jpg   = lire_config('galuse/compression');        // paramètre de compression jpeg
$moderation = lire_config('galuse/moderation');         // moderation


//  Définition des variables liées au fichier IMG
$nom_file   = $_FILES['fichier']['name'];
$taille     = $_FILES['fichier']['size'];
$tmp        = $_FILES['fichier']['tmp_name'];

if(!autoriser('joindregaluse',$_POST['type_objet'],$_POST['id_objet'],$_POST['id_auteur'],)){
    if( $_POST['id_auteur'] == "non"){
        // fichier local
        if(!empty($_POST['posted'])) {
            if(!empty($_FILES['fichier']['name'])) {
            if( $type=exif_imagetype($_FILES['fichier']['name'])){
                if( in_array($type, $types_autorises)) {
                $infos_img = getimagesize($_FILES['fichier']['tmp_name']); 
                if(($infos_img[0] <= $width_max) && ($infos_img[1] <= $height_max) && ($_FILES['fichier']['size'] <= $max_size)) {
                    $nom=substr($nom_file,0,strrpos($nom_file));
                    switch($type){
                    case IMAGETYPE_JPEG:$ext='jpg';break;
                    case IMAGETYPE_PNG:$ext='png';break;
                    case IMAGETYPE_GIF:$ext='gif';break;
                    }
                    if( file_exists($dir.$nom.'.'.$ext)){
                    $num=1;
                    while( file_exists($dir.$nom.$num.'.'.$ext)) $num+=1;
                    $nom.=$num;
                    }
                    $nom_file=$nom.'.'.$ext;
                    if(move_uploaded_file($_FILES['fichier']['tmp_name'], $dir.$nom_file )) {
                    $dateheure=date('Y-m-d H:i:s');
                    $type=$infos_img[2];
                    if(($infos_img[0] > $width_redim ) or ($infos_img[1] > $height_redim ) // redimension du fichier
                        $r= max($infos_img[0] / $width_redim,$infos_img[1] / $height_redim);
                        switch($type){
                        case IMAGETYPE_JPEG:
                            $im=imagecreatefromjpeg($dir.$nom_file);
                            imagecopyresized($im2, $im, 0, 0, 0, 0, $infos_img[0]/$r, $infos_img[1]/$r, $infos_img[0], $infos_img[0]);
                            imagedestroy($im);
                            imagejpeg($im2,$dir.$nom_file,$comp_jpg);
                            imagedestroy($im2);
                            break;
                        case IMAGETYPE_PNG:
                            $im=imagecreatefrompng($dir.$nom_file);
                            imagecopyresized($im2, $im, 0, 0, 0, 0, $infos_img[0]/$r, $infos_img[1]/$r, $infos_img[0], $infos_img[0]);
                            imagedestroy($im);
                            imagepng($im2,$dir.$nom_file,9);
                            imagedestroy($im2);
                            break;
                        case IMAGETYPE_GIF:
                            $im=imagecreatefromgif($dir.$nom_file);
                            imagecopyresized($im2, $im, 0, 0, 0, 0, $infos_img[0]/$r, $infos_img[1]/$r, $infos_img[0], $infos_img[0]);
                            imagedestroy($im);
                            imagegif($im2,$dir.$nom_file);
                            imagedestroy($im2);
                            break;
                        }
                    }
                    
                    if( !autoriser('publiergaluse',$_POST['type_objet'],$_POST['id_objet'],$_POST['id_auteur'],) ) $statut="prepa"; else $statut="publie";
                    $dateheure=date('Y-m-d H:i:s'); 
                    $infos_img = getimagesize($dir.$nom_file);
                    $id=sql_insertq("$GLOBALS['table_prefix']_galuse", array(
                        "id_auteur" => $_POST['id_auteur'],
                        "extension" => $ext,
                        "id_vignette" => 0,
                        "titre" => corriger_caracteres($_POST['titre']),
                        "date" => $dateheure,
                        "descriptif" => corriger_caracteres($_POST['description']);,
                        "fichier" => $dir.$nom_file,
                        "taille" => filesize($dir.$nom_file),
                        "largeur" => $infos_img[0],
                        "hauteur" => $infos_img[1],
                        "mode" => "document",
                        "distant" => "non",
                        "statut" => $statut,
                        "date_publication" => $dateheure,
                        "brise" => 0,
                        "credits" => corriger_caracteres($_POST['credits']),
                    ));
                    sql_insertq("$GLOBALS['table_prefix']_galuse", array(
                        "id_image"  => $id,
                        "id_objet"  => $_POST['id_objet'],
                        "objet"     => $_POST['objet'],
                        "vu"        => "non"
                    ));
                    } else {
                    echo '<b>Problème lors de l\'upload !</b><br /><br /><b>',$chemin, '', $_FILES['fichier']['error'], '</b><br /><br />';
                    }
                } else {
                    echo '<b>Problème dans les dimensions ou tailles de l\'image !</b><br /><br />';
                }
                } else {
                echo '<b> Format d\'image non autoris&eactue;</b><br /><br />';
                }
            } else {
                echo '<b>Ceci n\'est pas une image valide</b><br /><br />';
            }
            } else {
            echo '<b>Le champ du formulaire est vide !</b><br /><br />';
            }
        } else {    // fichier distant
            echo '<b>L\'utilisation de fichier du web n\'est pas encore implémentée... désolé...</b><br /><br />';
            if (0){
                    $id=sql_insertq("$GLOBALS['table_prefix']_galuse", array(
                        "id_auteur" => $_POST['id_auteur'],
                        "extension" => $ext,
                        "id_vignette" => 0,
                        "titre" => corriger_caracteres($_POST['titre']),
                        "date" => $dateheure,
                        "descriptif" => corriger_caracteres($_POST['description']);,
                        "fichier" => $dir.$nom_file,
                        "taille" => filesize($dir.$nom_file),
                        "largeur" => $infos_img[0],
                        "hauteur" => $infos_img[1],
                        "mode" => "document",
                        "distant" => "oui",
                        "statut" => $statut,
                        "date_publication" => $dateheure,
                        "brise" => 0,
                        "credits" => corriger_caracteres($_POST['credits']),
                    ));
                    sql_insertq("$GLOBALS['table_prefix']_galuse", array(
                        "id_image"  => $id,
                        "id_objet"  => $_POST['id_objet'],
                        "objet"     => $_POST['objet'],
                        "vu"        => "non"
                    ));
                }
        }
	}else {
		echo '<b>Vous n\'avez pas l\'autorisation d\'ajouter une image ici !</b><br /><br />';
	}
}

?>
