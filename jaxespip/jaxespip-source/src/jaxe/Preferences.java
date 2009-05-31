/*
Jaxe - Editeur XML en Java

Copyright (C) 2003 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.FileDialog;
import java.awt.FlowLayout;
import java.awt.Rectangle;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FilenameFilter;
import java.io.IOException;
import java.util.Properties;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;

/**
 * Préférences:
 * 
 *     fenetreArbre
 *     fenetreInsertion
 *     fenetreAttributs
 *     navigateur
 *     consIndent
 *     dictionnaire
 */
public class Preferences extends JDialog implements ActionListener {

    private static Properties prefs = null;
    
    private JCheckBox caseArbre;
    private JCheckBox caseInsertion;
    private JCheckBox caseAttributs;
    private JLabel labelNav;
    private String prefNav;
    private JCheckBox caseIndent;
    private JLabel labelDico;
    private String prefDico;
    
    public static Properties chargerPref() {
        prefs = new Properties();
        return(prefs);
    }
    
    public static Properties getPref() {
        if (prefs == null)
            prefs = new Properties();
        return(prefs);
    }
    
    public static void enregistrerPref(Properties prefs1) {
    }
    
    public Preferences(JFrame jframe) {
	super(jframe, JaxeResourceBundle.getRB().getString("pref.Preferences"), true);
        this.getContentPane().setLayout(new BorderLayout());
        
        Properties prefs = getPref();
        String prefArbre = prefs.getProperty("fenetreArbre");
        if (prefArbre == null)
            prefArbre = "true";
        String prefInsertion = prefs.getProperty("fenetreInsertion");
        if (prefInsertion == null)
            prefInsertion = "true";
        String prefAttributs = prefs.getProperty("fenetreAttributs");
        if (prefAttributs == null)
            prefAttributs = "true";
        prefNav = prefs.getProperty("navigateur");
        String prefIndent = prefs.getProperty("consIndent");
        if (prefIndent == null)
            prefIndent = "false";
        prefDico = prefs.getProperty("dictionnaire");
        
        JPanel prefPanes = new JPanel();
        prefPanes.setLayout(new BoxLayout(prefPanes, BoxLayout.Y_AXIS));
        
        JPanel fenPane = new JPanel();
        fenPane.setLayout(new BoxLayout(fenPane, BoxLayout.Y_AXIS));
        fenPane.setBorder(BorderFactory.createTitledBorder(
        	JaxeResourceBundle.getRB().getString("pref.Fenetres")));
        caseArbre = new JCheckBox(
            JaxeResourceBundle.getRB().getString("pref.Arbre"));
        caseArbre.setSelected("true".equals(prefArbre));
        fenPane.add(caseArbre);
        caseInsertion = new JCheckBox(
            JaxeResourceBundle.getRB().getString("pref.Insertion"));
        caseInsertion.setSelected("true".equals(prefInsertion));
        fenPane.add(caseInsertion);
        caseAttributs = new JCheckBox(
            JaxeResourceBundle.getRB().getString("pref.Attributs"));
        caseAttributs.setSelected("true".equals(prefAttributs));
        fenPane.add(caseAttributs);
        prefPanes.add(fenPane);
        fenPane.setAlignmentX(Component.LEFT_ALIGNMENT);
        fenPane.setMaximumSize(new Dimension(Short.MAX_VALUE,Short.MAX_VALUE));
        
        JPanel navPane = new JPanel(new FlowLayout());
        navPane.setBorder(BorderFactory.createTitledBorder(
            JaxeResourceBundle.getRB().getString("pref.Navigateur")));
        String nomNav = null;
        if (prefNav != null)
            nomNav = (new File(prefNav)).getName();
        labelNav = new JLabel(nomNav);
        navPane.add(labelNav);
        JButton defNav = new JButton(
            JaxeResourceBundle.getRB().getString("pref.Definir"));
        defNav.addActionListener(this);
        defNav.setActionCommand("defNav");
        navPane.add(defNav);
        prefPanes.add(navPane);
        navPane.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        JPanel enrPane = new JPanel(new FlowLayout());
        enrPane.setBorder(BorderFactory.createTitledBorder(
            JaxeResourceBundle.getRB().getString("pref.Indentations")));
        caseIndent = new JCheckBox(
            JaxeResourceBundle.getRB().getString("pref.consIndent"));
        caseIndent.setSelected("true".equals(prefIndent));
        enrPane.add(caseIndent);
        prefPanes.add(enrPane);
        enrPane.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        JPanel dicoPane = new JPanel(new FlowLayout());
        dicoPane.setBorder(BorderFactory.createTitledBorder(
            JaxeResourceBundle.getRB().getString("pref.Dictionnaire")));
        String nomDico = null;
        if (prefDico != null) {
            nomDico = (new File(prefDico)).getName();
            int pp = nomDico.lastIndexOf('.');
            if (pp != -1)
                nomDico = nomDico.substring(0, pp);
        }
        labelDico = new JLabel(nomDico);
        dicoPane.add(labelDico);
        JButton defDico = new JButton(
            JaxeResourceBundle.getRB().getString("pref.Definir"));
        defDico.addActionListener(this);
        defDico.setActionCommand("defDico");
        dicoPane.add(defDico);
        prefPanes.add(dicoPane);
        dicoPane.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        this.getContentPane().add(prefPanes, BorderLayout.CENTER);
        
        JPanel bPane = new JPanel(new FlowLayout(FlowLayout.CENTER, 15, 15));
        JButton boutonAnnuler = new JButton(
            JaxeResourceBundle.getRB().getString("pref.Annuler"));
        boutonAnnuler.addActionListener(this);
        boutonAnnuler.setActionCommand("Annuler");
        bPane.add(boutonAnnuler);
        JButton boutonOK = new JButton(
            JaxeResourceBundle.getRB().getString("pref.Enregistrer"));
        boutonOK.addActionListener(this);
        boutonOK.setActionCommand("Enregistrer");
        bPane.add(boutonOK);
        getRootPane().setDefaultButton(boutonOK);
        this.getContentPane().add(bPane, BorderLayout.SOUTH);
        Rectangle r = jframe.getBounds();
        setLocation(r.x + r.width/4, r.y + r.height/4);
        this.pack();
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        
        if ("Enregistrer".equals(cmd)) {
            Properties prefs = getPref();
            
            String prefArbre;
            if (caseArbre.isSelected())
                prefArbre = "true";
            else
                prefArbre = "false";
            prefs.setProperty("fenetreArbre", prefArbre);
            
            String prefInsertion;
            if (caseInsertion.isSelected())
                prefInsertion = "true";
            else
                prefInsertion = "false";
            prefs.setProperty("fenetreInsertion", prefInsertion);
            
            String prefAttributs;
            if (caseAttributs.isSelected())
                prefAttributs = "true";
            else
                prefAttributs = "false";
            prefs.setProperty("fenetreAttributs", prefAttributs);
             
            if (prefNav != null) {
                prefs.setProperty("navigateur", prefNav);
            } else {
                prefs.setProperty("navigateur", "");
            }
            
            if (prefDico != null) {
                prefs.setProperty("dictionnaire", prefDico);
            } else {
                prefs.setProperty("dictionnaire", "");
            }
            
            String prefIndent;
            if (caseIndent.isSelected())
                prefIndent = "true";
            else
                prefIndent = "false";
            prefs.setProperty("consIndent", prefIndent);
             
            Preferences.enregistrerPref(prefs);
            
        } else if ("defNav".equals(cmd)) {
            defNavigateur();
        } else if ("defDico".equals(cmd)) {
            defDictionnaire();
        }
        if ("Enregistrer".equals(cmd) || "Annuler".equals(cmd))
            setVisible(false);
    }
    
