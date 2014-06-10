<?php
/**
 * Plugin Credits en filigrane
 * Licence GPL3 (c) 2014 tofulm cy_altern
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * réduire les images dès leur chargement
 *
 */
function img_max_post_edition($flux) {
    // config: la taille max en pixels
    $taille_max = lire_config('img_max/img_max');
    // ne déclencher que sur les docs
    if ($flux['args']['table'] != 'spip_documents'
        OR !isset($flux['args']['id_objet'])
        OR (intval($flux['args']['id_objet']) != $flux['args']['id_objet']))
        return;

    // recup les infos de l'image en particulier son nom de fichier
    $id_doc = $flux['args']['id_objet'];
    $res = sql_fetsel("*", "spip_documents", "id_document=$id_doc");
    if (!in_array($res['extension'], array('jpg', 'gif', 'png')))
        return;

    $fichier = $res['fichier'];
    $fichier = _DIR_IMG.$fichier;
//    echo "<br>fichier";
//    echo $fichier;

    // le petit nécessaire pour bidouiller les images
    include_spip('filtres/images_transforme');

    $fic_res = extraire_attribut(image_reduire($fichier, $taille_max, $taille_max), 'src');
    $Tfic = explode('?', $fic_res);
    $fic_res = $Tfic[0];
//    echo "<br>fic_res";
//    echo $fic_res;
    if (@file_exists($fic_res)) {
        @spip_unlink($fichier); // necessaire avant le rename si OS windows
        if (!@rename($fic_res, $fichier))
            spip_log("Erreur de renommage de $fic_res vers $fichier", 'img_max');
    }

	$infos = renseigner_taille_dimension_image($fichier,$res['extension']);
	sql_updateq('spip_documents', array('largeur'=>$infos['largeur'], 'hauteur'=>$infos['hauteur'],'taille'=>$infos['taille']),'id_document='.intval($id_doc));
	
    return $flux;
}

?>
