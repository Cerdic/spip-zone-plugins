<?php
/**
 * Plugin PreProd pour Spip 2.0
 * Licence GPL (c) 2011 - Ateliers CYM
 */

function preprod_insert_head($flux)
{
	include_spip('inc/autoriser');

	// si l'utilisateur est autorisé, on insère le fichier javascript
	if (autoriser('configurer') || 9070==$GLOBALS['visiteur_session']['id_auteur']) {
		$js = find_in_path("preprod.js");
		if ($js)
			$flux .= '<script type="application/javascript" src="'. $js .'"></script>';
	}
    return $flux;	
}

function preprod_affichage_final($texte)
{
	include_spip('inc/autoriser');
	if (autoriser('configurer') || 9070==$GLOBALS['visiteur_session']['id_auteur']) {
		
		// on récupère l'url de la page
		$self = self();
		
		// si c'est une page publique "normale"
		if (false===strpos($self, 'preprod_') && false!==strpos($texte, '<div id="page">'))
		{
			include_spip('inc/preprod_fonctions');
			$tickets = lister_tickets_par_url($self);
			$contexte = $GLOBALS['contexte'];
			ksort($contexte);

			// on insère le bloc "preprod" à la fin du <body>
			$ajout_preprod = recuperer_fond('inclure/inc-boite-preprod',array(
					'preprod_url'	=> $self,
					'tickets'		=> $tickets,
					'contexte'		=> http_build_query($contexte)
			));
			$texte = str_replace('</body>', $ajout_preprod.'</body>', $texte);
		}
	}
    return $texte;	
}

// insertion du fichier de style de preprod
function preprod_insert_head_css($flux)
{
    $css = find_in_path('preprod.css');
	if ($css)
    	$flux .= '<link rel="stylesheet" type="text/css" media="all" href="'.$css.'" />';
    return $flux;	
}

?>