<?php
// ajouter js et css necessaires pour utliser jQuery UI dans l'interface de config CFG
function top_bando_header_prive($flux){
	// Initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['top_bando']);
 
// si on veut pouvoir manipuler la zone de recadrage en drag/resize dans la page CFG il faut les jQuery UI
  if (_request('exec') == 'cfg' AND _request('cfg') == 'top_bando'){

    // Insertion des librairies js en fonction de la version de jQuery donc de celle de SPIP...
      // en SPIP 2.1.* jQuery est en version 1.3.+ donc ui en version 1.7.2
      if (strpos('2.1', $GLOBALS['spip_version_affichee'])) {
          $flux .='<link type="text/css" href="'.url_absolue(find_in_path('lib/jquery-ui-themes-1.7.2/themes/base/jquery-ui.css')).'" rel="Stylesheet"/>'."\r\n";
          $flux .='<script src="'.url_absolue(find_in_path('lib/jquery-ui-1.7.2/ui/jquery-ui.js')).'"></script>'."\r\n";
          $flux .='<script src="'.find_in_path('lib/jquery-ui-1.7.2/ui/ui.resizable.js').'"></script>'."\r\n";
          $flux .='<script src="'.find_in_path('lib/jquery-ui-1.7.2/ui/ui.draggable.js').'"></script>'."\r\n";
      }
      // en SPIP 2.0.* jQuery est en version 1.2.6 donc ui en version 1.6
      else {
          $flux .='<link type="text/css" href="'.find_in_path('lib/jquery.ui-1.6/themes/default/ui.all.css').'" rel="Stylesheet"/>'."\r\n";
          $flux .='<script src="'.find_in_path('lib/jquery.ui-1.6/ui/ui.core.js').'"></script>'."\r\n";
          $flux .='<script src="'.find_in_path('lib/jquery.ui-1.6/ui/ui.resizable.js').'"></script>'."\r\n";
          $flux .='<script src="'.find_in_path('lib/jquery.ui-1.6/ui/ui.draggable.js').'"></script>'."\r\n";
      }
      
  }
  
  return $flux;
}

?>