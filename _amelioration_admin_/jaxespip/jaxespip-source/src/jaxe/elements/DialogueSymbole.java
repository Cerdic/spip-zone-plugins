/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Component;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.Rectangle;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.io.File;
import java.util.ArrayList;

import javax.swing.BorderFactory;
import javax.swing.Icon;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;

import jaxe.JaxeResourceBundle;

import org.w3c.dom.Element;

public class DialogueSymbole extends JDialog implements ActionListener {

    Element el;
    JFrame jframe;
    boolean valide = false;
    File[] liste;
    JLabel[] labels;
    int ichoix = -1;

    public DialogueSymbole(JFrame jframe, Element el) {
        super(jframe, JaxeResourceBundle.getRB().getString("symbole.Insertion"), true);
        this.jframe = jframe;
        this.el = el;
        File dossierSymboles = new File("symboles");
        if (!dossierSymboles.exists()) {
            JOptionPane.showMessageDialog(jframe, JaxeResourceBundle.getRB().getString("erreur.SymbolesNonTrouve"),
                JaxeResourceBundle.getRB().getString("erreur.Erreur"), JOptionPane.ERROR_MESSAGE);
            return;
        }
        liste = chercherImages(dossierSymboles);
        JPanel cpane = new JPanel(new BorderLayout());
        setContentPane(cpane);
        GridLayout grille = new GridLayout((int)Math.ceil(liste.length / 13.0), 13, 10, 10);
        JPanel spane = new JPanel(grille);
        cpane.add(spane, BorderLayout.CENTER);
        
        MyMouseListener ecouteur = new MyMouseListener();
        labels = new JLabel[liste.length];
        for (int i=0; i<liste.length; i++) {
            Icon ic = new ImageIcon(liste[i].getPath());
            JLabel label = new JLabel(ic);
            label.addMouseListener(ecouteur);
            labels[i] = label;
            spane.add(label);
        }

        JPanel bpane = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        JButton boutonAnnuler = new JButton(JaxeResourceBundle.getRB().getString("bouton.Annuler"));
        boutonAnnuler.addActionListener(this);
        boutonAnnuler.setActionCommand("Annuler");
        bpane.add(boutonAnnuler);
        JButton boutonOK = new JButton(JaxeResourceBundle.getRB().getString("bouton.OK"));
        boutonOK.addActionListener(this);
        boutonOK.setActionCommand("OK");
        bpane.add(boutonOK);
        cpane.add(bpane, BorderLayout.SOUTH);
        getRootPane().setDefaultButton(boutonOK);
        ichoix = -1;
        choix(0);
        Rectangle r = jframe.getBounds();
        setLocation(r.x + r.width/4, r.y + r.height/4);
        pack();
    }

    public static File[] chercherImages(File dossier) {
        File[] liste = dossier.listFiles();
        ArrayList res = new ArrayList();
        for (int i=0; i<liste.length; i++)
            if (liste[i].isDirectory())
                res.addAll(toArrayList(chercherImages(liste[i])));
            else if (liste[i].isFile()) {
                String nomf = liste[i].getName();
                int ip = nomf.lastIndexOf('.');
                if (ip != -1) {
                    String ext = nomf.substring(ip+1).toLowerCase();
                    // si on trouve un png avec le même nom que le gif, on prend le png
                    if ("png".equals(ext))
                        res.add(liste[i]);
                    else if ("gif".equals(ext)) {
                        String nomfpng = nomf.substring(0, ip) + ".png";
                        boolean trouv = false;
                        for (int j=0; j<liste.length && !trouv; j++)
                            if (nomfpng.equals(liste[j].getName()))
                                trouv = true;
                        if (!trouv)
                            res.add(liste[i]);
                    }
                }
            }
        return(toFileArray(res));
    }
    
    public static ArrayList toArrayList(Object[] tableau) {
        ArrayList res = new ArrayList();
        for (int i=0; i<tableau.length; i++)
            res.add(tableau[i]);
        return(res);
    }

    public static File[] toFileArray(ArrayList al) {
        File[] res = new File[al.size()];
        for (int i=0; i<al.size(); i++)
            res[i] = (File)al.get(i);
        return(res);
    }

    public boolean afficher() {
        if (ichoix == -1)
            return(false);
        show();
        return(valide);
    }

    public String fichierChoisi() {
        String chemin = liste[ichoix].getPath();
        // sur Windows, on transforme les \ en /
        if (File.separatorChar != '/')
            chemin = chemin.replace(File.separatorChar, '/');
        return(chemin);
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        if ("OK".equals(cmd)) {
            valide = true;
            setVisible(false);
        } else if ("Annuler".equals(cmd)) {
            valide = false;
            setVisible(false);
        }
    }

    protected void choix(int ich) {
        if (ichoix != -1) {
            JLabel label = labels[ichoix];
            label.setBorder(null);
        }
        ichoix = ich;
        JLabel label = labels[ichoix];
        label.setBorder(BorderFactory.createLineBorder(Color.darkGray));
    }
    
    class MyMouseListener extends MouseAdapter {
        public MyMouseListener() {
            super();
        }
        public void mouseClicked(MouseEvent e) {
            Component c = e.getComponent();
            for (int i=0; i<labels.length; i++)
                if (labels[i] == c)
                    choix(i);
        }
    }
}
