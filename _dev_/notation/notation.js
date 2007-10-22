/**
* Plugin Notation v.0.3
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Affichage des etoiles
* /!\ les variables notation_img et notation_multi doivent etre definies
*		notation_img : les images a afficher (sans -on et -off)
*		notation_multi : on a plusieurs representations (-on1, -on2, ...)
*  
**/

// on est en train de voter ?
var selected=false;

/** Changer la note dans le formulaire
*/
function notation_set_etoile(n, nb, id)
{ if (selected) return;
  // Afficher
  if (notation_multi)
  { for(i=1; i<=n; i++) document.images['star-'+id+'-'+i].src = notation_img+"-on"+i+".gif";
	  for(i=n+1; i<=nb; i++) document.images['star-'+id+'-'+i].src = notation_img+"-off"+i+".gif";
  }
  else
  { for(i=1; i<=n; i++) document.images['star-'+id+'-'+i].src = notation_img+"-on.gif";
	  for(i=n+1; i<=nb; i++) document.images['star-'+id+'-'+i].src = notation_img+"-off.gif";
  }
}

/** Permettre le vote
*/
function notation_change_etoile(n, nb, id)
{	selected = false;
	notation_set_etoile(n, nb, id);
	selected = true;
	// Changer la valeur dans le formulaire
	document.getElementById("id_donnees"+id).value = n;
	// Permettre le vote (afficher le bouton submit)
	if (document.getElementById) { //IE5 ou Netscape 6
		document.getElementById("id_notation-"+id).style.display="";
	}
}

