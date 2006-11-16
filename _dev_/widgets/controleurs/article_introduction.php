<?php

function controleurs_article_introduction_dist($regs) {
    list(,$widget,$type,$champ,$id) = $regs;
    $valeur = valeur_colonne_table($type, array('descriptif', 'chapo', 'texte'), $id);
    if ($valeur === false) {
	    return array("$type $id $champ: " . _U('widgets:pas_de_valeur'), 6);
    }

    // largeur du widget
    $w = min(max(intval($_GET['w']), 100), 700);
    // hauteur maxi d'un textarea selon wh: window height
    $maxheight = min(max(intval($_GET['wh']) - 50, 400), 700);
    $h = min(max(intval($_GET['h']), 234), $maxheight);
    
// en utilisant les inputs défauts
    $inputAttrs = array(
    	'descriptif' => array('type' => 'texte', 'attrs' => array(
	        'style' => "width:${w}px; height:" . (int)ceil($h*2/13) . "px;")),
		'chapo' =>  array('type' => 'texte', 'attrs' => array(
	        'style' => "width:${w}px; height:" . (int)ceil($h*4/13) . "px;")),
		'texte' =>  array('type' => 'texte', 'attrs' => array(
	        'style' => "width:${w}px; height:" . (int)ceil($h*4/13) . "px;")));
//	$n = new Widget('article-introduction-' . $id, $valeur);

// pour la methode par modeles
    $contexte = array(
    	'h_descriptif' => (int)ceil($h*2/13),
		'h_chapo' => (int)ceil($h*4/13),
		'h_texte' => (int)ceil($h*4/13));
	$n = new Widget('article-introduction-' . $id, $valeur,
			array('largeur'=>$w, 'hauteur'=>$h));
        $html = $n->formulaire($contexte);
        $status = NULL;

	return array($html, $status);
}

?>
