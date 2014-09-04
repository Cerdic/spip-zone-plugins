<?php
/**
 * Fonctions utiles au plugin Accès Restreint Partiel
 *
 * @plugin     Accès Restreint Partiel
 * @copyright  2014
 * @author     Bruno Caillard
 * @licence    GNU/GPL
 * @package    SPIP\Arp\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");

/*
 * Un fichier de fonctions permet de définir des éléments
 * systématiquement chargés lors du calcul des squelettes.
 *
 * Il peut par exemple définir des filtres, critères, balises, …
 * 
 */
 
/* Fonction globale de filtrage, appelée par table_des_traitements
Elle vérifie si l'auteur à les droits d'accès à l'article
lance les filtres adéquats en fonction des mots-clé présents dans l'article
*/
 
function arp_filtrage($texte, $connect, $pile){
	$id_rubrique = $pile['id_rubrique'];
	$id_article = $pile['id_article'];
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	$change = false;
//echo "<br>id_auteur=$id_auteur";
	if (accesrestreint_article_restreint($id_article, $id_auteur)) // TODO: enlever le ! après test
	{
		$zone = accesrestreint_zones_rubrique_et_hierarchie($id_rubrique);
//krumo($zone);
		$zone = $zone[0];
//echo "<br>zone=$zone";

		// Le visiteur n'a pas le droit d'accès à cet article
		// On vérifie quel filtre doit-être appliqué à cet article.
		// De plus prioritaire au moins
		//(-------------------------------------------------------

		// 1 - Y'a t'il un tag <couper_ici> dans le corps du #TEXTE ?
		//-------------------------------------------------------
		$pos = strpos($texte, '<couper_ici>');
		if ($pos>0)
		{
			$texte = substr($texte, 0, $pos);
			$change = true;
		}
		else
		{
			// 2 - Y'a t'il des mots-clé arp_regle_(N) ?
			//--------------------------------------
			$s = spip_query("SELECT id_mot,titre FROM spip_mots"); // Met tous les mots-clé existant dans un tableau
			while ($r = sql_fetch($s)) $array_mot[$r['id_mot']] = $r['titre'];

			$s = spip_query("SELECT id_mot FROM spip_mots_liens WHERE objet='article' AND id_objet=".$id_article);
			$mot_cle = false;
			while ($r = sql_fetch($s)) // pour chaque mot-clé affecté à cet article
			{
				$mot = $array_mot[$r['id_mot']];

				if (preg_match('/^arp_regle_(\d*)$/', $mot, $match)) // Le mot-clé est-il de la forme arp_regle_(N) ?
				{
					$mot_cle = true;
					$nr = $match[1]; // Numéro de règle
					$arp_nregle = lire_config("arp/arp_nregle");
					
					if ($nr > $arp_nregle)
					{
						spip_log("ARP: la règle demandée ($nr) dépasse le nombre de règle programmée ($arp_nregle)", _LOG_AVERTISSEMENT);
						return NULL; // Par précaution, on supprime tout le texte
					}

					$filtre = lire_config("arp/arp_regle_$nr");
					$param = lire_config("arp/arp_filtre_param_$nr");
					
					if (!function_exists($filtre))
					{
						spip_log("ARP: le filtre demandé ($filtre) n\'existe pas.", _LOG_AVERTISSEMENT);
						return NULL; // Par précaution, on supprime tout le texte
					}
					
					$texte = $filtre($texte, $change, $param);
//echo "<br>Règle $mot à appliquer, Regle#=$nr, Nb de régle définie=$arp_nregle, regle=$regle, param=$param";
				}
			}

			if (!$mot_cle) // 3 - Pas de mot-clé de la forme arp_regle_(N) ?
			{
				// 3 - Y'a t'il une règle définie pour la zone en cours ?
				$arp_regle = lire_config("arp/arp_regle_zone_$zone");
				if (empty($arp_regle))
				{
					//  4 - on prend le traitement par défaut
					//-------------------------------------
	//echo "<br>Traitement par défaut";
					$arp_regle = lire_config("arp/arp_regle_defaut");
					if (empty($arp_regle))
					{
						spip_log("ARP: la règle par défaut n\'est pas définie.", _LOG_AVERTISSEMENT);
						return NULL; // Par précaution, on supprime tout le texte
					}
				}

				preg_match('/^arp_regle_(\d*)$/', $arp_regle, $match);
				$nr = $match[1]; // Numéro de règle

				$filtre = lire_config("arp/arp_regle_$nr");
				$param = lire_config("arp/arp_filtre_param_$nr");
				
				if (!function_exists($filtre))
				{
					spip_log("ARP: le filtre demandé ($filtre) n\'existe pas.", _LOG_AVERTISSEMENT);
					return NULL; // Par précaution, on supprime tout le texte
				}
				
				$texte = $filtre($texte, $change, $param);
			}
		}
	}

	$texte_avant = lire_config("arp/arp_texte_avant_$zone");
	if (empty($texte_avant)) $texte_avant = lire_config('arp/arp_texte_avant');
	$texte_apres = lire_config("arp/arp_texte_apres_$zone");
	if (empty($texte_apres)) $texte_apres = lire_config('arp/arp_texte_apres');

	if ($change) $texte = $texte_avant.$texte.$texte_apres;
	return $texte;
}


