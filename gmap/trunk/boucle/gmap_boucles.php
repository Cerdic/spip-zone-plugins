<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Boucles et critères introduits par le plugin :
 * - GEOTEST : test si un objet est géolocalisé ou contient des objets géolocalisés
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/gmap_config_utils");
include_spip('inc/gmap_db_utils');


/*
 * La seule boucle à créer est GEOTEST, qui a un comportement particulier.
 * Le plugin introduit d'autres boucles mais qui sont prises en compte directement
 * par SPIP puisqu'elles sont liées à des tables.
 * GEOTEST est un peu particulière car son résultat ne dépend pas des champs SQL
 * mais d'un algorithme qui compte le nombre de points sur un objet et ses 
 * descendants.
 * Dans SPIP, une boucle correspond à une table dans une base de données
 * (cf. ecrire/public/compiler.php), à moins qu'elle ne soit déclarée optionelle
 * (BOUCLE_(TABLE ?)) ou qu'on définisse un nouveau connecteur de base de données.
 * Pour simplifier, GEOTEST est associée à gmap_points_liens (cf. base/gmap_tables.php)
 */

 
/*
 * Outils pour gérer les boucles
 */

// Ordre de préférence des champs id
// Utilisé quand l'objet sur lequel on fait les requêtes n'est pas défini
$GLOBALS['preferred_ids'] = array(
	'id_mot',
	'id_auteur',
	'id_document',
	'id_article',
	'id_breve',
	'id_rubrique',
	'id_secteur'
);

// Recherche d'une boucle de nom prédéfini dans la pile
// Les fonction du core de SPIP :
// - index_pile : recherche un champ dans la pile de boucle, mais ce champs doit être définis dans les tables
// - rindex_pile : recherche une boucle qui a un critère donné
// ... ne sont pas adaptées à ce que je veux faire ici : chercher un nom de boucle particulier car il intègre le champs que
// je veux récupérer.
// Donc je refais une fonction boucle_index_pile qui recherche une boucle d'un nom donné.
// $idb : id de boucle de départ (boucle actuelle)
// $type_boucle : type de la boucle (ARTICLES, GEOTEST)
// $nom_champ : nom du champ recherché
// &$boucles : tableaux des boucles
// $explicite : id d'une boucle de démarrage explicite (autre que $idb)
// $defaut : si TRUE et que rien n'est trouvé dans les boucles, on prend les paramètres
function _gmap_boucle_index_pile($idb, $type_boucle, $nom_champ, &$boucles, $explicite='', $defaut = TRUE)
{
	$i = 0; // nombre de niveaux remontés
	$type_boucle = strtolower($type_boucle);
	$nom_champ = strtolower($nom_champ);
	
	// Remonter d'abord jusqu'à la boucle de nom $explicite
	if (strlen($explicite))
	{
		while (($idb !== $explicite) && ($idb !==''))
		{
			$i++;
			$idb = $boucles[$idb]->id_parent;
		}
	}

	// Puis remonter jusqu'à une boucle sur la table voulue
	while (isset($boucles[$idb]))
	{
		// Si c'est la bonne boucle, renvoyer le champs dans la pile
		// ATTENTION : on n'a aucun moyen de tester si le champ existe bien
		// c'est un pari sur la cohérence du squelette !
		if ($boucles[$idb]->type_requete === $type_boucle)
			return '@$Pile[$SP' . ($i ? "-$i" : "") . '][\'' . $nom_champ . '\']';

		// Sinon on remonte d'un cran
		$idb = $boucles[$idb]->id_parent;
		$i++;
	}

	// esperons qu'il y sera
	if ($defaut)
		return('@$Pile[0][\''. $nom_champ . '\']');
	else
		return NULL;
}

