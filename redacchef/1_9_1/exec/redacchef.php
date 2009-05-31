<?php

ob_start();

include_spip("inc/presentation");
include("redacchef_inc.php");

function exec_redacchef() {

  global $connect_statut, $connect_toutes_rubriques;
  global $operation, $couleur_foncee;

  $tababs = redacchef_tablesabsentes();

  pipeline('exec_init',array('args'=>array('exec'=>'auteurs_edit'),'data'=>''));

  debut_page(_T('redacchef:redacchef'), "auteurs", "redacchef");

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
    echo _T('avis_non_acces_page');
    exit;    
  }
  
  // HTML output
  debut_gauche();	
  debut_boite_info();
  echo propre(_T('redacchef:readme'));  	
  fin_boite_info();
  
  debut_droite();
  echo "<br>";
  
  if (!$tababs)
    {
      switch ($operation)
	{
	case "swap":
	  redacchef_swappe();
	default:
	  redacchef_afficher_auteurs();
	  break;
	}
    }
  else
    {
      redacchef_gere_tablesabsentes();
    }
  
  fin_page();
}



function redacchef_swappe()
{
  // fonction appelee pour swapper l'etat
  
  global $id_auteur;
  $prefix = $GLOBALS['table_prefix'];

  // recherche de tous les auteurs passes en parametres
  $eff = array();
  $aj = array();
  foreach($_POST as $nom=>$var)
    {
      if (strncmp($nom, "redacchefnok", 12) == 0)
	{
	  $idnok = $var;
	  $idok = $_POST["redacchefok".$var];
	  if ($idok == $idnok)
	    $aj[] .= $idok;
	  else 
	    $eff[] .= $idnok;
	}
    }

  foreach($eff as $e)
    {
      $req = "DELETE FROM ".$prefix."_redac_chef WHERE id_auteur=".$e.";";
      $res = spip_query($req);
    }

  foreach($aj as $a)
    {
      $req = "INSERT INTO ".$prefix."_redac_chef(id_auteur) VALUES(".$a.") ";
      $res = spip_query($req);
    }
}


function redacchef_afficher_auteurs()
{
  global  $debut, $tri, $visiteurs;

  if (!$tri) $tri='nom'; else $tri = preg_replace('/["\'?=&<>]/', '', $tri);
  $debut = intval($debut);
  $result = redacchef_requete_auteurs($tri, $visiteurs);
  $nombre_auteurs = spip_num_rows($result);
  $max_par_page = 30;
  $debut = intval($debut);
  if ($debut > $nombre_auteurs - $max_par_page)
    $debut = max(0,$nombre_auteurs - $max_par_page);
  
  $i = 0;
  $auteurs=$lettre=array();
  $lettres_nombre_auteurs =0;
  $lettre_prec ="";
  
  while ($auteur = spip_fetch_array($result)) {
    if ($i>=$debut AND $i<$debut+$max_par_page) {
      if ($auteur['statut'] == '0minirezo')
	$auteur['restreint'] = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs_rubriques WHERE id_auteur=".$auteur['id_auteur']));
      $auteurs[] = $auteur;
    }
    $i++;
    
    if ($tri == 'nom') {
      $premiere_lettre = strtoupper(spip_substr(extraire_multi($auteur['nom']),0,1));
      if ($premiere_lettre != $lettre_prec) {
#			echo " - $auteur[nom] -";
	$lettre[$premiere_lettre] = $lettres_nombre_auteurs;
      }
      $lettres_nombre_auteurs ++;
      $lettre_prec = $premiere_lettre;
    }
  }
  pipeline('exec_init',array('args'=>array('exec'=>'auteurs'),'data'=>''));
  
  redacchef_affiche_auteurs($auteurs, $lettre, $max_par_page, $nombre_auteurs);
}


