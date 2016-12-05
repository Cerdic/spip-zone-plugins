<?php
/*
 * Plugin Z-core
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

// definition des balises et filtres boites
include_spip('inc/filtres_boites');

// verifier une seule fois que l'on peut utiliser APL si demande
if (defined('_Z_AJAX_PARALLEL_LOAD')) {
	// les pages APL contiennent un <noscript>
	// avec une meta refresh sur self()+var_zapl=non
	// ainsi, les clients sans javascript rechargent la page,
	// se voient poser un cookie, et voient ensuite le site sans APL
	if (_request('var_zapl') == 'non') {
		include_spip('inc/cookie');
		spip_setcookie('no_zapl', $_COOKIE['no_zapl'] = 'no_zapl');
	}
	if (!isset($_COOKIE['no_zapl']) AND !_IS_BOT AND !_request('var_zajax') AND _request('var_mode') !== "debug" AND $_SERVER['REQUEST_METHOD'] == 'GET') {
		define('_Z_AJAX_PARALLEL_LOAD_OK', true);
		$GLOBALS['marqueur'] = isset($GLOBALS['marqueur']) ? $GLOBALS['marqueur'] . ":Zapl" : ":Zapl";
	}
}

/**
 * Inutilise mais permet le chargement de ce fichier avant le decodage des urls
 * et l'utilisation de _DEFINIR_CONTEXTE_TYPE
 *
 * @param  <type> $flux
 *
 * @return <type>
 */
function zcore_declarer_url_objets($flux) {
	return $flux;
}

/**
 * Fonction Page automatique a partir de contenu/page-xx
 *
 * @param array $flux
 *
 * @return array
 */
function zcore_styliser($flux) {
	// dans les futures versions de SPIP on pourra faire simplement un define('_ZPIP',true);
	if (!test_espace_prive() AND !defined('_ZCORE_EXCLURE_PATH')) {
		$styliser_par_z = charger_fonction('styliser_par_z', 'public');
		$flux = $styliser_par_z($flux);
	}

	return $flux;
}

/**
 * Routage automatique de la 404 si le bloc de contenu est vide
 * Seul le bloc principal est pris en compte (le premier de la liste)
 * mais il est possible de personaliser le ou les blocs a prendre en compte pour detecter une 404 :
 * $GLOBALS['z_blocs_404'] = array('content','aside');
 * On ne declenchera alors une 404 que si content/xxx et aside/xxx sont vide tous les deux
 * (attention a ce que la page 404 ait bien un de ces blocs non vide pour eviter une boucle infinie)
 *
 * @param array $flux
 *
 * @return array
 */
function zcore_recuperer_fond($flux) {
	static $empty_count = 0, $is_404 = false;
	static $z_blocs_404, $z_blocs_404_nlength, $z_blocs_404_ncount;

	if ($is_404) {
		if ($flux['args']['fond'] === "structure") {
			$is_404 = false; // pas de risque de reentrance
			$code = "404 Not Found";
			$contexte_inclus = array(
				'erreur' => "",
				'code' => $code,
				'lang' => $GLOBALS['spip_lang'],
			);

			$flux['data'] = evaluer_fond('404', $contexte_inclus);
			$flux['data']['status'] = intval($code); // pas remonte vers la page mais un jour peut etre...
			// du coup on envoie le status a la main
			include_spip("inc/headers");
			http_status(intval($code));
		}
	} elseif (!test_espace_prive()) {
		if (!isset($z_blocs_404)) {
			if (isset($GLOBALS['z_blocs_404'])) {
				$z_blocs_404 = $GLOBALS['z_blocs_404'];
				if (is_array($z_blocs_404) AND count($z_blocs_404) == 1) {
					$z_blocs_404 = reset($z_blocs_404);
				}
			} else {
				if (!function_exists("z_blocs")) {
					$styliser_par_z = charger_fonction('styliser_par_z', 'public');
				}
				$z_blocs = z_blocs(test_espace_prive());
				$z_blocs_404 = reset($z_blocs); // contenu par defaut
			}
			if (is_array($z_blocs_404)) {
				$z_blocs_404_ncount = count($z_blocs_404);
				$z_blocs_404_nlength = array_map('strlen', $z_blocs_404);
			} else {
				$z_blocs_404_ncount = 1;
				$z_blocs_404_nlength = strlen($z_blocs_404);
			}
		}
		$fond = $flux['args']['fond'];
		// verifier rapidement que c'est un des fonds de reference pour la 404 :
		// le fond commende par nomdudossier/
		// le fond n'a pas de / suppelementaires (on est au bon niveau)
		$quick_match = false;
		if (strpos($fond, "/") !== false AND $z_blocs_404_ncount) {
			if ($z_blocs_404_ncount == 1) {
				$quick_match = (strncmp($fond, "$z_blocs_404/", $z_blocs_404_nlength + 1) === 0 AND strpos($fond, "/", $z_blocs_404_nlength + 1) === false);
			} else {
				foreach ($z_blocs_404 as $k => $zb) {
					if (strncmp($fond, "$zb/", $z_blocs_404_nlength[$k] + 1) === 0 AND strpos($fond, "/", $z_blocs_404_nlength[$k] + 1) === false) {
						$quick_match = true;
						break;
					}
				}
			}
		}
		if ($quick_match AND !strlen(trim($flux['data']['texte']))) {
			$empty_count++;
			if ($empty_count >= $z_blocs_404_ncount) {
				$is_404 = true;
			}
		}
	}

	return $flux;
}

