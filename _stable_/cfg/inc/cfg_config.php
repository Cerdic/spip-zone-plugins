<?php
/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2007-2008, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * Definitions des fonctions lire_config, ecrire_config et effacer_config.
 * 
 */


// charge le depot qui va bien en fonction de l'argument demande
// exemples : 
// meta::description
// metapack::prefixe_plugin
// metapack::prefixe/casier/champ
// tablepack::auteur@extra:8/prefixe/casier/champ
// tablepack::~login@extra/prefixe/casier/champ
//
// en l'absence du nom de depot (gauche des ::) cette fonction prendra comme suit :
// ~ en premier caractere : tablepack
// : present avant un / : tablepack
// sinon metapack
function cfg_charger_depot($args){
	list($depot,$args) = explode('::',$args,2);

	// si un seul argument, il faut trouver le depot
	if (!$args) {
		$args = $depot;
		if ($args[0] == '~'){
			$depot = 'tablepack';	
		} elseif (
			(list($head, $body) = explode('/',$args,2)) &&
			(strpos($head,':') !== false)) {
				$depot = 'tablepack';
		} else {
			if (count(explode('/',$args))>1)
				$depot = 'metapack';
			else 
				$depot = 'meta';
		}
	}

	$d = cfg_charger_classe('cfg_depot');
	$depot = new $d($depot);
	$depot->charger_args($args);
	return $depot;
}

// lire_config() permet de recuperer une config depuis le php
// memes arguments que la balise (forcement)
// $cfg: la config, lire_config('montruc') est un tableau
// lire_config('montruc/sub') est l'element "sub" de cette config
// comme la balise pour ~, ~duchmol ou table:id
// on peut aussi passer un tableau array($table, $colonne, $colid, $id, $cfg) cf. inc/cfg_tablepack.php
// $def: un defaut optionnel
// $serialize: defaut false contrairement a la balise
// (en php on veut plutot un tableau, en squellette, du texte)
function lire_config($cfg='', $def=null, $serialize=false) {
	$lire = charger_fonction("lire_config","inc");
	return $lire($cfg, $def, $serialize);
}


function inc_lire_config_dist($cfg='', $def=null, $serialize=false){ // supprimer serialize

	$depot = cfg_charger_depot($cfg);
	if ($depot->version > 1) {
		$r = $depot->lire_config();
//echo "\n- $cfg : r:"; var_dump($r); echo "\n- $cfg : def:"; var_dump($def); echo "<br />";
		if (is_null($r)) return $def;
		return $r;
	}

	// Toute la suite est temporaire, le temps que tous les
	// depots fonctionnent avec la nouvelle API
	if (!is_array($cfg)) {
		list($cfg,$args) = explode('::',$cfg,2);
		if ($args) $cfg=$args; 
	}
	
	$param = charger_fonction("cfg_analyse_param","inc");
	$params = $param($cfg);

	$chemin=$params['chemin']; 
	$config=$params['donnees'];
	
	$cfg = explode('/',$chemin);

	while ($x = array_shift($cfg)) {
		if (is_string($config) && is_array($c = @unserialize($config))) {
			$config = $c[$x];
		} else {
			if (is_string($config)) {
				$config = null;
			} else {
				$config = $config[$x];
			}
		}
	}

	// transcodage vers le mode serialize
	if ($serialize && is_array($config)) {
		$ret = serialize($config);
	} elseif (!$serialize && is_null($config) && !$def 
			&& $serialize === '') // hack affreux pour le |in_array...
	{
		// pas de serialize requis et config vide, c'est qu'on veut un array()
		// un truc de toggg que je ne sais pas a quoi ca sert.
		// bon, ca sert si on fait un |in_array{#CONFIG{chose,'',''}}
		$ret = array();
	} elseif (!$serialize && ($c = @unserialize($config))) {
	// transcodage vers le mode non serialize
		$ret = $c;
	} else {
	// pas de transcodage
		$ret = $config;
	}
	
	return is_null($ret) && $def ? $def : $ret;	
}



/*
 * 
 * ecrire_config($chemin, $valeur) 
 * permet d'enregistrer une configuration
 * 
 * Si valeur == null : suppression.
 * 
 * $serialise = true : serialise les donnees (choix par defaut)
 */
