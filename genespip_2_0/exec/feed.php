<?php
function exec_feed(){
header('Content-Type: text/xml');
echo "<?xml version='1.0' encoding='utf-8'?>";
$url_lien_distant=generer_url_public('patronyme');
$url_site = $_SERVER['HTTP_REFERER'];

echo "<rss version='2.0'>";
echo "<channel>";
echo "<title>"._T('genespip:liste_patronyme')."</title>";
echo "<link>$url_site</link>";
echo "<description>"._T('genespip:site_genealogie_genespip')."</description>";
echo "<language>fr</language>";
echo "<ttl>5</ttl>";

echo "<item>";
echo "<title>"._T('genespip:liste_noms')."</title>";
echo "<link>$url_lien_distant</link>";
echo "<category>"._T('genespip:patronymes')."</category>";
echo "<description>";
$result_individu = sql_select('nom, count(id_individu) as comptenom', 'spip_genespip_individu', 'poubelle<>1', 'nom');
        while ($indi = spip_fetch_array($result_individu)) {
        echo $indi['nom']." (".$indi['comptenom']."), ";
        }
echo "</description>";
echo "<pubDate>";
$result_individu = sql_select('date_update', 'spip_genespip_liste', 'date_update DESC limit 0,1');
        while ($indi = spip_fetch_array($result_individu)) {
        echo $indi['date_update'];
        }
echo "</pubDate>";
echo "</item>";
echo "</channel>";
echo "</rss>";
}
?>
