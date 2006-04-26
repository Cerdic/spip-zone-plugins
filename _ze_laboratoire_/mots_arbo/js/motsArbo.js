function departDrag(elemToDrag, event) {
				 
	var x = parseInt(elemToDrag.style.left);
	var y = parseInt(elemToDrag.style.top);
	var ecartX = event.clientX - x;
	var ecartY = event.clientY - y;
	
	document.addEventListener("mousemove", dragElem, true);
	document.addEventListener("mouseup", dropElem, true);
	
	event.stopPropagation();
	event.preventDefault();

	function dragElem(event) {
		elemToDrag.style.left = (event.clientX - ecartX) + "px";
		elemToDrag.style.top = (event.clientY - ecartY) + "px";
		event.stopPropagation();
	}
	
	function dropElem(event) {
		document.removeEventListener("mousemove", dragElem, true);
		document.removeEventListener("mouseup", dropElem, true);
		event.stopPropagation();
	}
}

					
window.onload= function () { 
	Tlis = document.getElementsByTagName("li");	
	for (i in Tlis) {
		if (li_ec = Tlis[i].innerHTML) {
//			 Tlis[i].addEventListener("mousedown", departDrag(this.parentNode), false);
			 Tlis[i].style.position = "relative";
			 Tlis[i].style.top = "0px";
			 Tlis[i].style.left = "0px";
			 
			 Tlis[i].setAttribute("onMouseDown", "departDrag(this, event)");
//			 Tlis[i].innerHTML = '<span class="conteneurLI" id="conteneur_' + Tlis[i].id + '">' + Tlis[i].innerHTML + '</span>';
//			 document.getElementById("conteneur_" + Tlis[i].id).addEventListener("mousedown", departDrag(this), false);

		}
	}
}
/**/					
