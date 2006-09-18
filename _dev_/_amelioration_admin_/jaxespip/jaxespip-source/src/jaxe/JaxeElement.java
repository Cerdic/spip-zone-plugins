/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Font;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import javax.swing.BorderFactory;
import javax.swing.Icon;
import javax.swing.JComponent;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.UIManager;
import javax.swing.border.Border;
import javax.swing.text.AttributeSet;
import javax.swing.text.BadLocationException;
import javax.swing.text.Position;
import javax.swing.text.SimpleAttributeSet;
import javax.swing.text.Style;
import javax.swing.text.StyleConstants;

import jaxe.elements.JEInconnu;
import jaxe.elements.JESauf;
import jaxe.elements.JEStyle;
import jaxe.elements.JESwing;
import jaxe.elements.JETableTexte;
import jaxe.elements.JETexte;

import org.apache.oro.text.regex.MalformedPatternException;
import org.apache.oro.text.regex.MatchResult;
import org.apache.oro.text.regex.Pattern;
import org.apache.oro.text.regex.Perl5Compiler;
import org.apache.oro.text.regex.Perl5Matcher;
import org.w3c.dom.DOMException;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.ProcessingInstruction;

/**
 * Elément Jaxe, représentant à la fois l'affichage graphique et l'arbre DOM
 * correspondant (noeud)
 */
public abstract class JaxeElement {

    public final static String kNormal = "NORMAL";

    public final static String kGras = "GRAS";

    public final static String kItalique = "ITALIQUE";

    public final static String kExposant = "EXPOSANT";

    public final static String kCouleur = "PCOULEUR";

    public final static String kCouleurDeFond = "FCOULEUR";

    public final static String kIndice = "INDICE";

    public final static String kSouligne = "SOULIGNE";

    public final static String kBarre = "BARRE";

    public final static Map styleMapper = new HashMap() {
    };
    
    private static Perl5Compiler perlComp = new Perl5Compiler();
    
    private Perl5Matcher matcher = new Perl5Matcher();

    //static String newline = Jaxe.newline;
    public Position debut = null; // position du premier caractère de l'élément

    public Position fin = null; // position du dernier caractère de l'élément

    public Node noeud;

    public JaxeDocument doc;

    public ArrayList jcomps = new ArrayList(); // de JComponent

    public ArrayList compos = new ArrayList(); // de Position (positions des

    // composants)

    private boolean effacementAutorise = true;

    private boolean editionAutorisee = true;

    /**
     * Insère le texte de l'élément à partir de l'arbre DOM, à la position pos
     * dans le texte
     */
    public abstract void init(Position pos, Node noeud);

    /**
     * Initialise le champ noeud, met à jour dom2JaxeElement, et appelle
     * init(pos, noeud)
     */
    public void creer(Position pos, Node noeud) {
        this.noeud = noeud;
        doc.dom2JaxeElement.put(noeud, this);
        Element defbalise = null;
        if (doc.cfg != null) {
            if (noeud.getNodeType() == Node.ELEMENT_NODE)
                    defbalise = doc.cfg.getElementDef((Element) noeud);
            if (noeud.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE)
                    defbalise = doc.cfg
                            .getProcessingDef((ProcessingInstruction) noeud);
            if (defbalise != null) {
                String seffacement = doc.cfg.getParamFromDefinition(defbalise,
                        "effacementAutorise", null);
                String sedition = doc.cfg.getParamFromDefinition(defbalise,
                        "editionAutorisee", null);

                effacementAutorise = !("false".equals(seffacement));
                editionAutorisee = !("false".equals(sedition));
            }
        }
        init(pos, noeud);
    }

    public abstract Node nouvelElement(Element defbalise);

    /**
     * Affiche le dialogue correspondant à l'élément
     */
    public void afficherDialogue(JFrame jframe) {
        // à remplacer dans les sousclasses
    }

    /**
     * Mise à jour de l'affichage par rapport à l'arbre XML
     */
    public void majAffichage() {
        // à remplacer dans les sousclasses
    }

    /**
     * Test et mise à jour de l'affichage de la validité
     */
    public void majValidite() {
        // à remplacer dans les sousclasses
    }

    /**
     * Renvoit la liste des composants graphiques utilisés dans l'affichage en
     * plus du texte
     */
    public ArrayList getComponents() {
        return jcomps;
    }

    /**
     * Renvoit la liste des positions dans le texte des composants graphiques
     */
    public ArrayList getComponentPositions() {
        return compos;
    }

