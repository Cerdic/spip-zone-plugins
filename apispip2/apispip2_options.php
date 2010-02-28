<?php 

	/* Autoload */
	function __autoload($className)	{
		if (!class_exists($className, false)) {
			require_once('classes/'.$className.'.php');
		}
	}


	/*
	
	//////////// EXEMPLE ///////////
	
	$test = new article();
	$test->titre = "Test !";
	$test->chapo = "Introduction!";
	$test->texte = "mmmmmm mmmmmmmmmmmm!";
	$test->statut = "publie";
	$test->set_rubrique(3);
	$test->add();
	$test->add_logo($_FILES['file']);
	
	$document = new document();
	$document->add($_FILES['file']);
	
	$test->add_document($document->id_document);
	
	echo $test->id_article;
	
	$axome = new auteur();
	$axome->nom = "Axome";
	$axome->login = "axome2";
	$axome->email = "dev@axome.com";
	$axome->statut = "0minirezo";
	$axome->lang = "fr";
	$axome->setPass("rhxx09zm");
	if($axome->add()) echo $axome->id_auteur;
		
	$test->add_auteur($axome->id_auteur);
	
	
	$mon_mot = new mot();
	$mon_mot->titre = "Test de mot clef8";
	$mon_mot->set_groupe(1);
	$mon_mot->add();
	$test->add_mot($mon_mot->id_mot);


-------------------

On peut aussi editer un article :

	$article = new article(5);
	$article->titre = "toto";
	$article->update();

	*/

?>
