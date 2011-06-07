<?php
function dida_inclure_java($flux)
{
$flux .= '
<script type="text/javascript"
src="'.'../plugins/didaspip/'.'librairie_controles.js"
name="java-dxxxx"></script>

<script language="JavaScript">
<!--

function check()
	{
		

		var test=true;
		var message="";

		
	
		
		
		
		if (isEmpty(document.import.nom.value))
		{
			message+="\nnom est un champ obligatoire !";
			test=false;
		} else if (isNotAlphanumeric(document.import.nom.value))
		{
			message+="\nnom doit uniquement comporter des chiffres et lettres non accentuées !";
			test=false;
		}
		

		
		if (message!="") alert(message);
		return test;
	}
//-->
</script>
';
return $flux;
}


?>