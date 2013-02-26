<?php

/*  Copyright 2010  Robert Sebille  (email : robert -AT- sebille -DOT- be)

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
      if ($ch=="@") {$ch=":";}
      if ($ch=="?") {$ch="!";}
      if ($ch=="&") {$ch="#";}
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
	// on regarde si il y a des matches pour l'affichage du <noscript>
	$noscript = count($to_replace[0]);

	foreach ($to_replace[0] as $val) {
		$val_array = array(); $val_nom = "";
		$val_array = explode("->", $val);
		// on supprime le ] de la fin du match
		$val_adresse = substr($val_array[1], 0, -1);
		// On vire le mailto: eventuel
		$val_adresse = str_replace("mailto:", "", $val_adresse);
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
		$text_to_replace = "[".$val_nom."->javascript:mdecode('".$val_adresse."')]";
		// On prepare la mise à jour du texte
		
		// On met le texte a jour
		$texte = str_replace($val, $text_to_replace, $texte);

	}

if ($noscript) {
$texte = "<noscript><div class=\"rspipcm_noscript\">"._T('rspipcm:javascript_est_desactive')."</div></noscript>".$texte;
}

   return $texte;
}


?>