function redacchef_affiche_auteurs($auteurs, $lettre, $max_par_page, $nombre_auteurs)
{
  global $debut, $options, $spip_lang_right, $tri, $visiteurs, $connect_id_auteur,   $connect_statut,   $connect_toutes_rubriques;


  if ($tri=='nom') $s = _T('info_par_nom');
  if ($tri=='statut') $s = _T('info_par_statut');
  if ($tri=='nombre') $s = _T('info_par_nombre_articles');
  $partri = ' ('._T('info_par_nombre_article').')';

  echo pipeline('affiche_gauche',array('args'=>array('exec'=>'auteurs'),'data'=>''));
  creer_colonne_droite();
  echo pipeline('affiche_droite',array('args'=>array('exec'=>'auteurs'),'data'=>''));
  
  gros_titre(_T('redacchef:inforedac')); 
  echo "<br>";
  
  debut_cadre_relief('auteur-24.gif');
  echo "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 WIDTH='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
  echo "<tr bgcolor='#DBE1C5'>";
  echo "<td width='20'>";
  if ($tri=='statut')
    echo http_img_pack('admin-12.gif','', "border='0'");
  else
    echo http_href_img(generer_url_ecrire('redacchef','tri=statut'),'admin-12.gif', "border='0'", _T('lien_trier_statut'));
  
  echo "</td><td>";
  if ($tri == '' OR $tri=='nom')
    echo '<b>'._T('info_nom').'</b>';
  else
    echo "<a href='" . generer_url_ecrire("redacchef","tri=nom") . "' title='"._T('lien_trier_nom')."'>"._T('info_nom')."</a>";
  
  if ($options == 'avancees') echo "</td><td colspan='2'>"._T('redacchef:infocheck');
  echo "</td><td>";
  if (!$visiteurs) {
    if ($tri=='nombre')
      echo '<b>'._T('info_articles').'</b>';
    else
      echo "<a href='" . generer_url_ecrire("redacchef","tri=nombre") . "' title=\""._T('lien_trier_nombre_articles')."\">"._T('info_articles_2')."</a>"; //'
  }
  echo "</td></tr>\n";
  
  if ($nombre_auteurs > $max_par_page) {
    echo "<tr bgcolor='white'><td class='arial1' colspan='".($options == 'avancees' ? 5 : 3)."'>";
    //echo "<font face='Verdana,Arial,Sans,sans-serif' size='2'>";
    for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
      if ($j > 0) echo " | ";
      
      if ($j == $debut)
	echo "<b>$j</b>";
      else if ($j > 0)
	echo "<a href='", generer_url_ecrire('redacchef',"tri=$tri$visiteurs&debut=$j"), "'>$j</a>";
      else
	echo " <a href='",  generer_url_ecrire('redacchef',"tri=$tri$visiteurs"), "'>0</a>";
      
      if ($debut > $j  AND $debut < $j+$max_par_page){
	echo " | <b>$debut</b>";
      }
      
    }
    //echo "</font>";
    echo "</td></tr>\n";
    
    if ($tri == 'nom' AND $options == 'avancees') {
      // affichage des lettres
      echo "<tr bgcolor='white'><td class='arial11' colspan='5'>";
      foreach ($lettre as $key => $val) {
	if ($val == $debut)
	  echo "<b>$key</b> ";
	else
	  echo "<a href='", generer_url_ecrire('redacchef',"tri=$tri$visiteurs&debut=$val"),"'>$key</a> ";
      }
      echo "</td></tr>\n";
    }
    echo "<tr height='5'></tr>";
  }
  
  echo "<form action='".generer_url_ecrire("redacchef")."&operation=swap' method='post' name='tradlang'>\n";
  redacchef_afficher_n_auteurs($auteurs);
  echo "</table>\n";
  
  echo "<span style='float:right'><input type='submit' class='fondo' value='"._T("redacchef:modifier")."'></span>";
  echo "</form>";
  
  echo "<a name='bas'>";
  echo "<table width='100%' border='0'>";
  
  $debut_suivant = $debut + $max_par_page;
  if ($visiteurs) $visiteurs = "\n<input type='hidden' name='visiteurs' value='oui' />";
  if ($debut_suivant < $nombre_auteurs OR $debut > 0) {
    echo "<tr height='10'></tr>";
    echo "<tr bgcolor='white'><td align='left'>";
    if ($debut > 0) {
      $debut_prec = max($debut - $max_par_page, 0);
      echo generer_url_post_ecrire("redacchef","tri=$tri&debut=$debut_prec"),
	"\n<input type='submit' value='&lt;&lt;&lt;' class='fondo' />",
	$visiteurs,
	"\n</form>";
    }
    echo "</td><td style='text-align: $spip_lang_right'>";
    if ($debut_suivant < $nombre_auteurs) {
      echo generer_url_post_ecrire("redacchef","tri=$tri&debut=$debut_suivant"),
	"\n<input type='submit' value='&gt;&gt;&gt;' class='fondo' />",
	$visiteurs,
	"\n</form>";
    }
    echo "</td></tr>\n";
  }
  
  echo "</table>\n";
  
  
  
  fin_cadre_relief();
    
}


