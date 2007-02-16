<?php
function cron_radiobot_cron($t){

include_spip('inc/scan');

spip_log("wesh wesh");

$query="SELECT DISTINCT a.id_syndic, a.titre, a.url
FROM spip_syndic_articles a
LEFT JOIN spip_documents_syndic d ON a.id_syndic_article = d.id_syndic_article
WHERE d.id_syndic_article IS NULL
ORDER BY a.maj DESC
LIMIT 0, 1";

$result=spip_query($query);
while($row=spip_fetch_array($result)){
//echo $row['titre']."<br>";
pecho_sons($row['url'],$row['id_syndic']);
}

return 1;
}
?>