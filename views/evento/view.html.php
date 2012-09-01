<?php
/**
 * @package    Joomla
 * @subpackage Components
 * @license    GNU/GPL
*/
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
//require_once "components/com_agenda/lib/comuni.php";

/**
 * HTML View class for the Agenda Component
 *
 * @package    com_agenda
 */
 
class AgendaViewEvento extends JView
{
    function display($tpl = null)
    {
    	
    	$pathway = &JFactory::getApplication()->getPathway();
    	$pathway->addItem( 'Agenda' , '');
        $app = & JFactory::getApplication();
    	$edit = JRequest::getInt('edit', 0);
        $show = JRequest::getInt('show', 0);
        
        $nuovo = empty($edit) && empty($show); 
        $code = !empty($edit) ? $edit : (!empty($show)? $show : 0);
        
        
    	$evento = &$this->getModel( 'evento' );
        
        $document =& JFactory::getDocument();
        
        $document->addStyleSheet('components/com_agenda/calendario.css');
    	
 	$h1 = "Nuovo Evento";
        
        if (empty($code) && !$evento->isPersonale() && !$evento->isOperatore()) {
            $app->redirect(JRoute::_('index.php?option=com_agenda'), "Non sei autorizzato");
	   }
        if (!empty($code)) {
            if ($evento->searchCode($code)) {
                if (empty($show) && !$evento->isOwner()) {
                    $app->redirect(JRoute::_('index.php'), "Non sei autorizzato");
                }
                if ($edit) {
                        $h1 = "Modifica evento";
                } else {
                        $h1 = "Mostra evento";
                }
            }
            //die("<pre>".print_r($evento,true)."</pre>");
        }
        $naviga = $evento->getDatiNavigazione(); 
        $data = $naviga['giorno'] . '/' . $naviga['mese'] . '/' . $naviga['anno']; 
        $db = & JFactory::getDbo(); 

        if (empty($code))	{ 
                $evento->dettaglio['datainiziale'] = $data;
                $evento->dettaglio['datafinale'] = $data; 
        }


        $this->readonly = $show ? 'readonly="readonly"' : '';
        $this->show = !empty($show);
        $this->code = $code;
        $this->naviga = $naviga;
        $this->evento = $evento;
        $this->h1 = $h1;
        parent::display($tpl);
    }
    

    
}
