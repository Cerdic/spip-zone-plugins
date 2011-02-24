<?php
/**
 * Balise TIPAFRIEND dynamique
 *
 * En utilisation 'classique', la balise ne prend qu'un seul argument indiquant :
 * - soit le squelette à choisir pour générer le bouton "Envoyer cette page ..." ; ce squelette doit
 * être trouvé dans le répertoire 'modeles/'
 * <pre>Exemple:
 * #TIPAFRIEND{mon_squelette}</pre>
 * - soit l'info 'mini' qui indique à la balise qu'elle ne doit afficher que l'image (sans le texte
 * "Envoyer cette page ...")
 * <pre>Exemple:
 * #TIPAFRIEND{mini}</pre>
 *
 * Comme pour l'ensemble du plugin, si le squelette n'est pas trouvé, le squelette par défaut sera
 * chargé à la place.
 *
 * Arguments complets possibles (cf. 'tipafriend_options.php') :
 * => 1: type de squelette (cf. ci-dessus)
 * => 2: URL
 * => 3: email expediteur
 * => 4: nom expediteur
 * => 5: adresses destination
 *
 * Arguments ajoutés par la balise statique :
 * => tot-1: id_objet
 * => tot-2: type objet
 *
 * @name 		BaliseDynamique
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package		Tip-a-friend
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
global $div_debug; $div_debug = array();

/**
 * Balise classique, appelée par SPIP
 *
 * On ajoute ici dans les paramètres du contexte de la balise les valeurs de l'objet demandé
 * et de son identifiant
 */
function balise_TIPAFRIEND($p, $nom='TIPAFRIEND') {
	global $div_debug;
	if (!is_array($p->param) OR !count($p->param))
		$p->param = array(array(0=>null));

    $objet = $p->boucles[$p->id_boucle]->id_table;
    $_objet = $objet ? objet_type($objet) : "balise_hors_boucle";
	$t = new Texte;
	$t->texte = $_objet;
	$p->param[0][] = array($t); 
	if(_TIPAFRIEND_TEST)
		$div_debug[_T('tipafriend:taftest_creation_objet_texte')] = 
			var_export($t, true);

	$_id_objet = $p->boucles[$p->id_boucle]->primary;
    $id_objet = champ_sql($_id_objet, $p);
	$t = new Champ;
	$t->nom_champ = "id_$_objet";
	$p->param[0][] = array($t); 
	if(_TIPAFRIEND_TEST)
		$div_debug[_T('tipafriend:taftest_creation_objet_champs')] =
			var_export($t, true);

	// Arguments vides puisque fonction statique ci-dessous
	$args = $supp = array();
	return calculer_balise_dynamique($p, $nom, $args, $supp);
}

/**
 * Balise statique
 *
 * On organise ici les paramètres qui seront passés à la fonction dynamiques,
 * notamment en fonction de leur nombre (<i>paramètres passés à la balise dans les squelettes
 * ou non ...</i>).
 */
function balise_TIPAFRIEND_stat($args, $filtres) {
	global $div_debug;
	$num = count($args);

	if(_TIPAFRIEND_TEST)
		$div_debug[_T('tipafriend:taftest_arguments_balise_stat')] =
			var_export($args, true);

	$type_skel = ($num >= 3) ? $args[0] : ''; // 1: type de squelette
	$url = ($num >= 4) ? $args[1] : ''; // 2: URL
	$adresse_exped = ($num >= 5) ? $args[2] : ''; // 3: email expediteur
	$nom_exped = ($num >= 6) ? $args[3] : ''; // 4: nom expediteur
	$adresse_dest = ($num >= 7) ? $args[4] : ''; // 5: adresses destination

    $objet = $args[$num-2]; // tot-2: type objet
    $id_objet = $args[$num-1];// tot-1: id_objet

	$args = array($objet, $id_objet, $url, $type_skel, $adresse_exped, $nom_exped, $adresse_dest);
	return $args;
}

