<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
function balise_FORMULAIRE_PHOTOS($p) {
    return calculer_balise_dynamique($p, 'FORMULAIRE_PHOTOS', array());
	
}

function balise_FORMULAIRE_PHOTOS_dyn() {
return array('formulaires/formulaire_photos', 0, 
		array(
						
		));
}
$alt_photo=corriger_caracteres($_POST['alt_photo']);
$id_auteur=$_POST['id_auteur'];

//Définition des variables

$target     = './plugins/photos/vignettes/';  // Répertoire cible 
$extension  = 'jpg';      // Extension du fichier sans le . 
$max_size   = 300000;     // Taille max en octets du fichier 
$width_max  = 520;        // Largeur max de l'image en pixels 
$height_max = 820;        // Hauteur max de l'image en pixels 


//  Définition des variables liées au fichier IMG


$nom_file   = $_FILES['fichier']['name']; 
$taille     = $_FILES['fichier']['size']; 
$tmp        = $_FILES['fichier']['tmp_name']; 
$chemin= "./plugins/photos/vignettes/";
$ext='.jpg';
$logo='image';
 
if(!empty($_POST['posted'])) { 
       if(!empty($_FILES['fichier']['name'])) { 
                if(substr($nom_file, -3) == $extension) { 
                        $infos_img = getimagesize($_FILES['fichier']['tmp_name']); 
                        if(($infos_img[0] <= $width_max) && ($infos_img[1] <= $height_max) && ($_FILES['fichier']['size'] <= $max_size)) {
			                if(move_uploaded_file($_FILES['fichier']['tmp_name'], $target.$nom_file )) { 
                    
{
$tab= split("[.]",$nom_file);
$nom_file_thumb= $tab[0];
$dateheure=date('Y-m-d H:i:s'); 
spip_query ( "INSERT into spip_photos (nom_photo,nom_vignette,dateheure,id_auteur,alt_photo) VALUES ("._q($nom_file).","._q($nom_file).","._q($dateheure).","._q($id_auteur).","._q($alt_photo)." ) ");

}
					                } else { 
                                       echo '<b>Problème lors de l\'upload !</b><br /><br /><b>',$chemin, '', $_FILES['fichier']['error'], '</b><br /><br />'; 
                } 
            } else { 
              				
                echo '<b>Problème dans les dimensions ou tailles de l\'image !</b><br /><br />'; 
            } 
        } else { 
            
            echo '<b>Votre image ne comporte pas l\'extension .jpg !</b><br /><br />'; 
        } 
    } else { 
               echo '<b>Le champ du formulaire est vide !</b><br /><br />'; 
    } 
} 

?>
