function init_events(){
	/*$('tr.row').click(function(){	$(this).selectRow();	});*/
	/*$('td').click(function(){	$(this).selectRow();	});*/
	$('th').click(function(){	$(this).selectCol();	});
	$('img.noeud').click(function(){	$(this).toggleLine();	});
	update_toolbar_icones();
}
function update_toolbar_icones(){
	if (row_selected!=undefined)
		$('#toolbar a.MoveLeft,#toolbar a.MoveRight,#toolbar a.RemoveItem').removeClass('inactif');
	else
		$('#toolbar a.MoveLeft,#toolbar a.MoveRight,#toolbar a.RemoveItem').addClass('inactif');
	if (col_selected!=undefined)
		$('#toolbar a.AddColumn,#toolbar a.RemoveColumn').removeClass('inactif');
	else
		$('#toolbar a.AddColumn,#toolbar a.RemoveColumn').addClass('inactif');
}
// selectionne une ligne
var row_selected=undefined;
var col_selected=undefined;
function unselect_all(){
	if (row_selected!=undefined)
		row_selected.removeClass('row_sel');
	row_selected = undefined;
	if (col_selected!=undefined)
		/*$('td.col_sel,th.col_sel').removeClass('col_sel');*/
		$('th.col_sel').removeClass('col_sel');
	col_selected = undefined;
}
jQuery.fn.selectRow = function() {
	if (row_selected!=undefined)
		row_selected.removeClass('row_sel');
	row_selected = this;
	update_toolbar_icones();
	return this
    .addClass('row_sel');
}
jQuery.fn.selectParentRow = function() {
	this.parents('tr.row').selectRow();
	return this;
}
jQuery.fn.selectCol = function() {
	if (col_selected!=undefined)
		$('th.col_sel').removeClass('col_sel');
	col_selected = this.attr('class');
	$('th.'+col_selected).addClass('col_sel');
	update_toolbar_icones();
	return this;
}

// replier/reduire par niveau
function getLevel(c){
  var n = c.match(/niveau-(\d+)/);
  if (n) {
    n = n[1] ? parseInt(n[1]) : 1;
  }
  else n=1;
  return n;
}
jQuery.fn.toggleLine = function() {
	cur = l = this.parents('tr.row');
	niveau = l.attr('name'); //getLevel(this.parent().attr('class'));
	expand=false;
	if (this.attr('src')==img_noeud_plus) {	
		expand=true;
	}
	else {
		this.attr('src',img_noeud_plus);
	}
	next = l.next('tr.row');
	if (expand){ 
		while (next.size() && (n=next.attr('name')>niveau)){
			cur.find('img.noeud').attr('src',img_noeud_moins);
			cur = next.show();
			next = next.next('tr.row');
		}
		cur.find('img.noeud').attr('src',img_noeud_plus);
	}
	else{
		while (next.size() && (n=next.attr('name')>niveau)){
			cur = next.hide();
			next = next.next('tr.row');
		}
	}
	return this;
}
function filtre_niveau(niveau){
	l=$('#');
	for (i=1;i<niveau;i++)
		l = l.add('tr.row[@name='+i+']:hidden');
	l.show().find('img.noeud').attr('src',img_noeud_moins);
	$('tr.row[@name='+niveau+']').show().find('img.noeud').attr('src',img_noeud_plus);
	l=$('#');
	niveau++;
	for (i=niveau;i<10;i++)
		l = l.add('tr.row[@name='+i+']:visible');
	l.hide();
}

// augmenter/reduire le niveau
jQuery.fn.changeLevel = function(increment) {
	niveau = getLevel(this.find('div.niveau').attr('class'));
	if (niveau+increment<1) return this;
	ids = this.attr('id')+':'+niveau;
	this.find('div.niveau').attr('class','niveau niveau-'+(niveau+increment));
	this.attr('name',niveau+increment);
	cur = this.next('tr.row');
	while (cur.size() && ( (n=getLevel(cur.find('div.niveau').attr('class'))) >niveau)){
		ids = ids+','+cur.attr('id')+':'+n;
		cur.find('div.niveau').attr('class','niveau niveau-'+(n+increment));
		cur.attr('name',niveau+increment);
		cur = cur.next('tr.row');
	}
	//alert(ids);
	return this;
}

// fonctions de la toolbar
function CollapseAll(){
	filtre_niveau(1);
}
function ExpandAll(){
	filtre_niveau(10);
}
function MoveLeft(){
	$('tr.row_sel').changeLevel(-1);
}
function MoveRight(){
	$('tr.row_sel').changeLevel(1);
}
function actionItem(lien,ajout){
	href = $(lien).attr('href');
	sel = $('tr.row_sel');
	if (sel.size()==0){
		if (!ajout) return false;
		sel = $('tr.row:last');
	}
	if (sel.size()){
		sel = sel.eq(0);
		href = href+':'+sel.attr('id');
		sel = sel.next('tr.row');
		if (sel.size())
			href = href+':'+sel.attr('id');
		else
			href = href+':0';
	}
	else
		href = href+':0:0';
	$(lien).attr('href',href);
}
function AddItem(lien){
	return actionItem(lien,true)
}
function RemoveItem(lien){
	return actionItem(lien,false)
}

function actionColumn(lien){
	href = $(lien).attr('href');
	sel = $('th.col_sel');
	if (sel.size()==0) return false;
	sel = sel.eq(0);
	sel.removeClass('col_sel');
	href = href+':'+sel.attr('class');
	sel.addClass('col_sel');
	sel = sel.next('th');
	if (sel.size())
		href = href+':'+sel.attr('class');
	else
		href = href+':0';
	$(lien).attr('href',href);
}
function AddColumn(lien){
	return actionColumn(lien);
}
function RemoveColumn(lien){
	return actionColumn(lien);
}

$(document).ready(function(){
	init_events();
});