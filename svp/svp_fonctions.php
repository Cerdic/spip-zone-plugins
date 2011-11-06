<?php

function svp_afficher_intervalle($intervalle, $logiciel){
	if (!strlen($intervalle)) return '';
	if (!preg_match(',^[\[\(\]]([0-9.a-zRC\s\-]*)[;]([0-9.a-zRC\s\-\*]*)[\]\)\[]$,Uis',$intervalle,$regs)) return false;
	$mineure = $regs[1];
	$majeure = preg_replace(',\.99$,', '.*', $regs[2]);
	$mineure_inc = $intervalle{0}=="[";
	$majeure_inc = substr($intervalle,-1)=="]";
	if (strlen($mineure)){
		if (!strlen($majeure))
			$version = $logiciel . ($mineure_inc ? ' &ge; ' : ' &gt; ') . $mineure;
		else
			$version = $mineure . ($mineure_inc ? ' &le; ' : ' &lt; ') .  $logiciel . ($majeure_inc ? ' &le; ' : ' &lt; ') . $majeure;
	}
	else {
		$version =  $logiciel . ($majeure_inc ? ' &le; ' : ' &lt; ') . $majeure;
	}	

	return $version;
}


function svp_afficher_etat($etat) {
	include_spip('plugins/afficher_plugin');
	return plugin_etat_en_clair($etat);
}


function svp_afficher_dependances($balise_serialisee, $dependance='necessite', $sep='<br />') {
	$texte = '';
	
	$t = unserialize($balise_serialisee);
	$dependances = $t[$dependance];
	ksort($dependances);

	foreach($dependances as $_compatibilite => $_dependance) {
		$compatibilite = ($_compatibilite !== 0)
				? _T('svp:info_compatibilite_dependance', array('compatibilite' => svp_afficher_intervalle($_compatibilite, 'SPIP')))
				: '';
		if ($compatibilite)
			$texte .= ($texte ? str_repeat($sep, 2) : '') . $compatibilite;
		foreach ($_dependance as $_plugin) {
			if ($texte)
				$texte .= $sep;
			if (($dependance == 'necessite' ) OR ($dependance == 'utilise')) {
				if ($plugin = sql_fetsel('id_plugin, nom', 'spip_plugins', 'prefixe=' . sql_quote($_plugin['nom'])))
					$logiciel = '<a href="' . generer_url_entite($plugin['id_plugin'], 'plugin') . '" title="' . _T('svp:bulle_aller_plugin') . '">' .
								extraire_multi($plugin['nom']) . '</a>';
				else
					// Cas ou le plugin n'est pas encore dans la base SVP.
					// On affiche son préfixe, cependant ce n'est pas un affichage devant perdurer
					$logiciel = $_plugin['nom'];
				$intervalle = svp_afficher_intervalle($_plugin['version'], $logiciel);
				$texte .= ($intervalle) ? svp_afficher_intervalle($_plugin['version'], $logiciel) : $logiciel;
			}
			else
				// On demande l'affichage des librairies
				$texte .= '<a href="' . $_plugin['lien'] . '" title="' . _T('svp:bulle_telecharger_librairie') . '">' .	$_plugin['nom'] . '</a>';
		}
	}

	return $texte;
}


function svp_dependances_existe($balise_serialisee) {
	$dependances = unserialize($balise_serialisee);
	foreach($dependances as $_dependance) {
		if ($_dependance)
			return true;
	}

	return false;
}


function svp_afficher_credits($balise_serialisee, $sep=', ') {
	$texte = '';
	
	$credits = unserialize($balise_serialisee);
	if (is_array($credits)) {
		foreach ($credits as $_credit) {
			if ($texte) 
				$texte .= $sep;
			// Si le credit en cour n'est pas un array c'est donc un copyright
			$texte .= 
				(!is_array($_credit)) 
				? $_credit 
				: ($_credit['url'] ? '<a href="' . $_credit['url'] . '">' : '') . 
				  $_credit['nom'] .
				  ($_credit['url'] ? '</a>' : '');
		}
	}

	return $texte;
}


function svp_afficher_langues($langues, $sep=', '){
	$texte = '';
	
	if ($langues) {
		foreach ($langues as $_code => $_traducteurs) {
			if ($texte) 
				$texte .= $sep;
			$texte .= $_code . (count($_traducteurs) > 0 ? ' (' . implode(', ', $_traducteurs) . ')' : '');
		}
	}

	return $texte;
}


