<?php
/*
 * Plugin GMap
 * G�olocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Boucles et crit�res introduits par le plugin :
 * - GEOTEST : test si un objet est g�olocalis� ou contient des objets g�olocalis�s
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/gmap_config_utils");
include_spip('inc/gmap_db_utils');


/*
 * La seule boucle � cr�er est GEOTEST, qui a un comportement particulier.
 * Le plugin introduit d'autres boucles mais qui sont prises en compte directement
 * par SPIP puisqu'elles sont li�es � des tables.
 * GEOTEST est un peu particuli�re car son r�sultat ne d�pend pas des champs SQL
 * mais d'un algorithme qui compte le nombre de points sur un objet et ses 
 * descendants.
 * Dans SPIP, une boucle correspond � une table dans une base de donn�es
 * (cf. ecrire/public/compiler.php), � moins qu'elle ne soit d�clar�e optionelle
 * (BOUCLE_(TABLE ?)) ou qu'on d�finisse un nouveau connecteur de base de donn�es.
 * Pour simplifier, GEOTEST est associ�e � gmap_points_liens (cf. base/gmap_tables.php)
 */

 
/*
 * Outils pour g�rer les boucles
 */

// Ordre de pr�f�rence des champs id
// Utilis� quand l'objet sur lequel on fait les requ�tes n'est pas d�fini
$GLOBALS['preferred_ids'] = array(
	'id_mot',
	'id_auteur',
	'id_document',
	'id_article',
	'id_breve',
	'id_rubrique',
	'id_secteur'
);

