/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conform�ment aux dispositions de la Licence Publique G�n�rale GNU, telle que publi�e par la Free Software Foundation ; version 2 de la licence, ou encore (� votre choix) toute version ult�rieure.

Ce programme est distribu� dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de d�tail, voir la Licence Publique G�n�rale GNU .

Vous devez avoir re�u un exemplaire de la Licence Publique G�n�rale GNU en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.event.ActionEvent;

import javax.swing.text.TextAction;

public class ActionFonction extends TextAction {

    Fonction fct;
    JaxeDocument doc;

    public ActionFonction(JaxeDocument doc, String titre, String classe) {
        super(titre);
        this.doc = doc;
        try {
            Class c = Class.forName(classe);
            fct = (Fonction) c.newInstance();
        } catch (Exception ex) {
            System.err.println("Erreur: Classe introuvable : " + classe);
            System.err.println(ex.getClass().getName() + " : " + ex.getMessage());
        }
    }
    
    public void actionPerformed(ActionEvent e) {
        if (doc == null || doc.textPane == null || fct == null)
            return;
        if (getTextComponent(e) == null && doc.textPane != null)
            doc.textPane.requestFocus();
        doc.modif = true;
        int start = doc.textPane.getSelectionStart();
        int end = doc.textPane.getSelectionEnd();
        fct.appliquer(doc, start, end);
    }
    
}
