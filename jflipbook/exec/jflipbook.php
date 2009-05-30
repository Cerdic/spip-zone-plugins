<?php 
function exec_jflipbook() {
   include_spip("inc/presentation");
    // vérifier les droits
   global $connect_statut;
   global $connect_toutes_rubriques;
   if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
       debut_page(_T('titre'), "jflipbook_admin", "plugin");
       echo _T('avis_non_acces_page');
       fin_page();
       exit;
   }

$icone = _DIR_PLUGIN_JFLIPBOOK."/img_pack/jflip.png";
$commencer_page = charger_fonction('commencer_page', 'inc');

$jflip = 'jflip';
$descriptifmot = 'mot-clé permettant de transformer un article contenant des images en documents joints en livre jFlipBook';

   echo $commencer_page(_T('jflipbook:titre_page'),'','','');	 
   echo "<br />";
   
   echo gros_titre(_T('jflipbook:titre_page'),'',false);
   echo debut_gauche('',true);
	
   echo debut_boite_info(true);
   echo _T('jflipbook:boite_info');
	echo _T('jflipbook:projeteva');
	echo _T('jflipbook:texte_descriptif');
   echo '<br /><br /> Documentation officielle EVA-WEB :';
   echo '<br /><a href="http://eva-web.edres74.net/spip.php?rubrique4" target="_blank" >Documentation eva-web</a>';
   echo fin_boite_info(true);
		
   echo debut_droite('',true);
   echo debut_cadre_trait_couleur($icone, true,'', _T('jflipbook:titre_boite_principale'));
   echo debut_cadre_couleur('',true);

//Texte description du plugin   
   echo _T('jflipbook:texte_descriptif');

//Vérification de la configuration du site
   echo _T('jflipbook:verification_conf');
	echo _T('jflipbook:conf_mots');	
	$confmot = jflipbook_test_conf('articles_mots');
	if ($confmot == 'oui'){
	   echo 'La configuration des mots-clés est correcte';
	   }else{
	   echo "Activez l'utilisation des mots-clés pour utiliser ce plugin.<br />";
	   echo "Cliquez ici : <a href='?exec=configuration' class='fondo'>Allez à la page de configuration</a><br />";
	   }
	echo "<br />";
	
	echo _T('jflipbook:conf_docs');
	$confdoc = jflipbook_test_conf('documents_article');
	if ($confdoc == 'oui'){
	   echo 'La configuration des documents joints aux articles est correcte';
	   }else{
	   echo "Vous devez autoriser les documents joints aux articles pour utiliser ce plugin.<br />";
	   echo "Cliquez ici : <a href='?exec=configuration' class='fondo'>Allez à la page de configuration</a><br />";
	   }
	echo "<br />";
	   
//Gestion du mot-clé jflip   
	echo _T('jflipbook:texte_motcle');
	echo "Mot-clé nécessaire : ".$jflip."<br />";
	$presence = jflipbook_test_mots($jflip);
	
	if ($presence == false){
		echo 'il faut créer '.$jflip.' mots-clés<br/>';
			if (_request('creermot')!=NULL){
				jflipbook_crea_mots($jflip,$descriptifmot);
			}
			echo '<form method="POST" action="'.generer_url_ecrire("jflipbook").'">';
			echo "<input type='submit' name='creermot' value='"._T('jflipbook:creer_mots')."' class='fondo'>";
			echo "</form>";
	}else{
	echo 'Le mot-clé '.$jflip.' existe bien.<br/>';
	}
	
//Rappel des réglages en cours
	echo _T('jflipbook:texte_reglages');
   echo "<br />"._T('jflipbook:largeur').lire_config('jflipbook/largeur');
   echo "<br /><br />"._T('jflipbook:hauteur').lire_config('jflipbook/hauteur');
   echo "<br /><br />"._T('jflipbook:couleur').lire_config('jflipbook/couleur');
   
   echo "<br /><br />"._T('jflipbook:coins');
   echo _T('jflipbook:'.lire_config('jflipbook/corners').'');
   
   echo "<br /><br />"._T('jflipbook:scale');
   echo _T('jflipbook:'.lire_config('jflipbook/scale').'');
   
   echo '<br />';
   
   echo fin_cadre_couleur(true);
   
   echo '<form method="post" action="?exec=cfg&cfg=jflipbook">';
    echo '<input type="submit" class="fondo" value="';
    echo _T('jflipbook:modif_conf');
    echo '" />';
    echo '</form>';
   echo '<br />';
   
   echo fin_cadre_trait_couleur(true);
   echo fin_gauche(), fin_page();
}

####################
function jflipbook_test_mots($jflip){

$resultat = sql_select('id_mot','spip_mots',"titre='".$jflip."'");
$nb = sql_count($resultat);

if ($nb == 0){
	return false;
	}else{
	return true;
	}
}
####################
function jflipbook_crea_mots($jflip, $descriptifmot){
echo 'Cr&eacute;ation du mots-cl&eacute;s '.$jflip.' <br/>';
$ajout_mot = sql_insertq('spip_mots',array('titre'=>$jflip,'descriptif'=>$descriptifmot));

}
####################	
function	jflipbook_test_conf($nom){
$resultat = sql_select('valeur','spip_meta',"nom='".$nom."'");
$nb = sql_count($resultat);
$row = sql_fetch($resultat);

if ($nb == 1){
	return $row['valeur'];
	}else{
	return false;
	}
}
####################	
	 

?>