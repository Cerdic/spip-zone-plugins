<?php
/**
 * Plugin Sommaire automatique
 * (c) 2013 Cédric
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Filtre |retire_sommaire
 * pour retirer le sommaire automatique et les ancres d'un texte
 * @param string $texte
 * @return string
 */
function retire_sommaire($texte) {
	// retirer le sommaire
	if (strpos($texte, '<!--sommaire-->') !== false) {
		$texte = preg_replace(',<!--sommaire-->.*<!--/sommaire-->,Uims', '', $texte);
	}
	return $texte;
}

/**
 * Filtre |retire_ancres_sommaire
 * pour retirer les ancres et le sommaire qui ont ete ajoutee automatiquement sur un texte
 * @param $texte
 * @return mixed
 */
function retire_ancres_sommaire($texte) {
	// retirer les liens de retour au sommaire
	if (strpos($texte, 'sommaire-back') !== false) {
		$texte = preg_replace(",<a class='sommaire-back'[^>]*></a>,Uims", '', $texte);
	}
	return retire_sommaire($texte);
}

/**
 * Filtre |ancres_sommaire
 * pour ajouter les ancres de sommaire sur les intertitres d'un texte
 * (enleve aussi le sommaire eventuellement genere automatiquement)
 *
 * @param string $texte
 * @return string
 */
function ancres_sommaire($texte) {
	$texte = sommaire_post_propre(retire_sommaire($texte), false);
	return $texte;
}

/**
 * Balise #SOMMAIRE pour afficher le sommaire d'un contenu
 * #SOMMAIRE{#TEXTE}
 * gere les notes pour eviter leur doublement
 *
 * @param $p
 * @return mixed
 */
function balise_SOMMAIRE_dist($p) {
	$_texte = interprete_argument_balise(1, $p);
	$_niveau_max = interprete_argument_balise(2, $p);
	$_niveau_max = ($_niveau_max?$_niveau_max:"''");

	$p->code = "sommaire_empile_note().affiche_sommaire($_texte,$_niveau_max).sommaire_depile_note()";
	$p->interdire_scripts = false; // le contenu vient d'un modele
	return $p;
}

/*
 * Protected
 */

function sommaire_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/sommaire.css').'"/>'."\n";
	return $flux;
}

/**
 * Affiche le sommaire d'un texte
 * @param string $texte
 * @return string
 */
function affiche_sommaire($texte, $niveau_max = '') {

	// retirer le(s) sommaire(s) eventuel(s) deja la avant de re-calculer le sommaire
	return sommaire_post_propre(retire_sommaire($texte), $ajoute = true, $sommaire_seul = true, $niveau_max);
}

/**
 * Empile les notes avant evaluation du texte sur lequel est calcule #SOMMAIRE
 * @return string
 */
function sommaire_empile_note() {
	$notes = charger_fonction('notes', 'inc');
	$notes('', 'empiler');
	return '';
}

/**
 * Depile les notes apres calcul de #SOMMAIRE
 * @return string
 */
function sommaire_depile_note() {
	$notes = charger_fonction('notes', 'inc');
	$notes('', 'depiler');
	return '';
}


/**
 * Ajouter le calcul du sommaire automatique sur les textes d'article
 * @param $interfaces
 * @return mixed
 */
function sommaire_declarer_tables_interfaces($interfaces) {
	$traitement = $interfaces['table_des_traitements']['TEXTE'][0];
	if (isset($interfaces['table_des_traitements']['TEXTE']['articles'])) {
		$traitement = $interfaces['table_des_traitements']['TEXTE']['articles'];
	}

	$traitement = str_replace('propre(', 'sommaire_propre(', $traitement);
	$interfaces['table_des_traitements']['TEXTE']['articles']= $traitement;

	return $interfaces;
}

/**
 * Transforme les raccourcis SPIP, liens et modèles d'un texte en code HTML
 *
 * Extrait le sommaire et ses éventuels paramètres
 * avant d'appeler propre() puis sommaire_post_propre().
 *
 * @uses propre()
 * @uses sommaire_post_propre()
 *
 * @param string $texte
 * @param string|null $connect
 * @param array $env
 * @return string
 */
