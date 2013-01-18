<?php
/*
 * Plugin Zpip
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// demander a SPIP de definir 'type' dans le contexte du premier squelette
define('_DEFINIR_CONTEXTE_TYPE',true);
// verifier une seule fois que l'on peut utiliser APL si demande
if (defined('_Z_AJAX_PARALLEL_LOAD')) {
	if (_request('var_zapl')=='non') {
		include_spip('inc/cookie');
		spip_setcookie('no_zapl',$_COOKIE['no_zapl']='no_zapl');
	}
	if (!isset($_COOKIE['no_zapl'])
	 AND !_IS_BOT
	 AND !_request('var_zajax')
	 AND _request('var_mode')!=="debug"
	 AND $_SERVER['REQUEST_METHOD'] == 'GET'
	 ) {
		define('_Z_AJAX_PARALLEL_LOAD_OK',true);
		$GLOBALS['marqueur'] .= ":Zapl";
	}
}

/**
 * Inutilise mais permet le chargement de ce fichier avant le decodage des urls
 * et l'utilisation de _DEFINIR_CONTEXTE_TYPE
 * @param array $flux
 * @return array
 */
function Z_declarer_url_objets($flux){
	return $flux;
}

/**
 * Fonction Page automatique a partir de contenu/page-xx
 *
 * @param array $flux
 * @return array
 */
function Z_styliser($flux){
	$z_blocs = isset($GLOBALS['z_blocs'])?$GLOBALS['z_blocs']:array('contenu','navigation','extra','head');
	$z_contenu = reset($z_blocs); // contenu par defaut

	$squelette = $flux['data'];
	$fond = $flux['args']['fond'];
	$ext = $flux['args']['ext'];

	// Ajax Parallel loading : ne pas calculer le bloc, mais renvoyer un js qui le loadera an ajax
	if (defined('_Z_AJAX_PARALLEL_LOAD_OK')
	  AND $dir = explode('/',$fond)
	  AND count($dir)==2 // pas un sous repertoire
	  AND $dir = reset($dir)
	  AND in_array($dir,$z_blocs) // verifier deja qu'on est dans un bloc Z
	  AND in_array($dir,explode(',',_Z_AJAX_PARALLEL_LOAD)) // et dans un demande en APL
	  AND $pipe = find_in_path("$dir/z_apl.$ext") // et qui contient le squelette APL
	  ){
		$flux['data'] = substr($pipe, 0, - strlen(".$ext"));
		return $flux;
	}

	// gerer les squelettes non trouves
	// -> router vers les /dist.html
	// ou scaffolding ou page automatique les contenus
	if (!$squelette){

		// Cas de figure où on a déclaré type-composition.html dans un bloc, mais où type.html n'existe pas
		if (isset($flux['args']['contexte']['composition'])
		  AND $dir = explode('/',$fond)
		  AND $dir = reset($dir)
		  AND in_array($dir,$z_blocs)
		  AND $f=find_in_path($fond."-".$flux['args']['contexte']['composition'].".$ext")){
			$flux['data'] = substr($f,0,-strlen(".$ext"));
		}

		// si on est sur un ?page=XX non trouve
		elseif ($flux['args']['contexte'][_SPIP_PAGE] == $fond OR $flux['args']['contexte']['type'] == $fond) {
			// si c'est un objet spip, associe a une table, utiliser le fond homonyme
			if (z_scaffoldable($fond)){
				$flux['data'] = substr(find_in_path("objet.$ext"), 0, - strlen(".$ext"));
			}
			// sinon, brancher sur contenu/page-xx si elle existe
			// si on est sur un ?page=XX non trouve
			elseif ($flux['args']['contexte'][_SPIP_PAGE] == $fond) {
				$base = "$z_contenu/page-".$fond.".".$ext;
				if ($base = find_in_path($base)){
					$flux['data'] = substr(find_in_path("page.$ext"), 0, - strlen(".$ext"));
				}
			}
		}

		// scaffolding :
		// si c'est un fond de contenu d'un objet en base
		// generer un fond automatique a la volee pour les webmestres
		elseif (strncmp($fond, "$z_contenu/", strlen($z_contenu)+1)==0
		  AND include_spip('inc/autoriser')
		  AND isset($GLOBALS['visiteur_session']['id_auteur']) // performance
		  AND autoriser('webmestre')){
		  $type = substr($fond,strlen($z_contenu)+1);
			if ($is = z_scaffoldable($type))
				$flux['data'] = z_scaffolding($type,$is[0],$is[1],$is[2],$ext);
		}
		
		// sinon, si on demande un fond non trouve dans un des autres blocs
		// et si il y a bien un contenu correspondant ou scaffoldable
		// se rabbatre sur le dist.html du bloc concerne
		else{
			if ( $dir = explode('/',$fond)
			  AND $dir = reset($dir)
			  AND $dir !== $z_contenu
			  AND in_array($dir,$z_blocs)){
				$type = substr($fond,strlen("$dir/"));
				if (find_in_path("$z_contenu/$type.$ext") OR z_scaffoldable($type))
					$flux['data'] = substr(find_in_path("$dir/dist.$ext"), 0, - strlen(".$ext"));
			}
		}
		$squelette = $flux['data'];
	}
	if ($fond=='body' AND substr($squelette,-strlen($fond))==$fond){
		if (isset($flux['args']['contexte']['type'])
		  AND (
			(isset($flux['args']['contexte']['composition'])
			AND file_exists(($f=$squelette."-".$flux['args']['contexte']['type']."-".$flux['args']['contexte']['composition']).".$ext"))
			OR
			file_exists(($f=$squelette."-".$flux['args']['contexte']['type']).".$ext")
		  ))
			$flux['data'] = $f;
	}
	// chercher le fond correspondant a la composition
	elseif (isset($flux['args']['contexte']['composition'])
	  AND substr($squelette,-strlen($fond))==$fond
	  AND $dir = explode('/',$fond)
	  AND $dir = reset($dir)
	  AND in_array($dir,$z_blocs)
	  AND $f=find_in_path($fond."-".$flux['args']['contexte']['composition'].".$ext")){
		$flux['data'] = substr($f,0,-strlen(".$ext"));
	}
	return $flux;
}


