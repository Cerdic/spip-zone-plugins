<?php
/**
 *   +----------------------------------+
 *    Nom :   Table des matieres
 *   +----------------------------------+
 *    Date : aout 2004
 *    Auteur :  Mortimer Porte mortimer.pa@free.fr
 *    Modifications: Bertrand Marne bmarne@gmail.com
 *    Modifications: Stéphane Deschamps spip@nota-bene.org
 *    Modifications: Christian Paulus (CP) paladin at quesaco.org
 *      pour les ancres nommées.
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     affiche une table des matières ou génère automatiquement la numérotation des titres.
 *   +-------------------------------------+ 
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/article.php3?id_article=627
 *
 * Petites modifications de Bertrand Marne (mise en plugin)
 * - prise en compte des dièses:
 * les * donnent des titres non numérotés, les # des titres numérotés
 * Mais attention ils sont tous décomptés pareil pour l'arborescence de la table
 * des matières et la numérotation, qu'ils soient numérotés (#) ou non (*).
 *
 * - prise ne compte des titres comme <hx class="spip"> où x>3
 *
 * - encore une modif, très moche: pour permettre de n'afficher que la table,
 * quand on utilise ceci comme un filtre avec unn second paramètre:
 * [(#TEXTE|table_des_matieres{table_seule})]
 * - ajout d'une fonction qui converti les intertitres des enluminures en
 * intertitres compatibles avec cette contrib'
 * 
 * Petites modifications de Stéphane Deschamps
 * - prise en compte des niveaux de titres si ils sont déclarés dans mes_fonctions ou mes_options par $GLOBALS['debut_intertitre']
 *
 * CP: pour les ancres nommées :
 * {@link IntertitresTdm_composer_ancre()}
 * Les ancres sont calculées à partir du contenu du titre.
 * Si deux titres ont le même contenu, un chiffre est ajouté
 * à l'ancre du second (et suivant).
 * Ce chiffre est la position du titre dans la page.
 * Les anciennes ancres 'aNNN' sont conservées pour compatibilité
 * avec les modèles accompagnant le plugin (extrait, ...)
 * 
 */
// $LastChangedBy$
// $LastChangedDate$