/**
 * Balise dynamique
 *
 * On charge ici l'ensemble du contexte en fonction des paramètres passés et de la configuration
 * courante. Puis on renvoie le modèle et le contexte.
 *
 * Le cache de ce modèle est fixé à 0. Cette valeur peut être modifiée en première ligne de cette fontion.
 */
function balise_TIPAFRIEND_dyn($objet='', $id_objet='', $url='', $skel='', $mail_exp='', $nom_exp='', $mail_dest='', $plus='') {
	// Temps du cache sur le modèle | peut être modifié
	$temps_de_cache = 0;

	global $div_debug;
	include_spip('inc/filtres');
	$config = tipafriend_config();
	$list_objets = array('article', 'breve', 'rubrique', 'mot', 'auteur', 'syndic');
	$id = $type = '';

	if(_TIPAFRIEND_TEST)
		$div_debug[_T('tipafriend:taftest_arguments_balise_dyn')] =
			var_export(array('objet'=>$objet, 'id_objet'=>$id_objet, 'url'=>$url, 'squelette'=>$skel, 'adresse mail'=>$mail, 'ajouts'=>$plus), true);

	// Completer la requete
	$_url = strlen($url) ? urlencode($url) : urlencode(url_absolue(self('&amp;')));
	$type_skel = strlen($skel) ? $skel : 'complet';
	$model = str_replace('.html', '', $config['modele']);
	if (!in_array($type_skel, array('mini','complet'))) {
		$model = str_replace('.html', '', $type_skel);
		$type_skel = 'complet';
		if(_TIPAFRIEND_TEST)
			$div_debug[_T('tipafriend:taftest_modele_demande')] = var_export($model, true);
	}
	if(!find_in_path('modeles/'.$model.'.html')) {
		if(_TIPAFRIEND_TEST)
			$div_debug[] = _T('tipafriend:taftest_skel_pas_trouve', array('skel'=>$model));
		spip_log("TIPAFRIEND squelette de config utilisateur pas trouve ! ['$model']");
		$model = str_replace('.html', '', $GLOBALS['TIPAFRIEND_DEFAULTS']['modele']);
	}

	// Construction du contexte
	$contexte = array( 
		'fond' => 'modeles/'.$model,
		'url' => $_url,
		'type' => $type_skel,
		'options' => _request('options') ? _request('options') : (
			$config['options'] ? $config['options'] : ''
		),
		'java' => ($config['javascript_standard'] == 'oui') ? 'oui' : 'non',
		'adresse_expediteur' => $_mail ? $_mail : '',
		'temps_cache' => $temps_de_cache
	);
	foreach($list_objets as $_obj){
		if( strlen($objet) AND strtolower($objet) == $_obj) {
			$contexte["id_$_obj"] = $id_objet;
			$id = $id_objet;
			$type = $objet;
		}
		else $contexte["id_$_obj"] = '';
	}
	$url_args = "id=$id&type=$type&mex=$mail_exp&nex=$nom_exp&mdes=$mail_dest&usend=$_url";
	$skel = $config['squelette'];
	if(!find_in_path($skel.'.html')) {
		if(_TIPAFRIEND_TEST)
			$div_debug[] = _T('tipafriend:taftest_skel_pas_trouve', array('skel'=>$skel));
		spip_log("TIPAFRIEND squelette de config utilisateur pas trouve ! ['$skel']");
		$skel = str_replace('.html', '', $GLOBALS['TIPAFRIEND_DEFAULTS']['squelette']);
	}
	if(_TIPAFRIEND_TEST) $url_args .= "&var_mode=recalcul";
	$contexte['lien_href_accessible'] = generer_url_public($skel, $url_args);
	if($config['header'] == 'non') $url_args .= "&header=non";
	if($config['close_button'] == 'non') $url_args .= "&close_button=non";
	$contexte['lien_href'] = generer_url_public($skel, $url_args);

	if(_TIPAFRIEND_TEST){
		$div_debug[_T('tipafriend:taftest_contexte_modele')] = 
			var_export($contexte, true);
		echo taf_dbg_block($div_debug);
	}

	return array('modeles/'.$model.'.html', $temps_de_cache, $contexte);
}
?>