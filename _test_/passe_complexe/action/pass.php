<?php

  //on surcharge l'action par defaut, donc on triche en reincluant l'action dist... pas tres propre...
include_once _DIR_RESTREINT.'/action/pass.php';

function action_pass()
{
	utiliser_langue_visiteur();

	include_spip('inc/passe_complexe');
	//avec minipres, on n'a pas de pipeline insert_head ou header_prive, donc on triche...
	//on integre le script apres, mais il faut aussi ajouter jquery qui n'est pas la normalement
	$flux = '<script type="text/javascript" src="'.generer_url_public('jquery.js').'"></script>';
	$flux .= passe_complexe_generer_javascript('#oubli');

	echo str_replace('</head>',$flux."\n</head>",
					 install_debut_html(_T('pass_mot_oublie'), " class='pass'")
					 );

	inclure_balise_dynamique(formulaire_oubli_dyn('test', _request('oubli')));
	echo install_fin_html();
}

?>