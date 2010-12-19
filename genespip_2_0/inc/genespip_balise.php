<?php
if (!defined("_ECRIRE_INC_VERSION")) return;    #securite

function quand($resultat) {
    $split = split(',',$resultat);
    $id_individu = $split[0];
    $type_evenement = $split[1];
    $id_epoux = $split[2];
  if($id_individu){
    if($id_epoux){
    $req = sql_select("date_evenement, precision_date", "spip_genespip_evenements, spip_genespip_type_evenements", "spip_genespip_evenements.id_individu='$id_individu' and spip_genespip_type_evenements.id_type_evenement=spip_genespip_evenements.id_type_evenement and type_evenement='$type_evenement' and id_epoux='$id_epoux'");
    }else{
    $req = sql_select("date_evenement, precision_date", "spip_genespip_evenements, spip_genespip_type_evenements", "spip_genespip_evenements.id_individu='$id_individu' and spip_genespip_type_evenements.id_type_evenement=spip_genespip_evenements.id_type_evenement and type_evenement='$type_evenement'");
    }
     while ($row = spip_fetch_array($result)){
//test si l'année de l'évènement est inférieure à 100 ans
      $splitcentans = split('-',$row['date_evenement']);
      if ($splitcentans[0]+100>=date("Y")){$depasse="oui";}else{$depasse="non";}
// fin test -> voir variable $depasse oui ou non

      $resultat = $row['precision_date']." ".genespip_datefr($row['date_evenement']);
if (!($GLOBALS['auteur_session']['statut'] == '0minirezo' or $GLOBALS['auteur_session']['statut'] == '1comite'))
    {
// on vérifie si le site accepte les date de moins de 100 ans oui si centans=0, non si centans=1
//si non on affiche le cadenas à la place de la date
      $verifcentans=sql_select('centans', 'spip_genespip_parametres');
      if ($rowverifcentans = spip_fetch_array($verifcentans)){$centans=$rowverifcentans['centans'];}
      if ($centans==1 and $depasse=="oui"){
         $resultat="<img src='"._DIR_PLUGIN_GENESPIP."img_pack/limit.png' width='15px' alt='"._T('genespip:restreint')."'>";
         }else{
// si oui on test au cas par cas en fonction du champ limitation
          $verif=sql_select('limitation', 'spip_genespip_individu', 'id_individu=.$id_individu');
          if ($rowverif = spip_fetch_array($verif)){$limitation=$rowverif['limitation'];}
          if ($limitation==1){
          $resultat="<img src='"._DIR_PLUGIN_GENESPIP."img_pack/limit.png' width='15px' alt='"._T('genespip:restreint')."'>";
          }
         }
    }
    return $resultat;
    }
  }
}
function m_j($resultat) {
    $split = split(',',$resultat);
    $id_individu = $split[0];
    $type_evenement = $split[1];
    $id_epoux = $split[2];
  if($id_individu){
    if($id_epoux){
    $req = sql_select("date_evenement, precision_date", "spip_genespip_evenements, spip_genespip_type_evenements", "spip_genespip_evenements.id_individu='$id_individu' and spip_genespip_type_evenements.id_type_evenement=spip_genespip_evenements.id_type_evenement and type_evenement='$type_evenement' and id_epoux='$id_epoux'");
    }else{
    $req = sql_select("date_evenement, precision_date", "spip_genespip_evenements, spip_genespip_type_evenements", "spip_genespip_evenements.id_individu='$id_individu' and spip_genespip_type_evenements.id_type_evenement=spip_genespip_evenements.id_type_evenement and type_evenement='$type_evenement'");
    }
     while ($row = spip_fetch_array($result)){
     $split = split('-',$row['date_evenement']);
     $resultat = $split[1]."-".$split[2];
    return $resultat;
    }
  }
}