// Recherche de la définition d'un objet dans les boucles englobantes (ou dans les paramètres de la page)
// Renvoie un tableau associatif :
// 'objet' => type d'objet
// 'id_objet' => id de l'objet, NULL s'il n'est pas défini
function _gmap_find_boucle_object($objetDef, &$boucles, $idb, $explicite='', $defaut = TRUE)
{
	// On commence sur la boucle locale
	$i = 0;
	
	// Si une autre boucle est désignée explicitement, l'utiliser comme point de départ
	if (strlen($explicite))
	{
		while (($idb !== $explicite) && ($idb !==''))
		{
			$i++;
			$idb = $boucles[$idb]->id_parent;
		}
	}

	// Remonter les boucles
	// Sur chaque niveau, rechercher un id qui pourrait nous convenir
	while (isset($boucles[$idb]))
	{
		// Si un nom est donné, rechercher seulement celui-là
		if (strlen($objetDef['objet']))
		{
			list ($t, $c) = index_tables_en_pile($idb, 'id_'.$objetDef['objet'], $boucles);
			if ($t)
			{
				// Ajouter le champ à la requête
				if (!in_array($t, $boucles[$idb]->select))
					$boucles[$idb]->select[] = $t;
				
				// Stocker le code qui permet de le récupérer
				$objetDef['id_objet'] = '$Pile[$SP' . ($i ? "-$i" : "") . '][\'' . $c . '\']';
				return $objetDef;
			}
		}
		
		// Sinon tout essayer
		else
		{
			// Parcours des IDs connus
			foreach ($GLOBALS['preferred_ids'] as $idName)
			{
				// Les tables contiennent-elles ce champ ?
				list ($t, $c) = index_tables_en_pile($idb, $idName, $boucles);
				if ($t)
				{
					// Ajouter le champ à la requête
					if (!in_array($t, $boucles[$idb]->select))
						$boucles[$idb]->select[] = $t;
					
					// Stocker le code qui permet de le récupérer
					$objetDef['objet'] = substr($idName, strlen("id_"));
					$objetDef['id_objet'] = '$Pile[$SP' . ($i ? "-$i" : "") . '][\'' . $c . '\']';
					return $objetDef;
				}
			}
		}

		// Sinon on remonte d'un cran
		$idb = $boucles[$idb]->id_parent;
		$i++;
	}

	// On n'a rien trouvé, récupérer dans la requête
	if ($defaut)
	{
		// Si un type est défini, on le prend des paramètres en espérant qu'il sera là
		if (strlen($objetDef['objet']))
		{
			$objetDef['id_objet'] = '@$Pile[0][\'id_'. $objetDef['objet'] . '\']';
			return $objetDef;
		}
		// Sinon on peut essayer de scanner les paramètres...
		else
		{
			// Parcours des IDs connus
			foreach ($GLOBALS['preferred_ids'] as $idName)
			{
				if (_request($idName))
				{
					$objetDef['objet'] = substr($idName, strlen("id_"));
					$objetDef['id_objet'] = _request($idName);
					return $objetDef;
				}
			}
		}
	}
	
	// Rien à faire...
	return NULL;
}

// Récupération des paramètres d'une boucle sous une forme pratique
// Retour :
// Tableau associatif $nom-du-paramètre => $valeur, s'il y en a une
function _gmap_boucle_params(&$boucle)
{
	// La première source d'information est le tableau criteres :
	// criteres[index] :
	// 	op : operation, soit un opérateur (=,!=,>...), soit le texte du critère s'il n'y a pas d'opérateur (id_article)
	// 	not :
	// 	exclus :
	// 	param : tableau des paramètres de l'opération, si c'est un opérateur
	// 		param[index] tableau : (je ne sais pas à quoi sert ce niveau)
	// 			param[index][0] texte :
	// 				param[index][0]->texte : texte du paramètre
	// 				param[index][0]->avant :
	// 				param[index][0]->apres :
	//
	// Le tableau param fournit aussi des infos : les '?' ajoutés derrière les critères
	// param[index] :
	// 	param[index][0] : je n'en connais pas l'utilité
	// 	param[index][1] : tableau
	// 		param[index][1][0] : objet texte
	// 		param[index][1][0]->texte : texte complet du paramètre, sauf dans le cas d'un opérateur (id_article ?)
	// 		param[index][1][0]->avant :
	// 		param[index][1][0]->apres :
	
	// Vérifier qu'il y a des paramètres
	if (count($boucle->criteres) == 0)
		return NULL;
		
	// Init retour
	$params = NULL;
	
	// Parcours des critères
	foreach ($boucle->criteres as $crit)
	{
		// Si c'est un opérateur, il a des paramètres
		if ($crit->param && (count($crit->param) > 0))
		{
			// Pour ce qui est de GMAP, on ne supporte que l'opérayeur d'égalité...
			if ($crit->op != '=')
			{
				spip_log("Boucle ".$boucle->id_boucle." : critère '".$crit->op."' non géré.", "gmap");
				continue;
			}
				
			// Vérifier les données : on doit avoir deux paramètres
			if (!$crit->param || (count($crit->param) != 2))
			{
				spip_log("Boucle ".$boucle->id_boucle." : critère '=' illisible (plus ou moins de deux paramètres)", "gmap");
				continue;
			}
			// chacun des deux paramètres doit contenir un tableau dont le premier élément est un texte
			if ((count($crit->param[0]) != 1) || (count($crit->param[1]) != 1) ||
				($crit->param[0][0]->type !== 'texte') || ($crit->param[1][0]->type !== 'texte'))
			{
				spip_log("Boucle ".$boucle->id_boucle." : critère '=' illisible (paramètres non textuels)", "gmap");
				continue;
			}

			// Ajouter dans le tableau
			if (!$params)
				$params = array();
			$params[$crit->param[0][0]->texte] = $crit->param[1][0]->texte; // on retient le dernier qui est précis
		}
		else // c'est un critère seul
		{
			// Ajouter dans le tableau
			if (!$params)
				$params = array();
			if (!isset($params[$crit->param[0]->texte])) // on retient le premier, qui devait être plus précis
				$params[$crit->op] = TRUE;
		}
	}
	
	// Ici, on n'a pas l'info si un '?' est ajouté derrière le critère, pour ça, il faut parcourir le tableau params...
	foreach ($boucle->param as $param)
	{
		// Vérifier les données 
		if ((count($param) < 2) || (count($param[1]) != 1) ||
		    ($param[1][0]->type !== 'texte'))
			continue;
		
		// Voir s'il y a un ? dans le texte
		$crit = $param[1][0]->texte;
		if (($pos = strpos($crit, '?')) === FALSE)
			continue;
		$crit = trim(substr($crit, 0, $pos));
		
		// Si un critère du même nom existe déjà et qu'il n'avait pas de valeur, le mettre à '?'
		if ($params[$crit] === TRUE)
			$params[$crit] = '?';
	}
	
	return $params;
}

