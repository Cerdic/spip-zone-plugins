/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conform�ment aux dispositions de la Licence Publique G�n�rale GNU, telle que publi�e par la Free Software Foundation ; version 2 de la licence, ou encore (� votre choix) toute version ult�rieure.

Ce programme est distribu� dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de d�tail, voir la Licence Publique G�n�rale GNU .

Vous devez avoir re�u un exemplaire de la Licence Publique G�n�rale GNU en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
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
 * El�ment dont la d�finition est inconnue
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

