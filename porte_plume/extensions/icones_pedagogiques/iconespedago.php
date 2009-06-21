<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EVA_ICONESPEDAGO',(_DIR_PLUGINS.end($p)));

function iconespedago_pre_typo($chaine) {
	$chemin = '<img alt="icones_peda" src="'._DIR_PLUGIN_EVA_ICONESPEDAGO.'/img_pack/';

//consignes de travail

	    $chaine = preg_replace('/:dire+/m', $chemin . "dire.gif\" />", $chaine);
	    $chaine = preg_replace('/:ecrire+/m', $chemin . "ecrire.gif\" />", $chaine);
	    $chaine = preg_replace('/:lire+/m', $chemin . "lire.gif\" />", $chaine);
	    //$chaine = preg_replace('/:ecouter+/m', $chemin . "ecouter.gif\" />", $chaine);
	    
//mode de travail

		$chaine = preg_replace('/:seul+/m', $chemin . "seul.gif\" />", $chaine);
	    $chaine = preg_replace('/:plusieurs+/m', $chemin . "plusieurs.gif\" />", $chaine);
	    
// materiel


	    $chaine = preg_replace('/:ciseaux+/m', $chemin . "ciseaux.gif\" />", $chaine);
	    $chaine = preg_replace('/:colle+/m', $chemin . "colle.gif\" /", $chaine);
	    $chaine = preg_replace('/:compas+/m', $chemin . "compas.gif\" />", $chaine);
	    $chaine = preg_replace('/:equerre+/m', $chemin . "equerre.gif\" />", $chaine);
	    $chaine = preg_replace('/:regle+/m', $chemin . "regle.gif\" />", $chaine);
	    $chaine = preg_replace('/:gomme+/m', $chemin . "gomme.gif\" />", $chaine);
	    $chaine = preg_replace('/:crayon+/m', $chemin . "crayon.gif\" />", $chaine);
	    $chaine = preg_replace('/:rapporteur+/m', $chemin . "rapporteur.gif\" />", $chaine);
	    $chaine = preg_replace('/:pinceau+/m', $chemin . "pinceau.gif\" />", $chaine);
	    $chaine = preg_replace('/:sport+/m', $chemin . "sport.gif\" />", $chaine);
	    $chaine = preg_replace('/:cahier+/m', $chemin . "cahier.gif\" />", $chaine);
	    $chaine = preg_replace('/:classeur+/m', $chemin . "classeur.gif\" />", $chaine);
	    $chaine = preg_replace('/:livre+/m', $chemin . "livre.gif\" />", $chaine);
	    
//Supports d'ecriture

	    $chaine = preg_replace('/:q5mm+/m', $chemin . "quadrillage5mm.gif\" />", $chaine);
	    $chaine = preg_replace('/:q1cm+/m', $chemin . "quadrillage1cm.gif\" />", $chaine);
	    $chaine = preg_replace('/:carreaux1cm+/m', $chemin . "carreaux1cm.png\" />", $chaine);
	    $chaine = preg_replace('/:trou+/m', $chemin . "trou.png\" />", $chaine);
	    $chaine = preg_replace('/:rond+/m', $chemin . "rond.png\" />", $chaine);
	  	$chaine = preg_replace('/:ligne+/m', $chemin . "ligne.png\" />", $chaine);
		$chaine = preg_replace('/:frise+/m', $chemin . "frise.gif\" />", $chaine);

 // Conscience phonologique


		//$chaine = preg_replace('/:calcul+/m', $chemin . "calcul.gif\" />", $chaine);
		$chaine = preg_replace('/:oeil+/m', $chemin . "oeil.png\" />", $chaine);
		$chaine = preg_replace('/:oreille+/m', $chemin . "oreille.png\" />", $chaine);
		$chaine = preg_replace('/:noeil+/m', $chemin . "noeil.png\" />", $chaine);
		$chaine = preg_replace('/:noreille+/m', $chemin . "noreille.png\" />", $chaine);

//Numeration

		//dominos

	    $chaine = preg_replace('/:dominovide+/m', $chemin . "dominovide.png\" />", $chaine);
	    $chaine = preg_replace('/:domino0+/m', $chemin . "domino0.png\" />", $chaine);
	    $chaine = preg_replace('/:domino1+/m', $chemin . "domino1.png\" />", $chaine);
	    $chaine = preg_replace('/:domino2+/m', $chemin . "domino2.png\" />", $chaine);
	    $chaine = preg_replace('/:domino3+/m', $chemin . "domino3.png\" />", $chaine);
	    $chaine = preg_replace('/:domino4+/m', $chemin . "domino4.png\" />", $chaine);
	    $chaine = preg_replace('/:domino5+/m', $chemin . "domino5.png\" />", $chaine);
	    $chaine = preg_replace('/:domino6+/m', $chemin . "domino6.png\" />", $chaine);
	    $chaine = preg_replace('/:domino7+/m', $chemin . "domino7.png\" />", $chaine);
	    $chaine = preg_replace('/:domino8+/m', $chemin . "domino8.png\" />", $chaine);
	    $chaine = preg_replace('/:domino9+/m', $chemin . "domino9.png\" />", $chaine);
	    $chaine = preg_replace('/:dominoX+/m', $chemin . "dominoX.png\" />", $chaine);
	
		//doigts
		
	    $chaine = preg_replace('/:un-g+/m', $chemin . "un-g.png\" />", $chaine);
	    $chaine = preg_replace('/:deux-g+/m', $chemin . "deux-g.png\" />", $chaine);
	    $chaine = preg_replace('/:trois-g+/m', $chemin . "trois-g.png\" />", $chaine);
	    $chaine = preg_replace('/:quatre-g+/m', $chemin . "quatre-g.png\" />", $chaine);
	    $chaine = preg_replace('/:cinq-g+/m', $chemin . "cinq-g.png\" />", $chaine);
	    $chaine = preg_replace('/:un-d+/m', $chemin . "un-d.png\" />", $chaine);
	    $chaine = preg_replace('/:deux-d+/m', $chemin . "deux-d.png\" />", $chaine);
	    $chaine = preg_replace('/:trois-d+/m', $chemin . "trois-d.png\" />", $chaine);
	    $chaine = preg_replace('/:quatre-d+/m', $chemin . "quatre-d.png\" />", $chaine);
	    $chaine = preg_replace('/:cinq-d+/m', $chemin . "cinq-d.png\" />", $chaine);
	    
//Binettes
	$chaine = preg_replace('/:->+/m', $chemin.'diable.png" />',$chaine);
	$chaine = preg_replace('/:-\(\(+/m', $chemin.'en_colere.png" />', $chaine);
	$chaine = preg_replace('/:-\)\)+/m', $chemin."mort_de_rire.png\" />", $chaine);
	$chaine = preg_replace('/:-D+/m', $chemin."mort_de_rire.png\" />", $chaine);
	$chaine = preg_replace('/:-\)+/m', $chemin."sourire.png\" />", $chaine);
	$chaine = preg_replace('/;-\)+/m', $chemin."clin_d-oeil.png\" />", $chaine);
	$chaine = preg_replace("/:'-\)+/m", $chemin."pleure_de_rire.png\" />", $chaine);
	$chaine = preg_replace("/:'-D+/m", $chemin."pleure_de_rire.png\" />", $chaine);
	$chaine = preg_replace('/:o\)+/m', $chemin."rigolo.png\" />", $chaine);
	$chaine = preg_replace('/B-\)+/m', $chemin."lunettes.png\" />", $chaine);
	$chaine = preg_replace('/\s:-p/m', $chemin."tire_la_langue.png\" />", $chaine);
	$chaine = preg_replace('/:-\|+/m', $chemin."bof.png\" />", $chaine);
	$chaine = preg_replace('/:-\/+/m', $chemin."mouai.png\" />", $chaine);
	$chaine = preg_replace('/:-o+/m', $chemin."surpris.png\" />", $chaine);
	$chaine = preg_replace('/:-O+/m', $chemin."surpris.png\" />", $chaine);
	$chaine = preg_replace('/:-\(+/m', $chemin."pas_content.png\" />", $chaine);
	$chaine = preg_replace("/:'-\(+/m", $chemin."triste.png\" />", $chaine);

return $chaine;
}

