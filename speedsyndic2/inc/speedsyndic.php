<?php
// la cle est la tache, la valeur le temps minimal, en secondes, entre
// deux memes taches
// NE PAS METTRE UNE VALEUR INFERIEURE A 30 (cf ci-dessus)
// Note : en fait mettre absolument une valeur superieure
// au max execution time PHP si j'ai bien compris
function speedsyndic_taches_generales_cron($taches_generales){
    $taches_generales['speedsyndication']= lire_config('speedsyndic/frequence');
    return $taches_generales;
}
?>