function svp_afficher_statistiques_globales($id_depot=0){
	$info = '';

	$total = svp_compter('depot', $id_depot);
	if (!$id_depot) {
		// Si on filtre pas sur un depot alors on affiche le nombre de depots
		$info = '<li id="stats-depot" class="item">
					<div class="unit size4of5">' . ucfirst(trim(_T('svp:info_depots_disponibles', array('total_depots'=>'')))) . '</div>
					<div class="unit size1of5 lastUnit">' . $total['depot'] . '</div>
				</li>';
	}
	// Compteur des plugins filtre ou pas par depot
	$info .= '<li id="stats-plugin" class="item">
				<div class="unit size4of5">' . ucfirst(trim(_T('svp:info_plugins_heberges',  array('total_plugins'=>'')))) . '</div>
				<div class="unit size1of5 lastUnit">' . $total['plugin'] . '</div>
			</li>';
	// Compteur des paquets filtre ou pas par depot
	$info .= '<li id="stats-paquet" class="item">
				<div class="unit size4of5">' . ucfirst(trim(_T('svp:info_paquets_disponibles', array('total_paquets'=>'')))) . '</div>
				<div class="unit size1of5 lastUnit">' . $total['paquet'] . '</div>
			</li>';

	return $info;
}


function svp_compter_telechargements($id_depot=0, $categorie='', $compatible_spip=''){
	$total = svp_compter('paquet', $id_depot, $categorie, $compatible_spip);
	$info = _T('svp:info_paquets_disponibles', array('total_paquets'=>$total['paquet']));
	return $info;
}


function svp_compter_depots($id_depot, $contrib='plugin'){
	$info = '';

	$total = svp_compter('depot', $id_depot);
	if (!$id_depot) {
		$info = _T('svp:info_depots_disponibles', array('total_depots'=>$total['depot'])) . ', ' .
				_T('svp:info_plugins_heberges', array('total_plugins'=>$total['plugin'])) . ', ' .
				_T('svp:info_paquets_disponibles', array('total_paquets'=>$total['paquet']));
	}
	else {
		if ($contrib == 'plugin') {
			$info = _T('svp:info_plugins_heberges', array('total_plugins'=>$total['plugin'])) . ', ' .
					_T('svp:info_paquets_disponibles', array('total_paquets'=>$total['paquet']-$total['autre']));
		}
		else {
			$info = _T('svp:info_contributions_hebergees', array('total_autres'=>$total['autre']));
		}
	}
	return $info;
}


function svp_compter_plugins($id_depot=0, $categorie='', $compatible_spip='') {
	$total = svp_compter('plugin', $id_depot, $categorie, $compatible_spip);
	$info = _T('svp:info_plugins_disponibles', array('total_plugins'=>$total['plugin']));
	return $info;
}


// Attention le critere de compatibilite spip pris en compte est uniquement celui d'une branche SPIP
function svp_compter($entite, $id_depot=0, $categorie='', $compatible_spip=''){
	$compteurs = array();

	$group_by = array();
	$where = array();
	if ($id_depot)
		$where[] = "t1.id_depot=" . sql_quote($id_depot);

	if ($entite == 'plugin') {
		$from = 'spip_plugins AS t2, spip_depots_plugins AS t1';
		$where[] = "t1.id_plugin=t2.id_plugin";
		if ($categorie)
			$where[] = "t2.categorie=" . sql_quote($categorie);
		if ($compatible_spip) {
			$creer_where = charger_fonction('where_compatible_spip', 'inc');
			$where[] =  $creer_where($compatible_spip, 't2', '>');
		}
		$compteurs['plugin'] = sql_count(sql_select('t2.id_plugin', $from, $where));
	}
	elseif ($entite == 'paquet') {
		if ($categorie) {
			$ids = sql_allfetsel('id_plugin', 'spip_plugins', 'categorie='.sql_quote($categorie));
			$ids = array_map('reset', $ids);
			$where[] = sql_in('t1.id_plugin', $ids);
		}
		if ($compatible_spip) {
			$creer_where = charger_fonction('where_compatible_spip', 'inc');
			$where[] =  $creer_where($compatible_spip, 't1', '>');
		}
		$compteurs['paquet'] = sql_countsel('spip_paquets AS t1', $where);
	}
	elseif ($entite == 'depot') {
		$champs = array('COUNT(t1.id_depot) AS depot', 
						'SUM(t1.nbr_plugins) AS plugin', 
						'SUM(t1.nbr_paquets) AS paquet',
						'SUM(t1.nbr_autres) AS autre');
		$compteurs = sql_fetsel($champs, 'spip_depots AS t1', $where);
	}
	elseif ($entite == 'categorie') {
		$from = array('spip_plugins AS t2');
		$where_depot = $where[0];
		$where = array();
		if ($id_depot) {
			$ids = sql_allfetsel('id_plugin', 'spip_depots_plugins AS t1', $where_depot);
			$ids = array_map('reset', $ids);
			$where[] = sql_in('t2.id_plugin', $ids);
		}
		if ($compatible_spip) {
			$creer_where = charger_fonction('where_compatible_spip', 'inc');
			$where[] =  $creer_where($compatible_spip, 't2', '>');
		}
		if ($categorie)
			$where[] = "t2.categorie=" . sql_quote($categorie);
		else
			$group_by = array('t2.categorie');
		$compteurs['categorie'] = sql_countsel($from, $where, $group_by); 
	}

	return $compteurs;
}


