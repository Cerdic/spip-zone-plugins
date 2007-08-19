Array.prototype.contains = function (ele) {
	for (var i = 0; i < this.length; i++) {
		if (this[i] == ele) {
			return true;
		}
	}
	return false;
};

Array.prototype.remove = function (ele) {
	var arr = new Array();
	var count = 0;
	for (var i = 0; i < this.length; i++) {
		if (this[i] != ele) {
			arr[count] = this[i];
			count++;
		}
	}
	return arr;
};

function splittags(txt) {
	var temp = new Array();
	var r, i, debut;
	var compteur=1;

	if (txt.match(/^[ ,"]*$/))
		return new Array();

	while (r = txt.match(/(^| )"([^"]*)"(,| |$)/)) {
		debut = txt.search(r[0]);
		txt = txt.substring(0,debut)
			+ r[1]
			+ 'compteur'+compteur
			+ r[3]
			+ txt.substring(debut+r[0].length, 100000);
		temp['compteur'+compteur] = r[2];
		compteur++;
	}
	txt = txt.split(/[, ]+/);
	for (i=0; i<txt.length; i++) {
		if (txt[i].match('^compteur[0-9]+$')) {
			txt[i] = temp[txt[i]];
		}
	}
	return txt;
}

function jointags(a) {
	var tag, sp;
	for (var i = 0; i < a.length; i++) {
		tag = a[i];
		if (tag.split('"').length == 1
		&&(tag.split(' ').length > 1
			|| tag.split(',').length > 1
		)) {
			tag = '"'+tag+'"';
		}
		a[i] = tag;
	}

	return a.join(' ');  // ici mettre ' ' si on ne veut pas de virgule et ', ' dans le cas contraire
}



function addtag() {
	var thisTag = this.innerHTML;
	var taglist = document.getElementById('tags');
	var tags = splittags(taglist.value);
	
   var pop = new Array();
   var populartags = document.getElementById('popularTags').getElementsByTagName('span');
	   
	//effacer la saisie 
   for (var i = 0; i < populartags.length; i++) {
		pop[i] = populartags[i].innerHTML ;
		}
   
	for (var i = 0; i < tags.length; i++) {
		if (!pop.contains(tags[i])) {
		tags = tags.remove(tags[i]);
		}
	}
   
	// If tag is already listed, remove it
	if (tags.contains(thisTag)) {
		tags = tags.remove(thisTag);
		this.className = 'unselected';
		
		
	// Otherwise add it
	} else {
		tags.push(thisTag);
		// tags.splice(0, 0, thisTag);
		this.className = 'selected';
	}
	
	taglist.value = jointags(tags) + ' ';
	document.getElementById('tags').focus();
}

function loadpopular() {
	var taglist = document.getElementById('tags');

	var tags = splittags(taglist.value);

	var populartags = document.getElementById('popularTags').getElementsByTagName('span');

	for (var i = 0; i < populartags.length; i++) {
		populartags[i]['onmousedown'] = addtag;
		if (tags.contains(populartags[i].innerHTML)) {
			populartags[i].className = 'selected';
		}
	}
	document.onkeydown = document.onkeypress = document.onkeyup = handler ;
}


function handler(event) { var e=(event||window.event) //w3||ie
	var taglist = document.getElementById('tags');
	var tags = splittags(taglist.value);

	
	for (var i = 0; i < pop.length; i++) {
		pop[i].className = '';
		if (tags.contains(pop[i].innerHTML)) {
			pop[i].className = 'selected';
		}
	}
	
	if (e.type == 'keypress' && e.keyCode == 9) {
		e.preventDefault();
		
		susu=document.getElementById('suggestions').getElementsByTagName('span');
		document.write(susu.tosource());
		susu[0] = addtag ;
	}
		
	if (e.type == 'keypress' && e.keyCode == 32) {
		//effacer les suggestions
		var tab_sug = document.getElementById('suggestions') ;
		tab_sug.innerHTML='&nbsp;';
	}
		
	if (e.type == 'keyup') {
		//effacer les suggestions
		var tab_sug = document.getElementById('suggestions') ;
		tab_sug.innerHTML='&nbsp;';
		
		var saisie = tags.pop() ;
		
		var is_text = new RegExp('^[A-Za-z0-9 ÉÈÊÀÁÂÄÇÌÍÎÏÑÓÒÔÖÚÙÛÜ-]+$', 'gi');
		var re = new RegExp('^'+saisie+'[A-Za-z0-9 ÉÈÊÀÁÂÄÇÌÍÎÏÑÓÒÔÖÚÙÛÜ-]+$' , 'gi');
		
		if(saisie.match(is_text)){
			var i = 1 ;
			for (var j = 0; j < pop.length; j++) {
				//trouver les tags
				var tag_c = pop[j].innerHTML;
				if (tag_c.match(re) ) {
					pop[j].className = 'auto';
					
					//afficher des suggestions 
					var suggestion = document.createElement('span');
					suggestion.id = 'span'+i ;
					var titre = document.createTextNode(tag_c);
					document.getElementById('suggestions').appendChild(suggestion);	
					
					document.getElementById('span'+i).appendChild(titre);		
					
					suggestion['onmousedown'] = addtag;
					i=i+1;
				}
			}
		}
	}
}
var pop = document.getElementById('popularTags').getElementsByTagName('span');
loadpopular();
