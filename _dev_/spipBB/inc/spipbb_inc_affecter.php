<?php
/*
+-------------------------------------------+
| Hugues AROUX - SCOTY @ koakidi.com
+-------------------------------------------+
| functions hierarchie (spipbb_affecter_thread.php)
|( anc. heritage.php (gaf) )
+-------------------------------------------+
*/


//
// les pitis articles de grand_ma rubrique
//
function bb_article($id_rubrique, $rang_rub, $id_art) {
global $couleur_claire;

$req=spip_query("SELECT id_article, id_rubrique, titre FROM spip_articles WHERE id_rubrique=$id_rubrique");
$nbr_rep=spip_num_rows($req);
	if($nbr_rep > 0) {
		while ($row=spip_fetch_array($req)) {
			$id_article=$row['id_article'];
			$id_rubrique=$row['id_rubrique'];
			$titre=$row['titre'];
			$retrait = 35*$rang_rub;
			debut_ligne_grise($retrait);
			// bloque si article d'origine
			if($id_article == $id_art)
				{
				echo "<div style='float:right; padding:3px; text-align:right; 
						border:2px solid ".$couleur_claire."; -moz-border-radius:5px;'>\n";
				echo http_img_pack("puce-blanche.gif",'o',"align='absmiddle'");
				echo "</div>\n";
				}
			else
				{
				echo "<div style='float:right; padding:3px; text-align:right; 
						border:2px solid ".$couleur_claire."; -moz-border-radius:5px;'>\n";
				echo "<input type='radio' name='id_art_new' value='".$id_article."'>";
				echo "</div>\n";			
				}
			echo "<img src='"._DIR_IMG_SPIPBB."gaf_forum.gif' align='absmiddle'>\n 
				<span class='verdana2'>".$id_article."</span> - <b>".propre($titre)."</b>\n";
			fin_bloc();
		}
	echo "<div align='right'><input type='submit' value='"._T('spip:bouton_valider')."' class='fondo'></div>\n";
	}
}

//
// grand_ma et ses pitis
//
function grand_ma($id_rubrique, $rang_rub, $id_art) {

$req=spip_query("SELECT id_rubrique, titre FROM spip_rubriques WHERE id_parent=$id_rubrique");
	while ($row=spip_fetch_array($req)) 	{
		$id_rubrique=$row['id_rubrique'];
		$titre=$row['titre'];
		$rang_rub++;
		$retrait = 20*$rang_rub;
		debut_ligne_claire($retrait);
		echo http_img_pack("rubrique-12.gif","rub"," align='absmiddle'");
		echo "&nbsp;<span class='verdana2'>".$id_rubrique."</span> - <b>".propre($titre)."</b>\n";
		fin_bloc();
	
		bb_article($id_rubrique, $rang_rub, $id_art);
		$rang_rub--;
		grand_ma($id_rubrique, $rang_rub, $id_art);
	}
}
?>
