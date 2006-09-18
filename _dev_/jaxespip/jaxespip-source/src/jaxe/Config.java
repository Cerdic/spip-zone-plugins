/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.Toolkit;
import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Hashtable;
import java.util.MissingResourceException;
import java.util.ResourceBundle;

import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JMenuItem;
import javax.swing.KeyStroke;
import javax.swing.text.Position;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.ProcessingInstruction;

/*
3 regular expression matching libraries have been tested.
code is left as comment since it could be reused
- jakarta-regexp (1.2) quickly gets StackOverflowException, and generates
    RESyntaxException: Syntax error: Closure operand can't be nullable
- jakarta-oro-awk (2.0.8) is the fastest but a bit big; limited to awk regular expressions;
    limited to 8-bit ASCII
- gnu.regexp (1.1.4) is a bit slow
*/

//jakarta-regexp
//import org.apache.regexp.RE;
//import org.apache.regexp.RESyntaxException;

//jakarta-oro
import org.apache.oro.text.regex.*;
import org.apache.oro.text.awk.*;

//gnu.regexp
//import gnu.regexp.*;

/**
 * Gestion du fichier de configuration et du fichier de schéma XML
 */
public class Config {
    public static final String ELEM_FICHER_TITRES = "FICHIERTITRES";
    
    static String newline = "\n";
    public Element jaxecfg;
    public Element schema;
    public Hashtable fichierXSL2Parametres ;
    String schemaNamespace;
    public String targetNamespace;
    String cfgdir;
    public URL schemaURL;
    String namespacecfg;
    
    // liste des éléments avec un attribut name (en général sous xs:schema), avec les inclusions éventuelles
    // (Element ArrayList)
    ArrayList lelements;
    ArrayList lgroups;
    ArrayList lcomptypes;
    ArrayList lsimptypes;
    ArrayList lattgroups;
    ArrayList lextensions;
    
    ArrayList nomsElements; // String ArrayList  synchro avec lelements
    
    // liste de tous les éléments (pas forcément directement sous xs:schema)
    ArrayList ltouselements;
    ArrayList ltousgroups;
    ArrayList ltousextensions;
    
    Hashtable cacheBaliseDef; // cache des associations nombalise -> définition
    Hashtable cacheNomsBalises; // cache des associations définition -> nombalise
    Hashtable cacheInsertion = null; // cache des expressions régulières pour les insertions
    Hashtable cacheSubst; // cache pour ajSubst
    Hashtable cacheParametres = null;
    
    String schemadir = null; // répertoire du schéma principal
    ArrayList fichiersInclus;

    ArrayList autresConfigs;
    
    // jakarta-oro
    PatternCompiler compiler;
    PatternMatcher matcher;

    ResourceBundle resourceTitres;

    public Config(String nomFichierCfg, boolean lireSchema) {
        super();
        try {
            initialisation(new URL(nomFichierCfg), lireSchema);
        } catch (MalformedURLException ex) {
            System.err.println("MalformedURLException: " + ex.getMessage());
        }
    }
    
    public Config(URL urlFichierCfg, boolean lireSchema) {
        super();
        initialisation(urlFichierCfg, lireSchema);
    }
    
    private void initialisation(URL urlFichierCfg, boolean lireSchema) {
        if (urlFichierCfg == null) {
            jaxecfg = null;
            return;
        }
        fichierXSL2Parametres = new Hashtable() ;
        
        // jakarta-oro
        compiler = new AwkCompiler();
        matcher = new AwkMatcher();
        
        Document configdoc;
        try {
            DocumentBuilderFactory docFactory = DocumentBuilderFactory.newInstance();
            docFactory.setNamespaceAware(true);
            DocumentBuilder docbuilder = docFactory.newDocumentBuilder();
            configdoc = docbuilder.parse(urlFichierCfg.toExternalForm());
        } catch (Exception e) {
            e.printStackTrace(System.err);
            return;
        }
        jaxecfg = configdoc.getDocumentElement();
        
        int index = urlFichierCfg.toExternalForm().lastIndexOf("/");
        if (index >= 0)
            cfgdir = urlFichierCfg.toExternalForm().substring(0, index);
        else
            cfgdir = "";
        
        autresConfigs = new ArrayList();
        NodeList lconfig = jaxecfg.getElementsByTagName("CONFIG");
        for (int i=0; i<lconfig.getLength(); i++) {
            Element elconfig = (Element)lconfig.item(i);
            URL urlAutreConfig;
            try {
                if ("".equals(cfgdir))
                    urlAutreConfig = new URL(elconfig.getAttribute("nom"));
                else
                    urlAutreConfig = new URL(cfgdir + "/" + elconfig.getAttribute("nom"));
            } catch (MalformedURLException ex) {
                System.err.println("MalformedURLException: " + ex.getMessage());
                urlAutreConfig = null;
            }
            Config autreConfig = new Config(urlAutreConfig, true);
            autresConfigs.add(autreConfig);
        }
        
        construireCacheBaliseDef();
        namespacecfg = chercherNamespace();
        
        // Getting the bundle according to locale for resolving labels
        String resource = getResource();
        if (null == resource) {
            resourceTitres = null;
        }
        else {
            resourceTitres = ResourceBundle.getBundle(resource);
        }
        
        String noms = nomSchema();
        if (noms == null) {
            //System.err.println("Aucune balise FICHIERSCHEMA dans " + urlFichierCfg + " ?");
            return;
        }
        try {
            if (!"".equals(cfgdir))
                schemaURL = new URL(cfgdir + "/" + noms);
            else
                schemaURL = new URL(noms);
        } catch (MalformedURLException ex) {
            System.err.println("MalformedURLException: " + ex.getMessage());
        }
        URL urls = schemaURL;
        schemadir = cfgdir;
        if (noms == null || !lireSchema)
            schema = null;
        else {
            lelements = new ArrayList();
            lgroups = new ArrayList();
            lcomptypes = new ArrayList();
            lsimptypes = new ArrayList();
            lattgroups = new ArrayList();
            lextensions = new ArrayList();
            
            ltouselements = new ArrayList();
            ltousgroups = new ArrayList();
            ltousextensions = new ArrayList();
            
            fichiersInclus = new ArrayList();
            schemaNamespace = null;
            targetNamespace = null;
            schema = inclusion1(urls);
            
            nomsElements = new ArrayList();
            for (int i=0; i<lelements.size(); i++)
                nomsElements.add(((Element)lelements.get(i)).getAttribute("name"));
            cacheSubst = new Hashtable();
        }
    }
    
    protected Element inclusion1(URL urls) {
        if (fichiersInclus.indexOf(urls) != -1)
            return(null);
        fichiersInclus.add(urls);
        Document schemadoc;
        try {
            DocumentBuilderFactory docFactory = DocumentBuilderFactory.newInstance();
            docFactory.setNamespaceAware(true);
            DocumentBuilder docbuilder = docFactory.newDocumentBuilder();
            schemadoc = docbuilder.parse(urls.toExternalForm());
        } catch (Exception e) {
            e.printStackTrace(System.err);
            return(null);
        }
        Element schema2 = schemadoc.getDocumentElement();
        if (schemaNamespace == null)
            schemaNamespace = schema2.getNamespaceURI();
        if (targetNamespace == null) {
            targetNamespace = schema2.getAttribute("targetNamespace");
            /*
            if (!"".equals(targetNamespace) && !targetNamespace.equals(namespacecfg))
                System.err.println(targetNamespace + " != " + namespacecfg + " !");
            */ // warning retiré parce-qu'il est maintenant possible de mélanger les espaces de noms
        }
        inclusion2(schema2);
        return(schema2);
    }
    
    protected ArrayList enfants(Element parent, String tag) {
        ArrayList liste = new ArrayList();
        NodeList lsousb = parent.getChildNodes();
        for (int i=0; i<lsousb.getLength(); i++) {
            if (lsousb.item(i) instanceof Element) {
                Element sousb = (Element)lsousb.item(i);
                if (tag.equals(sousb.getLocalName()))
                    liste.add(sousb);
            }
        }
        return(liste);
    }
    
    protected void addNodeList(ArrayList l, NodeList nl) {
        for (int i=0; i<nl.getLength(); i++)
            l.add(nl.item(i));
    }
    
    protected ArrayList listeTous(Element parent, String tag) {
        ArrayList liste = new ArrayList();
        NodeList lbalises = parent.getElementsByTagNameNS(schemaNamespace, tag);
        //NodeList lbalises = parent.getElementsByTagName("xs:"+tag);
        addNodeList(liste, lbalises);
        return(liste);
    }
    
