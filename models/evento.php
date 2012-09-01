<?php

defined('_JEXEC') or die('Restricted access');

define ('CALENDAR_ROOT', JPATH_BASE . DS . 'components/com_agenda/lib/Calendar/');
require_once 'components/com_agenda/lib/Calendar/Month/Weekdays.php';
require_once 'components/com_agenda/lib/Calendar/Week.php';
require_once 'components/com_agenda/lib/Calendar/Day.php';


jimport('joomla.application.component.model');

class AgendaModelEvento extends JModel {

    public $dettaglio = array();
    public $classi = array();
    public $model = null;
    public $settimana = array('LUN', 'MAR', 'MER', 'GIO', 'VEN', 'SAB', 'DOM');
    public $mesi = array(
        9 => array('n' => 'set', 'nome' => 'settembre'),
        10 => array('n' => 'ott', 'nome' => 'ottobre'),
        11 => array('n' => 'nov', 'nome' => 'novembre'),
        12 => array('n' => 'dic', 'nome' => 'dicembre'),
        1 => array('n' => 'gen', 'nome' => 'gennaio'),
        2 => array('n' => 'feb', 'nome' => 'febbraio'),
        3 => array('n' => 'mar', 'nome' => 'marzo'),
        4 => array('n' => 'apr', 'nome' => 'aprile'),
        5 => array('n' => 'mag', 'nome' => 'maggio'),
        6 => array('n' => 'giu', 'nome' => 'giugno'),
        7 => array('n' => 'lug', 'nome' => 'luglio'),
        8 => array('n' => 'ago', 'nome' => 'agosto')
    );

