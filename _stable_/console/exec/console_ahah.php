<?php

function exec_console_ahah(){
	global $connect_statut;
	global $connect_id_auteur;
	global $connect_toutes_rubriques;
	global $spip_lang_right;

	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {
?>
<html>
<head>
	<script src='<?php echo find_in_path('img_pack/layer.js'); ?>'></script>
	<script>
	var type='mysql';
	var ticker= null;

	function tail() {
		// createXmlHttp et xmlhttp viennent de img_pack/layer.js
		if (!(xmlhttp['log'] = createXmlHttp()))
			return false;

		xmlhttp['log'].open("GET", '?exec=spiplog&logfile='+type+'&format=text', true);

		// traiter la reponse du serveur
		xmlhttp['log'].onreadystatechange = function() {
			if (xmlhttp['log'].readyState == 4) { 
				// si elle est non vide, l'afficher
				if (xmlhttp['log'].responseText != '') {
					logdiv= document.getElementById('log');
					logdiv.innerHTML= xmlhttp['log'].responseText;
				}
			}
		}
	    xmlhttp['log'].send(null);
	}

	function switchAuto() {
		auto= document.getElementById('auto');
		if(ticker==null) {
			ticker= self.setInterval('tail()', 1000);
			auto.value= 'arreter';
		} else {
			self.clearInterval(ticker);
			ticker= null;
			auto.value= 'automatique';
		}
	}
	</script>
</head>
<body>
	<pre id='log'>allo ?</pre>
	<a anchor='bottom'></a>
	<input type='button' value='rafraichir'  onClick='tail()'>
	<input type='button' id='auto' value='automatique' onClick='switchAuto()'>
</body>
</html>
<?php
	}
}

?>
