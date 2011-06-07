<?php
function dida_didaboitegauche($flux) {
     if ($flux['args']['exec'] == 'articles_edit') {
        $flux['data'] .= affichedidaboitegauche();
        return $flux; }
     return $flux;
  }
  

 function affichedidaboitegauche() {
    $retour = '';
    $retour .= 'Importer un projet Didapages'.$LesTrucsAAfficher;
    return $retour;
 }
 
?>