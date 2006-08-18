/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.util.Vector;

import javax.swing.JFrame;
import javax.swing.JOptionPane;

import jaxe.JaxeDocument;
import jaxe.JaxeResourceBundle;

import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;

/**
 * Elément dont la définition est inconnue
 */
public class JEInconnu extends JEString {

    public JEInconnu(JaxeDocument doc) {
        super(doc);
    }
    
    public Node nouvelElement(Element defbalise) {
        // ajouter dialogue pour le nom de la balise et les attributs
        String balise = JOptionPane.showInputDialog(doc.jframe,
            JaxeResourceBundle.getRB().getString("inconnu.NomElement"),
            JaxeResourceBundle.getRB().getString("inconnu.NouvelleBalise"), JOptionPane.QUESTION_MESSAGE);

        Node newel = nouvelElementDOM(doc, "", balise);
        
        return(newel);
    }
    
    public void afficherDialogue(JFrame jframe) {
        Element el = (Element)noeud;
        
        Vector data = new Vector();
        NamedNodeMap attmap = el.getAttributes();
        for (int i=0; i<attmap.getLength(); i++) {
            Node attn = attmap.item(i);
            String name = attn.getNodeName();
            String val = attn.getNodeValue();
            data.add(name + "=" + val);
        }
        DialogueInconnu dlg = new DialogueInconnu(doc.jframe, doc,
            getString("inconnu.Balise") + " " + el.getTagName(), data, el);
        if (!dlg.afficher())
            return;
        dlg.enregistrerReponses();
        majAffichage();
        doc.textPane.miseAJourArbre();
    }
    
    public void majAffichage() {
    }
}

