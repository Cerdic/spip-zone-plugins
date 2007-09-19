<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!function_exists('syndication_en_erreur')){
		// filtre |syndication_en_erreur
	function syndication_en_erreur($statut_syndication) {
		if ($statut_syndication == 'off'
		OR $statut_syndication == 'sus')
			return _T('sedna:probleme_de_syndication');
	}
}
	// filtre de nettoyage XHTML strict d'un contenu potentiellement hostile
	// |textebrut|lignes_longues|entites_html|antispam2|texte_script
	function nettoyer_texte($texte) {
		return texte_script(
			antispam2(
			corriger_toutes_entites_html(
			entites_html(
			couper(
			lignes_longues(
			textebrut(
				$texte
			)), 600)
			))));
	}
	// tri maison : d'abord par jour de syndication,
	// et a l'interieur du jour par date de maj
	function critere_tri_sedna($idb, &$boucles, $crit) {
		$boucle = &$boucles[$idb];
		$boucle->order = array(
			"'date_format(syndic_articles.date,\\'%Y-%m-%d 00:00:00\\') DESC'", "'syndic_articles.maj DESC'", "'syndic_articles.date DESC'"
		);
	}

	// critere {contenu}
	function critere_contenu($idb, &$boucles, $crit) {
		$boucle = &$boucles[$idb];

		// un peu trop rapide, ca... le compilateur exige mieux
		$boucle->hash = '
		// RECHERCHE
		if ($r = addslashes($Pile[0]["recherche"]))
			$s = "(syndic_articles.descriptif LIKE \'%$r%\'
				OR syndic_articles.titre LIKE \'%$r%\'
				OR syndic_articles.url LIKE \'%$r%\'
				OR syndic_articles.lesauteurs LIKE \'%$r%\')";
			else $s = 1;
		';
		$boucle->where[] = '$s';
	}
	// identifiant d'un lien en fonction de son url et sa date, 4 chars
	// 3ko = 500 * (5 caracteres + espace)
	// 16**5 possibilites = suffisant pour eviter risque de doublons sur 500
	function creer_identifiant ($url,$date) {
		return substr(md5("$date$url"),0,5);
	}
	// unicode 24D0 = caractere de forme "(a)"
	function antispam2($texte) {
		return str_replace('@','&#x24d0;', $texte);
	}

?>