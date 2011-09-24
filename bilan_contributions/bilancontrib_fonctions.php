<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * fonction pour afficher une icone 12px selon le statut de l'auteur
 *
 * @param string $statut
 * @return string
 */
function bilancontrib_icone_auteur($statut,$webmestre,$id,$nom){

	if ($statut=='0minirezo') {
		if($webmestre == 'oui') {
			$src = find_in_path("images/webmestre-12.gif") ;
			$alt = _T('bilancontrib:info_webmestre') ;
		} else {
			$src = find_in_path("images/admin-12.gif") ;
			$alt = _T('info_administrateur') ;
		}
	} elseif ($statut=='1comite') {
		$src = find_in_path("images/redac-12.gif") ;
		$alt = _T('info_redacteur_1') ;
	} elseif ($statut=='6forum') {
		$src = find_in_path("images/visit-12.gif") ;
		$alt = _T('info_visiteur_1') ;
	} elseif ($statut=='5poubelle') {
		$src = find_in_path("images/poubelle-12.gif") ;
		$alt = _T('texte_statut_poubelle') ;
	} else {
		$src = find_in_path("images/inconnu-12.gif") ;
		$alt = _T('erreur') ;
	}

	$balise_img = chercher_filtre('balise_img');
	return '<a href="?exec=auteur_infos&amp;id_auteur='.$id.'" title="'.$alt.'">'.$balise_img($src,$alt).'&nbsp;'.$nom.'</a>' ;
}
?>