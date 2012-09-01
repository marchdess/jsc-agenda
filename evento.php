<?php 
require_once("../common.php"); 
require_once("lib/comuni.php");

checkLogin();
if (!validUser('personale')) {
 die(errore_input("Non sei autorizzato"));
}


$evento = new Evento();
$h1 = "Nuovo Evento";

if (isset($_REQUEST['code'])) {
	if ($evento->searchCode($_REQUEST['code'])) {
		if (!isset($_GET['show']) && !$evento->isOwner()) {
			header("location: index.php");
		}
		if (!isset($_GET['show'])) {
			$h1 = "Modifica evento";
		} else {
			$h1 = "Mostra evento";
		}
	}
}
if (isset($_POST['registra'])) {
	$err = false;
	$_POST['orainiziale'] = ereg_replace("\.", ":", $_POST['orainiziale']);
	$_POST['orafinale'] = ereg_replace("\.", ":", $_POST['orafinale']);
	if ($_POST['titolo']=='' || $_POST['datainiziale']=='' || $_POST['datafinale']=='') {
		$err = true;
	}
	$evento->dettaglio= array(
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
		$evento->dettaglio['code']=$_REQUEST['code'];
		$evento->dettaglio['datamodifica'] = date('d/m/Y');
	} else {
		$evento->dettaglio['datainserimento'] = date('d/m/Y');
		$evento->dettaglio['utente'] = $_SESSION['logusername'];
	}
	
	if ($_POST['icodc']=='C') {
		$classi=array();
		foreach ($_POST['classi'] as $c=>$v) {
			$classi[] = $c;
		}
		$evento->classi = $classi;
	}
	if (!$err) {
		$evento->registra();
	}
	header("Location: index.php");
}

if (isset($_POST['elimina']) && $evento->isOwner() && $evento->elimina()) {
	header("Location: index.php");	
}


?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="it"><!-- InstanceBegin template="../Templates/modelloWIDE.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta name="description" content="ISIT Istituto Statale di Istruzione Tecnica Ugo Bassi Pietro Burgatti. Indirizzi industriali: meccanica, elettronica e elettrotecnica. Indirizzi commerciali: amministrativo IGEA e programmatori Mercurio. Liceo scientifico-tecnologico." />
<meta name="keywords" content="ISIT Cento I.S.I.T. Istituto Statale di Istruzione Tecnica ITIS I.T.I.S. Tecnico Industriale Statale ITC I.T.C. Burgatti Commerciale scuole scuola superiori italiane programmatori programmatore ragioniere elettronico meccanico elettrotecnico liceo scientifico tecnologico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../stileWIDE.css.php" rel="stylesheet" type="text/css" />
<link href="../stampa.css" rel="stylesheet" type="text/css" media="print" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Intranet ISIT -
<?= $h1 ?>
</title>
<!-- InstanceEndEditable --><!-- InstanceBeginEditable name="head" -->
<link href="calendario.css" rel="stylesheet" type="text/css" />
<!-- InstanceEndEditable --><!-- InstanceParam name="accessibile" type="boolean" value="true" --><!-- InstanceParam name="seHTML" type="boolean" value="true" -->
</head>
<body>
<div id="contenitore">
  <div id="pagina">
    <div id="intestazione">
      <div id="info"> <a href="http://www.isit100.fe.it/" title="Home page"><img src="../img/logo.gif" alt="Logo ISIT" width="192" height="99" id="logoisit" /></a>
        <h1>Istituto Statale di Istruzione Tecnica</h1>
        <h2>Ugo Bassi - Pietro Burgatti</h2>
        <address>
        <span class="p3">via Rigone 1 - 44042 Cento FE</span> <span class="p4">Tel 051 6859711 - Fax 051 904015 - email <a href="mailto:isit@isit100.fe.it" title="Mail ISIT">isit@isit100.fe.it</a></span> <span class="p5">&nbsp;</span>
        </address>
      </div>
      <object data="../img/ruota.swf" type="application/x-shockwave-flash"  id="ruota">
        <param name="movie" value="../img/ruota.swf" />
        <param name="menu" value="false" />
        <div id="altruota">Ruota Flash</div>
      </object>
