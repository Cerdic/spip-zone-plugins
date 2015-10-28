<?php

/**
 * Fonctions et filtres du dictionnaires
 *
 * @package SPIP\Dictionnaires\Fonctions
 */
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Liste les définitions en activité
 * 
 * Récupére un tableau facilement utilisable de toutes les définitions en activité sur le site
 * Une définition active est une définition publié ET dans un dico actif
 *
 * @param bool $purger
 *     true pour vider le cache statique des définitions connues.
 * @return array
 *     Tableau des définitions
 * 	array(
 * 		array(
 * 			'titre' => 'Truc',
 * 			'texte' => 'Ma définition',
 * 			'termes' => array('trucs', 'bidule'), // autres termes qui seront reconnus
 * 			'type' => '' | 'abbr',
 * 		),
 * 		array(
 * 			...
 * 		)
 * 	)
 */
function dictionnaires_lister_definitions($purger=false){
	// Le nom du fichier de cache
	$langue = false;
	if(in_array('spip_definitions',explode(',',(isset($GLOBALS['meta']['multi_objets'])?$GLOBALS['meta']['multi_objets']:'')))){
		$langue = _request('lang') ? _request('lang') : ($GLOBALS['spip_lang'] ? $GLOBALS['spip_lang'] : $GLOBALS['meta']['langue_site']);
		if(!in_array($langue,explode(',', $GLOBALS['meta']['langues_multilingue'])))
			$langue = $GLOBALS['meta']['langue_site'];
		$fichier_cache =  _DIR_CACHE.'definitions_'.$langue.'.txt';
	}
	else{
		$fichier_cache =  _DIR_CACHE.'definitions.txt';
	}

	// Par défaut rien
	static $definitions_actives;
	
	// Si ça contient déjà quelque chose à ce stade, c'est avec le static donc on ne fait rien d'autre
	if (!$purger and $definitions_actives and is_array($definitions_actives)){
		return $definitions_actives;
	}
	else{
		$definitions_actives = array();
	}
	
	// Si on le demande explicitement où si la lecture du fichier ne marche pas, on recalcule
	if (
		$purger == true
		or !lire_fichier($fichier_cache, $definitions_actives)
		or !($definitions_actives = unserialize($definitions_actives))
		or !is_array($definitions_actives)
	){
		$definitions_actives = array();
		
		// On récupère tous les dictionnaires actifs
		$dicos_actifs = sql_allfetsel('id_dictionnaire', 'spip_dictionnaires', 'statut = '. sql_quote('actif'));
		if ($dicos_actifs and is_array($dicos_actifs)){
			$dicos_actifs = array_map('reset', $dicos_actifs);
			$where = array(
					sql_in('id_dictionnaire', $dicos_actifs),
					'statut ='.sql_quote('publie')
				);
			if($langue){
				$where[] = "lang =".sql_quote($langue);
			}
			// À l'intérieur on récupère toutes les définitions publiées
			$definitions_publiees = sql_allfetsel(
				'id_dictionnaire, id_definition, titre, termes, type, casse, texte, url_externe, lang',
				'spip_definitions',
				$where
			);
			
			if ($definitions_publiees and is_array($definitions_publiees)){
				$definitions_actives = $definitions_publiees;
			}
		}
		
		// On passe la liste des définitions actives dans un pipeline
		$definitions_actives = pipeline('lister_definitions', $definitions_actives);

		include_spip('inc/charsets');
		// On traite maintenant le tableau pour ajouter des infos prêtes à l'emploi
		foreach ($definitions_actives as $cle=>$definition){
			// Si les termes sont données en chaine, on coupe avec les virgules
			if (is_string($definition['termes'])){
				$definition['termes'] = array_map('trim', explode(',', $definition['termes']));
			}
			// Si c'est ni une string ni un tableau on met un tableau vide
			elseif (!is_array($definition['termes'])){
				$definition['termes'] = array();
			}
			
			// On ajoute le titre à la liste des termes reconnus
			$definition['termes'][] = $definition['titre'];
			
			// Si c'est une abbréviation, on reconnait automatique une version avec des p.o.i.n.t.s.?
			if ($definition['type'] == 'abbr'){
				$titre = $definition['casse'] ? $definition['titre'] : strtolower($definition['titre']);
				$avec_points = spip_substr($titre,0,1);
				$length = spip_strlen($titre);
				for ($i=1 ; $i<$length ; $i++){
					$avec_points .= '.'.spip_substr($titre,$i,1);
				}
				$definition['termes'][] = $avec_points.'.';
				$definition['termes'][] = $avec_points;
			}
			
			// On nettoie les termes = pas de truc vide, tout en minuscule, pas de doublons
			$definition['termes'] = array_filter($definition['termes']);
			if (!$definition['casse']){
				$definition['termes'] = array_map('strtolower', $definition['termes']);
			}
			$definition['termes'] = array_unique($definition['termes']);
			
			// On génère le masque de recherche
			$definition['masque'] = '{([^\w@\.]|^)('
				.join(
					'|',
					array_map(
						create_function('$mot', 'return str_replace(".", "\.", $mot);'),
						$definition['termes']
					)
				)
				.')(?=([^\w]|\s|&nbsp;|$))}su'.($definition['casse']?'':'i');
			
			// Et voilà
			$definitions_actives[$cle] = $definition;
		}
		
		// Si on a des définitions à mettre en cache, on les écrit
		if ($definitions_actives and is_writeable(_DIR_CACHE)){
			ecrire_fichier($fichier_cache, serialize($definitions_actives));
		}
	}
	return $definitions_actives;
}

