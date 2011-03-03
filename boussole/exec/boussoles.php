<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('boussole_fonctions');

function exec_boussoles_dist()
{
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	$alias = _request('alias');

	pipeline('exec_init',array('args'=>array('exec'=>'depots','id_depot'=>$id_depot),'data'=>''));

	if (!$meta = sql_fetsel("valeur, maj", "spip_meta", "nom=" . sql_quote('boussole_infos_' . $alias))) {
		include_spip('inc/minipres');
		echo minipres(_T('boussole:message_nok_boussole_inconnue', array('alias' => $alias)));
	} 
	else {
		$boussole = unserialize($meta['valeur']);
		$boussole['maj'] = $meta['maj'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		$entete = $commencer_page('&laquo; ' . boussole_traduire($boussole['alias'], 'nom_boussole') . ' &raquo;', 'configuration', 'boussoles');
		$entete .= "<br />\n";
		$entete .= "<br />\n";
		
		// titre
		$entete .= gros_titre(_T('boussole:titre_page_edition_boussole'),'', false);
		
		// barre d'onglets
		$entete .= barre_onglets("boussoles", "");

		$page = debut_gauche('accueil', true)
			.  afficher_boussole($alias, $boussole)
			. "<br /><br /><div class='centered'>"
			. "</div>"
			. fin_gauche();


		echo $entete,
			debut_grand_cadre(true),
			fin_grand_cadre(true),
			$page,
			fin_page();
	}
}


function afficher_boussole($alias, $boussole) {
	global $spip_lang_right, $logo_libelles;

	$nom = boussole_traduire($alias, 'nom_boussole');
	$slogan = boussole_traduire($alias, 'slogan_boussole');
	$descriptif = boussole_traduire($alias, 'descriptif_boussole');
	$url = $boussole['xml'];
	$version = $boussole['version'];
	$nbr_sites = $boussole['nbr_sites'];
	$maj = $boussole['maj'];
	$demo = $boussole['demo'];

	$boite = pipeline ('boite_infos', 
				array('data' => '', 'args' => array('type'=>'boussole', 
													'alias' => $alias, 
													'version' => $version, 
													'nbr_sites' => $nbr_sites,
													'maj' => $maj)));

	$logo = recuperer_fond("prive/logo/boussole", array('alias' => $alias));
	$navigation = debut_boite_info(true). $boite . fin_boite_info(true)
		. $logo
		. pipeline('affiche_gauche',array('args'=>array('exec'=>'boussoles', 'alias' => $alias),'data'=>''));

	$extra = creer_colonne_droite('', true)
		. afficher_autres_boussoles($alias)
		. pipeline('affiche_droite',array('args'=>array('exec'=>'boussoles', 'alias' => $alias),'data'=>''))
		. debut_droite('',true);

	$actions = '';
	$haut = "<div class='bandeau_actions'>$actions</div>" . gros_titre($nom, '' , false);

	$onglet_contenu = afficher_corps_boussole($alias, $slogan, $descriptif, $url, $demo);
	$onglet_proprietes = ((!_INTERFACE_ONGLETS) ? "" :"")
		. pipeline('affiche_milieu',array('args'=>array('exec'=>'boussoles', 'alias' => $alias),'data'=>''));

	$page = $navigation
		. $extra
		. "<div class='fiche_objet'>"
		. $haut
		. afficher_onglets_pages(
		array(
			'voir' => _T('onglet_contenu'),
			'props' => _T('onglet_proprietes')
			),
		array(
			'props' => $onglet_proprietes,
			'voir' => $onglet_contenu)
		)
		. "</div>";
	  
	$page .= recuperer_fond("prive/contenu/boussole_editer", array('alias' => $alias));

	$page .= pipeline('affiche_enfants', array('args'=>array('exec'=>'boussoles', 'alias' => $alias),'data'=>''));

	return $page;
}

function afficher_autres_boussoles($alias) {
	$bloc = recuperer_fond("prive/navigation/boussoles_autres", array('alias' => $alias));
	return $bloc;
}

function afficher_corps_boussole($alias, $slogan, $descriptif, $url, $demo) {
	$corps = '';
	$type = 'boussole';
	$contexte = array('alias' => $alias, 'slogan'=>$slogan, 'descriptif'=>$descriptif, 'url'=>$url, 'demo'=>$demo);
	$fond = recuperer_fond("prive/contenu/$type",$contexte);
	// Permettre a d'autres plugins de faire des modifs ou des ajouts
	$fond = pipeline('afficher_contenu_objet',
					array('args'=>array('type'=>$type, 'contexte'=>$contexte), 'data'=> $fond));
	$corps .= "<div id='wysiwyg'>$fond</div>";

	return $corps;
}

?>
