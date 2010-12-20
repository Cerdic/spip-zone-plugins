<?php
function genespip_evt($id_type_evenement,$id_individu){

	$resultevt = sql_select("*", "spip_genespip_evenements,spip_genespip_lieux,spip_genespip_type_evenements", "spip_genespip_type_evenements.id_type_evenement=spip_genespip_evenements.id_type_evenement and spip_genespip_evenements.id_type_evenement='".sql_quote($id_type_evenement)."' and spip_genespip_evenements.id_lieu=spip_genespip_lieux.id_lieu and spip_genespip_evenements.id_individu = '".sql_quote($id_type_evenement)."'");
	while ($evt = spip_fetch_array($resultevt)) {
		$date_evt=genespip_datefr($evt['date_evenement']);
		if ($evt['id_epoux']!=0){$union="<b>"._T('genespip:avec')." ".genespip_nom_prenom($evt['id_epoux'],1)."</b>";}else{$union=NULL;}
		$precision_date=$evt['precision_date'];
		$lieu = $evt['ville'].", ".$evt['departement']."(".$evt['code_departement']."), ".$evt['region'].", ".$evt['pays'];
		$lieu_court = $evt['ville'].", ".$evt['departement']."(".$evt['code_departement'].")";
		$id_lieu = $evt['id_lieu'];
		$clair_evenement= $evt['clair_evenement'];
		echo "<br /><fieldset><legend>$id"._T('genespip:'.$clair_evenement)."</legend>";
		echo "<table width='100%'>";
		echo '<form action="'.$url_action_fiche.'" method="post">';
		echo "<tr><td colspan='3'>".$precision_date." ".$date_evt.", ".$lieu." ".$union."</td></tr>";
		echo "<tr><td>"._T('genespip:date')."&deg;</td>";
		echo "<td><select name='precision_date' size='1'>",
			 "<option value='".$precision_date."'>".$precision_date."</option>",
			 "<option value=''>=</option>",
			 "<option value='~'>~</option>",
			 "<option value='<'><</option>",
			 "<option value='>'>></option>",
			 "</select></td>";
		echo "<td><i>jj/mm/aaaa</i> <input name='date_evenement' value='".$date_evt."' size='15' /></td></tr>";
		echo "<tr><td>"._T('genespip:lieu')."</td>";
		echo "<td colspan='2'><select name='id_lieu' size='1'>";
		echo "<option value='".$id_lieu."'>".$lieu_court."</option>";
		$resultlieu = sql_select('* ', 'spip_genespip_lieux', 'ville');
		while ($lieu = spip_fetch_array($resultlieu)) {
			echo "<option value='".$lieu['id_lieu']."'>".$lieu['ville'].", ".$lieu['departement']."(".$lieu['code_departement'].")</option>";
		}
		echo "</select></td></tr>";
		echo "<input name='id_type_evenement' type='hidden' value='".$evt['id_type_evenement']."'>";
		echo "<input name='id_epoux' type='hidden' value='".$evt['id_epoux']."'>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
		echo "<input name='edit' type='hidden' value='up_evt'>";
		echo "<tr><td colspan='3'><hr /><table><tr><td>";
		echo "<input name='submit' type='submit' value='"._T('genespip:modifier')."' class='fondo'></form>";
		echo "</td><form action='".$url_action_fiche."' method='post'><td>";
		echo "<input name='submit' type='submit' value='"._T('genespip:supprimer')."' class='fondo'>";
		echo "<input name='id_evenement' type='hidden' value='".$evt['id_evenement']."'>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
		echo "<input name='edit' type='hidden' value='del_evt'>";
		echo "</form></td></tr></table></td></tr>";
		echo "</table>";
		echo "</fieldset>";
	}
}