    protected void inclusion2(Element sch) {
        ltouselements.addAll(listeTous(sch, "element"));
        ltousgroups.addAll(listeTous(sch, "group"));
        ltousextensions.addAll(listeTous(sch, "extension"));

        for (int i=0; i<ltouselements.size(); i++)
            if (!"".equals(((Element)ltouselements.get(i)).getAttribute("name")))
                lelements.add(ltouselements.get(i));
        for (int i=0; i<ltousgroups.size(); i++)
            if (!"".equals(((Element)ltousgroups.get(i)).getAttribute("name")))
                lgroups.add(ltousgroups.get(i));
        lcomptypes.addAll(enfants(sch, "complexType"));
        lsimptypes.addAll(enfants(sch, "simpleType"));
        ArrayList ltousattgroups = listeTous(sch, "attributeGroup");
        for (int i=0; i<ltousattgroups.size(); i++)
            if (!"".equals(((Element)ltousattgroups.get(i)).getAttribute("name")))
                lattgroups.add(ltousattgroups.get(i));
        lextensions.addAll(enfants(sch, "extension"));
        
        ArrayList linc = enfants(sch, "include");
        for (int i=0; i<linc.size(); i++) {
            Element inc = (Element)linc.get(i);
            String noms = inc.getAttribute("schemaLocation");
            URL urls;
            try {
                if (!"".equals(cfgdir))
                    urls = new URL(cfgdir + "/" + noms);
                else
                    urls = new URL(noms);
            } catch (MalformedURLException ex) {
                System.err.println("MalformedURLException: " + ex.getMessage());
                urls = null;
            }
            inclusion1(urls);
        }
    }
    
    protected JMenu creationMenu(JaxeDocument doc, Element menudef) {
        String titreMenu = menudef.getAttribute("titre");
        if (resourceTitres != null) {
            try {
                titreMenu = resourceTitres.getString(titreMenu);
            } catch (MissingResourceException ex) {
            }
        }
        JMenu jmenu = new JMenu(titreMenu);
        NodeList lmenusitems = menudef.getChildNodes();
        for (int i=0; i<lmenusitems.getLength(); i++) {
            Node menunode = lmenusitems.item(i);
            JMenuItem item = null;
            String nodename = menunode.getNodeName();
            if ("BALISE".equals(nodename)) {
                Element balise = (Element)menunode;
                boolean cache = ("true".equals(balise.getAttribute("cache"))); // menu caché
                if (!cache) {
                    item = jmenu.add(new ActionInsertionBalise(doc, balise));
                    String itemdoc = documentation(balise);
                    if (itemdoc != null)
                        item.setToolTipText(itemdoc);
                }
            } else if ("FONCTION".equals(nodename)) {
                Element fonction = (Element)menunode;
                String classe = fonction.getAttribute("classe");
                String titre = fonction.getAttribute("titre");
                if (resourceTitres != null)
                    titre = resourceTitres.getString(titre);
                item = jmenu.add(new ActionFonction(doc, titre, classe));
            } else if ("MENU".equals(nodename)) {
                item = creationMenu(doc, (Element)menunode);
                jmenu.add(item);
            }
            
            if (item != null) {
                String commande = ((Element)menunode).getAttribute("commande");
                if (commande != null && !"".equals(commande)) {
                    char c = commande.toUpperCase().charAt(0);
                    int cmdMenu = Toolkit.getDefaultToolkit().getMenuShortcutKeyMask();
                    item.setAccelerator(KeyStroke.getKeyStroke(c, cmdMenu));
                }
            }
        }
        return(jmenu);
    }
    
    public JMenuBar makeMenus(JaxeDocument doc) {
        JMenuBar barreBalises = new JMenuBar();
        
        NodeList nl = jaxecfg.getChildNodes();
        for (int i=0; i<nl.getLength(); i++)
            if (nl.item(i).getNodeType() == Node.ELEMENT_NODE &&
                    "MENU".equals(nl.item(i).getNodeName())) {
                Element menudef = (Element)nl.item(i);
                JMenu jmenu = creationMenu(doc, menudef);
                barreBalises.add(jmenu);
            }
        
        for (int i=0; i<autresConfigs.size(); i++) {
            Config conf = (Config)autresConfigs.get(i);
            JMenuBar mbar = conf.makeMenus(doc);
            while (mbar.getMenuCount() > 0) {
                JMenu menu = mbar.getMenu(0);
                mbar.remove(menu);
                barreBalises.add(menu);
            }
        }
        return(barreBalises);
    }
    
    public String description() {
        String desc = null;
        if (resourceTitres != null) {
            try {
                desc = resourceTitres.getString("description_config");
            } catch (MissingResourceException ex) {
            }
        }
        if (desc == null) {
            NodeList nl = jaxecfg.getElementsByTagName("DESCRIPTION");
            if (nl == null || nl.getLength() == 0)
                return(null);
            Element descel = (Element)nl.item(0);
            if (descel.getFirstChild() == null)
                return(null);
            desc = descel.getFirstChild().getNodeValue().trim();
        }
        return(desc);
    }

    public Element racine() {
        NodeList nl = jaxecfg.getElementsByTagName("RACINE");
        Element racine = (Element)nl.item(0);
        nl = racine.getElementsByTagName("BALISE");
        Element balise = (Element)nl.item(0);
        return(balise);
    }
    
    /**
     * Retourne la liste des noms des éléments racines possibles
     */
    public ArrayList listeRacines() {
        ArrayList liste = new ArrayList();
        NodeList lracine = jaxecfg.getElementsByTagName("RACINE");
        for (int i=0; i<lracine.getLength(); i++) {
            Element racine = (Element)lracine.item(i);
            NodeList lbalise = racine.getElementsByTagName("BALISE");
            if (lbalise.getLength() > 0)
                liste.add(nomBalise((Element)lbalise.item(0)));
        }
        return(liste);
    }
    
    public String chercherNamespace() {
        NodeList nl = jaxecfg.getElementsByTagName("ESPACE");
        if (nl == null || nl.getLength() == 0)
            return(null);
        Element espace = (Element)nl.item(0);
        String uri = espace.getAttribute("uri");
        if ("".equals(uri))
            uri = null;
        return(uri);
    }
    
    public String namespace() {
        return(namespacecfg);
    }
    
    public String prefixe() {
        NodeList nl = jaxecfg.getElementsByTagName("ESPACE");
        if (nl == null || nl.getLength() == 0)
            return(null);
        Element espace = (Element)nl.item(0);
        String pref = espace.getAttribute("prefixe");
        if ("".equals(pref))
            pref = null;
        return(pref);
    }
    
    /** Return the name of the resource bundle to use.
     *
     * @return the name of the resource bundle, null if not defined.
     */
    public String getResource() {
        NodeList nl = jaxecfg.getElementsByTagName(ELEM_FICHER_TITRES);
        if (nl == null || nl.getLength() == 0)
            return(null);
        Element bundle = (Element)nl.item(0);
        return(bundle.getAttribute("nom"));
    }
    
    public String nomSchema() {
        NodeList nl = jaxecfg.getElementsByTagName("FICHIERSCHEMA");
        if (nl == null || nl.getLength() == 0)
            return(null);
        Element schema = (Element)nl.item(0);
        return(schema.getAttribute("nom"));
    }
    
    public String nomBalise(Element balisedef) {
        return((String)cacheNomsBalises.get(balisedef));
    }
    
    public String typeBalise(Element balisedef) {
        return(balisedef.getAttribute("type"));
    }

    public String noeudtypeBalise(Element balisedef) {
        return(balisedef.getAttribute("noeudtype"));
    }
    
    public String titreBalise(Element balisedef) {
        String titre = balisedef.getAttribute("titre");
        if (resourceTitres == null) {
            if ("".equals(titre))
                titre = balisedef.getAttribute("nom");
        } else {
            if ("".equals(titre)) {
                try {
                    titre = resourceTitres.getString(balisedef.getAttribute("nom"));
                } catch (MissingResourceException ex) {
                    titre = balisedef.getAttribute("nom");
                }
            } else {
                try {
                    titre = resourceTitres.getString(titre);
                } catch (MissingResourceException ex) {
                }
            }
        }
        return(titre);
    }
    
    protected Hashtable construireCacheBaliseDef() {
        cacheBaliseDef = new Hashtable();
        cacheNomsBalises = new Hashtable();
        if (jaxecfg == null)
            return(cacheBaliseDef);
        NodeList lbalises = jaxecfg.getElementsByTagName("BALISE");
        for (int j=0; j<lbalises.getLength(); j++) {
            Element balise = (Element)lbalises.item(j);
            String nom = balise.getAttribute("nom");
            cacheBaliseDef.put(nom, balise);
            cacheNomsBalises.put(balise, nom);
        }
        for (int i=0; i<autresConfigs.size(); i++) {
            Config conf = (Config)autresConfigs.get(i);
            cacheNomsBalises.putAll(conf.cacheNomsBalises);
        }
        return(cacheBaliseDef);
    }
    
