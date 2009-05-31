/*
 Jaxe - Editeur XML en Java

 Copyright (C) 2002 Observatoire de Paris-Meudon

 Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

 Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

 Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
 */

package jaxe;

import java.awt.Component;
import java.awt.Container;
import java.awt.Image;
import java.awt.Toolkit;
import java.awt.datatransfer.Clipboard;
import java.awt.datatransfer.DataFlavor;
import java.awt.datatransfer.Transferable;
import java.awt.datatransfer.UnsupportedFlavorException;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.StringWriter;
import java.net.URL;
import java.util.ArrayList;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.ResourceBundle;
import java.util.Vector;

import javax.swing.JComponent;
import javax.swing.JFrame;
import javax.swing.JOptionPane;
import javax.swing.OverlayLayout;
import javax.swing.event.DocumentEvent;
import javax.swing.event.UndoableEditEvent;
import javax.swing.text.AbstractDocument;
import javax.swing.text.AttributeSet;
import javax.swing.text.BadLocationException;
import javax.swing.text.BoxView;
import javax.swing.text.ComponentView;
import javax.swing.text.DefaultStyledDocument;
import javax.swing.text.EditorKit;
import javax.swing.text.IconView;
import javax.swing.text.JTextComponent;
import javax.swing.text.LabelView;
import javax.swing.text.ParagraphView;
import javax.swing.text.Position;
import javax.swing.text.SimpleAttributeSet;
import javax.swing.text.Style;
import javax.swing.text.StyleConstants;
import javax.swing.text.StyleContext;
import javax.swing.text.StyledEditorKit;
import javax.swing.text.View;
import javax.swing.text.ViewFactory;
import javax.swing.undo.UndoableEdit;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import javax.xml.transform.*;
import javax.xml.transform.dom.*;
import javax.xml.transform.stream.*;

import jaxe.elements.JEDivision;
import jaxe.elements.JEInconnu;
import jaxe.elements.JEStyle;
import jaxe.elements.JESwing;
import jaxe.elements.JETexte;
import jaxe.elements.JETableTexte;

import org.w3c.dom.DocumentFragment;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;

/**
 * Classe représentant un document XML
 */
public class JaxeDocument extends DefaultStyledDocument {

    private static ResourceBundle rb = JaxeResourceBundle.getRB();
    static String newline = "\n";
    public org.w3c.dom.Document DOMdoc = null;
    public Hashtable dom2JaxeElement = null;
    public JaxeElement rootJE = null;
    public JaxeTextPane textPane;
    public File fsave = null;
    public String encodage = "ISO-8859-1"; // valeur par défaut
    public boolean modif = false;
    public Config cfg = null;
    public JFrame jframe;
    public String nomFichierCfg;

    final static String kPoliceParDefaut = "Serif";

    final static int kTailleParDefaut = 14;

    private ErrorHandlerIf errorHandler = new ErrorHandler(this);
    
    private List _editListener;
    
    private boolean _ignorer = false;

    public JaxeDocument() {
        super();
        setDefaultStyle();
        _editListener = new ArrayList();
    }

    public JaxeDocument(String nomFichierCfg) {
        super();
        this.nomFichierCfg = nomFichierCfg;
        if (nomFichierCfg != null) cfg = new Config(nomFichierCfg, true);
        setDefaultStyle();
        _editListener = new ArrayList();
    }

    public JaxeDocument(URL urlFichierCfg) {
        super();
        this.nomFichierCfg = urlFichierCfg.toExternalForm();
        if (urlFichierCfg != null) cfg = new Config(urlFichierCfg, true);
        setDefaultStyle();
        _editListener = new ArrayList();
    }

    public JaxeDocument(Config newconfig) {
        super();
        cfg = newconfig;
        setDefaultStyle();
        _editListener = new ArrayList();
    }

    public JaxeDocument(JaxeTextPane textPane, String nomFichierCfg) {
        super();
        this.textPane = textPane;
        this.nomFichierCfg = nomFichierCfg;
        jframe = textPane.jframe;
        if (nomFichierCfg != null) cfg = new Config(nomFichierCfg, true);
        setDefaultStyle();
        _editListener = new ArrayList();
    }

    public JaxeDocument(JaxeTextPane textPane, URL urlFichierCfg) {
        super();
        this.textPane = textPane;
        this.nomFichierCfg = urlFichierCfg.toExternalForm();
        jframe = textPane.jframe;
        if (urlFichierCfg != null) cfg = new Config(urlFichierCfg, true);
        setDefaultStyle();
        _editListener = new ArrayList();
    }

    /**
     * Sets the Errorhandler for this Document
     * 
     * @param error
     *            Errorhandler
     */
    public void setErrorHandler(ErrorHandlerIf error) {
        errorHandler = error;
    }

    /**
     * Returns the ErrorHandler for this Document
     * 
     * @return ErrorHandler
     */
    public ErrorHandlerIf getErrorHandler() {
        return errorHandler;
    }

    private void setDefaultStyle() {
        Style defaultStyle = getStyle(StyleContext.DEFAULT_STYLE);
        StyleConstants.setFontFamily(defaultStyle, kPoliceParDefaut);
        StyleConstants.setFontSize(defaultStyle, kTailleParDefaut);
    }

    public void setTextPane(JaxeTextPane textPane) {
        this.textPane = textPane;
        jframe = textPane.jframe;
    }

    /**
     * Initialise un document vide
     */
    public void nouveau() {
        if (cfg == null) {
            System.err
                    .println("nouveau: pas de fichier de configuration en entrée");
            // cette erreur ne peut normalement pas arriver, donc pas de string
            // dans le ResourceBundle
            return;
        }
        fsave = null;
        try {
            DocumentBuilder docbuilder = DocumentBuilderFactory.newInstance().newDocumentBuilder();
            DOMdoc = docbuilder.newDocument();
        } catch (ParserConfigurationException ex) {
            System.err.println("ParserConfigurationException: " + ex.getMessage());
        }
        dom2JaxeElement = new Hashtable();
        ArrayList racines = cfg.listeRacines();
        if (racines.size() == 1) {
            Element defracine = cfg.racine();
            String typebalise = cfg.typeBalise(defracine);
            if (!"".equals(typebalise))
                rootJE = JEFactory.createJE(typebalise, this, defracine, (Element)null);
            else
                rootJE = new JEDivision(this);
            Element rootel = (Element) rootJE.nouvelElement(defracine);
            if (rootel == null) {
                // l'utilisateur pourrait annuler, ce qui peut poser problème...
                rootel = JaxeElement.nouvelElementDOM(this, defracine);
            }
            cfg.ajouterAttributsEspaces(rootel);
            if (cfg.getEncodage() != null)
                encodage = cfg.getEncodage();
            DOMdoc.appendChild(rootel);

            textPane.debutIgnorerEdition();
            try {
                rootJE.creer(createPosition(0), rootel);
            } catch (BadLocationException ex) {
                System.err.println("BadLocationException: " + ex.getMessage());
            }
            textPane.finIgnorerEdition();
            textPane.setCaretPosition(rootJE.insPosition().getOffset());
            textPane.moveCaretPosition(rootJE.insPosition().getOffset());
        } else
            rootJE = null;
    }

    /**
     * Initialise un document lu à partir d'une URL
     */
    public boolean lire(URL url) {
        return(lire(url, null));
    }
    
