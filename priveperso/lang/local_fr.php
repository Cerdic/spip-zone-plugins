<?php
//redefinir les nouvelles chaines de langue:
//articles
$GLOBALS[$GLOBALS['idx_lang']]['info_titre_url'] = _T('info_titre');
//rubriques
$GLOBALS[$GLOBALS['idx_lang']]['info_titre_rubriques'] = _T('info_titre');
$GLOBALS[$GLOBALS['idx_lang']]['texte_descriptif_rapide_rubriques'] = _T('texte_descriptif_rapide');
//breves
$GLOBALS[$GLOBALS['idx_lang']]['info_titre_breves'] = _T('info_titre');
$GLOBALS[$GLOBALS['idx_lang']]['info_titre_url_breves'] = _T('info_titre');
$GLOBALS[$GLOBALS['idx_lang']]['entree_liens_sites_breves'] = _T('entree_liens_sites');
$GLOBALS[$GLOBALS['idx_lang']]['info_url_breves'] = _T('info_url');

include_spip('inc/inscrire_priveperso');


$id_rubrique = priveperso_recupere_id_rubrique();

if ($id_rubrique!==NULL){
// On vérifie si la rubrique en cours ou une des rubriques parentes est personnalisée
	if (!priveperso_rubrique_deja_perso($id_rubrique)){
		$id_rub = priveperso_trouver_rubrique_parent_perso($id_rubrique);
		if (($id_rub!==NULL) && ($id_rub!=='0')) $id_rubrique = $id_rub;
	}

	if (priveperso_rubrique_deja_perso($id_rubrique)){
		$priveperso_texte =priveperso_texte_recuperer_valeurs($id_rubrique);
		foreach($priveperso_texte as $j => $w) {
			if (($priveperso_texte[$j]!==$id_rubrique) && ($priveperso_texte[$j]!==NULL))
			$GLOBALS[$GLOBALS['idx_lang']][$j] = $priveperso_texte[$j];
		}
	}
}   
?>