    /**
     * Renvoit la définition du premier élément du fichier de config dont le nom est celui indiqué.
     * Attention: à n'utiliser que si on est sûr que l'élément est définit dans cette configuration.
     */
    public Element getBaliseDef(String nombalise) {
        return((Element)cacheBaliseDef.get(localValue(nombalise)));
    }
    
    /**
     * Renvoit la définition du premier élément du fichier de config correspondant,
     * en regardant dans les autres configurations si nécessaire.
     */
    public Element getElementDef(Element el) {
        Config conf = getElementConf(el);
        if (conf == this) {
            String nom;
            if (el.getPrefix() == null)
                nom = el.getNodeName();
            else
                nom = el.getLocalName();
            return((Element)cacheBaliseDef.get(nom));
        } else if (conf != null)
            return(conf.getElementDef(el));
        else
            return(null);
    }

    public Element getProcessingDef(ProcessingInstruction el) {
        Config conf = getProcessingConf(el);
        if (conf == this) {
            String nom;
            if (el.getPrefix() == null)
                nom = el.getNodeName();
            else
                nom = el.getLocalName();
            return((Element)cacheBaliseDef.get(nom));
        } else if (conf != null)
            return(conf.getProcessingDef(el));
        else
            return(null);
    }

    /**
     * renvoit la définition du premier élément du fichier de config dont le nom et le type sont ceux indiqués
     */
    public Element getBaliseNomType(String nombalise, String typebalise) {
        if (jaxecfg == null)
            return(null);
        String nombalise2 = localValue(nombalise);
        NodeList lbalises = jaxecfg.getElementsByTagName("BALISE");
        for (int j=0; j<lbalises.getLength(); j++) {
            Element balise = (Element)lbalises.item(j);
            if (nombalise2.equals(balise.getAttribute("nom")) && typebalise.equals(balise.getAttribute("type")))
                return(balise);
        }
        return(null);
    }
    
    /**
     * renvoit la définition du premier élément du fichier de config dont le type est celui indiqué
     */
    public Element getBaliseAvecType(String typebalise) {
        if (jaxecfg == null)
            return(null);
        NodeList lbalises = jaxecfg.getElementsByTagName("BALISE");
        for (int j=0; j<lbalises.getLength(); j++) {
            Element balise = (Element)lbalises.item(j);
            if (typebalise.equals(balise.getAttribute("type")))
                return(balise);
        }
        return(null);
    }
    
    public Element schemaBaliseDef(String nombalise) {
        if (schema == null)
            return(null);
        for (int i=0; i<lelements.size(); i++) {
            if (nombalise.equals((String)nomsElements.get(i)))
                return((Element)lelements.get(i));
        }
        if (autresConfigs.size() > 0) {
            Config conf = getBaliseConf(nombalise);
            if (conf != null)
                return(conf.schemaBaliseDef(nombalise));
        }
        return(null);
    }
    
    /**
     * Renvoit la config correspondant à un nom d'élément.
     * Attention: peut être ambiguë si le nom n'a pas de préfixe.
     * Il est donc préférable d'utiliser getDefConf et getElementConf à la place.
     */
    public Config getBaliseConf(String nombalise) {
        if (autresConfigs.size() == 0)
            return(this);
        int inds = nombalise.indexOf(':');
        if (inds != -1) {
            String prefixe = nombalise.substring(0, inds);
            for (int i=0; i<autresConfigs.size(); i++) {
                Config conf = (Config)autresConfigs.get(i);
                if (prefixe.equals(conf.prefixe()))
                    return(conf);
            }
            nombalise = nombalise.substring(inds+1);
        }
        NodeList lbalises = jaxecfg.getElementsByTagName("BALISE");
        for (int i=0; i<lbalises.getLength(); i++)
            if (nombalise.equals(nomBalise((Element)lbalises.item(i))))
                return(this);
        for (int i=0; i<autresConfigs.size(); i++) {
            Config conf = (Config)autresConfigs.get(i);
            lbalises = conf.jaxecfg.getElementsByTagName("BALISE");
            for (int j=0; j<lbalises.getLength(); j++)
                if (nombalise.equals(conf.nomBalise((Element)lbalises.item(j))))
                    return(conf);
        }
        System.err.println("erreur: config introuvable pour " + nombalise);
        return(null);
    }
    
    /**
     * Renvoit la config correspondant à une définition d'élément du fichier de config.
     */
    public Config getDefConf(Element defbalise) {
        Document domdoc = defbalise.getOwnerDocument();
        if (domdoc == jaxecfg.getOwnerDocument())
            return(this);
        for (int i=0; i<autresConfigs.size(); i++) {
            Config conf = (Config)autresConfigs.get(i);
            if (domdoc == conf.jaxecfg.getOwnerDocument())
                return(conf);
        }
        System.err.println("attention: pas de config trouvée pour " + nomBalise(defbalise));
        return(null);
    }
    
    /**
     * Renvoit la config correspondant à un élément du document XML.
     */
    public Config getElementConf(Element el) {
        String ns = el.getNamespaceURI();
        if ((ns != null && ns.equals(targetNamespace)) ||
                (ns == null && (targetNamespace == null || targetNamespace.equals(""))))
            return(this);
        for (int i=0; i<autresConfigs.size(); i++) {
            Config conf = (Config)autresConfigs.get(i);
            if ((ns != null && ns.equals(conf.targetNamespace)) ||
                    (ns == null && (conf.targetNamespace == null || conf.targetNamespace.equals(""))))
                return(conf);
        }
        System.err.println("attention: pas de config trouvée pour " + el.getNodeName());
        System.err.println("espace élément: " + ns);
        System.err.println("espace cible de la config: " + targetNamespace);
        return(null);
    }

    public Config getProcessingConf(ProcessingInstruction el) {
        String ns = el.getNamespaceURI();
        if ((ns != null && ns.equals(targetNamespace)) ||
                (ns == null && (targetNamespace == null || targetNamespace.equals(""))))
            return(this);
        for (int i=0; i<autresConfigs.size(); i++) {
            Config conf = (Config)autresConfigs.get(i);
            if ((ns != null && ns.equals(conf.targetNamespace)) ||
                    (ns == null && (conf.targetNamespace == null || conf.targetNamespace.equals(""))))
                return(conf);
        }
        System.err.println("attention: pas de config trouvée pour " + el.getNodeName());
        System.err.println("espace élément: " + ns);
        System.err.println("espace cible de la config: " + targetNamespace);
        return(null);
    }

    public boolean sousbalise(Element parentdef, String nombalise) {
        int inds = nombalise.indexOf(':');
        if (inds != -1)
            nombalise = nombalise.substring(inds+1);
        ArrayList lsousb = listeSousbalises(parentdef);
        for (int i=0; i<lsousb.size(); i++)
            if (nombalise.equals((String)lsousb.get(i)))
                return(true);
        return(false);
    }
    
    public static String localValue(String s) {
        if (s == null)
            return(null);
        int ind = s.indexOf(':');
        if (ind == -1)
            return(s);
        else
            return(s.substring(ind + 1));
    }
    
    protected void ajSubst(Element el, String nomel, ArrayList liste) {
        ArrayList l = (ArrayList)cacheSubst.get(nomel);
        if (l != null)
            liste.addAll(l);
        else {
            l = new ArrayList();
            ajSubst2(el, nomel, l);
            cacheSubst.put(nomel, l);
            liste.addAll(l);
        }
    }
    
    protected void ajSubst2(Element el, String nomel, ArrayList liste) {
        if (!"true".equals(localValue(el.getAttribute("abstract"))))
            liste.add(nomel);
        for (int i=0; i<lelements.size(); i++) {
            Element el2 =(Element)lelements.get(i);
            String nom2 = (String)nomsElements.get(i);
            if (!"".equals(nom2) &&
                nomel.equals(localValue(el2.getAttribute("substitutionGroup")))) {
                ajSubst2(el2, nom2, liste);
            }
        }
    }
    
    protected void retirerDoublons(ArrayList liste) {
        for (int i=0; i<liste.size()-1; i++) {
            String s1 = (String)liste.get(i);
            for (int j=i+1; j<liste.size(); j++)
                if (s1.equals((String)liste.get(j))) {
                    liste.remove(j);
                    j--;
                }
        }
    }
    
    protected Element chercherElement(String nom) {
        for (int i=0; i<lelements.size(); i++) {
            if (nom.equals((String)nomsElements.get(i))) {
                return((Element)lelements.get(i));
            }
        }
        return(null);
    }
    
