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

?>