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

// encode les adresses
function rspipcm_encode($adr) {
   $email = ""; $ch = "";

   $curenc = mb_internal_encoding();
   if (mb_detect_encoding($adr) == "UTF-8") {mb_internal_encoding("UTF-8");}

   for ($i = (mb_strlen($adr) - 1); $i > -1; $i--) {
      $ch = mb_substr($adr, $i, 1);
      if ($ch=="@") {$ch="__µ__";} //:
      if ($ch=="?") {$ch="__?__";} //!
      if ($ch=="&") {$ch="__&__";} //#
      $email .= $ch;
      }
   
   $email = str_replace("'", " ", $email);
   $email = str_replace('"', '', $email);
  
   mb_internal_encoding($curenc);   
   
 //     $email = "javascript:mdecode('".$email."');";
   
   return $email;
   }



//Filtre les emails
function rspipcm_filtre_email($texte) {

	// On matche les raccourcis email 
	$to_replace = array(); $val_code = "";
	preg_match_all("/\[.*->.*@.*\]/i", $texte, $to_replace);

	foreach ($to_replace[0] as $val) {
		$val_array = array(); $val_nom = "";
		$val_array = explode("->", $val);
		// on supprime le ] de la fin du match
		$val_adresse = substr($val_array[1], 0, -1);
		// On vire le mailto: eventuel
		$val_adresse = str_replace("mailto:", "", $val_adresse);


		//on preserve le user originel pour un eventuel noscript
		$val_nom_nojs = substr($val_adresse, 0, strpos($val_adresse, "@"));
		$val_nom_nojs = _T('rspipcm:ecrire_a_pre')." : ".$val_nom_nojs." "._T('rspipcm:ecrire_a_post');
		// on recupere le domaine pour javascript desactive
		$val_domaine = strstr($val_adresse, '@');
		// on backes le @ et les eventuels param
		$val_domaine = substr($val_domaine, 1, strlen($val_domaine));
		if (strpos($val_domaine, "?")) {$val_domaine = substr($val_domaine, 0, strpos($val_domaine, "?"));}

		// On encode l'adresse (et ses param eventuels)
		$val_adresse = rspipcm_encode($val_adresse);

		// on supprime le [ du debut du match et on remplace par le user si vide
		if ($val_array[0] != "[") {
			$val_nom = substr($val_array[0], 1, strlen($val_array[0]));
			} 
		else {
			$val_nom = substr($val_array[1], 0, strpos($val_array[1], "@"));
			// On supprime un eventuel mailto:
			$val_nom = str_replace("mailto:", "", $val_nom);
			
			// Si il y a des . dans user on l'eclate: par exple: nom.prenom > Nom Prenom
			if (preg_match("/\./", $val_nom)) {
				$val_nom_array = explode(".", $val_nom); $nom_prov = "";
				foreach ($val_nom_array as $val_n) {$nom_prov .= ucfirst($val_n)." ";}
			$val_nom = trim($nom_prov);
			$val_nom = _T('rspipcm:ecrire_a_pre')." ".$val_nom." "._T('rspipcm:ecrire_a_post');
			}
		}
		// On prepare la mise à jour du texte
		// RSPIPCM_JS_MDECODE => javascript:mdecode dans affichage_final
		$text_to_replace = "[".$val_nom."->RSPIPCM_JS_MDECODE('".$val_adresse."')]\n<noscript>\n<div class=\"rspipcm_noscript\">".$val_nom_nojs."<br />"._T('rspipcm:dans_le_domaine')." : ".$val_domaine."</div>\n</noscript>"; 
		
		// On met le texte a jour
		$texte = str_replace($val, $text_to_replace, $texte);
	}

   return $texte;

} // function rspipcm_filtre_email($texte)


function rspipcm_affichage_final($texte_final) {
	if ($GLOBALS['html']) {
		$texte_final = str_replace("RSPIPCM_JS_MDECODE", "javascript:mdecode", $texte_final);
		$texte_final = str_replace("__µ__", ":", $texte_final);
		$texte_final = str_replace("__?__", "!", $texte_final);
		$texte_final = str_replace("__&amp;__", "#", $texte_final);
	}
	
	return $texte_final;
}

?>