    protected ArrayList sListeSousbalises(Element sparent) {
        ArrayList liste = new ArrayList();
        String nombalise = sparent.getLocalName();
        if (nombalise.equals("element") && !"".equals(sparent.getAttribute("type"))) {
            String stype = localValue(sparent.getAttribute("type"));
            for (int i=0; i<lcomptypes.size(); i++) {
                Element ct =(Element)lcomptypes.get(i);
                if (stype.equals(ct.getAttribute("name")))
                    liste.addAll(sListeSousbalises(ct));
            }
        } else if (nombalise.equals("group") && !"".equals(sparent.getAttribute("ref"))) {
            String sref = localValue(sparent.getAttribute("ref"));
            for (int i=0; i<lgroups.size(); i++) {
                Element ct =(Element)lgroups.get(i);
                if (sref.equals(ct.getAttribute("name")))
                    liste.addAll(sListeSousbalises(ct));
            }
        } else {
            if (nombalise.equals("extension") && !"".equals(sparent.getAttribute("base"))) {
                String sbase = localValue(sparent.getAttribute("base"));
                for (int i=0; i<lcomptypes.size(); i++) {
                    Element ct =(Element)lcomptypes.get(i);
                    if (sbase.equals(ct.getAttribute("name")))
                        liste.addAll(sListeSousbalises(ct));
                }
            }
            NodeList lsousb = sparent.getChildNodes();
            for (int i=0; i<lsousb.getLength(); i++) {
                if (lsousb.item(i) instanceof Element) {
                    Element sousb = (Element)lsousb.item(i);
                    if (sousb.getLocalName().equals("element")) {
                        String sname = sousb.getAttribute("name");
                        if (!"".equals(sname)) {
                            ajSubst(sousb, sname, liste);
                        } else if (!"".equals(sousb.getAttribute("ref"))) {
                            String sref = localValue(sousb.getAttribute("ref"));
                            Element refel = chercherElement(sref);
                            if (refel != null)
                                ajSubst(refel, sref, liste);
                        }
                        // sinon cas bizarre
                    } else if (!sousb.getLocalName().equals("attribute"))
                        liste.addAll(sListeSousbalises(sousb));
                }
            }
        }
        retirerDoublons(liste);
        return(liste);
    }
    
    public ArrayList listeSousbalises(Element parentdef) {
        Config conf = getDefConf(parentdef);
        if (conf != this)
            return(conf.listeSousbalises(parentdef));
        if (schema != null) {
            Element sparent = schemaBaliseDef(nomBalise(parentdef));
            if (sparent == null)
                System.err.println("erreur: balise inconnue dans le schéma: " + nomBalise(parentdef));
            return(sListeSousbalises(sparent));
        } else {
            ArrayList liste = new ArrayList();
            NodeList lsousb = parentdef.getElementsByTagName("SOUSBALISE");
            for (int i=0; i<lsousb.getLength(); i++) {
                Element sousb = (Element)lsousb.item(i);
                String attens = sousb.getAttribute("ensemble");
                if (attens != null && !"".equals(attens)) {
                    String nomens = sousb.getAttribute("ensemble");
                    NodeList lens = jaxecfg.getElementsByTagName("ENSEMBLE");
                    for (int j=0; j<lens.getLength(); j++) {
                        Element ensemble = (Element)lens.item(j);
                        if (nomens.equals(ensemble.getAttribute("nom")))
                            liste.addAll(listeSousbalises(ensemble));
                    }
                } else
                    liste.add(sousb.getAttribute("nom"));
            }
            return(liste);
        }
    }
    
    /**
     * utilisé dans expressionReguliere pour les substitutionGroup
     */
    protected String substExpr(Element el, String nomel, boolean modevisu, boolean modechoice) {
        String expr = null;
        if (!"true".equals(localValue(el.getAttribute("abstract")))) {
            expr = nomel;
            if (!modevisu)
                expr += "_";
        }
        boolean bliste = false;
        for (int i=0; i<lelements.size(); i++) {
            Element el2 =(Element)lelements.get(i);
            String nom2 = (String)nomsElements.get(i);
            if (!"".equals(nom2) &&
                    nomel.equals(localValue(el2.getAttribute("substitutionGroup")))) {
                if (expr == null)
                    expr = "";
                else
                    expr += "|";
                expr += substExpr(el2, nom2, modevisu, true);
                bliste = true;
            }
        }
        if (bliste && (modevisu || !modechoice))
            expr = "(" + expr + ")";
        return(expr);
    }
    
