/*
 *  widgets.js (c) Fil 2006 -- licence GPL
 */

// le prototype configuration de Widgets
function configWidgets(options)
{
    for (opt in options) {
        this[opt] = options[opt];
    }
}

// ouvre un widget
$.fn.openwidget = function() {
  return this
  .each(function(){
    // verifier que je suis un widget
    if (!$(this).is('.widget'))
      return;

    // voir si je dispose deja du widget comme voisin
    if ($(this).is('.has-widget')) {
      $(this)
      .hide()
      .next()
        .show();
    }
    // sinon charger le formulaire
    else {
      $(this)
      .append(SEARCHING); // icone d'attente
      var me=this;
      $.get(url_widgets_html
        + '&w=' + $(this).width()
        + '&h=' + $(this).height()
        + '&em=' + $(this).css('fontSize')
        + '&class=' + encodeURIComponent(me.className)
       ,
        function (c) {
          $(me)
          .find("img.widget-searching")
            .remove()
          .end()
          .hide()
          .addClass('has-widget')
          .next()
            .html(c)
            .show() // animate
            .activatewidget();
        }
      );
    }
  });
}

// annule le widget ouvert
$.fn.cancelwidget = function() {
  return this
  .filter('.has-widget')
  .show()
  .removeClass('has-widget')
  .next()
    .html('')
    .hide();
}

// masque le widget ouvert
$.fn.hidewidget = function() {
  return this
  .filter('.has-widget')
  .show()
  .next()
    .hide()
    .removeClass('widget-hover');
}

// active un widget qui vient d'etre charge
$.fn.activatewidget = function() {
  return this
  .each(function(){
    var me = this;
    var w,h;
    $(me)
    .find('form')
      .ajaxForm(function(d){
        if (!d.responseText) {
	        alert(configWidgets.txtErrInterdit);
	        $(me)
	        .find("img.widget-searching")
	          .remove();
	        $(".bouton-mobile", me).show();
	        return false;
        }
        $(me)
        .prev()
          .html(d.responseText)
          .prepend(ICONCLICK)
          .show()
          .removeClass('has-widget')
        .next()
        .hide()
        .html('');
      }).onesubmit(function(){
        $("form", me)
        .append(SEARCHING); // icone d'attente
      })
      .find(".widget-active")
        .css('font', 'inherit') // pour safari
        .css({
            'fontSize': $(me).css('fontSize'),
            'fontFamily': $(me).css('fontFamily')
        })
        .each(function(n){
          if (n==0)
            this.focus();
        })
        .keypress(function(e){
          if (e.keyCode == 27) {
            $(me)
            .prev()
            .cancelwidget();
          }
        })
      .end()
      .find(".cancel_widget")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .prev()
          .cancelwidget();
        })
      .end()
      .find(".hide_widget")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .prev()
          .hidewidget();
        })
      .end()
    .end();
  });
}

// initialise les widgets (cree le clone actif)
$.fn.initwidget = function(){
  this
  .each(function(){
    $(this)
    .after(
      this.cloneNode(true)
    );
  })
  .addClass('widget-autorise')
  .next()
    .hide()
    .html('')
    .removeAttr('id') // necessaire ??
    .removeClass('widget')
    .click(function(e){
      e.stopPropagation();
    })
  .prev()
  .prepend(ICONCLICK)
  .click(function(e){
    e.stopPropagation();
  })
  .dblclick(function(e){
    e.stopPropagation();
    $(this).openwidget();
  });

  // :hover pour MSIE
  if (jQuery.browser.msie) {
    this.hover(
      function(){
        $(this).addClass('widget-hover');
      },function(){
        $(this).removeClass('widget-hover');
      }
    );
  }

  return this;
}

// demarrage
$(document).ready(function() {
  if (!configWidgets.droits) return;
  var c = configWidgets.droits.split('|');
  url_widgets_html = 'spip.php?action=widgets_html';
  SEARCHING = "<img class='widget-searching' src='" + configWidgets.imgPath + "/searching.gif' style='float:right;' />";
  ICONCLICK = "<span style='float:right;z-index:100;'><img class='widget-edit' onclick='event.stopPropagation();$(this).parent().parent().openwidget();' style='position:absolute;border:0;' src='" + configWidgets.imgPath + "/edit.gif' title='" + configWidgets.txtEditer + "' /></span>";

  for (var i=0; i<c.length; i++) {
    $(".widget."+c[i])
    .initwidget();
  }

  // fermer tous les widgets ouverts
  $("html")
  .click(function() {
    $(".widget.has-widget:hidden")
    .hidewidget();
  });
});