function ecrire_config($cfg='', $valeur=null, $serialize=true){
	$ecrire = charger_fonction("ecrire_config","inc");
	return $ecrire($cfg, $valeur, $serialize);	
}


function inc_ecrire_config_dist($cfg='', $valeur=null, $serialize=true){ // supprimer $serialize ensuite
	$depot = cfg_charger_depot($cfg);
	if ($depot->version > 1) {
		return $depot->ecrire_config($valeur);
	}

	// Toute la suite est temporaire, le temps que tous les
	// depots fonctionnent avec la nouvelle API
	if (!is_array($cfg)) {
		list($cfg,$args) = explode('::',$cfg,2);
		if ($args) $cfg=$args; 
	}
		
	$param = charger_fonction("cfg_analyse_param","inc");
	$params = $param($cfg);
	
// 1) lecture
	// on recupere toutes les informations depuis
	// la racine de la meta ou du champ de table (extra par defaut)
	$chemin = explode('/', $params['chemin']);
	$champ 	= $chemin[count($chemin)-1];
	$racine = $chemin[0];

	switch ($params['storage']) {
		case 'auteur':
			$base = lire_config('~' . implode('', $params['table']['id']) 
									. '@' . $params['table']['colonne'] );
			break; 
			
		case 'table':
			$base = lire_config($params['table']['nom'] 
									. '@' . $params['table']['colonne'] 
									. ':' . implode(':', $params['table']['id']));
			break; 
			
		case 'meta':
		default:
			$base = lire_config($racine);
			break;	
	}
	
	
// 2) modifications
	// on modifie le tableau recupere pour prendre en compte
	// les changements (modifs ou suppressions)
	$ici = &$base;
	$supprimer = ($valeur === null);

	switch ($params['storage']) {
		case 'auteur':
		case 'table':
			// champs compose : 'chose/truc', 'chose/bidule/truc', ...
			if (count($chemin)>1){
				array_pop($chemin);		
				$ici = &cfg_monte_arbre($ici, $chemin);

				if ($supprimer) unset($ici[$champ]);
				else $ici[$champ] = $valeur;
				
			// champs simples : 'truc'
			} else {
				// si pas de champ (ie. '~duchmol/' ou '~duchmol' ou 'auteur:3')
				// modifier tout le contenu
				if (empty($champ)) {
					if ($supprimer) unset($base);
					else $base = $valeur;	
				
				// si un champ 	(ie. '~duchmol/champ')	
				// ne modifier que celui ci		
				} else {
					if (!is_array($ici)) $ici = array();
					
					if ($supprimer) unset($ici[$champ]);
					else $ici[$champ] = $valeur;	
				}
			}			
			break; 
			
		case 'meta':
		default:
			// champs compose : 'chose/truc', 'chose/bidule/truc', ...
			if (count($chemin)>1){	
				array_pop($chemin);
				array_shift($chemin);
				$ici = &cfg_monte_arbre($ici, $chemin);
	
				if ($supprimer) unset($ici[$champ]);
				else $ici[$champ] = $valeur;
				
			// champs simples : 'truc'
			} else {
				if ($supprimer) unset($base);
				else $base = $valeur;
			}
			break;	
	}			

		
	
// 3) ecriture
	// on sauvegarde les changements dans la meta
	// ou le champ de table (extra par defaut)
	$retour = '';
	switch ($params['storage']) {
		case 'table':
		case 'auteur':
			// une requete sql pour mettre a jour
			// where
			$where = array();
			foreach ($params['table']['id'] as $nom => $val)
				$where[] = "$nom = '$val'";
			// contenu
			$c = (($base == null) ? '' : (($serialize) ? serialize($base) : $base));
			// creer la colonne si elle n'existe pas
			$col = sql_showtable($params['table']['nom']);
			if (!array_key_exists($params['table']['colonne'], $col['field'])) {
				if (!sql_alter("TABLE " . $params['table']['nom'] . " ADD " . $params['table']['colonne'] . " TEXT DEFAULT ''")) {
					spip_log("CFG (ecrire_config) n'a pas reussi a creer automatiquement la colonne " . $params['table']['colonne'] . " dans la table " . $params['table']['nom'] . ".");
					break;	
				}
			}
			$retour = sql_updateq(
				$params['table']['nom'],
				array($params['table']['colonne'] => $c ),
				$where);
			break;	
			
		case 'meta':
		default:	
			if (!$base) effacer_meta($racine);
			else ecrire_meta($racine, (($serialize) ? serialize($base) : $base) );
			// ecrire_meta ne renvoie rien, on considere que c'est bon ?
			$retour = true;
			break;	
	}
	return $retour;

}