    /**
     * Initialise un document lu à partir d'une URL, en utilisant un fichier de config particulier
     */
    public boolean lire(URL url, String cheminFichierCfg) {
        org.w3c.dom.Document ddoc = null;
        try {
            DocumentBuilder docbuilder = DocumentBuilderFactory.newInstance().newDocumentBuilder();
            ddoc = docbuilder.parse(url.toExternalForm());
            //encodage = ddoc.getEncoding(); // attention, DOM 3 expérimental !
        } catch (SAXException ex) {
            String infos = rb.getString("erreur.XML") + ":" + newline;
            infos += ex.getMessage();
            if (ex instanceof SAXParseException)
                infos += " " + rb.getString("erreur.ALaLigne") + " " +
                    ((SAXParseException)ex).getLineNumber();
            JOptionPane.showMessageDialog(jframe, infos,
                rb.getString("document.Lecture"), JOptionPane.ERROR_MESSAGE);
            return false;
        } catch (IOException ex) {
            String infos = rb.getString("erreur.ES") + ":" + newline;
            infos += ex.getMessage();
            JOptionPane.showMessageDialog(jframe, infos,
                rb.getString("document.Lecture"), JOptionPane.ERROR_MESSAGE);
            return false;
        } catch (ParserConfigurationException ex) {
            System.err.println("ParserConfigurationException: " + ex.getMessage());
            ddoc = null;
        }

        fsave = new File(url.getFile());
        return (setDOMDoc(ddoc, cheminFichierCfg));
    }

    /**
     * Spécifie le document DOM de ce document Jaxe
     */
    public boolean setDOMDoc(org.w3c.dom.Document ddoc) {
        return(setDOMDoc(ddoc, null));
    }
    
    /**
     * Spécifie le document DOM de ce document Jaxe, en utilisant un fichier de config particulier
     */
    public boolean setDOMDoc(org.w3c.dom.Document ddoc, String cheminFichierCfg) {
        DOMdoc = ddoc;
        dom2JaxeElement = new Hashtable();
        Element rootel = DOMdoc.getDocumentElement();
        //if (!"true".equals(Preferences.getPref().getProperty("consIndent")))
        //    virerEspaces(rootel);

        if (cheminFichierCfg == null)
            nomFichierCfg = chercherConfig(rootel);
        else if (!cheminFichierCfg.equals(nomFichierCfg)) {
           nomFichierCfg = cheminFichierCfg;
            cfg = new Config(cheminFichierCfg, true);
        }
        if (nomFichierCfg == null)
            JOptionPane.showMessageDialog(jframe,
                rb.getString("erreur.ConfigPour") + " " +
                Config.localValue(rootel.getTagName()),
                rb.getString("erreur.Erreur"), JOptionPane.ERROR_MESSAGE);

        if (nomFichierCfg == null)
            rootJE = new JEInconnu(this);
        else {
            Element defracine = cfg.racine();
            String typebalise = cfg.typeBalise(defracine);
            if (!"".equals(typebalise))
                rootJE = JEFactory.createJE(typebalise, this, defracine, rootel);
            else
                rootJE = new JEDivision(this);
        }

        try {
            textPane.debutIgnorerEdition();
            rootJE.creer(createPosition(0), rootel);
            textPane.finIgnorerEdition();
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return false;
        }
        //DefaultDocumentEvent de = new DefaultDocumentEvent(0, getLength(), DocumentEvent.EventType.CHANGE);
        //fireChangedUpdate(de);
        // marche pas !

        modif = false;
        return true;
    }

    /**
     * Sets the RootNode of the Document
     * 
     * @param node
     *            the Node
     * @return boolean successfull ?
     */
    public boolean setRootElement(org.w3c.dom.Element node) {
        return setRootElement(node, node);
    }

    /**
     * Sets the RootNode of the Document with a Node that is used to search the
     * Config-File
     * 
     * @param node
     *            the Node
     * @param configNode
     *            the Node wich will be used as Config-File
     * @return boolean successfull ?
     */
    public boolean setRootElement(org.w3c.dom.Element node,
            org.w3c.dom.Element configNode) {
        DOMdoc = node.getOwnerDocument();
        dom2JaxeElement = new Hashtable();
        Element rootel = node;
        //if (!"true".equals(Preferences.getPref().getProperty("consIndent")))
                virerEspaces(rootel);

        String nomFichierCfg = chercherConfig(configNode);
        if (nomFichierCfg == null)
            System.err.println(rb.getString("erreur.ConfigPour") + " " +
                Config.localValue(rootel.getTagName()));

        if (nomFichierCfg == null)
            rootJE = new JEInconnu(this);
        else {
            Element defracine = cfg.getBaliseDef(rootel.getNodeName());
            String typebalise = cfg.typeBalise(defracine);
            if (!"".equals(typebalise))
                rootJE = JEFactory.createJE(typebalise, this, defracine, rootel);
            else
                rootJE = new JEDivision(this);
        }

        try {
            textPane.debutIgnorerEdition();
            rootJE.creer(createPosition(0), rootel);
            textPane.finIgnorerEdition();
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return false;
        }
        //DefaultDocumentEvent de = new DefaultDocumentEvent(0, getLength(),
        // DocumentEvent.EventType.CHANGE);
        //fireChangedUpdate(de);
        // marche pas !

        modif = false;
        return true;
    }

    public Node getRootElement() {
        Node result = rootJE.noeud.cloneNode(true);
        boolean changed = false;
        do {
            changed = false;
            Node child = result.getFirstChild();
            while (child != null) {
                if (child instanceof Element) {
                    Element defbalise =  cfg.getBaliseDef(child.getNodeName());
                    
                    if (defbalise != null) {
                        String typebalise = cfg.typeBalise(defbalise);
                        if (typebalise.equals("style")) {
                            if (child.getNextSibling() != null) {
                                Node next = child.getNextSibling();
                                Element defbalise2 =  cfg.getBaliseDef(next.getNodeName());
                                if (defbalise2 != null) {
                                    String typebalise2 = cfg.typeBalise(defbalise);
                                    if (typebalise2.equals("style")) {
                                        Node prev = child.getPreviousSibling();
                                        changed = changed | joinNodes(child, next);
                                        if (changed) {
                                            if (prev == null) {
                                                child = result.getFirstChild();
                                            } else {
                                                child = prev;
                                            }
                                        }
                                    }
                                }
                            }
                            if (!changed) {
                                changed = changed | goDeep(child);
                            }
                        
                        } else {
                            changed = changed | goDeep(child);
                        }
                    }
                    
                } else {
                    changed = changed | goDeep(child);
                }
                child = child.getNextSibling();
            }
        } while (changed);
        return result;
    }

    private int childCount(Node n){
        return n.getChildNodes().getLength();
    }
    
    /**
     * @param child
     * @param nextSibling
     * @return
     */
    private boolean joinNodes(Node child, Node nextSibling) {
        if (child.getNodeName().equals(nextSibling.getNodeName())) {
            Node c = nextSibling.getFirstChild();
            while (c != null) {
                child.appendChild(c);
                c = c.getNextSibling();
            }
            nextSibling.getParentNode().removeChild(nextSibling);
            return true;
        }
        return false;
    }

