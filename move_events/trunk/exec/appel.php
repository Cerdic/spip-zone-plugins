<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
 
include_spip('inc/presentation');
 
function exec_appel_dist(){
 
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'appel')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
 
	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'appel'),'data'=>''));
 
	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page(_T('plugin:titre_appel'), "editer", "editer");
 
	// titre
	echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
	echo gros_titre(_T('plugin:titre_appel'),'', false);
 
	// colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'appel'),'data'=>''));
 
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'appel'),'data'=>''));
 
	// centre
	echo debut_droite('', true);
 
	// contenu
	// ...
	echo "on va essayer de faire une liste des événements d'un article";


function liste_evenements(){
	$id_article_origine=1;
	$id_article="\"id_article=".$id_article_origine."\"";

	$types = array();
		$res = sql_select("titre", "spip_evenements",$id_article);
	while ($row = sql_fetch($res)) {
		$types[$row['titre']] = $row;
	}
	print_r ($types);
}
echo "<br/>";
	$id_article_origine=0;
	$id_article_cible=1;
	$where=array('id_article ='.$id_article_origine);

	$res = sql_select("titre,id_evenement", "spip_evenements",$where);
	echo "<ul>";
	while ($row=sql_fetch($res)) {
		echo "<li>".$row['titre']." - ".$row['id_evenement']."</li>";
		$where_id_evenement=array('id_evenement = '.$row['id_evenement']);
		sql_updateq('spip_evenements',array('id_article'=>$id_article_cible),$where_id_evenement);
		echo "<li>L'événement a été migré</li>";
	}
	echo "</ul>";



echo "<br/>";



	// ...
	// fin contenu
 
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'appel'),'data'=>''));
 
	echo fin_gauche(), fin_page();
}
?>