function lieu($resultat) {
    $split = split(',',$resultat);
    $id_individu = $split[0];
    $type_evenement = $split[1];
  if($id_individu){
$result=sql_select("*", "spip_genespip_evenements,spip_genespip_type_evenements,spip_genespip_lieux", "id_individu='$id_individu' and spip_genespip_type_evenements.id_type_evenement=spip_genespip_evenements.id_type_evenement and spip_genespip_type_evenements.type_evenement='$type_evenement' and spip_genespip_lieux.id_lieu=spip_genespip_evenements.id_lieu");
if (spip_num_rows($result)==NULL){$resultat="";}
while($row = spip_fetch_array($result)){
$resultat =  $row['ville'].", ".$row['departement'].", ".$row['code_departement'].", ".$row['region'].", ".$row['pays'];
}
  }
  return $resultat;
}

function ville($resultat) {
    $split = split(',',$resultat);
    $resultat = trim($split[0]);
  return $resultat;
}
function departement($resultat) {
    $split = split(',',$resultat);
    $resultat = trim($split[1]);
  return $resultat;
}
function code($resultat) {
    $split = split(',',$resultat);
    $resultat = trim($split[2]);
  return $resultat;
}
function region($resultat) {
    $split = split(',',$resultat);
    $resultat = trim($split[3]);
  return $resultat;
}
function pays($resultat) {
    $split = split(',',$resultat);
    $resultat = trim($split[4]);
  return $resultat;
}
function drapeau($resultat){
    $pays = $resultat;
    $drapeau="<img src='"._DIR_PLUGIN_GENESPIP."img_pack/pays/".$pays.".png' alt=".$pays.">";
    $resultat = str_replace($pays, $drapeau, $resultat);
   return $resultat;
}
function acces($resultat) {
        if($resultat=='0minirezo'){
        $resultat=0;
        }elseif($resultat=='1comite'){
        $resultat=1;
        }elseif($resultat=='6forum'){
        $resultat=2;
        }else{
        $resultat=3;
        }
  return $resultat;
}

//----------Balise arbre id_individu----------
function requete_arbre($id_individu){
	if(isset($_GET['rang'])){
		$val=$_GET['rang'];
	}else{
		$val=4;
	}
	if($val>=9){
		$val=9;
		$plus= "";
	}else{
		$plus= "&raquo; (+1)";
	}
	if (isset($_GET['decujus'])){$decujus=$_GET['decujus'];}else{$decujus=$id_individu;}
	$tab = array(pow(2,$val+1)=>1);
	$tab[1]=$id_individu;
	$val2=$val;
	$lar=100;
	$cell=1;
	$cellp=1;
	$res .= "<div style='font-size:10px'>$col<a href='spip.php?page=arbreasc&id_individu=".$decujus."'><b>[id De Cujus:$decujus]</b></a> ";
	$res .= "<b>[rang=".($val+1)." <a href='spip.php?page=arbreasc&id_individu=".$decujus."&rang=".($val+1)."'><small>$plus</small></a>]</b></div>";
	$res .= "<table border='0' width='100%'>";
		for ($ligne = $ligne; $ligne <= $val; $ligne++) {
		$res .= "<tr>";
		$res .= "<td style='background-color:#BFFF51;margin:0px;padding:0px;border:1px solid black'>".($ligne+1)."</td>";
			for ($col = pow(2,$ligne) ;$col < pow(2,$ligne+1); $col++) {
			//1
			//2 3
			//4 5 6 7
			//8 9 10 11 12 13 14 15
			if ($tab[$col]<>0){$cellp=$cellp+1;}
			$cols=pow(2,$val2+1);
			$res .= "<td colspan='$cols' width='$lar%' style='text-align:center;min-height:60px;vartical-align:top'>";

			  $result = mysql_query(sql_select("*", "spip_genespip_individu", "id_individu='$tab[$col]'") or die ('Requête1 '._T('genespip:invalide')));
			while($row = spip_fetch_array($result)){
				if ($row['sexe']==1){$color_fond='#FFBADD';}else{$color_fond='#CECEFF';}
				$res .= "<div style='text-align:center;border:1px solid black;background-color:".$color_fond.";font-size:9px;min-height:60px;min-width:75px'>";
				$naissance="&deg;".quand($row['id_individu'].",BIRT");
				$deces="&dagger;".quand($row['id_individu'].",DEAT");
				$mariageparent ="&times;".quand($row['pere'].",MARR,".$row['mere']);
				$res .= "<a href='spip.php?page=arbreasc&decujus=".$decujus."&id_individu=".$row['id_individu']."'><b>[$col]</b></a><br />";
				$res .= "<a href='spip.php?page=individu&id_individu=".$row['id_individu']."'>".$row['nom']." ".$row['prenom']."</a>";
				$res .= "<br /><small>".$naissance."<br />".$deces."</small>";
				$res .= "</div>\n";
				$res .= "<div style='text-align:center;border:1px solid black;font-size:8px;background-color:#D2D2D2;position:relative;top:7'>$mariageparent</div>";

				$tab[$col+$col]=$row['pere'];
				$tab[$col+$col+1]=$row['mere'];
				  }
			$cell=$cell+1;
			$res .= "</td>\n";
			}
		$res .= "</tr>";
		$val2=$val2-1;
		$lar=$lar/2;
		}
	$calc=($cellp*100)/$cell;
	$reponse = _T('genespip:cellules_creees').":".$cell."," ._T('genespip:cellules_occupees').":".$cellp.",". _T('genespip:tableau_ocupe_a'). $calc."%";

	$res .= "</table>";
	$res .= "<br /><div style='font-size:10px'><b>[$reponse]</b></div>";


  return $res;
}