    private boolean goDeep(Node n) {
        boolean changed = false;
        Node child = n.getFirstChild();
        while (child != null) {
            if (child instanceof Element) {
                Element defbalise =  cfg.getBaliseDef(child.getNodeName());
                
                if (defbalise != null) {
                    String typebalise = cfg.typeBalise(defbalise);
                    if (typebalise.equals("style")) {
                        if (child.getNextSibling() != null) {
                            Node next = child.getNextSibling();
                            Element defbalise2 =  cfg.getBaliseDef(next.getNodeName());
                            if (defbalise2 != null) {
                                String typebalise2 = cfg.typeBalise(defbalise);
                                if (typebalise2.equals("style")) {
                                    Node prev = child.getPreviousSibling();
                                    if (joinNodes(child, next)) {
                                    	changed = true;
                                        if (prev == null) {
                                            child = n.getFirstChild();
                                        } else {
                                            child = prev;
                                        }
                                    }
                                }
                            }
                        }
                        if (!changed) {
                            changed = changed | goDeep(child);
                        }
                    
                    } else {
                        changed = changed | goDeep(child);
                    }
                }
                
            } else {
                changed = changed | goDeep(child);
            }
            child = child.getNextSibling();
        }
        return changed;
    }

    private Node getTextNode(Node n) {
        Node result = null;
        while (n != null && n.getNodeType() != Node.TEXT_NODE) {
            n = n.getFirstChild();
            result = n;
        }
        return result;
    }

    private boolean sameStyle(List source, List target) {
        List s1 = new ArrayList();
        Iterator temp = source.iterator();
        while (temp.hasNext()) {
            s1.add(((Node) temp.next()).getNodeName());
        }
        List s2 = new ArrayList();
        temp = target.iterator();
        while (temp.hasNext()) {
            s2.add(((Node) temp.next()).getNodeName());
        }

        boolean result = true;
        Iterator it = s1.iterator();
        while (it.hasNext()) {
            if (!s2.contains((String) it.next())) result = false;
        }
        it = s2.iterator();
        while (it.hasNext()) {
            if (!s1.contains((String) it.next())) result = false;
        }
        return result;
    }

    protected String chercherConfig(Element rootel) {
        String nomFichierCfg = null;
        File configdir = new File("config");
        String[] liste = configdir.list();
        if (liste == null) {
            System.err.println(rb.getString("erreur.DossierConfig"));
            return (null);
        }
        for (int i = 0; i < liste.length; i++){
            if (liste[i].endsWith("_cfg.xml")) {
                Config cfgtest = new Config("config" + File.separator
                        + liste[i], false);
                if (Config.localValue(rootel.getTagName()).equals(
                        cfgtest.nomBalise(cfgtest.racine()))) {
                    nomFichierCfg = "config" + File.separator + liste[i];
                    cfg = new Config("config" + File.separator + liste[i], true);
                    break;
                }
            }
        }
        return (nomFichierCfg);
    }

