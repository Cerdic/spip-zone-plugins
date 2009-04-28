function toggle(id, ImageRep){

  ul = "ul_" + id;
  img = "img_" + id;
  ulElement = document.getElementById(ul);
  imgElement = document.getElementById(img);

  if (ulElement){
          if (ulElement.className == 'closed'){
 
                  ulElement.className = "open";
                  imgElement.src = ImageRep + "/opened.png";
                  }else{
                  ulElement.className = "closed";
                  imgElement.src = ImageRep + "/closed.png";
                 
                  }
          }
         
         
  }

function hierarchie (listRubriques, ImageRep) {

var itemList = listRubriques;
var restoredArray = itemList.split(",");
var numberItem = restoredArray.length;

for (i=0; i<numberItem; i++) {
    currentRub = restoredArray[i]
                 ul = "ul_" + currentRub;
    img = "img_" + currentRub;
    ulElement = document.getElementById(ul);
    imgElement = document.getElementById(img);

    if (ulElement) {
        ulElement.className = "open";
        imgElement.src = ImageRep + "/closed.png";

      }


  }

}
