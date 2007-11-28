<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_attribuer_logo_categorie(){
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['lang'] = _request('lang_categorie');
	
	$type_file = $_FILES['logo_categorie']['type'];
	
	$content_dir = 'IMG/'; // dossier où sera déplacé le fichier

    $tmp_file = $_FILES['logo_categorie']['tmp_name'];

    if( !is_uploaded_file($tmp_file) )
    {
        die(_T('echoppe:fichier_introuvable'));
    }

    // on vérifie maintenant l'extension
    if( !strstr($type_file, 'png') && !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') )
    {
        die(_T('echoppe:fichier_pas_une_image'));
    }

    // on copie le fichier dans le dossier de destination
    $name_file = 'cat'.$contexte['id_categorie'].'_'.$contexte['lang_categorie'].strrchr($_FILES['logo_categorie']['name'], '.');
	unlink($content_dir . $name_file);
    if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
    {
        die(_T('echoppe:inmpossible_copier_dans').$content_dir);
    }

    $sql_maj_logo_categorie = "UPDATE spip_echoppe_categories_descriptions SET logo = '".$name_file."' WHERE id_categorie = '".$contexte['id_categorie']."' AND lang = '".$contexte['lang']."';";
    $res_maj_logo_categorie = spip_query($sql_maj_logo_categorie);
    //die($sql_maj_logo_categorie);
    
    $redirect = generer_url_ecrire('echoppe_edit_categorie', 'id_categorie='.$contexte['id_categorie'].'&lang_categorie='.$contexte['lang_categorie'],'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>