    public void defNavigateur() {
        FileDialog fdlg = new FileDialog((JFrame)getOwner(),
            JaxeResourceBundle.getRB().getString("pref.DefNavigateur"), FileDialog.LOAD);
        fdlg.show();
        String chemin = null;
        String dir = fdlg.getDirectory();
        if (dir != null && dir.endsWith(File.separator))
            dir = dir.substring(0, dir.length()-1);
        String nom = fdlg.getFile();
        if (dir == null)
            chemin = nom;
        else if (nom != null)
            chemin = dir + File.separator + nom;
        if (chemin != null) {
            prefNav = chemin;
            labelNav.setText(nom);
        }
    }
    
    public void defDictionnaire() {
        FileDialog fdlg = new FileDialog((JFrame)getOwner(),
            JaxeResourceBundle.getRB().getString("pref.Dictionnaire"), FileDialog.LOAD);
        fdlg.setFilenameFilter(new ExtFilter("dico"));
        fdlg.setDirectory(System.getProperty("user.dir") + File.separator + "dicos");
        fdlg.show();
        String chemin = null;
        String dir = fdlg.getDirectory();
        if (dir != null && dir.endsWith(File.separator))
            dir = dir.substring(0, dir.length()-1);
        String nom = fdlg.getFile();
        if (dir == null)
            chemin = nom;
        else if (nom != null)
            chemin = dir + File.separator + nom;
        if (chemin != null) {
            prefDico = chemin;
            if (nom != null) {
                int pp = nom.lastIndexOf('.');
                if (pp != -1)
                    nom = nom.substring(0, pp);
            }
            labelDico.setText(nom);
        }
    }
    
    class ExtFilter implements FilenameFilter {
        String[] exta;
        public ExtFilter(String ext) {
            exta = new String[1];
            exta[0] = ext;
        }
        public ExtFilter(String[] exta) {
            this.exta = exta;
        }
        public boolean accept(File dir, String name) {
            for (int i=0; i<exta.length; i++)
                if (name.endsWith("." + exta[i]))
                    return(true);
            return(false);
        }
    }
}