/*
 * effacer_config($chemin) 
 * permet de supprimer une config 
 */
function effacer_config($cfg=''){
	$effacer = charger_fonction("effacer_config","inc");
	return $effacer($cfg);	
}

function inc_effacer_config_dist($cfg=''){
	$depot = cfg_charger_depot($cfg);
	if ($depot->version > 1) {
		return $depot->effacer_config();
	}
	
	return ecrire_config($cfg);	
}
	
//
// Se positionner dans le tableau arborescent
// 
// $base est le contenu de la racine d'une lecture de cfg... 
// Par exemple, si on lit 'metapack::nom/mon/casier/champ'
// $base est le contenu de la meta 'nom'
//
// $chemin est l'endroit ou il faut se positionner dans le tableau
// $chemin sera 'mon/casier' pour l'exemple precedent.
// Si cette arborescence n'existe pas, elle sera cree.
// 
function &cfg_monte_arbre(&$base, $chemin){
	if (!$chemin) {
		return $base;
	}
	
	if (!is_array($chemin)) {
		$chemin = explode('/', $chemin);
	}	

	if (!is_array($base)) {
		$base = array();
	}

	foreach ($chemin as $chunk) {
		if (!isset($base[$chunk])) {
			$base[$chunk] = array();
		}
		$base = &$base[$chunk];
	}
	
	if (!is_array($base)) $base = array();
	
	return $base;
}
	

/*
 * Cherche les cles primaires d'une table
 */
function get_table_id($table) {	
	static $catab = array(
		'tables_principales' => 'base/serial',
		'tables_auxiliaires' => 'base/auxiliaires',
	);
	$try = array($table, 'spip_' . $table);
	foreach ($catab as $categ => $catinc) {
		include_spip($catinc);
		foreach ($try as $nom) {
			if (isset($GLOBALS[$categ][$nom])) {
				return array($nom,
					preg_split('/\s*,\s*/', $GLOBALS[$categ][$nom]['key']['PRIMARY KEY']));
			}
		}
	}
	if ($try = table_objet($table)) {
		return array('spip_' . $try, array(id_table_objet($table)));
	}
	return array(false, false);
}


/*
 * 
 * Analyse la chaine ou le tableau passe a CFG
 * et retourne un tableau renseignant le type
 * de donnee a chercher (meta ou table)
 * ainsi que le chemin de la donnee
 * 
 */
