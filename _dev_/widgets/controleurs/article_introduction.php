<?php

function controleurs_article_introduction_dist($regs) {
    list(,$widget,$type,$champ,$id) = $regs;
    $valeur = valeur_colonne_table($type, array('descriptif', 'chapo', 'texte'), $id);
    if ($valeur === false) {
	    return array("$type $id $champ: " . _U('widgets:pas_de_valeur'), 6);
    }

	$n = new Widget('article-introduction-' . $id, $valeur,
			array('hauteurMini' => 234));
    
// en utilisant les inputs défauts
    $inputAttrs = array(
    	'descriptif' => array('type' => 'texte', 'attrs' => array(
	        'style' => "width:${w}px; height:" . (int)ceil($n->hauteur*2/13) . "px;")),
		'chapo' =>  array('type' => 'texte', 'attrs' => array(
	        'style' => "width:${w}px; height:" . (int)ceil($n->hauteur*4/13) . "px;")),
		'texte' =>  array('type' => 'texte', 'attrs' => array(
	        'style' => "width:${w}px; height:" . (int)ceil($n->hauteur*4/13) . "px;")));
//	$n = new Widget('article-introduction-' . $id, $valeur);

// pour la methode par modeles
    $contexte = array(
    	'h_descriptif' => (int)ceil($n->hauteur*2/13),
		'h_chapo' => (int)ceil($n->hauteur*4/13),
		'h_texte' => (int)ceil($n->hauteur*4/13));
        $html = $n->formulaire($contexte);
        $status = NULL;

	return array($html, $status);
}

?>