function iconespedago_porte_plume_barre_pre_charger($barres) {
	$barre = &$barres['edition'];
	$barre->ajouterApres('grpCaracteres',
	array(
		// groupe code et bouton <code>
		"id"          => 'sepIconesPedago',
		"separator" => "---------------",
		"display"   => false,
	));
	$barre->ajouterApres('sepIconesPedago',
	array(
		// groupe code et bouton <code>
		"id"          => 'iconespedago_onglet_consignes',
		"name"        => _T('iconespedago:consignes'),
		"className"   => 'iconespedago_onglet_consignes',
		"replaceWith" => "",
		"display"     => true,
		"dropMenu"    => array(
			array(
				"id"          => 'iconespedago_lire',
				"name"        => _T('iconespedago:lire'),
				"className"   => 'iconespedago_lire', 
				"replaceWith" => " :lire ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_dire',
				"name"        => _T('iconespedago:dire'),
				"className"   => 'iconespedago_dire', 
				"replaceWith" => " :dire ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ecrire',
				"name"        => _T('iconespedago:ecrire'),
				"className"   => 'iconespedago_ecrire', 
				"replaceWith" => " :ecrire ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_seul',
				"name"        => _T('iconespedago:seul'),
				"className"   => 'iconespedago_seul', 
				"replaceWith" => " :seul ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_plusieurs',
				"name"        => _T('iconespedago:plusieurs'),
				"className"   => 'iconespedago_plusieurs', 
				"replaceWith" => " :plusieurs ",
				"display"     => true,
			),
		),
	
	));
	
	$barre->ajouterApres('iconespedago_onglet_consignes',
	array(
		// groupe code et bouton <code>
		"id"          => 'iconespedago_onglet_materiel',
		"name"        => _T('iconespedago:materiel'),
		"className"   => 'iconespedago_onglet_materiel',
		"replaceWith" => "",
		"display"     => true,
		"dropMenu"    => array(
			array(
				"id"          => 'iconespedago_colle',
				"name"        => _T('iconespedago:colle'),
				"className"   => 'iconespedago_colle', 
				"replaceWith" => " :colle ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ciseaux',
				"name"        => _T('iconespedago:ciseaux'),
				"className"   => 'iconespedago_ciseaux', 
				"replaceWith" => " :ciseaux ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_equerre',
				"name"        => _T('iconespedago:equerre'),
				"className"   => 'iconespedago_equerre', 
				"replaceWith" => " :equerre ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_compas',
				"name"        => _T('iconespedago:compas'),
				"className"   => 'iconespedago_compas', 
				"replaceWith" => " :compas ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_gomme',
				"name"        => _T('iconespedago:gomme'),
				"className"   => 'iconespedago_gomme', 
				"replaceWith" => " :gomme ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_crayon',
				"name"        => _T('iconespedago:crayon'),
				"className"   => 'iconespedago_crayon', 
				"replaceWith" => " :crayon ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_rapporteur',
				"name"        => _T('iconespedago:rapporteur'),
				"className"   => 'iconespedago_rapporteur', 
				"replaceWith" => " :rapporteur ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_sport',
				"name"        => _T('iconespedago:sport'),
				"className"   => 'iconespedago_sport', 
				"replaceWith" => " :sport ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_regle',
				"name"        => _T('iconespedago:regle'),
				"className"   => 'iconespedago_regle', 
				"replaceWith" => " :regle ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_pinceau',
				"name"        => _T('iconespedago:pinceau'),
				"className"   => 'iconespedago_pinceau', 
				"replaceWith" => " :pinceau ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_cahier',
				"name"        => _T('iconespedago:cahier'),
				"className"   => 'iconespedago_cahier', 
				"replaceWith" => " :cahier ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_classeur',
				"name"        => _T('iconespedago:classeur'),
				"className"   => 'iconespedago_classeur', 
				"replaceWith" => " :classeur ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_livre',
				"name"        => _T('iconespedago:livre'),
				"className"   => 'iconespedago_livre', 
				"replaceWith" => " :livre ",
				"display"     => true,
			),
		),
	
	));
	
	$barre->ajouterApres('iconespedago_onglet_materiel',
	array(
		// groupe code et bouton <code>
		"id"          => 'iconespedago_onglet_domino',
		"name"        => _T('iconespedago:dominos'),
		"className"   => 'iconespedago_onglet_domino',
		"replaceWith" => "",
		"display"     => true,
		"dropMenu"    => array(
			array(
				"id"          => 'iconespedago_domino_vide',
				"name"        => _T('iconespedago:domino_vide'),
				"className"   => 'iconespedago_domino_vide', 
				"replaceWith" => " :dominovide ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_zero',
				"name"        => _T('iconespedago:zero'),
				"className"   => 'iconespedago_domino_zero', 
				"replaceWith" => " :domino0 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_un',
				"name"        => _T('iconespedago:un'),
				"className"   => 'iconespedago_domino_un', 
				"replaceWith" => " :domino1 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_deux',
				"name"        => _T('iconespedago:deux'),
				"className"   => 'iconespedago_domino_deux', 
				"replaceWith" => " :domino2 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_trois',
				"name"        => _T('iconespedago:trois'),
				"className"   => 'iconespedago_domino_trois', 
				"replaceWith" => " :domino3 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_quatre',
				"name"        => _T('iconespedago:quatre'),
				"className"   => 'iconespedago_domino_quatre', 
				"replaceWith" => " :domino4 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_cinq',
				"name"        => _T('iconespedago:cinq'),
				"className"   => 'iconespedago_domino_cinq', 
				"replaceWith" => " :domino5 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_six',
				"name"        => _T('iconespedago:six'),
				"className"   => 'iconespedago_domino_six', 
				"replaceWith" => " :domino6 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_sept',
				"name"        => _T('iconespedago:sept'),
				"className"   => 'iconespedago_domino_sept', 
				"replaceWith" => " :domino7 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_huit',
				"name"        => _T('iconespedago:huit'),
				"className"   => 'iconespedago_domino_huit', 
				"replaceWith" => " :domino8 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_neuf',
				"name"        => _T('iconespedago:neuf'),
				"className"   => 'iconespedago_domino_neuf', 
				"replaceWith" => " :domino9 ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_domino_dix',
				"name"        => _T('iconespedago:dix'),
				"className"   => 'iconespedago_domino_dix', 
				"replaceWith" => " :dominoX ",
				"display"     => true,
			),
		),
	
	));
	
	
$barre->ajouterApres('iconespedago_onglet_domino',
	array(
		// groupe code et bouton <code>
		"id"          => 'iconespedago_onglet_mains',
		"name"        => _T('iconespedago:mains'),
		"className"   => 'iconespedago_onglet_mains',
		"replaceWith" => "",
		"display"     => true,
		"dropMenu"    => array(
			array(
				"id"          => 'iconespedago_gauche_un',
				"name"        => _T('iconespedago:gauche1'),
				"className"   => 'iconespedago_gauche_un', 
				"replaceWith" => " :un-g ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_gauche_deux',
				"name"        => _T('iconespedago:gauche2'),
				"className"   => 'iconespedago_gauche_deux', 
				"replaceWith" => " :deux-g ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_gauche_trois',
				"name"        => _T('iconespedago:gauche3'),
				"className"   => 'iconespedago_gauche_trois', 
				"replaceWith" => " :trois-g ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_gauche_quatre',
				"name"        => _T('iconespedago:gauche4'),
				"className"   => 'iconespedago_gauche_quatre', 
				"replaceWith" => " :quatre-g ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_gauche_cinq',
				"name"        => _T('iconespedago:gauche5'),
				"className"   => 'iconespedago_gauche_cinq', 
				"replaceWith" => " :cinq-g ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_droite_un',
				"name"        => _T('iconespedago:droite1'),
				"className"   => 'iconespedago_droite_un', 
				"replaceWith" => " :un-d ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_droite_deux',
				"name"        => _T('iconespedago:droite2'),
				"className"   => 'iconespedago_droite_deux', 
				"replaceWith" => " :deux-d ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_droite_trois',
				"name"        => _T('iconespedago:droite3'),
				"className"   => 'iconespedago_droite_trois', 
				"replaceWith" => " :trois-d ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_droite_quatre',
				"name"        => _T('iconespedago:droite4'),
				"className"   => 'iconespedago_droite_quatre', 
				"replaceWith" => " :quatre-d ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_droite_cinq',
				"name"        => _T('iconespedago:droite5'),
				"className"   => 'iconespedago_droite_cinq', 
				"replaceWith" => " :cinq-d ",
				"display"     => true,
			),
		),
	
	));


$barre->ajouterApres('iconespedago_onglet_mains',
	array(
		// groupe code et bouton <code>
		"id"          => 'iconespedago_onglet_phonologie',
		"name"        => _T('iconespedago:phonologie'),
		"className"   => 'iconespedago_onglet_phonologie',
		"replaceWith" => "",
		"display"     => true,
		"dropMenu"    => array(
			array(
				"id"          => 'iconespedago_phonologie_oeil',
				"name"        => _T('iconespedago:oeil'),
				"className"   => 'iconespedago_phonologie_oeil', 
				"replaceWith" => " :oeil ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_phonologie_oreille',
				"name"        => _T('iconespedago:oreille'),
				"className"   => 'iconespedago_phonologie_oreille', 
				"replaceWith" => " :oreille ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_phonologie_noeil',
				"name"        => _T('iconespedago:noeil'),
				"className"   => 'iconespedago_phonologie_noeil', 
				"replaceWith" => " :noeil ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_phonologie_noreille',
				"name"        => _T('iconespedago:noreille'),
				"className"   => 'iconespedago_phonologie_noreille', 
				"replaceWith" => " :noreille ",
				"display"     => true,
			),
		),
	
	));


$barre->ajouterApres('iconespedago_onglet_phonologie',
	array(
		// groupe code et bouton <code>
		"id"          => 'iconespedago_onglet_ecriture',
		"name"        => _T('iconespedago:ecriture'),
		"className"   => 'iconespedago_onglet_ecriture',
		"replaceWith" => "",
		"display"     => true,
		"dropMenu"    => array(
			array(
				"id"          => 'iconespedago_ecriture_q5mm',
				"name"        => _T('iconespedago:q5mm'),
				"className"   => 'iconespedago_ecriture_q5mm', 
				"replaceWith" => " :q5mm ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ecriture_q1cm',
				"name"        => _T('iconespedago:q1cm'),
				"className"   => 'iconespedago_ecriture_q1cm', 
				"replaceWith" => " :q1cm ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ecriture_carreaux1cm',
				"name"        => _T('iconespedago:carreaux1cm'),
				"className"   => 'iconespedago_ecriture_carreaux1cm', 
				"replaceWith" => " :carreaux1cm ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ecriture_trou',
				"name"        => _T('iconespedago:trou'),
				"className"   => 'iconespedago_ecriture_trou', 
				"replaceWith" => " :trou ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ecriture_rond',
				"name"        => _T('iconespedago:rond'),
				"className"   => 'iconespedago_ecriture_rond', 
				"replaceWith" => " :rond ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ecriture_ligne',
				"name"        => _T('iconespedago:ligne'),
				"className"   => 'iconespedago_ecriture_ligne', 
				"replaceWith" => " :ligne ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_ecriture_frise',
				"name"        => _T('iconespedago:frise'),
				"className"   => 'iconespedago_ecriture_frise', 
				"replaceWith" => " :frise ",
				"display"     => true,
			),
		),
	
	));


$barre->ajouterApres('iconespedago_onglet_ecriture',
	array(
		// groupe code et bouton <code>
		"id"          => 'iconespedago_onglet_binettes',
		"name"        => _T('iconespedago:binettes'),
		"className"   => 'iconespedago_onglet_binettes',
		"replaceWith" => "",
		"display"     => true,
		"dropMenu"    => array(
			array(
				"id"          => 'iconespedago_binettes_diable',
				"name"        => _T('iconespedago:diable'),
				"className"   => 'iconespedago_binettes_diable', 
				"replaceWith" => " :-> ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_colere',
				"name"        => _T('iconespedago:colere'),
				"className"   => 'iconespedago_binettes_colere', 
				"replaceWith" => " :-(( ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_mdr',
				"name"        => _T('iconespedago:mdr'),
				"className"   => 'iconespedago_binettes_mdr', 
				"replaceWith" => " :-)) ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_sourire',
				"name"        => _T('iconespedago:sourire'),
				"className"   => 'iconespedago_binettes_sourire', 
				"replaceWith" => " :-) ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_oeil',
				"name"        => _T('iconespedago:clin_oeil'),
				"className"   => 'iconespedago_binettes_oeil', 
				"replaceWith" => " ;-) ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_pleure_rire',
				"name"        => _T('iconespedago:pleure_rire'),
				"className"   => 'iconespedago_binettes_pleure_rire', 
				"replaceWith" => " :'-) ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_rigolo',
				"name"        => _T('iconespedago:rigolo'),
				"className"   => 'iconespedago_binettes_rigolo', 
				"replaceWith" => " :o) ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_lunettes',
				"name"        => _T('iconespedago:lunettes'),
				"className"   => 'iconespedago_binettes_lunettes', 
				"replaceWith" => " B-) ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_langues',
				"name"        => _T('iconespedago:langues'),
				"className"   => 'iconespedago_binettes_langues', 
				"replaceWith" => " :-p ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_bof',
				"name"        => _T('iconespedago:bof'),
				"className"   => 'iconespedago_binettes_bof', 
				"replaceWith" => " :-| ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_mouai',
				"name"        => _T('iconespedago:mouai'),
				"className"   => 'iconespedago_binettes_mouai', 
				"replaceWith" => " :-/ ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_surpris',
				"name"        => _T('iconespedago:surpris'),
				"className"   => 'iconespedago_binettes_surpris', 
				"replaceWith" => " :-O ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_mecontent',
				"name"        => _T('iconespedago:mecontent'),
				"className"   => 'iconespedago_binettes_mecontent', 
				"replaceWith" => " :-( ",
				"display"     => true,
			),
			array(
				"id"          => 'iconespedago_binettes_triste',
				"name"        => _T('iconespedago:triste'),
				"className"   => 'iconespedago_binettes_triste', 
				"replaceWith" => " :'-( ",
				"display"     => true,
			),
		),
	
	));

	return $barres;
}

