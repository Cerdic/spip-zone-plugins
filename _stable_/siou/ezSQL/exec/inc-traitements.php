<?php
/*
    This file is part of ezSQL.

    ezSQL is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    ezSQL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with SIOU; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
    Copyright 2007, 2008 - Ghislain VLAVONOU, Yannick EDAHE, Cedric PROTIERE
*/

  /*****                                                                *****
   ***** Fonctions recuperees du projet SIOU pour rendre ezSQL autonome *****
   *****                                                                *****/

/**
 * rend une chaine compatible url-rewriting (copier/coller de getRewriteString dans inc-html.php)
 *
 * @see http://www.php.net/manual/en/function.strtr.php#51862
 * @param string $sString : chaine a traiter
 * @return string
 */
function ezGetRewriteString($sString) {
	$string    = htmlentities(strtolower($sString));
	$string    = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
	$string    = preg_replace("/([^a-z0-9]+)/", "-", html_entity_decode($string));
	$string    = trim($string, "-");
	//echo "$sString&rarr;$string<br>";
	return $string;
}

/**
 * Supprime les accents et enleve les espaces inutiles (copier/coller de supprimeAccents dans inc-html.php)
 * 
 * @param string str : chaine a traiter 
 * @param string $stripette : true (par defaut) s'il faut remplacer "-" et "/" par " - " et " / "
 * @return string
 */
function ezSupprimeAccents($str,$stripette=true) {
	if($stripette) {
		$str=str_replace("-"," - ",$str);
		$str=str_replace("/"," / ",$str);
	}
	//$str=ereg_replace('^[:blank:]+$',' ',$str);
	while(substr_count($str,'  ')>0)
	$str=str_replace('  ',' ',$str);
	$str=utf8_decode($str);
	$str = strtr($str,"\xC0\xC1\xC2\xC3\xC4\xC5\xC6","AAAAAAA");
	$str = strtr($str,"\xC7","C");
	$str = strtr($str,"\xC8\xC9\xCA\xCB","EEEE");
	$str = strtr($str,"\xCC\xCD\xCE\xCF","IIII");
	$str = strtr($str,"\xD1","N");
	$str = strtr($str,"\xD2\xD3\xD4\xD5\xD6\xD8","OOOOOO");
	$str = strtr($str,"\xDD","Y");
	$str = strtr($str,"\xDF","S");
	$str = strtr($str,"\xE0\xE1\xE2\xE3\xE4\xE5\xE6","aaaaaaa");
	$str = strtr($str,"\xE7","c");
	$str = strtr($str,"\xE8\xE9\xEA\xEB","eeee");
	$str = strtr($str,"\xEC\xED\xEE\xEF","iiii");
	$str = strtr($str,"\xF1","n");
	$str = strtr($str,"\xF2\xF3\xF4\xF5\xF6\xF8","oooooo");
	$str = strtr($str,"\xF9\xFA\xFB\xFC","uuuu");
	$str = strtr($str,"\xFD\xFF","yy");
	return trim($str);
}


/**
 * Affiche une table html (copier coller de odb_html_table dans inc-html.php du projet siou)
 * 
 * @param string $titre : titre du tableau
 * @param array $tbody : tableau de lignes (&lt;tr&gt;...&lt;/tr&gt;)) sans les &lt;tr&gt;
 * @param strin $thead : tableau de lignes de &lt;th&gt; a mettre au debut ('' si aucun)
 * @param string $icone : nom de fichier de l'icone (cf ../dist/images/)
 * @return string : table HTML
 */ 
function ez_html_table($titre,$tbody,$thead='',$icone='vignette-24.png') {
	$isMSIE=eregi('msie',$_SERVER['HTTP_USER_AGENT'])>0;

	$wrapper=$isMSIE ? 'wrapper.php?file=':'';

	$ret="<div class='liste'>\n"
   . "   <div style='position: relative;'>\n"
   . "      <div style='position: absolute; top: -12px; left: 3px;'><img src='../dist/images/$wrapper$icone' alt=''  /></div>\n"
	. "      <div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n"
   . "         <b>$titre</b>\n"
	. "      </div>\n"
   . "   </div>\n"
   . "   <table id='".ezGetRewriteString(ezSupprimeAccents(strip_tags(substr(html_entity_decode(ez_propre($titre)),0,20))))."' width='100%' cellpadding='2' cellspacing='0' border='0' class='spip'>\n"
   ;
   if($thead!=='') {
   	$ret.="<thead>\n";
   	if(is_array($thead))
   		foreach($thead as $ligne)
   			$ret.="\t<tr $js>\n\t\t$ligne\n\t</tr>\n";
   	else $ret.="\t<tr $js>\n\t\t$thead\n\t</tr>\n";
   	$ret.="</thead>\n";
	}
	$ret.="<tbody>\n";

	//$js=$isMSIE ? "onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"" : '';
	$js="onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"";
	if(is_array($tbody))
		foreach($tbody as $ligne)
			$ret.="\t<tr class='tr_liste' $js>\n\t\t$ligne\n\t</tr>\n";
	$ret.="</tbody>\n";
	$ret .= "   </table>\n</div>\n";

	return $ret;
}

/** Execute une requete sql (copier coller de odb_query dans inc-odb.php du projet siou)
 *
 * @param string $sql : code SQL a executer
 * @param string $fichier : nom du fichier (par exemple, passer __FILE__)
 * @param int $ligne : ligne (passer __LINE__)
 * @param string $obsc : texte a dissimuler (mot de passe, par exemple)
 * @return resource : resultset correspondant
 */
function ez_query($sql,$fichier,$ligne,$obsc='****') {
	$cherche='/plugins/';
	if(substr_count($sql,'DECODE(')>0 && $obsc=='****') {
		$tmp=stristr($sql,'decode(');
		$tmp=substr($tmp,0,strpos($tmp,')'));
		list($rien,$obsc)=explode(',',$tmp);
		$obsc=trim(str_replace(array('\'','"'),'',$obsc));
	}
	$fichier=substr($fichier,strpos($fichier,$cherche));
	$result = mysql_query($sql) or die("<div style='margin:5px;border:1px outset red;background-color:#ddf;'>"
		."<div style='border:1px none red;background-color:#bbf;'>".KO." - Erreur dans la requete</div><pre>"
		.wordwrap(str_replace($obsc,'****',$sql),65)
		."</pre><small>$fichier<b>[$ligne]</b></small><br/><div style='border:1px none red;background-color:#bbf;'>"
		.htmlentities(str_replace($obsc,'****',mysql_error()))."</div></div>");
	//echo "<br/>$sql (<b>$fichier</b>:$ligne)";
	return $result;
}

/**
 * Nettoie une chaine de caracteres (copier coller de odb_propre dans inc-odb.php du projet siou)
 *
 * @param string $str
 * @return string
 */
function ez_propre($str) {
	//$str=preg_replace('/\s\s+/',' ',$str);
	$str=str_replace(array("\r","\n","\t",'  '),' ',trim($str));
	$str=str_replace(' - ','-',$str);
	return trim($str);
}
?>