/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.awt.Cursor;

import javax.swing.ImageIcon;
import javax.swing.JLabel;
import javax.swing.text.Position;

import jaxe.JaxeDocument;
import jaxe.JaxeElement;

import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.ProcessingInstruction;

/**
 * Representation-element for processing instructions 
 */
public class JESauf extends JaxeElement {
	boolean mettreajour = false;

	public JESauf() {
	}

    public JESauf(JaxeDocument doc) {
        this.doc = doc;
    }
    
    /**
     * Adds a flag with nodecontent as tooltip for a processing instruction
     */
    public void init(Position pos, Node noeud) {
        String image = "images_Jaxe/flag.png";
        JLabel l = new JLabel(new ImageIcon(image));
        l.setCursor(Cursor.getDefaultCursor());
        ProcessingInstruction p = (ProcessingInstruction) noeud;
        l.setToolTipText(p.getTarget() + ": " + p.getData());
        insertComponent(pos, l);
    }

	public Node nouvelElement(Element defbalise) {
		String nombalise = defbalise.getAttribute("nom");
		String noeudtypebalise = defbalise.getAttribute("noeudtype");
		Node newel = nouvelElementDOM(doc, noeudtypebalise, nombalise);
		mettreajour = true;
		return(newel);
	}
}
