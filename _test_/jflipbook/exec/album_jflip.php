<?php 
function exec_album_jflip() {
   include_spip("inc/presentation");
    // vérifier les droits
   global $connect_statut;
   global $connect_toutes_rubriques;
   if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
       debut_page(_T('titre'), "albumjflip_admin", "plugin");
       echo _T('avis_non_acces_page');
       fin_page();
       exit;
   }

$icone = _DIR_PLUGIN_ALBUM_JFLIP."/img_pack/jflip.png";
$commencer_page = charger_fonction('commencer_page', 'inc');

$jflip = 'jflip';
$descriptifmot = 'mot-clé permettant de transformer un article contenant des images en documents joints en livre jFlipBook';

   echo $commencer_page(_T('albumjflip:titre_page'),'','','');	 
   echo "<br />";
   
   echo gros_titre(_T('albumjflip:titre_page'),'',false);
   echo debut_gauche('',true);
	
   echo debut_boite_info(true);
   echo _T('albumjflip:boite_info');

	echo _T('albumjflip:texte_descriptif');
   echo '<br /><br /> Documentation officielle EVA-WEB :';
   echo '<br /><a href="http://eva-web.edres74.net/spip.php?rubrique4" target="_blank" >Documentation eva-web</a>';
   echo fin_boite_info(true);
	
   //echo debut_raccourcis();
   //echo 'contenu de la boite des raccourcis du plugin';
   //echo fin_raccourcis();
		
   echo debut_droite('',true);
   echo debut_cadre_trait_couleur($icone, true,'', _T('albumjflip:titre_boite_principale'));
   echo debut_cadre_couleur('',true);

//Texte description du plugin   
   echo _T('albumjflip:texte_descriptif');

//Vérification de la configuration du site
   echo _T('albumjflip:verification_conf');
	echo _T('albumjflip:conf_mots');	
	$confmot = test_conf('articles_mots');
	if ($confmot == 'oui'){
	   echo 'La configuration des mots-clés est correcte';
	   }else{
	   echo "Activez l'utilisation des mots-clés pour utiliser ce plugin.<br />";
	   echo "Cliquez ici : <a href='?exec=configuration' class='fondo'>Allez à la page de configuration</a><br />";
	   }
	echo "<br />";
	
	echo _T('albumjflip:conf_docs');
	echo "";	
	$confdoc = test_conf('documents_article');
	if ($confdoc == 'oui'){
	   echo 'La configuration des documents joints aux articles est correcte';
	   }else{
	   echo "Vous devez autoriser les documents joints aux articles pour utiliser ce plugin.<br />";
	   echo "Cliquez ici : <a href='?exec=configuration' class='fondo'>Allez à la page de configuration</a><br />";
	   }
	echo "<br />";
	   
//Gestion du mot-clé jflip   
	echo _T('albumjflip:texte_motcle');
	echo "Mot-clé nécessaire : ".$jflip."<br />";
	$presence = test_mots($jflip);
	
	if ($presence == false){
		echo 'il faut créer '.$jflip.' mots-clés<br/>';
			if (_request('creermot')!=NULL){
				crea_mots($jflip,$descriptifmot);
				#echo 'id aff ='.$id_aff.'<br/>';
			}
			echo '<form method="POST" action="'.generer_url_ecrire("album_jflip").'">';
			echo "<input type='submit' name='creermot' value='"._T('albumjflip:creer_mots')."' class='fondo'>";
			echo "</form>";
	}else{
	echo 'Le mot-clé '.$jflip.' existe bien.<br/>';
	}
	
//Rappel des réglages en cours
	echo _T('albumjflip:texte_reglages');
   echo "<br />"._T('albumjflip:largeur').lire_config('album_jflip/largeur');
   echo "<br /><br />"._T('albumjflip:hauteur').lire_config('album_jflip/hauteur');  
   echo "<br /><br />"._T('albumjflip:couleur').lire_config('album_jflip/couleur');
   echo "<br /><br />"._T('albumjflip:coins').lire_config('album_jflip/corners');  
   echo "<br /><br />"._T('albumjflip:scale').lire_config('album_jflip/scale');    
   echo '<br />';
   
   echo fin_cadre_couleur(true);
   
   echo '<form method="post" action="?exec=cfg&cfg=album_jflip">';
    echo '<input type="submit" class="fondo" value="';
    echo _T('albumjflip:modif_conf');
    echo '" />';
    echo '</form>';
   echo '<br />';

   echo '<form method="post" action="../">';
    echo '<input type="submit" class="fondo" value="';
    echo _T('albumjflip:page_publique');
    echo '" />';
    echo '</form>';
   
   echo fin_cadre_trait_couleur(true);
   echo fin_gauche(), fin_page();
}

####################
function test_mots($jflip){

//echo 'jflip = '.$jflip.'<br/>';

$resultat = sql_select('id_mot','spip_mots',"titre='".$jflip."'");
$nb = sql_count($resultat);

//echo 'nombre trouve pour '.$jflip.'= '.$nb.'<br>';

if ($nb == 0){
	//echo 'il manque '.$jflip.' mots-clés<br/>';
	return false;
	}else{
	//echo 'C\'est OK.<br/>';
	return true;
	}
#echo '----------------------------<br/>';
}
####################
function crea_mots ($jflip, $descriptifmot){
#echo 'Dans la fonction, id = '.$id.'<br/>';

#echo "<br/>-----------------------------------<br/>";

#echo "Valeur de id du groupe =".$id."<br/>";
echo 'Cr&eacute;ation du mots-cl&eacute;s '.$jflip.' <br/>';

$ajout_mot = sql_insertq('spip_mots',array('titre'=>$jflip,'descriptif'=>$descriptifmot));

}
####################	
function	test_conf($nom){
$resultat = sql_select('valeur','spip_meta',"nom='".$nom."'");
$nb = sql_count($resultat);
$row = sql_fetch($resultat);

if ($nb == 1){
	//echo 'nb = '.$nb;
	//echo '<br />';
	//echo 'resultat = '.$resultat;
	//echo '<br />';
	//echo 'row = '.$row['valeur'];
	return $row['valeur'];
	}else{
	//echo 'ERREUR';
	return false;
	}
}
####################	
	 

?>