    function __construct() {
        parent::__construct();
        $this->dettaglio = array(
            'titolo' => '',
            'note' => '',
            'datainiziale' => '',
            'datafinale' => '',
            'orainiziale' => '',
            'orafinale' => '',
            'codc' => '',
            'utente' => JFactory::getUser()->username,
            'datainserimento' => date('d/m/Y'),
            'datamodifica' => date('d/m/Y')
        );
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       1.6
     */
    public function getTable($type = 'Eventi', $prefix = 'AgendaTable', $config = array()) {
        JTable::addIncludePath('components' . DS . 'com_agenda' . DS . 'tables');

        return JTable::getInstance($type, $prefix, $config);
    }

    function registra() {

        $eventi = $this->getTable();
        $db = JFactory::getDbo();

        if ($this->dettaglio['orainiziale'] == '')
            $this->dettaglio['orainiziale'] = '08:00';
        if ($this->dettaglio['orafinale'] == '')
            $this->dettaglio['orafinale'] = '13:00';

        if (!$eventi->bind($this->dettaglio)) {
            return JError::raiseWarning(500, $eventi->getError());
        }
        if (!$eventi->store()) {
            JError::raiseError(500, print_r($eventi, true));
        } else {
            $id = $eventi->getLastId();
            foreach ($this->dettaglio['classi'] as $classe) {
                $db->setQuery("insert into #__eventi_classi (code, classe) values ({$id}, '{$classe}')");
                $db->query();
            }
        }
        if (!empty($this->dettaglio['code'])) {
            $db->setQuery("delete from #__eventi_classi where code={$this->dettaglio['code']}");
            $db->query();
            foreach ($this->dettaglio['classi'] as $classe) {
                $db->setQuery("insert into #__eventi_classi (code, classe) values ({$this->dettaglio['code']}, '{$classe}')");
                $db->query();
            }
        }
    }

    function searchCode($code) {
        $db = & JFactory::getDbo();
        $sql = "select * from #__eventi join #__eventi_categorie 
                            on #__eventi.codc=#__eventi_categorie.codc 
                         where #__eventi.code={$code}";
        $db->setQuery($sql);

        if ($this->dettaglio = $db->loadAssoc()) {
            $this->dettaglio['note'] = stripslashes($this->dettaglio['note']);
            $this->dettaglio['titolo'] = stripslashes($this->dettaglio['titolo']);
            $this->dettaglio['orafinale'] = preg_replace("/:00$/", "", $this->dettaglio['orafinale']);
            $this->dettaglio['orainiziale'] = preg_replace("/:00$/", "", $this->dettaglio['orainiziale']);
            $this->dettaglio['datainiziale'] = preg_replace("/-/", "/", $this->dettaglio['datainiziale']);
            $this->dettaglio['datafinale'] = preg_replace("/-/", "/", $this->dettaglio['datafinale']);
            $pattern = '/([0-9]*)\/([0-9]*)\/([0-9]*)/';
            preg_match($pattern, $this->dettaglio['datainiziale'], $data);
            $this->dettaglio['datainiziale'] = $data[3] . '/' . $data[2] . '/' . $data[1];
            preg_match($pattern, $this->dettaglio['datafinale'], $data);
            $this->dettaglio['datafinale'] = $data[3] . '/' . $data[2] . '/' . $data[1];

            //$this->getClassi();
            return true;
        } else {
            return false;
        }
    }

    function searchDate($data, $categoria='', $classe='') {
        $db = & JFactory::getDbo();
        $eventi = array();
        $sqlCategoria = '';
        if ($categoria != '') {
            $sqlCategoria = " and (#__eventi_categorie.codc='{$categoria}' or #__eventi_categorie.codc='F' or #__eventi_categorie.codc='CS') ";
        }

        $sql = "select * 
	   			from #__eventi join #__eventi_categorie on #__eventi.codc=#__eventi_categorie.codc 
				where datainiziale<='{$data}' and datafinale>='{$data}' {$sqlCategoria} 
				order by orainiziale;";
        $db->setQuery($sql);
        if ($tab = $db->loadAssocList()) {
            foreach ($tab as $dettaglio) {
                $evento = new AgendaModelEvento();
                $dettaglio['note'] = stripslashes($dettaglio['note']);
                $dettaglio['titolo'] = stripslashes($dettaglio['titolo']);
                $dettaglio['orafinale'] = preg_replace("/:00$/", "", $dettaglio['orafinale']);
                $dettaglio['orainiziale'] = preg_replace("/:00$/", "", $dettaglio['orainiziale']);
                $dettaglio['datainiziale'] = preg_replace("/-/", "/", $dettaglio['datainiziale']);
                $dettaglio['datafinale'] = preg_replace("/-/", "/", $dettaglio['datafinale']);
                $pattern = '/([0-9]*)\/([0-9]*)\/([0-9]*)/';
                preg_match($pattern, $dettaglio['datainiziale'], $data);
                $dettaglio['datainiziale'] = $data[3] . '/' . $data[2] . '/' . $data[1];
                preg_match($pattern, $dettaglio['datafinale'], $data);
                $dettaglio['datafinale'] = $data[3] . '/' . $data[2] . '/' . $data[1];
                $evento->dettaglio = $dettaglio;
                $evento->getClassi();
                if ($dettaglio['globale'] != 'S' && $classe != '') {
                    $ok = false;
                    foreach ($evento->classi as $c) {
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
        $sql = "select * from #__eventi_classi where code=" . $this->dettaglio['code'];
        $db->setQuery($sql);
        $this->classi = array();
        if ($tab = $db->loadAssocList()) {
            foreach ($tab as $riga) {
                $this->classi[] = $riga['classe'];
            }
        }
    }

    function getHTMLTitolo() {
        $m = '<div style="background-color:' . $this->dettaglio['colore'] . ' !important" class="tabEvento !important">';
        $m.= '<a class="tabTitoloEvento linkevento" title="' . $this->getHTMLNote() . '" href="'.JRoute::_('index.php?option=com_agenda&task=evento&show=' . $this->dettaglio['code'] ).'" >' . $this->dettaglio['orainiziale'] . ' - ' . $this->dettaglio['titolo'] . '</a>';
        if ($this->isOwner()) {
            $m .= '<a href="' . JRoute::_("index.php?option=com_agenda&task=evento&edit=" . $this->dettaglio['code']) . '" title="Modifica" class="nostampa">';
            $m .= '<img src="components/com_agenda/img/edit.png" alt="Modifica"/></a>';
        }
        $m.="</div>";
        return $m;
    }

    function getHTMLNote() {
        $m = '';
        $m .= $this->dettaglio['note'];
        $m .= ' registrato da ' . $this->dettaglio['utente'] . ' il ' . $this->dettaglio['datainserimento'];
        return $m;
    }

    function isOperatore() {
        $db = & JFactory::getDbo();
        $user = & JFactory::getUser();
	   if (version_compare(JVERSION, '1.6.0', 'ge')) {
	        $sql = "select group_id from #__user_usergroup_map where user_id=" . $user->id;
	        $db->setQuery($sql);
	        $op = false;
	        if ($groups = $db->loadObjectList()) {
	            foreach ($groups as $group) {
	                if ($group->group_id >= 4 && $group->group_id <= 8) {
	                    $op = true;
	                    break;
	                }
	            }
	        }
	        return $op;
	     } else {
			$gruppi = array(32,25,24,23,21);
			return in_array($user->gid, $gruppi);
		}
        //die("<pre>".print_r($groups,true)."</pre>");
    }

    function isPersonale() {
        $db = & JFactory::getDbo();
        $user = & JFactory::getUser();
	   if (version_compare(JVERSION, '1.6.0', 'ge')) {
	        $sql = "select group_id, title 
	                    from #__user_usergroup_map join #__usergroups on #__user_usergroup_map.group_id=#__usergroups.id 
	                    where user_id=" . $user->id;
	        $db->setQuery($sql);
	        $op = false;
	        if ($groups = $db->loadObjectList()) {
	            foreach ($groups as $group) {
	                if (($group->group_id >= 4 && $group->group_id <= 8) || $group->title == 'Docenti') {
	                    $op = true;
	                    break;
	                }
	            }
	        }
	        return $op;
		} else {
			$gruppi = array(18,19,20);
			return in_array($user->gid, $gruppi) || $this->isOperatore();
		}
    }

    function isOwner() {
        $user = & JFactory::getUser();
        $is = ($user->username == ($this->dettaglio['utente']) || $this->isOperatore());
        return $is;
    }

    function elimina() {
        $db = & JFactory::getDbo();
        if ($this->dettaglio['code'] != 0 && $this->isOwner()) {
            $sql = "delete from #__eventi where code = {$this->dettaglio['code']}";
            $db->setQuery($sql);
            if ($db->query()) {
                $sql = "delete from #__eventi_classi where code={$this->dettaglio['code']}";
                $db->setQuery($sql);
                $db->query();
                return true;
            }
        }
        return false;
    }

    function getDatiNavigazione() {
        if (!isset($_SESSION['naviga'])) {
            $_SESSION['naviga']['mese'] = date('n');
            $_SESSION['naviga']['anno'] = date('Y');
            $_SESSION['naviga']['giorno'] = date('d');
            $_SESSION['naviga']['codc'] = '';
            $_SESSION['naviga']['classe'] = '';
            $_SESSION['naviga']['periodo'] = 'M';
        }
        $mese = JRequest::getInt('mese',0);
        $anno = JRequest::getInt('anno',0);
        $giorno = JRequest::getInt('giorno',0);
        $codc = JRequest::getVar('codc',0);
        $classe = JRequest::getVar('classe',0);
        $periodo = JRequest::getVar('periodo',0);

        if (!empty($mese))
            $_SESSION['naviga']['mese'] = $mese;
        if (!empty($anno))
            $_SESSION['naviga']['anno'] = $anno;
        if (!empty($giorno))
            $_SESSION['naviga']['giorno'] = $giorno;
        if (!empty($codc))
            $_SESSION['naviga']['codc'] = $codc;
        if (!empty($classe))
            $_SESSION['naviga']['classe'] = $classe;
        if (!empty($periodo))
            $_SESSION['naviga']['periodo'] = $periodo;

        return $_SESSION['naviga'];
    }

    function getReq($campo) {
        if (isset($_REQUEST[$campo])) {
            return $_REQUEST[$campo];
        } else {
            return '';
        }
    }

    function elencoOption($tabella, $chiave, $elenco, $selected, $where="1=1") {
        $db = & JFactory::getDbo();
        if ($chiave == $elenco) {
            $sql = "select $chiave from $tabella where $where";
        } else {
            $sql = "select $chiave,$elenco from $tabella where $where";
        }
        $db->setQuery($sql);
        if (!$tab = $db->loadAssocList()) {
            //die(errore_grave("Option",$tab->getMessage()." sql=$sql"));
            return null;
        }
        $r = "";
        foreach ($tab as $riga) {
            $r .= "\n<option value=\"" . $riga[$chiave] . "\"";
            if ($riga[$chiave] == $selected) {
                $r.=" selected=\"selected\"";
            }
            $r .=">" . $riga[$elenco] . "</option>";
        }
        return $r;
    }

    function tabellaMese($mese='', $anno='') {


        $naviga = $this->getDatiNavigazione();
        if ($mese != '') {
            $naviga['mese'] = $mese;
            $naviga['anno'] = $anno;
        }
        $m = '';
        $settimana = $this->settimana;
        $mesi = $this->mesi;

        $Month = new Calendar_Month_Weekdays($naviga['anno'], $naviga['mese']);

        $Month->build();

        $m.= "<div class=\"divMese\">";

        $m.= "<table class=\"tabMese\">\n";

        $m.= "<caption>{$mesi[$naviga['mese']]['nome']} {$naviga['anno']}";
        if ($naviga['classe'] != '') {
            $db = $this->getDbo();
            $db->setQuery("select title from #__usergroups where id=" . $naviga['classe']);
            $classe = $db->loadResult();
            $m.= " - Classe {$classe}";
        }
        $m.= "</caption>";

        $m.= '<tr>';
        foreach ($settimana as $giorno)
            $m.= '<th class="tabGiornoSettimana">' . $giorno . '</th>';
        $m.= '</tr>';
	//	die ("<pre>".print_r($naviga,true)."</pre>");

        while ($Day = $Month->fetch()) {
            if ($Day->isFirst()) {
                $m.= "<tr class=\"tabRiga\">\n";
            }

            if ($Day->isEmpty()) {
                $m.= "<td class=\"tabCellaGiornoVuoto\">&nbsp;</td>\n";
            } else {
                $eventi = array();
                $classEvt = 'tabCellaGiorno ';
                if (count($eventi = $this->searchDate($naviga['anno'] . '-' . $naviga['mese'] . '-' . $Day->thisDay(), $naviga['codc'], $naviga['classe'])) > 0) {
                    //$classEvt .= 'tabCellaEvento ';
                    //foreach ($eventi as $evento)
                    //if ($evento->dettaglio['globale']=='S') {
                    //$classEvt .= 'tabCellaFestivo ';
                    //}
                } else {
                    if ($Day->isLast()) {
                        $classEvt .= 'tabCellaFestivo ';
                    }
                }
                if ($Day->thisDay() . "/{$Day->thisMonth()}/{$Day->thisYear()}" == date('j/n/Y')) {
                    $classEvt .= 'tabCellaOggi ';
                }
                $m.= "<td class=\"{$classEvt}\">";
                $m.= '<span class="tabGiorno">' . $Day->thisDay() . '&nbsp;' . $this->linkNuovoEvento($Day->thisDay(), $Day->thisMonth(), $Day->thisYear()) . '</span>';
                foreach ($eventi as $evento) {
                    //$m.="<br/>";
                    $m.= $evento->getHTMLTitolo();
                }
                $m.= "</td>\n";
            }

            if ($Day->isLast()) {
                $m.= "</tr>\n";
            }
        }

        $m.= "</table>\n";

        $m.= "</div>\n";
        return $m;
    }

    function tabellaSettimana() {

        $naviga = $this->getDatiNavigazione();

        $m = '';

        $evento = $this;
        $settimana = $evento->settimana;
        $mesi = $evento->mesi;

        $Week = new Calendar_Week($naviga['anno'], $naviga['mese'], $naviga['giorno']);

        $primo = $Week->thisWeek('array');

        $m.= "<div class=\"divMese tabSettimana\">";

        $m.= "<table class=\"tabMese\">\n";

        $m.= "<caption>Settimana {$primo['day']} {$mesi[$primo['month']]['nome']} {$primo['year']}";
        if ($naviga['classe'] != '')
            $m.= " - Classe {$naviga['classe']}";
        $m.= "</caption>";

        for ($i = 0; $i < 7; $i++) {
            $m.= "<tr class=\"tabRiga\">\n";

            $Day = new Calendar_Day($primo['year'], $primo['month'], $primo['day'] + $i);
            $Day->adjust();

            $eventi = array();
            $classEvt = 'tabCellaGiorno ';

            if (count($eventi = $this->searchDate("{$Day->thisYear()}-{$Day->thisMonth()}-{$Day->thisDay()}", $naviga['codc'], $naviga['classe'])) > 0) {
                //$classEvt .= 'tabCellaEvento ';
                //foreach ($eventi as $evento)
                //	if ($evento->dettaglio['globale']=='S'){
                //		$classEvt .= 'tabCellaFestivo';
                //	}
            } else {
                if ($i == 6) {
                    $classEvt .= 'tabCellaFestivo ';
                }
            }
            if ($Day->thisDay() . "/{$Day->thisMonth()}/{$Day->thisYear()}" == date('j/n/Y')) {
                $classEvt .= 'tabCellaOggi ';
            }
            $m.= "<td class=\"{$classEvt}\">";
            $m.= '<span class="tabGiorno">' . $settimana[$i] . ' ' . $Day->thisDay() . '&nbsp;';
            $m.= $this->linkNuovoEvento($Day->thisDay(), $Day->thisMonth(), $Day->thisYear()) . '</span>';
            foreach ($eventi as $evento) {
                //$m.="<br/>";
                $m.= $evento->getHTMLTitolo();
            }
            $m.= "</td>\n";


            $m.= "</tr>\n";
        }

        $m.= "</table>\n";

        $m.= "</div>\n";
        return $m;
    }

    function tabellaQuadrimestre() {
        $naviga = $this->getDatiNavigazione();

        $m = '';

        $evento = $this;
        $settimana = $evento->settimana;
        $mesi = $evento->mesi;
        if ($naviga['mese'] > 8 || $naviga['mese'] == 1) {
            $quad = array(9, 10, 11, 12, 1);
        } else {
            $quad = array(2, 3, 4, 5, 6);
        }
        if ($naviga['mese'] == 1)
            $_SESSION['naviga']['anno']--;
        foreach ($quad as $mese) {
            $_SESSION['naviga']['mese'] = $mese;
            if ($mese == 1)
                $_SESSION['naviga']['anno']++;
            $m.= $this->tabellaMese($_SESSION['naviga']['mese'], $_SESSION['naviga']['anno']);
        }
        $_SESSION['naviga'] = $naviga;
        return $m;
    }

    function navigaSettimanale() {
        $naviga = $this->getDatiNavigazione();
        if ($naviga['giorno'] == '') {
            $naviga['giorno'] = date('d');
        }
        $prec = new Calendar_Day($naviga['anno'], $naviga['mese'], $naviga['giorno'] - 7);
        $prec->adjust();
        $succ = new Calendar_Day($naviga['anno'], $naviga['mese'], $naviga['giorno'] + 7);
        $succ->adjust();
        $m = "";
        $m .= "&nbsp;<span class=\"pulsante nostampa\"><a href=\"".JRoute::_("index.php?option=com_agenda&view=settimanale&anno=" . $prec->thisYear() . "&mese=" . $prec->thisMonth() . "&giorno=" . $prec->thisDay()) . "\" title=\"Settimana precedente\">&nbsp;Precedente&nbsp;</a></span>";
        $m .= "&nbsp;<span class=\"pulsante nostampa\"><a href=\"".JRoute::_("index.php?option=com_agenda&view=settimanale&anno=" . $succ->thisYear() . "&mese=" . $succ->thisMonth() . "&giorno=" . $succ->thisDay()) . "\" title=\"Settimana successiva\">&nbsp;Successiva&nbsp;</a></span>";
        return $m;
    }

    function navigaMensile() {
        $settimana = $this->settimana;
        $mesi = $this->mesi;

        $naviga = $this->getDatiNavigazione();

        $M = new Calendar_Day($naviga['anno'], $naviga['mese'], 1);
        $N = new Calendar_Day($naviga['anno'], $naviga['mese'] + 1, 1);
        $N->adjust();
        $P = new Calendar_Day($naviga['anno'], $naviga['mese'] - 1, 1);
        $P->adjust();

        $m = '';
        $m .= '<span class="navigazioneMensile">';
        $m .= '<form method="POST" action="' . JRoute::_("index.php?option=com_agenda&view=mensile") . '">';

        $m .= '<a href="' . JRoute::_('index.php?option=com_agenda&mese=' . $P->thisMonth() . '&anno=' . $P->thisYear()) . '"><img src="components/com_agenda/img/Prev.gif" alt="Mese precedente" title="Mese precedente"/></a>&nbsp;';
        $m .= '<select name="mese">';
        foreach ($mesi as $n => $meseSel) {
            $m .= '<option value="' . $n . '"' . ($n == $naviga['mese'] ? ' selected="selected"' : '') . '>' . $meseSel['nome'] . '</option>';
        }
        $m .= '</select>&nbsp;';
        $m .= '<select name="anno">';
        for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++) {
            $m .= '<option value="' . $i . '"' . ($i == $naviga['anno'] ? ' selected="selected"' : '') . '>' . $i . '</option>';
        }
        $m .= '</select>&nbsp;';
        $m .= '<input type="submit"  name="selAnnoScol" value="Apri" class="pulsante"/>&nbsp;';
        $m .= '<a href="' . JRoute::_('index.php?option=com_agenda&view=mensile&mese=' . $N->thisMonth() . '&anno=' . $N->thisYear(), true) . '"><img src="components/com_agenda/img/Next.gif" alt="Mese successivo" title="Mese successivo"/></a>&nbsp;';
        $m .= '</form>';
        $m .= '</span>';
        return $m;
    }

    function navigaQuadrimestre() {
        $settimana = $this->settimana;
        $mesi = $this->mesi;
        $naviga = $this->getDatiNavigazione();
        $m = "";

        if ($naviga['mese'] > 8 || $naviga['mese'] == 1) {
            $quad = 1;
            $msucc = 2;
            $mprec = 2;
            $asucc = $naviga['mese'] == 1 ? $naviga['anno'] : $naviga['anno'] + 1;
            $aprec = $naviga['mese'] == 1 ? $naviga['anno'] - 1 : $naviga['anno'];
        } else {
            $quad = 2;
            $msucc = 9;
            $mprec = 9;
            $asucc = $naviga['anno'];
            $aprec = $naviga['anno'] - 1;
        }

        if ($naviga['mese'] == 1)
            $ap = $naviga['anno'] - 1;

        $m .= '<a href="' . JRoute::_('index.php?option=com_agenda&view=quadrimestre&mese=' . $mprec . '&anno=' . $aprec, true) . '" class="nostampa"><img src="components/com_agenda/img/Prev.gif" alt="Quadrimestre precedente" title="Quadrimestre precedente"/></a>&nbsp;';
        $m .= '<a href="' . JRoute::_('index.php?option=com_agenda&view=quadrimestre&mese=' . $msucc . '&anno=' . $asucc, true) . '" class="nostampa"><img src="components/com_agenda/img/Next.gif" alt="Quadrimestre successivo" title="Quadrimestre successivo"/></a>&nbsp;';

        return $m;
    }

    function navigaCategorie() {
        $db = & JFactory::getDbo();
        $naviga = $this->getDatiNavigazione();
        $view = JRequest::getVar('view');
        if (empty($view))
            $view = "mensile";
        $m = '';
        $m .= '<span class="navigazioneCategorie">';
        $m .= '<form method="post" action="' . JRoute::_("index.php?option=com_agenda&view=" . $view) . '">';
        $m.= "<select name=\"codc\" class=\"combobox\">\n";
        $m.= "<option value=\"\">Tutte le categorie</option>\n";
        $sql = "select * from #__eventi_categorie";
        $db->setQuery($sql);
        if ($tab = $db->loadAssocList()) {
            foreach ($tab as $dettaglio) {
                $m.= "<option value=\"{$dettaglio['codc']}\" style=\"background-color: {$dettaglio['colore']}\"";
                if ($naviga['codc'] == $dettaglio['codc'])
                    $m.= " selected";
                $m.= ">{$dettaglio['categoria']}</option>\n";
            }
        }
        $m.= "</select>&nbsp;";
        $m .= '<input type="submit"  name="sel" value="Apri" class="pulsante"/>&nbsp;';
        $m .= '</form>';
        $m .= '</span>';
        return $m;
    }

    function navigaClassi() {
        $db = & JFactory::getDbo();
        $naviga = $this->getDatiNavigazione();
        $m = '';
        $m .= '<span class="navigazioneClassi">';
        $m .= '<form method="POST" action="' . JRoute::_("index.php?option=com_agenda", true) . '">';
        $m.= "<select name=\"classe\" class=\"combobox\">\n";
        $m.= "<option value=\"\">Tutte le classi</option>\n";
        $sql = "select * from #__usergroups order by title";
        $db->setQuery($sql);
        if ($tab = $db->loadAssocList()) {
            foreach ($tab as $dettaglio) {
                $m.= "<option value=\"{$dettaglio['id']}\"";
                if ($naviga['classe'] == $dettaglio['id'])
                    $m.= " selected";
                $m.= ">{$dettaglio['title']}</option>\n";
            }
        }
        $m .= '</select>&nbsp;';
        $m .= '<input type="submit"  name="sel" value="Apri" class="pulsante"/>&nbsp;';
        $m .= '</form>';
        $m .= '</span>';
        return '';
    }

    function pulsantiPeriodo() {
        $m = '';
        $m .= '<br/><span class="pulsantiPeriodo nostampa">';
        $m .= "&nbsp;<a href=\"" . JRoute::_("index.php?option=com_agenda&view=Settimanale", true) . "\" title=\"Vista Settimanale\"><img src=\"components/com_agenda/img/icon-week.gif\" alt=\"Vista settimanale\" style=\"vertical-align: top\"/></a>";
        $m .= "&nbsp;<a href=\"" . JRoute::_("index.php?option=com_agenda&view=Mensile", true) . "\" title=\"Vista Mensile\"><img src=\"components/com_agenda/img/icon-month.gif\" alt=\"Vista mensile\" style=\"vertical-align: top\"/></a>";
        $m .= "&nbsp;<a href=\"" . JRoute::_("index.php?option=com_agenda&view=Quadrimestre", true) . "\" title=\"Vista Quadrimestrale\"><img src=\"components/com_agenda/img/icon-year.gif\" alt=\"Vista quadrimestrale\" style=\"vertical-align: top\"/></a>";
        $m .= '&nbsp;</span><br/>';
        return $m;
    }

    function linkNuovoEvento($giorno, $mese, $anno) {
        if ($this->isPersonale()) {
            $e = "<span class=\"nostampa\"><a href=\"" . JRoute::_("index.php?option=com_agenda&task=evento&mese={$mese}&anno={$anno}&giorno={$giorno}", true) . "\" >";
            $e .= '<img src="components/com_agenda/img/icon-add.gif" alt="Nuovo evento" title="Nuovo evento"/>';
            $e .= '</a></span>';
            return $e;
        } else {
            return '';
        }
    }

}

?>