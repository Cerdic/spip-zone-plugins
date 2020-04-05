// Filepicker : by Daniel FAIVRE - 2008-2012 */

var TFP = new TFilePicker();

function TFilePicker() {
	this.popup = TFPopup;
	this.select = TFSelect;
	this.draw = TFDraw;
	this.preview = TFPreview;
}

function TFPopup(field, file, dir, root_directory) {
	this.field = field;
	var w = 800, h = 380,
	move = screen ? ',left=' + ((screen.width - w) >> 1) + ',top=' + ((screen.height - h) >> 1) : '',
  o_fileWindow = window.open(root_directory + '?action=filepickerwrapper&file='+file+'&dir='+dir,null,"help=no,status=no,scrollbars=yes,resizable=no" + move + ",width=" + w + ",height=" + h + ",dependent=yes",true);
	o_fileWindow.opener = window;
	o_fileWindow.focus();
}

function TFSelect(f) {
	this.field.value = '' + f;
	this.win.close();
}

function TFDraw(o_win, o_doc) {
	this.win = o_win;
	this.doc = o_doc;
}

function TFPreview(f) {
	this.doc.selection.src = f;
}