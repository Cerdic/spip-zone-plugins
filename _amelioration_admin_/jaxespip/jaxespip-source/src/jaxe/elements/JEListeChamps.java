/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conform�ment aux dispositions de la Licence Publique G�n�rale GNU, telle que publi�e par la Free Software Foundation ; version 2 de la licence, ou encore (� votre choix) toute version ult�rieure.

Ce programme est distribu� dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de d�tail, voir la Licence Publique G�n�rale GNU .

Vous devez avoir re�u un exemplaire de la Licence Publique G�n�rale GNU en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.awt.Color;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.util.Vector;

import javax.swing.BorderFactory;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JList;
import javax.swing.JOptionPane;
import javax.swing.ListCellRenderer;
import javax.swing.text.Position;

import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeResourceBundle;

import org.w3c.dom.DOMException;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;


/**
 * Petite liste modifiable dans un dialogue. Les �l�ments de la liste ne peuvent �tre que
 * de courts textes.
 * Type d'�l�ment Jaxe: 'listechamps'
 */
public class JEListeChamps extends JaxeElement {

    JList jliste = null;
    Vector data;

    public JEListeChamps(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;
                
        data = new Vector();
        NodeList litems = el.getElementsByTagName("ITEM");
        for (int i=0; i<litems.getLength(); i++) {
            Node n = litems.item(i);
            Node n2 = n.getFirstChild(); // on suppose que ITEM contient du texte
            if (n2 != null) {
                if (n2.getNodeValue() != null)
                    data.add(n2.getNodeValue().trim());
                else
                    data.add("");
            } else
                data.add("");
        }
            // on ignore le reste
        jliste = new JList(data);
        
        jliste.setCellRenderer(new MyCellRenderer());// pour �viter les items minuscules quand la valeur est ""
        
        jliste.addMouseListener(new MyMouseListener(this, doc.jframe));

        insertComponent(pos, jliste);
    }
    
    public Node nouvelElement(Element defbalise) {
        // ajouter dialogue pour le type de liste
        
        String nombalise = doc.cfg.nomBalise(defbalise);
        String snitems = JOptionPane.showInputDialog(doc.jframe,
            JaxeResourceBundle.getRB().getString("liste.NbElements"),
            JaxeResourceBundle.getRB().getString("zone.NouvelleBalise") + " " + nombalise,
            JOptionPane.QUESTION_MESSAGE);
        int nitems;
        try {
            nitems = (Integer.valueOf(snitems)).intValue();
        } catch (NumberFormatException ex) {
            JOptionPane.showMessageDialog(doc.jframe, JaxeResourceBundle.getRB().getString("erreur.Conversion"),
                JaxeResourceBundle.getRB().getString("zone.NouvelleBalise") + " " + nombalise, JOptionPane.ERROR_MESSAGE);
            return(null);
        }

        Element newel = nouvelElementDOM(doc, defbalise);
        for (int i=0; i<nitems; i++) {
            Element itemel = doc.DOMdoc.createElement("ITEM");
            newel.appendChild(itemel);
        }

        return(newel);
    }
    
    public void afficherDialogue(JFrame jframe) {
        Element el = (Element)noeud;

        DialogueListeChamps dlg = new DialogueListeChamps(doc.jframe, "liste: " + el.getTagName(), data);
        if (!dlg.afficher())
            return;
        data = dlg.data;
        
        // mise � jour DOM
        try {
            for (Node n=el.getFirstChild(); n != null; n=el.getFirstChild())
                el.removeChild(n);
            for (int i=0; i<data.size(); i++) {
                Element itemel = doc.DOMdoc.createElement("ITEM");
                el.appendChild(itemel);
                Node ns = doc.DOMdoc.createTextNode((String)data.get(i));
                itemel.appendChild(ns);
            }
        } catch (DOMException ex) {
            System.err.println("DOMException: " + ex.getMessage());
        }

        majAffichage();
    }
    
    public void majAffichage(boolean majArbre) {
        jliste.setListData(data);
    }
    
    class MyMouseListener extends MouseAdapter {
        JEListeChamps jei;
        JFrame jframe;
        public MyMouseListener(JEListeChamps obj, JFrame jframe) {
            super();
            jei = obj;
            this.jframe = jframe;
        }
        public void mouseClicked(MouseEvent e) {
            if (e.getClickCount() == 2) {
                //int index = list.locationToIndex(e.getPoint());
                //System.out.println("Double clicked on Item " + index);
                jei.afficherDialogue(jframe);
            }
        }
    }

    class MyCellRenderer extends JLabel implements ListCellRenderer {
        public MyCellRenderer() {
            setOpaque(true);
        }
        public Component getListCellRendererComponent(
            JList list,
            Object value,
            int index,
            boolean isSelected,
            boolean cellHasFocus)
        {
            setText(value.toString());
            setBackground(isSelected ? Color.black : Color.white);
            setForeground(isSelected ? Color.white : Color.black);
            setBorder(BorderFactory.createLineBorder(Color.darkGray));
            if ("".equals(value.toString())) {
                Dimension mini = new Dimension(50,12);
                setMinimumSize(mini);
                //Dimension pref = getPreferredSize();
                //if (pref.height < mini.height || pref.width < mini.width)
                setPreferredSize(mini);
            } else
                setPreferredSize(null);
            return this;
        }
    }
}