function redacchef_requete_auteurs($tri, $visiteurs)
{
  global $connect_statut, $spip_lang, $connect_id_auteur;
  
  //
  // Construire la requete
  //
  
  $sql_visible = "aut.statut IN ('1comite')"; 
  $sql_sel = '';
  
  // tri
  switch ($tri) {
  case 'nombre':
    $sql_order = ' compteur DESC, unom';
    break;
    
  case 'statut':
    $sql_order = ' statut, login = "", unom';
    break;
    
  case 'nom':
  default:
    $sql_sel = ", ".creer_objet_multi ("nom", $spip_lang);
    $sql_order = " multi";
  }
  
  
  //
  // La requete de base est tres sympa
  //
  
  $sql = "SELECT aut.id_auteur AS id_auteur, aut.statut AS statut, ".
    "aut.login AS login, aut.nom AS nom, aut.email AS email, aut.source AS source, ".
    "aut.pass AS pass,	aut.url_site AS url_site, aut.messagerie AS messagerie,
	UPPER(aut.nom) AS unom,
	count(lien.id_article) as compteur
	$sql_sel  FROM spip_auteurs as aut
	LEFT JOIN spip_auteurs_articles AS lien ON aut.id_auteur=lien.id_auteur	LEFT JOIN spip_articles AS art ON (lien.id_article = art.id_article)
	WHERE	$sql_visible
	GROUP BY aut.id_auteur ORDER BY  $sql_order";
  $row = spip_query($sql);
  return $row;
}


function redacchef_jaja()
{
  // ecrit le code javascript poru changer les 
  // bouton "on the fly"

  echo "
<script language='Javascript'>

function getHTTPObject()
{
  var xmlhttp = false;

  /* Compilation conditionnelle d'IE */
  /*@cc_on
  @if (@_jscript_version >= 5)
     try
     {
        xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
     }
     catch (e)
     {
        try
        {
           xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
        }
        catch (E)
        {
           xmlhttp = false;
        }
     }
  @else
     xmlhttp = false;
  @end @*/

  if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
  {
     try
     {
        xmlhttp = new XMLHttpRequest();
     }
     catch (e)
     {
        xmlhttp = false;
     }
  }

  if (xmlhttp)
  {
     xmlhttp.onreadystatechange=function()
     {
        if (xmlhttp.readyState == 4)
        {
           if (xmlhttp.status == 200)
           {
              //alert(xmlhttp.responseText);
           }
        }
     }
  }
  return xmlhttp;
}


function callServerMethod(url)
{
  xhttpRequest = getHTTPObject();  //new XMLHttpRequest();
  if (xhttpRequest == null ) return ;
  xhttpRequest.open('GET' , url, true );
  xhttpRequest.send(null);
  return true;
}
</script>
";
  
}