/**
 * Tester si un type est scaffoldable
 * cad si il correspond bien a un objet en base
 * 
 * @staticvar array $scaffoldable
 * @param string $type
 * @return bool
 */
function z_scaffoldable($type){
	static $scaffoldable = array();
	if (isset($scaffoldable[$type]))
		return $scaffoldable[$type];
	if (preg_match(',[^\w],',$type))
		return $scaffoldable[$type] = false;
	if ($table = table_objet($type)
	  AND $type == objet_type($table)
	  AND $trouver_table = charger_fonction('trouver_table','base')
	  AND
		($desc = $trouver_table($table)
		OR $desc = $trouver_table($table_sql = $GLOBALS['table_prefix']."_$table"))
		)
		return $scaffoldable[$type] = array($table,$desc['table'],$desc);
	else
		return $scaffoldable[$type] = false;
}


/**
 * Generer a la volee un fond a partir d'une table de contenu
 *
 * @param string $type
 * @param string $table
 * @param string $table_sql
 * @param array $desc
 * @param string $ext
 * @return string
 */
function z_scaffolding($type,$table,$table_sql,$desc,$ext){
	include_spip('public/interfaces');
	$primary = id_table_objet($type);
	if (!$primary AND isset($desc['key']["PRIMARY KEY"])){
		$primary = $desc['key']["PRIMARY KEY"];
	}

	// reperer un titre
	$titre = 'titre';
	if (isset($GLOBALS['table_titre'][$table])){
		$titre = explode(' ',$GLOBALS['table_titre'][$table]);
		$titre = explode(',',reset($titre));
		$titre = reset($titre);
	}
	if (isset($desc['field'][$titre])){
		unset($desc['field'][$titre]);
		$titre="<h1 class='h1 #EDIT{titre}'>#".strtoupper($titre)."</h1>";
	}
	else $titre="";

	// reperer une date
	$date = "date";
	if (isset($GLOBALS['table_date'][$table]))
		$date = $GLOBALS['table_date'][$table];
	if (isset($desc['field'][$date])){
		unset($desc['field'][$date]);
		$date = strtoupper($date);
		$date="<p class='info-publi'>[(#$date|nom_jour) ][(#$date|affdate)][, <span class='auteurs'><:par_auteur:> (#LESAUTEURS)</span>]</p>";
	}
	else $date = "";

	$content = array();
	foreach($desc['field'] as $champ=>$z){
		if (!in_array($champ,array('maj','statut','idx',$primary))){
			$content[] = "[<div><strong>$champ</strong><div class='#EDIT{".$champ."} $champ'>(#".strtoupper($champ)."|image_reduire{500,0})</div></div>]";
		}
	}
	$content = implode("\n\t",$content);

	$scaffold = "#CACHE{0}
<BOUCLE_contenu($table_sql){".$primary."}>
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

	$dir = sous_repertoire(_DIR_CACHE,"scaffold",false);
	$dir = sous_repertoire($dir,"contenu",false);
	$f = $dir."$type";
	ecrire_fichier("$f.$ext",$scaffold);
	return $f;
}