function balise_ARBRE($p){
  $p->code = "requete_arbre(".champ_sql('id_individu', $p).")";
  return $p;
}

//----------Balise Naissance id_individu----------
function requete_naissance($id_individu){
$resultat=$id_individu.",BIRT";
  return $resultat;
}

function balise_NAISSANCE($p){
  $p->code = "requete_naissance(".champ_sql('id_individu', $p).")";
  return $p;
}

//----------Balise Deces id_individu----------
function requete_deces($id_individu){
$resultat=$id_individu.",DEAT";
  return $resultat;
}

function balise_DECES($p){
  $p->code = "requete_deces(".champ_sql('id_individu', $p).")";
  return $p;
}

//----------Balise mariage id_individu----------
function requete_mariage($id_individu,$id_epoux){
$resultat=$id_individu.",MARR,".$id_epoux;
  return $resultat;
}

function balise_MARIAGE($p){
  $p->code = "requete_mariage(".champ_sql('id_individu', $p).",".champ_sql('id_epoux', $p).")";
  return $p;
}

//----------Balise photo----------
function requete_photo($id_individu,$format_portrait,$portrait){
if ($portrait==1){
$resultat = "<img src='"._DIR_PLUGIN_GENESPIP."IMG/portrait".$id_individu.".".$format_portrait."' alt='"._T('genespip:portrait')."'>";
}
  return $resultat;
}

function balise_PHOTO($p){
  $p->code = "requete_photo(".champ_sql('id_individu', $p).",".champ_sql('format_portrait', $p).",".champ_sql('portrait', $p).")";
  return $p;
}

//----------Balise signature----------
function requete_signature($id_individu,$format_signature,$signature){
if ($signature==1){
$resultat = "<img src='"._DIR_PLUGIN_GENESPIP."IMG/signature".$id_individu.".".$format_signature."' alt='"._T('genespip:signature')."'>";
}
  return $resultat;
}
function balise_SIGNATURE($p){
	$p->code = "requete_signature(".champ_sql('id_individu', $p).",".champ_sql('format_signature', $p).",".champ_sql('signature', $p).")";
	return $p;
}

	function balise_ACCESS_dist($p) {
		$p->descr['session'] = true;
		
		if(function_exists('balise_ENV')){
			return balise_ENV($p, '$GLOBALS["auteur_session"]');
		}else{
			return balise_ENV_dist($p, '$GLOBALS["auteur_session"]');
		}
	}
?>
