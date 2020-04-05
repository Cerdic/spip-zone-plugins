<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

/**
 * Generer a la volee un fond a partir d'une table de contenu
 *
 * @param string $type
 * @param string $table
 * @param string $table_sql
 * @param array  $desc
 * @param string $ext
 *
 * @return string
 */
function public_echafauder_dist($type, $table, $table_sql, $desc, $ext) {
	include_spip('public/interfaces');
	$primary = id_table_objet($type);
	if (!$primary AND isset($desc['key']["PRIMARY KEY"])) {
		$primary = $desc['key']["PRIMARY KEY"];
	}

	/* reperer un titre */
	$titre = 'titre';
	if (isset($GLOBALS['table_titre'][$table])) {
		$titre = explode(' ', $GLOBALS['table_titre'][$table]);
		$titre = explode(',', reset($titre));
		$titre = reset($titre);
	}
	if (isset($desc['field'][$titre])) {
		unset($desc['field'][$titre]);
		$titre = "<h1 class='h1 #EDIT{titre}'>#" . strtoupper($titre) . "</h1>";
	} else {
		$titre = "";
	}

	/* reperer une date */
	$date = "date";
	if (isset($GLOBALS['table_date'][$table])) {
		$date = $GLOBALS['table_date'][$table];
	}
	if (isset($desc['field'][$date])) {
		unset($desc['field'][$date]);
		$date = strtoupper($date);
		$date = "<p class='info-publi'>[(#$date|nom_jour) ][(#$date|affdate)][, <span class='auteurs'><:par_auteur:> (#LESAUTEURS)</span>]</p>";
	} else {
		$date = "";
	}

	$content = array();
	foreach ($desc['field'] as $champ => $z) {
		if (!in_array($champ, array('maj', 'statut', 'idx', $primary))) {
			$content[] = "[<div><strong>$champ</strong><div class='#EDIT{" . $champ . "} $champ'>(#" . strtoupper($champ) . "|image_reduire{500,0})</div></div>]";
		}
	}
	$content = implode("\n\t", $content);

	$scaffold = "#CACHE{0}
<BOUCLE_contenu($table_sql){" . $primary . "}>
[(#REM) Fil d'Ariane ]
<p id='hierarchie'><a href='#URL_SITE_SPIP/'><:accueil_site:></a>[ &gt; <strong class='on'>(#TITRE|couper{80})</strong>]</p>

<div class='contenu-principal'>
	<div class='cartouche'>
		$titre
		$date
	</div>

	$content

</div>

[<div class='notes surlignable'><h2 class='h2 pas_surlignable'><:info_notes:></h2>(#NOTES)</div>]
</BOUCLE_contenu>";

	$dir = sous_repertoire(_DIR_CACHE, "scaffold", false);
	$dir = sous_repertoire($dir, "contenu", false);
	$f = $dir . "$type";
	ecrire_fichier("$f.$ext", $scaffold);

	return $f;
}

?>