    /**
     * Insère le texte dans le Textpane en mettant à jour debut et fin
     */
    public Position insertText(Position pos, String texte, AttributeSet attset) {
        try {
            int offsetdebut = pos.getOffset();
            doc.insertString(pos.getOffset(), texte, attset);
            if (debut == null) debut = doc.createPosition(offsetdebut);
            if (pos.getOffset() == 0) // bug fix with insertString
                    pos = doc.createPosition(1);
            fin = doc.createPosition(pos.getOffset() - 1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        return (pos);
    }

    /**
     * Insère le texte dans le Textpane en mettant à jour debut et fin
     */
    public Position insertText(Position pos, String texte) {
        SimpleAttributeSet att = null;
        JaxeElement jeparent;
        if (debut == null) {
            Node parentnode = noeud.getParentNode();
            if (parentnode != null)
                jeparent = doc.getElementForNode(parentnode);
            else
                jeparent = null;
        } else
            jeparent = this;
        if (jeparent != null) {
            if (jeparent.debut.getOffset() == pos.getOffset() && !(jeparent instanceof JESwing))
                jeparent = jeparent.getParent();
            if (jeparent != null)
                att = jeparent.attStyle(null);
        }
        return (insertText(pos, texte, att));
    }

    /**
     * Insère le composant graphique dans le texte, en l'ajoutant dans la liste
     * des composants et en mettant à jour debut et fin
     */
    public Position insertComponent(Position pos, JComponent comp) {
        int offsetdebut = pos.getOffset();
        Style s = doc.textPane.addStyle(null, null);
        StyleConstants.setComponent(s, comp);
        try {
            doc.insertString(pos.getOffset(), "*", s, false);
            jcomps.add(comp);
            compos.add(doc.createPosition(pos.getOffset() - 1));
            doc.fixbug(comp);
            if (debut == null) debut = doc.createPosition(offsetdebut);
            fin = doc.createPosition(offsetdebut);
            if (pos.getOffset() == 0) // bug fix with insertString
                pos = doc.createPosition(1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        return (pos);
    }

    /**
     * Insère l'icône dans le texte, en l'ajoutant dans la liste des composants
     * et en mettant à jour debut et fin
     */
    public Position insertIcon(Position pos, Icon icon) {
        int offsetdebut = pos.getOffset();
        Style s = doc.textPane.addStyle(null, null);
        StyleConstants.setIcon(s, icon);
        try {
            doc.insertString(pos.getOffset(), "*", s, false);
            jcomps.add(icon);
            compos.add(doc.createPosition(pos.getOffset() - 1));
            //doc.fixbug(comp);
            if (debut == null) debut = doc.createPosition(offsetdebut);
            fin = doc.createPosition(offsetdebut);
            if (pos.getOffset() == 0) // bug fix with insertString
                pos = doc.createPosition(1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        return (pos);
    }

    /**
     * Renvoit l'élément de plus bas niveau se trouvant à la position donnée
     * dans le texte
     */
    public JaxeElement elementA(int pos) {
        if (debut == null || fin == null) return null;
        if (debut.getOffset() > pos || fin.getOffset() < pos) return null;
        for (Node n = noeud.getFirstChild(); n != null; n = n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE
                    || n.getNodeType() == Node.TEXT_NODE
                    || n.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                JaxeElement je = doc.getElementForNode(n);
                if (je != null) {
                    JaxeElement nje = je.elementA(pos);
                    if (nje != null) return nje;
                }
            }
        }
        return this;
    }

    /**
     * Renvoit les éléments se trouvant dans la zone du texte indiquée
     */
    public ArrayList elementsDans(int dpos, int fpos) {
        ArrayList l = new ArrayList();
        if (debut == null || fin == null) return l;
        if (debut.getOffset() > fpos || fin.getOffset() < dpos) return l;
        if (debut.getOffset() >= dpos && (fin.getOffset() <= fpos ||
                this instanceof JESwing && fin.getOffset() == fpos+1))
            l.add(this);
        else
            for (Node n = noeud.getFirstChild(); n != null; n = n
                    .getNextSibling()) {
                if (n.getNodeType() == Node.ELEMENT_NODE
                        || n.getNodeType() == Node.TEXT_NODE
                        || n.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                    JaxeElement je = doc.getElementForNode(n);
                    if (je != null) l.addAll(je.elementsDans(dpos, fpos));
                }
            }
        return l;
    }

    /**
     * Renvoit le nombre XPath (le numéro de l'élément dans la liste des
     * éléments avec ce nom), ou 0 si le noeud n'a pas de parent.
     */
    public int nombreXPath() {
        JaxeElement p = getParent();
        if (p == null) return (0);
        int no = 0;
        String nomel = noeud.getNodeName();
        for (Node n = p.noeud.getFirstChild(); n != null; n = n
                .getNextSibling()) {
            if (nomel.equals(n.getNodeName())) no++;
            if (n == noeud) break;
        }
        return (no);
    }

    /**
     * Renvoit le chemin XML pour la position pos
     */
    public String cheminA(int pos) {
        if (debut == null || fin == null) return null;
        if (debut.getOffset() > pos || fin.getOffset() < pos) return null;
        if (noeud.getNodeType() == Node.TEXT_NODE) return ("texte");
        if (!(noeud.getNodeType() == Node.ELEMENT_NODE || noeud.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE))
                return (null);
        Element el = (Element) noeud;
        String nomel = el.getTagName();
        if (getParent() != null) nomel += "[" + nombreXPath() + "]";
        if (this instanceof JEStyle) {
            Iterator it = ((JEStyle)this)._styles.iterator();
            nomel = "";
            while (it.hasNext()) {
                Node n = (Node)it.next();
                if (getParent() != null) nomel += n.getNodeName() + "[" + nombreXPath() + "]";
                if (it.hasNext()) nomel += "/"; else el = (Element) n;
            }
        }
        for (Node n = el.getFirstChild(); n != null; n = n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE
                    || n.getNodeType() == Node.TEXT_NODE
                    || n.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                JaxeElement je = doc.getElementForNode(n);
                if (je != null) {
                    String chemin = je.cheminA(pos);
                    if (chemin != null) return (nomel + "/" + chemin);
                }
            }
        }
        return (nomel);
    }

    /**
     * Renvoit le premier élément enfant de celui-ci dont la position est pos ou
     * après pos
     */
    public JaxeElement enfantApres(int pos) {
        if (debut == null || fin == null) return null;
        if (debut.getOffset() > pos || fin.getOffset() < pos) return null;
        for (Node n = noeud.getFirstChild(); n != null; n = n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE
                    || n.getNodeType() == Node.TEXT_NODE
                    || n.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                JaxeElement je = doc.getElementForNode(n);
                if (je != null) {
                    if (je.debut.getOffset() == pos) return (je);
                    JaxeElement nje = je.elementA(pos);
                    if (nje != null && n.getNextSibling() != null) { return (doc
                            .getElementForNode(n.getNextSibling())); }
                }
            }
        }
        return null;
    }

    /**
     * appelé juste avant que l'élément soit effacé
     */
    public void effacer() {
        for (Node n = noeud.getFirstChild(); n != null; n = n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE
                    || n.getNodeType() == Node.TEXT_NODE
                    || n.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                JaxeElement je = doc.getElementForNode(n);
                if (je != null) je.effacer();
            }
        }
        Iterator it = jcomps.iterator();
        while (it.hasNext()) {
            Object o = it.next();
            if (o instanceof JComponent && ((JComponent) o).getParent() != null) {
                JComponent comp = (JComponent) o;
                comp.getParent().remove(comp);
            }
        }
        jcomps = new ArrayList();
        compos = new ArrayList();
    }

    /**
     * met à jour l'arbre de JaxeElement et l'arbre DOM à partir de modifs de la
     * zone de texte
     */
    public void mettreAJourDOM() {
        if (debut == null || fin == null) return;
        try {
            if (noeud.getNodeType() == Node.TEXT_NODE
                    || this instanceof JEStyle) {
                Node nsuivant = noeud.getNextSibling();
                if (nsuivant != null) {
                    JaxeElement jesuivant = doc.getElementForNode(nsuivant);
                    if (jesuivant != null
                            && jesuivant.debut.getOffset() > fin.getOffset() + 1) {
                        // texte rajouté à la fin, avant un autre élément
                        fin = doc
                                .createPosition(jesuivant.debut.getOffset() - 1);
                    }
                }
                String texte = doc.getText(debut.getOffset(), fin.getOffset()
                        - debut.getOffset() + 1);
                if (texte == null || "".equals(texte))
                    getParent().supprimerEnfant(this);
                else {
                    if (noeud.getNodeType() == Node.TEXT_NODE)
                        noeud.setNodeValue(texte);
                    else {
                        Node n = noeud;
                        while (n != null && n.getNodeType() != Node.TEXT_NODE) {
                            n = n.getFirstChild();
                        }
                        if (n != null) n.setNodeValue(texte);
                    }
                }
            } else {
                int offdebut = debut.getOffset();
                int debuttexte;
                if (this instanceof JESwing)
                    debuttexte = offdebut;
                else
                    debuttexte = offdebut + 1;
                for (Node n = noeud.getFirstChild(); n != null; n = n
                        .getNextSibling()) {
                    JaxeElement je = doc.getElementForNode(n);
                    if (je != null) {
                        if (debuttexte < je.debut.getOffset()) {
                            JaxeElement jeprev = null;
                            if (n.getPreviousSibling() != null)
                                    jeprev = doc.getElementForNode(n
                                            .getPreviousSibling());
                            if (jeprev != null
                                    && (jeprev instanceof JEStyle || jeprev instanceof JETexte)) {
                                // texte ajouté à la fin du précédent noeud
                                jeprev.fin = doc.createPosition(je.debut
                                        .getOffset() - 1);
                            } else if (je instanceof JETexte)
                                // texte ajouté au début
                                je.debut = doc.createPosition(debuttexte);
                            else {
                                // nouvelle zone de texte avant ce noeud
                                String texte = doc.getText(debuttexte, je.debut
                                        .getOffset()
                                        - debuttexte);
                                JETexte newje = JETexte.nouveau(doc, doc
                                        .createPosition(debuttexte),
                                        doc
                                                .createPosition(je.debut
                                                        .getOffset() - 1),
                                        texte);
                                noeud.insertBefore(newje.noeud, n);
                            }
                        }
                        offdebut = je.fin.getOffset();
                        debuttexte = offdebut + 1;
                    }
                }
                if (debuttexte < fin.getOffset()) { // texte à la fin, après le
                    // dernier enfant
                    JaxeElement pje = null;
                    if (noeud.getLastChild() != null)
                            pje = doc.getElementForNode(noeud.getLastChild());
                    if (pje instanceof JEStyle || pje instanceof JETexte)
                        // texte ajouté à la fin du dernier enfant
                        pje.fin = doc.createPosition(fin.getOffset() - 1);
                    else {
                        // nouvelle zone de texte à la fin
                        String texte = doc.getText(debuttexte, fin.getOffset()
                                - debuttexte);
                        JETexte newje = JETexte.nouveau(doc, doc
                                .createPosition(debuttexte), doc
                                .createPosition(fin.getOffset() - 1), texte);
                        noeud.appendChild(newje.noeud);
                    }
                }
                for (Node n = noeud.getFirstChild(); n != null; n = n
                        .getNextSibling()) {
                    JaxeElement je = doc.getElementForNode(n);
                    if (je != null) je.mettreAJourDOM();
                }
            }
        } catch (BadLocationException ex) {
            ex.printStackTrace();
            System.err.println("mettreAJourDOM: BadLocationException: "
                    + ex.getMessage());
        }
    }

    /**
     * nouvel élément DOM. Attention: ambiguë quand des espaces de noms sont
     * employés -> utiliser nouvelElementDOM(JaxeDocument doc, Element
     * defbalise) à la place.
     */
    public static Node nouvelElementDOM(JaxeDocument doc, String type,
            String nombalise) {
        Node newel;
        if (type.equals("instruction")) {
            newel = doc.DOMdoc.createProcessingInstruction(nombalise, "");
        } else {
            if (doc.cfg.namespace() == null)
                newel = doc.DOMdoc.createElement(nombalise);
            else {
                Config conf = doc.cfg.getBaliseConf(nombalise);
                if (conf == null) conf = doc.cfg;
                if (conf.prefixe() != null)
                        nombalise = conf.prefixe() + ":" + nombalise;
                newel = doc.DOMdoc.createElementNS(conf.namespace(), nombalise);
            }
        }
        return (newel);
    }

    /**
     * nouvel élément DOM. Espace de noms obtenu à partir de la définition de la
     * configuration.
     */
    public static Element nouvelElementDOM(JaxeDocument doc, Element defbalise) {
        Element newel;
        String nombalise = doc.cfg.nomBalise(defbalise);
        Config conf = doc.cfg.getDefConf(defbalise);
        if (conf == null) conf = doc.cfg;
        if (conf.namespace() == null)
            newel = doc.DOMdoc.createElement(nombalise);
        else {
            if (conf.prefixe() != null)
                    nombalise = conf.prefixe() + ":" + nombalise;
            newel = doc.DOMdoc.createElementNS(conf.namespace(), nombalise);
        }
        return (newel);
    }

    /**
     * nouvel élément DOM à partir Espace de noms obtenu à partir de l'élément
     * parent
     */
    public static Element nouvelElementDOM(JaxeDocument doc, String balise,
            Element parent) {
        String ns = parent.getNamespaceURI();
        String prefixe = parent.getPrefix();
        String balise2;
        if (prefixe != null)
            balise2 = prefixe + ':' + balise;
        else
            balise2 = balise;
        return (doc.DOMdoc.createElementNS(ns, balise2));
    }

    /**
     * initialise et insère cet élément dans le texte et l'arbre DOM
     */
    public void inserer(Position pos, Node newel) {
        doc.textPane.debutIgnorerEdition();
        insererDOM(pos, newel);
        creer(pos, newel);
        doc.textPane.finIgnorerEdition();

        // JESwing: mise à jour du début des parents
        JaxeElement jeparent = getParent();
        while (jeparent instanceof JESwing
                && jeparent.debut.getOffset() > debut.getOffset()) {
            jeparent.debut = debut;
            jeparent = jeparent.getParent();
        }
    }

    /**
     * insère newel dans l'arbre DOM
     */
    public void insererDOM(Position pos, Node newel) {
        JaxeElement parent = doc.rootJE.elementA(pos.getOffset());
        if (parent.debut.getOffset() == pos.getOffset()
                && !(parent instanceof JESwing)) parent = parent.getParent();
        if (parent instanceof JETexte) {
            int ic = pos.getOffset() - parent.debut.getOffset();
            if (ic > 0) {
                // nouvelle zone de texte... à revoir
                /*
                 * String s = parent.noeud.getNodeValue(); String s1 =
                 * s.substring(0, ic); String s2 = s.substring(ic);
                 * parent.noeud.setNodeValue(s2); Node ns1 =
                 * doc.DOMdoc.createTextNode(s1); Node parent2 =
                 * parent.noeud.getParentNode(); parent2.insertBefore(ns1,
                 * parent.noeud); parent2.insertBefore(newel, parent.noeud);
                 */
                JaxeElement je2 = parent.couper(pos);
                Node parent2 = parent.noeud.getParentNode();
                parent2.insertBefore(newel, je2.noeud);
            } else {
                Node parent2 = parent.noeud.getParentNode();
                parent2.insertBefore(newel, parent.noeud);
            }
        } else {
            JaxeElement jelbef = parent.enfantApres(pos.getOffset());
            if (jelbef == null)
                parent.noeud.appendChild(newel);
            else
                parent.noeud.insertBefore(newel, jelbef.noeud);
        }
    }

    /**
     * creer les enfants de ce noeud, en supposant que c'est un élément DOM
     */
    public void creerEnfants(Position newpos) {
        Element el = (Element) noeud;
        for (Node n = el.getFirstChild(); n != null; n = n.getNextSibling())
            n = creerEnfant(newpos, n);
    }

    /**
     * creer l'enfant n à la position newpos (avec JaxeElement.creer)
     */
    public Node creerEnfant(Position newpos, Node n) {
        int offsetdebut = newpos.getOffset();
        if (n.getNodeType() == Node.ELEMENT_NODE) {
            Element bdef = null;
            if (doc.cfg != null) bdef = doc.cfg.getElementDef((Element) n);
            if (bdef == null) {
                JEInconnu newje = new JEInconnu(doc);
                newje.creer(newpos, (Element) n);
            } else {
                String typebalise = bdef.getAttribute("type");
                if (typebalise.equals("style") && !hasText(n)
                        && !hasProcessing(n)) {
                    // on ne crée pas de JEStyle vide, sinon debut = fin = null
                    // -> pb
                } else if (typebalise.equals("style") && hasProcessing(n)) {
                    Node prev = n.getPreviousSibling();
                    Node parent = n.getParentNode();
                    ProcessingInstruction p = getProcessing(n);
                    n.getParentNode().replaceChild(p, n);
                    if (prev == null) {
                        n = parent.getFirstChild();
                    } else {
                        n = prev.getNextSibling();
                    }
                    if (doc.cfg != null)
                            bdef = doc.cfg
                                    .getProcessingDef((ProcessingInstruction) n);
                    if (bdef == null) {
                        JESauf newje = new JESauf(doc);
                        newje.creer(newpos, (ProcessingInstruction) n);
                    } else {
                        String typebalise2 = bdef.getAttribute("type");
                        JaxeElement newje;
                        JaxeElement oldje = doc.getElementForNode(n);
                        if (oldje != null) {
                            // il existe déjà un JaxeElement pour ce noeud, on
                            // va le
                            // réutiliser
                            // (il est peut-être pointé par un JaxeUndoableEdit)
                            newje = oldje;
                            newje.debut = null;
                            newje.fin = null;
                            newje.jcomps = new ArrayList();
                            newje.compos = new ArrayList();
                        } else
                            newje = JEFactory.createJE(typebalise2, doc, bdef,
                                    (ProcessingInstruction) n);
                        newje.creer(newpos, (ProcessingInstruction) n);
                    }
                } else {
                    JaxeElement newje;
                    JaxeElement oldje = doc.getElementForNode(n);
                    if (oldje != null) {
                        // il existe déjà un JaxeElement pour ce noeud, on va le
                        // réutiliser
                        // (il est peut-être pointé par un JaxeUndoableEdit)
                        newje = oldje;
                        newje.debut = null;
                        newje.fin = null;
                        newje.jcomps = new ArrayList();
                        newje.compos = new ArrayList();
                    } else
                        newje = JEFactory.createJE(typebalise, doc, bdef,
                                (Element) n);
                    newje.creer(newpos, (Element) n);
                }
            }
        } else if (n.getNodeType() == Node.TEXT_NODE) {
            JETexte newje = new JETexte(doc);
            newje.creer(newpos, n);
        } else if (n.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
            Element bdef = null;
            if (doc.cfg != null)
                    bdef = doc.cfg.getProcessingDef((ProcessingInstruction) n);
            if (bdef == null) {
                JESauf newje = new JESauf(doc);
                newje.creer(newpos, (ProcessingInstruction) n);
            } else {
                String typebalise = bdef.getAttribute("type");
                JaxeElement newje;
                JaxeElement oldje = doc.getElementForNode(n);
                if (oldje != null) {
                    // il existe déjà un JaxeElement pour ce noeud, on va le
                    // réutiliser
                    // (il est peut-être pointé par un JaxeUndoableEdit)
                    newje = oldje;
                    newje.debut = null;
                    newje.fin = null;
                    newje.jcomps = new ArrayList();
                    newje.compos = new ArrayList();
                } else
                    newje = JEFactory.createJE(typebalise, doc, bdef,
                            (ProcessingInstruction) n);
                newje.creer(newpos, (ProcessingInstruction) n);
            }
        }
        try {
            if (debut == null) debut = doc.createPosition(offsetdebut);
            if (newpos.getOffset() == 0) // bug fix with insertString
                    newpos = doc.createPosition(1);
            fin = doc.createPosition(newpos.getOffset() - 1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        return n;
    }

    /**
     * position pour setCaretPosition après création d'un nouvel élément
     */
    public Position insPosition() {
        try {
            Position p = doc.createPosition(fin.getOffset() + 1);
            return (p);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return (debut);
        }
    }

    public boolean hasText(Node n) {
        boolean result = false;
        Node child = n.getFirstChild();
        if (child != null) {
            if (child.getNodeType() == Node.TEXT_NODE) {
                result = true;
            } else {
                result = hasText(child);
            }
        }
        return result;
    }

    public boolean hasProcessing(Node n) {
        boolean result = false;
        Node child = n.getFirstChild();
        if (child != null) {
            if (child.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                result = true;
            } else {
                result = hasProcessing(child);
            }
        }
        return result;
    }

    public ProcessingInstruction getProcessing(Node n) {
        ProcessingInstruction result = null;
        Node child = n.getFirstChild();
        if (child != null) {
            if (child.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                result = (ProcessingInstruction) child;
            } else {
                result = getProcessing(child);
            }
        }
        return result;
    }

    /**
     * Renvoit l'élément parent, en utilisant l'arbre DOM
     */
    public JaxeElement getParent() {
        Node parent = noeud.getParentNode();
        if (parent == null) return null;
        return (doc.getElementForNode(parent));
    }

    /**
     * Renvoit le premier élément enfant (ou null)
     */
    public JaxeElement getFirstChild() {
        Node n = noeud.getFirstChild();
        if (n == null) return (null);
        return (doc.getElementForNode(n));
    }

    /**
     * Renvoit l'enfant suivant (ou null)
     */
    public JaxeElement getNextSibling() {
        Node n = noeud.getNextSibling();
        if (n == null) return (null);
        return (doc.getElementForNode(n));
    }

    /**
     * supprime l'enfant je à la fois dans le texte et dans le DOM
     */
    public void supprimerEnfant(JaxeElement je) {
        supprimerEnfantDOM(je); // placé avant doc.remove à cause de caretUpdate

        try {
            int len = je.fin.getOffset() - je.debut.getOffset() + 1;
            /*
             * String cfin = doc.getText(je.fin.getOffset() + 1, 1); if
             * (newline.equals(cfin)) len++;
             */
            int idebut = je.debut.getOffset();
            /*
             * javax.swing.text.Element pel = doc.getParagraphElement(idebut -
             * 1); javax.swing.text.Element pel2 =
             * doc.getParagraphElement(idebut); if (pel2 != pel) { AttributeSet
             * attavant = pel.getAttributes();
             * doc.setParagraphAttributes(pel2.getStartOffset(),
             * pel2.getEndOffset() - pel2.getStartOffset(), attavant, true); }
             */// bug avec jdk 1.4
            doc.remove(idebut, len, false);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
    }

    /**
     * supprime l'enfant je dans le DOM
     */
    public void supprimerEnfantDOM(JaxeElement je) {
        try {
            noeud.removeChild(je.noeud);
        } catch (DOMException ex) {
            System.err.println("DOMException: " + ex.getMessage());
        }
    }

    /**
     * remplace l'enfant je à la fois dans le texte et dans le DOM
     */
    public void remplacerEnfant(JaxeElement je, JaxeElement newje) {
        try {
            doc.remove(je.debut.getOffset(), je.fin.getOffset()
                    - je.debut.getOffset() + 1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        newje.creer(newje.debut, newje.noeud);

        remplacerEnfantDOM(je, newje);
    }

    /**
     * remplace l'enfant je dans le DOM
     */
    public void remplacerEnfantDOM(JaxeElement je, JaxeElement newje) {
        Node parent = je.noeud.getParentNode();
        if (parent == null)
                System.err.println("remplacerEnfantDOM: parent null !");
        try {
            parent.replaceChild(newje.noeud, je.noeud);
        } catch (DOMException ex) {
            System.err.println("DOMException: " + ex.getMessage());
        }
    }

    /**
     * Renvoit la profondeur dans l'arbre XML.
     */
    /*
     * public int profondeur() { JaxeElement p = getParent(); if (p == null)
     * return(0); else return(p.profondeur() + 1); }
     */

    /**
     * Indique si les descendants de l'élément doivent être indentés
     */
    public boolean avecIndentation() {
        return (false);
    }

    /**
     * Renvoit les indentations dans l'arbre XML. 0 pour la racine de l'arbre et
     * JETableTexte.
     */
    public int indentations() {
        JaxeElement p = getParent();
        if (p != null) {
            JaxeElement p2 = p.getParent();
            if (p2 != null) {
                p2 = p2.getParent();
                if (p2 instanceof JETableTexte) return (0);
            }
        }
        if (p == null)
            return (0);
        else if (avecIndentation())
            return (p.indentations() + 1);
        else
            return (p.indentations());
    }

    /**
     * coupe la zone de texte en 2, retourne la nouvelle zone créée après
     * celle-ci
     */
    public JaxeElement couper(Position pos) {
        String t = noeud.getNodeValue();
        String t1 = t.substring(0, pos.getOffset() - debut.getOffset());
        String t2 = t.substring(pos.getOffset() - debut.getOffset());
        noeud.setNodeValue(t1);
        Node textnode2 = doc.DOMdoc.createTextNode(t2);
        Node nextnode = noeud.getNextSibling();
        JaxeElement parent = getParent();
        if (nextnode == null)
            parent.noeud.appendChild(textnode2);
        else
            parent.noeud.insertBefore(textnode2, nextnode);
        JETexte je2 = new JETexte(doc);
        je2.noeud = textnode2;
        je2.doc = parent.doc;
        try {
            je2.debut = doc.createPosition(pos.getOffset());
            je2.fin = fin;
            fin = doc.createPosition(pos.getOffset() - 1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        doc.dom2JaxeElement.put(je2.noeud, je2);
        return (je2);
    }

    /**
     * fusionne cet élément avec celui donné, dans le DOM (aucun changement du
     * texte)
     */
    public void fusionner(JaxeElement el) {
        if (!(this instanceof JETexte && el instanceof JETexte)) return;
        if (noeud.getNextSibling() == el.noeud) {
            String t = el.noeud.getNodeValue();
            noeud.setNodeValue(noeud.getNodeValue() + t);
            fin = el.fin;
            el.getParent().supprimerEnfantDOM(el);
        } else if (el.noeud.getNextSibling() == noeud) {
            String t = el.noeud.getNodeValue();
            noeud.setNodeValue(t + noeud.getNodeValue());
            debut = el.debut;
            el.getParent().supprimerEnfantDOM(el);
        }
    }

    /**
     * regroupe les JETexte dans les enfants
     */
    public void regrouperTextes() {
        for (Node n = noeud.getFirstChild(); n != null; n = n.getNextSibling()) {
            while (n.getNodeType() == Node.TEXT_NODE
                    && n.getNextSibling() != null
                    && n.getNextSibling().getNodeType() == Node.TEXT_NODE) {
                JaxeElement je1 = doc.getElementForNode(n);
                JaxeElement je2 = doc.getElementForNode(n.getNextSibling());
                je1.fusionner(je2);
            }
        }
    }

    public void setEffacementAutorise(boolean autorise) {
        effacementAutorise = autorise;
    }

    public boolean getEffacementAutorise() {
        return (effacementAutorise);
    }

    public void setEditionAutorisee(boolean autorise) {
        editionAutorisee = autorise;
    }

    public boolean getEditionAutorisee() {
        return (editionAutorisee);
    }

    /**
     * Sélection de la zone de texte où se trouve cet élément
     */
    public void selection(boolean select) {
        for (int i = 0; i < jcomps.size(); i++) {
            Object comp = jcomps.get(i);
            if (comp instanceof MonBouton)
                    ((MonBouton) comp).selection(select);
        }
        for (Node n = noeud.getFirstChild(); n != null; n = n.getNextSibling()) {
            JaxeElement je = doc.getElementForNode(n);
            if (je != null) je.selection(select);
        }
    }

    /**
     * MouseListener pour MonBouton: positionner le curseur à droite ou à gauche
     * quand on clique sur un bord
     */
    public class MyMouseListener extends MouseAdapter {

        JaxeElement jei;

        JFrame jframe;

        public MyMouseListener(JaxeElement obj, JFrame jframe) {
            super();
            jei = obj;
            this.jframe = jframe;
        }

        public void mouseClicked(MouseEvent e) {
            if (doc.textPane.isEditable() && jei.getEditionAutorisee()) {
                jei.afficherDialogue(jframe);
            }
        }
    }

    static Font boutonFont = (Font) UIManager.getDefaults().get("Button.font");

    final static Color jauneLeger = new Color(255, 255, 150);

    final static Color rougeFonce = new Color(150, 0, 0);

    final static Color orange = new Color(255, 200, 150);

    final static Color bleuClair = new Color(210, 230, 255);

    final static Color violet = new Color(210, 200, 255);

    static Color[][] couleursButtons = { { jauneLeger, rougeFonce, orange },
            { bleuClair, rougeFonce, violet } };

    /**
     * Défini les ensembles de couleurs à utiliser pour les boutons. Par défaut,
     * {{jauneLeger, rougeFonce, orange}, {bleuClair, rougeFonce, violet}}
     */
    public static void setMonBoutonCouleurs(Color[][] couleurs) {
        couleursButtons = couleurs;
    }

    /**
     * Returns the current Colors of the Buttons
     * 
     * @return Colors of the Buttons
     */
    public static Color[][] getMonBoutonCouleurs() {
        return couleursButtons;
    }

    static Border boutonBorder = BorderFactory.createRaisedBevelBorder();

    /**
     * Sets the Border of the Button
     * 
     * @param border
     *            New Border of the Button
     */
    public static void setMonBoutonBorder(Border border) {
        boutonBorder = border;
    }

    /**
     * Returns the current border of the Buttons
     * 
     * @return Border of the Buttons
     */
    public static Border getMonBoutonBorder() {
        return boutonBorder;
    }

    /**
     * Gets the Font the MonBouton is using
     * 
     * @return Current Font of MonBouton
     */
    public static Font getMonBoutonFont() {
        return boutonFont;
    }

    /**
     * Sets the Font the MonBouton should use
     * 
     * @param font
     *            Font to use
     */
    public static void setMonBoutonFont(Font font) {
        boutonFont = font;
    }

    /**
     * Bouton représentant le début ou la fin d'un élément dans le texte
     */
    public class MonBouton extends JComponent {

        JLabel label;

        boolean valide = true;

        boolean selectionne = false;

        int noens = 0;

        boolean division;

        public MonBouton(String texte, boolean division) {
            this.division = division;
            if (division)
                setLayout(new BorderLayout());
            else
                setLayout(new FlowLayout(FlowLayout.CENTER, 0, 0));
            label = new JLabel(texte);
            label.setForeground(getForeground());
            label.setBackground(getBackground());
            label.setOpaque(true);
            label.setBorder(boutonBorder);
            label.setFont(boutonFont);
            if (division)
                add(label, BorderLayout.CENTER);
            else
                add(label);
            setBorder(BorderFactory.createEmptyBorder(0, 3, 0, 3));
            label.addMouseListener(new MyMouseListener(JaxeElement.this,
                    doc.textPane.jframe));
            setAlignmentY((float)0.6);
        }

        public void setText(String texte) {
            label.setText(texte);
        }

        public void setValidite(boolean valide) {
            this.valide = valide;
            label.setBackground(getBackground());
        }

        public Color getBackground() {
            if (selectionne)
                return (couleursButtons[noens][1]);
            else if (valide)
                return (couleursButtons[noens][0]);
            else
                return (couleursButtons[noens][2]);
        }

        public Color getForeground() {
            if (selectionne)
                return (couleursButtons[noens][0]);
            else
                return (couleursButtons[noens][1]);
        }

        public Dimension getPreferredSize() {
            //if (division) return (super.getPreferredSize());
            Dimension d = label.getMinimumSize();
            d.width += 6;
            return (d);
        }

        public Dimension getMaximumSize() {
            if (division) return (super.getMaximumSize());
            return (getPreferredSize());
        }

        public Dimension getMinimumSize() {
            if (division) return (super.getMinimumSize());
            return (getPreferredSize());
        }

        public void selection(boolean select) {
            selectionne = select;
            label.setForeground(getForeground());
            label.setBackground(getBackground());
        }

        public void setEnsembleCouleurs(int noens) {
            this.noens = noens - (noens / couleursButtons.length)
                    * couleursButtons.length;
            label.setForeground(getForeground());
            label.setBackground(getBackground());
        }
    }

    protected String getString(String key) {
        return (JaxeResourceBundle.getRB().getString(key));
    }

    public void changerStyle(String style, int offset, int longueur) {
        if (style != null) {

            String[] styleSplit = splitString(style);

            Style s = doc.textPane.addStyle(null, null);

            for (int i = 0; i < styleSplit.length; i++) {
                if (styleSplit[i].indexOf(kExposant) > -1)
                        StyleConstants.setSuperscript(s, true);
                if (styleSplit[i].indexOf(kIndice) > -1)
                        StyleConstants.setSubscript(s, true);
                if (styleSplit[i].indexOf(kCouleur) > -1)
                        StyleConstants.setForeground(s, obtenezCouleur(
                                styleSplit[i], Color.red));
                if (styleSplit[i].indexOf(kCouleurDeFond) > -1)
                        StyleConstants.setBackground(s, obtenezCouleur(
                                styleSplit[i], Color.green));
                if (styleSplit[i].indexOf(kItalique) > -1)
                        StyleConstants.setItalic(s, true);
                if (styleSplit[i].indexOf(kGras) > -1)
                        StyleConstants.setBold(s, true);
                if (styleSplit[i].indexOf(kSouligne) > -1)
                        StyleConstants.setUnderline(s, true);
                if (styleSplit[i].indexOf(kBarre) > -1)
                        StyleConstants.setStrikeThrough(s, true);
                if (!styleSplit[i].equals("")) {
                    doc.setCharacterAttributes(offset, longueur, s, false);

                }

            }
        }
    }

    private Color obtenezCouleur(String arg, Color result) {
        Pattern p = null;
        try {
            p = perlComp.compile("^.*\\[(x[0-9a-fA-F]{2}|[0-9]{1,3}),(x[0-9a-fA-F]{2}|[0-9]{1,3}),(x[0-9a-fA-F]{2}|[0-9]{1,3})\\]$");
        } catch (MalformedPatternException e2) {
            return result;
        }
        String s = arg;
        if (arg.indexOf(";") > 0) {
            String[] parts = splitString(arg);
            for (int i = 0; i < parts.length; i++) {
                if (parts[i].indexOf(kCouleur) > -1) {
                    s = parts[i];
                }
            }
        }
        if (matcher.matches(s, p)) {
            MatchResult match = matcher.getMatch();
            boolean error = false;
            int[] color = new int[3];
            for (int j = 0; j < 3; j++) {
                String value = match.group(j + 1);
                try {
                    if (value.startsWith("x")) {
                        color[j] = Integer.parseInt(value, 16);
                    } else {
                        color[j] = Integer.parseInt(value);
                    }
                } catch (NumberFormatException e) {
                    color[j] = 0;
                    error = true;
                }
            }
            Color c = new Color(color[0], color[1], color[2]);
            if (!(c.equals(Color.black) && error)) {
                result = c;
            }
        }

        return result;
    }
    
    /**
     * Splits the string by semicolon
     * @param s String to split
     * @return Array with parts
     */
    private String[] splitString(String s) {
        List parts = new ArrayList();
        while (s.indexOf(';') > -1) {
            int index = s.indexOf(';');
            parts.add(s.substring(0, index));
            s = s.substring(index + 1, s.length());
        }
        parts.add(s);
        String[] result = new String[parts.size()];
        for (int i = 0; i < result.length; i++) {
            result[i] = (String) parts.get(i);
        }
        return result;
    }

    public SimpleAttributeSet attStyle(SimpleAttributeSet attorig) {
        SimpleAttributeSet att = attorig;
        Element el;
        if (noeud.getNodeType() == Node.TEXT_NODE
                || noeud.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE)
            el = (Element) getParent().noeud;
        else
            el = (Element) noeud;

        while (el != null && (el.getParentNode() instanceof Element)) {
            Element defbalise = null;
            if (doc.cfg != null) defbalise = doc.cfg.getElementDef(el);
            if (defbalise == null) return (att);
            String style = doc.cfg.getParamFromDefinition(defbalise, "style",
                    null);
            if (this instanceof JEStyle) {
                style = ((JEStyle) this).ceStyle;
            }
            if (style != null) {
                if (att == null) att = new SimpleAttributeSet();

                String[] styleSplit = splitString(style);

                for (int i = 0; i < styleSplit.length; i++) {
                    if (styleSplit[i].indexOf(kExposant) > -1)
                            StyleConstants.setSuperscript(att, true);
                    if (styleSplit[i].indexOf(kIndice) > -1)
                            StyleConstants.setSubscript(att, true);
                    if (styleSplit[i].indexOf(kCouleur) > -1)
                            StyleConstants.setForeground(att, obtenezCouleur(
                                    styleSplit[i], Color.red));
                    if (styleSplit[i].indexOf(kCouleurDeFond) > -1)
                            StyleConstants.setBackground(att, obtenezCouleur(
                                    styleSplit[i], Color.green));
                    if (styleSplit[i].indexOf(kItalique) > -1)
                            StyleConstants.setItalic(att, true);
                    if (styleSplit[i].indexOf(kGras) > -1)
                            StyleConstants.setBold(att, true);
                    if (styleSplit[i].indexOf(kSouligne) > -1)
                            StyleConstants.setUnderline(att, true);
                    if (styleSplit[i].indexOf(kBarre) > -1)
                            StyleConstants.setStrikeThrough(att, true);
                }

            }

            if (att == null || !att.isDefined(StyleConstants.FontFamily)) {
                String police = doc.cfg.getParamFromDefinition(defbalise,
                        "police", null);
                if (police != null) {
                    if (att == null) att = new SimpleAttributeSet();
                    StyleConstants.setFontFamily(att, police);
                }
            }
            if (att == null || !att.isDefined(StyleConstants.FontSize)) {
                String staille = doc.cfg.getParamFromDefinition(defbalise,
                        "taille", null);
                if (staille != null) {
                    try {
                        int taille = Integer.parseInt(staille);
                        if (att == null) att = new SimpleAttributeSet();
                        StyleConstants.setFontSize(att, taille);
                    } catch (NumberFormatException ex) {
                        System.err.println(ex.getClass().getName() + ": "
                                + ex.getMessage());
                    }
                }
            }

            el = (Element) el.getParentNode();
        }
        JaxeElement jp = getParent();
        if (jp != null)
            return (jp.attStyle(att));
        else
            return (att);
    }
}