<?php

// insere le css et le js externes pour l'echiquier dans le <head> du document (#INSERT_HEAD)
function chess_insert_head($flux){
//	$flux .= "<link rel='stylesheet' type='text/css' href='".generer_url_public('chess.css')."' />";
//	(le CSS est actuellement lance par le JavaScript)
	$flux .= "<script src='".generer_url_public('chess.js')."' type='text/javascript'></script>" . "\n";
//	return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
    return $flux;
}

function chess_header_prive($flux) {
    return chess_insert_head($flux);
}

?>