function iconespedago_porte_plume_lien_classe_vers_icone($flux) {
	return array_merge($flux, array(
		'iconespedago_onglet_consignes' => 'bouton_plusieurs.gif',
		'iconespedago_dire' => 'bouton_dire.gif',
		'iconespedago_lire' => 'bouton_lire.gif',
		'iconespedago_ecrire' => 'bouton_ecrire.gif',
		'iconespedago_seul' => 'bouton_seul.gif',
		'iconespedago_plusieurs' => 'bouton_plusieurs.gif',
		'iconespedago_onglet_materiel' => 'bouton_ciseaux.gif',
		'iconespedago_colle' => 'bouton_colle.gif',
		'iconespedago_ciseaux' => 'bouton_ciseaux.gif',
		'iconespedago_equerre' => 'bouton_equerre.png',
		'iconespedago_compas' => 'bouton_compas.gif',
		'iconespedago_gomme' => 'bouton_gomme.gif',
		'iconespedago_crayon' => 'bouton_crayon.gif',
		'iconespedago_rapporteur' => 'bouton_rapporteur.gif',
		'iconespedago_sport' => 'bouton_sport.gif',
		'iconespedago_regle' => 'bouton_regle.gif',
		'iconespedago_pinceau' => 'bouton_pinceau.gif',
		'iconespedago_cahier' => 'bouton_cahier.gif',
		'iconespedago_classeur' => 'bouton_classeur.gif',
		'iconespedago_livre' => 'bouton_livre.gif',
		'iconespedago_onglet_domino' =>'bouton_domino7.png',
		'iconespedago_domino_vide' => 'bouton_domino0.png',
		'iconespedago_domino_zero' => 'bouton_dominoX.png',
		'iconespedago_domino_un' => 'bouton_domino1.png',
		'iconespedago_domino_deux' => 'bouton_domino2.png',
		'iconespedago_domino_trois' => 'bouton_domino3.png',
		'iconespedago_domino_quatre' => 'bouton_domino4.png',
		'iconespedago_domino_cinq' => 'bouton_domino5.png',
		'iconespedago_domino_six' => 'bouton_domino6.png',
		'iconespedago_domino_sept' => 'bouton_domino7.png',
		'iconespedago_domino_huit' => 'bouton_domino8.png',
		'iconespedago_domino_neuf' => 'bouton_domino9.png',
		'iconespedago_domino_dix' => 'bouton_dominovide_temp.png',
		'iconespedago_onglet_mains' => 'bouton_un-g.png',
		'iconespedago_gauche_un' => 'bouton_un-g.png',
		'iconespedago_gauche_deux' => 'bouton_deux-g.png',
		'iconespedago_gauche_trois' => 'bouton_trois-g.png',
		'iconespedago_gauche_quatre' => 'bouton_quatre-g.png',
		'iconespedago_gauche_cinq' => 'bouton_cinq-g.png',
		'iconespedago_droite_un' => 'bouton_un-d.png',
		'iconespedago_droite_deux' => 'bouton_deux-d.png',
		'iconespedago_droite_trois' => 'bouton_trois-d.png',
		'iconespedago_droite_quatre' => 'bouton_quatre-d.png',
		'iconespedago_droite_cinq' => 'bouton_cinq-d.png',
		'iconespedago_onglet_phonologie' => 'bouton_oeil.png',
		'iconespedago_phonologie_oeil' => 'bouton_oeil.png',
		'iconespedago_phonologie_oreille' => 'bouton_oreille.png',
		'iconespedago_phonologie_noeil' => 'bouton_noeil.png',
		'iconespedago_phonologie_noreille' => 'bouton_noreille.png',
		'iconespedago_onglet_ecriture' => 'bouton_frise.png',
		'iconespedago_ecriture_q5mm' => 'bouton_quadrillage5mm.gif',
		'iconespedago_ecriture_q1cm' => 'bouton_quadrillage1cm.gif',
		'iconespedago_ecriture_carreaux1cm' => 'bouton_carreaux1cm.png',
		'iconespedago_ecriture_trou' => 'bouton_trou.png',
		'iconespedago_ecriture_rond' => 'bouton_rond.png',
		'iconespedago_ecriture_ligne' => 'bouton_ligne.png',
		'iconespedago_ecriture_frise' => 'bouton_frise.png',
		'iconespedago_onglet_binettes' => 'bouton_sourire.png',
		'iconespedago_binettes_diable' => 'bouton_diable.png',
		'iconespedago_binettes_colere' => 'bouton_en_colere.png',
		'iconespedago_binettes_mdr' => 'bouton_mort_de_rire.png',
		'iconespedago_binettes_sourire' => 'bouton_sourire.png',
		'iconespedago_binettes_oeil' => 'bouton_clin_d-oeil.png',
		'iconespedago_binettes_pleure_rire' => 'bouton_pleure_de_rire.png',
		'iconespedago_binettes_rigolo' => 'bouton_rigolo.png',
		'iconespedago_binettes_lunettes' => 'bouton_lunettes.png',
		'iconespedago_binettes_langues' => 'bouton_tire_la_langue.png',
		'iconespedago_binettes_bof' => 'bouton_bof.png',
		'iconespedago_binettes_mouai' => 'bouton_mouai.png',
		'iconespedago_binettes_surpris' => 'bouton_surpris.png',
		'iconespedago_binettes_mecontent' => 'bouton_pas_content.png',
		'iconespedago_binettes_triste' => 'bouton_triste.png',
	));
}

?>