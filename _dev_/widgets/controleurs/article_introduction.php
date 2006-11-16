<?php

function controleurs_article_introduction_dist($regs) {
    list(,$widget,$type,$champ,$id) = $regs;
    $valeur = valeur_colonne_table($type, array('descriptif', 'chapo', 'texte'), $id);
    if ($valeur === false) {
	    return array("$type $id $champ: " . _U('widgets:pas_de_valeur'), 6);
    }

    // taille du widget
    $w = intval($_GET['w']);
    $w = $w<100 ? 100 : $w;
    $w = $w>700 ? 700 : $w;
    $h = intval($_GET['h']);
    $h = $h<234 ? 234 : $h;
    // hauteur maxi d'un textarea -- pas assez ? trop ?
    $wh = intval($_GET['wh']); // window height
    $maxheight = min(max($wh-50,400), 700);
    if ($h>$maxheight) $h=$maxheight;
    
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
