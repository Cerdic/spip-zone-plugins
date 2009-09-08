<?php
function exec_feed(){
header('Content-Type: text/xml');
echo "<?xml version='1.0' encoding='utf-8'?>";
$url_lien_distant=generer_url_public('patronyme');
$url_site = $_SERVER['HTTP_REFERER'];

echo "<rss version='2.0'>";
echo "<channel>";
echo "<title>Liste de patronymes</title>";
echo "<link>$url_site</link>";
echo "<description>Site de genealogie par GENESPIP</description>";
echo "<language>fr</language>";
echo "<ttl>5</ttl>";

echo "<item>";
echo "<title>Liste de NOMS</title>";
echo "<link>$url_lien_distant</link>";
echo "<category>PATRONYME</category>";
echo "<description>";
$result_individu = spip_query("SELECT nom, count(id_individu) as comptenom FROM spip_genespip_individu where poubelle<>1 group by nom");
        while ($indi = spip_fetch_array($result_individu)) {
        echo $indi['nom']." (".$indi['comptenom']."), ";
        }
echo "</description>";
echo "<pubDate>";
$result_individu = spip_query("SELECT date_update FROM spip_genespip_liste ORDER BY date_update DESC limit 0,1");
        while ($indi = spip_fetch_array($result_individu)) {
        echo $indi['date_update'];
        }
echo "</pubDate>";
echo "</item>";
echo "</channel>";
echo "</rss>";
}
?>