&nbsp; </div>
    <div id="login">
      <?php if (isset($_SESSION[lognome])) { ?>
      Sei connesso come
      <?= $_SESSION[lognome] ?>
      <?php } ?>
    </div>
    <div id="fastmenu"><span id="tree"><a href="http://www.isit100.fe.it">Home</a> &gt; <a href="/" title="Home" accesskey="h">Intranet</a> &gt; <!-- InstanceBeginEditable name="tree" --><a href="index.php">Agenda</a> &gt;
      <?= $h1 ?>
      <!-- InstanceEndEditable --></span>
      <ul>
        <li class="p1"><a href="http://www.isit100.fe.it/info/" title="Contatti" accesskey="i">[i]Contatti</a></li>
        <li class="p2"><a href="http://www.isit100.fe.it/cerca/" title="Cerca" accesskey="c">[c]Cerca</a></li>
        <li class="p3"><a href="http://www.isit100.fe.it/link/" title="Link utili" accesskey="l">[l]Link Utili</a></li>
        <li class="p4"><a href="http://www.isit100.fe.it/posta" title="Posta web" accesskey="p">[p]Posta Web</a></li>
        <li class="p5">
          <?php if(isset($_SESSION[lognome])) { ?>
          <a href="/logout.php" title="Scollegati" accesskey="o">[o]Logout</a>
          <?php } else { ?>
          <a href="/login.php" title="Autenticati" accesskey="a">[a]Login</a>
          <?php } ?>
        </li>
      </ul>
    </div>
    <!-- ********** Titolo della pagina ************* -->
    <h1><!-- InstanceBeginEditable name="Titolo" -->
      <?= $h1 ?>
      <!-- InstanceEndEditable --></h1>
    <!-- ********** Corpo della pagina ************* -->
    <div id="corpo"> <!-- InstanceBeginEditable name="Corpo" -->
      <?php
	//print "<pre>";
	//print_r($evento);
	//print "</pre>";
	
	$naviga = getdatiNavigazione();
	$data = $naviga['giorno'] . '/' . $naviga['mese'] . '/' . $naviga['anno'];
	if (!isset($_REQUEST['code'])) {
		$evento->dettaglio['datainiziale'] = $data;
		$evento->dettaglio['datafinale'] = $data;
	}
	?>
      <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset>
        <legend>
        <?= $h1 ?>
        </legend>
        <p class="input">
          <label>Titolo</label>
          <input type="text" name="titolo" value="<?=$evento->dettaglio['titolo']?>" size="40" <?= isset($_GET['show'])?'readonly="readonly"' : ''?>/>
        </p>
        <p class="input">
          <label>Data iniziale</label>
          <input type="text" name="datainiziale" value="<?=$evento->dettaglio['datainiziale']?>" <?= isset($_GET['show'])?'readonly="readonly"' : ''?> size="10" class="centrato"/>
		  <?php if(!isset($_GET[show])) {?><span style="font-size:80%">&nbsp;gg/mm/aaaa</span><?php } ?>
       </p>
        <p class="input">
          <label>Data finale</label>
          <input type="text" name="datafinale" value="<?=$evento->dettaglio['datafinale']?>"  size="10" class="centrato" <?= isset($_GET['show'])?'readonly="readonly"' : ''?>/>
		  <?php if(!isset($_GET[show])) {?><span style="font-size:80%">&nbsp;gg/mm/aaaa</span><?php } ?>
        </p>
        <p class="input">
          <label>Ora iniziale</label>
          <input type="text" name="orainiziale" value="<?=$evento->dettaglio['orainiziale']?>"  size="10"  class="centrato" <?= isset($_GET['show'])?'readonly="readonly"' : ''?>/>
		  <?php if(!isset($_GET[show])) {?><span style="font-size:80%">&nbsp;hh:mm</span><?php } ?>
        </p>
        <p class="input">
          <label>Ora finale</label>
          <input type="text" name="orafinale" value="<?=$evento->dettaglio['orafinale']?>"  size="10"  class="centrato" <?= isset($_GET['show'])?'readonly="readonly"' : ''?>/>
		  <?php if(!isset($_GET[show])) {?><span style="font-size:80%">&nbsp;hh:mm</span><?php } ?>
        </p>
        <p class="input">
          <label>Note</label>
          <textarea name="note" rows="10" cols="50" <?= isset($_GET['show'])?'readonly="readonly"' : ''?>><?=$evento->dettaglio['note']?>
