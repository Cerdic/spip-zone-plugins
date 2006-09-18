/*
 * Jaxe - Editeur XML en Java
 * 
 * Copyright (C) 2002 Observatoire de Paris-Meudon
 * 
 * Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le
 * modifier conformément aux dispositions de la Licence Publique Générale GNU,
 * telle que publiée par la Free Software Foundation ; version 2 de la licence,
 * ou encore (à votre choix) toute version ultérieure.
 * 
 * Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE
 * GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou
 * D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence
 * Publique Générale GNU .
 * 
 * Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en
 * même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free
 * Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
 */

package jaxe;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import javax.swing.text.BadLocationException;
import javax.swing.text.Position;

import jaxe.elements.JEStyle;
import jaxe.elements.JETexte;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

public class FonctionAjStyle implements Fonction {

    public FonctionAjStyle(Element elem) {
        _elem = elem;
    }

    public boolean appliquer(JaxeDocument doc, int start, int end) {
        boolean done = false;
        try {
            JaxeElement firstel = doc.rootJE.elementA(start);
            JaxeElement p1 = firstel;
            if (p1 instanceof JEStyle || p1 instanceof JETexte)
                    p1 = p1.getParent();
            JaxeElement lastel = doc.rootJE.elementA(end - 1);
            JaxeElement p2 = lastel;
            if (p2 instanceof JEStyle || p2 instanceof JETexte)
                    p2 = p2.getParent();
            if (p1 != p2) return true;

            doc.textPane.debutEditionSpeciale(JaxeResourceBundle.getRB()
                    .getString("style.Style"), false);

            Node next = firstel.noeud.getNextSibling();

            if (firstel instanceof JEStyle) {

                if (firstel.debut.getOffset() <= start) {

                    int firsteldebut = firstel.debut.getOffset();
                    int firstelfin = firstel.fin.getOffset();
                    List path = new ArrayList(((JEStyle) firstel)._styles);
                    Iterator it = path.iterator();
                    while (it.hasNext()) {
                        if (((Node) it.next()).getNodeName().equals(
                                _elem.getNodeName())) {
                            done = true;
                            break;
                        }
                    }
                    Element defbalise2 = doc.cfg.getElementDef((Element) _elem);
                    Config conf = doc.cfg.getDefConf(defbalise2);
                    if (conf == null) conf = doc.cfg;
                    String nombalise = doc.cfg.nomBalise(defbalise2);
                    Element parentdef = null;
                    Element parentns = (Element) firstel.noeud;
                    String pns = parentns.getNamespaceURI();
                    String cns = conf.namespace();
                    if ((pns != null || cns != null)
                            && (pns == null || !pns.equals(cns)))
                            parentns = doc.cfg.chercheParentEspace(parentns,
                                    cns);
                    if (parentns != null)
                            parentdef = conf.getElementDef(parentns);
                    if (parentdef != null
                            && conf.sousbalise(parentdef, nombalise)) {

                        String texte0 = ((JEStyle) firstel).getText();

                        String texte1 = texte0.substring(0, start
                                - firsteldebut);
                        String texte2;
                        if (firstelfin >= end)
                            texte2 = texte0.substring(start - firsteldebut, end
                                    - firsteldebut);
                        else
                            texte2 = texte0.substring(start - firsteldebut);
                        String ceStyle = ((JEStyle) firstel).ceStyle;//doc.cfg.getParamFromDefinition(defbalise,
                        // "style",
                        // defbalise.getAttribute("param"));
                        JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                                JaxeUndoableEdit.SUPPRIMER, firstel);
                        jedit.doit();

                        if (firsteldebut < start) {
                            JEStyle newje = new JEStyle(doc);
                            //                        newje.ceStyle = ceStyle;
                            Node newel = doc.DOMdoc.createTextNode(texte1);

                            it = path.iterator();
                            while (it.hasNext()) {
                                Node node = ((Node) it.next()).cloneNode(false);
                                node.appendChild(newel);
                                newel = node;
                            }

                            //Element newel = JaxeElement.nouvelElementDOM(doc,
                            // defbalise);
                            newje.noeud = newel;
                            newje.doc = doc;
                            doc.dom2JaxeElement.put(newel, newje);
                            newje.debut = doc.createPosition(firsteldebut);
                            //newje.fin = doc.createPosition(start-1);
                            newje.fin = null;
                            jedit = new JaxeUndoableEdit(
                                    JaxeUndoableEdit.AJOUTER, newje);
                            jedit.doit();
                        }

                        //JETexte newjetexte = JETexte.nouveau(doc,
                        // doc.createPosition(start), null, texte2);
                        JEStyle newjeF = new JEStyle(doc);

                        String ceStyle2 = doc.cfg.getParamFromDefinition(
                                defbalise2, "style", defbalise2
                                        .getAttribute("param"));
                        //                    newjeF.ceStyle = ceStyle + "," + ceStyle2;

                        //Element newelF = JaxeElement.nouvelElementDOM(doc,
                        // defbalise);
                        //_elem.appendChild(textnodeF);
                        //newelF.appendChild(_elem);

                        Node newelF = doc.DOMdoc.createTextNode(texte2);

                        List nPath = new ArrayList(path);
                        if (!containsNode(nPath, _elem)) nPath.add(0, _elem);
                        it = nPath.iterator();
                        while (it.hasNext()) {
                            Node node = ((Node) it.next()).cloneNode(false);
                            node.appendChild(newelF);
                            newelF = node;
                        }
                        newjeF.noeud = newelF;
                        newjeF.doc = doc;
                        doc.dom2JaxeElement.put(newelF, newjeF);
                        newjeF.debut = doc.createPosition(firsteldebut
                                + texte1.length());
                        newjeF.fin = null;
                        jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
                                newjeF);
                        jedit.doit();

                        if (firstelfin >= end) {
                            String texte3 = texte0
                                    .substring(end - firsteldebut);
                            JEStyle newje = new JEStyle(doc);
                            //                        newje.ceStyle = ceStyle;
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
                            jedit = new JaxeUndoableEdit(
                                    JaxeUndoableEdit.AJOUTER, newje);
                            jedit.doit();
                        }
                        done = true;
                    }
                    //JEStyle newStyle = JEStyle.nouveau(doc, start, end,
                    // null);

                } else {
                    tonormal(firstel, (Element) _elem.cloneNode(false));
                    done = true;
                }
            } else if (firstel instanceof JETexte) {
                if (firstel.debut.getOffset() <= start) {

                    int firsteldebut = firstel.debut.getOffset();
                    int firstelfin = firstel.fin.getOffset();
                    String texte0 = firstel.noeud.getNodeValue();

                    String texte1 = texte0.substring(0, start - firsteldebut);
                    String texte2;
                    if (firstelfin >= end)
                        texte2 = texte0.substring(start - firsteldebut, end
                                - firsteldebut);
                    else
                        texte2 = texte0.substring(start - firsteldebut);
                    // "style",
                    // defbalise.getAttribute("param"));
                    JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                            JaxeUndoableEdit.SUPPRIMER, firstel);
                    jedit.doit();

                    if (firsteldebut < start) {
                        //                        JEStyle newje = new JEStyle(doc);
                        //                        newje.ceStyle = ceStyle;
                        //                        Node newel = doc.DOMdoc.createTextNode(texte1);

                        JETexte newje = JETexte.nouveau(doc, doc
                                .createPosition(firsteldebut), doc
                                .createPosition(start - 1), texte1);

                        //                        it = path.iterator();
                        //                        while (it.hasNext()) {
                        //                            Node node = ((Node) it.next()).cloneNode(false);
                        //                            node.appendChild(newel);
                        //                            newel = node;
                        //                        }

                        //Element newel = JaxeElement.nouvelElementDOM(doc,
                        // defbalise);
                        //                        newje.noeud = newel;
                        //                        newje.doc = doc;
                        //                        doc.dom2JaxeElement.put(newel, newje);
                        //                        newje.debut = doc.createPosition(firsteldebut);
                        //newje.fin = doc.createPosition(start-1);
                        //                        newje.fin = null;
                        jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
                                newje);
                        jedit.doit();
                    }