    /**
     * Expression régulière correspondant au schéma pour un élément parent donné
     * problème 1: l'utilisateur entre les données au fur et à mesure, tout doit donc être facultatif
     * problème 2: jakarta-regexp n'accepte pas des expressions comme (a?|b?)?
     * on fait donc des transformations:
     * (a+|b)c -> (a*|b?)?c?
     * (a?|b?)* -> (a|b)*
     * (a?b?c?)* -> (a|b|c)*    (modechoice=true)
     * ((a?b*c*)|d?)? -> (((ab*c*)|(b+c*)|(b*c+))|d)?     (modepasnul=true)
     * ((a|b)*|c)* -> (a|b|c)*
     * on pourrait simplifier et retirer modepasnul si on continue
     * d'utiliser jakarta-oro au lieu de jakarta-regexp
     */
    protected String expressionReguliere(Element sparent, int niveau, boolean modechoice, boolean modevisu,
        boolean modepasnul, int imodepasnul, boolean modevalid) {
        //System.out.println("expressionReguliere " + sparent.getNodeName() + " " + niveau +
        //    " modechoice=" + modechoice + " modevisu=" + modevisu + " modepasnul=" +
        //    modepasnul + " " + imodepasnul + " " + modevalid);
        String regexp = null;
        String nombalise = sparent.getLocalName();
        if (niveau == 1 && nombalise.equals("element") && !"".equals(sparent.getAttribute("type"))) {
            String stype = localValue(sparent.getAttribute("type"));
            for (int i=0; i<lcomptypes.size(); i++) {
                Element ct =(Element)lcomptypes.get(i);
                if (stype.equals(ct.getAttribute("name"))) {
                    regexp = expressionReguliere(ct, 2, modechoice, modevisu, modepasnul, 0, modevalid);
                    break;
                }
            }
        } else if (nombalise.equals("group") && !"".equals(sparent.getAttribute("ref"))) {
            String sref = localValue(sparent.getAttribute("ref"));
            String min = sparent.getAttribute("minOccurs");
            String max = sparent.getAttribute("maxOccurs");
            for (int i=0; i<lgroups.size(); i++) {
                Element gr =(Element)lgroups.get(i);
                if (sref.equals(gr.getAttribute("name"))) {
                    boolean nouveaumodechoice = !modevisu && !modepasnul;
                    if (nouveaumodechoice && !"0".equals(min) && ("".equals(max) || "1".equals(max)))
                        nouveaumodechoice = false;
                    boolean nouveaumodepasnul = ( modepasnul ||
                        (!modevisu && !nouveaumodechoice && "0".equals(min)) );
                    regexp = expressionReguliere(gr, 2, nouveaumodechoice, modevisu, nouveaumodepasnul, 0, modevalid);
                    break;
                }
            }
            if ("0".equals(min) && !modepasnul) {
                if ("".equals(max) || "1".equals(max))
                    regexp = "(" + regexp + ")?";
                else
                    regexp = "(" + regexp + ")*";
            } else {
                if ("".equals(max) || "1".equals(max))
                    ;
                else
                    regexp = "(" + regexp + ")+";
            }
        } else if (nombalise.equals("group") && !"".equals(sparent.getAttribute("name"))) {
            NodeList lsousb = sparent.getChildNodes();
            for (int i=0; i<lsousb.getLength(); i++) {
                if (lsousb.item(i) instanceof Element) {
                    Element sousb = (Element)lsousb.item(i);
                    String r = expressionReguliere(sousb, 2, modechoice, modevisu, modepasnul, 0, modevalid);
                    if (r != null) {
                        regexp = r;
                        break;
                    }
                }
            }
        } else if (nombalise.equals("choice") ||
            (!(modevisu || modevalid) && nombalise.equals("sequence") &&
                !"".equals(sparent.getAttribute("maxOccurs")) &&
                !"1".equals(sparent.getAttribute("maxOccurs")))) {
            String min = sparent.getAttribute("minOccurs");
            String max = sparent.getAttribute("maxOccurs");
            boolean nouveaumodechoice = !modevisu && !modepasnul;
            if (nouveaumodechoice && ("".equals(max) || "1".equals(max)) &&
                    !modechoice)
                nouveaumodechoice = false;
            boolean nouveaumodepasnul = ( modepasnul ||
                (!modevisu && !nouveaumodechoice && nombalise.equals("choice") && "0".equals(min)) );
            NodeList lsousb = sparent.getChildNodes();
            for (int i=0; i<lsousb.getLength(); i++) {
                if (lsousb.item(i) instanceof Element) {
                    Element sousb = (Element)lsousb.item(i);
                    String r = expressionReguliere(sousb, 2, nouveaumodechoice, modevisu, nouveaumodepasnul, 0, modevalid);
                    if (r != null) {
                        if (regexp == null)
                            regexp = r;
                        else
                            regexp += "|" + r;
                    }
                }
            }
            if (!modechoice && regexp != null) {
                if ("0".equals(min) && !modepasnul) {
                    if ("".equals(max) || "1".equals(max))
                        regexp = "(" + regexp + ")?";
                    else
                        regexp = "(" + regexp + ")*";
                } else {
                    if ("".equals(max) || "1".equals(max))
                        regexp = "(" + regexp + ")";
                    else
                        regexp = "(" + regexp + ")+";
                }
            }
        } else if (nombalise.equals("sequence")) {
            NodeList lsousb = sparent.getChildNodes();
            if (modepasnul) {
                for (int i=imodepasnul; i<lsousb.getLength(); i++) {
                    if (lsousb.item(i) instanceof Element) {
                        Element sousb = (Element)lsousb.item(i);
                        String r1 = expressionReguliere(sousb, 2, modechoice, modevisu, true, 0, modevalid);
                        String r2 = expressionReguliere(sousb, 2, modechoice, modevisu, false, 0, modevalid);
                        String r3 = null;
                        String r4 = null;
                        for (int i2=i+1; i2<lsousb.getLength(); i2++) {
                            if (lsousb.item(i2) instanceof Element) {
                                Element sousb2 = (Element)lsousb.item(i2);
                                r3 = expressionReguliere(sparent, 2, modechoice, modevisu, true, i2, modevalid);
                                r4 = expressionReguliere(sparent, 2, modechoice, modevisu, false, i2, modevalid);
                                break;
                            }
                        }
                        if (r3 != null)
                            regexp = "(" + r1 + r4 + "|" + r2 + r3 + ")";
                        else
                            regexp = r1;
                        break;
                    }
                }
            } else {
                for (int i=imodepasnul; i<lsousb.getLength(); i++) {
                    if (lsousb.item(i) instanceof Element) {
                        Element sousb = (Element)lsousb.item(i);
                        String r = expressionReguliere(sousb, 2, modechoice, modevisu, false, 0, modevalid);
                        if (modechoice) {
                            if (regexp == null)
                                regexp = r;
                            else
                                regexp += "|" + r;
                        } else {
                            if (!(modevisu || modevalid) && r != null && !r.endsWith("*") && !r.endsWith("?")) {
                                r = expressionReguliere(sousb, 2, modechoice, modevisu, true, 0, modevalid);
                                if (r.endsWith(")"))
                                    r += "?";
                                else
                                    r = "(" + r + ")?";
                            }
                            if (regexp == null)
                                regexp = r;
                            else {
                                if (modevisu)
                                    regexp += ", ";
                                regexp += r;
                            }
                        }
                    }
                }
            }
            if (modevisu || modevalid) {
                String min = sparent.getAttribute("minOccurs");
                String max = sparent.getAttribute("maxOccurs");
                if ("0".equals(min)) {
                    if ("".equals(max) || "1".equals(max))
                        regexp = "(" + regexp + ")?";
                    else
                        regexp = "(" + regexp + ")*";
                } else {
                    if ("".equals(max) || "1".equals(max))
                        ;//regexp = "(" + regexp + ")";
                    else
                        regexp = "(" + regexp + ")+";
                }
            }
        } else if (nombalise.equals("complexType") || nombalise.equals("complexContent")) {
            NodeList lsousb = sparent.getChildNodes();
            for (int i=0; i<lsousb.getLength(); i++) {
                if (lsousb.item(i) instanceof Element) {
                    Element sousb = (Element)lsousb.item(i);
                    String r = expressionReguliere(sousb, 2, modechoice, modevisu, modepasnul, 0, modevalid);
                    if (r != null)
                        regexp = r;
                }
            }
        } else if (nombalise.equals("element") && niveau == 2) {
            String sname = sparent.getAttribute("name");
            if (!"".equals(sname)) {
                regexp = substExpr(sparent, sname, modevisu, modechoice);
            } else if (!"".equals(sparent.getAttribute("ref"))) {
                String sref = localValue(sparent.getAttribute("ref"));
                Element refel = chercherElement(sref);
                if (refel != null) {
                    regexp = substExpr(refel, sref, modevisu, modechoice);
                    String min = sparent.getAttribute("minOccurs");
                    String max = sparent.getAttribute("maxOccurs");
                    if ("0".equals(min) && !modechoice && !modepasnul) {
                        if ("".equals(max) || "1".equals(max))
                            regexp = "(" + regexp + ")?";
                        else
                            regexp = "(" + regexp + ")*";
                    } else {
                        if (!"".equals(max) && !"1".equals(max))
                            regexp = "(" + regexp + ")+";
                    }
                } else
                    System.err.println("référence non trouvée: " + sref);
            }
        } else if (nombalise.equals("any")) {
            //regexp = ".+";
            //System.err.println("any n'est pas géré");
        } else {
            if (nombalise.equals("extension") && !"".equals(sparent.getAttribute("base"))) {
                String sbase = localValue(sparent.getAttribute("base"));
                for (int i=0; i<lcomptypes.size(); i++) {
                    Element ct =(Element)lcomptypes.get(i);
                    if (sbase.equals(ct.getAttribute("name")))
                        regexp = expressionReguliere(ct, 2, modechoice, modevisu, modepasnul, 0, modevalid);
                }
            }
            NodeList lsousb = sparent.getChildNodes();
            String regexp2 = null;
            for (int i=0; i<lsousb.getLength(); i++) {
                if (lsousb.item(i) instanceof Element) {
                    Element sousb = (Element)lsousb.item(i);
                    if (!"annotation".equals(sousb.getLocalName())) {
                        regexp2 = expressionReguliere(sousb, 2, modechoice, modevisu,
                            modepasnul, 0, modevalid);
                        break;
                    }
                }
            }
            if (regexp2 != null) {
                if (regexp == null)
                    regexp = regexp2;
                else {
                    if (modevisu)
                        regexp = regexp + ", " + regexp2;
                    else
                        regexp = regexp + regexp2;
                }
            }
        }
        //System.out.println("-> " + regexp);
        return(regexp);
    }
    
    /**
     * Expression régulière correspondant au schéma pour un élément parent donné
     */
    public String expressionReguliere(Element parentdef) {
        if (schema == null) {
            ArrayList lsousb = listeSousbalises(parentdef);
            String expr = "";
            for (int i=0; i<lsousb.size(); i++) {
                if (i != 0)
                    expr += "|";
                expr += (String)lsousb.get(i);
            }
            if (lsousb.size() != 0)
                expr = "(" + expr + ")*";
            return(expr);
        }
        Config conf = getDefConf(parentdef);
        if (conf != this)
            return(conf.expressionReguliere(parentdef));
        Element sparent = schemaBaliseDef(nomBalise(parentdef));
        return(expressionReguliere(sparent, 1, false, true, false, 0, false));
    }
    
    /**
     * Cherche le premier élément ancêtre de même espace de nom
     */
    public Element chercheParentEspace(Element el, String namespace) {
        Node np = el.getParentNode();
        if (!(np instanceof Element))
            return(null);
        Element p = (Element)np;
        if (p == null)
            return(null);
        String pns = p.getNamespaceURI();
        boolean egal = false;
        if (namespace == null && pns == null)
            egal = true;
        if (namespace != null && namespace.equals(pns))
            egal = true;
        if (egal)
            return(p);
        else
            return(chercheParentEspace(p, namespace));
    }
    
    /**
     * Renvoit l'expression régulière correspondant aux enfants d'un élément,
     * en n'utilisant que les éléments ayant l'espace de noms de cet objet
     * et en ajoutant aInserer à pos
     */
    protected String expressionEspace(JaxeElement parent, Position pos, Element aInserer) {
        boolean danslazone = parent.debut.getOffset() <= pos.getOffset() &&
                parent.fin.getOffset() >= pos.getOffset();
        JaxeElement jcadet = null;
        if (danslazone)
            jcadet = parent.enfantApres(pos.getOffset());
        String cettexp = null;
        boolean insere = false;
        NodeList lsousb = parent.noeud.getChildNodes();
        for (int i=0; i<lsousb.getLength(); i++) {
            Node sousb = lsousb.item(i);
            if (sousb.getNodeType() == Node.ELEMENT_NODE || sousb.getNodeType() == Node.TEXT_NODE)  {
                JaxeElement je = parent.doc.getElementForNode(sousb);
                if (je != null) {
                    if (sousb.getNodeType() == Node.TEXT_NODE ||
                            (namespacecfg == null && sousb.getNamespaceURI() == null) ||
                            (namespacecfg != null && namespacecfg.equals(sousb.getNamespaceURI()))) {
                        String nomb = "";
                        if (sousb.getNodeType() == Node.ELEMENT_NODE)
                            nomb = localValue(sousb.getNodeName()) + "_";
                        if (je == jcadet && danslazone) {
                            nomb = nomBalise(aInserer) + "_" + nomb;
                            insere = true;
                        }
                        if (cettexp == null)
                            cettexp = nomb;
                        else
                            cettexp += nomb;
                    } else {
                        String ex2 = expressionEspace(je, pos, aInserer);
                        if (ex2 != null) {
                            if (cettexp == null)
                                cettexp = ex2;
                            else
                                cettexp += ex2;
                        }
                        if (je.debut.getOffset() <= pos.getOffset() &&
                                je.fin.getOffset() >= pos.getOffset()) {
                            insere = true;
                            danslazone = false;
                        }
                    }
                }
            }
        }
        if (!insere && danslazone) {
            if (cettexp == null)
                cettexp = nomBalise(aInserer) + "_";
            else
                cettexp += nomBalise(aInserer) + "_";
        }
        return(cettexp);
    }
    
