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
        $rY = (real)$oSection->CalcY(end($trY));
        if($rY) {
            if(end($trY) > $oSection->rHautNormale xor $rY > $oSection->rHautNormale) {
                $oLog->Add(_T('hydraulic:pente_forte').' '.$x. ' m ('._T('hydraulic:reduire_pas').')',true);
            }
            $trX[] = round($x,$iPrec);
            $trY[] = $rY;
        } else {
            $oLog->Add(_T('hydraulic:arret_calcul').' '.$x. ' m');
            break;
        }
    }
    return array($trX,$trY);
}
?>
