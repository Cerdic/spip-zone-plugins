/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conform�ment aux dispositions de la Licence Publique G�n�rale GNU, telle que publi�e par la Free Software Foundation ; version 2 de la licence, ou encore (� votre choix) toute version ult�rieure.

Ce programme est distribu� dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de d�tail, voir la Licence Publique G�n�rale GNU .

Vous devez avoir re�u un exemplaire de la Licence Publique G�n�rale GNU en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.awt.Color;
import java.awt.Image;
import java.awt.Toolkit;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.image.FilteredImageSource;
import java.awt.image.ImageFilter;
import java.awt.image.ImageProducer;
import java.awt.image.RGBImageFilter;
import java.util.ArrayList;

import javax.swing.ImageIcon;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.text.Position;

import jaxe.DialogueAttributs;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;

import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

/**
 * El�ment d'une liste, affich� avec des puces ou des num�ros. Ce type d'�l�ment de liste,
 * ins�r� dans le texte, peut avoir n'importe quelle longueur, � la diff�rence des �l�ments d'un JEListe.
 * Type d'�l�ment Jaxe: 'item'
 */
public class JEItem extends JaxeElement {

    static String newline = "\n";
    String fichierPastille1 = "images_Jaxe/pastille1.gif";
    String fichierPastille2 = "images_Jaxe/pastille2.gif";
    boolean selectionne = false;
    Image imagePastille1 = null;
    Image imagePastille2 = null;
    Image imagePastille1sel = null;
    Image imagePastille2sel = null;
    ImageIcon iconePastille1 = null;
    ImageIcon iconePastille2 = null;
    int typeListe = 0;
    JLabel label = null;
    boolean mettreajour = false;

    public JEItem(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        Position newpos;
        
        JaxeElement jeparent = null;
        if (getParent() != null)
            jeparent = doc.getElementForNode(getParent().noeud);
        if (jeparent instanceof JEListe)
            typeListe = ((JEListe)jeparent).typeListe;
        if (typeListe == JEListe.NUMEROS) {
            int lp = posDansListe();
            label = new JLabel(lp+".");
            label.setOpaque(true);
            label.setBackground(Color.white);
            label.setAlignmentY((float)0.9);
        } else {
            if (doc.cfg != null) {
                Element defbalise = doc.cfg.getElementDef((Element)noeud);
                
                if (defbalise != null)
                    fichierPastille1 = doc.cfg.getParamFromDefinition(defbalise, "image1", fichierPastille1); 
            }
            
            iconePastille1 = new ImageIcon(doc.getClass().getResource(fichierPastille1));
            label = new JLabel(iconePastille1);
            label.setAlignmentY(1);
        }
        label.addMouseListener(new MyMouseListener(this, doc.jframe));
        newpos = insertComponent(pos, label);
        
        creerEnfants(newpos);
        
        //doc.insertString(newpos.getOffset(), newline, null);
        if (doc.cfg != null) {
            Element defbalise = doc.cfg.getElementDef((Element)noeud);
            
            if (defbalise != null)
                fichierPastille2 = doc.cfg.getParamFromDefinition(defbalise, "image2", fichierPastille2); 
        }
        
        iconePastille2 = new ImageIcon(doc.getClass().getResource(fichierPastille2));
        newpos = insertIcon(newpos, iconePastille2);
        
        if (mettreajour) {
            majListe(false);
            mettreajour = false;
        }
    }
    
    public int posDansListe() {
        Element parel = (Element)getParent().noeud;
        NodeList lchildren = parel.getChildNodes();
        String itemTag = noeud.getNodeName();
        int p = 1;
        for (int i=0; i<lchildren.getLength(); i++) {
            if (itemTag.equals(lchildren.item(i).getNodeName())) {
                if (lchildren.item(i) == noeud)
                    return(p);
                p++;
            }
        }
        System.err.println("Erreur: Impossible de retrouver le num�ro dans la liste");
        return(0);
    }
    
    public void majNombre(int p) {
        if (p == 0)
            p = posDansListe();
        label.setText(p + ".");
    }
    
    public void majListe(boolean pourEffacer) {
        if (typeListe == JEListe.NUMEROS) {
            Element parel = (Element)getParent().noeud;
            NodeList lchildren = parel.getChildNodes();
            String itemTag = noeud.getNodeName();
            int p = 1;
            for (Node n=parel.getFirstChild(); n != null; n=n.getNextSibling()) {
                if (itemTag.equals(n.getNodeName()) && (!pourEffacer || n != noeud)) {
                    JEItem je = (JEItem)doc.getElementForNode(n);
                    je.majNombre(p);
                    p++;
                }
            }
        }
    }
    
    public void effacer() {
        super.effacer();
        majListe(true);
    }
    
    public Node nouvelElement(Element defbalise) {
        Element newel = nouvelElementDOM(doc, defbalise);
        
        mettreajour = true;
        return(newel);
    }

    public Position insPosition() {
        return(fin);
    }
    
    public void selection(boolean select) {
        if (!selectionne && select) {
            if (iconePastille1 != null && imagePastille1 == null)
                imagePastille1 = iconePastille1.getImage();
            if (imagePastille2 == null)
                imagePastille2 = iconePastille2.getImage();
            if (imagePastille1sel == null)
                creerImagesSel();
            if (iconePastille1 != null)
                iconePastille1.setImage(imagePastille1sel);
            else
                label.setBackground(Color.lightGray);
            iconePastille2.setImage(imagePastille2sel);
        }
        if (selectionne && !select) {
            if (iconePastille1 != null)
                iconePastille1.setImage(imagePastille1);
            else
                label.setBackground(Color.white);
            iconePastille2.setImage(imagePastille2);
        }
        selectionne = select;
        doc.textPane.repaint();
        super.selection(select);
    }
    
    protected void creerImagesSel() {
        ImageFilter filtre = new FiltreGris();
        if (imagePastille1 != null) {
            ImageProducer producteur1 = new FilteredImageSource(imagePastille1.getSource(), filtre);
            imagePastille1sel = Toolkit.getDefaultToolkit().createImage(producteur1);
        }
        ImageProducer producteur2 = new FilteredImageSource(imagePastille2.getSource(), filtre);
        imagePastille2sel = Toolkit.getDefaultToolkit().createImage(producteur2);
    }
    
    class FiltreGris extends RGBImageFilter {
        private int gris = 0xFFAFAFAF;
        public FiltreGris() {
            canFilterIndexColorModel = true;
        }
        public int filterRGB(int x, int y, int rgb) {
            return(rgb & gris);
        }
    }
    
    public void afficherDialogue(JFrame jframe) {
        Element el = (Element)noeud;

        Element defbalise = doc.cfg.getElementDef(el);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                "item: " + el.getTagName(), defbalise, el);
            if (dlg.afficher()) 
                dlg.enregistrerReponses();
            dlg.dispose();
        }
    }
    
    class MyMouseListener extends MouseAdapter {
        JEItem jei;
        JFrame jframe;
        public MyMouseListener(JEItem obj, JFrame jframe) {
            super();
            jei = obj;
            this.jframe = jframe;
        }
        public void mouseClicked(MouseEvent e) {
            jei.afficherDialogue(jframe);
        }
    }
}