// Recherche de la définition d'un objet dans les paramètres
// Renvoie un tableau associatif :
// 'objet' => type d'objet
// 'id_objet' => id de l'objet, NULL s'il n'est pas défini
function _gmap_find_boucle_param_objet($params, $idb, &$boucles)
{
	// Vérifications
	if (!$params || (count($params) == 0))
		return NULL;

	// On commence par chercher des définitions précises des objets (sans '?')
	// Partir des IDs les plus précis vers les plus englobants, parmi les objets qui
	// peuvent être géolocalisés
	foreach ($GLOBALS['preferred_ids'] as $idName)
	{
		if (!$params[$idName])
			continue;

		// Sauter les définitions hasardeuses
		if ($params[$idName] === '?')
			continue
		
		// On a un objet précis, l'utiliser
		$objetDef = array();
		$objetDef['objet'] = substr($idName, strlen("id_"));
		if (is_string($params[$idName]))
			$objetDef['id_objet'] = $params[$idName];
		else
			$objetDef['id_objet'] = NULL;
			
		// Rechercher cet objet dans la pile
		$objetDef = _gmap_find_boucle_object($objetDef, &$boucles, $idb);
		
		return $objetDef;
	}
	
	// Les paramètres ne contiennent pas de critère précis, seulement des
	// critères avec '?'.
	// Dans ce cas, on les parcours du plus précis au plus large et on va regarder
	// si la pile de boucle contient quelque chose.
	$objetDef = array();
	foreach ($GLOBALS['preferred_ids'] as $idName)
	{
		if (!$params[$idName] || ($params[$idName] !== '?'))
			continue;
		$objetDef['objet'] = substr($idName, strlen("id_"));
		$objetDef['id_objet'] = NULL;
		$objetDef = _gmap_find_boucle_object($objetDef, &$boucles, $idb, '', FALSE);
		if (strlen($objetDef['id_objet']))
			return $objetDef;
	}
	
	// Dernière chance, récupérer dans la requête
	foreach ($GLOBALS['preferred_ids'] as $idName)
	{
		if (!$params[$idName] || ($params[$idName] !== '?'))
			continue;
		if (_request($idName))
		{
			$objetDef['objet'] = substr($idName, strlen("id_"));
			$objetDef['id_objet'] = _request($idName);
			return $objetDef;
		}
	}
	
	return NULL;
}

// Petite bidouille sur la méthode SPIP public/criteres.php/critere_par_joint pour ne pas forcément mettre les quotes
function _gmap_critere_par_joint($table, $champ, &$boucle, $idb, $bQuotes = true)
{
	$t = array_search($table, $boucle->from);
	if (!$t) $t = trouver_jointure_champ($champ, $boucle);
	return !$t ? '' : ($bQuotes ? ("'" . $t . '.' . $champ . "'") : $t . '.' . $champ);
}



/*
 * Boucle GEOTEST et critères associés
 */

