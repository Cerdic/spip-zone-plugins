<?php
# Formulaire pour l'upload d'une archive scenari 
	if (!defined("_ECRIRE_INC_VERSION")) return;
?>

<div class="formulaire_spip formulaire_configurer formulaire_config_scenari">
	<div class="cadre_padding">
	<h3 class="titrem"><?php print _T('scenari:add')?></h3>
		<form method="POST" name="import" enctype="multipart/form-data"  action="?exec=scenari_upload">
		 <?php print _T('scenari:zip')?> &lt; <?php print ini_get("upload_max_filesize"); ?> :<br/>
		 <input type="file" name="scenari_zip" size="7"><br/>
		 <br/>
		 <?php print _T('scenari:extract')?> :<br/>
		 IMG/scenari/<input type="text" name="scenari_name" id="scenari_name" size='9'><br/>
		 <br/>
		 <center><input type="submit"></center>
		 <br/>
		</form>
	</div>
</div>

<script language="JavaScript" type="text/javascript">
//<!--
$(document).ready(function() {
	var keynum;
	var monreg=/[a-zA-Z0-9]+$/;
	$("#scenari_name").keypress(function (e) {
		if(window.event){  //IE
			keynum = e.keyCode;
		}
		if(e.which){ //Netscape/Firefox/Opera
			keynum = e.which;
		}
		if (keynum!=46 && keynum!=8)
			if (String.fromCharCode(keynum).match(monreg))
				return true;
			else
				return false;
	});
	
	$('.efface_scenari').click(function() {
		var id=$(this).attr('alt');
		if(confirm('Effacer '+id+'?')){
			$.ajax({
				type: "GET",
				url: ".",
				data: 'exec=scenari&dropid='+id
			});
			$(this).parent().parent().hide('slow');
		}
	});
	
});
//-->
</script>