function inc_cfg_analyse_param_dist(&$cfg){

	$params = array(
		'storage' => 'meta',
		'chemin' => '',
		'donnees' => '',
		'table' => array(
			'nom' => '',
			'id' => array(), // $nom => $valeur
			'colonne' => ''
		)
	);

	/*
	 * On peut passer une chaine
	 * ou directement un tableau array($table, $colonne, $nom_colonne_id, $id, $chemin_cfg)
	 */
	if (is_string($cfg)) {
		/*
		 * Cas de la recherche par auteur
		 * dans l'extra de la table auteur
		 * 
		 * '~login/monchamp'
		 */
		if ($cfg[0] == '~') {
			$cfg = explode('/', $cfg);
			$params['storage'] = 'auteur';
			$params['table']['nom'] = 'spip_auteurs';
			// ~duchmol/ ou ~32/ ou ~duchmol@extra/ , on veut un auteur par son login ou id
			if ((strlen($cfg[0]) > 1) && ($cfg[0][1] != '@') ) {
				$id_colonne = explode('@', substr(array_shift($cfg),1));
				// recuperer la colonne (attention au cas du login : 'toto@domain.tld'
				//  /!\ todo: REMPLACER 'EXTRA' PAR 'CFG' pour la valeur par defaut
				//  quand la prise en compte de la colonne 'cfg' lors des restaurations de dump sera au point
				$colonne = (count($id_colonne)>1)?array_pop($id_colonne):'extra';
				// retrouver le login
				$id = implode('@', $id_colonne);
				$colid = is_numeric($id) ? 'id_auteur' : 'login';
			// ~/
			// ~@colonne/
			// dans le cfg de l'auteur connecte, ne marche que si cache nul
			} else {
				array_shift($cfg);
				$colonne = substr(array_shift($cfg),2);
				if (!$colonne) $colonne = 'cfg';
				$id = $GLOBALS['auteur_session'] ? $GLOBALS['auteur_session']['id_auteur'] : '';
				$colid = 'id_auteur';
			}
			$params['table']['id'][$colid] = $id;
			$params['table']['colonne'] = $colonne;
			$params['chemin'] = implode('/', $cfg);
			
		/*
		 * si ca commence par table:id:id.../ , 
		 * on veut une table
		 * 
		 * 'rubrique:3/monchamp' -> dans colonne 'cfg'
		 * 'rubrique@extra:2/monchamp' -> dans colonne 'extra'
		 */
		} elseif (strpos($cfg, ':')) {
			$cfg = explode('/', $cfg);
			$id = explode(':', array_shift($cfg));
			// recuperer la colonne
			$table_colonne = explode('@', array_shift($id));
			//  /!\ todo: REMPLACER 'EXTRA' PAR 'CFG' pour la valeur par defaut
			//  quand la prise en compte de la colonne 'cfg' lors des restaurations de dump sera au point
			$colonne = (count($table_colonne)>1)?array_pop($table_colonne):'extra';
			// retrouver la table
			list($table, $colid) = get_table_id($table_colonne[0]);			
			$params['storage'] = 'table';

			$params['table']['nom'] = $table;
			foreach ($colid as $n=>$c) {
				$params['table']['id'][$c] = $id[$n];
			}
			$params['table']['colonne'] = $colonne;
			$params['chemin'] = implode('/', $cfg);
		/*
		 * Sinon, c'est une meta
		 */
		} else {
			$params['storage'] = 'meta';
			$params['chemin'] = $cfg;			
		}
	/*
	 * Sinon, on a passe a cfg directement un tableau
	 */
	} elseif ($cfg) {

		// on peut aussi comme cfg_tablepack donner directement table et le reste
		list($table, $colonne, $colid, $id, $cfg) = array_pad($cfg, 5, '');
		if ($table && !$colid) {
			list($table, $colid) = get_table_id($table);
		}
		if (!is_array($cfg)) {
			$cfg = array($cfg);
		}
		if (!is_array($colid)){
			$colid = array($colid);
			$id = array($id);
		}		
		$params['storage'] = 'table';
		$params['table']['nom'] = $table;
		foreach ($colid as $n=>$c) {
			$params['table']['id'][$c] = $id[$n];
		}
		$params['table']['colonne'] = $colonne;
		$params['chemin'] = implode('/', $cfg);	
	} 
	
	/*
	 * On recupere les donnees (racine du chemin) 
	 * qui serviront a trouver la valeur du chemin demande
	 */
	
	$params['donnees'] = cfg_recuperer_donnees($params);
	return $params;
}


/*
 * Recuperer les donnees en fonction du storage
 */
function cfg_recuperer_donnees($params){

	switch ($params['storage']) {
		case 'table':
		case 'auteur':
			// recuperer la valeur du champ de la table sql
			$where = array();
			foreach ($params['table']['id'] as $nom => $id) {
				$where[] = $nom . '=' . sql_quote($id);
			}

			// verifier que la colonne existe
			$col = sql_showtable($params['table']['nom']);
			if (!array_key_exists($params['table']['colonne'], $col['field'])) {
				$donnees = array();
			// si oui chercher le resultat
			} else {
				$res = sql_select($colonne = $params['table']['colonne'], $params['table']['nom'], $where);
				$res = sql_fetch($res);
				$donnees = isset($res[$colonne]) && $res[$colonne] ?
							$res[$colonne] :  array();	
			}	
			break;
			
		case 'meta':
		default:
			$donnees = $GLOBALS['meta'];
			break;	
	}	
	
	return $donnees;
}



?>
