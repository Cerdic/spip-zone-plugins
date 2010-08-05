<?php

function amap_ajouterOnglet($flux) {
	if($flux['args']=='amap')
	{
		$flux['data']['configuration']= new Bouton(null, _T('amap:configuration'),
											  generer_url_ecrire("amap_config", "table=spip_amap_saison"));
    	$flux['data']['annuaire']= new Bouton(null, _T('amap:annuaire'),
											  generer_url_ecrire("amap_annuaire", "table=spip_amap_personne"));
		$flux['data']['distributions']= new Bouton(null, _T('amap:distributions'),
											  generer_url_ecrire("amap_distributions", "table=spip_amap_evenements"));
    	$flux['data']['contrats']= new Bouton(null, _T('amap:contrats'),
											  generer_url_ecrire("amap_contrats", "table=spip_amap_contrat"));
    	$flux['data']['paniers']= new Bouton(null, _T('amap:paniers'),
											  generer_url_ecrire("amap_paniers", "table=spip_amap_panier"));
	}
	return $flux;
}

?>
