<?php
//require_once 'lib/iCalcreator.class.php';/*appeler la librairie qui se trouve dans le plugin icalendar*/
function mon_filtre($url){
echo "<h1>Test pour la récupération </h1>";

$config = array("unique_id" => "latp",
    "url" => $url);

$v = new vcalendar($config);

$v->parse();

while ($comp = $v->getComponent())
{
echo "<div>";

	/*date de début*/
   $summary_array = $comp->getProperty("summary", 1, TRUE);
    echo "summary: ", $summary_array["value"], "\n";

   $dtstart_array = $comp->getProperty("dtstart", 1, TRUE);
    $dtstart = $dtstart_array["value"];
    $startDate = "{$dtstart["year"]}-{$dtstart["month"]}-{$dtstart["day"]}";
    echo "start: ", $startDate;
    if (!in_array("DATE", $dtstart_array["params"])) {
        $startTime = "{$dtstart["hour"]}:{$dtstart["min"]}:{$dtstart["sec"]}";
        echo "T", $startTime;
    }
    echo "\n";

    /*date de fin*/
   $dtend_array = $comp->getProperty("dtend", 1, TRUE);
    $dtend = $dtend_array["value"];
    $endDate = "{$dtend["year"]}-{$dtend["month"]}-{$dtend["day"]}";
    echo "end: ", $endDate;
    if (!in_array("DATE", $dtend_array["params"])) {
        $endTime = "{$dtend["hour"]}:{$dtend["min"]}:{$dtend["sec"]}";
        echo "T", $endTime;
    }
    echo "\n";

    /*categorie*/
    $categories = $comp->getProperty( "categories" );
    echo "<strong>categories : ", $categories."</strong><br/>";

    /*attendee*/
    $attendee = $comp->getProperty( "attendee" );
        echo "<strong>attendee : ", $attendee."</strong><br/>";




echo "</div>";
}

return $url;
}


?>