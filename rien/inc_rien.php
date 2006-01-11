<?php

function rien() {
	global $truc;
	include_ecrire('inc_presentation');

	debut_page(_L("Le plugin rien, page officielle"), "documents", "rien");

	debut_gauche();

	icone_horizontale(_L("Ne rien faire"),
		# lien
		"?page=rien" . ($truc ? '' : '&amp;truc=1'),
		## chemin relou vers l'image !!
		'../'._DIR_PLUGINS.'rien/ecrire/img_pack/rien-24.png',
		"creer.gif");

	debut_droite();

	if (!$truc)
		gros_titre(_L("rien de special ici"));
	else
		gros_titre("Page $page demandee");


	# un peu de blabla au hasard
	if ($truc)
		for ($i=0; $i<500; $i++)
			echo chr(rand(65,100))." ";

	fin_page();
}

?>