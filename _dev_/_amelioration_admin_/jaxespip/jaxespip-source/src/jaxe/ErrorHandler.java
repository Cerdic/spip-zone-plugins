/*
 * Created on 17.02.2004
 */
package jaxe;

import java.awt.Toolkit;
import java.util.ArrayList;

import javax.swing.JOptionPane;

import org.w3c.dom.Element;

/**
 * The Default ErrorHandler
 * @author tasche
 */
public class ErrorHandler implements ErrorHandlerIf {

    private static final String newline = "\n";
    
    private JaxeDocument doc;

	/**
	 * @param document
	 */
	public ErrorHandler(JaxeDocument document) {
		doc = document;
	}

	public void childNotAllowedInParentdef(Element parentdef, Element defbalise) {
		ArrayList autorisees = doc.cfg.listeSousbalises(parentdef);
		String infos = JaxeResourceBundle.getRB().getString("insertion.BalisesAutorisees") + " " +
			doc.cfg.nomBalise(parentdef) + ":" + newline;
		String infos1 = "";
		int nbnl = 0;
		for (int i=0; i<autorisees.size(); i++) {
			if (i > 0)
				infos1 += ", ";
			if (nbnl < infos1.length()/80) {
				infos1 += newline;
				nbnl++;
			}
			infos1 += (String)autorisees.get(i);
		}
		infos += infos1;
		if (defbalise != null) {
			infos += newline + newline + JaxeResourceBundle.getRB().getString("insertion.BalisesParents") + " " +
				doc.cfg.nomBalise(defbalise) + ": " + newline;
			ArrayList lparents = doc.cfg.listeParents(defbalise);
			String infos2 = "";
			nbnl = 0;
			for (int i=0; i<lparents.size(); i++) {
				if (i > 0)
					infos2 += ", ";
				if (nbnl < infos2.length()/80) {
					infos2 += newline;
					nbnl++;
				}
				infos2 += (String)lparents.get(i);
			}
			infos += infos2;
		}
		JOptionPane.showMessageDialog(doc.textPane.jframe, infos,
			JaxeResourceBundle.getRB().getString("insertion.InsertionBalise"), JOptionPane.ERROR_MESSAGE);
	}

	public void childNotAllowed(String expr, JaxeElement parent, Element defbalise) {
		String infos = JaxeResourceBundle.getRB().getString("insertion.Expression") + " " + expr;
        
		if (infos.length() > 90) {
			int p=0;
			for (int i=0; i<infos.length(); i++) {
				if (i-p > 80 && (infos.charAt(i) == ' ' || infos.charAt(i) == '|')) {
					infos = infos.substring(0,i) + "\n" + infos.substring(i);
					p = i;
				}
			}
		}
		JOptionPane.showMessageDialog(doc.textPane.jframe, infos,
			JaxeResourceBundle.getRB().getString("insertion.InsertionBalise"), JOptionPane.ERROR_MESSAGE);
	}

	public void notInRootError(Element defbalise) {
		Toolkit.getDefaultToolkit().beep();
		JOptionPane.showMessageDialog(doc.textPane.jframe,
			JaxeResourceBundle.getRB().getString("insertion.SousRacine"),
			JaxeResourceBundle.getRB().getString("insertion.InsertionBalise"),
			JOptionPane.ERROR_MESSAGE);
	}

	public void editNotAllowed(JaxeElement parent, Element defbalise) {
		Toolkit.getDefaultToolkit().beep();
		JOptionPane.showMessageDialog(doc.textPane.jframe,
			JaxeResourceBundle.getRB().getString("insertion.EditionInterdite") +
			" " + parent.noeud.getNodeName(),
			JaxeResourceBundle.getRB().getString("insertion.InsertionBalise"),
			JOptionPane.ERROR_MESSAGE);
	}
        
	public void textNotAllowed(JaxeElement element) {
		String infos = JaxeResourceBundle.getRB().getString("erreur.InsertionInterdite") + " " + element.noeud.getNodeName();
		JOptionPane.showMessageDialog(doc.textPane.jframe, infos,
			JaxeResourceBundle.getRB().getString("document.Insertion"), JOptionPane.ERROR_MESSAGE);

	}

}