function IntertitresTdm_table_des_matieres($texte,$tableseule=false,$url_article="") {
	global $debut_intertitre, $fin_intertitre;
	static $pass = 0;
	$pass++;

	// $GLOBALS['debut_intertitre'] et $GLOBALS['fin_intertitre']
	// n'existent plus en SPIP 3 ?????
	if ( !isset ( $GLOBALS['debut_intertitre'] ) ) {
		$GLOBALS['debut_intertitre'] = '<h3 class="spip">';
		$GLOBALS['fin_intertitre'] = '</h3>';
	}

	// définition de la balise pour les titres des sections %num% sera remplacé 
	// par la profondeur de la section
	// les raccourcis soient remplacés par des headlines (<hx>)
	$css_debut_intertitre = "\n<h%num% class=\"spip\">";
	$css_fin_intertitre = "</h%num%>\n";
	
	// on trouve combien ajouter au level pour être dans le bon niveau de titres quand on génère la balise <hx>
	// sinon par defaut on ajoute 2 pour garder les niveaux du script original
	if($GLOBALS['debut_intertitre']) {
		$find = preg_match( "/(\<h)([0-9])/",$GLOBALS['debut_intertitre'], $matches );
		if($matches) {
			$level_base = $matches[2] -1; // on déduit 1 pour être au bon niveau ensuite : ce sera 1 + nombre d'astérisques trouvées
		}
	}
	if(!isset($level_base)) {
		$level_base = 2;
	}
	
	// on cherche les noms de section commençant par des * et #
	$my_debut_intertitre=trim($GLOBALS['debut_intertitre']); //astuce des trim trouvée là : http://www.spip-contrib.net/Generation-automatique-de#forum383092
	$my_fin_intertitre=trim($GLOBALS['fin_intertitre']);

	// pour que les différents niveaux d'intertitres soient gérés quand on repasse sur le texte dans le cadre d'un filtre avec tableseule
	if ($tableseule) {
		$my_debut_intertitre=trim("\n<h([3-9]) class=\"spip\">");
		$my_fin_intertitre=trim("</h[3-9]>\n");
	}
	
	// on cherche les noms de section commençant par des * et #
	$count = preg_match_all(
		// préserver les liens éventuels présents dans le titre
		// A vérifier le $ref qui semblait s'en servir ?
		//"(($my_debut_intertitre([\\*#]*)(.*?)(<(.*?)>)?$my_fin_intertitre))",
					"(($my_debut_intertitre([\\*#]*)(.*?)$my_fin_intertitre))",
					$texte,
					$matches
	);
	
	//error_log ( 'debut_intertitre: '.$GLOBALS['debut_intertitre']);
	//error_log ( 'fin_intertitre: '.$fin_intertitre);
	
	$table = '';

	//initialisation du compteur
	$cnt[0] = 0;
	
	// initialisation du code de la table des matières
	// s'articule autour d'un <a id=""> et d'un <ul>
	$table = "\n<a id=\"table_des_matieres\" name=\"table_des_matieres\"></a><div id=\"tablematiere\">\n<ul>";
	$lastlevel = 1;
	$cite[''] = '';
	
	// décalle le matching quand on repasse sur le texte avec tableseule
	if ($tableseule) $ajout=1;
	
	// pour chaque titre trouvé
	for ($j=0; $j< $count; $j++) {
		
		$ancre = IntertitresTdm_composer_ancre ($matches[0][$j], $pass, $j);
		
		$level = $matches[2+$ajout][$j];
		
		// quand tableseule, le niveau est "recréé" à partir du nombre du headline (ex <h4> donne niveau 2)
		if ($tableseule) {
			$level=str_repeat("*",$matches[2][$j]-2);
		}
		
		// pour tenir compte des {{{ }}} sans * ou # et donc qu'un name
		// leur soit ajouté, et qu'ils soient quand même dans la table des matières
		if(strlen($level) == 0) $level="*";
		$titre = $matches[3+$ajout][$j];
		$titre_lien = trim(strip_tags($matches[0][$j]));
	
		// Si tableseule alors on vire les <a id=''></a> des titres
		if ($tableseule) {
			$titre=preg_replace("/(<a id=')(.*?)('><\/a>)/","",$titre);
		}
		$ref = $matches[5+$ajout][$j];
		
		// si tableseule alors le $ref correspond au contenu du <a id=''></a>... Je sais pas si ça marche: pas testé ! :o)
		if ($tableseule) {
			preg_match(
				"/(<a id=')(.*?)(' id=')(.*?)('><\/a>)/",
				$matches[3+$ajout][$j],
				$tsmatches
			);
			$ref=$tsmatches[2];
		}
		//IntertitresTdm_log ('ref: '.$ref);
		
		if(strlen($level) == 1) {
		
		//on est au niveau de base
		//on réinitialise les compteurs
		for ($i=1; $i < count($cnt); $i++) {
			$cnt[$i] = 0;
		}
		
		//on génère le titre et le numéros
		$numeros = ++$cnt[0];
		
		// on teste si le level contient des # pour savoir si l'on affiche les
		//numéros avec le titre ou non (#->numéros affichés)
		if (preg_match("/#+/",$level)) $titre = $numeros.'- '.$titre;
		
		} else {
		
			// on est à un niveau plus profond
			// on construit le numéro
			$numeros = $cnt[0].'.';
			for ($i=1; $i < strlen($level)-1; $i++) {
				$numeros .= $cnt[$i].".";
			}
			$numeros = $numeros.(++$cnt[$i]);
			
			//on génère le titre
			//on teste si le level contient des # pour savoir si l'on affiche les
			//numéros avec le titre ou non (#->numéros affichés)
			if(preg_match("/#+/",$level)) $titre = $numeros.'- '.$titre;
		}
		
		//gestion de la liste dans la table
		if($lastlevel < strlen($level)) {
			//on ouvre une sous liste
			$table .= "<ul>\n";
		}
		
		if($lastlevel > strlen($level)) {
		
			//on doit fermer les derniers niveaux
			for ($i=0; $i < ($lastlevel - strlen($level)); $i++) {
				if($i+1==$lastlevel) {
					$table .= "\n</div></ins>";	// derniere fermeture
				} else {
					$table .= "</li>\n</ul>"; 
				}	
			}
		}
		
		if($lastlevel >= strlen($level)) {
			//on doit fermer l'item précédent
			if($cnt[0] > 1 || strlen($level) > 1) {
				$table .= "</li>\n";
			}
		}
		
		//on se rappelle du raccourcis
		$cite[$ref] = $numeros;
		//$table .= "<li><a href=\"$url_article#a$numeros\" title=\"Aller directement &agrave;  	&laquo;&nbsp;".attribut_html($titre)."&nbsp;&raquo;\">$titre</a>";
		$table .= "<li><a href=\"$url_article#$ancre\" title=\""
			. _T('IntertitresTdm:aller_directement_a_', array('titre'=>attribut_html($titre)))
			. "\">$titre_lien</a>"
			;
		
		//on mémorise le niveau de ce titre
		$lastlevel = strlen($level);
		
		//on génère la balise avec le bon style pour le niveau
		//et on ajoute $level_base à $lastlevel pour avoir des <hx> qui commencent à <h{$level_base}>
		$mdebut_intertitre = str_replace('%num%',$lastlevel+$level_base,$css_debut_intertitre);
		$mfin_intertitre = str_replace('%num%',$lastlevel+$level_base,$css_fin_intertitre);
		
		//on remplace le titre dans le texte
		//$texte = str_replace($matches[0][$j],"$mdebut_intertitre<a id='a$numeros' name='a$numeros'></a>$titre$mfin_intertitre",$texte);
		//$texte = str_replace($matches[0][$j],"$mdebut_intertitre<a id='$ancre' name='$ancre'></a><a id='a$numeros' name='a$numeros'></a>$titre$mfin_intertitre",$texte);
		
		
		/**
		 * Remplacer la première occurence. 
		 * Permet d'avoir plusieurs inter-titres au contenu identique.
		 */
		$search = str_replace ("'", '\'', $matches[0][$j]);
		
		if ($ancre && $search && (($pos = strpos($texte, $search)) !== false))
		{
			//IntertitresTdm_log ('search: '.$search.' pos: '.$pos);
			
			$len_search = strlen ($search);
			$s = substr ($texte, 0, $pos);
			$s .= $mdebut_intertitre
				// l'ancre nommée (avec mots du titre)
				. '<a id="'.$ancre.'" name="'.$ancre.'"></a>'
				// conserver les ancres compatibles avec les modèles présents
				// dans le plugin (renvoi, extrait)
				. '<a id="a'.$numeros.'" name="a'.$numeros.'"></a>'
				. $titre
				. $mfin_intertitre;
			$s .= substr ($texte, $pos + $len_search);
			$texte = $s;
		}
	}

   //on finit la table
	for ($i=0; $i < $lastlevel; $i++) {
		$table .= "</li>\n</ul>";		
	}

	//on remplace les raccourcis par les numéros des sections.
	foreach ($cite as $ref => $num) {
		$texte = str_replace("<$ref>","<a href=\"$url_article#$num\">$num</a>",$texte);	
	}

	// ajout d'un div plus propre !
	$table .="\n</div>";

	//on place la table des matières dans le texte
	//si y'a rien, ben on envoie rien !
	if ($cnt[0]==0) $table='';
	
	// Comme la TDM est désormais affichée de manière externe aux articles, si un auteur met #TABLEMATIERES dans son article, celà crée un lien vers la TDM externe, d'où un remplacement de:
	//$texte = str_replace('#TABLEMATIERES',$table,$texte); par:
	$texte = str_replace('#TABLEMATIERES',"<a href=\"#table_des_matieres\" title=\"Aller &agrave; la table des mati&egrave;res de l'article\">Table des mati&egrave;res</a>",$texte);
	
	// si tableseule on ne renvoit que la table, sinon, on renvoie tout
	if ($tableseule) {return $table;} else {return $texte;}
}


