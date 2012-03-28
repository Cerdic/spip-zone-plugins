<?php
function calcul_courbe_remous($oParam,$oSection,$oLog,$iPrec) {

   $trX = array();
   $trY = array();

   if($oParam->rDx > 0) {
     // Calcul depuis l'aval
     $xDeb = $oParam->rLong;
     $xFin = 0;
   }
   else {
     // Calcul depuis l'amont
     $xDeb = 0;
     $xFin = $oParam->rLong;
   }
   $dx = - $oParam->rDx;

   $trX[] = (real)round($xDeb,$iPrec);
   $trY[] = (real)$oParam->rYCL;

   // Boucle de calcul de la courbe de remous
   for($x = $xDeb + $dx; ($dx > 0 && $x <= $xFin) || ($dx < 0 && $x >= $xFin); $x += $dx) {
      $trX[] = round($x,$iPrec);
      $trY[] = (real)$oSection->CalcY_M1(end($trY));
      if($oParam->rDx > 0 xor !(end($trY) < $oSection->rHautCritique)) {
         $oLog->Add(_T('hydraulic:arret_calcul').' '.$x. ' m');
         break;
      }
   }
   return array($trX,$trY);
}
?>
