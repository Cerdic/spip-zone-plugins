<?php

  //on combine une liste de mots, separe par des virgules ou espace pour en faire une liste quoter: ,"mot1", "mot3"
function passe_complexe_quote_common($list) {
  $commons = split('[ ,]',$list);
  $return = '';
  for ($i = 0; $i < count($commons); $i++) {
	if($commons[$i] && count($commons[$i]) > 0)
	   $return .= ",'".str_replace("'","\\'",$commons[$i])."'";
  }
  return $return;
}

//creer le javascript a ajouter au header pour que ca marche
function passe_complexe_generer_javascript($selecteur) {
		$flux = '<script type="text/javascript" src="'.generer_url_public('jquery.pstrength.1.2.js').'"></script>';
		$common_cfg = lire_config('passe_complexe/common');
		if(count($common_cfg) <= 0) $common_cfg = '';
		else $common_cfg = ','.$common_cfg;

		$flux .= '<script type="text/javascript"><!--
		$(document).ready(function() {
           $("'.$selecteur.'").pstrength({ 
             minchar: '.max(lire_config('passe_complexe/length',6),6).',
             common: ["123456","123","spip","test"' //les chaines communes generales
		  .',"'.$GLOBALS['auteur_session']['nom'].'"' //le nom de l'auteur ne devrait pas se trouver dans le password
		  .',"'.$GLOBALS['auteur_session']['login'].'"' //ni son login
		  .passe_complexe_quote_common(
									   _T('passecomplexe:common') //la liste definit pour la langue de l'utilisateur
									   .$common_cfg //la liste definit par la config cfg
									   .','.$GLOBALS['auteur_session']['nom_site'] //le nom du site de l'auteur
									   .','.$GLOBALS['meta']['nom_site']) //le nom du site sur lequel on est
		  .'],
             verdects:	["'
		  //les differentes chaines traduites
		  ._T('passecomplexe:tres_faible').'","'
		  ._T('passecomplexe:faible').'","'
		  ._T('passecomplexe:moyen').'","'
		  ._T('passecomplexe:fort').'","'
		  ._T('passecomplexe:tres_fort').'","'
		  ._T('passecomplexe:court').'","'
		  ._T('passecomplexe:simple')
		  .'"],
             minchar_label:"'._T('passecomplexe:nb_mini').'"
            });
		});
		--></script>';
		return $flux;
}

?>
