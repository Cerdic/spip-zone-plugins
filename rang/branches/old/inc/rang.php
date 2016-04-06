<?php

function update_rang($rang,$objet,$id_objet) {
	switch($objet) {
		case 'rubrique' :
			$id_table = 'id_rubrique';
			break;
		case 'article' :
			$id_table = 'id_article';
			break;
	}
	sql_updateq("spip_".$objet."s", array('rang' => $rang), "id_".$objet.'='.$id_objet);    
}

function extraire_rang($texte) {
	list($rang,$titre) = explode(".", $texte, 2);
	$rang = trim($rang);
	$titre = trim($titre);
	if (!is_numeric($rang)) {
		$rang = null;
		$titre = $texte;
	}
	return array('rang'=>$rang,'titre'=>$titre);
}

?>
