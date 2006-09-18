/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conform�ment aux dispositions de la Licence Publique G�n�rale GNU, telle que publi�e par la Free Software Foundation ; version 2 de la licence, ou encore (� votre choix) toute version ult�rieure.

Ce programme est distribu� dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de d�tail, voir la Licence Publique G�n�rale GNU .

Vous devez avoir re�u un exemplaire de la Licence Publique G�n�rale GNU en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.util.ArrayList;

import javax.swing.JFrame;
import javax.swing.text.Position;

import jaxe.DialogueAttributs;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeResourceBundle;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

/**
 * El�ment vide (est affich� comme un bouton)
 * Type d'�l�ment Jaxe: 'vide'
 */
public class JEVide extends JaxeElement {

    static String newline = "\n";
    MonBouton lstart = null;
    ArrayList attributsTitre = null;

    public JEVide(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;
        
        String titre = el.getTagName();
        Element defbalise = doc.cfg.getElementDef(el);
        String valeurTitre = null;
        if (defbalise != null) {
            attributsTitre = doc.cfg.getValeursParam(defbalise, "titreAtt");
            if (attributsTitre != null)
                for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
                    if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                        valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        }
        if (valeurTitre != null)
            titre += " '" + valeurTitre + "'";
        lstart = new MonBouton(titre, false);
        if (el.getPrefix() != null)
            lstart.setEnsembleCouleurs(1);
        insertComponent(pos, lstart);
    }
    
    public Node nouvelElement(Element defbalise) {
        Element newel = nouvelElementDOM(doc, defbalise);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            String nombalise = doc.cfg.nomBalise(defbalise);
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                JaxeResourceBundle.getRB().getString("zone.NouvelleBalise") + " " + nombalise, defbalise, newel);
            if (!dlg.afficher())
                return null;
            try {
                dlg.enregistrerReponses();
            } catch (Exception ex) {
                System.err.println(ex.getClass().getName() + ": " + ex.getMessage());
                return(null);
            }
        }
        
        return(newel);
    }
    
    public void afficherDialogue(JFrame jframe) {
        Element el = (Element)noeud;

        Element defbalise = doc.cfg.getElementDef(el);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                getString("vide.Vide") + ": " + el.getTagName(), defbalise, el);
            if (dlg.afficher()) {
                dlg.enregistrerReponses();
                majAffichage();
            }
            dlg.dispose();
        }
    }
    
    public void majAffichage() {
        Element el = (Element)noeud;
        
        String titre = el.getTagName();
        String valeurTitre = null;
        if (attributsTitre != null) {
            for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
                if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                    valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        }
        if (valeurTitre != null)
            titre += " '" + valeurTitre + "'";
        lstart.setText(titre);
        doc.imageChanged(lstart);
    }
}