                    //JETexte newjetexte = JETexte.nouveau(doc,
                    // doc.createPosition(start), null, texte2);
                    JEStyle newjeF = new JEStyle(doc);

                    Element defbalise2 = doc.cfg.getElementDef((Element) _elem);

                    String ceStyle2 = doc.cfg.getParamFromDefinition(
                            defbalise2, "style", defbalise2
                                    .getAttribute("param"));
                    //                    newjeF.ceStyle = ceStyle + "," + ceStyle2;

                    //Element newelF = JaxeElement.nouvelElementDOM(doc,
                    // defbalise);
                    //_elem.appendChild(textnodeF);
                    //newelF.appendChild(_elem);

                    Node newelF = _elem.cloneNode(false);
                    newelF.appendChild(doc.DOMdoc.createTextNode(texte2));

                    //                    List nPath = new ArrayList(path);
                    //                    if (!containsNode(nPath, _elem)) nPath.add(0,_elem);
                    //                    it = nPath.iterator();
                    //                    while (it.hasNext()) {
                    //                        Node node = ((Node) it.next()).cloneNode(false);
                    //                        node.appendChild(newelF);
                    //                        newelF = node;
                    //                    }
                    newjeF.noeud = newelF;
                    newjeF.doc = doc;
                    doc.dom2JaxeElement.put(newelF, newjeF);
                    newjeF.debut = doc.createPosition(firsteldebut
                            + texte1.length());
                    newjeF.fin = null;
                    jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
                            newjeF);
                    jedit.doit();

                    if (firstelfin >= end) {
                        String texte3 = texte0.substring(end - firsteldebut);
                        JETexte newje = JETexte.nouveau(doc, doc
                                .createPosition(end), doc
                                .createPosition(firstelfin), texte1);
                        jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
                                newje);
                        jedit.doit();
                    }
                    done = true;
                    //JEStyle newStyle = JEStyle.nouveau(doc, start, end,
                    // null);

                } else {
                    tonormal(firstel, (Element) _elem.cloneNode(false));
                    done = true;
                }
            }
            if (lastel != firstel) {
                int pos = firstel.fin.getOffset();
                while (next != null && next != lastel.noeud && pos < end) {
                    JaxeElement je = p1.elementA(pos);
                    next = je.noeud.getNextSibling();
                    pos = je.fin.getOffset() + 1;
                    if (je instanceof JEStyle) {
                        JEStyle js = (JEStyle) je;

                        JEStyle newjeF = new JEStyle(doc);

                        Element defbalise = doc.cfg
                                .getElementDef((Element) _elem);

                        Config conf = doc.cfg.getDefConf(defbalise);
                        if (conf == null) conf = doc.cfg;
                        String nombalise = doc.cfg.nomBalise(defbalise);
                        Element parentdef = null;
                        Element parentns = (Element) js.noeud;
                        String pns = parentns.getNamespaceURI();
                        String cns = conf.namespace();
                        if ((pns != null || cns != null)
                                && (pns == null || !pns.equals(cns)))
                                parentns = doc.cfg.chercheParentEspace(
                                        parentns, cns);
                        if (parentns != null)
                                parentdef = conf.getElementDef(parentns);
                        if (parentdef != null
                                && conf.sousbalise(parentdef, nombalise)) {// &&
                            // !(parent
                            // instanceof
                            // JEStyle))
                            // {

                            String ceStyle = doc.cfg.getParamFromDefinition(
                                    defbalise, "style", defbalise
                                            .getAttribute("param"));

                            if (js.ceStyle.indexOf(ceStyle) == -1) {
                                //                            newjeF.ceStyle = js.ceStyle + "," + ceStyle;
                                String text = js.getText();

                                Node newelF = doc.DOMdoc.createTextNode(text);

                                List path = new ArrayList(js._styles);
                                if (!containsNode(path, _elem))
                                        path.add(0, _elem);
                                Iterator it = path.iterator();
                                while (it.hasNext()) {
                                    Node node = ((Node) it.next())
                                            .cloneNode(false);
                                    node.appendChild(newelF);
                                    newelF = node;
                                }

                                newjeF.noeud = newelF;
                                newjeF.doc = doc;
                                doc.dom2JaxeElement.put(newelF, newjeF);
                                newjeF.debut = je.debut;
                                newjeF.fin = je.fin;
                                JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                                        JaxeUndoableEdit.SUPPRIMER, je);
                                jedit.doit();
                                jedit = new JaxeUndoableEdit(
                                        JaxeUndoableEdit.AJOUTER, newjeF);
                                jedit.doit();
                            }
                        }
                    } else if (je instanceof JETexte) {
                        JEStyle newjeF = new JEStyle(doc);

                        Element defbalise = doc.cfg
                                .getElementDef((Element) _elem);

                        String ceStyle = doc.cfg.getParamFromDefinition(
                                defbalise, "style", defbalise
                                        .getAttribute("param"));
                        //                        newjeF.ceStyle = ceStyle;
                        String text = je.noeud.getNodeValue();

                        //Element newelF = JaxeElement.nouvelElementDOM(doc,
                        // defbalise);
                        //_elem.appendChild(textnodeF);
                        //newelF.appendChild(_elem);

                        Node newelF = _elem.cloneNode(false);
                        newelF.appendChild(doc.DOMdoc.createTextNode(text));

                        newjeF.noeud = newelF;
                        newjeF.doc = doc;
                        doc.dom2JaxeElement.put(newelF, newjeF);
                        newjeF.debut = je.debut;
                        newjeF.fin = je.fin;
                        JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                                JaxeUndoableEdit.SUPPRIMER, je);
                        jedit.doit();
                        jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
                                newjeF);
                        jedit.doit();
                    }
                    done = true;
                }
            }
            if (lastel != firstel
                    && (lastel instanceof JETexte || lastel instanceof JEStyle)) {
                if (lastel.fin.getOffset() >= end) {
                    if (lastel instanceof JETexte) {
                        int lasteldebut = lastel.debut.getOffset();
                        int lastelfin = lastel.fin.getOffset();
                        String texte0 = lastel.noeud.getNodeValue();
                        String texte1 = texte0.substring(0, end - lasteldebut);
                        String texte2 = texte0.substring(end - lasteldebut);

                        JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                                JaxeUndoableEdit.SUPPRIMER, lastel);
                        jedit.doit();

                        Element defbalise = doc.cfg.getElementDef(_elem);
                        String ceStyle = defbalise.getAttribute("param");
                        JEStyle newje = new JEStyle(doc);
                        //                        newje.ceStyle = ceStyle;
                        Node textnode = doc.DOMdoc.createTextNode(texte1);
                        Element newel = (Element) _elem.cloneNode(false);
                        newel.appendChild(textnode);
                        newje.noeud = newel;
                        newje.doc = doc;
                        doc.dom2JaxeElement.put(newel, newje);
                        //                        newje.debut = doc.createPosition(end);
                        //                        newje.fin = doc.createPosition(lastelfin);
                        newje.debut = doc.createPosition(lasteldebut);
                        newje.fin = null;
                        //                        newje.fin = doc.createPosition(lasteldebut+);
                        jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
                                newje);
                        jedit.doit();

                        JETexte newjetexte = JETexte.nouveau(doc, doc
                                .createPosition(end), doc
                                .createPosition(lastelfin), texte2);
                        jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
                                newjetexte);
                        jedit.doit();
                        done = true;
                    } else if (lastel instanceof JEStyle) {
                        int lasteldebut = lastel.debut.getOffset();
                        int lastelfin = lastel.fin.getOffset();
                        String texte0 = ((JEStyle) lastel).getText();
                        String texte1 = texte0.substring(0, end - lasteldebut);
                        String texte2 = texte0.substring(end - lasteldebut);
                        List path = ((JEStyle) lastel)._styles;

                        Element defbalise = doc.cfg
                                .getElementDef((Element) _elem);

                        Config conf = doc.cfg.getDefConf(defbalise);
                        if (conf == null) conf = doc.cfg;
                        String nombalise = doc.cfg.nomBalise(defbalise);
                        Element parentdef = null;
                        Element parentns = (Element) lastel.noeud;
                        String pns = parentns.getNamespaceURI();
                        String cns = conf.namespace();
                        if ((pns != null || cns != null)
                                && (pns == null || !pns.equals(cns)))
                                parentns = doc.cfg.chercheParentEspace(
                                        parentns, cns);
                        if (parentns != null)
                                parentdef = conf.getElementDef(parentns);
                        if (parentdef != null
                                && conf.sousbalise(parentdef, nombalise)) {// &&
                            // !(parent
                            // instanceof
                            // JEStyle))
                            // {

                            JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                                    JaxeUndoableEdit.SUPPRIMER, lastel);
                            jedit.doit();

                            String ceStyle = defbalise.getAttribute("param");
                            JEStyle newje = new JEStyle(doc);
                            Node newel = doc.DOMdoc.createTextNode(texte1);
                            List npath = new ArrayList(path);
                            if (!containsNode(npath, _elem))
                                    npath.add(0, _elem);
                            Iterator it = npath.iterator();
                            while (it.hasNext()) {
                                Node node = ((Node) it.next()).cloneNode(false);
                                node.appendChild(newel);
                                newel = node;
                            }
                            newje.noeud = newel;
                            newje.doc = doc;
                            doc.dom2JaxeElement.put(newel, newje);
                            //                        newje.fin = doc.createPosition(lastelfin);
                            newje.debut = doc.createPosition(lasteldebut);
                            newje.fin = null;
                            jedit = new JaxeUndoableEdit(
                                    JaxeUndoableEdit.AJOUTER, newje);
                            jedit.doit();

                            JEStyle newje2 = new JEStyle(doc);
                            Node newel2 = doc.DOMdoc.createTextNode(texte2);
                            it = path.iterator();
                            while (it.hasNext()) {
                                Node node = ((Node) it.next()).cloneNode(false);
                                node.appendChild(newel2);
                                newel2 = node;
                            }
                            newje2.noeud = newel2;
                            newje2.doc = doc;
                            doc.dom2JaxeElement.put(newel2, newje2);
                            //                        newje.fin = doc.createPosition(lastelfin);
                            newje2.debut = doc.createPosition(end);
                            newje2.fin = doc.createPosition(lastelfin);
                            jedit = new JaxeUndoableEdit(
                                    JaxeUndoableEdit.AJOUTER, newje2);
                            jedit.doit();
                            done = true;
                        }
                    }
                } else {
                    tonormal(lastel, (Element) _elem.cloneNode(false));
                    done = true;
                }
            }
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        doc.textPane.finEditionSpeciale();
        return done;
    }

    public void tonormal(JaxeElement je, Element elem) {
        JaxeDocument doc = je.doc;
        Position debut = je.debut;
        Position fin = je.fin;
        if (je instanceof JEStyle) {
            JEStyle js = (JEStyle) je;
            String style = js.ceStyle;
            List styles = js._styles;
            String texte = js.getText();
            JEStyle newje = new JEStyle(doc);

            Element defbalise2 = doc.cfg.getElementDef((Element) elem);

            Config conf = doc.cfg.getDefConf(defbalise2);
            if (conf == null) conf = doc.cfg;
            String nombalise = doc.cfg.nomBalise(defbalise2);
            Element parentdef = null;
            Element parentns = (Element) js.noeud;
            String pns = parentns.getNamespaceURI();
            String cns = conf.namespace();
            if ((pns != null || cns != null)
                    && (pns == null || !pns.equals(cns)))
                    parentns = doc.cfg.chercheParentEspace(parentns, cns);
            if (parentns != null) parentdef = conf.getElementDef(parentns);
            if (parentdef != null && conf.sousbalise(parentdef, nombalise)) {

                String ceStyle2 = doc.cfg.getParamFromDefinition(defbalise2,
                        "style", defbalise2.getAttribute("param"));
                newje.ceStyle = style + "," + ceStyle2;

                Node newel = doc.DOMdoc.createTextNode(texte);

                if (!containsNode(styles, elem)) styles.add(0, elem);
                Iterator it = styles.iterator();
                while (it.hasNext()) {
                    Node node = ((Node) it.next()).cloneNode(false);
                    node.appendChild(newel);
                    newel = node;
                }
                newje.noeud = newel;
                newje.doc = doc;
                doc.dom2JaxeElement.put(newel, newje);
                newje.debut = debut;
                newje.fin = fin;
                JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                        JaxeUndoableEdit.SUPPRIMER, je);
                jedit.doit();
                jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje);
                jedit.doit();
            }

        } else if (je instanceof JETexte) {
            JETexte jt = (JETexte) je;
            String texte = je.noeud.getNodeValue();
            JEStyle newje = new JEStyle(doc);

            Element defbalise2 = doc.cfg.getElementDef((Element) elem);

            newje.ceStyle = doc.cfg.getParamFromDefinition(defbalise2, "style",
                    defbalise2.getAttribute("param"));

            elem.appendChild(doc.DOMdoc.createTextNode(texte));

            newje.noeud = elem;
            newje.doc = doc;
            doc.dom2JaxeElement.put(elem, newje);
            newje.debut = debut;
            newje.fin = fin;
            JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                    JaxeUndoableEdit.SUPPRIMER, je);
            jedit.doit();
            jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje);
            jedit.doit();
        }
    }

    private boolean containsNode(List list, Node node) {
        boolean result = false;
        Iterator it = list.iterator();
        while (it.hasNext()) {
            if (((Node) it.next()).getNodeName().equals(node.getNodeName()))
                    result = true;
        }
        return result;
    }

    private Element _elem;
}