    /**
     * renvoit true si on peut insérer l'élement aInsérer sous la balise parent à la position pos.
     */
    public boolean insertionPossible (JaxeElement parent, Position pos, Element aInserer) {
    //System.out.println("insertionPossible " + namespacecfg + " " + parent.noeud.getNodeName() + " " +
    //    nomBalise(aInserer));
        if (schema == null)
            return(true); // on suppose que le test de sous-balise a déjà été fait
        if (autresConfigs.size() > 0) {
            Config conf = getDefConf(aInserer);
            Config pconf = getElementConf((Element)parent.noeud);
            if (conf != pconf) {
                Element noeudparent = chercheParentEspace((Element)parent.noeud, conf.namespace());
                if (noeudparent == null)
                    return(true);
                parent = parent.doc.getElementForNode(noeudparent);
                if (conf != this)
                    //return(conf.insertionPossible(parent, pos, aInserer));
                    return(true);
            } else {
                if (conf != this)
                    //return(conf.insertionPossible(parent, pos, aInserer));
                    return(true);
/*
    pb: on ne peut pas tester l'ordre des éléments dans certains cas, par exemple:
    <html>
        <head>
            <xsl:if test='truc'>
                <title>xxx</title>
            </xsl:if>
            <xsl:if test='not(truc)'>
                <title>yyy</title>
            </xsl:if>
        </head>
    </html>
    Ici on autorise deux éléments title sous head alors qu'un seul est normalement autorisé.
    Par contre on peut tester les imbrications (title est autorisé sous head).
*/
            }
        }
        Element sparent = schemaBaliseDef(localValue(parent.noeud.getNodeName()));
        String cettexp = expressionEspace(parent, pos, aInserer);
        //System.out.println("cettexp: " + cettexp);
        
        if (cacheInsertion == null)
            cacheInsertion = new Hashtable();
        
        // jakarta-regexp
        //RE r = (RE)cacheInsertion.get(sparent);
        // jakarta-oro
        Pattern r = (Pattern)cacheInsertion.get(sparent);
        // gnu-regexp
        //RE r = (RE)cacheInsertion.get(sparent);
        
        if (r == null) {
            String expr = "^" + expressionReguliere(sparent, 1, false, false, false, 0, false) + "$";
            /*
            // jakarta-regexp
            try {
                r = new RE(expr);
            } catch (RESyntaxException ex) {
                System.err.println("RESyntaxException: " + ex.getMessage());
                System.err.println(expr);
                return(true);
            }
            */
            
            // jakarta-oro
            try {
                r = compiler.compile(expr);
            } catch (MalformedPatternException ex) {
                System.err.println("MalformedPatternException: " + ex.getMessage());
                System.err.println(expr);
                return(true);
            }
            
            // gnu-regexp
            /*
            try {
                r = new RE(expr);
            } catch (REException ex) {
                System.err.println("REException: " + ex.getMessage());
                System.err.println(expr);
                return(true);
            }
            */
            cacheInsertion.put(sparent, r);
        }
        
        // jakarta-regexp
        //boolean matched = r.match(cettexp);
        // jakarta-oro
        boolean matched = matcher.matches(cettexp, r);
        // gnu-regexp
        //boolean matched = r.isMatch(cettexp);
        return(matched);
    }
    
    /**
     * renvoit true si l'élément parent est valide par rapport à ses enfants (au niveau 1).
     * + renvoit l'expression régulière utilisée pour le test dans texpr[0] si details=true
     */
    public boolean elementValide(JaxeElement parent, boolean details, String[] texpr) {
        if (schema == null)
            return(true); // on suppose que le test de sous-balise a déjà été fait
        if (autresConfigs.size() > 0) {
            Config conf = getElementConf((Element)parent.noeud);
            if (conf != this)
                return(true); // on ne peut pas tester, cf commentaire dans insertionPossible
        }
        Element sparent = schemaBaliseDef(localValue(parent.noeud.getNodeName()));
        Config conf = getElementConf((Element)parent.noeud);
        String namespace = parent.noeud.getNamespaceURI();
        String cettexp = "";
        NodeList lsousb = parent.noeud.getChildNodes();
        for (int i=0; i<lsousb.getLength(); i++) {
            Node sousb = lsousb.item(i);
            if (sousb.getNodeType() == Node.ELEMENT_NODE || sousb.getNodeType() == Node.TEXT_NODE)  {
                String ns2 = sousb.getNamespaceURI();
                if ((namespace == null && ns2 == null) || (namespace != null && namespace.equals(ns2))) {
                    JaxeElement je = parent.doc.getElementForNode(sousb);
                    if (je != null) {
                        String nomb = "";
                        if (sousb.getNodeType() == Node.ELEMENT_NODE)
                            nomb = localValue(sousb.getNodeName()) + "_";
                        cettexp += nomb;
                    }
                }
            }
        }
        String expr = conf.expressionReguliere(sparent, 1, false, false, false, 0, true);
        if (expr == null)
            return(true);
        expr = "^" + expr + "$";

        String exprvisu = null;
        if (details)
            exprvisu = conf.expressionReguliere(sparent, 1, false, true, false, 0, false);
        
        //System.out.println("parent: "+parent.noeud.getNodeName()+" expression: '"+cettexp+"'");
        //System.out.println("test: " + expr);
        //System.out.println("visu: " + exprvisu);
        
        /*
        // jakarta-regexp
        RE r;
        try {
            r = new RE(expr);
        } catch (RESyntaxException ex) {
            System.err.println("RESyntaxException: " + ex.getMessage());
            System.err.println(expr);
            return(true);
        }
        boolean matched = r.match(cettexp);
        */
        
        // jakarta-oro
        Pattern r;
        try {
            r = compiler.compile(expr);
        } catch (MalformedPatternException ex) {
            System.err.println("MalformedPatternException: " + ex.getMessage());
            System.err.println(expr);
            return(true);
        }
        boolean matched = matcher.matches(cettexp, r);
        
        // gnu-regexp
        /*
        RE r;
        try {
            r = new RE(expr);
        } catch (REException ex) {
            System.err.println("REException: " + ex.getMessage());
            System.err.println(expr);
            return(true);
        }
        boolean matched = r.isMatch(cettexp);
        */
        if (matched)
            return(true);
        else {
            if (details)
                texpr[0] = exprvisu;
            return(false);
        }
    }
    
    protected ArrayList sParents(Element balisedef) {
        ArrayList liste = new ArrayList();
        if (balisedef.getLocalName().equals("schema"))
            return(liste);
        String bdefname = balisedef.getAttribute("name");
        if (balisedef.getLocalName().equals("complexType") && !"".equals(bdefname)) {
            for (int i=0; i<ltousextensions.size(); i++) {
                Element ext = (Element)ltousextensions.get(i);
                if (bdefname.equals(ext.getAttribute("base"))) {
                    Element parent = (Element)ext.getParentNode();
                    liste.addAll(sParents(parent));
                }
            }
            for (int i=0; i<ltouselements.size(); i++) {
                Element el =(Element)ltouselements.get(i);
                if (!"".equals(el.getAttribute("type")) &&
                    localValue(el.getAttribute("type")).equals(bdefname))
                    liste.add(el.getAttribute("name"));
            }
        } else if (balisedef.getLocalName().equals("group") && !"".equals(bdefname)) {
            for (int i=0; i<ltousgroups.size(); i++) {
                Element el =(Element)ltousgroups.get(i);
                if (!"".equals(el.getAttribute("ref")) &&
                    localValue(el.getAttribute("ref")).equals(bdefname))
                    liste.addAll(sParents(el));
            }
        } else {
            Element parent = (Element)balisedef.getParentNode();
            if (parent.getLocalName().equals("element"))
                liste.add(parent.getAttribute("name"));
            else
                liste.addAll(sParents(parent));
        }
        return(liste);
    }
    
