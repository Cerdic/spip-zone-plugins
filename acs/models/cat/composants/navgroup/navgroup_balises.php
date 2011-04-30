<?php
// Librairies javascript a inclure pour ce composant 
// SI et SEULEMENT si :
// - on utilise le style "accordéon"
// - la librairie jQuery UI est installée (par le plugin jQuery UI)
function navgroup_jslib() {
  include_spip('jqueryui_pipelines');
  if (_DIR_JQUERYUI_JS != '_DIR_JQUERYUI_JS')
    return array(_DIR_JQUERYUI_JS.'minified/jquery.ui.accordion.min.js');
  else
    return array();
}
?>