<?php

function pp_blocs_porte_plume_barre_pre_charger($barres){

	$barre = &$barres['edition'];
	
	// separation
	$barre->ajouterApres('grpCaracteres', array(
		"id" => "sepBloc",
		"separator" => "---------------",
		"display"   => true,
	));
	
	// info
	$barre->ajouterApres('sepBloc', array(
		"id"          => 'bloc_info',
		"name"        => _T('pp_blocs:outil_inserer_bloc_info'),
		"className"   => "outil_bloc_info", 
		"openWith"    => "<div class='info'>\n",
		"closeWith"   => "\n</div>",
		"display"     => true,
		"dropMenu" => array(
			// warning
			array(
				"id"          => 'bloc_warning',
				"name"        => _T('pp_blocs:outil_inserer_bloc_warning'),
				"className"   => "outil_bloc_warning", 
				"openWith"    => "<div class='warning'>\n",
				"closeWith"   => "\n</div>",
				"display"     => true,
			),
			// succes
			array(
				"id"          => 'bloc_success',
				"name"        => _T('pp_blocs:outil_inserer_bloc_success'),
				"className"   => "outil_bloc_success", 
				"openWith"    => "<div class='success'>\n",
				"closeWith"   => "\n</div>",
				"display"     => true,
			),
			// erreur
			array(
				"id"          => 'bloc_error',
				"name"        => _T('pp_blocs:outil_inserer_bloc_error'),
				"className"   => "outil_bloc_error", 
				"openWith"    => "<div class='error'>\n",
				"closeWith"   => "\n</div>",
				"display"     => true,
			),
		),
	));

	return $barres;
}

function pp_blocs_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_bloc_info'     => 'bloc_info.png',
		'outil_bloc_warning'  => 'bloc_warning.png',
		'outil_bloc_success'  => 'bloc_success.png',
		'outil_bloc_error'    => 'bloc_error.png',
	));
}
?>
