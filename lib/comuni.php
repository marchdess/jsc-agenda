<?php
require_once 'evento.php';
require_once 'Calendar/Month/Weekdays.php';
require_once 'Calendar/Week.php';
require_once 'Calendar/Day.php';



function getDatiNavigazione() {
	if (!isset($_SESSION['naviga'])) {
		$_SESSION['naviga']['mese']= date('n');
		$_SESSION['naviga']['anno']= date('Y');
		$_SESSION['naviga']['giorno']= date('d');
		$_SESSION['naviga']['codc']= '';
		$_SESSION['naviga']['classe']= '';
		$_SESSION['naviga']['periodo']= 'M';
	}
	if (isset($_REQUEST['mese'])) $_SESSION['naviga']['mese']=$_REQUEST['mese'];
	if (isset($_REQUEST['anno'])) $_SESSION['naviga']['anno']=$_REQUEST['anno'];
	if (isset($_REQUEST['giorno'])) $_SESSION['naviga']['giorno']=$_REQUEST['giorno'];
	if (isset($_REQUEST['codc'])) $_SESSION['naviga']['codc']=$_REQUEST['codc'];
	if (isset($_REQUEST['classe'])) $_SESSION['naviga']['classe']=$_REQUEST['classe'];
	if (isset($_REQUEST['periodo'])) $_SESSION['naviga']['periodo']=$_REQUEST['periodo'];
	
	return $_SESSION['naviga'];
}

function getReq($campo) {
	if (isset($_REQUEST[$campo])){
		return $_REQUEST[$campo];
	} else {
		return '';
	}
}
		
