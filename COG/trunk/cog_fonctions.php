<?php
/*
 * Plugin COG
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */

/**
 * Retrouver le nom du dossier ou sont stockees les compositions
 * reglage par defaut, ou valeur personalisee via cfg
 *
 * @return string
 */
function cog_chemin_donnee(){
	$config_chemin = 'base/donnee/';
	if (isset($GLOBALS['meta']['cog'])){
		$config = unserialize($GLOBALS['meta']['cog']);
		$config_chemin = rtrim($config['chemin_donnee'],'/').'/';
	}
	return $config_chemin;
}



// Critere {communes} : "l'article est lie a tous les communes demandes"

function critere_communes_dist($idb, &$boucles, $crit,$id_ou_titre=false) {

	$boucle = &$boucles[$idb];


	if (isset($crit->param[0][0])) {
		$score = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucles[$idb]->id_parent);
	} else{
		$score = "'100%'";
    }
    if (isset($crit->param[0][1])){
        $quoi = calculer_liste(array($crit->param[0][1]), array(), $boucles, $boucles[$idb]->id_parent);
        }
    else{
        $quoi = '@$Pile[0]["communes"]';
    }


	$boucle->hash .= '
	// {COMMUNES}
	$prepare_communes = charger_fonction(\'prepare_communes\', \'inc\');
	$communes_where = $prepare_communes('.$quoi.', "'.$boucle->id_table.'", "'.$crit->cond.'", '.$score.', "' . $boucle->sql_serveur . '","'.$id_ou_titre.'");
	';

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$idb]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->where[] = "\n\t\t".'$communes_where';

}

function critere_communes_selon_id_dist($idb, &$boucles, $crit){
    critere_communes_dist($idb, $boucles, $crit,'id');
}
function critere_communes_selon_code_dist($idb, &$boucles, $crit){
    critere_communes_dist($idb, $boucles, $crit,'code');
}


function inc_prepare_communes_dist($communes, $table='articles', $cond=false, $score, $serveur='',$id_ou_code=false) {



    $score = trim($score);
	if (!is_array($communes)
	OR !$communes = array_filter($communes)) {
		// traiter le cas {mots?}
		if ($cond)
			return '';
		else
		// {mots} mais pas de mot dans l'url
			return '0=1';
	}


	$_table = str_replace('spip_', '', table_objet_sql($table));
	$_id_table = id_table_objet($table);
	$where = array();

    //selon le cas, on sélèctionne sur les titres ou sur les id
    if (!$id_ou_titre){
        foreach($communes as $commune) {
            if (preg_match(',^[1-9][0-9]*$,', $commune))
                $id_cog_commune = $commune;
            else
                $id_cog_commune = sql_getfetsel('id_cog_commune', 'spip_cog_communes', 'nom='.sql_quote($commune));
            $where[] = 'id_cog_commune='.sql_quote($id_cog_commune);
        }
    }
	elseif($id_ou_titre == 'id'){
	   foreach($communes as $commune) {
	       $where[] = 'id_cog_commune='.sql_quote($id_cog_commune);
	   }
	}
	elseif($id_ou_titre == 'code'){
	   foreach($communes as $commune) {
	        $id_cog_commune = sql_getfetsel('id_cog_commune', 'spip_cog_communes', 'code='.sql_quote($commune));
            $where[] = 'id_cog_commune='.sql_quote($id_cog_commune);
	   }
	}

	// on analyse la jointure spip_mots_$_table
	// sans regarder spip_mots ni les groupes
	// (=> faire attention si on utilise les mots techniques)

	// si on a un % dans le score, c'est que c'est un %age
	if (substr($score,-1)=='%'){

	   $score = str_replace('%','',$score);
	   $having = ' HAVING SUM(1) >= '.ceil($score/100 * count($where)) ;
	}
	elseif ((0 < $score) and ($score < 1)){
	   $having = ' HAVING SUM(1) >= '.ceil($score * count($where)) ;
	}
	else{
	   $having = ' HAVING SUM(1) >= '. $score;
	   }

	$wh = "$_table.$_id_table IN (
		SELECT id_objet FROM spip_cog_communes_liens WHERE objet=".sql_quote(substr($_table, 0, -1))." AND "
		. join(' OR ', $where)
		. ' GROUP BY id_objet'
		. $having
		. "\n\t)";


	return $wh;
}




// Critere {departements} : "l'article est lie a tous les departements demandes"

function critere_departements_dist($idb, &$boucles, $crit,$id_ou_titre=false) {

	$boucle = &$boucles[$idb];


	if (isset($crit->param[0][0])) {
		$score = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucles[$idb]->id_parent);
	} else{
		$score = "'100%'";
    }
    if (isset($crit->param[0][1])){
        $quoi = calculer_liste(array($crit->param[0][1]), array(), $boucles, $boucles[$idb]->id_parent);
        }
    else{
        $quoi = '@$Pile[0]["departements"]';
    }


	$boucle->hash .= '
	// {departementS}
	$prepare_departements = charger_fonction(\'prepare_departements\', \'inc\');
	$departements_where = $prepare_departements('.$quoi.', "'.$boucle->id_table.'", "'.$crit->cond.'", '.$score.', "' . $boucle->sql_serveur . '","'.$id_ou_titre.'");
	';

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$idb]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->where[] = "\n\t\t".'$departements_where';

}

function critere_departements_selon_id_dist($idb, &$boucles, $crit){
    critere_departements_dist($idb, $boucles, $crit,'id');
}
function critere_departements_selon_code_dist($idb, &$boucles, $crit){
    critere_departements_dist($idb, $boucles, $crit,'code');
}


