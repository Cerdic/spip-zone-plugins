<?php


// Affiche une arborescence connect > tables / champs
function extras_menu_champs($champs) {
	$menu = '';
	foreach ($champs as $connect => $tables)
	if ($tables) {
		$menu .= "<ul>\n";
		$menu .= "<h2>$connect</h2>\n";
		foreach ($tables as $table => $champs) {
			if ($champs) {
				$url = generer_url_ecrire('objet', 'table='.$table);
				$menu .= "<li>\n<h3><a href='".$url."'>".$table."</a></h3>\n";
				$menu .= "<ul>\n";
				foreach ($champs as $champ => $desc) {
					$menu .= "<li>".$desc."</li>\n";
				}
				$menu .= "</ul>\n</li>\n";
			}
		}
		$menu .= "</ul>\n";
	}

	return $menu;
}



// Liste les champs anormaux par rapport aux definitions de SPIP
function extras_champs_anormaux() {
	// recuperer les champs accessibles
	$tout = extras_tout();

	// recuperer les champs SPIP connus
	include_spip('base/auxiliaires');
	include_spip('base/serial');
	$tables_spip = array_merge($GLOBALS['tables_principales'], $GLOBALS['tables_auxiliaires']);

	// chercher ce qui est different
	$ntables = array();
	$nchamps = array();
	foreach ($tout['connect'] as $table => $champs) {
		if (!isset($tables_spip[$table]['field']))
			$nchamps[$table] = $champs;
		else foreach($champs as $champ => $desc)
			if (!isset($tables_spip[$table]['field'][$champ]))
				$nchamps[$table][$champ] = $desc;
	}

	unset($tout['connect']);
	if($nchamps)
		$tout['connect'] = $nchamps;

	return $tout;
}

// Liste les connexions disponibles dans config/
function extras_connexions() {
	$connexions = array();
	foreach(preg_files(_DIR_CONNECT.'.*[.]php$') as $fichier) {
		if (lire_fichier($fichier, $contenu)
		AND strpos($contenu, 'spip_connect_db')
		)
			$connexions[] = basename($fichier, '.php');
	}

	return $connexions;
}



// liste les tables dispos ans la connexion $connect
function extras_tables($connect) {
	$a = array();
	if ($s = sql_showbase(null, $connect))
	while ($t = sql_fetch($s, $connect))
		$a[] = array_pop($t);
		return $a;
}


// liste les champs dispos ans la table $table de la connexion $connect
function extras_champs($table, $connect) {
	$desc = sql_showtable($table, null, $connect);
	if (is_array($desc['field']))
		return $desc['field'];
	else
		return array();
}


// etablit la liste de tous les champs de toutes les tables de toutes les bases dispos
function extras_tout() {
	$champs = array();
	foreach(extras_connexions() as $connect)
		foreach (extras_tables($connect) as $table)
			$champs[$connect][$table] = extras_champs($table, $connect);

	return $champs;
}
