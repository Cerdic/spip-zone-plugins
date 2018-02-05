<?php
function filtre_helloWorldAgain($input)
{
   return "I'm there ! : ".$input;
}

function filtre_syntaxeMarge($input)
{
if(empty($input)) return $input;

preg_match_all('/([hbgd]+)(-?[0-9]+)/', $input, $matches);


$letters = $matches[1];
$numbers = $matches[2];

$result = '';//'('.print_r($letters,true).'-'.print_r($numbers,true).')';
	for($i=0;$i<count($letters);$i++)
	{
		preg_match_all('/([hbgd])/', $letters[$i], $eachLetter);

		$eachLetter = $eachLetter[1];
		//$result.' ['.print_r($eachLetter,true).'-'.count($eachLetter).']';
		for($j=0;$j<count($eachLetter);$j++) 
		{
		 switch($eachLetter[$j])
		  {
		  case 'h' :
		  		$result = $result.'margin-top:'.$numbers[$i].'px;';
		  break;
		  case 'b' :
		  		$result = $result.'margin-bottom:'.$numbers[$i].'px;';
		  break;
		  case 'g' :
		  		$result = $result.'margin-left:'.$numbers[$i].'px;';
		  break;
		  case 'd' :
		  		$result = $result.'margin-right:'.$numbers[$i].'px;';
		  break;
		  }
		}

	}
return $result;

}
?>
