<?php


function exec_extras_dist() {
	include_spip('inc/extras');
	include_spip('inc/filtres');
	include_spip('inc/meta');

	$actifs = @unserialize($GLOBALS['meta']['extras_actifs']);

	$anormaux = extras_champs_anormaux();

	if (is_array($_POST['var'])) {
		foreach($_POST['var'] as $code)
			if ($_POST['val-'.$code])
				$actifs[$code] = true;
			else
				unset($actifs[$code]);

		ecrire_meta('extras_actifs', serialize($actifs));
	}

	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page();
	echo debut_gauche('objet',true);
	echo creer_colonne_droite('', true) . debut_droite('',true);

	foreach ($anormaux as $connect => $tables)
	foreach ($tables as $table => $champs)
	foreach ($champs as $champ => $desc) {
		$code = "$connect/$table/$champ";
		$hidden .= "<input type='hidden' name='var[]' value='$code' />\n";
		$anormaux[$connect][$table][$champ] = 
			"<label><input type='checkbox'"
				." name='val-$code'"
				.(isset($actifs[$code])
					? " checked='checked'"
					: "")
				." />".$champ."</label> <small>".$desc."</small>\n";
	}

	$menu = extras_menu_champs($anormaux);

	if ($menu) {
		echo
			_L("Les champs suivants ont &#233;t&#233; ajout&#233;s &#224; la base de donn&#233;es. Ils sont d'ores et d&#233;j&#224; g&#233;rables dans les boucles. Cochez ceux que vous souhaitez g&#233;rer depuis l'espace priv&#233;.");
		
		echo "<form action='".self()."' method='post'>",
			form_hidden(self()),
			$hidden;
		echo $menu;

		echo "<input type='submit' />";

		echo "</form>\n";
	}


	echo "<p>"._L("Cr&#233;ez des champs et des tables suppl&#233;mentaires dans votre base de donn&#233;es pour pouvoir les g&#233;rer directement avec SPIP (boucles et/ou espace priv&#233;).")."</p>\n";

	echo fin_gauche(),fin_page();
}

