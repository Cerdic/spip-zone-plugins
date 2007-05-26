<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & François de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/
//---------------------------- 
//  DEBUT MODIF DES MEMBRES 
//---------------------------- 

include_spip('inc/presentation');

function exec_edit_adherent(){
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

// LES URL'S
	$url_upload=generer_url_ecrire('upload');
	$url_asso = generer_url_ecrire('association');
	$url_action_adherents=generer_url_ecrire('action_adherents');
	$url_retour = $_SERVER['HTTP_REFERER'];

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_liste_actifs'));
	debut_boite_info();
	
//LE MENU
	print  association_date_du_jour();

//---------------------------- 
//  ICI ON MODIFIE UN MEMBRE 
//---------------------------- 	
$id_adherent = $_GET['id'];

$query = spip_query( "SELECT * FROM spip_asso_adherents where id_adherent='$id_adherent' " );
	
echo '<fieldset><legend>'._T('asso:adherent_titre_modifier_membre').'</legend>';
echo '<table width="70%">';	
echo '<form action="'.$url_action_adherents.'" method="post">';	

while($data = spip_fetch_array($query)) 
{
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_reference_interne').' :</td>';
echo '<td><input name="id_asso" type="text" value="'.$data['id_asso'].'"></td>';
echo '<td>'._T('asso:adherent_libelle_numero').' :</td>';
echo '<td><input name="id_adherent" type="text" size="3" readonly="true" value="'.$data['id_adherent'].'"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_nom').' :</td>';
echo '<td><input name="nom" type="text" size="40" value="'.$data['nom'].'"></td>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_prenom').' :</td>';
echo '<td><input name="prenom" type="text" size="40" value="'.$data['prenom'].'"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_sexe').' :</td>';
echo '<td><input name="sexe" type="radio" value="H" ';
if ($data['sexe']=="H") {echo ' checked="checked"';}
echo '> '._T('asso:adherent_libelle_homme').' ';
echo '<input name="sexe" type="radio" value="F" ';
if ($data['sexe']=="F") {echo ' checked="checked"';}
echo '> '._T('asso:adherent_libelle_femme').' ';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_date_naissance').' :</td>';
echo '<td><input name="naissance" type="text" value="'.$data['naissance'].'"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_categorie').' :</td>';
echo '<td><select name="categorie" type="text" >';
$sql = spip_query( "SELECT * FROM spip_asso_categories" );
while($categorie = spip_fetch_array($sql)) 
{
echo '<option value="'.$categorie["valeur"].'"';
	if ($data["categorie"]==$categorie["valeur"]) { echo ' selected="selected"'; }
	echo '> '.$categorie["libelle"].'</option>';
}
echo '</select>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_fonction').' :</td>';
echo '<td><input name="fonction" type="text" size="40" value="'.$data['fonction'].'"></td>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_email').' :</td>';
echo '<td colspan="3"><input name="email" type="text" size="40" value="'.$data['email'].'"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_rue').' :</td>';
echo '<td><textarea  name="rue" cols="30">'.$data['rue'].'</textarea></td></tr>';
//echo '<td>'._T('asso:adherent_libelle_num_rue').' :</td>';
//echo '<td><input name="numero" type="text" size="10" value="'.$data['numero'].'"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_ville').' :</td>';
echo '<td><input name="ville" type="text" size="40" value="'.$data['ville'].'"></td>';
echo '<td>'._T('asso:adherent_libelle_codepostal').' :</td>';
echo '<td><input name="cp" type="text" value="'.$data['cp'].'"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_portable').' :</td>';
echo '<td><input name="portable" type="text" value="'.$data["portable"].'"></td>';
echo '<td>'._T('asso:adherent_libelle_telephone').' :</td>';
echo '<td><input name="telephone" type="text" value="'.$data["telephone"].'"></td></tr>';
echo '<tr> ';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_profession').' :</td>';
echo '<td><input name="profession" type="text"  size= "40" value="'.$data["profession"].'"></td>';
echo '<tr> ';	
echo '<td>'._T('asso:adherent_libelle_societe').' :</td>';
echo '<td><input name="societe" type="text" size= "40" value="'.$data["societe"].'"></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';	
echo '<td>'._T('asso:adherent_libelle_secteur').' :</td>';
echo '<td><input name="secteur" type="text" value="'.$data["secteur"].'"></td>';
echo '<td>'._T('asso:adherent_libelle_accord').' :</td>';
echo '<td><input name="publication" type="radio" value="oui"';
if ($data['publication']=="oui") {echo ' checked="checked"';}
echo '>'._T('asso:adherent_libelle_oui');
echo '<input name="publication" type="radio" value="non"';
if ($data['publication']=="non") {echo ' checked="checked"';}
echo '>'._T('asso:adherent_libelle_non').'</td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_utilisateur1').' :</td>';
echo '<td><input name="utilisateur1" type="text" value="'.$data["utilisateur1"].'"></td>';
echo '<td>'._T('asso:adherent_libelle_utilisateur2').' :</td>';
echo '<td><input name="utilisateur2" type="text" value="'.$data["utilisateur2"].'"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_utilisateur3').' :</td>';
echo '<td><input name="utilisateur3" type="text" value="'.$data["utilisateur3"].'"></td>';
echo '<td>'._T('asso:adherent_libelle_utilisateur4').' :</td>';
echo '<td><input name="utilisateur4" type="text" value="'.$data["utilisateur4"].'"></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr> ';	
echo '<td>'._T('asso:adherent_libelle_statut').' :</td>';
echo '<td><input name="statut" type="radio" name="statut" value="ok"';
if ($data['statut']=="ok") {echo ' checked="checked"';}
echo '> '._T('asso:adherent_libelle_statut_ok');
echo '<input name="statut" type="radio" name="statut" value="echu"';
if ($data['statut']=="echu") {echo ' checked="checked"';}
echo '> '._T('asso:adherent_libelle_statut_echu');
echo '<input name="statut" type="radio" name="statut" value="relance"';
if ($data['statut']=="relance") {echo ' checked="checked"';}
echo '> '._T('asso:adherent_libelle_statut_relance');
echo '<input name="statut" type="radio" name="statut" value="sorti"';
if ($data['statut']=="sorti") {echo ' checked="checked"';}
echo '> '._T('asso:adherent_libelle_statut_sorti');
echo '<input name="statut" type="radio" name="statut" value="prospect"';
if ($data['statut']=="prospect") {echo ' checked="checked"';}
echo '> '._T('asso:adherent_libelle_statut_prospect'). '</td>';
echo '<td>'._T('asso:adherent_libelle_validite').' :</td>';
echo '<td><input name="validite" type="text" value="'.$data["validite"].'"></td>';
echo '</tr>';
echo '<tr> ';      
echo '<td>'._T('asso:adherent_libelle_remarques').' :</td>';
echo '<td colspan="3"><textarea name="remarques" cols="65" rows="3">'.$data["remarques"].'</textarea>';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input type="hidden" name="action" value="modifie"></td></tr>';
}
echo '<tr>';
echo '<td></td>';
echo '<td><input name="submit" type="submit" value="'._T('asso:adherent_bouton_modifier').'" class="fondo"></td></tr>';
echo '</form>';
echo '</table>';
echo '</fieldset>';

