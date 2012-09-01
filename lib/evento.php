<?php
defined( '_JEXEC' ) or die( 'Restricted access' );


class Evento{
		
	public $dettaglio = array();
	public $classi = array();

	public $settimana = array('LUN', 'MAR', 'MER', 'GIO', 'VEN', 'SAB', 'DOM');
	public $mesi = array(
			  9=>array('n'=>'set','nome'=>'settembre'),
			  10=>array('n'=>'ott','nome'=>'ottobre'),
			  11=>array('n'=>'nov','nome'=>'novembre'),
			  12=>array('n'=>'dic','nome'=>'dicembre'),
			  1=>array('n'=>'gen','nome'=>'gennaio'),
              2=>array('n'=>'feb','nome'=>'febbraio'),
			  3=>array('n'=>'mar','nome'=>'marzo'),
			  4=>array('n'=>'apr','nome'=>'aprile'),
			  5=>array('n'=>'mag','nome'=>'maggio'),
			  6=>array('n'=>'giu','nome'=>'giugno'),
			  7=>array('n'=>'lug','nome'=>'luglio'),
			  8=>array('n'=>'ago','nome'=>'agosto')
		);	
		
	function __construct() {
		
		$this->dettaglio=array(
			'titolo' => '',
			'note' => '',
			'datainiziale' => '',
			'datafinale' => '',
			'orainiziale' => '',
			'orafinale' => '',
			'codc' => '',
			'utente' => $_SESSION['logusername'],
			'datainserimento' => date('d/m/Y'),
			'datamodifica' => date('d/m/Y')
		);
	}
	
	function registra() {
		global $db ;

		if ($this->dettaglio['orainiziale']=='') $this->dettaglio['orainiziale']='08:00'; 
		if ($this->dettaglio['orafinale']=='') $this->dettaglio['orafinale']='13:00'; 

		if (!isset($this->dettaglio['code'])) { 
			if ($tab = esegui('eventi', $this->dettaglio, 'insert')){ 
				$r = "Inserimenti Effettuati: ".$tab;
				$next = getRiga($db->query("select currval('eventi_code_seq') as id"));
				foreach ($this->classi as $classe) {
					$db->query("insert into eventi_classi (code, classe) values ({$next['id']}, '{$classe}')");
				}
			} else {
				$r = "Problemi con l'inserimento";
			}
		} else {
			if ($tab = esegui('eventi', $this->dettaglio, 'update', 'code = '.$this->dettaglio['code'])) {
				$r = "Aggiornamenti Effettuati: ". $tab;
				$db->query("delete from eventi_classi where code={$this->dettaglio['code']}");
				foreach ($this->classi as $classe) {
					$db->query("insert into eventi_classi (code, classe) values ({$this->dettaglio['code']}, '{$classe}')");
				}
			} else {
				$r = "Problemi con l'aggiornamento";
			}	
		}
		//print '<h2>'.$r.'</h2>';
	}
	
	function searchCode($code) {
		$db = & JFactory::getDbo();
		$sql = "select * from eventi join eventi_categorie on eventi.codc=eventi_categorie.codc where code={$code}";
		$db->setQuery($sql);
		
		if ($this->dettaglio=$db->loadAssoc()) {
			$this->dettaglio['note'] = stripslashes($this->dettaglio['note']);
			$this->dettaglio['titolo'] = stripslashes($this->dettaglio['titolo']);
			$this->dettaglio['orafinale'] = ereg_replace(":00$","",$this->dettaglio['orafinale']);
			$this->dettaglio['orainiziale'] = ereg_replace(":00$","",$this->dettaglio['orainiziale']);
			$this->dettaglio['datainiziale'] = ereg_replace("-","/",$this->dettaglio['datainiziale']);
			$this->dettaglio['datafinale'] = ereg_replace("-","/",$this->dettaglio['datafinale']);
			$this->getClassi();
			return true;
		} else {
			return false;
		}
	}
	
	function searchDate($data, $categoria='', $classe='') {
		$db = & JFactory::getDbo();
		$eventi = array();
		$sqlCategoria = '';
		if ($categoria!='') {
			$sqlCategoria = " and (eventi_categorie.codc='{$categoria}' or eventi_categorie.codc='F' or eventi_categorie.codc='CS') ";
		}
		
		$sql = "select * from eventi join eventi_categorie on eventi.codc=eventi_categorie.codc where datainiziale<='{$data}' and datafinale>='{$data}' {$sqlCategoria} order by orainiziale;";

		$db->setQuery($sql);
		if ($tab=$db->loadAssocList() && !empty($tab)) {
			foreach ($tab as $dettaglio){
				$evento=new Evento();
				$dettaglio['note'] = stripslashes($dettaglio['note']);
				$dettaglio['titolo'] = stripslashes($dettaglio['titolo']);
				$dettaglio['orafinale'] = ereg_replace(":00$","",$dettaglio['orafinale']);
				$dettaglio['orainiziale'] = ereg_replace(":00$","",$dettaglio['orainiziale']);
				$dettaglio['datainiziale'] = ereg_replace("-","/",$dettaglio['datainiziale']);
				$dettaglio['datafinale'] = ereg_replace("-","/",$dettaglio['datafinale']);
				$evento->dettaglio = $dettaglio;
				$evento->getClassi();
				if ($dettaglio['globale']!='S' && $classe!='') {
					$ok = false;
					foreach($evento->classi as $c) {
						if ($classe == $c) 
							$ok = true;
					}
				} else {
					$ok = true;
				}
				if ($ok)
					$eventi[] = $evento;
			}
		}
		return $eventi;
	}
	
	function getClassi() {
		$db = & JFactory::getDbo();
		$sql = "select * from eventi_classi where code=".$this->dettaglio['code'];
		$db->setQuery($sql);
		if ($tab=$db->loadAssocList() && !empty($tab)) {
			$this->classi=array();
			foreach ($tab as $riga){
				$this->classi[] = $riga['classe'];
			}
		}
	}
	
	function getHTMLTitolo() {
		$m='<div style="background-color:'.$this->dettaglio['colore'].' !important" class="tabEvento !important">';
		$m.= '<a class="tabTitoloEvento linkevento" title="'.$this->getHTMLNote().'" href="evento.php?code='.$this->dettaglio['code'].'&show" >'.$this->dettaglio['orainiziale'].' - '.$this->dettaglio['titolo'].'</a>';
		if  ($this->isOwner()) {
			$m .= '<a href="evento.php?code='.$this->dettaglio['code'].'" title="Modifica" class="nostampa">';
			$m .= '<img src="img/edit.png" alt="Modifica"/></a>';
		}
		$m.="</div>";
		return $m;
	}
	
	function getHTMLNote() {
		$m='';
		$m .= $this->dettaglio['note'];
		$m .= ' registrato da ' . $this->dettaglio['utente'] . ' il ' . $this->dettaglio['datainserimento'];
		return $m;
	}
	
	function isOwner() {
		$is = ($_SESSION['logusername']==($this->dettaglio['utente']) || ($_SESSION['logusername'])=='dessolis' || ($_SESSION['logusername'])=='presidenza' || ($_SESSION['logusername'])=='gbalboni');
		return $is;
	}
	
	function elimina() {
		global $db;
		if ($this->dettaglio['code'] != 0 && $this->isOwner()) {
			$sql = "delete from eventi where code = {$this->dettaglio['code']}";
			if (!DB::isError($r = $db->query($sql))) {
				$sql = "delete from eventi_classi where code={$this->dettaglio['code']}";
				if (!DB::isError($r = $db->query($sql))) {
					return true;
				}
			}
		}
		return false;
	}
}

?>