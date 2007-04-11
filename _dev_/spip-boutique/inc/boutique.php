<?php
//
// inc/boutique.php
//

function afficher_liste_parents($id_parent){

	$res_categories = spip_query("SELECT * FROM spip_boutique_categories WHERE id_parent = 0;");
	
	echo debut_cadre_couleur(_DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png",false,false,_T('boutique:dans_la_categorie'));
	if (mysql_num_rows($res_categories) > 0){
		
		echo '<select name="id_parent" class="formo">';
		
		echo '<option value="0"'; if ($id_parent == 0) { echo ' selected'; } echo '>'._T('boutique:racine_de_la_boutique').'</option>';
		
		while ($liste_categories = spip_fetch_array($res_categories)){
			echo '<option value="'.$liste_categories['id_categorie'].'" '; if ($id_parent == $liste_categories['id_categorie']) { echo 'selected'; } echo ' >'.$liste_categories['titre'].'</option>';
		}
		
		echo '</select><br />';
		
	}else{
		echo '<input type="hidden" name="id_parent" value="0" />'._T('boutique:racine_de_la_boutique');
	}
	echo fin_cadre_couleur();

}

?>
