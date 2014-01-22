<?php

function pp_loremipsum_porte_plume_barre_pre_charger($barres){

	$barre = &$barres['edition'];
	
	// separation
	$barre->ajouterApres('grpCaracteres', array(
		"id" => "sepLorem",
		"separator" => "---------------",
		"display"   => true,
	));
	
	// lorem ipsum
	$barre->ajouterApres('sepLorem', array(
		"id"          => 'lorem_ipsum',
		"name"        => _T('pp_loremipsum:outil_texte_lorem_ipsum_1'),
		"className"   => "outil_lorem_ipsum", 
		"replaceWith" => "\n\nLorem ipsum dolor sit amet, consectetuer adipiscing elit." 
						. "Aenean ut orci vel massa suscipit pulvinar. Nulla sollicitudin. "
						. "Fusce varius, ligula non tempus aliquam, nunc turpis ullamcorper nibh, in "
						. "tempus sapien eros vitae ligula. Pellentesque rhoncus nunc et augue. "
						. "Integer id felis. Curabitur aliquet pellentesque diam. Integer quis metus "
						. "vitae elit lobortis egestas. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. "
						. "Morbi vel erat non mauris convallis vehicula. Nulla et sapien. Integer tortor tellus, "
						. "aliquam faucibus, convallis id, congue eu, quam. Mauris ullamcorper felis vitae erat. "
						. "Proin feugiat, augue non elementum posuere, metus purus iaculis lectus, "
						. "et tristique ligula justo vitae magna.\n\n",
		"display"     => true,
		"dropMenu" => array(
			// lorem ipsum 3 paragraphes
			array(
				"id"          => 'lorem_ipsum_big',
				"name"        => _T('pp_loremipsum:outil_texte_lorem_ipsum_3_paragraphes'),
				"className"   => "outil_lorem_ipsum_big", 
				"replaceWith" => "\n\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. "
								. "Curabitur lacus mi, varius sit amet, suscipit in, hendrerit "
								. "sit amet, turpis. Duis in odio. Fusce mauris. Nulla quis ante. "
								. "Vestibulum id dui. Curabitur quis est ac quam euismod ullamcorper. "
								. "Phasellus nec justo. Vestibulum id erat sed odio ultrices hendrerit. "
								. "Duis fermentum, velit ut pretium fermentum, felis turpis rhoncus justo, "
								. "vel adipiscing nulla lectus sed eros. In hac habitasse platea dictumst. "
								. "In hac habitasse platea dictumst. Curabitur tellus velit, consequat nec, "
								. "tincidunt sit amet, posuere vel, ligula. Aenean auctor mollis mi. "
								. "In adipiscing dolor vel diam. Morbi justo. Maecenas eu risus id mi tincidunt "
								. "vestibulum.\n\n"
								. "Maecenas lacinia. Sed aliquet bibendum nisl. Vivamus vulputate, "
								. "sapien ut molestie iaculis, diam libero porttitor dolor, eget semper orci orci "
								. "ut sem. Nunc venenatis. Curabitur adipiscing, velit at iaculis dictum, "
								. "lacus nulla adipiscing mauris, id rhoncus velit nisl ac mauris. Aliquam "
								. "egestas, sapien sed placerat lacinia, tellus erat tempor quam, at sollicitudin "
								. "ligula eros sit amet sapien. Nam at dui id libero vehicula sodales. Vestibulum "
								. "dictum risus eget metus. Cras lorem. Pellentesque lobortis sodales ipsum. "
								. "Vivamus convallis lectus in nunc. Vivamus metus libero, ullamcorper in, "
								. "porttitor nec, dapibus id, est. Praesent pede. Sed viverra consequat leo. "
								. "Mauris pharetra tortor a orci.\n\n"
								. "Maecenas sed lacus. Phasellus iaculis risus et elit. Morbi sagittis nunc vitae "
								. "sem. Aliquam ac lorem vel magna ornare malesuada. Pellentesque habitant "
								. "morbi tristique senectus et netus et malesuada fames ac turpis egestas. "
								. "Mauris est dolor, aliquam eget, feugiat ut, tempus at, arcu. Duis porta, "
								. "pede sed hendrerit pellentesque, orci dolor consectetuer risus, id scelerisque "
								. "tellus ipsum quis felis. Sed ultrices. Nullam eleifend dui sodales massa. "
								. "Morbi consectetuer pellentesque dui. Vestibulum urna. Fusce congue velit ut "
								. "erat. Aliquam quis odio sollicitudin ipsum euismod porta. Vivamus pharetra, "
								. "lacus eu tempor lobortis, diam nisi vulputate lorem, ut aliquet dui neque eu "
								. "sem. Morbi varius, nisi ac laoreet mollis, pede odio cursus nisi, in imperdiet "
								. "dolor enim at metus. Nunc pretium pulvinar tortor. Vestibulum euismod ultrices "
								. "est. Etiam lobortis, enim ut bibendum dictum, urna orci lacinia tortor, "
								. "at eleifend pede sem eu sem. Morbi a neque. Vestibulum cursus.\n\n", 
				"display"     => true,
			),
		),
	));
	

	return $barres;
}

function pp_loremipsum_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(	
		'outil_lorem_ipsum' => 'newspaper.png',
		'outil_lorem_ipsum_big' => 'newspaper_add.png',
	));
}
?>