/**
 * Surcharger les intertires avant que le core ne les utilise
 * pour y mettre la class h3
 * une seule fois suffit !
 *
 * @param string $flux
 * @return string
 */
function Z_pre_propre($flux){
	static $init = false;
	if (!$init){
		if (isset($GLOBALS['debut_intertitre']) and $GLOBALS['debut_intertitre']){
			$intertitre = $GLOBALS['debut_intertitre'];
			$class = extraire_attribut($GLOBALS['debut_intertitre'],'class');
			$class = ($class ? " $class":"");
			$GLOBALS['debut_intertitre'] = inserer_attribut($GLOBALS['debut_intertitre'], 'class', "h3$class");
			foreach($GLOBALS['spip_raccourcis_typo'] as $k=>$v){
				$GLOBALS['spip_raccourcis_typo'][$k] = str_replace($intertitre,$GLOBALS['debut_intertitre'],$GLOBALS['spip_raccourcis_typo'][$k]);
			}
		}
		else {
			$GLOBALS['debut_intertitre'] = '<h3 class="h3 spip">';
			$GLOBALS['fin_intertitre'] = '</h3>';
		}
		$init = true;
	}
	return $flux;
}

/**
 * Ajouter le inc-insert-head du theme si il existe
 *
 * @param string $flux
 * @return string
 */
function Z_insert_head($flux){
	if (find_in_path('inc-insert-head.html')){
		$flux .= recuperer_fond('inc-insert-head',array());
	}
	return $flux;
}

//
// fonction standard de calcul de la balise #INTRODUCTION
// mais retourne toujours dans un <p> comme propre
//
// http://doc.spip.org/@filtre_introduction_dist
if (!function_exists('filtre_introduction')){ // securite
function filtre_introduction($descriptif, $texte, $longueur, $connect) {
	include_spip('public/composer');
	$texte = filtre_introduction_dist($descriptif, $texte, $longueur, $connect);

	if ($GLOBALS['toujours_paragrapher'] AND strpos($texte,"</p>")===FALSE)
		// Fermer les paragraphes ; mais ne pas en creer si un seul
		$texte = paragrapher($texte, $GLOBALS['toujours_paragrapher']);

	return $texte;
}
}

/**
 * Tester la presence sur une page
 * @param object $p
 * @return object
 */
if (!function_exists('balise_SI_PAGE_dist')){
function balise_SI_PAGE_dist($p) {
	$_page = interprete_argument_balise(1,$p);
	$p->code = "(((\$Pile[0][_SPIP_PAGE]==(\$zp=$_page)) OR (\$Pile[0]['composition']==\$zp AND \$Pile[0]['type']=='page'))?' ':'')";
	$p->interdire_scripts = false;
	return $p;
}
}
?>