function sommaire_propre($texte, $connect, $env) {

	// Repérer et analyser la balise <sommaire> en amont de propre().
	// Ce modèle n'est pas traité comme les autres : on extrait les éventuels paramètres
	// puis on remplace la balise par un simple marqueur <!--inserer_sommaire-->.
	// Perf : d'abord sans regex pour les formes simples, puis en regex si paramètres.
	$has_sommaire = false;
	$niveau_max = '';
	$marqueur = '<!--inserer_sommaire-->';
	if (
		$p = strpos($texte, '<sommaire>')
		or $p = strpos($texte, '[sommaire]')
	) {
		$has_sommaire = true;
		$texte = substr_replace($texte, $marqueur, $p, strlen('<sommaire>'));
	} elseif (
		$pattern = '/<sommaire(?P<id>\d+)?(?P<parametres>(?:\|[^>]+)*)>/i'
		and preg_match($pattern, $texte, $matches)
	) {
		$has_sommaire = true;
		$texte = preg_replace($pattern, $marqueur, $texte);
		// On récupère le niveau maximal éventuel passé en paramètre
		$niveau_max = (preg_match('/niveau_max=(\d+)/i', $matches['parametres'], $m)) ? $m[1] : '';
	}

	$texte = propre($texte, $connect, $env);

	if (
		!isset($GLOBALS['meta']['sommaire_automatique'])
		or $GLOBALS['meta']['sommaire_automatique'] == 'on'
		or (
			$GLOBALS['meta']['sommaire_automatique'] == 'ondemand'
			and $has_sommaire
		)
	) {
		$texte = sommaire_post_propre($texte, true, false, $niveau_max);
	}
	return $texte;
}

/**
 * evite les transformations typo dans les balises $balises
 * par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
 *
 * @param $texte
 *   $texte a filtrer
 * @param $filtre
 *   le filtre a appliquer pour transformer $texte
 *   si $filtre = false, alors le texte est retourne protege, sans filtre
 * @param $balises
 *   balises concernees par l'echappement
 *   si $balises = '' alors la protection par defaut est sur les balises de _PROTEGE_BLOCS
 *   si $balises = false alors le texte est utilise tel quel
 * @param null|array $args
 *   arguments supplementaires a passer au filtre
 * @return string
 */
function sommaire_filtre_texte_echappe($texte, $filtre, $balises = '', $args = null) {
	if (!strlen($texte)) {
		return '';
	}

	if ($filtre !== false) {
		$fonction = chercher_filtre($filtre, false);
		if (!$fonction) {
			spip_log("sommaire_filtre_texte_echappe() : $filtre() non definie", _LOG_ERREUR);
			return $texte;
		}
		$filtre = $fonction;
	}

	// protection du texte
	if ($balises !== false) {
		if (!strlen($balises)) {
			$balises = _PROTEGE_BLOCS;//'html|code|cadre|frame|script';
		} else {
			$balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
		}
		if (!function_exists('echappe_html')) {
			include_spip('inc/texte_mini');
		}
		$texte = echappe_html($texte, 'FILTRETEXTECHAPPE', true, $balises);
	}
	// retour du texte simplement protege
	if ($filtre === false) {
		return $texte;
	}
	// transformation par $fonction
	if (!$args) {
		$texte = $filtre($texte);
	} else {
		array_unshift($args, $texte);
		$texte = call_user_func_array($filtre, $args);
	}

	// deprotection des balises
	return echappe_retour($texte, 'FILTRETEXTECHAPPE');
}

/**
 * Insère le sommaire dans un texte
 *
 * @uses sommaire_recenser()
 * @uses sommaire_filtrer_niveaux()
 *
 * @param string $texte
 * @param boolean $ajoute
 * @param boolean $sommaire_seul
 * @param int|string $niveau_max
 * @return string
 */
function sommaire_filtre($texte, $ajoute = true, $sommaire_seul = false, $niveau_max = '') {
	$sommaire = sommaire_recenser($texte);

	// le niveau max peut être passé en paramètre (via la balise texte ou squelette)
	// à défaut on prend la valeur enregistrée dans la config
	$niveau_max_config = isset($GLOBALS['meta']['sommaire_niveau_max']) ? $GLOBALS['meta']['sommaire_niveau_max'] : '';
	$niveau_max = (intval($niveau_max) > 0) ? $niveau_max : $niveau_max_config;
	// on filtre les entrées du sommaire selon le niveau max
	$sommaire = sommaire_filtrer_niveaux($sommaire, $niveau_max);

	if ($ajoute or $sommaire_seul) {
		$sommaire = recuperer_fond('modeles/sommaire', array('sommaire' => $sommaire, 'niveau_max' => $niveau_max));
		$sommaire = "<!--sommaire-->$sommaire<!--/sommaire-->";
		if ($sommaire_seul) {
			return $sommaire;
		}
		// On insère le sommaire au niveau du marqueur <!--inserer_sommaire-->
		// Sinon on le place au début du texte
		if ($p = strpos($texte, '<p><!--inserer_sommaire--></p>')) {
			$texte = substr_replace($texte, $sommaire, $p, strlen('<p><!--inserer_sommaire--></p>'));
		} elseif ($p = strpos($texte, '<!--inserer_sommaire-->')) {
			$texte = substr_replace($texte, $sommaire, $p, strlen('<!--inserer_sommaire-->'));
		} else {
			$texte = $sommaire . $texte;
		}
	}

	return $texte;
}

