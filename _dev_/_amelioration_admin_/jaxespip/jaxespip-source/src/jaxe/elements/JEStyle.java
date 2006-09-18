/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.util.ArrayList;
import java.util.List;

import javax.swing.text.BadLocationException;
import javax.swing.text.Position;

import jaxe.FonctionAjStyle;
import jaxe.FonctionNormal;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;

import org.w3c.dom.Element;
import org.w3c.dom.Node;


/**
 * Elément de style (B ou I ou SUB ou SUP). Modifie l'aspect du texte en conséquence.
 * Type d'élément Jaxe: 'style'
 * paramètre: style: NORMAL | GRAS | ITALIQUE | EXPOSANT | INDICE | SOULIGNE
 *
 * NORMAL ne doit plus être utilisé (il faut utiliser FONCTION à la place, avec
 * classe="jaxe.FonctionNormal")
 */
public class JEStyle extends JaxeElement {

    public String ceStyle;
    public List _styles = new ArrayList();
    
    public JEStyle(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        _styles.clear();
        Element defbalise = doc.cfg.getElementDef((Element)noeud);
        ceStyle = defbalise.getAttribute("param");
        ceStyle = doc.cfg.getParamFromDefinition(defbalise, "style", ceStyle);
        if (ceStyle == null || ceStyle.equals(""))
            return;
        cutNode(noeud);
        _styles.add(0, noeud);
        
        Node node = noeud.getFirstChild();
        Node textnode = noeud.getFirstChild();
        while (node != null) {
            if (node.getNodeType() == Node.TEXT_NODE) {
                textnode = node;
            } else {
                _styles.add(node);
                Element defbalise2 = doc.cfg.getElementDef((Element)node);
                String style = defbalise2.getAttribute("param");
                style = doc.cfg.getParamFromDefinition(defbalise2, "style", style);
                if (ceStyle != null || !ceStyle.equals("")) {
                    ceStyle = ceStyle + ";" + style;
                } else {
                    ceStyle = style;
                }
            }
            node = node.getFirstChild();
        }
        String texte = null;
        if (textnode != null) {
            texte = textnode.getNodeValue();
            Node next = textnode.getNextSibling();
            while (next != null && next.getNodeType() == Node.TEXT_NODE) {
                texte = texte + next.getNodeValue();
            }
        }
        	
        int offsetdebut = pos.getOffset();
        Position newpos = pos;
        if (texte != null)
            newpos = insertText(newpos, texte);
        //creerEnfants(newpos);
/*        for (Node n=noeud.getFirstChild(); n != null; n=n.getNextSibling())  {
            if (n.getNodeType() != Node.TEXT_NODE) 
                //creerEnfant(newpos, n);
                ;
        }
  */      
        if (texte != null)
            changerStyle(ceStyle, offsetdebut, newpos.getOffset() - offsetdebut);
    }
    
    public String getText() {
        Node n = noeud;
        while (n != null && n.getNodeType() != Node.TEXT_NODE) {
            n = n.getFirstChild();
        }
        return n.getNodeValue();
    }
    
    /**
     * @param noeud
     */
    private void cutNode(Node node) {
        int count = 1;
        boolean ins = false;
        Node child = node.getFirstChild();
        while (child != null) {
            cutNode(child);
            if (count > 1) {
                Node add = child;
                child = child.getPreviousSibling();
                count--;
                Node n = node.cloneNode(false);
                n.appendChild(add);
                if (node.getNextSibling() != null) {
                    if (!ins) {
                        node.getParentNode().insertBefore(n, node.getNextSibling());
                        ins = true;
                    } else {
                        node.getParentNode().insertBefore(n, node.getNextSibling().getNextSibling());
                    }
                } else {
                    node.getParentNode().appendChild(n);
                }
            }
            count++;
            child = child.getNextSibling();
        }
        
    }

    public Node nouvelElement(Element defbalise) {
        return(null);
    }
    
    public static JEStyle nouveau(JaxeDocument doc, int start, int end, Element defbalise) {
        String ceStyle = defbalise.getAttribute("param");
        ceStyle = doc.cfg.getParamFromDefinition(defbalise, "style", ceStyle);
        if (ceStyle.equals("")) {
            System.err.println("Pas d'attribut param pour le style");
            return null;
        }
        
        if (ceStyle.equals(kNormal)) {
            // conservé temporairement pour la compatibilité
            FonctionNormal fct = new FonctionNormal();
            fct.appliquer(doc, start, end);
            return null;
        }
        if (doc.elementA(start) instanceof JEStyle || doc.elementA(start) != doc.elementA(end)) {
            Element newel = nouvelElementDOM(doc, defbalise);
            FonctionAjStyle fct = new FonctionAjStyle(newel);
            if (fct.appliquer(doc, start, end)) {
                return null;
            }
            
        }
        
        JaxeElement p1 = doc.rootJE.elementA(start);
        JaxeElement p2 = doc.rootJE.elementA(end - 1);

        if (p1 == p2) {
            p1 = doc.rootJE.elementA(start);
            p2 = doc.rootJE.elementA(end - 1);
        }
        if (p1 != p2 || !(p1 instanceof JETexte))
            return(null);
        
        try {
            String texte = doc.textPane.getText(start, end-start);
            
            JEStyle newje = new JEStyle(doc);
            
            Node textnode = doc.DOMdoc.createTextNode(texte);
            Element newel = nouvelElementDOM(doc, defbalise);
            newel.appendChild(textnode);
            newje.noeud = newel;
            newje.doc = doc;
            doc.dom2JaxeElement.put(newel, newje);
            
            newje.debut = doc.createPosition(start);
            newje.fin = doc.createPosition(end - 1);
            return(newje);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return(null);
        }
    }
    
}