/*
//---------------------------- 
//  DEFINITION DES VARIABLES  
//---------------------------- 

$target     = '../IMG/';  // Repertoire cible 
$extension  = 'jpg';      // Extension du fichier sans le . 
$max_size   = 100000;     // Taille max en octets du fichier 
$width_max  = 150;        // Largeur max de l'image en pixels 
$height_max = 150;        // Hauteur max de l'image en pixels 

//--------------------------------------------- 
//  DEFINITION DES VARIABLES LIEES AU FICHIER 
//--------------------------------------------- 

$nom_file   = $_FILES['fichier']['name']; 
$taille     = $_FILES['fichier']['size']; 
$tmp        = $_FILES['fichier']['tmp_name']; 
$chemin= "../IMG/";
$ext='.jpg';
$logo= "assologo";

//---------------------- 
//  SCRIPT D'UPLOAD 
//---------------------- 


//
// TODO FIXME : Si ce script est rÃ©activÃ©, il faudra le passer en multilingue.
if(!empty($_POST['posted'])) { 
    // On vérifie si le champ est rempli 
    if(!empty($_FILES['fichier']['name'])) { 
        // On vérifie l'extension du fichier 
        if(substr($nom_file, -3) == $extension) { 
            // On récupère les dimensions du fichier 
            $infos_img = getimagesize($_FILES['fichier']['tmp_name']); 
         $rep1=$_POST['rep1'];

            // On vérifie les dimensions et taille de l'image 
            if(($infos_img[0] <= $width_max) && ($infos_img[1] <= $height_max) && ($_FILES['fichier']['size'] <= $max_size)) {
			
 
                // Si c'est OK, on teste l'upload 
              if(move_uploaded_file($_FILES['fichier']['tmp_name'], $target.$logo.$id_adherent.$ext )) { 
                    // Si upload OK alors on affiche le message de réussite 
                    echo '<b>Image upload&eacute;e avec succ&egrave;s !</b>'; 
                    echo '<hr />'; 
                    echo '<b>Fichier :</b> ', $_FILES['fichier']['name'], '<br />'; 
                    echo '<b>Taille :</b> ', $_FILES['fichier']['size'], ' Octets<br />'; 
                    echo '<b>Largeur :</b> ', $infos_img[0], ' px<br />'; 
                    echo '<b>Hauteur :</b> ', $infos_img[1], ' px<br />';
					echo '<b>le chemin de votre image est :</b>',$chemin, '', $logo.$id_adherent.$ext, '<br /><br />'; 
                    
					echo '<hr />'; 
                    echo '<br /><br />';
					$nom_image= $_FILES['fichier']['name']; 
					$destination=$target.$logo.$id_adherent.$ext;
					
					 $infos_img[0] /=2;
					$infos_img[1] /=2;
					$vignette="<img src=\"$destination\" heigth='$infos_img[0]' width='$infos_img[0]'/>";
					$vignette1='<img src="/IMG/assologo'.$id_adherent.'">';
					$sql="UPDATE spip_asso_adherents SET vignette='$vignette1' WHERE id_adherent=$id_adherent";
					$req = spip_query($sql) ;

					echo $vignette;
               }
				 else { 
                    // Sinon on affiche une erreur système 
                    echo '<b>Probl&egrave;me lors de l\'upload !</b><br /><br /><b>',$chemin, '', $_FILES['fichier']['error'], '</b><br /><br />'; 
                } 
            } else { 
                // Sinon on affiche une erreur pour les dimensions et taille de l'image 
				
                echo '<b>Probl&egrave;me dans les dimensions ou taille de l\'image !</b><br /><br />'; 
            } 
        } else { 
            // Sinon on affiche une erreur pour l'extension 
            echo '<b>Votre image ne comporte pas l\'extension .jpg !</b><br /><br />'; 
        } 
    } else { 
        // Sinon on affiche une erreur pour le champ vide 
        echo '<b>Le champ du formulaire est vide !</b><br /><br />'; 
    } 
} 
echo'<form enctype="multipart/form-data" action="'.$PHP_SELF.'" method="POST">';
echo'<br />';
echo'<input type="hidden" name="posted" value="1" /> <input type="hidden" name="rep1" value="'.$rep1.'" />';
echo' <fieldset>';
echo'<div align="center">';
echo'<legend>Logo en .jpg (taille maxi 150px X 150px)</legend>';
echo'<p><label for="photo">Photo :</label><input name="fichier" type="file" />';
echo'<input type="submit" value="Envoyer" class="fondo"></legend>';
echo'</div>';
echo'</fieldset>';
echo'</form> ';
*/

// ON FERME TOUT
	fin_boite_info();

	fin_cadre_relief();

	fin_page();
}
?>