/**
 * Undocumented function
 *
 * @uses sommaire_filtre_texte_echappe
 * @uses sommaire_filtre
 *
 * @param string $texte
 * @param boolean $ajoute
 * @param boolean $sommaire_seul
 * @param int|string $niveau_max
 * @return string
 */
function sommaire_post_propre($texte, $ajoute = true, $sommaire_seul = false, $niveau_max = '') {

	if (strpos($texte, '<h') !== false) {
		$texte = sommaire_filtre_texte_echappe(
			$texte,
			'sommaire_filtre',
			'html|code|cadre|frame|script|acronym|cite',
			array($ajoute, $sommaire_seul, $niveau_max)
		);
	} elseif ($sommaire_seul) {
		return '';
	}
	return $texte;
}

/**
 * Renvoie le sommaire d'une page d'article
 * $page=false reinitialise le compteur interne des ancres
 *
 * @param string $texte
 * @return string
 */
function sommaire_recenser(&$texte) {
	$sommaire = array();
	$ancres_vues = array();

	// traitement des intertitres <hx>
	preg_match_all(",(<h([123456])[^>]*>)(.*)(</h\\2>),Uims", $texte, $matches, PREG_SET_ORDER);
	if (!count($matches)) {
		return $sommaire;
	}

	$debutsommairedejala = strpos($texte, '<!--sommaire-->');
	$finsommairedejala = strpos($texte, '<!--/sommaire-->');

	// trouver le niveau mini des hn qui consitue le niveau 1 du sommaire
	$toplevel = 6;
	foreach ($matches as $m) {
		$toplevel = min($toplevel, $m[2]);
		if ($toplevel==1) {
			break;
		}
	}
	#var_dump($toplevel);

	$currentpos = 0;
	$titleretour = attribut_html(_T('sommaire:titre_retour_sommaire'));

	foreach ($matches as $m) {
		if (($pos = strpos($texte, $m[0], $currentpos)) !==false
			and ($pos < $debutsommairedejala or $pos >= $finsommairedejala)) {
			$titre = $m[3];
			$titre = preg_replace(",</?a\b[^>]*>,Uims", '', $titre);
			$ancre = sommaire_intertitre_ancre($titre, $m, $ancres_vues);
			$ancres_vues[] = $ancre;

			$sommaire[] = array(
				'niveau' => $m[2]-$toplevel+1,
				'titre' => $titre,
				'href' => "#$ancre",
				'id' => "s-$ancre"
			);

			$lien_back = "<a class='sommaire-back' href='#s-$ancre' title='$titleretour'></a>";
			$h = inserer_attribut($m[1], 'id', $ancre).retire_ancres_sommaire($m[3]).$lien_back.$m[4];
			$texte = substr_replace($texte, $h, $pos, strlen($m[0]));
			$currentpos = $pos + strlen($h);
		}
	}

	if (count($sommaire)) {
		// ajouter le nombre de liens en classe sur chaque ancre (masquage CSS)
		$c = 'sommaire-back-'.count($sommaire);
		$texte = str_replace("<a class='sommaire-back'", "<a class='sommaire-back $c'", $texte);
	}

	#var_dump($sommaire);
	return $sommaire;
}

function sommaire_intertitre_ancre($titre, $h, $ancres_vues = array()) {
	// un id sur le hn deja ?
	if ($id = extraire_attribut($h[1], 'id')) {
		return $id;
	}

	// generer une ancre a partir du titre
	$ancre = trim(textebrut($titre));
	$ancre = translitteration($ancre);
	$ancre = couper($ancre, 80);
	$ancre = preg_replace(',\W+,', '-', $ancre);
	$ancre = trim($ancre, '-');
	if (!preg_match(',^[a-z],i', $ancre)) {
		$ancre = "t$ancre";
	}

	if (!in_array($ancre, $ancres_vues)) {
		return $ancre;
	}

	$md5 = substr(md5($titre), 0, 4);
	if (!in_array("$ancre-$md5", $ancres_vues)) {
		return "$ancre-$md5";
	}

	$i = 2;
	while (in_array("$ancre-$i", $ancres_vues)) {
		$i++;
	}
	return "$ancre-$i";
}

/**
 * Retire d'un sommaire les entrées d'une profondeur supérieures à un niveau donné
 *
 * @param array $sommaire
 *     tableau associatif des entrées du sommaire
 * @param int $niveau_max
 *     niveau de profondeur maximal
 * @return array
 *     tableau associatif des entrées du sommaire expurgé de certaines entrées
 */
function sommaire_filtrer_niveaux($sommaire, $niveau_max = '') {

	$niveau_max = intval($niveau_max);
	if ($niveau_max <= 0 or $niveau_max > 5) {
		return $sommaire;
	}

	foreach ($sommaire as $k => $v) {
		if (isset($v['niveau'])
			and is_int($v['niveau'])
			and $v['niveau'] > $niveau_max
		) {
			unset($sommaire[$k]);
		}
	}

	return $sommaire;
}