    // retire les espaces gênants de cet élément, et récursivement
    public void virerEspaces(Element el) {
        for (Node n = el.getFirstChild(); n != null; n = n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE)
                virerEspaces((Element) n);
            else if (n.getNodeType() == Node.TEXT_NODE) {
                String s = n.getNodeValue();

                // on ne retire pas les blancs s'il n'y a que du blanc dans
                // l'élément
                if (n.getNextSibling() == null
                        && n.getPreviousSibling() == null
                        && "".equals(s.trim())) break;

                if (n.getParentNode().getFirstChild() == n) {
                    // retire espaces au début si le texte est au début de l'élément
                    int ifin = -1;
                    while (ifin+1 < s.length() && (s.charAt(ifin+1) == ' ' || s.charAt(ifin+1) == '\t'))
                        ifin++;
                    if (ifin != -1)
                        s = s.substring(ifin+1);
                }

                // retire les espaces après les retours à la ligne
                int idebut = s.indexOf(newline + " ");
                int idebuttab = s.indexOf(newline + "\t");
                if (idebuttab != -1 && (idebut == -1 || idebuttab < idebut))
                        idebut = idebuttab;
                while (idebut != -1) {
                    int ifin = idebut;
                    while (ifin + 1 < s.length()
                            && (s.charAt(ifin + 1) == ' ' || s.charAt(ifin + 1) == '\t'))
                        ifin++;
                    s = s.substring(0, idebut + 1) + s.substring(ifin + 1);
                    idebut = s.indexOf(newline + " ");
                    idebuttab = s.indexOf(newline + "\t");
                    if (idebuttab != -1 && (idebut == -1 || idebuttab < idebut))
                        idebut = idebuttab;
                }

                // condense les espaces partout
                idebut = s.indexOf("  ");
                while (idebut != -1) {
                    int ifin = idebut;
                    while (ifin + 1 < s.length() && s.charAt(ifin + 1) == ' ')
                        ifin++;
                    s = s.substring(0, idebut) + s.substring(ifin);
                    idebut = s.indexOf("  ");
                }
                if ("".equals(s)) {
                    Node n2 = n.getPreviousSibling();
                    el.removeChild(n);
                    if (n2 == null) n2 = el.getFirstChild();
                    n = n2;
                    if (n == null) break;
                } else
                    n.setNodeValue(s);
            }
        }
    }

    public void ecrire(File f) throws IOException {
        try {
            DOMSource domSource = new DOMSource(DOMdoc);
            StreamResult streamResult = new StreamResult(new FileOutputStream(f));
            TransformerFactory tf = TransformerFactory.newInstance();
            Transformer serializer = tf.newTransformer();
            serializer.setOutputProperty(OutputKeys.ENCODING, encodage);
            serializer.setOutputProperty(OutputKeys.INDENT, "no");
            serializer.transform(domSource, streamResult);
        } catch (TransformerConfigurationException ex) {
            System.err.println("DOMVersXML: TransformerConfigurationException: " + ex.getMessage());
        } catch (TransformerException ex) {
            System.err.println("DOMVersXML: TransformerException: " + ex.getMessage());
        }
        fsave = f;
        modif = false;
    }

    public String getPathAsString(int p) {
        if (rootJE == null)
            return null;
        String chemin = rootJE.cheminA(p);
        return (chemin);
    }

    public void mettreAJourDOM() {
        rootJE.mettreAJourDOM();
    }

    public JaxeElement elementA(int pos) {
        if (rootJE == null)
            return (null);
        else
            return (rootJE.elementA(pos));
    }

    public DocumentFragment copier(int debut, int fin) {
        JaxeElement firstel = rootJE.elementA(debut);
        if (firstel == null) {
            Toolkit.getDefaultToolkit().beep();
            return null;
        }
        firstel = rootJE.elementA(debut);
        while (firstel.debut.getOffset() == debut && firstel.getParent() instanceof JESwing &&
                firstel.getParent().debut.getOffset() == debut && firstel.getParent().fin.getOffset() <= fin)
            firstel = firstel.getParent();
        JaxeElement p1 = firstel;
        if (p1 instanceof JETexte || p1 instanceof JEStyle || p1.debut.getOffset() == debut)
            p1 = p1.getParent();
        JaxeElement lastel = rootJE.elementA(fin - 1);
        if (lastel == null) {
            Toolkit.getDefaultToolkit().beep();
            return null;
        }
        lastel = rootJE.elementA(fin - 1);
        if (lastel.fin.getOffset() == fin-1 && lastel.getParent() instanceof JESwing &&
                lastel.getParent().debut.getOffset() >= debut && lastel.getParent().fin.getOffset() == fin)
            lastel = lastel.getParent();
        if (lastel.fin.getOffset() == fin && lastel.getParent() instanceof JESwing &&
                lastel.getParent().debut.getOffset() >= debut && lastel.getParent().fin.getOffset() == fin)
            lastel = lastel.getParent();
        while (lastel.fin.getOffset() == fin-1 &&
                (lastel.getParent() instanceof JESwing || lastel.getParent() instanceof JETableTexte) &&
                lastel.getParent().debut.getOffset() >= debut && lastel.getParent().fin.getOffset() == fin-1)
            lastel = lastel.getParent();
        JaxeElement p2 = lastel;
        if (p2 instanceof JETexte || p2 instanceof JEStyle || p2.fin.getOffset() == fin - 1 ||
                (p2 instanceof JESwing && p2.fin.getOffset() == fin))
            p2 = p2.getParent();
        if (p1 != p2 || p1 == null) {
            return null;
        }
        if (firstel == lastel && firstel.getClass().getName().equals("jaxe.elements.JETableTexte$JESwingTD")) {
            // on ne copie pas la cellule entière si juste son contenu est sélectionné
            p1 = firstel;
            firstel = getElementForNode(p1.noeud.getFirstChild());
            lastel = getElementForNode(p1.noeud.getLastChild());
        }
        DocumentFragment frag = DOMdoc.createDocumentFragment();
        if (firstel instanceof JETexte) {
            String texte = firstel.noeud.getNodeValue();
            if (fin - firstel.debut.getOffset() > texte.length()) {
                texte = texte.substring(debut - firstel.debut.getOffset());
            } else {
                texte = texte.substring(debut - firstel.debut.getOffset(), fin - firstel.debut.getOffset());
            }
            Node tn = DOMdoc.createTextNode(texte);
            frag.appendChild(tn.cloneNode(true));
        } else if (firstel instanceof JEStyle) {
            String texte = ((JEStyle)firstel).getText();
            if (fin - firstel.debut.getOffset() > texte.length()) {
                texte = texte.substring(debut - firstel.debut.getOffset());
            } else {
                texte = texte.substring(debut - firstel.debut.getOffset(), fin - firstel.debut.getOffset());
            }
            Node tn = DOMdoc.createTextNode(texte);
            Iterator style = ((JEStyle)firstel)._styles.iterator();
            while (style.hasNext()) {
				Node node = ((Node) style.next()).cloneNode(false);
				node.appendChild(tn);
				tn = node;
            }
            frag.appendChild(tn.cloneNode(true));
        } else
            frag.appendChild(firstel.noeud.cloneNode(true));
        if (firstel == p1) p1 = firstel.getParent();
        Node n = p1.noeud.getFirstChild();
        while (n != null && n != firstel.noeud)
            n = n.getNextSibling();
        if (n == null) {
            System.err.println("erreur dans la copie de texte!");
            return null;
        }
        if (firstel != lastel) {
            n = n.getNextSibling();
            while (n != null && n != lastel.noeud) {
                frag.appendChild(n.cloneNode(true));
                n = n.getNextSibling();
            }
            if (n == null) {
                System.err.println("erreur dans la copie de texte!");
                return null;
            }
            if (lastel instanceof JETexte) {
                String texte = lastel.noeud.getNodeValue();
                texte = texte.substring(0, fin - lastel.debut.getOffset());
                Node tn = DOMdoc.createTextNode(texte);
                frag.appendChild(tn.cloneNode(true));
            } else if (lastel instanceof JEStyle) {
                String texte = ((JEStyle)lastel).getText();
                texte = texte.substring(0, fin - lastel.debut.getOffset());
                Node tn = DOMdoc.createTextNode(texte);
                Iterator style = ((JEStyle)lastel)._styles.iterator();
                while (style.hasNext()) {
    				Node node = ((Node) style.next()).cloneNode(false);
    				node.appendChild(tn);
    				tn = node;
                }
                frag.appendChild(tn.cloneNode(true));
            } else
                frag.appendChild(lastel.noeud.cloneNode(true));
        }
        removeProcessingInstructions(frag);
        return frag;
    }
    
    protected Node removeProcessingInstructions(Node n) {
        Node child = n.getFirstChild();
        while (child != null) {
            if (child.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE) {
                Node prev = child.getPreviousSibling();
                child.getParentNode().removeChild(child);
                if (prev != null) {
                    child = prev;
                } else {
                    child = n.getFirstChild();
                }
            } else if (child.getNodeType() == Node.ELEMENT_NODE) {
                removeProcessingInstructions(child);
            }
            child = child.getNextSibling();
        }
        return n;
    }

    /**
     * Teste si l'insertion d'un fragment est autorisée sous un certain élément
     * parent à la position pos. Si elle n'est pas autorisée, affiche un message
     * d'erreur et renvoit false. Sinon renvoit true.
     */
    public boolean testerInsertionFragment(DocumentFragment frag,
            JaxeElement parent, Position pos) {
        if (cfg != null) {
            Element parentdef = cfg.getElementDef((Element) parent.noeud);
            for (Node n=frag.getFirstChild(); n != null; n=n.getNextSibling()) {
                if (n.getNodeType() == Node.TEXT_NODE && !"".equals(n.getNodeValue().trim()) &&
                    !cfg.contientDuTexte(parentdef)) {
                    String infos = rb.getString("erreur.InsertionInterdite") + " " +
                        parent.noeud.getNodeName();
                    JOptionPane.showMessageDialog(jframe, infos,
                        rb.getString("document.Insertion"), JOptionPane.ERROR_MESSAGE);
                    return (false);
                } else if (n.getNodeType() == Node.ELEMENT_NODE) {
                    String nombalise = n.getNodeName();
                    Config conf = cfg.getBaliseConf(nombalise);
                    if (conf == null) conf = cfg;
                    Element defbalise = conf.getBaliseDef(nombalise);
                    parentdef = null;
                    Element parentns = (Element) parent.noeud;
                    String pns = parentns.getNamespaceURI();
                    String cns = conf.namespace();
                    if ((pns != null || cns != null)
                            && (pns == null || !pns.equals(cns)))
                        parentns = cfg.chercheParentEspace(parentns, cns);
                    if (parentns != null)
                        parentdef = conf.getElementDef(parentns);
                    if (parentdef != null
                            && !conf.sousbalise(parentdef, nombalise)) {
                        errorHandler.childNotAllowedInParentdef(parentdef,
                                defbalise);
                        return (false);
                    } else {
                        if (!cfg.insertionPossible(parent, pos, defbalise)) {
                            String expr = cfg.expressionReguliere(parentdef);
                            errorHandler.childNotAllowed(expr, parent,
                                    defbalise);
                            return (false);
                        }
                    }
                }
            }
        }
        return (true);
    }

    /** pour coller du XML */
    public boolean coller(Object pp, Position pos) {
        if (!(pp instanceof DocumentFragment)) return false;
        DocumentFragment frag = (DocumentFragment) (((DocumentFragment) pp)
                .cloneNode(true));

        return coller(frag, pos, true);
    }

    /**
     * @param pos
     * @param frag
     */
    public boolean coller(DocumentFragment frag, Position pos, boolean event) {
        JaxeElement parent = rootJE.elementA(pos.getOffset());
        if (parent != null && parent.debut.getOffset() == pos.getOffset() &&
                !(parent instanceof JESwing))
            parent = parent.getParent() ;
        if (parent == null) {
            Toolkit.getDefaultToolkit().beep();
            return false;
        }

        textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString(
        "menus.Coller"), false);
        
        if (parent.noeud.getNodeType() == Node.TEXT_NODE) {
            JaxeElement je1 = parent;
            parent = parent.getParent();
            if (pos.getOffset() > je1.debut.getOffset()
                    && pos.getOffset() <= je1.fin.getOffset()) {
                // couper la zone de texte en 2
                JaxeElement je2 = je1.couper(pos);
            }
        }

        if (!testerInsertionFragment(frag, parent, pos)) {
            textPane.finEditionSpeciale();
//            textPane.undo();
            return false;
        }
        if (event) pos = firePrepareElementAddEvent(pos);

        if (DOMdoc != frag.getOwnerDocument())
                frag = (DocumentFragment) DOMdoc.importNode(frag, true);
        ArrayList nl = new ArrayList();
        for (Node n = frag.getFirstChild(); n != null; n = n.getNextSibling())
            nl.add(n);

        parent.insererDOM(pos, frag);
        textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString(
        "menus.Coller"), true);
        JaxeElement last = null;
        for (int i = 0; i < nl.size(); i++) {
            // creerEnfant modifie le ptr de fin, ce qui est utile à la création
            // du doc, mais pas ici
            Position sfin = parent.fin;
            parent.creerEnfant(pos, (Node) nl.get(i));
            parent.fin = sfin;
            JaxeElement newje = getElementForNode((Node) nl.get(i));
            
            // on corrige la position du parent, qui peut être changée après creerEnfant si c'est un JESwing
            JaxeElement testparent = parent;
            while (testparent instanceof JESwing && testparent.debut.getOffset() > newje.debut.getOffset()) {
                try {
                    testparent.debut = createPosition(newje.debut.getOffset());
                } catch (BadLocationException ex) {
                    System.err.println("BadLocationException: " + ex.getMessage());
                }
                testparent = testparent.getParent();
            }
            
            if (newje != null)
                    textPane.addEdit(new JaxeUndoableEdit(
                            JaxeUndoableEdit.AJOUTER, newje));
            last = newje;
        }
        if (event) pos = fireElementAddedEvent(new JaxeEditEvent(this, last), pos);
        textPane.finEditionSpeciale();
        textPane.finEditionSpeciale();
        parent.regrouperTextes();
        parent.majValidite();
        modif = true;
        textPane.miseAJourArbre();
        return true;
    }

    /** pour coller un texte ou une image */
    public void coller(JTextComponent target) {
        target.paste();
    }

    public String pp2string(Object pp) {
        if (!(pp instanceof DocumentFragment)) return null;
        DocumentFragment frag = (DocumentFragment) pp;
        return(DOMVersXML(frag));
    }
    
    public String DOMVersXML(Node xmldoc) {
        try {
            DOMSource domSource = new DOMSource(xmldoc);
            StringWriter sw = new StringWriter();
            StreamResult streamResult = new StreamResult(sw);
            TransformerFactory tf = TransformerFactory.newInstance();
            Transformer serializer = tf.newTransformer();
            serializer.setOutputProperty(OutputKeys.ENCODING, encodage);
            serializer.setOutputProperty(OutputKeys.INDENT, "no");
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
    
    protected void removeText(int offs, int len, boolean event) throws BadLocationException {
        String str = getText(offs, len);
//        textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString("annulation.AnnulerSuppression"), false);
        JaxeUndoableEdit jedit = new JaxeUndoableEdit(
                JaxeUndoableEdit.SUPPRIMER, this, str, offs);
        //install listener!!!
        jedit.doit();
        if (event) fireTextRemovedEvent(new JaxeEditEvent(this, offs, str));
//        textPane.finEditionSpeciale();
    }

    public void remove(int offs, int len) throws BadLocationException {
        remove(offs, len, true);
    }

    /**
     * @param offs
     * @param len
     * @param event
     * @throws BadLocationException
     */
    public void remove(int offs, int len, boolean event) throws BadLocationException {
        if (textPane.getIgnorerEdition()) {
            super.remove(offs, len);
            return;
        }
        if (!modif)
            modif = true;
        JaxeElement firstel = rootJE.elementA(offs);
        JaxeElement lastel = rootJE.elementA(offs + len - 1);
        if (firstel == lastel) {
            JaxeElement je = firstel;

            boolean avirer = false;
            if (je != null) {
            // si un JComponent est effacé, on efface tout le JaxeElement
                ArrayList compos = je.getComponentPositions();
                for (int i = 0; i < compos.size(); i++) {
                    int cp = ((Position) compos.get(i)).getOffset();
                    if (cp >= offs && cp < offs + len) {
                        avirer = true;
                        break;
                    }
                }
                // on efface aussi le JaxeElement s'il est entièrement dans la
                // sélection
                if (je.debut.getOffset() >= offs
                        && je.fin.getOffset() < offs + len) avirer = true;
                // ou si c'est un élément JESwing dont on efface le dernier
                // caractère
                if (je instanceof JESwing
                        && offs + len - 1 >= je.fin.getOffset()
                        && offs <= je.fin.getOffset()) {
                    while (je.getParent() != null
                            && je.getParent().fin.getOffset() == je.fin
                                    .getOffset())
                        je = je.getParent();
                    avirer = true;
                }
                if (avirer) {
                    if (!je.getEffacementAutorise()) { // SI c'est autorisé !
                        Toolkit.getDefaultToolkit().beep();
                        return;
                    }
                    if (je instanceof JESwing) {
                        JaxeElement parent = je;
                        while (parent != null && parent instanceof JESwing)
                            parent = parent.getParent();
                        // on efface tout le parent si c'est aussi un JESwing
                        je = parent;
                    }
                }
            }
            if (avirer) {
                if (je.getParent() == null) {
                    Toolkit.getDefaultToolkit().beep();
                    return;
                }
                // effacer aussi le parent s'il est exactement à la même position
                if (je.getParent().debut.getOffset() == je.debut.getOffset() &&
                    je.getParent().fin.getOffset() == je.fin.getOffset())
                    je = je.getParent();
                if (je.debut.getOffset() < offs) offs = je.debut.getOffset()+1;
                JaxeUndoableEdit e = new JaxeUndoableEdit(
                        JaxeUndoableEdit.SUPPRIMER, je);
                // on ne peut pas faire e.doit() tout de suite parce-que les
                // autres listeners doivent
                // être invoqués avant la modif
                //SwingUtilities.invokeLater(new ChangeRunnable(e));
                // invoquer plus tard pause problème quand on veut faire un
                // insertString juste après:
                // du coup il est fait avant...
                // finalement, ça a l'air de marcher avec e.doit(), alors on
                // essaie...
                e.doit();
                JaxeEditEvent jee = new JaxeEditEvent(this, je);
                if (event) fireElementRemovedEvent(jee);
                if (jee.isConsumed()) textPane.setCaretPosition(offs);
                textPane.miseAJourArbre();
            } else {
                /*if (je != null) {  retiré: fait dans JaxeUndoableEdit.effacer()
                    int finoff = je.fin.getOffset();
                    if (offs + len - 1 == finoff)
                        je.fin = createPosition(finoff - 1);
                }*/
                if (je instanceof JETexte || (je.debut.getOffset() == offs && !(je instanceof JESwing)))
                    je = je.getParent();
                if (!je.getEditionAutorisee()) {
                    errorHandler.textNotAllowed(je);
                    return;
                } else
                removeText(offs, len, event);
            }
        } else {
            //SwingUtilities.invokeLater(new SupRunnable(offs, len));
            // pour faire toutes les modifs (texte et élément) dans l'ordre, on est obligé de tout faire plus tard
            // tentative d'appel direct (c'est important pour ActionInsertionBalise,
            // qui doit insérer des éléments après en avoir supprimé)
            // question: sous quel environnement cela ne marche pas ?
            remove2(offs, len, event);
            textPane.miseAJourArbre();
        }
        _ignorer = false;
    }

    public void remove2(int offs, int len, boolean event) {
        try {
            JaxeElement firstel = rootJE.elementA(offs);
            JaxeElement lastel = rootJE.elementA(offs + len - 1);
            ArrayList l = rootJE.elementsDans(offs, offs + len - 1);
            for (int i = 0; i < l.size(); i++) {
                JaxeElement je = (JaxeElement) l.get(i);
                if (!_ignorer && !je.getEffacementAutorise()) {
                    Toolkit.getDefaultToolkit().beep();
                    return;
                }
            }
            textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString(
                    "annulation.Supprimer"), true);
            int lens2 = offs + len - lastel.debut.getOffset();
            if (firstel instanceof JETexte && l.indexOf(firstel) == -1) {
                String texte = firstel.noeud.getNodeValue();
                int lt = texte.length();
                texte = texte.substring(0, offs - firstel.debut.getOffset());
                firstel.noeud.setNodeValue(texte);
                removeText(offs, lt - texte.length(), event);
            }
            for (int i = 0; i < l.size(); i++) {
                JaxeElement je = (JaxeElement) l.get(i);
                // les textes peuvent être fusionnés et je.getParent devient null
                // -> utilisation de removeText
                if (je instanceof JETexte)
                    removeText(je.debut.getOffset(), je.fin.getOffset() - je.debut.getOffset() + 1, event);
                else if (je.getParent() != null) {
                    JaxeUndoableEdit e = new JaxeUndoableEdit(JaxeUndoableEdit.SUPPRIMER, je);
                    e.doit();
                    if (event) fireElementRemovedEvent(new JaxeEditEvent(this, je));
                }
            }
            if (lastel instanceof JETexte && l.indexOf(lastel) == -1) {
                String texte = lastel.noeud.getNodeValue();
                int lt = texte.length();
                texte = texte.substring(lens2);
                lastel.noeud.setNodeValue(texte);
                removeText(lastel.debut.getOffset(), lt - texte.length(), event);
                if (firstel instanceof JETexte && l.indexOf(firstel) == -1) {
                    // rassembler les deux zones de texte
                    firstel.fusionner(lastel);
                }
            }
            textPane.finEditionSpeciale();
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
    }

    public void enableIgnore() {
        _ignorer = true;
    }
    
    /*
     * class ChangeRunnable implements Runnable { JaxeUndoableEdit edit; public
     * ChangeRunnable(JaxeUndoableEdit e) { this.edit = e; } public void run() {
     * edit.doit(); textPane.miseAJourArbre(); } }
     */
    public void insertString(int offset, String str, AttributeSet a)
    throws BadLocationException {
        insertString(offset, str, a, true);
    }

    /*class SupRunnable implements Runnable {
        int offs;
        int len;
        public SupRunnable(int offs, int len) {
            this.offs = offs;
            this.len = len;
        }
	public void run() {
            remove2(offs, len);
            textPane.miseAJourArbre();
        }
    }*/

    public void insertString(int offset, String str, AttributeSet a, boolean event)
            throws BadLocationException {
        if (textPane.getIgnorerEdition()) {
            super.insertString(offset, str, a);
            return;
        }
        if (!modif) modif = true;

        int debut = textPane.getSelectionStart();
        int fin = textPane.getSelectionEnd();
        if (debut != fin) {
            // un appel à remove est généré automatiquement *après* l'appel à
            // insertString !
            // (probablement à cause du invokeLater dans remove)
            // on ne peut donc pas faire d'insertion quand il y a une
            // sélection...
            return;
        }

            JaxeElement je = elementA(offset);
            if (je == null) return;
            if (je instanceof JETexte
                    || (je.debut.getOffset() == offset && !(je instanceof JESwing)))
                    je = je.getParent();

            if (cfg != null) {
            Element jedef;
            if (je == null)
                jedef = null;
            else
                jedef = cfg.getElementDef((Element)je.noeud);
            if (jedef != null && ((!cfg.contientDuTexte(jedef) && !"".equals(str.trim())) ||
                    !je.getEditionAutorisee())) {
                errorHandler.textNotAllowed(je);
                return;
            }
        }
        
        //super.insertString(offset, str, a);
        /*if ("true".equals(Preferences.getPref().getProperty("consIndent")) &&
        	newline.equals(str)) {
            // ajout d'un espace comme celui de la ligne précédente en début de ligne
            int i1 = offset - 255;
            if (i1 < 0)
                i1 = 0;
            String extrait = textPane.getText(i1, offset - i1);
            i1 = extrait.lastIndexOf('\n');
            if (i1 != -1) {
                extrait = extrait.substring(i1 + 1);
                for (i1 = 0; i1 < extrait.length()
                        && (extrait.charAt(i1) == ' ' || extrait.charAt(i1) == '\t'); i1++)
                    ;
                str += extrait.substring(0, i1);
            }
        }*/
//        if (event) textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString(
//        "annulation.AnnulerAjout"), false);
        JaxeUndoableEdit jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER,
            this, str, offset);
        jedit.doit();
        if (event) {
            fireTextAddedEvent(new JaxeEditEvent(this, offset, str));
//            textPane.finEditionSpeciale();
        }

    }
    
    /**
     * Mise à jour des indentations après une suppression de \n (appelé par JaxeUndoableEdit)
     */
    protected void majIndentSupp(int offset) {
        JaxeElement je = elementA(offset);
        if (je != null) {
            if (je instanceof JETexte)
                je = je.getParent();
            if (je.avecIndentation()) {
                if (je.fin.getOffset() == offset) {
                    //if (!"true".equals(Preferences.getPref().getProperty("consIndent"))) {
                        Style s = textPane.addStyle(null, null);
                        StyleConstants.setLeftIndent(s, (float)20.0*je.indentations());
                        setParagraphAttributes(offset, 1, s, false);
                    //}
                    return;
                }
            }
        }
    }
    
    /**
     * Mise à jour des indentations après un ajout de \n (appelé par JaxeUndoableEdit)
     */
    protected void majIndentAjout(int offset) {
        //if (!"true".equals(Preferences.getPref().getProperty("consIndent"))) {
            JaxeElement je = elementA(offset-1);
            if (je != null) {
                if (je instanceof JETexte)
                    je = je.getParent();
                else if (!je.avecIndentation() && je.fin.getOffset() == offset - 1)
                    je = je.getParent();
                if (je.avecIndentation()) {
                    textPane.debutIgnorerEdition();
                    if (je.debut.getOffset() == offset-1 && je.fin.getOffset() > offset+1) {
                        Style s = textPane.addStyle(null, null);
                        StyleConstants.setLeftIndent(s, (float)20.0*(je.indentations()+1));
                        setParagraphAttributes(offset+1, 1, s, false);
                    } else if (je.fin.getOffset()-1 == offset &&
                            getParagraphElement(offset).getStartOffset() > je.debut.getOffset()) {
                        Style s = textPane.addStyle(null, null);
                        StyleConstants.setLeftIndent(s, (float)20.0*(je.indentations()+1));
                        setParagraphAttributes(offset, 1, s, false);
                    } else if (je.fin.getOffset() == offset-1 && je.getParent() != null &&
                            je.getParent().debut.getOffset() <
                            getParagraphElement(offset).getStartOffset()) {
                        Style s = textPane.addStyle(null, null);
                        StyleConstants.setLeftIndent(s, (float)20.0*je.indentations());
                        setParagraphAttributes(offset-1, 1, s, false);
                    }
                    textPane.finIgnorerEdition();
                }
                je = elementA(offset+1);
                if (je != null) {
                    if (je instanceof JETexte)
                        je = je.getParent();
                    if (je.avecIndentation() && je.fin.getOffset() == offset+1) {
                        textPane.debutIgnorerEdition();
                        Style s = textPane.addStyle(null, null);
                        StyleConstants.setLeftIndent(s, (float)20.0*je.indentations());
                        setParagraphAttributes(offset+1, 1, s, false);
                        textPane.finIgnorerEdition();
                    }
                }
            }
        //}
        
        /* Java 1.4 bug workaround:
           Components are not removed from the parent (and stay visible)
           when deleted after a \n is typed just before them,
           unless the paragraph view is manually updated */
        if (System.getProperty("java.version").startsWith("1.4")) {
            /*JaxeElement*/ je = elementA(offset+1);
            if (je != null) {
                ArrayList compos = je.getComponentPositions();
                for (int i=0; i<compos.size(); i++)
                    if (((Position)compos.get(i)).getOffset() == offset+1) {
                        textPane.debutIgnorerEdition();
                        Style s = textPane.addStyle(null, null);
                        setParagraphAttributes(offset, 1, s, false);
                        textPane.finIgnorerEdition();
                        break;
                    }
            }
        }
    }

    /* ne marche pas :(
    public void myInsertStuff(javax.swing.text.AbstractDocument.DefaultDocumentEvent chng,
            AttributeSet attr, int off, String str) {
        writeLock();
        try {
            try {
                UndoableEdit u = getContent().insertString(off, str);
                DefaultDocumentEvent e = 
                    new DefaultDocumentEvent(off, str.length(), DocumentEvent.EventType.INSERT);
                if (u != null) {
                    chng.addEdit(u);
                }
            } catch (BadLocationException ex) {
                ex.printStackTrace();
            }
            //buffer.insert(off, str.length(), data, chng);
            super.insertUpdate(chng, attr);
            chng.end();
            fireInsertUpdate(chng);
	    fireUndoableEditUpdate(new UndoableEditEvent(this, chng));
        } finally {
            writeUnlock();
        }
    }
    */
    
    public class SwingElementSpec {
        public String balise;
        public boolean branche;
        public String texte;
        int offset;
        public Vector enfants;
        SimpleAttributeSet att;

        public SwingElementSpec(String balise) {
            this.balise = balise;
            branche = true;
            texte = null;
            enfants = new Vector();
            att = null;
        }

        public SwingElementSpec(String balise, SimpleAttributeSet att) {
            this.balise = balise;
            branche = true;
            texte = null;
            enfants = new Vector();
            this.att = att;
        }

        public SwingElementSpec(String balise, int offset, String texte) {
            this.balise = balise;
            branche = false;
            this.offset = offset;
            this.texte = texte;
            enfants = null;
            att = null;
        }

        public void ajEnfant(SwingElementSpec enfant) {
            enfants.add(enfant);
        }

        public Vector getElementSpecs() {
            Vector specs = new Vector();
            if (!branche) {
                SimpleAttributeSet attcontent = new SimpleAttributeSet();
                attcontent.addAttribute(AbstractDocument.ElementNameAttribute,
                        "content");
                if (texte == null)
                    specs.add(new ElementSpec(attcontent,
                            ElementSpec.ContentType));
                else
                    specs.add(new ElementSpec(attcontent,
                            ElementSpec.ContentType, texte.toCharArray(),
                        offset, texte.length()));
            } else {
                SimpleAttributeSet att2 = new SimpleAttributeSet();
                if (att != null)
                    att2.addAttributes(att);
                att2.addAttribute(AbstractDocument.ElementNameAttribute, balise);
                specs.add(new ElementSpec(att2, ElementSpec.StartTagType));
                for (int i = 0; i < enfants.size(); i++) {
                    SwingElementSpec enfant = (SwingElementSpec) enfants.get(i);
                    specs.addAll(enfant.getElementSpecs());
                }
                if (att != null) {
                    att2 = new SimpleAttributeSet();
                    att2.addAttribute(AbstractDocument.ElementNameAttribute,
                            balise);
                }
                specs.add(new ElementSpec(att2, ElementSpec.EndTagType));
            }
            return (specs);
        }

        public String getTexteArbre() {
            if (branche) {
                String atexte = "";
                for (int i = 0; i < enfants.size(); i++) {
                    SwingElementSpec enfant = (SwingElementSpec) enfants.get(i);
                    String etexte = enfant.getTexteArbre();
                    if (etexte != null) atexte += etexte;
                }
                return (atexte);
            } else
                return (texte);
        }
    }

    public SwingElementSpec prepareSpec(String baliseSpec) {
        return (new SwingElementSpec(baliseSpec));
    }

    public SwingElementSpec prepareSpec(String baliseSpec,
            SimpleAttributeSet att) {
        return (new SwingElementSpec(baliseSpec, att));
    }

    public SwingElementSpec prepareSpec(String baliseSpec, int offset,
            String texte) {
        return (new SwingElementSpec(baliseSpec, offset, texte));
    }

    public void sousSpec(SwingElementSpec parentspec,
            SwingElementSpec enfantspec) {
        parentspec.ajEnfant(enfantspec);
    }

    public javax.swing.text.Element insereSpec(SwingElementSpec jspec,
            int offset) {
        Vector vspecs = jspec.getElementSpecs();
        ElementSpec[] es = new ElementSpec[vspecs.size()];
        for (int i = 0; i < vspecs.size(); i++)
            es[i] = (ElementSpec) vspecs.get(i);

        String texte = jspec.getTexteArbre();

        writeLock();
        try {
            DefaultDocumentEvent evnt = null;
            try {
                UndoableEdit cEdit = getContent().insertString(offset, texte);
                evnt = new DefaultDocumentEvent(offset, texte.length(),
                        DocumentEvent.EventType.INSERT);
                evnt.addEdit(cEdit);
            } catch (BadLocationException ex) {
                ex.printStackTrace();
            }
            buffer.insert(offset, texte.length(), es, evnt);
            // update bidi (possibly)
            //AbstractDocument.super.insertUpdate(evnt, null);
            // notify the listeners
            evnt.end();
            fireInsertUpdate(evnt);
            fireUndoableEditUpdate(new UndoableEditEvent(this, evnt));
        } finally {
            writeUnlock();
        }
        return (elementTexteA(jspec.balise, offset));
    }

    public javax.swing.text.Element elementTexteA(String nom, int offset) {
        BranchElement branche = (BranchElement) getDefaultRootElement();
        while (branche != null && branche.getStartOffset() != offset) {
            javax.swing.text.Element el = branche.positionToElement(offset);
            if (el instanceof BranchElement)
                branche = (BranchElement) el;
            else
                branche = null;
        }
        return (branche);
    }

    public EditorKit createEditorKit() {
        return (new JaxeEditorKit());
    }

    class JaxeEditorKit extends StyledEditorKit {

        protected ViewFactory myViewFactory;

        public JaxeEditorKit() {
            super();
            myViewFactory = new JaxeViewFactory();
        }

        public ViewFactory getViewFactory() {
            return (myViewFactory);
        }
    }

    class JaxeViewFactory implements ViewFactory {

        public View create(javax.swing.text.Element elem) {
            String kind = elem.getName();
            if (kind != null) {
                if (kind.equals(AbstractDocument.ContentElementName)) {
                    return new LabelView(elem);
                } else if (kind.equals(AbstractDocument.ParagraphElementName)) {
                    //return new JaxeSpecialParagraph(elem);
                    return new ParagraphView(elem);
                } else if (kind.equals(AbstractDocument.SectionElementName)) {
                    return new BoxView(elem, View.Y_AXIS);
                } else if (kind.equals(StyleConstants.ComponentElementName)) {
                    return new ComponentView(elem);
                } else if (kind.equals(StyleConstants.IconElementName)) {
                    return new IconView(elem);
                } else if (kind.equals("table")) { return new JaxeTableView(
                        elem); }
            }

            // default to text display
            return new LabelView(elem);
        }
    }

    /*class JaxeSpecialParagraph extends ParagraphView {
        
        public JaxeSpecialParagraph(javax.swing.text.Element elem) {
            super(elem);
            setInsets((short)3, (short)3, (short)3, (short)3);
        }
        
        public void paint(Graphics g, Shape allocation) {
            super.paint(g, allocation);
            Rectangle alloc = (allocation instanceof Rectangle) ?
	                   (Rectangle)allocation : allocation.getBounds();
            g.setColor(Color.red);
            g.drawRect(alloc.x, alloc.y, alloc.width-1, alloc.height-1);
            g.setColor(Color.black);
        }
    }*/
    
    /*class MyUndoableEditListener implements UndoableEditListener {
        public void undoableEditHappened(UndoableEditEvent e) {
            if (e.getEdit() instanceof JaxeUndoableEdit) {
                ((JaxeUndoableEdit)(e.getEdit())).doit();
            }
        }
    }*/
    
    // swing bug fix (doesn't work)
    /*public void insertString(int offs, String str, AttributeSet a) throws BadLocationException {
        super.insertString(offs, str, a);
        if (a == null)
            return;
        Component c = StyleConstants.getComponent(a);
        if (c != null && c.getParent() != null && c instanceof JComponent)
            invalidateComponentLayout((JComponent)c);
    }*/
    
    /** Java Bug Parade n° 4353673 */
    public void fixbug(Component comp) {
        if (System.getProperty("java.version").startsWith("1.3")/* ||
            System.getProperty("java.version").startsWith("1.4.2")*/)
        // le bug est de retour avec Java 1.4.2 !!!  (n° 4839979) (cf JaxeTextPane.add)
            comp.addHierarchyListener(new MyHierarchyListener(comp, textPane));
    }
    
    // bug workaround from the Java Bug Parade, adapted to JComponent
    // doesn't work with undo -> using MyHierarchyListener instead
    /**
    * Clear cached size calculations to work around
    * ArrayIndexOutOfBounds exception
    * in SizeRequirements. Button is a JComponent
    * that has its own internal OverlayLayout that
    * must be invalidated.
    */
    /*
    public static void invalidateComponentLayout(JComponent comp) {
        LayoutManager layout = comp.getParent().getLayout();
	if(layout instanceof OverlayLayout) {
            ((OverlayLayout)layout).invalidateLayout((Container)(comp.getParent()));
	}
    }
    */
    
    public void styleChanged() { // another bug fix (see Jaxe)
        styleChanged(null);
    }
    
    /*public void imageChanged(int offset) { // another UGLY (Windows/Linux) bug workaround
        // to force a ParagraphView update
        // problem 1: causes a ArrayIndexOutOfBoundsException with Sun's JVM on Linux
        // problem 2: moves the view to wherever the caret is
        textPane.debutIgnorerEdition();
        try {
            super.insertString(offset, "\n", null);
            super.remove(offset, 1);
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
        }
        textPane.finIgnorerEdition();
    }*/
    
    public void imageChanged(JComponent comp) { // yet another UGLY bug workaround
        Container cont = comp.getParent();
        if (cont.getLayout() == null)
            cont.setLayout(new OverlayLayout(cont));
        cont.validate();
    }

    /**
     * Returns the JaxeElement that represents the Node
     * @param node get the JaxeElement for this Node
     * @return The representation for the given Node
     */
    public JaxeElement getElementForNode(Node node) {
        if (node == null)
            return null;
        else
            return (JaxeElement)dom2JaxeElement.get(node);
    }
    
    /**
     * Adds a listener for editevents
     * @param edit Listener to add
     */
    public void addEditListener(JaxeEditListenerIf edit) {
        _editListener.add(edit);
    }
    
    /**
     * Removes a listener for editevents
     * @param edit Listener to remove
     */
    public void removeEditListener(JaxeEditListenerIf edit) {
        _editListener.remove(edit);
    }
    
    /**
     * Fires an event for removing text to all listeners
     * @param event Event to send
     */
    protected void fireTextRemovedEvent(JaxeEditEvent event) {
        Iterator it = _editListener.iterator();
        while (it.hasNext()) {
            JaxeEditListenerIf l = (JaxeEditListenerIf)it.next();
            l.textRemoved(event);
        }
    }
    
    /**
     * Fires an event for removing JaxeElements to all listeners
     * @param event Event to send
     */
    protected void fireElementRemovedEvent(JaxeEditEvent event) {
        Iterator it = _editListener.iterator();
        while (it.hasNext()) {
            JaxeEditListenerIf l = (JaxeEditListenerIf)it.next();
            l.elementRemoved(event);
        }
    }
    
    /**
     * Fires an event for adding text to all listeners
     * @param event Event to send
     */
    protected void fireTextAddedEvent(JaxeEditEvent event) {
        Iterator it = _editListener.iterator();
        while (it.hasNext()) {
            JaxeEditListenerIf l = (JaxeEditListenerIf)it.next();
            l.textAdded(event);
        }
    }
    
    /**
     * Fires an event for adding JaxeElements to all listeners an returns a possible new insert position
     * @param event Event to send
     * @param pos Position element will be added
     * @return New position of insert
     */
    protected Position fireElementAddedEvent(JaxeEditEvent event, Position pos) {
        Iterator it = _editListener.iterator();
        while (it.hasNext()) {
            JaxeEditListenerIf l = (JaxeEditListenerIf)it.next();
            pos = l.elementAdded(event, pos);
        }
        return pos;
    }

    /**
     * Fires an event to prepare the position a JaxeElement will be added and returns a possible
     * new instert position
     * @param pos Position to prepare
     * @return New position of insert
     */
    protected Position firePrepareElementAddEvent(Position pos) {
        Iterator it = _editListener.iterator();
        while (it.hasNext()) {
            JaxeEditListenerIf l = (JaxeEditListenerIf)it.next();
            pos = l.prepareAddedElement(pos);
        }
        return pos;
    }
}