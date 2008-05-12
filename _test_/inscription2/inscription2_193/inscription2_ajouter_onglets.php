<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;
function Inscription2_ajouter_boutons($boutons_admin){
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

		$boutons_admin['auteurs']->sousmenu['inscription2_adherents']= new Bouton(
		"../"._DIR_PLUGIN_INSCRIPTION2."images/inscription2_icone.png", // icone
		_T("inscription2:adherents") //titre
		);
	}
	return $boutons_admin;
}

function Inscription2_affiche_milieu($flux){
	switch($flux['args']['exec']) {	
			case 'auteur_infos':
				include_spip('inc/inscription2_gestion');
				$id_auteur = $flux['args']['id_auteur'];
				$flux['data'] .= inscription2_ajouts($id_auteur);
				break;
			case 'auteurs_edit':
				include_spip('inc/inscription2_fiche_adherent');
				$id_auteur = $flux['args']['id_auteur'];
				$flux['data'] .= inscription2_fiche_adherent($id_auteur);
				break;
			default:
				break;
		}
	return $flux;
}

function Inscription2_header_prive($flux){
	if (_request('exec')=='ajouter_adherent'){
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_INSCRIPTION2."css/inscription2_forms.css' type='text/css' media='all' />\n";
		$flux .= "<link rel='stylesheet' href='".direction_css(_DIR_PLUGIN_INSCRIPTION2."css/inscription2.css")."' type='text/css' media='all' />\n";
		$flux .= "<script type='text/javascript' src='".find_in_path('lib/jquery-validate/jquery.validate.pack.js')."'></script>\n";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_INSCRIPTION2."javascript/md5_inscription2.js'></script>\n";
	}
	return $flux;
}

function inscription2_affichage_final($page){
	// regarder si la page contient le formulaire inscription2
	if (!strpos($page, 'id="inscription"'))
		return $page;
	$page = inscription2_preparer_page($page);
		return $page;
}

function inscription2_preparer_page($page) {

	$css = find_in_path('css/inscription2_forms.css');
	$jqueryvalidate = find_in_path('lib/jquery-validate/jquery.validate.pack.js');

	$incHead = <<<EOS
<script type='text/javascript' src='$jqueryvalidate'></script>
<link rel="stylesheet" href="$css" type="text/css" media="all" />
EOS;
	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}
				
?>
