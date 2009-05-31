function calculeOffsetLeft(r){
  return calculeOffset(r,"offsetLeft")
}

function calculeOffsetTop(r){
  return calculeOffset(r,"offsetTop")
}

function calculeOffset(element,attr){
  var offset=0;
  while(element){
    offset+=element[attr];
    element=element.offsetParent
  }
  return offset
}

function afficheBulle(idBulle, parent)
{
  var bulle = document.getElementById(idBulle);
  var offset;
  var exp = new RegExp("^td_","gi");
  var bl = 0;

  if (chrono!=null) {
     clearTimeout(chrono);
     cacheBulleT();
  }
  bulle.style.display = "block";
  bl = calculeOffsetLeft(bulle);
  bt = calculeOffsetTop(bulle);

  if (exp.test(idBulle)==false) {
    if (bl + bulle.offsetWidth < x)
       offsetx = 0;
    else
       offsetx = x - bl - bulle.offsetWidth;
/*
    if (bt + bulle.offsetHeight < y)
       offsety = 0;
    else
       offsety = y - bt - bulle.offsetHeight;
*/
    bulle.style.left = bulle.offsetLeft + offsetx + shiftx + "px";
//    bulle.style.top = bulle.offsetTop + offsety + "px";
  }
}

function cacheBulleT()
{
  document.getElementById(idBulleT).style.display = "none";
  chrono = null;
}

function cacheBulle(idBulle)
{
  idBulleT = idBulle;
  chrono = setTimeout("cacheBulleT()",delai);
}

function mouseOverBulle()
{
  clearTimeout(chrono);
  chrono = null;
}

function mouseOutBulle()
{
   chrono = setTimeout("cacheBulleT()",delai);
}
/*
function init_agenda() {
  jQuery("#agenda_prev").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/agenda/inc-agenda"), "agenda", init_agenda);
        return false;
      }
      obj.href = unwrapUrl(obj.href, "composants/agenda/inc-agenda", pageUrl());
    }
  );
  jQuery("#agenda_next").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/agenda/inc-agenda"), "agenda", init_agenda);
        return false;
      }
      obj.href = unwrapUrl(obj.href, "composants/agenda/inc-agenda", pageUrl());
    }
  );
}

jQuery(document).ready(
  function() {
    init_agenda()
  }
);*/