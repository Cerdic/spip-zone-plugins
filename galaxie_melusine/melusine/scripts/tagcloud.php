<tags>
<a href='spip.php?page=mot&id_mot=3'  rel='tag' style='font-size: 20pt;' color='0xff0099' >TITRE</a>
<a href='spip.php?page=mot&id_mot=3'  rel='tag' style='font-size: 20pt;' color='0xff0099' >TITRE</a>
</tags>
<?php  
@define('_DIR_RESTREINT_ABS', 'ecrire/');
include_once _DIR_RESTREINT_ABS.'inc_version.php';


# au travail...
//include _DIR_RESTREINT_ABS.'public.php';
$texte="<tags>\n";
$r=mysql_query("select * from spip_mots")or die (mysql_error());
while($req=mysql_fetch_array($r)){
$texte.="<a href='spip.php?page=mot&id_mot=".$req['id_mot']."'  rel='tag' style='font-size: 20pt;' color='0xff0099' >".$req['titre']."</a>\n";
};
$texte.="</tags>";
echo $texte;

?>