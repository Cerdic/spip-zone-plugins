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

function buttonfix() {
    var buttons = document.getElementsByTagName('button');
    for (var i=0; i<buttons.length; i++) {
        if(buttons[i].onclick) continue;
        
        buttons[i].onclick = function () {
            for(j=0; j<this.form.elements.length; j++)
                if( this.form.elements[j].tagName == 'BUTTON' )
                    this.form.elements[j].disabled = true;
            this.disabled=false;
            this.value = this.attributes.getNamedItem("value").nodeValue ;
        }
    }
}