function genespip_new_evt($id_individu,$id_type_evenement){
	$resultevt = sql_select('* ', 'spip_genespip_type_evenements', 'id_type_evenement='.sql_quote($id_type_evenement));
	while ($evt = spip_fetch_array($resultevt)) {
		$clair_evenement=$evt['clair_evenement'];
		$type_evenement=$evt['type_evenement'];
	}
	echo "<br /><fieldset><legend>$id"._T('genespip:'.$clair_evenement)."</legend>";
	if ($type_evenement=='MARR'){
		//Listing des noms pour nouvelle_union
		echo "<table style='border:1px;border-color:black'>";
		echo "<form action='".$url_action_fiche."' method='post'>";
		$sql = sql_select('nom', 'spip_genespip_individu', 'nom') or die (_T('genespip:requete_invalide'));
		echo "<tr><td><select name='choix_nom'>";
		echo "<option value=''>-- NOM --</option>";
		while ($list = spip_fetch_array($sql)) {
			$nom = strtoupper($list['nom']);
			echo "<option value='$nom'>$nom</option>";
		}
		echo "</select></td>";
		echo "<td>&mdash;&mdash;&rsaquo;"._T('genespip:nouvelle_union')."&mdash;&mdash;&rsaquo;</td>";
		echo "<td><INPUT TYPE='submit' VALUE='"._T('genespip:choisir')."' class='fondo' /></td></tr>";
		echo "<input name='actionnom' type='hidden' value='choixnom' />";
		echo "<input name='edit' type='hidden' value='choix_evt'>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
		echo "<input name='id_type_evenement' type='hidden' value='".$id_type_evenement."'>";
		echo "</form>";
		echo "<form action='".$url_action_fiche."' method='post'><tr><td colspan='3'><hr />";
		echo "<input name='submit' type='submit' value='"._T('genespip:annuler')."' class='fondo'>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'></td></tr>";
		echo "</table>";
		
		if ($_POST['actionnom']=='choixnom'){
			echo "<br /><fieldset><legend>"._T("genespip:liste_des_personnes_nees ".$_POST['choix_nom'])."</b></i></legend>";
			echo "<table style='border:1px;border-color:black'>";
			echo "<form action='".$url_action_fiche."' method='post'>";
			$result_sexe = sql_select('sexe', 'spip_genespip_individu', 'id_individu='.sql_quote(_request('id_individu')));
			while ($sexe = spip_fetch_array($result_sexe)){
				$sexe_res = $sexe['sexe'];
				echo $_POST['id_individu']."/sexe=".$sexe_res;
			}
			$result_epoux = sql_select('id_individu, nom, prenom', 'spip_genespip_individu', 'sexe!=$sexe_res and nom='.sql_quote(_request('choix_nom')).' and poubelle <> 1', 'prenom');
			while ($liste = spip_fetch_array($result_epoux)) {
				$result_date = sql_select('id_type_evenement,date_evenement', 'spip_genespip_evenements', 'id_individu="'.sql_quote($liste['id_individu']).'" and id_type_evenement<>3');
				$date_BD="(&ordm;inconnu-&dagger;inconnu)";
				while ($liste_date = spip_fetch_array($result_date)) {
					if ($liste_date['id_type_evenement']==1){$date_naissance=genespip_datefr($liste_date['date_evenement']);}else{$date_naissance="inconnu";}
					if ($liste_date['id_type_evenement']==2){$date_deces=genespip_datefr($liste_date['date_evenement']);}else{$date_deces="inconnu";}
					$date_BD="(&ordm;".$date_naissance."-&dagger;".$date_deces.")";
				}
				echo "<tr><td><input type='radio' name='id_epoux' value='".$liste['id_individu']."' /></td>";
				echo "<td><small>[".$liste['id_individu']."]</small> ".$liste['nom']."&nbsp;".$liste['prenom']."&nbsp;".$date_BD."</td></tr>";
			}
			echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
			echo "<input name='id_lieu' type='hidden' value='1'>";
			echo "<input name='id_type_evenement' type='hidden' value='".$id_type_evenement."'>";
			echo "<input name='edit' type='hidden' value='add_evt'>";
			echo "<tr><td><INPUT TYPE='submit' VALUE='"._T('genespip:valider')."' class='fondo' /></td></tr>";
			echo "</form>";
			echo "</table>";
			echo "</fieldset>";
		}
		
	}else{
		echo "<table width='100%'>";
		echo "<form action='".$url_action_fiche."' method='post'>";
		echo "<tr><td>"._T('genespip:date')."&deg;</td>";
		echo "<td><select name='precision_date' size='1'>",
			 "<option value=''>=</option>",
			 "<option value='~'>~</option>",
			 "<option value='<'><</option>",
			 "<option value='>'>></option>",
			 "</select></td>";
		echo "<td><i>jj/mm/aaaa</i> <input name='date_evenement' value='' size='15' /></td></tr>";
		echo "<tr><td>"._T('genespip:lieu')."</td>";
		echo "<td colspan='2'><select name='id_lieu' size='1'>";
		$resultlieu = sql_select('*', 'spip_genespip_lieux', 'ville');
		while ($lieu = spip_fetch_array($resultlieu)) {
			echo "<option value='".$lieu['id_lieu']."'>".$lieu['ville'].", ".$lieu['departement']."(".$lieu['code_departement'].")</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td colspan='3'><hr /><table><tr><td>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
		echo "<input name='edit' type='hidden' value='add_evt'>";
		echo "<input name='id_type_evenement' type='hidden' value='".$id_type_evenement."'>";
		echo "<input name='submit' type='submit' value='"._T('genespip:creer')."' class='fondo'></form>";
		echo "</td><form action='".$url_action_fiche."' method='post'><td>";
		echo "<input name='submit' type='submit' value='"._T('genespip:annuler')."' class='fondo'>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
		echo "</form></td></tr></table></td></tr>";
		echo "</table>";
	}
	echo "</fieldset>";
}
?>
