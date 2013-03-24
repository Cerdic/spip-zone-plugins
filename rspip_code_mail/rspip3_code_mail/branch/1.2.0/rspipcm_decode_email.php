<?php

/*  Copyright 2010  Robert Sebille  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$rspipcm_config = find_in_path ('config/config.inc.php');
include ($rspipcm_config);

function rspipcm_ecris_entete($flux) {

//   $flux .= '<script type="text/javascript" src="../plugins/rspip_code_mail/rspipcm_code_email_prive.js"></script>';

   $rspipcm_chemin_css = find_in_path ('css/noscript.css');
   $rspipcm_chemin_js = find_in_path ('lib/rspipcm_decode_autres_fonctions.js');

   // Recup des langues
   $entrez_resultat_addition = _T('rspipcm:entrez_resultat_addition');
   $erreur_entrez_resultat_addition = _T('rspipcm:erreur_entrez_resultat_addition');

   
$flux .= <<<EOD

<!-- feuille de style pour l'encodage sans javascript -->
<link rel='stylesheet' type='text/css' href='$rspipcm_chemin_css' />
<!-- Decodeur et captcha du plugin Codeur d adresse email - debut -->
<script type='text/javascript' src='$rspipcm_chemin_js'></script>
EOD;

   return $flux;

}

// decode les adresses (pour une future version)
function decode($adr) {
   $email = ""; $ch = "";

   $curenc = mb_internal_encoding();
   if (mb_detect_encoding($adr) == "UTF-8") {mb_internal_encoding("UTF-8");}

   for ($i = (mb_strlen($adr) - 1); $i > -1; $i--) {
      $ch = mb_substr($adr, $i, 1);
      if ($ch=="__µ__") {$ch="@";} //:
      if ($ch=="__?__") {$ch="?";} //!
      if ($ch=="__&__") {$ch="&";} //#
      $email .= $ch;
      }
   
   $email = str_replace("'", " ", $email);
   $email = str_replace('"', '', $email);
  
   mb_internal_encoding($curenc);   
      
   return $email;
   
}



$GLOBALS["rspipcm_matches"] = array();
function rspipcm_pre_typo ($texte) {
	$rspipcm_matches = array();

	$texte_traite = $texte;

	if ($GLOBALS["rspipcm_recherche_mailbot_approfondie"]) {
	
		// parser les voyelles accentuées => A CORRIGER
		$a = "(a|A|&#65;|&#97;|á|&aacute;|&#225;|Á|&Aacute;|&#193;|â|&acirc;|&#226;|Â|&Acirc;|&#194;|à|&agrave;|&#224;|À|&Agrave;|&#192;|å|&aring;|&#229;|Å|&Aring;|&#197;|ã|&atilde;|&#227;|Ã|&Atilde;|&#195;|ä|&auml;|&#228;|Ä|&Auml;|&#196;|α|&alpha;|&#945;|Α|&Alpha;|&#913;)";
		$e = "(e|E|&#69;|&#101;|é|&eacute;|&#233;|É|&Eacute;|&#201;|ê|&ecirc;|&#234;|Ê|&Ecirc;|&#202;|è|&egrave;|&#232;|È&Egrave;|&#200;|ë|&euml;|&#235;|Ë|&Euml;|&#203;|ε|&epsilon;|&#949;|Ε|&Epsilon;|&#917;)";
		$i = "(i|I|&#73;|&#105;|í|&iacute;|&#237;|Í|&Iacute;|&#205;|î|&icirc;|&#238;|Î|&Icirc;|&#206;|ì|&igrave;|&#236;|Ì|&Igrave;|&#204;|ï|&iuml;|&#239;|Ï|&Iuml;|&#207;|ι|&iota;|&#953;|Ι|&Iota;|&#921;)";
		$o = "(o|O|&#79;|&#111;|ó|&oacute;|&#243;|Ó|&Oacute;|&#211;|ô|&ocirc;|&#244;|Ô|&Ocirc;|&#212;|ò|&ograve;|&#242;|Ò|&Ograve;|&#210;|ø|&oslash;|&#248;|Ø|&Oslash;|&#216;|õ|&otilde;|&#245;|Õ|&Otilde;|&#213;|ö|&ouml;|&#246;|Ö|&Ouml;|&#214;|ο|&omicron;|&#959;|Ο|&Omicron;|&#927;)";
		$u = "(u|U|&#85;|&#117;|ú|&uacute;|&#250;|Ú|&Uacute;|&#218;|û|&ucirc;|&#251;|Û|&Ucirc;|&#219;|ù|&ugrave;|&#249;|Ù|&Ugrave;|&#217;|ü|&uuml;|&#252;|Ü|&Uuml;|&#220;|υ|&upsilon;|&#965;|Υ|&Upsilon;|&#933;)";

		// match at, chez, arobaz ou arobase découpé entre signes non alphanum
		$at = $a."+[\W_]*t+";
		$chez = "c+[\W_]*h+[\W_]*".$e."+[\W_]*z+";
		$arobase = $a."+[\W_]*r+[\W_]*".$o."+[\W_]*b+[\W_]*".$a."+[\W_]*(s|z)+[\W_]*(".$e."|)+";
		$motif = "/ ?[\W_]+(".$at."|".$chez."|".$arobase.")[\W_]+ ?/iu";
    	$at_motif = $motif; 

		$remplace = "@";
		$texte_traite = preg_replace($motif, $remplace, $texte_traite);

		// match dot, point ou punt découpé entre signes non alphanum
		$dot = "d+[\W_]*".$o."+[\W_]*t+";
		$point = "p+[\W_]*".$o."+[\W_]*".$i."+[\W_]*n+[\W_]*t+";
		$punt = "p+[\W_]*".$u."+[\W_]*n+[\W_]*t+";
		$motif = "/ ?[\W_]+(".$dot."|".$point."|".$punt.")[\W_]+ ?/iu";
	    $dot_motif = $motif; 

		$remplace = ".";
		$texte_traite = preg_replace($motif, $remplace, $texte_traite);

//echo "<h1>texte</h1>".$texte."<h1>texte traite</h1>".$texte_traite."<hr>";


	} // if ($rspipcm_recherche_mailbot_approfondie) 



	//match mails dans un texte:
	//$rspipcm_matches_item = array();
	$motif = "/[\w.-]+@[\w.-]{2,}\.[a-zA-Z]{2,6}/";
	preg_match_all($motif, $texte_traite, $rspipcm_matches, PREG_PATTERN_ORDER);

	// on sauve les hard email dans le tableau global pour affiche_milieu
	array_push ($GLOBALS["rspipcm_matches"],$rspipcm_matches);
	
	return $texte; // qu'on a laisse intact.

}


//echo pipeline('affiche_milieu',array('args'=>array('exec'=>'article','id_rubrique'=>$id_rubrique),'data'=>''));
//echo pipeline('affiche_milieu',array('args'=>array('exec'=>'rubrique','id_article'=>$id_article),'data'=>''));

function rspipcm_affiche_milieu ($flux){

	// y a t il kechose a afficher?
	$rspipcm_affiche = 0;
	foreach ($GLOBALS["rspipcm_matches"] as $val) {
		foreach ($val as $val2) {
			$rspipcm_affiche += count($val2);
		}
	}

	if ($rspipcm_affiche > 0) {
	    $exec = $flux["args"]["exec"];
    	$ret = "<div class='rspipcm_avert_mails_en_clair'>\n";
		$ret .= "<h1>"._T('rspipcm:avertissement_codeur')."</h1>\n";
    	if ($exec == "rubrique") {
        	$id_rubrique = $flux["args"]["id_rubrique"];

			$j = 0;
			foreach ($GLOBALS["rspipcm_matches"] as $val) {
				foreach ($val as $val2) {
					foreach ($val2 as $val3) {
						$ret .= "<div>".$val3."</div>\n";
						$j++;
					}
				}
			}
			$ret .= "<div>"._T('rspipcm:nb_adresses_victimes_spambot')." (".$exec." "._T('rspipcm:numero')." ".$id_rubrique.") : ".$j.".</div>";

	    	$ret .= "</div>";
        	$flux["data"] = $ret.$flux["data"];
    	}

    	if ($exec == "article") {
        	$id_article = $flux["args"]["id_article"];

			$j = 0;
			foreach ($GLOBALS["rspipcm_matches"] as $val) {
				foreach ($val as $val2) {
					foreach ($val2 as $val3) {
						$ret .= "<div>".$val3."</div>\n";
						$j++;
					}
				}
			}
			$ret .= "<div>"._T('rspipcm:nb_adresses_victimes_spambot')." (".$exec." "._T('rspipcm:numero')." ".$id_article.") : ".$j.".</div>";

	    	$ret .= "</div>";
        	$flux["data"] = $ret.$flux["data"];
    	}
    
    	if ($exec == "breve") {
        	$id_breve = $flux["args"]["id_breve"];

			$j = 0;
			foreach ($GLOBALS["rspipcm_matches"] as $val) {
				foreach ($val as $val2) {
					foreach ($val2 as $val3) {
						$ret .= "<div>".$val3."</div>\n";
						$j++;
					}
				}
			}
			$ret .= "<div>"._T('rspipcm:nb_adresses_victimes_spambot')." (".$exec." "._T('rspipcm:numero')." ".$id_breve.") : ".$j.".</div>";

	    	$ret .= "</div>";
        	$flux["data"] = $ret.$flux["data"];
    	}


	} // if ($rspipcm_affiche > 0)

	
	return $flux;

}




?>