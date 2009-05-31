<?php
include_spip('inc/presentation');
include_spip('inc/sites_voir');
include_spip('inc/publiHAL_gestion');
include_spip('inc/publiHAL_voir');

function exec_publihal(){
	global $connect_statut, $connect_toutes_rubriques;
	debut_page(_T('Les publications RSS'), "", "");

debut_gauche();
echo pipeline('affiche_gauche',array('args'=>array('exec'=>'publihal'),'data'=>''));
creer_colonne_droite();
echo pipeline('affiche_droite',array('args'=>array('exec'=>'publihal'),'data'=>''));	  
debut_droite();
	
	gros_titre(_T('Les publications RSS'));
	debut_cadre_relief(  "", false, "", $titre = _T('Initialisations au chargement de la page'));
	debut_boite_info();
	$r=publiHAL_installation();
	echo $r.'<br>';
	if($r&1)	{echo '+++ Ajout du groupe de mots "publiHAL_Type_de_document"';}
	else {echo '--- Pas d\'ajout pour le groupe de mots "publiHAL_Type_de_document"';}
	echo '<br>';
	if($r&2)	{echo '+++ Ajout du groupe de mots "publiHAL_auteurs_publi"';}
	else {echo '--- Pas d\'ajout pour le groupe de mots "publiHAL_auteurs_publi"';}
	echo '<br>';
	if($r&4)	{echo '+++ Ajout du groupe de mots "publiHAL_Labo_publi"';}
	else {echo '--- Pas d\'ajout pour le groupe de mots "publiHAL_Labo_publi"';}
	//publiHAL_Keywords
	echo '<br>';
	if($r&16)	{echo '+++ Ajout du groupe de mots "publiHAL_Keywords"';}
	else {echo '--- Pas d\'ajout pour le groupe de mots "publiHAL_Keywords"';}
	echo '<br>';
	if($r&8)	{echo '+++ Création base "spip_mots_syndic_articles"';}
	else {echo '--- Pas de création de base ';}
	echo '<br>';
//TEST
//	$id_syndic_article=4060;
//	$req="SELECT lesauteurs FROM spip_syndic_articles WHERE id_syndic_article=$id_syndic_article ";
//	$res=spip_fetch_array(spip_query($req));
//	if($res){
//		$lesauteurs=$res['lesauteurs'];
//		echo "<br> $lesauteurs";
//		publiHAL_traite_mots_auteurs($id_syndic_article,$lesauteurs); 
//	}
	fin_boite_info();
	fin_cadre_relief();
	publiHAL_afficher_syndic_articles(_T('titre_dernier_article_syndique'), array('FROM' => 'spip_syndic_articles', 'ORDER BY' => "id_syndic_article DESC"));
	fin_page();
	exit;
}
?>