/**
 * Lister les définitions par terme.
 * 
 * Chaque clé du tableau est alors un terme reconnu, en minuscule.
 * Une même définition peut donc se trouver plusieurs fois dans la liste, avec différentes clés.
 *
 * @return array Tableau des définitions classé par terme
 */
function dictionnaires_lister_definitions_par_terme(){
	static $definitions_par_terme = array();
	$definitions = dictionnaires_lister_definitions();
	
	if (!$definitions_par_terme and $definitions){
		foreach ($definitions as $definition){
			foreach ($definition['termes'] as $terme){
				$definitions_par_terme[$terme] = $definition;
			}
		}
	}
	
	return $definitions_par_terme;
}

/**
 * Fonction de remplacement par défaut pour les termes trouvés dans les textes
 *
 * @param string $mot
 *     Le mot trouvé
 * @param string $definition
 *     La définition correspondante
 * @return string
 *     Code HTML de remplacement de la définition
 */
function dictionnaires_remplacer_defaut_dist($mot, $definition) {
	$class="";
	if ((!isset($definition['url']) OR !$url = $definition['url']) && (!isset($definition['url_externe']) OR !$url = $definition['url_externe'])) {
		$url = generer_url_entite($definition['id_definition'],'definition');
	}else{
		if(strpos($url,'http') == 0)
			$class="spip_out";
	}
	$class = (strlen($class) > 0) ? " class='$class' " : "";
	return $mot
		.'<sup><a href="'.$url.'"'.$class.'title="'._T('definition:titre_definition').': '
			. couper(trim(attribut_html(supprimer_tags(typo(expanser_liens($definition['texte']))))),80).'">'
		.'?'
		.'</a></sup>';
}

/*
 * Fonction de remplacement par défaut pour les abbréviations trouvées dans les textes
 * Ceci est un EXEMPLE montrant qu'on peut mettre un truc différent pour un type de définition précis
 * Mais ce code est une MAUVAISE PRATIQUE en accessibilité
 * (car seuls les gens avec des yeux valides et un pointeur de souris ont accès à l'information)
 */
#function dictionnaires_remplacer_abbr_dist($mot, $definition){
#	return '<abbr title="'.couper(trim(attribut_html(supprimer_tags(typo($definition['texte'])))),80).'">'.$mot.'</abbr>';
#}


?>
