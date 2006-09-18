<?php
function calcul_DROITS() {
   //pour ne pas modifier le coeur de spip il faudra aller faire une
   //requet sur la table adherent
   //on en profitera pour v?fier la validit?e sa date
   global $auteur_session;
   $cgfp_retour='';
   $cgfp_statut_loc=$auteur_session['statut'];
   if (strlen($cgfp_statut_loc)==0)
    $cgfp_retour= 10;
   if ((strlen($cgfp_statut_loc)>0) AND
(settype($cgfp_statut_loc,"integer"))) {
      switch($cgfp_statut_loc):
         case 0:
            $cgfp_retour= 0;
            break;
         case 1:
            $cgfp_retour= 1;
            break;
         case 5:
            $cgfp_retour= 10;
            break;
         case 6:
            $cgfp_retour= 6;
            break;
         default:
            $cgfp_retour= 10;
            break;
      endswitch;
   }
   return $cgfp_retour;
}

function balise_DROITS ($p){
    $p->code="calcul_DROITS()";
    $p->statut = 'html';
  return $p;
}
function calcul_IDENTITE (){
  global $auteur_session;
  $nom_connecte = $auteur_session['nom'];
  return $nom_connecte;
}
function balise_IDENTITE($p){
    $p->code="calcul_IDENTITE()";
    $p->statut = 'html';
  return $p;
}
?>