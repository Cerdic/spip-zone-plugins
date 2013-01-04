<?php
/**
 * Plugin Credits en filigrane
 * Licence GPL3 (c) 2012 cy_altern
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * appliquer un filigrane sur une image lorsqu'elle a le champ credits non-vide
 * !!! NECESSITE la lib GD2 pour le traitement des images dans la conf SPIP avancée !!!
 * les fichiers texte-du-filigrane_000000.png ou texte-du-filigrane_ffffff.png sont stockés dans le dossier IMG/filigranes
 * parametres des fichiers png utilises comme masque: cf http://www.paris-beyrouth.org/tutoriaux-spip/article/un-site-dument-timbre
 * les images originales sont déplacées dans IMG_sans_filigrane/ext
 * la copie avec filigrane remplace l'originale de façon transparente pour l'utilisateur
 *
 */
function c2f_post_edition($flux) {
	// config: la taille du texte du credit
	// en proportion de la hauteur de l'image: par ex 20 => taille texte = hteur/20
	$ratio_hauteur_texte = 20;
	
	if ($flux['args']['table'] != 'spip_documents'
		OR !isset($flux['args']['id_objet'])
		OR (intval($flux['args']['id_objet']) != $flux['args']['id_objet'])
		OR !isset($flux['data']['credits'])
		OR $flux['data']['credits']=='')
		return;

	// recup les infos de l'image en particulier son nom de fichier
	$id_doc = $flux['args']['id_objet'];
	$res = sql_fetsel("*", "spip_documents", "id_document=$id_doc");
	if (!in_array($res['extension'], array('jpg', 'gif', 'png')))
		return;
	$fichier = $res['fichier'];
	
	if (isset($res['hauteur']) AND $res['hauteur'] > 0)
		$hteur_txt = round($res['hauteur'] / $ratio_hauteur_texte);
	else
		$hteur_txt = 20;

	// si l'image existe dans IMG_sans_filigrane c'est cette version qu'il faut utiliser: ecraser l'ancienne image filigranée
	// sinon c'est que celle de IMG n'est pas filigranée donc on l'utilise après l'avoir copiée dans IMG_sans_filigrane
	$rep_sans_filigrane = str_replace('IMG', 'IMG_sans_filigrane', _DIR_IMG);
	if (!is_dir($rep_sans_filigrane)) {
		sous_repertoire($rep_sans_filigrane,'',false,true);
		foreach(array('jpg','gif','png') as $f)
			sous_repertoire($rep_sans_filigrane.'/'.$f,'',false,true);
	}
	
	if (@file_exists($rep_sans_filigrane.$fichier))
		@copy($rep_sans_filigrane.$fichier, _DIR_IMG.$fichier);
	else 
		@copy(_DIR_IMG.$fichier, $rep_sans_filigrane.$fichier);
	$fichier = _DIR_IMG.$fichier;

	// pour restaurer l'image sans filigrane: mettre 0 dans le champ credits
	$credits = $flux['data']['credits'];
	if ($credits == '0')
		return;

	// le petit nécessaire pour générer le masque et l'appliquer
	include_spip('filtres/couleurs');
	include_spip('filtres/images_transforme');
	include_spip('filtres/images_typo');
	include_spip('filtres/charsets');

	// faut il un masque avec filigrane noir ou blanc? (masque bla_000000.png ou bla_ffffff.png)
	$coul_txt = couleur_inverser( couleur_extreme( couleur_extraire($fichier, 19,19)));

	// si elle n'existe pas déja, générer l'image typo du filigrane et la stocker dans le dossier IMG/filigranes
	$credits = $flux['data']['credits'];
	$nomfic_credits = substr(str_replace(' ', '_', translitteration($credits)), 0, 100);
	$masque = 'filigranes/'.$nomfic_credits.'_'.$coul_txt.'_'.$hteur_txt.'px.png';
	
	if (!find_in_path($masque)){
		$img_typo = produire_image_typo('© '.$credits, 'taille='.$hteur_txt, 'couleur='.$coul_txt, 'padding_horizontal=10', 'padding_vertical=5');
		$img_typo = extraire_attribut(image_aplatir($img_typo, 'png', '808080',128,0), 'src');

		if (!is_dir(_DIR_IMG.'filigranes')){
			include_spip('inc/getdocument');
			creer_repertoire_documents('filigranes');
		}
		@rename($img_typo, _DIR_IMG.$masque);
		$masque = _DIR_IMG.$masque;
	}
spip_log('fip masque: '.find_in_path($masque), 'c2f');

	// appliquer le filigrane et generer un fichier jpg ou gif a partir du PNG obtenu
	switch ($res['extension']) {
		case 'jpg':
			$fic_res = extraire_attribut(image_aplatir(image_masque($fichier, $masque, "bottom=0", "right=0"),'jpg','ffffff',128,1), 'src');
		break;
		case 'gif':
			$fic_res = extraire_attribut(image_aplatir(image_masque($fichier, $masque, "bottom=0", "right=0"),'gif','ffffff',128,1), 'src');
		break;
		case 'png':
			$fic_res = extraire_attribut(image_masque($fichier, $masque, "bottom=0", "right=0"), 'src');
		break;
	}

	if (@file_exists($fic_res)) {
		@spip_unlink($fichier);	// necessaire avant le rename si OS windows
		@rename($fic_res, $fichier);
	}
	
	return $flux;
}

?>
