/*
JaxeSPIPApplet - Applet utilisant Jaxe pour éditer un article

Copyright (C) 2006 Observatoire de Paris

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

import java.awt.*;
import java.awt.event.ActionEvent;
import java.io.*;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;

import javax.swing.*;
import javax.swing.event.CaretEvent;
import javax.swing.event.CaretListener;
import javax.swing.text.JTextComponent;
import javax.swing.text.TextAction;
import javax.swing.undo.CannotRedoException;
import javax.swing.undo.UndoManager;

import javax.xml.parsers.*;
import javax.xml.transform.*;
import javax.xml.transform.dom.*;
import javax.xml.transform.stream.*;

import org.xml.sax.*;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import jaxe.ActionInsertionBalise;
import jaxe.Config;
import jaxe.EcouteurMAJ;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeTextPane;
import jaxe.elements.JESwing;

// compilation : javac -target 1.4 -encoding ISO-8859-1 -classpath .:Jaxe.jar -d construction/classes JaxeSPIPApplet.java
public class JaxeSPIPApplet extends JApplet implements EcouteurMAJ {

    private JaxeTextPane textPane = null;
    private String initTexte = null;
    private JaxeDocument jaxeDoc = null;
    private URL urlCfg = null;
    private boolean initfait = false;
    private CaretListenerLabel caretListenerLabel;
    private JMenuBar barreBalises;
    private UndoAction undoAction;
    private RedoAction redoAction;

    public void init() {
        if (initTexte != null)
            initialisation();
        else
            initfait = true;
    }

    public String getTexte() {
        if (jaxeDoc == null)
            return(null);
        else
            return(DOMVersSPIP(jaxeDoc.DOMdoc));
    }

    public void setTexte(String texte) {
        initTexte = texte;
        if (initfait)
            initialisation();
    }
    
    private void initialisation() {
        initTexte = initTexte.replaceAll("\\r", ""); // nettoyage des \r avec IE/Windows, Java 1.4
        String texteXML = SPIPVersXML(initTexte, true);
        Document xmldoc;
        try {
            xmldoc = XMLVersDOM(texteXML);
        } catch (SAXException ex) {
            getContentPane().add(new JLabel("Erreur: le document n'est pas valide: " + ex.getMessage()));
            System.err.println("Erreur: le document n'est pas valide: " + ex.getMessage());
            System.err.println(texteXML);
            validate();
            return;
        }
        try {
            urlCfg = new URL(getDocumentBase(), "SPIP_Jaxe_cfg.xml");
            jaxeDoc = new JaxeDocument();
        } catch (MalformedURLException ex) {
            System.err.println("MalformedURLException: " + ex.getMessage());
            jaxeDoc = null;
        }
        textPane = new JaxeTextPane(jaxeDoc, this);
        jaxeDoc.setDOMDoc(xmldoc, urlCfg.toExternalForm());
        initTexte = null;
        
        getContentPane().setLayout(new BorderLayout());
        
        barreBalises = jaxeDoc.cfg.makeMenus(jaxeDoc);
        
        JMenu editMenu = new JMenu("Edition");
        
        undoAction = new UndoAction();
        JMenuItem miUndo = editMenu.add(undoAction);
        int cmdMenu = Toolkit.getDefaultToolkit().getMenuShortcutKeyMask();
        miUndo.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_Z, cmdMenu));
        redoAction = new RedoAction();
        JMenuItem miRedo = editMenu.add(redoAction);
        miRedo.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_R, cmdMenu));
        editMenu.addSeparator();
        
        editMenu.addSeparator();
        
        JMenuItem miCut = editMenu.add(new ActionCouper());
        miCut.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_X, cmdMenu));
        JMenuItem miCopy = editMenu.add(new ActionCopier());
        miCopy.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_C, cmdMenu));
        JMenuItem miPaste = editMenu.add(new ActionColler());
        miPaste.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_V, cmdMenu));
        JMenuItem miSelectAll = editMenu.add(new ActionToutSelectionner());
        miSelectAll.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_A, cmdMenu));
        
        editMenu.addSeparator();
        
        JMenuItem miFind = editMenu.add(new ActionRechercher());
        miFind.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_F, cmdMenu));
        JMenuItem miAgain = editMenu.add(new ActionSuivant());
        miAgain.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_G, cmdMenu));
        barreBalises.add(editMenu);
        
        if (barreBalises != null)
            getContentPane().add(barreBalises, BorderLayout.NORTH);
        
        JPanel statusPane = new JPanel(new GridLayout(1, 1));
        caretListenerLabel = new CaretListenerLabel(" ", jaxeDoc);
        statusPane.add(caretListenerLabel);
        getContentPane().add(statusPane, BorderLayout.SOUTH);
        
        JScrollPane paneScrollPane = new JScrollPane(textPane);
        paneScrollPane.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_ALWAYS);
        getContentPane().add(paneScrollPane, BorderLayout.CENTER);
        
        textPane.addCaretListener(caretListenerLabel);
        textPane.ajouterEcouteurAnnulation(this);
        
        validate();
    }
    
    /**
     * Transforme un texte XML en document DOM
     */
    public static Document XMLVersDOM(String texte) throws SAXException {
        Document ddoc = null;
        try {
            DocumentBuilder docbuilder = DocumentBuilderFactory.newInstance().newDocumentBuilder();
            ddoc = docbuilder.parse(new InputSource(new StringReader(texte)));
        } catch (ParserConfigurationException ex) {
            System.err.println("XMLVersDOM: ParserConfigurationException: " + ex.getMessage());
        } catch (IOException ex) {
            System.err.println("XMLVersDOM: IOException: " + ex.getMessage());
        }
        return(ddoc);
    }
    
    public static String SPIPVersXML(String texte, boolean avecRacine) {
        StringBuffer buff = new StringBuffer();
        if (avecRacine)
            buff.append("<SPIP>\n");
        char sub1;
        String sub2, sub3, sub4, sub5, sub6, sub7, sub8;
        int taille = texte.length();
        boolean dansTitre = false;
        boolean dansGras = false;
        boolean dansItalique = false;
        boolean dansNote = false;
        boolean dansLien = false;
        boolean dansCibleLien = false;
        boolean ancre = false;
        boolean titreTable = false;
        boolean premiereLigne = false;
        boolean dansListe = false;
        int niveauListe = 0;
        boolean listenum = false;
        boolean dansImg = false;
        boolean dansEmb = false;
        boolean dansDoc = false;
        boolean dansGlossaire = false;
        String affImage = null;
        int indImage=-1;
        for (int i=0; i<taille; i++) {
            sub1 = texte.charAt(i);
            if (i < taille - 1)
                sub2 = texte.substring(i, i+2);
            else
                sub2 = null;
            if (i < taille - 2)
                sub3 = texte.substring(i, i+3);
            else
                sub3 = null;
            if (i < taille - 3)
                sub4 = texte.substring(i, i+4);
            else
                sub4 = null;
            if (i < taille - 4)
                sub5 = texte.substring(i, i+5);
            else
                sub5 = null;
            if (i < taille - 5)
                sub6 = texte.substring(i, i+6);
            else
                sub6 = null;
            if (i < taille - 6)
                sub7 = texte.substring(i, i+7);
            else
                sub7 = null;
            if (i < taille - 7)
                sub8 = texte.substring(i, i+8);
            else
                sub8 = null;
            if ("{{{".equals(sub3)) {
                dansTitre = true;
                buff.append("<INTERTITRE>");
                i += 2;
            } else if ("{{".equals(sub2)) {
                dansGras = true;
                buff.append("<B>");
                i++;
            } else if ('{' == sub1) {
                dansItalique = true;
                buff.append("<I>");
            } else if ("[[".equals(sub2)) {
                dansNote = true;
                if (i < taille - 3 && texte.charAt(i+2) == '<') {
                    int j = i+3;
                    while (j < taille - 2 && texte.charAt(j) != '>' && texte.charAt(j) != ']')
                        j++;
                    if (j == taille - 2 || texte.charAt(j) == ']') {
                        // pas normal -> on n'interprête pas
                        buff.append("<NOTE>");
                        i++;
                    } else {
                        buff.append("<NOTE mention=\"" + texte.substring(i+3, j) + "\">");
                        i = j;
                    }
                } else {
                    buff.append("<NOTE>");
                    i++;
                }
            } else if ("[?".equals(sub2)) {
                dansGlossaire = true;
                buff.append("<GLOSSAIRE>");
                i++;
            } else if ("<IMG".equalsIgnoreCase(sub4)) {
                dansImg = true;
                indImage = i;
                i += 3;
            } else if ("<EMB".equalsIgnoreCase(sub4)) {
                dansEmb = true;
                indImage = i;
                i += 3;
            } else if ("<DOC".equalsIgnoreCase(sub4)) {
                dansDoc = true;
                indImage = i;
                i += 3;
            } else if ('[' == sub1) {
                // la difficulté ici est que le texte sur lequel est le lien est placé dans SPIP avant la cible,
                // et c'est l'inverse en XML
                int j = i+1;
                while (j < taille - 2 && texte.charAt(j) != ']' &&
                        !"->".equals(texte.substring(j, j+2)) &&
                        !"<-".equals(texte.substring(j, j+2)))
                    j++;
                if (j == taille - 2) {
                    // pas normal -> on n'interprête pas
                    buff.append("[");
                } else if ("->".equals(texte.substring(j, j+2))) {
                    int k = j+2;
                    while (k < taille && texte.charAt(k) != ']')
                        k++;
                    if (k == taille) {
                        // pas normal -> on n'interprête pas
                        buff.append("[");
                    } else {
                        String typeetnum = texte.substring(j+2, k);
                        String typelien, numerolien;
                        if (typeetnum.startsWith("rubrique")) {
                            typelien = "rubrique";
                            numerolien = typeetnum.substring(8);
                        } else if (typeetnum.startsWith("rub")) {
                            typelien = "rubrique";
                            numerolien = typeetnum.substring(3);
                        } else if (typeetnum.startsWith("brève")) {
                            typelien = "brève";
                            numerolien = typeetnum.substring(5);
                        } else if (typeetnum.startsWith("br")) {
                            typelien = "brève";
                            numerolien = typeetnum.substring(2);
                        } else if (typeetnum.startsWith("image")) {
                            typelien = "image";
                            numerolien = typeetnum.substring(5);
                        } else if (typeetnum.startsWith("img")) {
                            typelien = "image";
                            numerolien = typeetnum.substring(3);
                        } else if (typeetnum.startsWith("document")) {
                            typelien = "document";
                            numerolien = typeetnum.substring(8);
                        } else if (typeetnum.startsWith("doc")) {
                            typelien = "document";
                            numerolien = typeetnum.substring(3);
                        } else if (typeetnum.startsWith("#")) {
                            typelien = "ancre";
                            numerolien = typeetnum.substring(1);
                        } else if (typeetnum.startsWith("http")) {
                            typelien = "externe";
                            numerolien = typeetnum;
                        } else if (typeetnum.startsWith("mot")) {
                            typelien = "mot-clé";
                            numerolien = typeetnum.substring(3);
                        } else if (typeetnum.startsWith("auteur")) {
                            typelien = "auteur";
                            numerolien = typeetnum.substring(6);
                        } else if (typeetnum.startsWith("aut")) {
                            typelien = "auteur";
                            numerolien = typeetnum.substring(3);
                        } else if (typeetnum.startsWith("site")) {
                            typelien = "site-syndiqué";
                            numerolien = typeetnum.substring(4);
                        } else {
                            typelien = "article";
                            if (typeetnum.startsWith("article"))
                                numerolien = typeetnum.substring(7);
                            else if (typeetnum.startsWith("art"))
                                numerolien = typeetnum.substring(3);
                            else
                                numerolien = typeetnum;
                        }
                        if (numerolien != null)
                            numerolien = numerolien.trim();
                        buff.append("<LIEN type=\"" + typelien + "\" numéro=\"" + numerolien + "\">");
                        dansLien = true;
                        dansCibleLien = false;
                    }
                } else if ("<-".equals(texte.substring(j, j+2))) {
                    buff.append("<ANCRE nom=\"" + texte.substring(i+1, j) + "\"/>");
                    dansLien = true;
                    dansCibleLien = true;
                    ancre = true;
                } else {
                    // pas normal -> on n'interprête pas
                    buff.append("[");
                }
            } else if ("----".equals(sub4)) {
                buff.append("<SEPARATION/>");
                i += 3;
            } else if ("<quote>".equalsIgnoreCase(sub7)) {
                buff.append("<CITATION>");
                i += 6;
            } else if ("<math>".equalsIgnoreCase(sub6)) {
                buff.append("<MATH>");
                i += 5;
            } else if ("<sup>".equalsIgnoreCase(sub5)) {
                buff.append("<SUP>");
                i += 4;
            } else if ("<sub>".equalsIgnoreCase(sub5)) {
                buff.append("<SUB>");
                i += 4;
            } else if ("<cadre>".equalsIgnoreCase(sub7) || "<frame>".equalsIgnoreCase(sub7)) {
                int j = i+7;
                while (j < taille - 7 && !"</cadre>".equalsIgnoreCase(texte.substring(j, j+8))
                         && !"</frame>".equalsIgnoreCase(texte.substring(j, j+8)))
                    j++;
                buff.append("<CADRE>");
                String contenu = texte.substring(i+7, j);
                contenu = contenu.replaceAll("&", "&amp;");
                contenu = contenu.replaceAll("<", "&lt;");
                contenu = contenu.replaceAll(">", "&gt;");
                contenu = contenu.replaceAll("\"", "&quot;");
                buff.append(contenu);
                buff.append("</CADRE>");
                i = j+7;
            } else if ("<code>".equalsIgnoreCase(sub6)) {
                int j = i+6;
                while (j < taille - 6 && !"</code>".equalsIgnoreCase(texte.substring(j, j+7)))
                    j++;
                buff.append("<CODE>");
                String contenu = texte.substring(i+6, j);
                contenu = contenu.replaceAll("&", "&amp;");
                contenu = contenu.replaceAll("<", "&lt;");
                contenu = contenu.replaceAll(">", "&gt;");
                contenu = contenu.replaceAll("\"", "&quot;");
                buff.append(contenu);
                buff.append("</CODE>");
                i = j+6;
            } else if ("<poesie>".equalsIgnoreCase(sub8) || "<poetry>".equalsIgnoreCase(sub8)) {
                buff.append("<POESIE>");
                i += 7;
            } else if ("<table".equalsIgnoreCase(sub6)) {
                int j = i+6;
                String resume = null;
                while (j < taille - 10 && !"summary=\"".equalsIgnoreCase(texte.substring(j, j+9)) &&
                        !"</table>".equalsIgnoreCase(texte.substring(j, j+8)))
                    j++;
                if (j < taille - 10 && "summary=\"".equalsIgnoreCase(texte.substring(j, j+9))) {
                    int k = j+9;
                    while (k < taille - 9 && '"' != texte.charAt(k) &&
                            !"</table>".equalsIgnoreCase(texte.substring(k, k+8)))
                        k++;
                    if ('"' == texte.charAt(k))
                        resume = texte.substring(j+9, k);
                }
                
                j = i+6;
                boolean avecTitre = false;
                while (j < taille - 17 && !"<caption>".equalsIgnoreCase(texte.substring(j, j+9)) &&
                        !"</table>".equalsIgnoreCase(texte.substring(j, j+8)))
                    j++;
                if (j < taille - 17 && "<caption>".equalsIgnoreCase(texte.substring(j, j+9))) {
                    int k = j+9;
                    while (k < taille - 9 && !"</caption>".equalsIgnoreCase(texte.substring(k, k+10)) &&
                            !"</table>".equalsIgnoreCase(texte.substring(k, k+8)))
                        k++;
                    if (k < taille - 9 && "</caption>".equalsIgnoreCase(texte.substring(k, k+10))) {
                        String titre = texte.substring(j+9, k);
                        if (resume != null)
                            buff.append("<TABLE titre=\"" + titre + "\" résumé=\"" + resume + "\">");
                        else
                            buff.append("<TABLE titre=\"" + titre + "\">");
                        i = k+9;
                        avecTitre = true;
                    }
                }
                
                if (!avecTitre) {
                    if (resume != null)
                        buff.append("<TABLE résumé=\"" + resume + "\">");
                    else
                        buff.append("<TABLE>");
                    j = i+6;
                    while (j < taille && texte.charAt(j) != '>')
                        j++;
                    i = j;
                }
            } else if ("<tr".equalsIgnoreCase(sub3)) {
                buff.append("<TR>");
                int j = i+3;
                while (j < taille && texte.charAt(j) != '>')
                    j++;
                i = j;
            } else if ("<td".equalsIgnoreCase(sub3)) {
                if (i >= taille - 3)
                    i += 2;
                else if ('>' == texte.charAt(i+3)) {
                    buff.append("<TD>");
                    i += 3;
                } else if (' ' == texte.charAt(i+3)) {
                    int j = i;
                    while (j < taille && texte.charAt(j) != '>')
                        j++;
                    buff.append("<TD" + texte.substring(i+3, j+1));
                    i = j;
                }
            } else if ("<th".equalsIgnoreCase(sub3)) {
                if (i >= taille - 3)
                    i += 2;
                else if ('>' == texte.charAt(i+3)) {
                    buff.append("<TH>");
                    i += 3;
                } else if (' ' == texte.charAt(i+3)) {
                    int j = i;
                    while (j < taille && texte.charAt(j) != '>')
                        j++;
                    buff.append("<TH" + texte.substring(i+3, j+1));
                    i = j;
                }
            } else if ((i == 0 || texte.charAt(i-1) == '\n') && "-#".equals(sub2)) {
                if (!dansListe) {
                    dansListe = true;
                    listenum = true;
                    buff.append("<LISTENUM>\n<EL>");
                    int j = i + 2;
                    while (j < taille && texte.charAt(j) == '#')
                        j++;
                    int nbetoiles = j - (i + 2);
                    while (nbetoiles > niveauListe) {
                        niveauListe++;
                        buff.append("<LISTENUM>\n<EL>");
                    }
                    i += nbetoiles;
                    if (texte.length() > i+2 && texte.charAt(i+2) == ' ')
                        i++;
                }
                i++;
            } else if ((i == 0 || texte.charAt(i-1) == '\n') && '-' == sub1) {
                if (!dansListe) {
                    dansListe = true;
                    listenum = false;
                    buff.append("<LISTE>\n<EL>");
                    int j = i + 1;
                    while (j < taille && texte.charAt(j) == '*')
                        j++;
                    int nbetoiles = j - (i + 1);
                    while (nbetoiles > niveauListe) {
                        niveauListe++;
                        buff.append("<LISTE>\n<EL>");
                    }
                    i += nbetoiles;
                    if (texte.length() > i+2 && texte.charAt(i+1) == ' ')
                        i++;
                }
            } else if (dansListe && ('\n' == sub1 || i == taille - 1)) {
                if (i == taille - 1)
                    buff.append(sub1);
                String baliseListe;
                String ident;
                char etoile;
                if (listenum) {
                    baliseListe = "LISTENUM";
                    ident = "-#";
                    etoile = '#';
                } else {
                    baliseListe = "LISTE";
                    ident = "-";
                    etoile = '*';
                }
                if (i >= taille - ident.length() || !ident.equals(texte.substring(i+1, i+1+ident.length()))) {
                    while (niveauListe > 0) {
                        buff.append("</EL>\n</" + baliseListe + ">");
                        niveauListe--;
                    }
                    buff.append("</EL>\n</" + baliseListe + ">");
                    dansListe = false;
                } else {
                    int j = i + 1 + ident.length();
                    while (j < taille && texte.charAt(j) == etoile)
                        j++;
                    int nbetoiles = j - (i + 1 + ident.length());
                    if (nbetoiles > niveauListe) {
                        while (nbetoiles > niveauListe) {
                            niveauListe++;
                            buff.append("<" + baliseListe + ">\n<EL>");
                        }
                    } else if (nbetoiles < niveauListe) {
                        buff.append("</EL>\n");
                        while (nbetoiles < niveauListe) {
                            niveauListe--;
                            buff.append("</" + baliseListe + "></EL>\n");
                        }
                        buff.append("<EL>");
                    } else {
                        buff.append("</EL>\n<EL>");
                    }
                    i = i + ident.length() + nbetoiles;
                    if (texte.length() > i+1 && texte.charAt(i+1) == ' ')
                        i++;
                }
            } else if (!dansImg && !dansDoc && !dansEmb && '|' == sub1) {
                // malheur, c'est une table !
                
                // récupération d'un titre et/ou résumé eventuels
                String titre = null;
                String resume = null;
                if (i < taille - 2 && '|' == texte.charAt(i+1)) {
                    int j = i+2;
                    while (j < taille - 2 && !('\n' == texte.charAt(j)) &&
                            !('|' == texte.charAt(j)))
                        j++;
                    if (j < taille - 2 && '|' == texte.charAt(j)) {
                        titre = texte.substring(i+2, j);
                        if ('|' == texte.charAt(j+1)) {
                            i = j+3;
                        } else {
                            int k = j+1;
                            while (k < taille - 1 && !('\n' == texte.charAt(k)) &&
                                    !"||".equalsIgnoreCase(texte.substring(k, k+2)))
                                k++;
                            resume = texte.substring(j+1, k);
                            i = k+3;
                        }
                    }
                }
                
                // on découpe le tableau par cellules et on met tout dans un tableau
                ArrayList tableau = new ArrayList(); // ArrayList de ArrayList de String
                int posligne = i;
                while (texte.length() > posligne && texte.charAt(posligne) == '|') {
                    int indn = texte.substring(posligne).indexOf('\n');
                    if (indn != -1)
                        indn += posligne;
                    while (indn != -1 && texte.length() > indn+3 &&
                            "_ ".equals(texte.substring(indn+1, indn+3))) {
                        int indtmp = indn+3;
                        indn = texte.substring(indtmp).indexOf('\n');
                        if (indn != -1)
                            indn += indtmp;
                    }
                    String ligne;
                    if (indn == -1)
                        ligne = texte.substring(posligne);
                    else
                        ligne = texte.substring(posligne, indn);
                    ArrayList tabligne = new ArrayList();
                    int poscellule = 1;
                    while (ligne.length() > poscellule) {
                        int indp = ligne.substring(poscellule).indexOf('|');
                        String cellule;
                        if (indp == -1)
                            cellule = ligne.substring(poscellule);
                        else {
                            indp += poscellule;
                            cellule = ligne.substring(poscellule, indp);
                        }
                        if (indp != -1)
                            tabligne.add(cellule);
                        if (indp != -1)
                            poscellule = indp + 1;
                        else
                            poscellule = ligne.length();
                    }
                    tableau.add(tabligne);
                    if (indn != -1)
                        posligne = indn + 1;
                    else
                        posligne = texte.length();
                }
                i = posligne - 1;
                
                /*
                // debug
                System.out.println("titre:" + titre);
                System.out.println("resume:" + resume);
                for (int j=0; j<tableau.size(); j++) {
                    ArrayList tabligne = (ArrayList)tableau.get(j);
                    for (int k=0; k<tabligne.size(); k++)
                        System.out.print("#" + (String)tabligne.get(k));
                    System.out.println();
                }
                System.out.println();
                */
                
                // vérif cohérence du nombre de cellules par ligne
                boolean erreurCellules = false;
                int nblignes = tableau.size();
                int nbcols;
                if (nblignes == 0)
                    nbcols = 0;
                else
                    nbcols = ((ArrayList)tableau.get(0)).size();
                for (int j=1; j<nblignes; j++) {
                    ArrayList tabligne = (ArrayList)tableau.get(j);
                    if (tabligne.size() != nbcols) {
                        System.err.println("JaxeSPIPApplet: erreur: nombre de cellules incorrect à la ligne " +
                             (j+1) + " du tableau");
                         erreurCellules = true;
                     }
                }
                
                // calcul des rowspans / colspans
                int[][] colspans = new int[nblignes][nbcols];
                int[][] rowspans = new int[nblignes][nbcols];
                if (!erreurCellules) {
                    for (int j=0; j<nblignes; j++)
                        for (int k=0; k<nbcols; k++) {
                            int colspan = 1;
                            ArrayList tabligne = (ArrayList)tableau.get(j);
                            for (int l=k+1; l<nbcols; l++) {
                                String cellule = (String)tabligne.get(l);
                                if ("<".equals(cellule))
                                    colspan++;
                                else
                                    break;
                            }
                            colspans[j][k] = colspan;
                            int rowspan = 1;
                            for (int l=j+1; l<nblignes; l++) {
                                String cellule = (String)((ArrayList)tableau.get(l)).get(k);
                                if ("^".equals(cellule))
                                    rowspan++;
                                else
                                    break;
                            }
                            rowspans[j][k] = rowspan;
                        }
                }
                
                // écriture dans le buffer
                if (titre != null && resume != null)
                    buff.append("<TABLE titre=\"" + titre + "\" résumé=\"" + resume + "\">\n");
                else if (titre != null)
                    buff.append("<TABLE titre=\"" + titre + "\">\n");
                else
                    buff.append("<TABLE>\n");
                for (int j=0; j<tableau.size(); j++) {
                    ArrayList tabligne = (ArrayList)tableau.get(j);
                    buff.append("<TR>");
                    for (int k=0; k<tabligne.size(); k++) {
                        String cellule = (String)tabligne.get(k);
                        if (!erreurCellules && ("<".equals(cellule) || "^".equals(cellule)))
                            continue;
                        String celltrim = cellule.trim();
                        boolean entete = (j == 0 && celltrim.startsWith("{{") && celltrim.endsWith("}}"));
                        if (entete)
                            cellule = celltrim.substring(2, celltrim.length()-2);
                        if (entete)
                            buff.append("<TH");
                        else
                            buff.append("<TD");
                        if (!erreurCellules && rowspans[j][k] > 1)
                            buff.append(" rowspan=\"" + rowspans[j][k] + "\"");
                        if (!erreurCellules && colspans[j][k] > 1)
                            buff.append(" colspan=\"" + colspans[j][k] + "\"");
                        buff.append(">");
                        // appel récursif pour le contenu de la cellule
                        buff.append(SPIPVersXML(cellule, false));
                        if (entete)
                            buff.append("</TH>");
                        else
                            buff.append("</TD>");
                    }
                    buff.append("</TR>\n");
                }
                buff.append("</TABLE>");
            } else if ("_ ".equals(sub2)) {
                buff.append("<BR/>");
                i++;
            } else if ((dansImg || dansEmb || dansDoc) && '>' == sub1) {
                String subImage = texte.substring(indImage+4, i);
                int indp = subImage.indexOf('|');
                String numero;
                if (indp != -1)
                    numero = subImage.substring(0, indp);
                else
                    numero = subImage;
                String position;
                if (indp != -1) {
                    position = subImage.substring(indp + 1);
                    if ("left".equals(position))
                        position = "gauche";
                    else if ("center".equals(position))
                        position = "milieu";
                    else if ("right".equals(position))
                        position = "droite";
                } else
                    position = "danstexte";
                if (dansImg) {
                    buff.append("<IMAGE numéro=\"" + numero + "\" position=\"" + position + "\"/>");
                    dansImg = false;
                } else if (dansDoc) {
                    buff.append("<DOCUMENT numéro=\"" + numero + "\" position=\"" + position + "\"/>");
                    dansDoc = false;
                } else {
                    buff.append("<EMBED numéro=\"" + numero + "\" position=\"" + position + "\"/>");
                    dansEmb = false;
                }
            } else if (dansTitre && "}}}".equals(sub3)) {
                dansTitre = false;
                buff.append("</INTERTITRE>");
                i += 2;
            } else if (dansGras && "}}".equals(sub2)) {
                dansGras = false;
                buff.append("</B>");
                i++;
            } else if (dansItalique && '}' == sub1) {
                dansItalique = false;
                buff.append("</I>");
            } else if (dansNote && "]]".equals(sub2)) {
                dansNote = false;
                buff.append("</NOTE>");
                i++;
            } else if (dansGlossaire && ']' == sub1) {
                dansGlossaire = false;
                buff.append("</GLOSSAIRE>");
            } else if (dansLien && ']' == sub1) {
                if (!ancre)
                    buff.append("</LIEN>");
                dansLien = false;
                dansCibleLien = false;
            } else if (dansLien && "->".equals(sub2)) {
                ancre = false;
                dansCibleLien = true;
                i++;
            } else if (dansLien && "<-".equals(sub2)) {
                ancre = true;
                dansCibleLien = true;
                i++;
            } else if ("</quote>".equalsIgnoreCase(sub8)) {
                buff.append("</CITATION>");
                i += 7;
            } else if ("</math>".equalsIgnoreCase(sub7)) {
                buff.append("</MATH>");
                i += 6;
            } else if ("</sup>".equalsIgnoreCase(sub6)) {
                buff.append("</SUP>");
                i += 5;
            } else if ("</sub>".equalsIgnoreCase(sub6)) {
                buff.append("</SUB>");
                i += 5;
            } else if (i < taille - 8 && ("</poesie>".equalsIgnoreCase(texte.substring(i, i+9)) ||
                    "</poetry>".equalsIgnoreCase(texte.substring(i, i+9)))) {
                buff.append("</POESIE>");
                i += 8;
            } else if ("</table>".equalsIgnoreCase(sub8)) {
                buff.append("</TABLE>");
                i += 7;
            } else if ("</tr>".equalsIgnoreCase(sub5)) {
                buff.append("</TR>");
                i += 4;
            } else if ("</td>".equalsIgnoreCase(sub5)) {
                buff.append("</TD>");
                i += 4;
            } else if ("</th>".equalsIgnoreCase(sub5)) {
                buff.append("</TH>");
                i += 4;
            } else if ('<' == sub1)
                buff.append("&lt;");
            else if ('>' == sub1)
                buff.append("&gt;");
            else if ('&' == sub1)
                buff.append("&amp;");
            else if ('"' == sub1)
                buff.append("&quot;");
            else if (!dansCibleLien && !dansImg && !dansDoc && !dansEmb)
                buff.append(sub1);
        }
        if (avecRacine)
            buff.append("\n</SPIP>");
        //System.out.println(buff);
        return(buff.toString());
    }
    
    /*
    public static String DOMVersXML(Document xmldoc) {
        try {
            DOMSource domSource = new DOMSource(xmldoc);
            StringWriter sw = new StringWriter();
            StreamResult streamResult = new StreamResult(sw);
            TransformerFactory tf = TransformerFactory.newInstance();
            Transformer serializer = tf.newTransformer();
            serializer.setOutputProperty(OutputKeys.ENCODING,"ISO-8859-1");
            serializer.setOutputProperty(OutputKeys.INDENT,"no");
            serializer.transform(domSource, streamResult);
            return(sw.toString());
        } catch (TransformerConfigurationException ex) {
            System.err.println("DOMVersXML: TransformerConfigurationException: " + ex.getMessage());
            return(null);
        } catch (TransformerException ex) {
            System.err.println("DOMVersXML: TransformerException: " + ex.getMessage());
            return(null);
        }
    }
    */
    
    private static void transfo(Element el, StringBuffer buff, boolean dansCellule, boolean tableHTML) {
        String nom = el.getNodeName();
        if ("INTERTITRE".equals(nom))
            buff.append("{{{");
        else if ("B".equals(nom)) {
            if (buff.length() > 0 && buff.charAt(buff.length() - 1) == '{')
                buff.append(' ');
            buff.append("{{");
        } else if ("I".equals(nom)) {
            if (buff.length() > 0 && buff.charAt(buff.length() - 1) == '{')
                buff.append(' ');
            buff.append('{');
        } else if ("LIEN".equals(nom))
            buff.append('[');
        else if ("ANCRE".equals(nom))
            buff.append('[');
        else if ("EL".equals(nom)) {
            if (buff.length() > 0 && buff.charAt(buff.length() - 1) != '\n')
                buff.append('\n');
            Node enfant1 = el.getFirstChild();
            Element p = (Element)el.getParentNode();
            String baliseListe = p.getNodeName();
            if (enfant1 == null || !baliseListe.equals(enfant1.getNodeName())) {
                buff.append('-');
                int profondeur = 0;
                char etoile;
                if ("LISTENUM".equals(baliseListe)) {
                    etoile = '#';
                    profondeur = 1;
                } else
                    etoile = '*';
                while (p != null) {
                    if (baliseListe.equals(p.getNodeName()))
                        profondeur++;
                    if (p.getParentNode() instanceof Element)
                        p = (Element)p.getParentNode();
                    else
                        p = null;
                }
                for (int i=0; i<profondeur-1; i++)
                    buff.append(etoile);
                buff.append(' ');
            }
        } else if ("NOTE".equals(nom)) {
            if ("".equals(el.getAttribute("mention")))
                buff.append("[[");
            else
                buff.append("[[<" + el.getAttribute("mention") + ">");
        } else if ("IMAGE".equals(nom)) {
            String numero = el.getAttribute("numéro");
            String position = el.getAttribute("position");
            if ("".equals(position) || "gauche".equals(position))
                position = "left";
            else if ("milieu".equals(position))
                position = "center";
            else if ("droite".equals(position))
                position = "right";
            if ("danstexte".equals(position))
                buff.append("<IMG" + numero + ">");
            else
                buff.append("<IMG" + numero + "|" + position + ">");
        } else if ("DOCUMENT".equals(nom)) {
            String numero = el.getAttribute("numéro");
            String position = el.getAttribute("position");
            if ("".equals(position) || "gauche".equals(position))
                position = "left";
            else if ("milieu".equals(position))
                position = "center";
            else if ("droite".equals(position))
                position = "right";
            if ("danstexte".equals(position))
                buff.append("<DOC" + numero + ">");
            else
                buff.append("<DOC" + numero + "|" + position + ">");
        } else if ("EMBED".equals(nom)) {
            String numero = el.getAttribute("numéro");
            String position = el.getAttribute("position");
            if ("".equals(position) || "gauche".equals(position))
                position = "left";
            else if ("milieu".equals(position))
                position = "center";
            else if ("droite".equals(position))
                position = "right";
            if ("danstexte".equals(position))
                buff.append("<EMB" + numero + ">");
            else
                buff.append("<EMB" + numero + "|" + position + ">");
        } else if ("TABLE".equals(nom)) {
            if (buff.length() > 0 && buff.charAt(buff.length() - 1) != '\n')
                buff.append('\n');
            tableHTML = false;
            for (Node n = el.getFirstChild(); n != null && !tableHTML; n = n.getNextSibling()) {
                if (n.getNodeType() == Node.ELEMENT_NODE && "TR".equals(n.getNodeName())) {
                    Element tr = (Element)n;
                    for (Node n2 = tr.getFirstChild(); n2 != null && !tableHTML; n2 = n2.getNextSibling()) {
                        if (n2.getNodeType() == Node.ELEMENT_NODE &&
                                ("TD".equals(n2.getNodeName()) || "TH".equals(n2.getNodeName()))) {
                            Element td = (Element)n2;
                            // avant SPIP 1.9 on testait aussi colspan et rowspan
                            if (!"".equals(td.getAttribute("align")))
                                tableHTML = true;
                        }
                    }
                }
            }
            if (tableHTML) {
                if ("".equals(el.getAttribute("résumé")))
                    buff.append("<table class=\"spip\">\n");
                else
                    buff.append("<table class=\"spip\" summary=\"" + el.getAttribute("résumé") + "\">\n");
                if (!"".equals(el.getAttribute("titre")))
                    buff.append("<caption>" + el.getAttribute("titre") + "</caption>\n");
            } else {
                if (!"".equals(el.getAttribute("titre"))) {
                    if ("".equals(el.getAttribute("résumé")))
                        buff.append("||" + el.getAttribute("titre") + "||\n");
                    else
                        buff.append("||" + el.getAttribute("titre") + "|" + el.getAttribute("résumé") + "||\n");
                }
                int nblignes = 0;
                Element premiereLigne = null;
                for (Node n = el.getFirstChild(); n != null; n = n.getNextSibling())
                    if (n.getNodeType() == Node.ELEMENT_NODE && "TR".equals(n.getNodeName())) {
                        if (premiereLigne == null)
                            premiereLigne = (Element)n;
                        nblignes++;
                    }
                int nbcols = 0;
                for (Node n = premiereLigne.getFirstChild(); n != null; n = n.getNextSibling()) {
                    if (n.getNodeType() == Node.ELEMENT_NODE &&
                            ("TD".equals(n.getNodeName()) || "TH".equals(n.getNodeName()))) {
                        Element td = (Element)n;
                        if (!"".equals(td.getAttribute("colspan"))) {
                            try {
                                nbcols += Integer.parseInt(td.getAttribute("colspan"));
                            } catch (NumberFormatException ex) {
                                nbcols++;
                            }
                        } else
                            nbcols++;
                    }
                }
                Object[][] tableau = new Object[nblignes][nbcols];
                for (int i=0; i<nblignes; i++)
                    for (int j=0; j<nbcols; j++)
                        tableau[i][j] = null;
                int iligne = 0;
                for (Node n = el.getFirstChild(); n != null; n = n.getNextSibling()) {
                    if (n.getNodeType() == Node.ELEMENT_NODE && "TR".equals(n.getNodeName())) {
                        Element tr = (Element)n;
                        int icol = 0;
                        for (Node n2 = tr.getFirstChild(); n2 != null; n2 = n2.getNextSibling()) {
                            if (n2.getNodeType() == Node.ELEMENT_NODE &&
                                    ("TD".equals(n2.getNodeName()) || "TH".equals(n2.getNodeName()))) {
                                Element td = (Element)n2;
                                while (icol < nbcols && tableau[iligne][icol] != null)
                                    icol++;
                                tableau[iligne][icol] = td;
                                int colspan = 1;
                                if (!"".equals(td.getAttribute("colspan"))) {
                                    try {
                                        colspan = Integer.parseInt(td.getAttribute("colspan"));
                                    } catch (NumberFormatException ex) {
                                    }
                                }
                                int rowspan = 1;
                                if (!"".equals(td.getAttribute("rowspan"))) {
                                    try {
                                        rowspan = Integer.parseInt(td.getAttribute("rowspan"));
                                    } catch (NumberFormatException ex) {
                                    }
                                }
                                for (int i=1; i<colspan; i++)
                                    tableau[iligne][icol+i] = "<";
                                for (int i=1; i<rowspan; i++)
                                    for (int j=0; j<colspan; j++)
                                        tableau[iligne+i][icol+j] = "^";
                                icol += colspan;
                            }
                        }
                        iligne++;
                    }
                }
                for (int i=0; i<nblignes; i++) {
                    buff.append('|');
                    for (int j=0; j<nbcols; j++) {
                        if (tableau[i][j] instanceof String)
                            buff.append((String)tableau[i][j] + "|");
                        else if (tableau[i][j] instanceof Element)
                            transfo((Element)tableau[i][j], buff, dansCellule, tableHTML);
                    }
                    buff.append('\n');
                }
            }
        } else if ("TR".equals(nom)) {
            if (tableHTML) {
                boolean premiereLigne = false;
                for (Node n = el.getFirstChild(); n != null && !premiereLigne; n = n.getNextSibling())
                    if (n.getNodeType() == Node.ELEMENT_NODE && "TH".equals(n.getNodeName()))
                        premiereLigne = true;
                if (premiereLigne)
                    buff.append("<tr class=\"row_first\">");
                else {
                    boolean pair = false;
                    Node tr = el;
                    while (tr != null) {
                        tr = tr.getPreviousSibling();
                        while (tr != null && tr.getNodeType() != Node.ELEMENT_NODE)
                            tr = tr.getPreviousSibling();
                        premiereLigne = false;
                        if (tr != null) {
                            for (Node n = tr.getFirstChild(); n != null && !premiereLigne; n = n.getNextSibling())
                                if (n.getNodeType() == Node.ELEMENT_NODE && "TH".equals(n.getNodeName()))
                                    premiereLigne = true;
                        }
                        if (!premiereLigne)
                            pair = !pair;
                    }
                    if (pair)
                        buff.append("<tr class=\"row_even\">");
                    else
                        buff.append("<tr class=\"row_odd\">");
                }
            } else
                buff.append('|');
        } else if ("TD".equals(nom)) {
            if (tableHTML) {
                buff.append("<td");
                NamedNodeMap atts = el.getAttributes();
                for (int i=0; i<atts.getLength(); i++) {
                    Node n = atts.item(i);
                    buff.append(" " + n.getNodeName() + "=\"" + n.getNodeValue() + "\"");
                }
                buff.append('>');
            } else
                dansCellule = true;
        } else if ("TH".equals(nom)) {
            if (tableHTML) {
                buff.append("<th");
                NamedNodeMap atts = el.getAttributes();
                for (int i=0; i<atts.getLength(); i++) {
                    Node n = atts.item(i);
                    buff.append(" " + n.getNodeName() + "=\"" + n.getNodeValue() + "\"");
                }
                buff.append('>');
            } else {
                if (buff.length() > 1 && !"{{".equals(buff.substring(buff.length() - 2)))
                    buff.append("{{");
                dansCellule = true;
            }
        } else if ("BR".equals(nom)) {
            if (buff.length() > 0 && buff.charAt(buff.length() - 1) != '\n')
                buff.append('\n');
            buff.append("_ ");
        } else if ("SEPARATION".equals(nom))
            buff.append("----");
        else if ("CITATION".equals(nom))
            buff.append("<quote>");
        else if ("MATH".equals(nom))
            buff.append("<math>");
        else if ("SUP".equals(nom))
            buff.append("<sup>");
        else if ("SUB".equals(nom))
            buff.append("<sub>");
        else if ("GLOSSAIRE".equals(nom))
            buff.append("[?");
        else if ("CADRE".equals(nom))
            buff.append("<cadre>");
        else if ("CODE".equals(nom))
            buff.append("<code>");
        else if ("POESIE".equals(nom))
            buff.append("<poesie>");
        
        if (!"TABLE".equals(nom) || tableHTML) {
            for (Node n = el.getFirstChild(); n != null; n = n.getNextSibling()) {
                if (n.getNodeType() == Node.ELEMENT_NODE)
                    transfo((Element)n, buff, dansCellule, tableHTML);
                else if (n.getNodeType() == Node.TEXT_NODE && !"TABLE".equals(nom) && !"LISTE".equals(nom) && !"LISTENUM".equals(nom)) {
                    String contenu = n.getNodeValue();
                    if (dansCellule)
                        contenu = contenu.replace('\n', ' ');
                    buff.append(contenu);
                }
            }
        }
        if ("INTERTITRE".equals(nom))
            buff.append("}}}");
        else if ("B".equals(nom)) {
            if (buff.charAt(buff.length() - 1) == '}')
                buff.append(' ');
            buff.append("}}");
        } else if ("I".equals(nom)) {
            if (buff.charAt(buff.length() - 1) == '}')
                buff.append(' ');
            buff.append('}');
        } else if ("LIEN".equals(nom)) {
            String ancrespip = "";
            String typelien = el.getAttribute("type");
            if ("".equals(typelien) || "article".equals(typelien))
                ancrespip = "art";
            else if ("rubrique".equals(typelien))
                ancrespip = "rub";
            else if ("brève".equals(typelien))
                ancrespip = "br";
            else if ("image".equals(typelien))
                ancrespip = "img";
            else if ("document".equals(typelien))
                ancrespip = "doc";
            else if ("ancre".equals(typelien))
                ancrespip = "#";
            else if ("externe".equals(typelien))
                ancrespip = "";
            else if ("mot-clé".equals(typelien))
                ancrespip = "mot";
            else if ("auteur".equals(typelien))
                ancrespip = "aut";
            else if ("site-syndiqué".equals(typelien))
                ancrespip = "site";
            String numerolien = el.getAttribute("numéro");
            ancrespip += numerolien;
            buff.append("->" + ancrespip + "]");
        } else if ("ANCRE".equals(nom)) {
            String nomancre = el.getAttribute("nom");
            buff.append(nomancre + "<-]");
        } else if ("NOTE".equals(nom))
            buff.append("]]");
        else if ("TABLE".equals(nom)) {
            if (tableHTML)
                buff.append("</table>");
        } else if ("TR".equals(nom)) {
            if (tableHTML)
                buff.append("</tr>");
            buff.append("\n");
        } else if ("LISTE".equals(nom) || "LISTENUM".equals(nom)) {
            Element p = (Element)el.getParentNode();
            if (!"EL".equals(p.getNodeName()))
                buff.append("\n");
        } else if ("TD".equals(nom)) {
            if (tableHTML)
                buff.append("</td>");
            else
                buff.append('|');
        } else if ("TH".equals(nom)) {
            if (tableHTML)
                buff.append("</th>");
            else {
                if (buff.length() > 1 && !"}}".equals(buff.substring(buff.length() - 2)))
                    buff.append("}}");
                buff.append('|');
            }
        } else if ("CITATION".equals(nom))
            buff.append("</quote>");
        else if ("MATH".equals(nom))
            buff.append("</math>");
        else if ("SUP".equals(nom))
            buff.append("</sup>");
        else if ("SUB".equals(nom))
            buff.append("</sub>");
        else if ("GLOSSAIRE".equals(nom))
            buff.append("]");
        else if ("CADRE".equals(nom))
            buff.append("</cadre>");
        else if ("CODE".equals(nom))
            buff.append("</code>");
        else if ("POESIE".equals(nom))
            buff.append("</poesie>");
    }
    
    public static String DOMVersSPIP(Document xmldoc) {
        StringBuffer buff = new StringBuffer();
        Element spip = xmldoc.getDocumentElement();
        transfo(spip, buff, false, false);
        buff.deleteCharAt(0);
        buff.deleteCharAt(buff.length()-1);
        //System.out.println(buff);
        return(buff.toString());
    }
    
    protected class CaretListenerLabel extends JLabel implements CaretListener {
        JaxeDocument doc;
        public CaretListenerLabel (String label, JaxeDocument doc) {
            super(label);
            this.doc = doc;
        }

        public void caretUpdate(CaretEvent e) {
            int dot = e.getDot();
            int mark = e.getMark();
            if (dot == mark) {  // no selection
                setText(dot + ": " + doc.getPathAsString(dot));
            }
            majMenus(dot);
        }
    }
    
    /**
     * Mise à jour des menus (grisé / non grisé) avec la liste des balises autorisées
     */
    public void majMenus(int pos) {
        JaxeDocument doc = jaxeDoc;
        if (doc.cfg == null || barreBalises == null)
            return;
        JaxeElement parent = null;
        if (doc.rootJE != null)
            parent = doc.rootJE.elementA(pos);
        if (parent != null && parent.debut.getOffset() == pos &&
                !(parent instanceof JESwing))
            parent = parent.getParent() ;
        if (parent != null && parent.noeud.getNodeType() == Node.TEXT_NODE)
            parent = parent.getParent();
        ArrayList autorisees = null;
        Config parentconf = null;
        if (parent == null) {
            parentconf = doc.cfg;
            autorisees = doc.cfg.listeRacines();
        } else {
            Element parentdef = doc.cfg.getElementDef((Element)parent.noeud);
            if (parentdef == null)
                return;
            parentconf = doc.cfg.getDefConf(parentdef);
            autorisees = parentconf.listeSousbalises(parentdef);
        }
        for (int i=0; i<barreBalises.getMenuCount(); i++) {
            JMenu menu = barreBalises.getMenu(i);
            if (!"Edition".equals(menu.getText()))
                majMenu(menu, parentconf, autorisees);
        }
    }
    
    protected boolean majMenu(JMenu menu, Config parentconf, ArrayList autorisees) {
        boolean anyenab = false;
        for (int i=0; i<menu.getItemCount(); i++) {
            JMenuItem item = menu.getItem(i);
            Action action = item.getAction();
            if (action instanceof ActionInsertionBalise) {
                Element defbalise = ((ActionInsertionBalise)action).getDefbalise();
                if (defbalise != null) {
                    Config conf = jaxeDoc.cfg.getDefConf(defbalise);
                    if (conf == parentconf) {
                        String typebalise = conf.typeBalise(defbalise);
                        String nombalise = conf.nomBalise(defbalise);
                        if (!(typebalise.equals("style") && nombalise.equals("NORMAL"))) {
                            boolean enable = false;
                            for (int j=0; j<autorisees.size(); j++)
                                if (nombalise.equals((String)autorisees.get(j))) {
                                    enable = true;
                                    anyenab = true;
                                    break;
                                }
                            action.setEnabled(enable);
                        }
                    } else
                        action.setEnabled(true);
                }
            } else if (item instanceof JMenu)
                anyenab = majMenu((JMenu)item, parentconf, autorisees) || anyenab;
        }
        if (!menu.isTopLevelMenu())
            menu.setEnabled(anyenab);
        return(anyenab);
    }
    
    class UndoAction extends AbstractAction {
        public UndoAction() {
            super("Annuler");
            setEnabled(false);
        }
          
        public void actionPerformed(ActionEvent e) {
            textPane.undo();
        }
        
        protected void updateUndoState() {
            UndoManager undo = textPane.getUndo();
            if (undo.canUndo()) {
                setEnabled(true);
                putValue(Action.NAME, undo.getUndoPresentationName());
            } else {
                setEnabled(false);
                putValue(Action.NAME, "Annuler");
            }
        }      
    }
    
    class RedoAction extends AbstractAction {
        public RedoAction() {
            super("Rétablir");
            setEnabled(false);
        }

        public void actionPerformed(ActionEvent e) {
            UndoManager undo = textPane.getUndo();
            try {
                undo.redo();
            } catch (CannotRedoException ex) {
                System.out.println("Impossible de rétablir: " + ex);
                ex.printStackTrace();
            }
            updateRedoState();
            undoAction.updateUndoState();
        }

        protected void updateRedoState() {
            UndoManager undo = textPane.getUndo();
            if (undo.canRedo()) {
                setEnabled(true);
                putValue(Action.NAME, undo.getRedoPresentationName());
            } else {
                setEnabled(false);
                putValue(Action.NAME, "Rétablir");
            }
        }
    }
    
    public void miseAJour() {
        undoAction.updateUndoState();
        redoAction.updateRedoState();
    }
    
    class ActionRechercher extends TextAction {

        public ActionRechercher() {
            super("Rechercher...");
        }

        public void actionPerformed(ActionEvent e) {
            /*JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane)target).rechercher();
            */
            textPane.rechercher();
        }
    }

    class ActionSuivant extends TextAction {

        public ActionSuivant() {
            super("Rechercher suivant");
        }

        public void actionPerformed(ActionEvent e) {
            /*JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane)target).suivant();
            */ // pb focus
            textPane.suivant();
        }
    }

    // inspiré de DefaultEditorKit.CutAction, mais EN FRANCAIS
    protected class ActionCouper extends TextAction {
        public ActionCouper() {
            super("Couper");
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane)target).couper();
        }
    }
    
    protected class ActionCopier extends TextAction {
        public ActionCopier() {
            super("Copier");
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane)target).copier();
        }
    }
    
    protected class ActionColler extends TextAction {
        public ActionColler() {
            super("Coller");
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane)target).coller();
        }
    }
    
    static class ActionToutSelectionner extends TextAction {

        public ActionToutSelectionner() {
            super("Tout sélectionner");
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane)target).toutSelectionner();
        }

    }
}
