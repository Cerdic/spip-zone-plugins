<?php

// Affiche un objet connu de extras_actifs
function exec_objet_dist() {
	$actifs = @unserialize($GLOBALS['meta']['extras_actifs']);

	// la connexion
	if (!$connect = _request('connect'))
		$connect = 'connect';

	// l'objet
	$table = _request('table');
	$id = _request('id');
	$edit = _request('edit');

	// quels champs sont gerables pour cet objet ?
	foreach ($actifs as $champ => $ignore) {
		if (strpos($champ, $connect.'/'.$table.'/') === 0)
			$champs[] = preg_replace(',.*/,', '', $champ);
	}

	if (!$champs) {
		include_spip('inc/presentation');

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page();
		echo debut_gauche('objet',true);
		echo creer_colonne_droite('', true) . debut_droite('',true);


		$connects = $tables = array();
		foreach ($actifs as $champ => $ignore) {
			list($connect, $table) = explode('/', $champ);
			$tables[$connect][$table]++;
		}

		foreach ($tables as $connect => $vals) {
			echo "<h2>$connect</h2>\n";
			echo "<ul>";
			foreach ($vals as $table => $ignore) {
				$url = parametre_url(self(), 'connect', $connect);
				$url = parametre_url($url, 'table', $table);
				echo "<li><a href='".$url."'>".$table."</a></li>\n";
			}
			echo "</ul>\n";
		}

		echo fin_gauche(),fin_page();
		exit;
	}

	// Aller chercher les donnees avec la primary key id
	$desc = sql_showtable($table, null, $connect);
	$id_primary = $desc['key']['PRIMARY KEY'];
	$t = sql_fetsel($champs, $table, $id_primary.'='.sql_quote($id, $connect), null, null, null, null, $connect);

	if ($id
	AND !autoriser($table, 'modifier', $id)) {
		include_spip('inc/minipres');
		echo minipres();
	}

	// des modifs envoyees ?
	include_spip('inc/securiser_action');
	if (is_array($_POST['val'])) {
		$modifs = array();
		foreach($t as $key => $val) {
			$cle = md5(secret_du_site()."modifier $connect $table $key");
			if (isset($_POST[$cle]))
				$modifs[$key] = $_POST[$cle];
		}
		if ($modifs) {
			include_spip('inc/modifier');
			modifier_contenu($table, $id, array(), $modifs, $connect);
			$t = sql_fetsel($champs, $table, $id_primary.'='.sql_quote($id, $connect), null, null, null, null, $connect);
		}
	}


	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page();
	echo debut_gauche('objet',true);
	echo creer_colonne_droite('', true) . debut_droite('',true);

	echo "<h1>".$table." ".$id."</h1>\n";


	include_spip('inc/texte');

	if ($t) {

		if ($edit) {
			$url = parametre_url(self(), 'edit', '');
			echo "<form action='".$url."' method='post'>\n",
				form_hidden($url);
		}

		foreach ($t as $key => $val) {
			echo "<h2>$key</h2>\n";
			$ligne = !preg_match(',TEXT,i', $desc['field'][$key])
				AND !preg_match(",[\r\n],", $val);

			if (!$edit)
				echo $ligne
					? typo($val)
					: propre($val);

			if ($edit) {
				$cle = md5(secret_du_site()."modifier $connect $table $key");
				echo 
					"<input type='hidden' name='val[]' value='$cle' />\n"
					, $ligne
						? "<input type='text' name='$cle' value=\""
							.entites_html($val)
							."\" />\n"
						: "<textarea name='$cle'>"
							.entites_html($val)
							."</textarea>\n";
			}
		}

		if (!$edit) {
			$url = parametre_url(self(), 'edit', 1);
			echo "<hr /><a href='".$url."'>"._L('Editer cet objet')."</a>";
		}

		if ($edit) {
			echo "<input type='submit' />\n";
			echo "</form>\n";
			$url = parametre_url(self(), 'edit', '');
			echo "<hr /><a href='".$url."'>"._L('Retour &#224; l\'objet')."</a>";
		}

		$url = parametre_url(parametre_url(self(), 'edit', ''), 'id', '');
		echo "<br /><a href='".$url."'>"._L('Liste des objets')."</a>";

	}
	else {
		// ici une liste d'objets
		$s = sql_query("SELECT $id_primary,"
			.join(', ', $champs)." FROM $table LIMIT 0,50", $connect);
		if (sql_count($s)) {
			echo "<ul>\n";
			while ($t = sql_fetch($s)) {
				$url = parametre_url(self(), 'id', $t[$id_primary]);
				echo "<li><a href='".$url."'>".$t[$id_primary]."</a> ";
				foreach ($champs as $champ)
					echo ' &mdash; ' .couper(typo($t[$champ]))
						."</li>\n";
			}
			echo "</ul>\n";
		} else {
			echo "table vide";
		}
	}

	echo fin_gauche(),fin_page();

}
