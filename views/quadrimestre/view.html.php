<?php
/**
 * @package    Joomla
 * @subpackage Components
 * @license    GNU/GPL
*/
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * HTML View class for the Agenda Component
 *
 * @package    com_agenda
 */
 
class AgendaViewQuadrimestre extends JView
{
    function display($tpl = null)
    {
    	//global $mainframe;
    	//$pathway = &$mainframe->getPathway();
    	//$pathway->addItem( 'Agenda Mensile' , '');
    	
    	$model = &$this->getModel( 'evento' );
        

        
        $document =& JFactory::getDocument();
        
        $document->addStyleSheet('components/com_agenda/calendario.css');
    	
 		$this->canAdd = $model->isPersonale();
 		
        parent::display($tpl);
    }
    

    
}