// Boucle spécialisée pour tester la présence d'info géographique sur
// un objet ou sur ses descendants
function boucle_GEOTEST_dist($id_boucle, &$boucles)
{
	// Passer le pipeline, au cas où quelqu'un voudrait nous modifier la boucle...
	$boucles[$id_boucle] = pipeline('post_boucle', $boucles[$id_boucle]);

	// Récupérer la boucle
	$boucle = &$boucles[$id_boucle];
	
	// Début de la boucle
	$corps = '
	$SP++;
	$Numrows["'.$id_boucle.'"]["compteur_boucle"] = 0;
	$Numrows["'.$id_boucle.'"]["total"] = 0;
	$Numrows["'.$id_boucle.'"]["grand_total"] = 0;
	$boucle->numrows = FALSE;
	$t0 ="";';
	
	// D'abord tester si le plugin est activé
	// On ne teste pas ici si l'objet est géolocalisable : même s'il a été géolocalisé par le passé, et ne l'est plus,
	// on considère que les positions saisies peuvent être affichées sur une carte. En fait, ce sont aux
	// squelettes de déterminer si une carte doit être affichée sur un objet ou non, pas au paramétrage
	// global.
	if (gmap_est_actif())
	{
		// Récupérer les paramètres et l'objet sur lequel on fait la requête
		$params = _gmap_boucle_params(&$boucle);
		$objetDef = _gmap_find_boucle_param_objet($params, $id_boucle, &$boucles); // id_article, id_rubrique...
		
		// Récupérer les autres paramètres
		$recursive = ($params['recursif'] ? TRUE : FALSE);
	
		// Si une carte peut-être affichée, ajouter le contenu de la boucle
		if ($objetDef)
		{
			$corps .= '
	if (gmap_est_objet_geo("'.$objetDef['objet'].'", '.$objetDef['id_objet'].', '.($recursive?'TRUE':'FALSE').'))
	{
		$boucle->numrows = TRUE;
		$Numrows["'.$id_boucle.'"]["compteur_boucle"]++;
		$t0 .=' . $boucle->return . ';
	}';
		}
	}
	
	// Fin de boucle et retour
	$corps .= '
	$Numrows["'.$id_boucle.'"]["total"] = $Numrows["'.$id_boucle.'"]["grand_total"] = $Numrows["'.$id_boucle.'"]["compteur_boucle"];';
	return $corps 
		. "\n	return \$t0;";
}
function critere_GMAP_POINTS_LIENS_recursif_dist($idb, &$boucles, $crit)
{
}


/*
 * Critères sur GEOPOINTS
 */
 
// Critère {visible} permettant de ne sélectionner que les points visibles
function critere_GMAP_POINTS_visible_dist($idb, &$boucles, $crit)
{
	include_spip('public/criteres');
	$boucle = &$boucles[$idb];
	$visible = critere_par_joint('spip_gmap_types', 'visible', $boucle, $idb);
	$boucle->where[] = array("'='", $visible, $crit->not ? "sql_quote('non')" : "sql_quote('oui')");
}

// Critère {meilleur} permettant de sélectionner le meilleur point pour un objet
/* Requête de test (sélectionne le meilleur point de tous les objets) :
SELECT *
 FROM spip_gmap_points
 JOIN spip_gmap_points_liens ON spip_gmap_points.id_point = spip_gmap_points_liens.id_point
 JOIN spip_gmap_types ON spip_gmap_types.id_type_point = spip_gmap_points.id_type_point
 WHERE spip_gmap_types.priorite =
	(SELECT MIN(typesSub.priorite) AS maxpri
		FROM spip_gmap_points AS pointsSub
		 JOIN spip_gmap_points_liens AS liensSub ON pointsSub.id_point = liensSub.id_point
		 JOIN spip_gmap_types AS typesSub ON typesSub.id_type_point = pointsSub.id_type_point
		WHERE liensSub.objet = spip_gmap_points_liens.objet AND liensSub.id_objet = spip_gmap_points_liens.id_objet
		GROUP BY liensSub.objet, liensSub.id_objet)
*/
function critere_GMAP_POINTS_meilleur_dist($idb, &$boucles, $crit)
{
	include_spip('public/criteres');
	$boucle = &$boucles[$idb];
	$priorite = critere_par_joint('spip_gmap_types', 'priorite', $boucle, $idb);
	$objet = _gmap_critere_par_joint('spip_gmap_points_liens', 'objet', $boucle, $idb, false);
	$id_objet = _gmap_critere_par_joint('spip_gmap_points_liens', 'id_objet', $boucle, $idb, false);
	$subSelect =
		"SELECT MIN(typesSub.priorite)"
		." FROM spip_gmap_points AS pointsSub"
		." JOIN spip_gmap_points_liens AS liensSub ON pointsSub.id_point = liensSub.id_point"
		." JOIN spip_gmap_types AS typesSub ON typesSub.id_type_point = pointsSub.id_type_point"
		." WHERE liensSub.objet = ".$objet." AND liensSub.id_objet = ".$id_objet
		." GROUP BY liensSub.objet, liensSub.id_objet";
	$boucle->where[] = array("'='", $priorite, "'(".$subSelect.")'");
}


?>
