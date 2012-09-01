<?php
/**
 * @package    Joomla
 * @subpackage Components
 * components/com_ecdl/ecdl.php
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Require the base controller
 
require_once( JPATH_COMPONENT.DS.'controller.php' );
 
// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
 
// Create the controller
$classname    = 'AgendaController'.$controller;
$controller   = new $classname( );
$view 	= JRequest::getVar( 'view' );
$task	= JRequest::getVar( 'task' );
$user	= & JFactory::getUser();
$autorizzati =array(62, 65, 67); // admin ecdl segreteria


//dump ($user, 'user');

//if ( in_array($user->id, $autorizzati) || $view == 'sessioniout' ){

	// Perform the Request task
	$controller->execute( $task );
	 
	// Redirect if set by the controller
	$controller->redirect();

//} else {
	
//	JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
//}