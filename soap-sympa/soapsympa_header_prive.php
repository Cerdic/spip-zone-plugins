<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function soapsympa_header_prive($flux) {

$exec = _request('exec');
	
if(($exec == 'auteur_infos')||($exec == 'soapsympa_review')) {

	if($exec == 'auteur_infos') {
	$Id = _request('id_auteur');
	$email = sql_getfetsel("email","spip_auteurs","id_auteur=$Id");
	$Url = generer_url_ecrire("auteur_infos", "id_auteur=$Id",true,false);
	
	}

	if($exec == 'soapsympa_review') {
	$listname = explode("@",_request('list'));
	$List = $listname[0];
	
	$Url = generer_url_ecrire("soapsympa_review", "list=$List",true,false);
	}



$script = '
<style type="text/css">

table td:hover {background-color:#ffffff; text-decoration:none;} /* background-color pour IE6*/
td.tooltip  span {display:none; padding:2px 3px; margin-left:10px; width:150px;}
td.tooltip:hover span{display:inline; position:absolute; border:1px solid #cccccc; background:#ffffff; color:#dd;}
.opacity {opacity: 0.5}

</style>
';


if($exec == 'auteur_infos') {

	$script .= '
	<script type="text/javascript">
	<!--
	$(document).ready(function(){

	var Bloc = $("#soapsympa");

	var Usermail = "'.$email.'";
	

	Bloc.delegate(".subscribe", "click", function() {
	      
	      Bloc.addClass("opacity");
	      var List = $(this).attr("rel");
	     
	      $.ajax({
		type: "GET",
		url: "'.$Url.'",
		data: "subscribe=1&email="+Usermail+"&list="+ List + "",
		cache: false,
		success: function(data){
		      var Contenu = Bloc.load("'.$Url.' .reload_soapsympa");
		      Bloc.load(function () {
			Bloc.empty().html(Contenu);
		      }).removeClass("opacity");
		       var jqObj = jQuery(data);
		      var Message = jqObj.find(".message").html();
		      alert(Message);
		      
		}
	      });
	      return false;

	});


	Bloc.delegate(".signoff", "click", function() {
		
		Bloc.addClass("opacity");
		var List = $(this).attr("rel");

		$.ajax({
		  type: "GET",
		  url: "'.$Url.'",
		  data: "signoff=1&email="+Usermail+"&list="+ List + "",
		  cache: false,
		  success: function(data){
		  var Contenu = Bloc.load("'.$Url.' .reload_soapsympa");
		    Bloc.load(function () {
			  Bloc.empty().html(Contenu);
		    }).removeClass("opacity");
		    var jqObj = jQuery(data);
		      var Message = jqObj.find(".message").html();
		      alert(Message);
		  }
		});
		return false;

		});
	});
	// -->
	</script>';
}

if($exec == 'soapsympa_review') {
$Submit = _T('ajouter_abonne');
	  $script .= '
	  <script type="text/javascript">
	  <!--
	  $(document).ready(function(){

$(".abonnement .submit").val("'.$Submit.'");

	  var Bloc = $("#soapsympa");
	  var List = "'.$List.'";

	  Bloc.delegate(".signoff", "click", function() {
		  
		  Bloc.addClass("opacity");
		  var Usermail = $(this).attr("rel");

		  $.ajax({
		    type: "GET",
		    url: "'.$Url.'",
		    data: "signoff=1&email="+Usermail+"&list="+ List + "",
		    cache: false,
		    success: function(data){
		    var Contenu = Bloc.load("'.$Url.' .reload_soapsympa");
		      Bloc.load(function () {
			    Bloc.empty().html(Contenu);
		      }).removeClass("opacity");
		      var jqObj = jQuery(data);
		      var Message = jqObj.find(".message").html();
		      alert(Message);
		    }
		  });
		  return false;

		  });

	

	  });
	  // -->
	  </script>';
}



$flux .= $script;
}
return $flux;
}

?>