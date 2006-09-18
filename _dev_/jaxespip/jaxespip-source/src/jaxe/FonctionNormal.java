/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import javax.swing.text.BadLocationException;

import jaxe.elements.JEStyle;
import jaxe.elements.JETexte;

import org.w3c.dom.Element;
import org.w3c.dom.Node;


public class FonctionNormal implements Fonction {

    public boolean appliquer(JaxeDocument doc, int start, int end) {
        try {
        JaxeElement firstel = doc.rootJE.elementA(start);
        JaxeElement p1 = firstel;
        if (p1 instanceof JEStyle || p1 instanceof JETexte)
            p1 = p1.getParent();
        JaxeElement lastel = doc.rootJE.elementA(end - 1);
        JaxeElement p2 = lastel;
        if (p2 instanceof JEStyle || p2 instanceof JETexte)
            p2 = p2.getParent();
        if (p1 != p2)
            return true;
        
        doc.textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString("style.normal"), false);
        
        Node next = firstel.noeud.getNextSibling();
        
        if (firstel instanceof JEStyle) {
            
            if (firstel.debut.getOffset() <= start) {
                
                int firsteldebut = firstel.debut.getOffset();
                int firstelfin = firstel.fin.getOffset();
                List path = new ArrayList(((JEStyle)firstel)._styles);
                Iterator it;
                String texte0 = ((JEStyle)firstel).getText();

                String texte1 = texte0.substring(0, start - firsteldebut);
                String texte2;
                if (firstelfin >= end)
                    texte2 = texte0.substring(start - firsteldebut, end - firsteldebut);
                else
                    texte2 = texte0.substring(start - firsteldebut);
                Element defbalise = doc.cfg.getElementDef((Element)firstel.noeud);
//                String ceStyle = defbalise.getAttribute("param");
                String ceStyle  = ((JEStyle)firstel).ceStyle;//doc.cfg.getParamFromDefinition(defbalise, "style", defbalise.getAttribute("param"));              
                JaxeUndoableEdit jedit = new JaxeUndoableEdit(JaxeUndoableEdit.SUPPRIMER, firstel);
                jedit.doit();
                
                if (firsteldebut < start) {
                    JEStyle newje = new JEStyle(doc);
                    newje.ceStyle = ceStyle;
                    Node newel = doc.DOMdoc.createTextNode(texte1);
                    
                    it = path.iterator();
                    while (it.hasNext()) {
                        Node node = ((Node) it.next()).cloneNode(false); 
                        node.appendChild(newel);
                        newel = node;
                    }
                    
                    //Element newel = JaxeElement.nouvelElementDOM(doc, defbalise);
                    newje.noeud = newel;
                    newje.doc = doc;
                    doc.dom2JaxeElement.put(newel, newje);
                    newje.debut = doc.createPosition(firsteldebut);
                    //newje.fin = doc.createPosition(start-1);
                    newje.fin = null;
                    jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje);
                    jedit.doit();
                }
                
                
                JETexte newjetexte = JETexte.nouveau(doc, doc.createPosition(start), null, texte2);
                jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newjetexte);
                jedit.doit();
                
                if (firstelfin >= end) {
                    String texte3 = texte0.substring(end - firsteldebut);
                    JEStyle newje = new JEStyle(doc);
                    newje.ceStyle = ceStyle;
                    Node newel = doc.DOMdoc.createTextNode(texte3);

                    it = path.iterator();
                    while (it.hasNext()) {
                        Node node = ((Node) it.next()).cloneNode(false);
                        //if (!it.hasNext()) break;
                        node.appendChild(newel);
                        newel = node;
                    }
                    newje.noeud = newel;
                    newje.doc = doc;
                    doc.dom2JaxeElement.put(newel, newje);
                    newje.debut = doc.createPosition(end);
                    newje.fin = doc.createPosition(firstelfin);
                    jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje);
                    jedit.doit();
                }
                
            } else
                tonormal((JEStyle)firstel);
        }
        if (lastel != firstel) {
            int pos = firstel.fin.getOffset() + 1;
            while (next != null && next != lastel.noeud && pos < end) {
                JaxeElement je = p1.elementA(pos);
                next = je.noeud.getNextSibling();
                pos = je.fin.getOffset() + 1;
                if (je instanceof JEStyle)
                    tonormal((JEStyle)je);
            }
        }
        if (lastel != firstel && lastel instanceof JEStyle) {
            if (lastel.fin.getOffset() >= end) {
                int lasteldebut = lastel.debut.getOffset();
                int lastelfin = lastel.fin.getOffset();
                List styles = ((JEStyle) lastel)._styles;
                String style = ((JEStyle) lastel).ceStyle;
                String texte0 = ((JEStyle)lastel).getText();
                String texte1 = texte0.substring(0, end - lasteldebut);
                String texte2 = texte0.substring(end - lasteldebut);
                
                JaxeUndoableEdit jedit = new JaxeUndoableEdit(JaxeUndoableEdit.SUPPRIMER, lastel);
                jedit.doit();
                
                JETexte newjetexte = JETexte.nouveau(doc, doc.createPosition(lasteldebut), null, texte1);
                jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newjetexte);
                jedit.doit();
                
                Element defbalise = doc.cfg.getElementDef((Element)lastel.noeud);
                String ceStyle = defbalise.getAttribute("param");
                JEStyle newje = new JEStyle(doc);
                newje.ceStyle = ceStyle;
                Node newel = doc.DOMdoc.createTextNode(texte2);
				Iterator it = styles.iterator();
				while (it.hasNext()) {
					Node node = ((Node) it.next()).cloneNode(false);
					node.appendChild(newel);
					newel = node;
				}
                newje.noeud = newel;
                newje.doc = doc;
                newje.ceStyle = style;
                doc.dom2JaxeElement.put(newel, newje);
                newje.debut = doc.createPosition(end);
                newje.fin = doc.createPosition(lastelfin);
                jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje);
                jedit.doit();
            } else
                tonormal((JEStyle)lastel);
        }
        //StyleTools.joinStyles(p1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        doc.textPane.finEditionSpeciale();
        return true;
    }
    
    public static void tonormal(JEStyle je) {
        String texte = je.getText();
        JETexte newje = JETexte.nouveau(je.doc, je.debut, je.fin, texte);
        int start = je.debut.getOffset();
        JaxeUndoableEdit jedit = new JaxeUndoableEdit(JaxeUndoableEdit.SUPPRIMER, je);
        jedit.doit();
        jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje);
        jedit.doit();
    }

}