function tabellaMese($mese='',$anno='') {

	
	$naviga = getDatiNavigazione();
	if ($mese!='') {
		$naviga['mese']=$mese;
		$naviga['anno']=$anno;
	}
	$m = '';
	$evento = new Evento();
	$settimana = $evento->settimana;
	$mesi = $evento->mesi;
		
	$Month = new Calendar_Month_Weekdays($naviga['anno'],$naviga['mese']);

	$Month->build();

	$m.= "<div class=\"divMese\">";
	
	$m.= "<table class=\"tabMese\">\n";
	
	$m.= "<caption>{$mesi[$naviga['mese']]['nome']} {$naviga['anno']}";
	if ($naviga['classe']!='')
		$m.= " - Classe {$naviga['classe']}";
	$m.= "</caption>";
	
	$m.= '<tr>';
	foreach ($settimana as $giorno) 
		$m.= '<th class="tabGiornoSettimana">'.$giorno.'</th>';
	$m.= '</tr>';
	
	while ($Day = $Month->fetch()) {
    	if ($Day->isFirst()) {
        	$m.= "<tr class=\"tabRiga\">\n";
    	}

    	if ($Day->isEmpty()) {
        	$m.= "<td class=\"tabCellaGiornoVuoto\">&nbsp;</td>\n";
    	} else {
			$eventi = array();
			$classEvt = 'tabCellaGiorno ';
			if (count($eventi = Evento::searchDate($Day->thisDay()."/{$naviga['mese']}/{$naviga['anno']}", $naviga['codc'], $naviga['classe']))>0) {
				//$classEvt .= 'tabCellaEvento ';
				//foreach ($eventi as $evento)
					//if ($evento->dettaglio['globale']=='S') {
						//$classEvt .= 'tabCellaFestivo ';
					//}
			} else{
				if ($Day->isLast()) {
					$classEvt .= 'tabCellaFestivo ';
				}
			}
			if ($Day->thisDay()."/{$Day->thisMonth()}/{$Day->thisYear()}" == date('j/n/Y')) {
				$classEvt .= 'tabCellaOggi ';
			}
	    	$m.= "<td class=\"{$classEvt}\">";
			$m.= '<span class="tabGiorno">'.$Day->thisDay().'&nbsp;'.linkNuovoEvento($Day->thisDay(),$Day->thisMonth(),$Day->thisYear()).'</span>';
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

	$naviga = getDatiNavigazione();

	$m = '';
	
	$evento = new Evento();
	$settimana = $evento->settimana;
	$mesi = $evento->mesi;
			
	$Week = new Calendar_Week($naviga['anno'], $naviga['mese'], $naviga['giorno']);
	
	$primo = $Week->thisWeek('array');

	$m.= "<div class=\"divMese tabSettimana\">";
	
	$m.= "<table class=\"tabMese\">\n";
	
	$m.= "<caption>Settimana {$primo['day']} {$mesi[$primo['month']]['nome']} {$primo['year']}";
	if ($naviga['classe']!='')
		$m.= " - Classe {$naviga['classe']}";
	$m.= "</caption>";
	
	for ($i=0; $i<7; $i++) {
        $m.= "<tr class=\"tabRiga\">\n";
 		
		$Day = new Calendar_Day($primo['year'], $primo['month'], $primo['day'] + $i); $Day->adjust(); 

		$eventi = array();
		$classEvt = 'tabCellaGiorno ';

		if (count($eventi = Evento::searchDate($Day->thisDay()."/{$Day->thisMonth()}/{$Day->thisYear()}", $naviga['codc'], $naviga['classe']))>0) {
			//$classEvt .= 'tabCellaEvento ';
			//foreach ($eventi as $evento)
			//	if ($evento->dettaglio['globale']=='S'){
			//		$classEvt .= 'tabCellaFestivo';
			//	}
		} else{
			if ($i==6) {
				$classEvt .= 'tabCellaFestivo ';
			}
		}
		if ($Day->thisDay()."/{$Day->thisMonth()}/{$Day->thisYear()}" == date('j/n/Y')) {
			$classEvt .= 'tabCellaOggi ';
		}
    	$m.= "<td class=\"{$classEvt}\">";
		$m.= '<span class="tabGiorno">'.$settimana[$i].' '.$Day->thisDay().'&nbsp;';
		$m.= linkNuovoEvento($Day->thisDay(),$Day->thisMonth(),$Day->thisYear()).'</span>';
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
	$naviga = getDatiNavigazione();

	$m = '';
	
	$evento = new Evento();
	$settimana = $evento->settimana;
	$mesi = $evento->mesi;
		if ($naviga['mese']>8 || $naviga['mese']==1) {
		$quad = array(9,10,11,12,1);
	} else {
		$quad = array(2,3,4,5,6);
	}
	if ($naviga['mese']==1) $_SESSION['naviga']['anno']--;
	foreach ($quad as $mese) {
		$_SESSION['naviga']['mese']=$mese;
		if ($mese==1) $_SESSION['naviga']['anno']++;
		$m.= tabellaMese($_SESSION['naviga']['mese'], $_SESSION['naviga']['anno']);
	}
	$_SESSION['naviga']=$naviga;
	return $m;
}

function navigaSettimanale() {
	$naviga = getDatiNavigazione();
	if ($naviga['giorno']=='') {
		$naviga['giorno']=date('d');
	}
	$prec = new Calendar_Day($naviga['anno'], $naviga['mese'], $naviga['giorno']-7); $prec->adjust();
	$succ = new Calendar_Day($naviga['anno'], $naviga['mese'], $naviga['giorno']+7); $succ->adjust();
	$m = "";
	$m .= "&nbsp;<span class=\"pulsante nostampa\"><a href=\"index.php?option=com_agenda&anno=" . $prec->thisYear() . "&mese=" . $prec->thisMonth() . "&giorno=" . $prec->thisDay(). "\" title=\"Settimana precedente\">&nbsp;Precedente&nbsp;</a></span>";
	$m .= "&nbsp;<span class=\"pulsante nostampa\"><a href=\"index.php?option=com_agenda&anno=" . $succ->thisYear() . "&mese=" . $succ->thisMonth() . "&giorno=" . $succ->thisDay(). "\" title=\"Settimana successiva\">&nbsp;Successiva&nbsp;</a></span>";
	return $m;
}

function navigaMensile() {
	$evento = new Evento();
	$settimana = $evento->settimana;
	$mesi = $evento->mesi;
		
	$naviga = getDatiNavigazione();
	
	$M = new Calendar_Day($naviga['anno'], $naviga['mese'], 1);
	$N = new Calendar_Day($naviga['anno'], $naviga['mese']+1, 1); $N->adjust();
	$P = new Calendar_Day($naviga['anno'], $naviga['mese']-1, 1); $P->adjust();
	
	$m = '';
	$m .= '<span class="navigazioneMensile">';
	$m .= '<form method="GET" action="index.php?option=com_agenda>';
	
	$m .= '<a href="index.php?option=com_agenda&mese='.$P->thisMonth().'&anno='.$P->thisYear().'"><img src="components/com_agenda/img/Prev.gif" alt="Mese precedente" title="Mese precedente"/></a>&nbsp;';
	$m .= '<select name="mese">';
	foreach ($mesi as $n=>$meseSel) {
		$m .= '<option value="'.$n.'"'.($n==$naviga['mese']? ' selected="selected"': '').'>'.$meseSel['nome'].'</option>';
	}
	$m .= '</select>&nbsp;';
	$m .= '<select name="anno">';
	for($i = date('Y')-1; $i <= date('Y')+1; $i++) {
		$m .= '<option value="'.$i.'"'.($i==$naviga['anno']? ' selected="selected"' : '').'>'.$i.'</option>';
	}
	$m .= '</select>&nbsp;';
	$m .= '<input type="submit"  name="selAnnoScol" value="Apri" class="pulsante"/>&nbsp;';
	$m .= '<a href="index.php?option=com_agenda&mese='.$N->thisMonth().'&anno='.$N->thisYear().'"><img src="components/com_agenda/img/Next.gif" alt="Mese successivo" title="Mese successivo"/></a>&nbsp;';
	$m .= '</form>';
	$m .= '</span>';
	return $m;
}

function navigaQuadrimestre() {
	$evento = new Evento();
	$settimana = $evento->settimana;
	$mesi = $evento->mesi;
		$naviga = getDatiNavigazione();
	$m = "";

	if ($naviga['mese']>8 || $naviga['mese']==1) {
		$quad = 1;
		$msucc = 2;
		$mprec = 2;
		$asucc = $naviga['mese']==1 ? $naviga['anno'] : $naviga['anno']+1;
		$aprec = $naviga['mese']==1 ? $naviga['anno']-1 : $naviga['anno'];
	} else {
		$quad = 2;
		$msucc = 9;
		$mprec = 9;
		$asucc = $naviga['anno'];
		$aprec = $naviga['anno']-1;
	}
	
	if ($naviga['mese']==1) $ap = $naviga['anno']-1;

	$m .= '<a href="index.php?option=com_agenda&mese='.$mprec.'&anno='.$aprec.'" class="nostampa"><img src="components/com_agenda/img/Prev.gif" alt="Quadrimestre precedente" title="Quadrimestre precedente"/></a>&nbsp;';
	$m .= '<a href="index.php?option=com_agenda&mese='.$msucc.'&anno='.$asucc.'" class="nostampa"><img src="components/com_agenda/img/Next.gif" alt="Quadrimestre successivo" title="Quadrimestre successivo"/></a>&nbsp;';
	
	return $m;
}

function navigaCategorie() {
	$db = & JFactory::getDbo();
	$naviga = getDatiNavigazione();
	$m = '';
	$m .= '<span class="navigazioneCategorie">';
	$m .= '<form method="GET" action="index.php?option=com_agenda">';
	$m.= "<select name=\"codc\" class=\"combobox\">\n";
	$m.= "<option value=\"\">Tutte le categorie</option>\n";
	$sql = "select * from eventi_categorie";
	$db->setQuery($sql);
	if ($tab=$db->loadAssocList() && !empty($tab)) {
		foreach ($tab as $dettaglio){
			$m.= "<option value=\"{$dettaglio['codc']}\" style=\"background-color: {$dettaglio['colore']}\"";
			if ($naviga['codc']==$dettaglio['codc']) $m.= " selected";
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
	$naviga = getDatiNavigazione();
	$m = '';
	$m .= '<span class="navigazioneClassi">';
	$m .= '<form method="GET" action="index.php?option=com_agenda">';
	$m.= "<select name=\"classe\" class=\"combobox\">\n";
	$m.= "<option value=\"\">Tutte le classi</option>\n";
	$sql = "select * from classi order by indirizzo, classe";
	$db->setQuery($sql);
	if ($tab=$db->loadAssocList() && !empty($tab)) {
		foreach ($tab as $dettaglio){
			$m.= "<option value=\"{$dettaglio['classe']}\"";
			if ($naviga['classe'] == $dettaglio['classe']) $m.= " selected";
			$m.= ">{$dettaglio['classe']}</option>\n";
		}
	}
	$m .= '</select>&nbsp;';
	$m .= '<input type="submit"  name="sel" value="Apri" class="pulsante"/>&nbsp;';
	$m .= '</form>';
	$m .= '</span>';
	return $m;
}

function pulsantiPeriodo() {
	$m = '';
	$m .= '<br/><span class="pulsantiPeriodo nostampa">';
	$m .= "&nbsp;<a href=\"index.php?option=com_agenda&periodo=S\" title=\"Vista Settimanale\"><img src=\"components/com_agenda/img/icon-week.gif\" alt=\"Vista settimanale\" style=\"vertical-align: top\"/></a>";
	$m .= "&nbsp;<a href=\"index.php?option=com_agenda&periodo=M\" title=\"Vista Mensile\"><img src=\"components/com_agenda/img/icon-month.gif\" alt=\"Vista mensile\" style=\"vertical-align: top\"/></a>";
	$m .= "&nbsp;<a href=\"index.php?option=com_agenda&periodo=Q\" title=\"Vista Quadrimestrale\"><img src=\"components/com_agenda/img/icon-year.gif\" alt=\"Vista quadrimestrale\" style=\"vertical-align: top\"/></a>";
	$m .= '&nbsp;</span><br/>';
	return $m;
}

function linkNuovoEvento($giorno, $mese, $anno){
	if (isOperatore()) {
		$e  = "<span class=\"nostampa\"><a href=\"evento.php?mese={$mese}&anno={$anno}&giorno={$giorno}\" >";
		$e .= '<img src="components/com_agenda/img/icon-add.gif" alt="Nuovo evento" title="Nuovo evento"/>';
		$e .= '</a></span>';
		return $e;
	} else {
		return '';
	}
}

function isOperatore(){
	return true;//(validUser('personale'));
}

function esegui($tabella, $valori, $modo="insert", $where='') {
	global $db, $azienda;
	$res = $db->autoExecute($tabella, $valori, $modo=='insert' ? DB_AUTOQUERY_INSERT : DB_AUTOQUERY_UPDATE, $where);

	if (PEAR::isError($res)) {
		return null;
	} else {
		return $res;
	}
}

?>
