/**
 * Transforme une url spip pour "wrapper" un composant (requis pour Ajax)
 */
function wrapUrl(url, c) {
  url = url.replace(/\#[\w]*/, "");
  creg = '&c=' + c;
  url = url.replace(creg, "");
  url = url.replace(/page=[\w]*/, "page=wrap&c=" + c);
  if (url.indexOf("page=") < 0)
    url = url.replace(/\?/, "?page=wrap&c=" + c + "&");
  return url;
}

function unwrapUrl(url, c, page) {
  creg = 'page=wrap&c=' + c;
  url = url.replace(creg, page);
  url = url.replace(/\?\&/, "?");
  return url;
}

function pageUrl() {
  page = document.URL.match(/page=[\w]*/);
  if (page)
    page = page[0];
  else
    page = "";
  return page;
}
/* ACS-Cat override for swap_couche() dist */
function swap_couche(couche, rtl, dir, no_swap) {
  var layer;
  var triangle = document.getElementById('triangle' + couche);
  if (!(layer = findObj('Layer' + couche))) return;
  if (layer.style.display == "none"){
    if (!no_swap && triangle) triangle.src = dir + acs_deplier_bas;
    layer.style.display = 'block';
  } else {
    if (!no_swap && triangle) {
      if (rtl)
        triangle.src = dir + acs_deplier_haut_rtl;
      else
        triangle.src = dir + acs_deplier_haut;
    }
    layer.style.display = 'none';
  }
}