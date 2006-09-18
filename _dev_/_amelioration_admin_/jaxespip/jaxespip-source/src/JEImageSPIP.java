/*
JaxeSPIPApplet - Applet utilisant Jaxe pour éditer un article

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

import java.awt.Color;
import java.awt.Container;
import java.awt.Image;
import java.awt.MediaTracker;
import java.awt.Toolkit;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;

import javax.swing.BorderFactory;
import javax.swing.ImageIcon;
import javax.swing.JApplet;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.text.Position;

import jaxe.DialogueAttributs;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeResourceBundle;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

/**
 * Fichier d'image. L'image est affichée dans le texte si elle est trouvée, sinon un message d'erreur
 * est affiché dans le texte à la place de l'image.
 * Type d'élément Jaxe: 'plugin'
 * attribut donnant le numéro du document SPIP : numéro
 * URL pour récupérer l'image : voir ci-dessous
 */
public class JEImageSPIP extends JaxeElement {

    //final static String access = "../spip_acces_doc.php3?id_document="; // SPIP 1.8
    final static String access = "../../../spip.php?action=autoriser&arg="; // SPIP 1.9
    URL urlapplet;
    String srcAttr;
    JLabel label = null;
    float alignementY = 1;
    // les images sont réduites à l'affichage pour limiter l'espace mémoire utilisé
    public static int taillemax = 300;
    public static boolean reduction = true;

    public JEImageSPIP(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        urlapplet = doc.textPane.japplet.getDocumentBase();
        
        Element el = (Element)noeud;
        
        Element defbalise = doc.cfg.getElementDef(el);
        srcAttr = "numéro";
        
        URL urlimg = null;
        try {
            urlimg = new URL(urlapplet, access + el.getAttribute(srcAttr));
        } catch (MalformedURLException ex) {
            System.err.println(ex.getClass().getName() + ": " + ex.getMessage());
        }
        Image img;
        if (urlimg != null) {
            try {
                img = Toolkit.getDefaultToolkit().createImage(urlimg);
            } catch (Exception ex) { // par exemple FilePermission
                System.err.println(ex.getClass().getName() + ": " + ex.getMessage());
                img = null;
            }
        } else
            img = null;
        if (img == null || !chargerImage(img)) {
            label = new JLabel(getString("erreur.AffichageImage") + ": " + urlimg);
            label.setBorder(BorderFactory.createLineBorder(Color.darkGray));
        } else {
            if (reduction)
                img = reduireImage(img);
            ImageIcon icon;
            if (img != null)
                icon = new ImageIcon(img);
            else
                icon = null;
            if (icon == null || icon.getImageLoadStatus() == MediaTracker.ABORTED ||
                icon.getImageLoadStatus() == MediaTracker.ERRORED) {
                label = new JLabel(getString("erreur.AffichageImage") + ": " + urlimg);
                label.setBorder(BorderFactory.createLineBorder(Color.darkGray));
            } else
                label = new JLabel(icon);
        }
        label.setAlignmentY(alignementY);
        
        label.addMouseListener(new JEFichierMouseListener(this, doc.jframe));
        Position newpos = insertComponent(pos, label);
        
        creerEnfants(newpos);
    }
    
    protected boolean chargerImage(Image img) {
        if (img == null)
            return(false);
        MediaTracker tracker = new MediaTracker(doc.textPane.japplet);
        tracker.addImage(img, 0);
        try {
            tracker.waitForAll();
        } catch (InterruptedException e) {
            return(false);
        }
        return(!tracker.isErrorAny());
    }
    
    protected static Image reduireImage(Image img) {
        if (img == null)
            return(null);
        int width = img.getWidth(null);
        int height = img.getHeight(null);
        if (width == -1 || height == -1) {
            //System.err.println("reduireImage: taille image inconnue");
            return(null);
        } else if (width > taillemax || height > taillemax) {
            if (width > height) {
                double scale = (taillemax*1.0) / width;
                width = taillemax;
                height = (int)(height*scale);
            } else {
                double scale = (taillemax*1.0) / height;
                height = taillemax;
                width = (int)(width*scale);
            }
            Image img2 = img.getScaledInstance(width, height, Image.SCALE_FAST);
            img.flush();
            return(img2);
        }
        return(img);
    }
    
    public Node nouvelElement(Element defbalise) {
        Element newel = nouvelElementDOM(doc, defbalise);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            String nombalise = doc.cfg.nomBalise(defbalise);
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc, nombalise, defbalise, newel);
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
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc, el.getTagName(), defbalise, el);
            if (dlg.afficher()) {
                dlg.enregistrerReponses();
                majAffichage();
            }
            dlg.dispose();
        }
    }
    
    public void majAffichage() {
        URL urlimg;
        Element el = (Element)noeud;
        try {
            urlimg = new URL(urlapplet, access + el.getAttribute(srcAttr));
        } catch (MalformedURLException ex) {
            System.err.println(ex.getClass().getName() + ": " + ex.getMessage());
            return;
        }
        if ((ImageIcon)label.getIcon() != null) {
            ((ImageIcon)label.getIcon()).getImage().flush();
            try {
                // workaround bug 4725530 (JEditorPane: Unintended caching of images)
                // for the HTMLFrame (which refers to images with a URL)
                Toolkit.getDefaultToolkit().getImage(urlimg).flush();
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        }
        Image img = Toolkit.getDefaultToolkit().createImage(urlimg);
        boolean erreur = false;
        if (img == null || !chargerImage(img))
            erreur = true;
        if (!erreur && reduction)
            img = reduireImage(img);
        if (!erreur) {
            ImageIcon icon = new ImageIcon(img);
            label.setIcon(icon);
            label.setText(null);
            label.setBorder(null);
        } else {
            label.setIcon(null);
            label.setText(getString("erreur.AffichageImage") + ": " + urlimg);
            label.setBorder(BorderFactory.createLineBorder(Color.darkGray));
        }
        doc.imageChanged(label);
    }

    public void selection(boolean select) {
        super.selection(select);
        label.setEnabled(!select);
    }

    class JEFichierMouseListener extends MouseAdapter {
        JEImageSPIP jei;
        JFrame jframe;
        public JEFichierMouseListener(JEImageSPIP obj, JFrame jframe) {
            super();
            jei = obj;
            this.jframe = jframe;
        }
        public void mouseClicked(MouseEvent e) {
            jei.afficherDialogue(jframe);
        }
    }
}
