<?php

function svp_afficher_version($intervalle, $logiciel){
	if (!strlen($intervalle)) return '';
	if (!preg_match(',^[\[\(]([0-9.]*[\s-]*[a-zRC\s]*)[;]([0-9.]*[\s-]*[a-zRC\s]*)[\]\)]$,',$intervalle,$regs)) return false;
	$mineure = $regs[1];
	$majeure = $regs[2];
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

function svp_afficher_dependances($balise_serialisee, $dependance='necessite', $type='plugins', $sep='<br />'){
	$texte = '';
	
	$dependances = unserialize($balise_serialisee);
	foreach($dependances[$dependance] as $_dependance) {
		if (($type == 'plugins' ) AND ($_dependance['id'] != 'spip')) {
			if ($texte) 
				$texte .= $sep;
			$plugin = sql_fetsel('id_plugin, nom', 'spip_plugins', 'prefixe=' . sql_quote($_dependance['id']));
			$logiciel = '<a href="' . generer_url_entite($plugin['id_plugin'], 'plugin') . '" title="' . _T('svp:bulle_aller_plugin') . '">' .
						extraire_multi($plugin['nom']) . '</a>';
			$texte .= svp_afficher_version($_dependance['version'], $logiciel);
		}
	}
	
	return $texte;
}

function svp_afficher_voirenligne($id_depot) {
	$url = generer_url_action('redirect', "type=depot&id=$id_depot&var_mode=calcul");
	return icone_horizontale(_T('voir_en_ligne'), $url, 'racine-24.gif', 'rien.gif', 0);
}


function svp_extraire_url($lien) {

	if (!preg_match(_RACCOURCI_LIEN, $lien, $matches))
		return trim($lien);
	else
		return trim($matches[4]);
}

function svp_afficher_statistiques_globales($id_depot){
	$info = '';

	$total = svp_compter('depot', $id_depot);
	if (!$id_depot) {
		// Si on filtre pas sur un depot alors on affiche le nombre de depots
		$info = '<li id="stats-depot" class="liste-items">
					<div class="unit size80">' . ucfirst(trim(_T('svp:info_depots_disponibles', array('total_depots'=>'')))) . '</div>
					<div class="unit size20 lastUnit">' . $total['depot'] . '</div>
				</li>';
	}
	// Compteur des plugins filtre ou pas par depot
	$info .= '<li id="stats-plugin" class="liste-items">
				<div class="unit size80">' . ucfirst(trim(_T('svp:info_plugins_heberges',  array('total_plugins'=>'')))) . '</div>
				<div class="unit size20 lastUnit">' . $total['plugin'] . '</div>
			</li>';
	// Compteur des paquets filtre ou pas par depot
	$info .= '<li id="stats-paquet" class="liste-items">
				<div class="unit size80">' . ucfirst(trim(_T('svp:info_paquets_disponibles', array('total_paquets'=>'')))) . '</div>
				<div class="unit size20 lastUnit">' . $total['paquet'] . '</div>
			</li>';

	return $info;
}

function svp_compter_telechargements($id_depot, $categorie){
	$total = svp_compter('paquet', $id_depot, $categorie);
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

function svp_compter_plugins($id_depot, $categorie){
	$total = svp_compter('plugin', $id_depot, $categorie);
	$info = _T('svp:info_plugins_disponibles', array('total_plugins'=>$total['plugin']));
	return $info;
}

function svp_compter($entite, $id_depot=0, $categorie=0){
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
		$compteurs['plugin'] = sql_count(sql_select('t2.id_plugin', $from, $where));
	}
	elseif ($entite == 'paquet') {
		if ($categorie) {
			$ids = sql_allfetsel('id_plugin', 'spip_plugins', 'categorie='.sql_quote($categorie));
			$ids = array_map('reset', $ids);
			$where[] = sql_in('t1.id_plugin', $ids);
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
		if ($id_depot) {
			$ids = sql_allfetsel('id_plugin', 'spip_depots_plugins AS t1', $where);
			$ids = array_map('reset', $ids);
			$where = array(sql_in('t2.id_plugin', $ids));
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

	$retour = '';
	$svp_categories = unserialize($GLOBALS['meta']['svp_categories']);

	if (is_array($svp_categories)) {
		if (($categorie) AND in_array($categorie, $svp_categories))
			$retour = _T('svp:categorie_' . strtolower($categorie));
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

function svp_traduire_categorie($alias) {

	$traduction = '';
	if ($alias) {
		$traduction = _T('svp:categorie_' . $alias);
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

// Surcharge du filtre foreach pour qu'il passe des parametres au modele
// Pour l'instant ces parametres sont passes sous la forme Vi=Ei
// mais peut-etre qu'un tableau serait plus approprie ???
// Le foreach est toutefois renomme
function filtre_iterer_modele($balise_deserializee, $modele ='foreach') {
		
	$parametres = func_get_args();
	unset($parametres[0], $parametres[1]);

	$texte = '';
	$i = 0;
	$contexte = array();
	if(is_array($balise_deserializee)) {
		foreach ($balise_deserializee as $k => $v) {
			$i++;
			$contexte = array_merge(array('iteration' => $i, 'cle' => $k), (is_array($v) ? $v : array('valeur' => $v))) ;
			if (is_array($parametres)){
				foreach($parametres as $_p){
					if (preg_match(",^([^=]*)=(.*)$,", $_p, $matches)) {
						$contexte[$matches[1]] = $matches[2];
					}
				}	
			}
			$texte .= recuperer_fond('modeles/'.$modele, $contexte);
		}
	}

	return $texte;
}

?>