    protected ArrayList sListeParents(Element balisedef) {
        ArrayList liste = new ArrayList();
        String bdefname = balisedef.getAttribute("name");
        for (int i=0; i<ltouselements.size(); i++) {
            Element sousb = (Element)ltouselements.get(i);
            if (bdefname.equals(sousb.getAttribute("name")) ||
                bdefname.equals(localValue(sousb.getAttribute("ref")))) {
                Element parent = (Element)sousb.getParentNode();
                if (parent.getLocalName().equals("element"))
                    liste.add(parent.getAttribute("name"));
                else {
                    liste.addAll(sParents(parent));
                }
                if (!"".equals(balisedef.getAttribute("substitutionGroup"))) {
                    String nomsub = localValue(balisedef.getAttribute("substitutionGroup"));
                    for (int j=0; j<lelements.size(); j++)
                        if (nomsub.equals((String)nomsElements.get(j)))
                            liste.addAll(sListeParents((Element)lelements.get(j)));
                }
            }
        }
        for (int i=0; i<liste.size(); i++) {
            String s =(String)liste.get(i);
            int li = liste.lastIndexOf(s);
            while (li != i) {
                liste.remove(li);
                li = liste.lastIndexOf(s);
            }
        }
        return(liste);
    }
    
    public ArrayList listeParents(Element balisedef) {
        if (schema != null) {
            Config conf = getDefConf(balisedef);
            if (conf != this)
                return(conf.listeParents(balisedef));
            Element sbalisedef = schemaBaliseDef(nomBalise(balisedef));
            if (sbalisedef == null) {
                System.err.println("erreur: balise inconnue dans le schéma: " + nomBalise(balisedef));
                return(new ArrayList());
            }
            return(sListeParents(sbalisedef));
        } else {
            ArrayList liste = new ArrayList();
            NodeList lsousb = jaxecfg.getElementsByTagName("SOUSBALISE");
            for (int i=0; i<lsousb.getLength(); i++) {
                Element sousb = (Element)lsousb.item(i);
                if (balisedef.getAttribute("nom").equals(sousb.getAttribute("nom"))) {
                    Element parent = (Element)sousb.getParentNode();
                    if (parent.getNodeName().equals("BALISE"))
                        liste.add(parent.getAttribute("nom"));
                    else if (parent.getNodeName().equals("ENSEMBLE")) {
                        String nomens = parent.getAttribute("nom");
                        NodeList lsousb2 = jaxecfg.getElementsByTagName("SOUSBALISE");
                        for (int j=0; j<lsousb2.getLength(); j++) {
                            Element sousb2 = (Element)lsousb2.item(j);
                            if (nomens.equals(sousb2.getAttribute("ensemble"))) {
                                Element parent2 = (Element)sousb2.getParentNode();
                                liste.add(parent2.getAttribute("nom"));
                            }
                        }
                    }
                }
            }
            return(liste);
        }
    }
    
    // attributs dans complexType ou attributeGroup ou extension
    protected ArrayList sCtAttributs(Element ctdef) {
        ArrayList liste = new ArrayList();
        
        NodeList lsousb = ctdef.getChildNodes();
        for (int ils=0; ils<lsousb.getLength(); ils++) {
            if (lsousb.item(ils) instanceof Element) {
                Element sousb = (Element)lsousb.item(ils);
                String localname = sousb.getLocalName();
                if ("attribute".equals(localname))
                    liste.add(sousb);
                else if ("attributeGroup".equals(localname)) {
                    String ref = localValue(sousb.getAttribute("ref"));
                    if (ref != null) {
                        for (int j=0; j<lattgroups.size(); j++) {
                            Element agj = (Element)lattgroups.get(j);
                            if (ref.equals(agj.getAttribute("name")))
                                liste.addAll(sCtAttributs(agj));
                        }
                    } else
                        liste.addAll(sCtAttributs(sousb));
                } else if ("simpleContent".equals(localname) || "complexContent".equals(localname)) {
                    ArrayList extl = enfants(sousb, "extension");
                    for (int i=0; i<extl.size(); i++) {
                        Element ext = (Element)extl.get(i);
                        String sbase = localValue(ext.getAttribute("base"));
                        if (!"".equals(sbase)) {
                            for (int j=0; j<lelements.size(); j++)
                                if (sbase.equals((String)nomsElements.get(j)))
                                    liste.addAll(sListeAttributs((Element)lelements.get(j)));
                                    // espérons qu'on ne boucle pas
                        }
                        liste.addAll(sCtAttributs(ext));
                    }
                }
            }
        }
                
        return(liste);
    }
    
    public ArrayList sListeAttributs(Element balisedef) {
        ArrayList liste = new ArrayList();
        String nombalise = balisedef.getLocalName();
        if (nombalise.equals("element") && !"".equals(balisedef.getAttribute("type"))) {
            String stype = localValue(balisedef.getAttribute("type"));
            for (int i=0; i<lcomptypes.size(); i++) {
                Element ct =(Element)lcomptypes.get(i);
                if (stype.equals(ct.getAttribute("name")))
                    liste.addAll(sCtAttributs(ct));
            }
        } else {
            NodeList lsn = balisedef.getChildNodes();
            for (int i=0; i<lsn.getLength(); i++) {
                Node n = lsn.item(i);
                if (n instanceof Element && n.getLocalName().equals("complexType"))
                    liste.addAll(sCtAttributs((Element)n));
            }
        }
        return(liste);
    }
    
    public ArrayList listeAttributs(Element balisedef) {
        Config conf = getDefConf(balisedef);
        if (conf != this)
            return(conf.listeAttributs(balisedef));
        if (schema != null) {
            Element sbalisedef = schemaBaliseDef(nomBalise(balisedef));
            if (sbalisedef == null)
                System.err.println("erreur: balise inconnue dans le schéma: " + nomBalise(balisedef));
            return(sListeAttributs(sbalisedef));
        } else {
            NodeList latt = balisedef.getElementsByTagName("ATTRIBUT");
            ArrayList l = new ArrayList();
            addNodeList(l, latt);
            return(l);
        }
    }
    
    public String nomAttribut(Element attdef) {
        if (schema != null) {
            if (!"".equals(attdef.getAttribute("name")))
                return(attdef.getAttribute("name"));
            else
                return(attdef.getAttribute("ref"));
        } else
            return(attdef.getAttribute("nom"));
    }
    
    public boolean estObligatoire(Element attdef) {
        if (schema != null) {
            String presence = attdef.getAttribute("use");
            return("required".equals(presence));
       } else {
            String presence = attdef.getAttribute("presence");
            return("obligatoire".equals(presence));
        }
    }
    
    public String[] listeValeurs(Element attdef) {
        if (schema != null) {
            NodeList lval = attdef.getElementsByTagNameNS(schemaNamespace, "enumeration"); // pas très rigoureux
            if (lval.getLength() == 0) {
                lval = null;
                if (!"".equals(attdef.getAttribute("type"))) {
                    String stype = localValue(attdef.getAttribute("type"));
                    for (int i=0; i<lsimptypes.size(); i++) {
                        Element st =(Element)lsimptypes.get(i);
                        if (stype.equals(st.getAttribute("name"))) {
                            lval = st.getElementsByTagNameNS(schemaNamespace, "enumeration");
                            break;
                        }
                    }
                }
                if (lval == null || lval.getLength() == 0)
                    return(null);
            }
            String[] liste = new String[lval.getLength()];
            for (int i=0; i<lval.getLength(); i++) {
                Element val = (Element)lval.item(i);
                String sval = val.getAttribute("value");
                liste[i] = sval;
            }
            return(liste);
        } else {
            NodeList lval = attdef.getElementsByTagName("VALEUR");
            if (lval.getLength() == 0)
                return(null);
            String[] liste = new String[lval.getLength()];
            for (int i=0; i<lval.getLength(); i++) {
                Element val = (Element)lval.item(i);
                String sval = val.getFirstChild().getNodeValue().trim();
                liste[i] = sval;
            }
            return(liste);
        }
    }
    
    /**
     * Renvoit la valeur par défaut d'un attribut dont l'élément définition est donné en paramètre
     * (c'est la valeur de l'attribut "default")
     */
    public String valeurParDefaut(Element attdef) {
        if (schema == null)
            return(null);
        if ("".equals(attdef.getAttribute("default")))
            return(null);
        return(attdef.getAttribute("default"));
    }
    