function inc_prepare_departements_dist($departements, $table='articles', $cond=false, $score, $serveur='',$id_ou_code=false) {



    $score = trim($score);
	if (!is_array($departements)
	OR !$departements = array_filter($departements)) {
		// traiter le cas {mots?}
		if ($cond)
			return '';
		else
		// {mots} mais pas de mot dans l'url
			return '0=1';
	}


	$_table = str_replace('spip_', '', table_objet_sql($table));
	$_id_table = id_table_objet($table);
	$where = array();

   foreach($departements as $departement) {
		$where[] = 'departement = '.sql_quote($departement);
   }


	// on analyse la jointure spip_mots_$_table
	// sans regarder spip_mots ni les groupes
	// (=> faire attention si on utilise les mots techniques)

	// si on a un % dans le score, c'est que c'est un %age
	$having=' HAVING COUNT( DISTINCT concat( id_objet,\'-\', departement ) ) >=';
	if (substr($score,-1)=='%'){

	   $score = str_replace('%','',$score);
	   $having .= ceil($score/100 * count($where)) ;
	}
	elseif ((0 < $score) and ($score < 1)){
	   $having .= ceil($score * count($where)) ;
	}
	else{
	   $having .=  $score;
	   }

	$wh = "$_table.$_id_table IN (
		SELECT id_objet FROM spip_cog_communes_liens l left join spip_cog_communes com on com.id_cog_commune=l.id_cog_commune where objet=".sql_quote(substr($_table, 0, -1))." AND "
		.'('. join(' OR ', $where).')'
		. ' GROUP BY id_objet'
		. $having
		. "\n\t)";

	return $wh;
}
include_spip("cog_config");
function balise_COG_TABLE($p)
{
$p->code = "cog_config_tab_table()";
return $p;
}

function cog_recherche_commune($nom_ville,$code_departement="")
{
	include_spip('base/abstract_sql');
	$where='';
	if (!empty($code_departement))
		$where = 'and departement='.sql_quote($code_departement);
	$items=sql_allfetsel('distinct id_cog_commune as id,trim(concat(MID(article,2,LENGTH(article_majuscule)-2),concat(\' \',nom))) as label','spip_cog_communes','nom_majuscule like '.sql_quote(strtoupper($nom_ville).'%').' or concat(MID(article_majuscule,2,LENGTH(article_majuscule)-2),concat(\' \',nom_majuscule)) like '.sql_quote(strtoupper($nom_ville).'%').$where);
	return $items;
}


function cog_formulaire_recherche_commune($id_cog_commune,$nom_ville,$code_departement="")
{
	include_spip('base/abstract_sql');
	if(intval($id_cog_commune)!=0)
		{
			$item=sql_fetsel('distinct id_cog_commune as id_cog_commune,trim(concat(MID(article,2,LENGTH(article_majuscule)-2),concat(\' \',nom))) as label,code,departement','spip_cog_communes','id_cog_commune='.intval($id_cog_commune));
		}
	else
		{
			$item=cog_recherche_commune_strict($nom_ville,$code_departement);
		}
	if(isset($item['id_cog_commune'])) {
		return $item;
		}
	else {
		$erreurs='La commune de "'.$nom_ville.'" est introuvable';
	}
}


function cog_recherche_id_commune_strict($nom_ville,$code_departement="")
{
	$cog_commune=cog_recherche_commune_strict($nom_ville,$code_departement);
	if(isset($cog_commune['id_cog_commune']))
		return $cog_commune['id_cog_commune'];
	return null;
}


function cog_recherche_commune_strict($nom_ville,$code_departement="")
{
	include_spip('base/abstract_sql');;
	$where='';
	if (!empty($code_departement))
		$where = 'and departement='.sql_quote($code_departement);
	$nom_ville =strtoupper(str_replace(array("œ"),array("oe"),$nom_ville));		
	$item=sql_fetsel('distinct id_cog_commune as id_cog_commune,trim(concat(MID(article,2,LENGTH(article_majuscule)-2),concat(\' \',nom))) as label,code,departement','spip_cog_communes','nom_majuscule = '.sql_quote($nom_ville).' or concat(MID(article_majuscule,2,LENGTH(article_majuscule)-2),concat(\' \',nom_majuscule)) = '.sql_quote($nom_ville).$where);
	return $item;
}


function get_nom_commune($id_cog_commune)
{
    include_spip('base/abstract_sql');
    return sql_getfetsel('trim(concat(MID(article,2,LENGTH(article_majuscule)-2),concat(\' \',nom))) as nom_commune', 'spip_cog_communes','id_cog_commune = '.sql_quote($id_cog_commune));
}



////////////////////////////////////////
// Pour l'espace privé en version 2.1
///////////////////////////////////////
function filtre_cog_bloc_des_raccourcis($bloc,$titre=""){
global $spip_display;
if($titre=='')
$titre=_T('titre_cadre_raccourcis');
include_spip('inc/presentation');
	return "\n"
	. creer_colonne_droite('',true)
	. debut_cadre_enfonce('',true)
	. (($spip_display != 4)
	     ? ("\n<div style='font-size: x-small' class='verdana1'><b>"
		.$titre
		."</b>")
	       : ( "<h3>".$titre."</h3><ul>"))
	. $bloc
	. (($spip_display != 4) ? "</div>" :  "</ul>")
	. fin_cadre_enfonce(true);
}

function filtre_cog_icone_horizontale($lien, $texte="", $fond = "",  $fonction = "",  $javascript='') {
include_spip('inc/presentation');
return icone_horizontale($texte, $lien, $fond, $fonction, false, $javascript);
}





?>