if (!defined('_ANCHOR_LEN_MAX')) define('_ANCHOR_LEN_MAX', 35);

/**
 * Calcul de l'ancre.
 * Réalisé à la première passe
 * (ce script est appelé par les balises #TEXTE du squelette)
 * Aux passages suivants, donne l'ancre calculée à la première passe.
 * @author Christian Paulus
 * @param int $pass num du passe (de l'appel du script)
 * @param int $pos position du titre dans le document
 * @return string l'ancre composée
 */
function IntertitresTdm_composer_ancre ($titre, $pass, $pos)
{
	static $ancres_locales = array();
	$ancre = '';
	
	/**
	 * Si l'ancre a déjà été calculée dans
	 * un précedant passage, renvoyer le résultat
	 */
	if (isset($ancres_locales[$pos]))
	{
		$ancre = $ancres_locales[$pos];
		return ($ancre);
	}
	
	/**
	 * Si une ancre est déjà présente, la conserver.
	 * Nota: il y a deux façons de forcer une ancre :
	 * 1/ ajout de <a id="ancre" name="ancre"></a>
	 * 2/ ajout de <monancre> , dans ce cas interprété
	 *    par le compilo SPIP
	 * Dans les deux cas, il faut placer cet ajout
	 *    dans l'inter-titre. Par exemple :
	 *    {{{Mon inter-titre <monancre>}}}
	 */
	if (($ii = strpos ($titre, '<a id=')) !== false)
	{
		$ancre = substr ($titre, $ii + 6);
		// ' ou " ?
		$cc = substr ($ancre, 0, 1);
		$ancre = substr ($ancre, 1);
		$ii = strpos ($ancre, $cc);
		$ancre = substr ($ancre, 0, $ii);
	}
	
	if (!strlen($ancre))
	{
		/**
		 * Traduire le titre en ascii 7 bits
		 */
		$titre = preg_replace('{([^[:alnum:]]+)}',
							  ' ',
							  translitteration(trim(strip_tags($titre))));
		
		/**
		 * Calculer l'ancre à partir du titre
		 */
		foreach (explode(' ', $titre) as $mot)
		{
			/** Ne pas traiter les mots trop courts
			*/
			if (strlen($mot) <= 2) { continue; }
			/**
			 * Sinon, rajouter à la suite de l'ancre
			 */
			$ancre .= $mot.'-';
			/**
			 * Dans la limite acceptable
			 */
			if (strlen($ancre) >= _ANCHOR_LEN_MAX) { break; }
		}
		$ancre = rtrim($ancre, '-');
		
		/**
		 * Si inter-titre vide (c'est possible ?) baptiser 'ancre'
		 */
		if (!strlen($ancre))
		{
			$ancre = 'ancre';
		}
		
		// Les ancres sont case-insensitives
		// Autant tout passer en bdc
		$ancre = strtolower ($ancre);
	}
	
	$ancre_calcule = $ancre.($pos ? '-'.$pos : '');
	$ancres_locales[$pos] = $ancre_calcule;

	return ($ancre_calcule);
}

function IntertitresTdm_log ($msg) {
	spip_log ($msg, 'itdm');
}