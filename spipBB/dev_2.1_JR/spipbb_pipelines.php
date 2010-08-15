<?php


#
# bouton interface spip
#
function spipbb_ajouter_boutons($boutons_admin) {
	// si on est admin ou admin restreint
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		// on voit le bouton dans la barre "statistiques"
		$boutons_admin['forum']->sousmenu["spipbb_admin"]= new Bouton(
		"../"._DIR_PLUGIN_SPIPBB."img_pack/spipbb-24.png",  // icone
		_T('spipbb:titre_spipbb')	// titre
		);
	}
	return $boutons_admin;
}

#
# js + css prive
#
function spipbb_header_prive($flux) {
	$exec = _request('exec');
	if(strpos($exec, '^(spipbb_).*')!==false) { 
	$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_SPIPBB.'css/spipbb_styles.css" />'."\n";
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_SPIPBB.'javascript/spipbb_vueavatar.js"></script>'."\n";
	}
	if($exec=="spipbb_formpost") {
	$flux.='<script type="text/javascript" src="'._DIR_PLUGIN_SPIPBB.'javascript/spipbb_js_formpost.js"></script>'."\n";
	}
	return $flux;
}

#
# bouton interface spip col. droite sur exec/naviguer (rubrique)
#
function spipbb_affiche_droite($flux)
{
	// [fr] Peut etre ajouter un controle d acces
	// [en] Todo : maybe add access control

	if ( ($flux['args']['exec']=='naviguer') AND (!empty($flux['args']['id_rubrique'])) )
	{ // AND (!empty($GLOBALS['meta']['spipbb']))
		include_spip('inc/spipbb_util'); // pour spipbb_is_configured
		$r = sql_fetsel("id_secteur", "spip_rubriques", "id_rubrique=".$flux['args']['id_rubrique']);
		$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
		if ( !spipbb_is_configured()
			OR ($GLOBALS['spipbb']['configure']!='oui')
			OR (empty($GLOBALS['spipbb']['id_secteur'])) ) {
		// [fr] configuration pas terminee -> lien vers la config
			$url_lien = generer_url_ecrire('spipbb_configuration',"") ;
			$flux['data'] .= debut_cadre_relief('',true);
			$flux['data'] .= "<div style='font-size: x-small' class='verdana1'><b>" ;
			$flux['data'] .= _T('spipbb:admin_titre') . " :</b>\n";
			$flux['data'] .= "<table class='cellule-h-table' cellpadding='0' style='vertical-align: middle'>\n" ;
			$flux['data'] .= "<tr><td><a href='$url_lien' class='cellule-h'><span class='cell-i'>" ;
			$flux['data'] .= "<img src='"._DIR_PLUGIN_SPIPBB ."img_pack/spipbb-24.png' width='24' alt='";
			$flux['data'] .= _T('spipbb:admin_titre') . "' /></span></a></td>\n" ;
			$flux['data'] .= "<td class='cellule-h-lien'><a href='$url_lien' class='cellule-h'>" ;
			$flux['data'] .= _T('spipbb:config_spipbb') . "</a></td></tr></table>\n</div>\n" ;
			$flux['data'] .= fin_cadre_relief(true);
		} elseif (is_array($r) AND ($r['id_secteur']!=$GLOBALS['meta']['spipbb']['id_secteur'])) {
		// [fr] configuration Ok et on est dans la rubrique forum
			$url_lien = generer_url_ecrire('spipbb_admin',"") ;
			$flux['data'] .= debut_cadre_relief('',true);
			$flux['data'] .= "<div style='font-size: x-small' class='verdana1'><b>" . _T('spipbb:admin_titre') . " :</b>\n";
			$flux['data'] .= "<table class='cellule-h-table' cellpadding='0' style='vertical-align: middle'>\n" ;
			$flux['data'] .= "<tr><td><a href='$url_lien' class='cellule-h'><span class='cell-i'>" ;
			$flux['data'] .= "<img src='"._DIR_PLUGIN_SPIPBB ."img_pack/spipbb-24.png' width='24' alt='";
			$flux['data'] .= _T('spipbb:admin_surtitre') . "' /></span></a></td>\n" ;
			$flux['data'] .= "<td class='cellule-h-lien'><a href='$url_lien' class='cellule-h'>" ;
			$flux['data'] .= _T('spipbb:admin_sous_titre') . "</a></td></tr></table>\n</div>\n" ;
			$flux['data'] .= fin_cadre_relief(true);
		}
	}
	return $flux;
}

#
# affiche formulaire sur page exec_auteur_infos
function spipbb_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='auteur_infos'){
		$id_auteur = $flux['args']['id_auteur'];
		// c 7/12/8 plus d'extras donc... pour modifier il faut le plugin extras !!
		if(lire_config("spipbb/support_auteurs")=="table") 
		{
			include_spip('inc/spipbb_auteur_infos');
			$flux['data'].= spipbb_auteur_infos($id_auteur);
		}
	}
	return $flux;
}

#
# ch. traiter visite-forum en cron
#
function spipbb_taches_generales_cron($taches_generales){
	if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
	$taches_generales['statvisites'] = _SPIPBB_DELAIS_CRON ;
	return $taches_generales;
} // spipbb_taches_generales_cron

#
# Onglet dans la page de configuration
#
function spipbb_ajouter_onglets($flux){
	// si on est admin...
	if ($flux['args']=='configuration' && spipbb_autoriser())
		$flux['data']['spipbb']= new Bouton(find_in_path('img_pack/spipbb-24.png'), _T('spipbb:titre_spipbb'), generer_url_ecrire('spipbb_configuration'));
	return $flux;
}


// [Backick] Définir le squelette a utiliser si on est dans le cas d'une rubrique de spipBB 
function spipbb_styliser($flux){
	

	// si article ou rubrique
	if (($fond = $flux['args']['fond'])
	AND in_array($fond, array('article','rubrique'))) {
		
		$ext = $flux['args']['ext'];
		
		if ($id_rubrique = $flux['args']['id_rubrique']) {
			// calcul du secteur
			$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . intval($id_rubrique));
			
			// je retrouve le secteur de spipBB grâce à CFG
			$spipbb_id_secteur =  lire_config('spipbb/id_secteur');
			// comparaison du secteur avec la config de spipBB
			if ($id_secteur==$spipbb_id_secteur) {
				// si un squelette $fond_spipbb existe
                if ($squelette = test_squelette_spipbb($fond, $ext)) {
                    $flux['data'] = $squelette;
                }
			}

		}
	}
	return $flux;
}

function test_squelette_spipbb($fond, $ext) {
    if ($squelette = find_in_path($fond."_spipbb.$ext")) {
        return substr($squelette, 0, -strlen(".$ext"));
    }
    return false;
}

