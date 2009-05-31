/*
 * Created on 17.02.2004
 */
package jaxe;

import org.w3c.dom.Element;

/**
 * The Errorhandler shows the Errors to the Users. 
 * It can be overwritten to add better Errorhandling, like
 * displaying Tips.
 *  
 * @author tasche
 */
public interface ErrorHandlerIf {
	/**
	 * The User tried to add an Element before or after the Root-Element
	 * @param defbalise Element the User tried to add
	 */
	public void notInRootError(Element defbalise);
	
	/**
	 * The User tried to add an Element in an Node that is not editable
	 * @param parent This node was edited
	 * @param defbalise This node was not inserted
	 */
	public void editNotAllowed(JaxeElement parent, Element defbalise);
	
	/**
	 * A child was not inserted because it is not allowed in the parent-node 
	 * @param parentdef Parent
	 * @param defbalise Child that should have been inserted
	 */
	public void childNotAllowedInParentdef(Element parentdef, Element defbalise);
	
	/**
	 * The childis not allowed in the Parent-Node
	 * @param expr Expr what is allowed in the Parent-Node
	 * @param parent The Parent
	 * @param defbalise The child that should have been inserted
	 */
	public void childNotAllowed(String expr, JaxeElement parent, Element defbalise);
	
	/**
	 * It is not allowed to insert Text into this Element
	 * @param element no Text allowed here
	 */
	public void textNotAllowed(JaxeElement element);
}