function redacchef_afficher_n_auteurs($auteurs) 
{ 
  global $connect_statut, $options, $messagerie;

  $prefix = $GLOBALS['table_prefix'];
  
  // teste si la table existe
  $rcs = array();
  $req = "SELECT * FROM ".$prefix."_redac_chef;";
  $res = spip_query($req);

  while ($idaut = spip_fetch_array($res))
    $rcs[] = $idaut['id_auteur'];

  //redacchef_jaja();

  foreach ($auteurs as $row) {
    
    echo "<tr style='background-color: #eeeeee;'>";
    
    // statut auteur
    echo "<td style='border-top: 1px solid #cccccc;'>";
    echo bonhomme_statut($row);
    
    // nom
    echo "</td><td class='verdana11' style='border-top: 1px solid #cccccc;'>";
    echo "<a href='", generer_url_ecrire('auteurs_edit',"id_auteur=".$row['id_auteur']), "'>",typo($row['nom']),'</a>';
    
    if (isset($row['restreint']) AND $row['restreint'])
      echo " &nbsp;<small>"._T('statut_admin_restreint')."</small>";    
    echo "</td>";

    // redac-chef ?
    echo "<td class='arial1' style='border-top: 1px solid #cccccc;'>";
    $idaut = $row['id_auteur'];
    $chk = "";
    if (in_array($idaut, $rcs))
      $chk = "checked";

    //echo "<input type='checkbox' ".$chk." value='' onclick='callServerMethod(\"".$url."\"); return true;' name='redacchef[]'>";
    echo "<input type='checkbox' ".$chk." value='".$idaut."' name='redacchefok".$idaut."'>";
    echo "<input type='hidden' ".$chk." value='".$idaut."' name='redacchefnok".$idaut."'>";
    echo "</td><td style='border-top: 1px solid #cccccc;'>&nbsp;";
    
    // nombre d'articles
    echo "</td><td class='arial1' style='border-top: 1px solid #cccccc;'>";
    if ($row['compteur'] > 1)
      echo $row['compteur']."&nbsp;"._T('info_article_2');
    else if($row['compteur'] == 1)
      echo "1&nbsp;"._T('info_article');
    else
      echo "&nbsp;";
    
    echo "</td></tr>\n";
  }

}


// teste si les table liees au module
// sont presentes dans la base
function redacchef_tablesabsentes()
{
  $prefix = $GLOBALS['table_prefix'];
  
  // teste si la table existe
  $req = "SELECT COUNT(*) FROM ".$prefix."_redac_chef;";
  $res = spip_query($req);
  if ($res) 
    return false;
  
  return true;
}


// gestion de la cinematique "tables absentes"
function redacchef_gere_tablesabsentes()
{
  global $connect_statut;
  global $operation;
  
  $prefix = $GLOBALS['table_prefix'];
  
  // test si on est dans la phase de creation
  if ($operation == "creertables")
    {
      $ret = false;
      debut_boite_info();
      
      if (redacchef_creertables())
	{
	  echo propre(_T('redacchef:creationok'));
	  echo "<br><br>";
	  echo "<form action='".generer_url_ecrire("redacchef")."&amp;' method='post' name='redacchef'>\n";
	  echo "<input type='hidden' name='operation' value='utiliser' />\n";
	  echo "<input type='submit' class='fondo' value='"._T("redacchef:creationutiliser")."'>";
	  echo "</form>";
	  $ret = true;
	}
      else
	{
	  echo propre(_T('redacchef:creationnok'));
	  echo "<br>";	  
	  echo propre(mysql_error());
	  echo "<br><br>";	  
	  $req = redacchef_req();
	  echo nl2br(implode("\n",$req));
	}
      
      fin_boite_info();
      return $ret;
    }

  debut_boite_info();
  echo propre(_T('redacchef:tablenoncreee'));  	
  
  if ($connect_statut == "0minirezo") 
    {
      echo '<br><br>';

      echo "<form action='".generer_url_ecrire("redacchef")."' method='post' name='redacchef'>\n";
      echo "<input type='hidden' name='operation' value='creertables' />\n";
      echo "<input type='submit' class='fondo' value='"._T("redacchef:creertables")."'>";
      echo "</form>";
    }
  else
    {
      echo '<br><br>';
      echo propre(_T('redacchef:demandeadmin'));  	
    }
  
  fin_boite_info();
  
  return true;
}


// creation des tables
function redacchef_creertables()
{
  // creation des tables redacchef

  $reqs = redacchef_req();

  foreach($reqs as $req)
    {
      $res = spip_query($req);
      if (!$res) 
	return false;    }

  return true;
}


// requere mysql pour creer les tables
function redacchef_req()
{
  $prefix = $GLOBALS['table_prefix'];  

  return array (
		"CREATE TABLE `".$prefix."_redac_chef` (`id_auteur` BIGINT( 21 ) NOT NULL ,PRIMARY KEY ( `id_auteur` )) ENGINE = MYISAM ;"
		);        
}



?>