function balise_SVP_CATEGORIES($p) {

	$categorie = interprete_argument_balise(1,$p);
	$categorie = isset($categorie) ? str_replace('\'', '"', $categorie) : '""';

	$p->code = 'calcul_svp_categories('.$categorie.')';

	return $p;
}

function calcul_svp_categories($categorie) {

	$retour = array();
	$svp_categories = $GLOBALS['categories_plugin'];

	if (is_array($svp_categories)) {
		if (($categorie) AND in_array($categorie, $svp_categories))
			$retour[$categorie] = _T('svp:categorie_' . strtolower($categorie));
		else {
			sort($svp_categories);
			// On positionne l'absence de categorie en fin du tableau
			$svp_categories[] = array_shift($svp_categories);
			foreach ($svp_categories as $_alias)
				$retour[$_alias] = svp_traduire_categorie($_alias);
		}
	}
	
	return $retour;
}


function balise_SVP_BRANCHES_SPIP($p) {

	$branche = interprete_argument_balise(1,$p);
	$branche = isset($branche) ? str_replace('\'', '"', $branche) : '""';

	$p->code = 'calcul_svp_branches_spip('.$branche.')';

	return $p;
}

function calcul_svp_branches_spip($branche) {

	$retour = array();
	$svp_branches = $GLOBALS['infos_branches_spip'];

	if (is_array($svp_branches)) {
		if (($branche) AND in_array($branche, $svp_branches))
			// On renvoie les bornes inf et sup de la branche specifiee
			$retour = $svp_branches[$branche];
		else {
			// On renvoie uniquement les numeros de branches
			$retour = array_keys($svp_branches);
		}
	}
	
	return $retour;
}

function svp_traduire_categorie($alias) {

	$traduction = '';
	if ($alias) {
		$traduction = _T('svp:categorie_' . strtolower($alias));
	}
	return $traduction;
}

function svp_traduire_type_depot($type) {

	$traduction = '';
	if ($type) {
		$traduction = _T('svp:info_type_depot_' . $type);
	}
	return $traduction;
}

/**
 * Critere de compatibilite avec une VERSION precise ou une BRANCHE de SPIP :
 * Fonctionne sur les tables spip_paquets et spip_plugins
 *
 *   {compatible_spip}
 *   {compatible_spip 2.0.8} ou {compatible_spip 1.9}
 *   {compatible_spip #ENV{vers}} ou {compatible_spip #ENV{vers, 1.9.2}}
 *   {compatible_spip #GET{vers}} ou {compatible_spip #GET{vers, 2.1}}
 *
 *   Si aucune valeur explicite (dans le critère, par #ENV, par #SET)
 *   tous les enregistrements sont retournés.
 *
 *   Le ! (NOT) fonctionne sur le critère BRANCHE
 */
function critere_compatible_spip_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;

	// Si on utilise ! la fonction LOCATE doit retourner 0.
	// -> utilise uniquement avec le critere BRANCHE
	$op = ($crit->not == '!') ? '=' : '>';

	$boucle->hash .= '
	// COMPATIBILITE SPIP
	$creer_where = charger_fonction(\'where_compatible_spip\', \'inc\');';

	// version/branche explicite dans l'appel du critere
	if (isset($crit->param[0][0])) {
		$version = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucle->id_parent);
		$boucle->hash .= '
		$where = $creer_where('.$version.', \''.$table.'\', \''.$op.'\');
		';
	}
	// pas de version/branche explicite dans l'appel du critere
	// on regarde si elle est dans le contexte
	else {
		$boucle->hash .= '
		$version = isset($Pile[0][\'compatible_spip\']) ? $Pile[0][\'compatible_spip\'] : \'\';
		$where = $creer_where($version, \''.$table.'\', \''.$op.'\');
		';
	}

	$boucle->where[] = '$where';
}

?>
