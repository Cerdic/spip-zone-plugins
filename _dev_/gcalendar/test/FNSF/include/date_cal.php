<?
//------ cconversion de date en fran�ais

function Get_JourFr($j)
{
    $jourf = array
	("Monday" => "Lundi",
	 "Tuesday" => "Mardi",
	  "Wednesday" => "Mercredi",
	  "Thursday" => "Jeudi",
	  "Friday" => "Vendredi",
	  "Saturday" => "Samedi",
	  "Sunday" => "Dimanche");
    return ($jourf[$j]);
} 
function Get_MoisFr($m)
{
    $moisf = array
	("January" => "janvier",
	 "February" => "f�vrier",
	 "March" => "mars",
	 "April" => "avril",
	 "May" => "mai",
	 "June" => "juin",
	 "July" => "juillet",
	 "August" => "ao�t",
	 "September" => "septembre",
	 "October" => "Octobre",
	 "November" => "novembre",
	 "December" => "d�cembre");
    return ($moisf[$m]);
} 
//---affichage sur la page web 
print(" " . Get_JourFr(date("l")) . " " . date("d") . " " . Get_MoisFr(date("F")) . " " . date("Y") . "");
?>