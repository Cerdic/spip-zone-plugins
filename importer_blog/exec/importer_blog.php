<?php


if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/presentation');


function exec_importer_blog_dist() {

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("Importer", "admin", "importer_blog");

		echo debut_gauche('', true);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'importer_blog'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'importer_blog'),'data'=>''));
		echo debut_droite('', true);


	gros_titre('Importer');

	echo propre('Cette page vous permet d\'importer dans votre site une sauvegarde de blog au format ATOM (blogspot).');

	if (!autoriser('webmestre')) {
		echo propre('Il faut être webmestre !');
	} else {
	
		echo <<<FORM
	<form method="post" action="?action=importer_blog" enctype="multipart/form-data">
	<input type='hidden' name='action' value='importer_blog' />
	<input type="file" name="file" />
	<input type="submit" />
	</form>

	

FORM;

	}

	echo fin_gauche(), fin_page();
}