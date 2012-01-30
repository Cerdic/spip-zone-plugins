<?php

function hyd_get_champ(&$champ) {
   if(isset($_POST[$champ[0]]) && $_POST[$champ[0]]!='') {
      $champ[2]=$_POST[$champ[0]];
   }
}

/**
 * Affiche
 * @param
 * @return
 */
function hyd_form_affiche(&$champs) {
$aff='<form action="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&var_mode=calcul" method="post"><table>';
   foreach($champs as &$champ) {
      hyd_get_champ($champ);
      $aff.='<tr><td align="right">'.$champ[1].' :</td><td><input name="'.$champ[0].
         '" type="text" size="30" maxlength="30" value="'.$champ[2].'"></td></tr>';
   }
   $aff.='<tr><td></td><td><input type="submit" value=" Calculer la courbe de remous"></td></tr>';
   $aff.='</table></form>';
   return $aff;
}

?>
