<?php



// teste si l'auteur est un redac chef
// fonction utilisee par les pipelines
function redacchef_ok($id_auteur)
{
  $prefix = $GLOBALS['table_prefix'];

  $rcs = array();
  $req = "SELECT * FROM ".$prefix."_redac_chef;";
  $res = spip_query($req);

  while ($idaut = spip_fetch_array($res))
    $rcs[] = $idaut['id_auteur'];

  if (in_array($id_auteur, $rcs))
    return true;

  return false;
}




// pipelines "ad-hoc" définis pour ce plugin

// pipeline installee dans "exec/naviguer.php" et "inc/presentation.php"
// pour tester si les articles a publier doivent etre affiches
function redacchef_affpubli_fct($vars)
{
  // le test initial est :
  //  if ($connect_statut == "0minirezo" AND $options == 'avancees') { 
  // on y ajoute le test "est-ce un redac-chef ??

  $options = $vars["options"];
  $connect_statut = $vars["connect_statut"];
  $id_auteur = $vars["id_auteur"];

  if ($connect_statut == "0minirezo" AND $options == 'avancees')
    return true;

  if ($connect_statut == "1comite" AND $options == 'avancees')
    {
      if (redacchef_ok($id_auteur))
	return true;
    }

  return false;
}

// pipeline installee dans "exec/articles_tous"
// pour tester si les articles a publier doivent etre affiches
function redacchef_affpubli2_fct($vars)
{
  $connect_statut = $vars["connect_statut"];
  $id_auteur = $vars["id_auteur"];

  if ($connect_statut == "0minirezo")
    return true;

  if ($connect_statut == "1comite")
    {
      if (redacchef_ok($id_auteur))
	return true;
    }

  return false;
}

// pipeline installee dans "inc/auth.php"
// et danns inc/presentation.php
function redacchef_ok_fct($vars)
{
  $id_auteur = $vars["id_auteur"];

  if (redacchef_ok($id_auteur))
    return true;

  return false;
}

?>
