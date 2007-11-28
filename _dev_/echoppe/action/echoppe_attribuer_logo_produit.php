<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_attribuer_logo_produit(){
	$contexte['id_produit'] = _request('id_produit');
	$contexte['lang'] = _request('lang_produit');
	
	$type_file = $_FILES['logo_produit']['type'];
	
	$content_dir = 'IMG/'; // dossier où sera déplacé le fichier

    $tmp_file = $_FILES['logo_produit']['tmp_name'];

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
    $name_file = 'prod'.$contexte['id_produit'].'_'.$contexte['lang_produit'].strrchr($_FILES['logo_produit']['name'], '.');
	unlink($content_dir . $name_file);
    if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
    {
        die(_T('echoppe:inmpossible_copier_dans').$content_dir);
    }

    $sql_maj_logo_produit = "UPDATE spip_echoppe_produits_descriptions SET logo = '".$name_file."' WHERE id_produit = '".$contexte['id_produit']."' AND lang = '".$contexte['lang']."';";
    $res_maj_logo_produit = spip_query($sql_maj_logo_produit);
    //die($sql_maj_logo_produit);
    
    $redirect = generer_url_ecrire('echoppe_edit_produit', 'id_produit='.$contexte['id_produit'].'&lang_produit='.$contexte['lang_produit'],'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>