    public boolean contientDuTexte(Element balisedef) {
        if (schema != null) {
            Element sbalisedef = schemaBaliseDef(nomBalise(balisedef));
            if (sbalisedef == null)
                System.err.println("erreur: balise inconnue dans le schéma: " + nomBalise(balisedef));
            if ("element".equals(sbalisedef.getLocalName())) {
                // si le type fait partie des schémas XML (comme "string" ou "anyURI")
                // on considère que c'est du texte
                String schemaPrefix = schema.getPrefix();
                String stype = sbalisedef.getAttribute("type");
                int indp = stype.indexOf(':');
                if (indp == -1 && schemaPrefix == null ||
                        indp != -1 && stype.substring(0, indp).equals(schemaPrefix))
                    return(true);
            }
            if ("element".equals(sbalisedef.getLocalName()) && !sbalisedef.getAttribute("type").equals("")) {
                // complexType
                String stype = localValue(sbalisedef.getAttribute("type"));
                for (int i=0; i<lcomptypes.size(); i++) {
                    Element ct =(Element)lcomptypes.get(i);
                    if (stype.equals(ct.getAttribute("name"))) {
                        if ("true".equals(ct.getAttribute("mixed")))
                            return(true);
                        else {
                            NodeList sc = ct.getElementsByTagNameNS(schemaNamespace, "simpleContent");
                            if (sc.getLength() > 0 && sc.item(0) instanceof Element)
                                return(true);
                        }
                    }
                }
                // simpleType
                for (int i=0; i<lsimptypes.size(); i++) {
                    Element st =(Element)lsimptypes.get(i);
                    if (stype.equals(st.getAttribute("name")))
                        return(true);
                }
            }
            NodeList lsn = sbalisedef.getChildNodes();
            for (int i=0; i<lsn.getLength(); i++) {
                Node n = lsn.item(i);
                if (n instanceof Element && n.getLocalName().equals("complexType")) {
                    if ("true".equals(((Element)n).getAttribute("mixed")))
                        return(true);
                    NodeList sc = ((Element)n).getElementsByTagNameNS(schemaNamespace, "simpleContent");
                    return (sc.getLength() > 0 && sc.item(0) instanceof Element);
                } else if (n instanceof Element && n.getLocalName().equals("simpleType"))
                    return(true);
            }
            return(false);
        } else {
            NodeList ltexte = balisedef.getElementsByTagName("TEXTE");
            if (ltexte.getLength() > 0)
                return(true);
            NodeList lsousb = balisedef.getElementsByTagName("SOUSBALISE");
            for (int i=0; i<lsousb.getLength(); i++) {
                Element sousb = (Element)lsousb.item(i);
                String attens = sousb.getAttribute("ensemble");
                if (attens != null && !"".equals(attens)) {
                    String nomens = sousb.getAttribute("ensemble");
                    NodeList lens = jaxecfg.getElementsByTagName("ENSEMBLE");
                    for (int j=0; j<lens.getLength(); j++) {
                        Element ensemble = (Element)lens.item(j);
                        if (nomens.equals(ensemble.getAttribute("nom")))
                            if (contientDuTexte(ensemble))
                                return(true);
                    }
                }
            }
            return(false);
        }
    }
    
    public String documentation(Element balisedef) {
        if (schema == null)
            return(null);
        Config conf = getDefConf(balisedef);
        if (conf != this)
            return(conf.documentation(balisedef));
        Element sbalisedef = schemaBaliseDef(nomBalise(balisedef));
        if (sbalisedef == null)
            return(null);
        NodeList lsn = sbalisedef.getChildNodes();
        for (int i=0; i<lsn.getLength(); i++) {
            Node n = lsn.item(i);
            if (n instanceof Element && n.getLocalName().equals("annotation")) {
                NodeList ldoc = ((Element)n).getElementsByTagNameNS(schemaNamespace, "documentation");
                String sdoc = null;
                for (int j=0; j<ldoc.getLength(); j++) {
                    Element doc = (Element)ldoc.item(j);
                    if (doc.getFirstChild() != null) {
                        if (sdoc == null)
                            sdoc = "";
                        else
                            sdoc += newline;
                    	sdoc += doc.getFirstChild().getNodeValue();
                    }
                }
                if (sdoc != null) {
                    // tranformation en HTML
                    sdoc = sdoc.trim();
                    int ind = sdoc.indexOf('\n');
                    while (ind != -1) {
                        sdoc = sdoc.substring(0, ind) + "<p>" + sdoc.substring(ind + 1);
                        ind = sdoc.indexOf('\n');
                    }
                    sdoc = "<html><body>" + sdoc + "</body></html>";
                }
                return(sdoc);
            }
        }
        return(null);
    }
    
    // Renvoit les fichiers XSL attachés à cette config et construit la table de hash des paramètres de ces fichiers
    public File[] getXSLFiles() {
        if (jaxecfg == null)
            return(null);
        NodeList lxsl = jaxecfg.getElementsByTagName("FICHIERXSL");
        File[] fichiersxsl = new File[lxsl.getLength()] ;
        for (int i=0; i<lxsl.getLength(); i++) {
            ArrayList parametres = new ArrayList() ;
            Element xslel = (Element)lxsl.item(i);
            String nom = xslel.getAttribute("nom");
            NodeList lxsl2 = xslel.getElementsByTagName("PARAMETRE");
            if (nom.startsWith("/"))
                fichiersxsl[i] = new File(nom);
            else
                fichiersxsl[i] = new File(cfgdir, nom);
            for (int j = 0 ; j < lxsl2.getLength() ; j++) {
                Element xslel2 = (Element)lxsl2.item(j);
                String nombis = xslel2.getAttribute("nom") ;
                String valeur = xslel2.getAttribute("valeur") ;
                String parametre[] = {nombis,valeur} ;
                parametres.add(parametre) ;
            }
            fichierXSL2Parametres.put(fichiersxsl[i],parametres) ;
            }
        return(fichiersxsl);
    }
    
    public ArrayList getXSLParam(File xslFile) {
        return (ArrayList)fichierXSL2Parametres.get(xslFile) ;
    }
    
    public String getEncodage() {
        NodeList nl = jaxecfg.getElementsByTagName("ENCODAGE");
        if (nl == null || nl.getLength() == 0)
            return(null);
        Element encodage = (Element)nl.item(0);
        if (encodage.getFirstChild() == null || encodage.getFirstChild().getNodeValue() == null)
            return(null);
        return(encodage.getFirstChild().getNodeValue().trim());
    }
    
    /**
     * Returns a Value from a Parameter in the Definition
     * @param defbalise the Definition
     * @param parameter the Parameter
     * @param defaultvalue the default-value if the parameter isn't found
     * @return the value 
     */
    public String getParamFromDefinition(Element defbalise, String parameter, String defaultvalue) {
        ArrayList lval = getValeursParam(defbalise, parameter);
        String valeur;
        if (lval != null && lval.size() > 0)
            valeur = (String)lval.get(0);
        else
            valeur = defaultvalue;
        return valeur;
    }
    
    protected Hashtable construireCacheParams(Element defbalise) {
        Hashtable hashparams = new Hashtable();
        NodeList params = defbalise.getElementsByTagName("PARAMETRE");
        
        for (int i=0; i<params.getLength(); i++) {
            Element parel = (Element)params.item(i);
            String nom = parel.getAttribute("nom");
            String valeur = parel.getAttribute("valeur");
            ArrayList lval = (ArrayList)hashparams.get(nom);
            if (lval == null) {
                lval = new ArrayList();
                lval.add(valeur);
                hashparams.put(nom, lval);
            } else
                lval.add(valeur);
        }
        cacheParametres.put(defbalise, hashparams);
        return(hashparams);
    }
    
    /**
     * Renvoit une liste de valeurs pour un paramètre
     * @param defbalise la définition de l'élément
     * @param nomParam le paramètre
     * @return les valeurs pour le paramètre, sous forme d'une ArrayList qui peut être nulle
     */
    public ArrayList getValeursParam(Element defbalise, String nomParam) {
        if (cacheParametres == null)
            cacheParametres = new Hashtable();
        Hashtable hashparams = (Hashtable)cacheParametres.get(defbalise);
        if (hashparams == null)
            hashparams = construireCacheParams(defbalise);
        ArrayList res = (ArrayList)hashparams.get(nomParam);
        //if (res == null)
        //    res = new ArrayList();
        // ça bouffait du CPU pour rien, la plupart du temps res==null
        return res;
    }
    
    /**
     * Ajoute les attributs pour les espaces de nom à l'élément racine
     */
    public void ajouterAttributsEspaces(Element rootel) {
        if (namespacecfg != null && !"".equals(namespacecfg)) {
            String nomatt = "xmlns";
            String prefixe = prefixe();
            if (prefixe != null && !"".equals(prefixe))
                nomatt += ":" + prefixe;
            rootel.setAttribute(nomatt, namespacecfg);
        }
        for (int i=0; i<autresConfigs.size(); i++) {
            Config conf = (Config)autresConfigs.get(i);
            conf.ajouterAttributsEspaces(rootel);
        }
    }
    
    public String getPublicId() {
        NodeList doctypes = jaxecfg.getElementsByTagName("DOCTYPE");
        if (doctypes.getLength() > 0)
            return(((Element)doctypes.item(0)).getAttribute("publicId"));
        else
            return(null);
    }
    
    public String getSystemId() {
        NodeList doctypes = jaxecfg.getElementsByTagName("DOCTYPE");
        if (doctypes.getLength() > 0)
            return(((Element)doctypes.item(0)).getAttribute("systemId"));
        else
            return(null);
    }
}
