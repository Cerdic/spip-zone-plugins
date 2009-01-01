<?
//------ cconversion de date en français

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
	 "February" => "février",
	 "March" => "mars",
	 "April" => "avril",
	 "May" => "mai",
	 "June" => "juin",
	 "July" => "juillet",
	 "August" => "août",
	 "September" => "septembre",
	 "October" => "Octobre",
	 "November" => "novembre",
	 "December" => "décembre");
    return ($moisf[$m]);
} 
//---affichage sur la page web 
print(" " . Get_JourFr(date("l")) . " " . date("d") . " " . Get_MoisFr(date("F")) . " " . date("Y") . "");
?>