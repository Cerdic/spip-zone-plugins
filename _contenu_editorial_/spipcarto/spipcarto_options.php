<?php
echo "chargement choses_possibles spipcarto";

$choses_possibles['carto_objets'] = array(
									  'titre_chose' => 'Objets',
									  'id_chose' => 'id_carto_objet',
									  'table_principale' => 'spip_carto_objets',
									  
									  'table_carte' => 'spip_carto_cartes',
									  'tables_limite' => array(
															   'carto_objets' => array(
																				   'table' => 'spip_carto_objets',
																				   'nom_id' => 'id_carto_objet'),
															   'carto_cartes' => array(
																					'table' => 'spip_carto_objets',
																					'nom_id' =>  'id_carto_carte'),
															   )
									  );
////////////////////////////////////////////////////////////////////////
function afficher_liste_carto_objets($choses) {
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2($titre_table, "../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif");
  
  echo afficher_liste_debut_tableau();
  
  $from = array('spip_carto_objets as carto_objets');
  $select= array();
  $select[] = 'id_carto_objet';
  $select[] = 'titre';
  $select[] = 'url_objet';
  $select[] ='id_carto_carte';
//  $select[] = 'statut';
  $where = array('carto_objets.id_carto_objet IN ('.calcul_in($choses).')');
  
  $result = spip_abstract_select($select,$from,$where);
  $i = 0;
  while ($row = spip_abstract_fetch($result)) {
	$i++;
	$vals = '';
	
	$id_carto_objet = $row['id_carto_objet'];
	$tous_id[] = $id_carto_objet;
	$titre = $row['titre'];
	$id_carto_carte = $row['id_carto_carte'];
	$url_objet = $row['url_objet'];
	
	$vals[] = "<input type='checkbox' name='id_choses[]' value='$id_carto_objet' id='id_chose$i'/>";
	
	// Le titre (et la langue)
	$s = "<div>";
	
	$s .= "<a href=\"carte_edit.php3?id_carte=$id_carto_carte#objet$id_carto_objet\" style=\"display:block;\">";
	
	$s .= typo($titre);
	$s .= "</a>";
	$s .= "</div>";
	
	$vals[] = $s;
	
	// L'url
	$s = "<a href=\"$url_objet\" style=\"display:block;\">lien</a>";
	$vals[] = $s;
	
	// Le numero (moche)
	if ($options == "avancees") {
	  $vals[] = "<b>"._T('info_numero_abbreviation')."$id_carto_objet</b>";
	}
	
	
	$table[] = $vals;
  }
  spip_free_result($result);
  
  if ($options == "avancees") { // Afficher le numero (JMB)
	if ($afficher_auteurs) {
	  $largeurs = array(11, '', 80, 100, 35);
	  $styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
	} else {
	  $largeurs = array(11, '', 100, 35);
	  $styles = array('', 'arial2', 'arial1', 'arial1');
	}
  } else {
	if ($afficher_auteurs) {
	  $largeurs = array(11, '', 100, 100);
	  $styles = array('', 'arial2', 'arial1', 'arial1');
	} else {
	  $largeurs = array(11, '', 100);
	  $styles = array('', 'arial2', 'arial1');
	}
  }
  afficher_liste($largeurs, $table, $styles);
  
  echo afficher_liste_fin_tableau();
}
?>