// Recherche d'une boucle de nom pr�d�fini dans la pile
// Les fonction du core de SPIP :
// - index_pile : recherche un champ dans la pile de boucle, mais ce champs doit �tre d�finis dans les tables
// - rindex_pile : recherche une boucle qui a un crit�re donn�
// ... ne sont pas adapt�es � ce que je veux faire ici : chercher un nom de boucle particulier car il int�gre le champs que
// je veux r�cup�rer.
// Donc je refais une fonction boucle_index_pile qui recherche une boucle d'un nom donn�.
// $idb : id de boucle de d�part (boucle actuelle)
// $type_boucle : type de la boucle (ARTICLES, GEOTEST)
// $nom_champ : nom du champ recherch�
// &$boucles : tableaux des boucles
// $explicite : id d'une boucle de d�marrage explicite (autre que $idb)
// $defaut : si TRUE et que rien n'est trouv� dans les boucles, on prend les param�tres
function _gmap_boucle_index_pile($idb, $type_boucle, $nom_champ, &$boucles, $explicite='', $defaut = TRUE)
{
	$i = 0; // nombre de niveaux remont�s
	$type_boucle = strtolower($type_boucle);
	$nom_champ = strtolower($nom_champ);
	
	// Remonter d'abord jusqu'� la boucle de nom $explicite
	if (strlen($explicite))
	{
		while (($idb !== $explicite) && ($idb !==''))
		{
			$i++;
			$idb = $boucles[$idb]->id_parent;
		}
	}

	// Puis remonter jusqu'� une boucle sur la table voulue
	while (isset($boucles[$idb]))
	{
		// Si c'est la bonne boucle, renvoyer le champs dans la pile
		// ATTENTION : on n'a aucun moyen de tester si le champ existe bien
		// c'est un pari sur la coh�rence du squelette !
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

// Recherche de la d�finition d'un objet dans les boucles englobantes (ou dans les param�tres de la page)
// Renvoie un tableau associatif :
// 'objet' => type d'objet
// 'id_objet' => id de l'objet, NULL s'il n'est pas d�fini
function _gmap_find_boucle_object($objetDef, &$boucles, $idb, $explicite='', $defaut = TRUE)
{
	// On commence sur la boucle locale
	$i = 0;
	
	// Si une autre boucle est d�sign�e explicitement, l'utiliser comme point de d�part
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
		// Si un nom est donn�, rechercher seulement celui-l�
		if (strlen($objetDef['objet']))
		{
			list ($t, $c) = index_tables_en_pile($idb, 'id_'.$objetDef['objet'], $boucles);
			if ($t)
			{
				// Ajouter le champ � la requ�te
				if (!in_array($t, $boucles[$idb]->select))
					$boucles[$idb]->select[] = $t;
				
				// Stocker le code qui permet de le r�cup�rer
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
					// Ajouter le champ � la requ�te
					if (!in_array($t, $boucles[$idb]->select))
						$boucles[$idb]->select[] = $t;
					
					// Stocker le code qui permet de le r�cup�rer
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

	// On n'a rien trouv�, r�cup�rer dans la requ�te
	if ($defaut)
	{
		// Si un type est d�fini, on le prend des param�tres en esp�rant qu'il sera l�
		if (strlen($objetDef['objet']))
		{
			$objetDef['id_objet'] = '@$Pile[0][\'id_'. $objetDef['objet'] . '\']';
			return $objetDef;
		}
		// Sinon on peut essayer de scanner les param�tres...
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
	
	// Rien � faire...
	return NULL;
}

// R�cup�ration des param�tres d'une boucle sous une forme pratique
// Retour :
// Tableau associatif $nom-du-param�tre => $valeur, s'il y en a une
function _gmap_boucle_params(&$boucle)
{
	// La premi�re source d'information est le tableau criteres :
	// criteres[index] :
	// 	op : operation, soit un op�rateur (=,!=,>...), soit le texte du crit�re s'il n'y a pas d'op�rateur (id_article)
	// 	not :
	// 	exclus :
	// 	param : tableau des param�tres de l'op�ration, si c'est un op�rateur
	// 		param[index] tableau : (je ne sais pas � quoi sert ce niveau)
	// 			param[index][0] texte :
	// 				param[index][0]->texte : texte du param�tre
	// 				param[index][0]->avant :
	// 				param[index][0]->apres :
	//
	// Le tableau param fournit aussi des infos : les '?' ajout�s derri�re les crit�res
	// param[index] :
	// 	param[index][0] : je n'en connais pas l'utilit�
	// 	param[index][1] : tableau
	// 		param[index][1][0] : objet texte
	// 		param[index][1][0]->texte : texte complet du param�tre, sauf dans le cas d'un op�rateur (id_article ?)
	// 		param[index][1][0]->avant :
	// 		param[index][1][0]->apres :
	
	// V�rifier qu'il y a des param�tres
	if (count($boucle->criteres) == 0)
		return NULL;
		
	// Init retour
	$params = NULL;
	
	// Parcours des crit�res
	foreach ($boucle->criteres as $crit)
	{
		// Si c'est un op�rateur, il a des param�tres
		if ($crit->param && (count($crit->param) > 0))
		{
			// Pour ce qui est de GMAP, on ne supporte que l'op�rayeur d'�galit�...
			if ($crit->op != '=')
			{
				spip_log("Boucle ".$boucle->id_boucle." : crit�re '".$crit->op."' non g�r�.", "gmap");
				continue;
			}
				
			// V�rifier les donn�es : on doit avoir deux param�tres
			if (!$crit->param || (count($crit->param) != 2))
			{
				spip_log("Boucle ".$boucle->id_boucle." : crit�re '=' illisible (plus ou moins de deux param�tres)", "gmap");
				continue;
			}
			// chacun des deux param�tres doit contenir un tableau dont le premier �l�ment est un texte
			if ((count($crit->param[0]) != 1) || (count($crit->param[1]) != 1) ||
				($crit->param[0][0]->type !== 'texte') || ($crit->param[1][0]->type !== 'texte'))
			{
				spip_log("Boucle ".$boucle->id_boucle." : crit�re '=' illisible (param�tres non textuels)", "gmap");
				continue;
			}

			// Ajouter dans le tableau
			if (!$params)
				$params = array();
			$params[$crit->param[0][0]->texte] = $crit->param[1][0]->texte; // on retient le dernier qui est pr�cis
		}
		else // c'est un crit�re seul
		{
			// Ajouter dans le tableau
			if (!$params)
				$params = array();
			if (!isset($params[$crit->param[0]->texte])) // on retient le premier, qui devait �tre plus pr�cis
				$params[$crit->op] = TRUE;
		}
	}
	
	// Ici, on n'a pas l'info si un '?' est ajout� derri�re le crit�re, pour �a, il faut parcourir le tableau params...
	foreach ($boucle->param as $param)
	{
		// V�rifier les donn�es 
		if ((count($param) < 2) || (count($param[1]) != 1) ||
		    ($param[1][0]->type !== 'texte'))
			continue;
		
		// Voir s'il y a un ? dans le texte
		$crit = $param[1][0]->texte;
		if (($pos = strpos($crit, '?')) === FALSE)
			continue;
		$crit = trim(substr($crit, 0, $pos));
		
		// Si un crit�re du m�me nom existe d�j� et qu'il n'avait pas de valeur, le mettre � '?'
		if ($params[$crit] === TRUE)
			$params[$crit] = '?';
	}
	
	return $params;
}

// Recherche de la d�finition d'un objet dans les param�tres
// Renvoie un tableau associatif :
// 'objet' => type d'objet
// 'id_objet' => id de l'objet, NULL s'il n'est pas d�fini
function _gmap_find_boucle_param_objet($params, $idb, &$boucles)
{
	// V�rifications
	if (!$params || (count($params) == 0))
		return NULL;

	// On commence par chercher des d�finitions pr�cises des objets (sans '?')
	// Partir des IDs les plus pr�cis vers les plus englobants, parmi les objets qui
	// peuvent �tre g�olocalis�s
	foreach ($GLOBALS['preferred_ids'] as $idName)
	{
		if (!$params[$idName])
			continue;

		// Sauter les d�finitions hasardeuses
		if ($params[$idName] === '?')
			continue
		
		// On a un objet pr�cis, l'utiliser
		$objetDef = array();
		$objetDef['objet'] = substr($idName, strlen("id_"));
		if (is_string($params[$idName]))
			$objetDef['id_objet'] = $params[$idName];
		else
			$objetDef['id_objet'] = NULL;
			
		// Rechercher cet objet dans la pile
		$objetDef = _gmap_find_boucle_object($objetDef, $boucles, $idb);
		
		return $objetDef;
	}
	
	// Les param�tres ne contiennent pas de crit�re pr�cis, seulement des
	// crit�res avec '?'.
	// Dans ce cas, on les parcours du plus pr�cis au plus large et on va regarder
	// si la pile de boucle contient quelque chose.
	$objetDef = array();
	foreach ($GLOBALS['preferred_ids'] as $idName)
	{
		if (!$params[$idName] || ($params[$idName] !== '?'))
			continue;
		$objetDef['objet'] = substr($idName, strlen("id_"));
		$objetDef['id_objet'] = NULL;
		$objetDef = _gmap_find_boucle_object($objetDef, $boucles, $idb, '', FALSE);
		if (strlen($objetDef['id_objet']))
			return $objetDef;
	}
	
	// Derni�re chance, r�cup�rer dans la requ�te
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

// Petite bidouille sur la m�thode SPIP public/criteres.php/critere_par_joint pour ne pas forc�ment mettre les quotes
function _gmap_critere_par_joint($table, $champ, &$boucle, $idb, $bQuotes = true)
{
	$t = array_search($table, $boucle->from);
	if (!$t) $t = trouver_jointure_champ($champ, $boucle);
	return !$t ? '' : ($bQuotes ? ("'" . $t . '.' . $champ . "'") : $t . '.' . $champ);
}



/*
 * Boucle GEOTEST et crit�res associ�s
 */

// Boucle sp�cialis�e pour tester la pr�sence d'info g�ographique sur
// un objet ou sur ses descendants
function boucle_GEOTEST_dist($id_boucle, &$boucles)
{
	// Passer le pipeline, au cas o� quelqu'un voudrait nous modifier la boucle...
	$boucles[$id_boucle] = pipeline('post_boucle', $boucles[$id_boucle]);

	// R�cup�rer la boucle
	$boucle = &$boucles[$id_boucle];
	
	// D�but de la boucle
	$corps = '
	$SP++;
	$Numrows["'.$id_boucle.'"]["compteur_boucle"] = 0;
	$Numrows["'.$id_boucle.'"]["total"] = 0;
	$Numrows["'.$id_boucle.'"]["grand_total"] = 0;
	$boucle->numrows = FALSE;
	$t0 ="";';
	
	// D'abord tester si le plugin est activ�
	// On ne teste pas ici si l'objet est g�olocalisable : m�me s'il a �t� g�olocalis� par le pass�, et ne l'est plus,
	// on consid�re que les positions saisies peuvent �tre affich�es sur une carte. En fait, ce sont aux
	// squelettes de d�terminer si une carte doit �tre affich�e sur un objet ou non, pas au param�trage
	// global.
	if (gmap_est_actif())
	{
		// R�cup�rer les param�tres et l'objet sur lequel on fait la requ�te
		$params = _gmap_boucle_params($boucle);
		$objetDef = _gmap_find_boucle_param_objet($params, $id_boucle, $boucles); // id_article, id_rubrique...
		
		// R�cup�rer les autres param�tres
		$recursive = ($params['recursif'] ? true : false);
		$visible = ($params['visible'] ? true : false);
	
		// Si une carte peut-�tre affich�e, ajouter le contenu de la boucle
		if ($objetDef)
		{
			$corps .= '
	if (gmap_est_objet_geo("'.$objetDef['objet'].'", '.$objetDef['id_objet'].', '.($visible?'true':'false').', '.($recursive?'true':'false').'))
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
function critere_GMAP_POINTS_LIENS_visible_dist($idb, &$boucles, $crit)
{
}


/*
 * Crit�res sur GEOPOINTS
 */
 
// Crit�re {visible} permettant de ne s�lectionner que les points visibles
function critere_GMAP_POINTS_visible_dist($idb, &$boucles, $crit)
{
	include_spip('public/criteres');
	$boucle = &$boucles[$idb];
	$visible = critere_par_joint('spip_gmap_types', 'visible', $boucle, $idb);
	$boucle->where[] = array("'='", $visible, $crit->not ? "sql_quote('non')" : "sql_quote('oui')");
}

// Crit�re {meilleur} permettant de s�lectionner le meilleur point pour un objet
/* Requ�te de test (s�lectionne le meilleur point de tous les objets) :
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