/**
 * Surcharger les intertires avant que le core ne les utilise
 * pour y mettre la class h3
 * une seule fois suffit !
 *
 * @param string $flux
 *
 * @return string
 */
function zcore_pre_propre($flux) {
	/*	static $init = false;
		if (!$init){
			$intertitre = $GLOBALS['debut_intertitre'];
			$class = extraire_attribut($GLOBALS['debut_intertitre'],'class');
			$class = ($class ? " $class":"");
			$GLOBALS['debut_intertitre'] = inserer_attribut($GLOBALS['debut_intertitre'], 'class', "h3$class");
			foreach($GLOBALS['spip_raccourcis_typo'] as $k=>$v){
				$GLOBALS['spip_raccourcis_typo'][$k] = str_replace($intertitre,$GLOBALS['debut_intertitre'],$GLOBALS['spip_raccourcis_typo'][$k]);
			}
			$init = true;
		}*/
	return $flux;
}

/**
 * Ajouter les scripts pour getZaplBloc() dans head, directement, sans src
 * Ajouter le inc-insert-head du theme si il existe
 *
 * @param string $flux
 *
 * @return string
 */
function zcore_insert_head($flux) {
	// on utilise recuperer_fond()  car on veut pouvoir s'affranchir de jQl pour agir plus tôt
	if ( defined('_Z_AJAX_PARALLEL_LOAD_OK') AND $fond = recuperer_fond('javascript/zapl.scripts.js', array(), array('ajax' => false) )) {
		$flux = "\n<script type=\"text/javascript\">\n" . compacte($fond,"js") . "</script>\n". $flux;
	}

	if (find_in_path('inc-insert-head.html')) {
		$flux .= recuperer_fond('inc-insert-head', array());
	}

	return $flux;
}

/**
 * Ajouter la definition du bloc contenu pour var_zajax
 *
 * @param string $flux
 *
 * @return string
 */
function zcore_insert_head_css($flux) {
	include_spip('public/styliser_par_z');
	$contenu = z_blocs(false);
	$contenu = reset($contenu);
	$flux = "<script type='text/javascript'>var var_zajax_content='$contenu';</script>" . $flux;

	if (find_in_path('inc-insert-head-css.html')) {
		$flux .= recuperer_fond('inc-insert-head-css', array());
	}

	return $flux;
}

//
// fonction standard de calcul de la balise #INTRODUCTION
// mais retourne toujours dans un <p> comme propre
//
// http://doc.spip.org/@filtre_introduction_dist
if (!function_exists('filtre_introduction')) {
	function filtre_introduction($descriptif, $texte, $longueur, $connect) {
		include_spip('public/composer');
		$texte = filtre_introduction_dist($descriptif, $texte, $longueur, $connect);

		if ($GLOBALS['toujours_paragrapher'] AND strpos($texte, "</p>") === false) // Fermer les paragraphes ; mais ne pas en creer si un seul
		{
			$texte = paragrapher($texte, $GLOBALS['toujours_paragrapher']);
		}

		return $texte;
	}
}

/**
 * Tester la presence sur une page
 *
 * @param  <type> $p
 *
 * @return <type>
 */
if (!function_exists('balise_SI_PAGE_dist')) {
	function balise_SI_PAGE_dist($p) {
		$_page = interprete_argument_balise(1, $p);
		$p->code = "((\n\t" . "((\$zp=$_page) AND isset(\$Pile[0][_SPIP_PAGE]) AND (\$Pile[0][_SPIP_PAGE]==\$zp))\n\t" . "OR (isset(\$Pile[0]['type-page']) AND \$Pile[0]['type-page']==\$zp)\n\t" . "OR (isset(\$Pile[0]['composition']) AND \$Pile[0]['composition']==\$zp AND \$Pile[0]['type-page']=='page'))?' ':'')\n";
		$p->interdire_scripts = false;

		return $p;
	}
}

/**
 * balise #CSS
 *
 */
function balise_CSS_dist($p) {
	$_css = interprete_argument_balise(1, $p);
	if (!$_css) {
		$msg = array('zbug_balise_sans_argument', array('balise' => ' CSS'));
		erreur_squelette($msg, $p);
	} else {
		$p->code = "timestamp(direction_css(trouver_fond($_css)?produire_fond_statique($_css,array('format'=>'css')):find_in_path($_css)))";
	}
	$p->interdire_scripts = false;

	return $p;
}

