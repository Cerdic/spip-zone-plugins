<?php

function cs_derniers_connectes($fetch){ 
	$query = spip_query("SELECT nom,statut,email,en_ligne FROM spip_auteurs ORDER BY en_ligne DESC LIMIT 10"); 
	$res = array(); 
    while ($row = $fetch($query)) $res[]=_T('cout:stats_auteur', array(
		'icon' => '<a href="'.generer_url_ecrire("auteurs","statut=" . $row['statut']).'">' . bonhomme_statut($row) . '</a>',
		'nom' => cs_lien($row['email'], $row['nom']),
		'date' => $row['en_ligne']
	));
	if(!count($res)) $res[] = _T('cout:variable_vide');
	return '<p>'.join('<br/>', $res).'</p>';
} 

function cs_non_confirmes($fetch){ 
	$query = spip_query('SELECT nom,email,maj FROM spip_auteurs WHERE statut=\'nouveau\' ORDER BY maj DESC'); 
	$res = array(); 
    while ($row = $fetch($query)) $res[]=_T('cout:stats_auteur', array(
		'icon' => http_img_pack("aide.gif", '', '', _T('cout:attente_confirmation')),
		'nom' => cs_lien($row['email'], $row['nom']),
		'date' => $row['maj']
	)); 
	if(!count($res)) $res[] = _T('cout:variable_vide');
	return '<p>'.join('<br/>', $res).'</p>';
} 

function stats_rapides_affiche_droite($flux) {
	if (cout_autoriser() && in_array($flux['args']['exec'],array('auteurs', 'auteur_infos'))) {
		// compatibilite SPIP 1.92
		$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
		// pour cs_lien()
		include_spip('cout_fonctions');
		$flux['data'] .= debut_cadre_relief(find_in_path('img/couteau-24.gif'),true,'',_T('icone_statistiques_visites'))
			. "<p><b>"._T('cout:derniers_connectes')."</b></p>"
			. cs_derniers_connectes($fetch)
			. "<p><b>"._T('cout:non_confirmes')."</b></p>"
			. cs_non_confirmes($fetch)
			. fin_cadre_relief(true);
	}
	return $flux;
}

?>