/* Filtre: arp_filtre_ncar
Paramètre :  nb de car. à conserver
Ne conserve que $param premiers caractères

L'algo essaye d'éviter de couper au milieu de certaines balises <> [] {}
*/
function arp_filtre_ncar($texte, &$change, $param='')
{
	if (!is_numeric($param))
	{
		spip_log("ARP:arp_filtre_ncar: le paramètre est incorrect $param).", _LOG_AVERTISSEMENT);
		$change = true;
		return NULL; // Par précaution, on supprime tout le texte
	}

	// Recherches des balises qu'il faut éviter de couper et remplacement  par un caractère invisible et insécable
	//------------------------------------------------------------------------------------------------------------

	// Les <quote>...</quote>
	// TODO: il faudrait pouvoir généraliser pour <truc>...</truc>
	preg_match_all('/<quote>.*<\/quote>/iUms', $texte, $balises_quote);
	$texte = preg_replace('/<quote>.*<\/quote>/iUms', chr(27), $texte);
//krumo($balises_quote);
//krumo($texte);

	// Les balises HTML et modèles
	preg_match_all('/<.*>/iUms', $texte, $balises_html);
	$texte = preg_replace('/<.*>/iUms', chr(28), $texte);
//krumo($balises_html);
//krumo($texte);

	 // Les accolades (intertitres, gras, etc.)
	preg_match_all('/{.*(}}}}|}}}|}}|})/iUms', $texte, $balises_accolade);
	$texte = preg_replace('/{.*(}}}}|}}}|}}|})/iUms', chr(29), $texte);

	// Les crochets (liens, crayon couleur)
	preg_match_all('/\[.*\]/iUms', $texte, $balises_crochet);
	$texte = preg_replace('/\[.*\]/iUms', chr(30), $texte);


	$texte = substr($texte, 0, $param); // On coupe


	// Restitution des balises
	//------------------------
	if (count($balises_quote[0]) > 0) foreach($balises_quote[0] as $value) $texte = preg_replace('/'.chr(27).'/', $value, $texte, 1);
	if (count($balises_html[0]) > 0) foreach($balises_html[0] as $value) $texte = preg_replace('/'.chr(28).'/', $value, $texte, 1);
//krumo($balises_html[0]);
//krumo($texte);
	if (count($balises_accolade[0]) > 0) foreach($balises_accolade[0] as $value) $texte = preg_replace('/'.chr(29).'/', $value, $texte, 1);
	if (count($balises_crochet[0]) > 0) foreach($balises_crochet[0] as $value) $texte = preg_replace('/'.chr(30).'/', $value, $texte, 1);


	$change = true;
	return $texte; 
}

/* Filtre: arp_filtre_pourcentage
Paramètre :  % de car. à conserver
Ne conserve que $param% premiers caractères

Le résultat final n'est peut être pas tout à fait exact par rapport au % demandé.
En effet, pour calculer le nb de car. à couper, on tient compte des balises,
et pour la découpe, ces balises ne comptent que pour 1 chacune.
*/
function arp_filtre_pourcentage($texte, &$change, $param='')
{
	if (!is_numeric($param) or ($param > 100))
	{
		spip_log("ARP:arp_filtre_pourcentage: le paramètre est incorrect $param).", _LOG_AVERTISSEMENT);
		$change = true;
		return NULL; // Par précaution, on supprime tout le texte
	}

	$nbc = strlen($texte) * $param / 100;
//krumo($nbc);
	$texte = arp_filtre_ncar($texte, $change, $nbc);
//krumo($texte);

	return $texte;
}

/* Filtre: arp_filtre_nintertitre
Paramètre :  N
Coupe juste avant le Nième intertitre
En d'autres termes, ne conserve que les N-1 ième intertitre et leur texte
*/
function arp_filtre_nintertitre($texte, &$change, $param='')
{
	if (!is_numeric($param))
	{
		spip_log("ARP:arp_filtre_nintertitre: le paramètre est incorrect $param).", _LOG_AVERTISSEMENT);
		$change = true;
		return NULL; // Par précaution, on supprime tout le texte
	}

	$t = explode('{{{', $texte, $param+1);
	
	if (count($t) == 1) // Un seul élément ? c'est qu'il n'y a pas d'intertitre
	{
		$change = false;
	}
	else
	{
//krumo($t);
		$texte = $t[0];
		for($i = 1; $i < $param; $i++) $texte .= '{{{'.$t[$i];
//krumo($texte);
	
		$change = true;
	}
	
	return $texte; 
}

/* Filtre: arp_filtre_que_intertitre
Paramètre :  le texte à mettre après un intertitre.
Ne laisse que les intertitres et remplace le texte entre les intertitres.
*/
function arp_filtre_que_intertitre($texte, &$change, $param='')
{
	preg_match_all('/{{{.*}}}/', $texte, $match);
	$texte = NULL;
	foreach($match[0] as $key=>$value)
	{
		$texte .= $value."\r\n".$param;
	}

//krumo($match);
//krumo($texte);
	$change = true;
	return $texte; 
}


/* Filtre: arp_filtre_tout
Paramètre :  aucun
Tout le texte est filtré, plus aucun affichage
*/
function arp_filtre_tout($texte, &$change, $param='')
{
	$change = true;
	return NULL; 
}

/* Filtre: arp_filtre_rien
Paramètre :  aucun
Rien n'est filtré, affichage du texte tel que
*/
function arp_filtre_rien($texte, &$change, $param='')
{
	$change = false;
	return $texte; 
}

/* Filtre: liste_intertitre, appelé par le modèle du même nom
Renvoie un tableau contenant les intertitres
*/
function liste_intertitre($texte)
{
	preg_match_all('/{{{([^{}]*)}}}/', $texte, $match);
//krumo($match);
	$t = array();
	foreach($match[1] as $value)
	{
		$t[] = $value;
	}
//krumo($t);
	return $t;

}
?>