</textarea>
        </p>
        <p class="input">
          <label>Categoria<sup>1</sup></label>
          <select name="icodc" id="icodc" onchange="check()" <?= isset($_GET['show'])?'disabled="disabled"' : ''?>>
            <option value="G">Scegli la categoria</option>
            <?= elencoOption('eventi_categorie', 'codc', 'categoria', $evento->dettaglio['codc']) ?>
          </select>
        </p>
        <p class="input" id="classi">
		  <!--
          <label id="lclassi" <?= $evento->dettaglio['codc']!='C'? 'style="display:none"' : '' ?> >Classi coinvolte</label>
          <select name="classi[]" multiple="multiple" id="iclassi" size="10" <?= $evento->dettaglio['codc']!='C'? 'style="display:none"' : '' ?> <?= isset($_GET['show']) ? 'disabled="disabled"' : ''?>>
 			<?php
				if ($tab=getTabella('vclassi')) {
					while ($riga = getRiga($tab)) {
						echo '<option value="' . $riga['classe'] . '"';
						foreach ($evento->classi as $classe) {
							if ($classe == $riga['classe']) {
								echo ' selected';
							}
						}
						echo '>' . $riga['descrizione'] . '</option>';
					}
				}
			?>
          </select>
		  -->
	    <table width="100%" id="iclassi" <?= $evento->dettaglio['codc']!='C'? 'style="display:none"' : '' ?>>
		  <caption>Classi coinvolte</caption>
		  <?php
		  if ($tab=getTabella('classi', 'order by indirizzo, classe')) {
		  	$temp='';
			while ($riga=getRiga($tab)) {
				if ($temp!=$riga['indirizzo']) {
					if ($temp!=''){
						print "</td></tr>";
					}
					print "<tr><th>{$riga['indirizzo']}</th><td>";
					$temp = $riga['indirizzo'];
				}
				print "<span style=\"margin: 0 3px 0 3px; font-family: monospace; font-size: 120%; white-space:nowrap\">";
				print $riga['classe'] . '<input type="checkbox" name="classi['.$riga['classe'].']" ' . (isset($_GET['show']) ? 'disabled="disabled"' : '');
				foreach ($evento->classi as $classe) {
					if ($classe == $riga['classe']) {
						echo ' checked="checked" ';
					}
				}
				print '/></span>';
			}
			print "</td></tr>";
		  }
		  ?>
	    </table>
        </p>
        <p class="input">
		  <?php if (!isset($_GET['show'])) { ?>
          <input type="submit" value="Registra" name="registra" class="pulsante" />
		  	<?php if (isset($_REQUEST['code'])) { ?>
          <input type="submit" value="Elimina" name="elimina" class="pulsante" />
		    <?php } ?>
		  <?php } ?>
          <input type="button" value="Torna al calendario" class="pulsante" onclick="window.location='index.php'" />
          <?php if (isset($_REQUEST['code'])) { ?>
          <input type="hidden" value="<?=$_REQUEST['code']?>" name="code" />
          <?php } ?>
        </p>
		  <?php
		  if (isset($_REQUEST['code'])) {
		  	print "<p>Evento inserito da {$evento->dettaglio['utente']} il {$evento->dettaglio['datainserimento']}";
			if ($evento->dettaglio['datamodifica'] != '') {
				print " - modificato il {$evento->dettaglio['datamodifica']}";
			}
			if ($evento->isOwner()){
				print " - ip {$evento->dettaglio['ip']}";
			}
			print "</p>";
		  }
		  ?>
        </fieldset>
      </form>
      <script type="text/javascript">
	function check() {
		var codc = document.getElementById('icodc');
		var classi = document.getElementById('iclassi');
		var lclassi = document.getElementById('lclassi');
		if (codc.selectedIndex==3) {
			classi.style.visibility='visible';
			classi.style.display='block';
			//lclassi.style.display='block';
		} else {
			classi.style.visibility='hidden';		
		}
		return true;
	}
	</script>
	
	<p>
	Note:
	</p>
	<p>
	<blockquote><sup>1</sup> Le categorie servono per organizzare le attivit&agrave; in base ai vari ambiti di appartenenza: <strong><br />
	  Classe</strong> indica attivit&agrave; riguardanti una o pi&ugrave; classi (da selezionare), ad esempio cinema, teatro, visite guidate, incontri con esperti;<br />
	  <strong>Generale</strong> indica attivit&agrave; riguardanti l'intera scuola o gruppi di studenti appartenenti a diverse classi, ad esempio scambi con l'estero, partecipazione a gare, ecc.<br />
	  <strong>Docenti</strong> indica attivit&agrave; destinate principalmente ai docenti, quindi Consigli di Classe, riunioni di dipartimenti e commissioni, ecc.<br />
	  <strong>Festivit&agrave;</strong> e <strong>chiusura scuola</strong> sono generalmente inserite dalla Segreteria e riguardano appunto giornate festive e di chiusura della scuola nel periodo estivo o per ponti </blockquote>
	</p>
      <!-- InstanceEndEditable --> </div>
    <div id="void">&nbsp;</div>
  </div>
  <!-- Fine Pagina -->
</div>
<div id="servizio">
  <p>Modificato il
    <!-- #BeginDate format:Fr1 -->13/01/09<!-- #EndDate -->
  </p>
  <p> <span><a href="http://validator.w3.org/check/referer" title="Valid XHTML"><img src="../img/valid.gif" alt="Valido XHTML" width="88" height="31" /></a></span> <span id="css"><a href="http://jigsaw.w3.org/css-validator/validator?uri=http://prove.isit100.fe.it<?=urlencode($_SERVER['PHP_SELF'])?>"><img src="../img/vcss.gif" alt="Valido CSS" width="88" height="31" /></a></span> <span id="accessibile"><a href="http://www.w3.org/WAI/WCAG1AA-Conformance"
      title="Explanation of Level Double-A Conformance"> <img height="31" width="88" 
          src="../img/wcag1AA.gif"
          alt="Level Double-A conformance icon, 
          W3C-WAI Web Content Accessibility Guidelines 1.0" /></a></span> </p>
</div>
</body>
<!-- InstanceEnd --></html>
