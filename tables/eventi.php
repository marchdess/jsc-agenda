<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Hello Table class
 */
class AgendaTableEventi extends JTable
{
	var $code = null;
	var $datainiziale = null;
	var $datafinale = null;
	var $note = null;
	var $titolo = null;
	var $codc = null;
	var $utente = null;
	var $datainserimento = null;
	var $datamodifica = null;
	var $orainiziale = null;
	var $orafinale = null;
	var $ip = null;
	
		/**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
                parent::__construct('#__eventi', 'code', $db);
        }
        
        function store(){
        	$pattern = '/([0-9]*)\/([0-9]*)\/([0-9]*)/';
        	preg_match($pattern,$this->datainiziale, $data);
        	$this->datainiziale = $data[3] . '-' . $data[2]. '-' . $data[1];
        	preg_match($pattern,$this->datafinale, $data);
        	$this->datafinale = $data[3] . '-' . $data[2]. '-' . $data[1];
        	$user = JFactory::getUser();
        	$this->utente = $user->username;
        	return parent::store();
        }

		function getLastId() {
			$db = $this->getDbo();
			$db->setQuery("select max(code) from #__eventi");
			if ($id = $db->loadResult()){
				return $id;
			} else {
				return 0;
			}
		}
}
