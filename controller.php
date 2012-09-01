<?php
/**
 * @package    Joomla
 * @subpackage Components
 * @license    GNU/GPL
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
//require_once ("lib/evento.php");

/**
 * Ecdl Component Controller
 *
 * @package    Joomla
 * @subpackage Components
 */
class AgendaController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	function display()
	{
		$model = & $this->getModel('evento'); 
		$view = JRequest::getString('view');
		if (empty($view)){
			$view = 'Mensile';
		}
		$view = & $this->getView($view, 'html');
		$view->setModel( $model );
		$view->display();
	}

	function evento()
	{
		
		$model = & $this->getModel( 'evento' );
		
		if (isset($_POST['registra'])) {
			$err = false;
			$_POST['orainiziale'] = preg_replace("/\./", ":", $_POST['orainiziale']);
			$_POST['orafinale'] = preg_replace("/\./", ":", $_POST['orafinale']);
			if ($_POST['titolo']=='' || $_POST['datainiziale']=='' || $_POST['datafinale']=='') {
				$err = true;
			}
			$dettaglio= array(
				'titolo' 		=> $_POST['titolo'],
				'note' 			=> $_POST['note'],
				'datainiziale' 	=> $_POST['datainiziale'],
				'datafinale' 	=> $_POST['datafinale'],
				'orainiziale' 	=> $_POST['orainiziale'],
				'orafinale' 	=> $_POST['orafinale'],
				'codc' 			=> $_POST['icodc'],
				'ip' 			=> $_SERVER['REMOTE_ADDR']
			);
			if (isset($_REQUEST['code'])) {
				$dettaglio['code']=$_REQUEST['code'];
				$dettaglio['datamodifica'] = date('Y-m-d');
			} else {
				$dettaglio['datainserimento'] = date('Y-m-d');
				$dettaglio['utente'] = JFactory::getUser()->username;
			}

			if ($_POST['icodc']=='C') {
				$classi=array();
				foreach ($_POST['classi'] as $c=>$v) {
					$classi[] = $c;
				}
				$dettaglio['classi'] = $classi;
			}
			if (!$err) {
				$model->dettaglio = $dettaglio;
				$model->registra();
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_agenda', false), "Evento non aggiornato");
			}
			$this->setRedirect(JRoute::_('index.php?option=com_agenda', false), "Evento Inserito");
		}

		if (isset($_POST['elimina'])){
			$model->dettaglio['code'] = JRequest::getInt( 'code' );
			if($model->elimina()) {
				$this->setRedirect(JRoute::_('index.php?option=com_agenda', false), "Evento cancellato");
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_agenda', false), "Evento non cancellato");
			}
		}
		
		$view = JRequest::getString('view');
		if (empty($view)){
			$view = 'evento';
		}
		$view = & $this->getView($view, 'html');
		$view->setModel( $model );
		$view->display();
		

	}
}
