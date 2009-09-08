
<?
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/
echo "test2";
$chemin=_DIR_PLUGIN_GENESPIP.'IMG/';
$split = split('/',$_FILES['image']['type']);
echo $chemin;
if (is_uploaded_file($_FILES['image']['tmp_name'])) {
   echo "Fichier ". $_FILES['image']['name'] ." t&eacute;l&eacute;charg&eacute; avec succ&egrave;s.\n";
   move_uploaded_file ( $_FILES['image']['tmp_name'],$chemin."portrait".$_POST['id_individu'].".".$split[1]);
genespip_modif_fiche_portrait(1,$_POST['id_individu'],$split[1]);
}


?>

