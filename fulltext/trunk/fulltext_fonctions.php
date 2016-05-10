<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function balise_AIDE_RECHERCHE($p) {
	if (!function_exists('recuperer_fond')) {
		include_spip('public/assembler');
	}
	//    $arg = interprete_argument_balise(1, $p);
	$mess_aide = str_replace("'", "\'", recuperer_fond('aide_recherche', array('lang' => $GLOBALS['spip_lang'])));
	$p->code = "'$mess_aide'";
	$p->statut = 'html';
	return $p;
}

function lien_objet_ptg($id, $type, $longueur = 80, $connect = null) {
	include_spip('inc/liens');
	$titre = traiter_raccourci_titre($id, $type, $connect);
	$titre = typo($titre['titre']);
	if (!strlen($titre)) {
		$titre = _T('info_sans_titre');
	}
	$url = generer_url_entite($id, $type);
	return "<a href='$url' class='$type'>" . couper($titre, $longueur) . '</a>';
}


/**
 * Un filtre pour mettre en forme #ENV{recherche}|fulltext_recherche_naturelle_fr
 *
 * (ne marche qu'avec la langue fr)
 * - on supprime les s et x pluriels
 * - on ajoute un * sur les mots pour attraper les pluriels quand un singulier est demande
 * - si des ET ou OU sont dans la requete on les transforme en + et (rien)
 * - sinon on ajoute des + pour avoir tous les mots (ET par defaut, chaque mot reduit le perimetre)
 * - les expressions entre "" sont conservees telles quelles
 *
 * @param string $recherche
 * @param bool $strict
 *   si false on n'ajoute pas les + sur chaque terme
 * @return string
 */
function fulltext_recherche_naturelle_fr($recherche, $strict = true) {

	#var_dump($recherche);

	// supprimer caracteres de ponctuation
	$recherche = strtr($recherche, "-_;,'+*", '       ');

	if (preg_match(",\b(ET|OU)\b,", $recherche)) {
		$strict = false; // pas la peine de faire + car c'est gere par les ET et OU
		$recherche = preg_replace(",\bOU\b,", ' ', $recherche);
		$recherche = preg_replace(",\bET\s+,", '+', $recherche);

		$recherche = '+'.trim($recherche); // le premier mot est un ET
	}

	$recherche = preg_replace(',\s+,', ' ', $recherche);

	// les guillemets sont a conserver et on ne touche pas au contenu de la chaine
	if (preg_match(',["][^"]+["],Uims', $recherche, $matches)) {
		foreach ($matches as $match) {
			// corriger le like dans le $q
			$word = preg_replace(',\s+,Uims', '\x1', $match);
			$recherche = str_replace($match, $word, $recherche);
		}
	}

	$recherche = explode(' ', $recherche);
	foreach ($recherche as $k => $r) {
		if (strlen($r) >= 3 and substr($r, 0, 1) !=='"' and substr($r, -1) != '"') {
			$r = rtrim($r, 'sxSX');
			// +the* est conserve dans la requete alors que the est supprime car stopwords
			// on contourne en mettant the* sans le + quand le mot est plus petit ou = 4 a
			$recherche[$k] = (($strict and strlen($r) >= 4) ? '+' : '') . $r . '*';
		}
	}

	$recherche = implode(' ', $recherche);
	$recherche = str_replace("\x1", ' ', $recherche);

	#var_dump($recherche);

	return $recherche;
}
