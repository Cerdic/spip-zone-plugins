

 var errok = 0;
 var merr = "";
  var query=this.location.search.substring(1);

  if (query.length > 0){

    var params=query.split("&");

    for (var i=0 ; i<params.length ; i++){

      var pos = params[i].indexOf("=");
      var name = params[i].substring(0, pos);
      var value = params[i].substring(pos + 1);

      if (name=="messerreur")
      {
	merr = unescape(value);
      }

      if (name == "erreur")
      {
        if (value=="1") 
           errok=1;
      }
   }
  }


if (errok==1)
 {
  alert(merr);
 }