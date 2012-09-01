<?php

// No direct access

defined('_JEXEC') or die('Restricted access');

//dump($this->elencoSessioni, "sessioni");



?>

<h1>Agenda d'Istituto</h1>
<?php
$evento = & $this->getModel( 'evento' );
$show=$this->show;
$code=$this->code;
 ?>
<form action="<?= JRoute::_("index.php?option=com_agenda&task=evento") ?>" method="post">
<fieldset><legend> <?= $this->h1 ?> </legend>
<p class="input"><label>Titolo</label> 
	<input type="text" name="titolo"	value="<?=$this->evento->dettaglio['titolo']?>" size="40" <?= $this->readonly ?> />
</p>
<p class="input"><label>Data iniziale</label> 
	<input type="text" 
	       name="datainiziale" 
	       value="<?=$evento->dettaglio['datainiziale']?>" 
	       <?= $this->readonly ?> 
	       size="10"
		   class="centrato" /> 
	<?php if(!$show) {?><span style="font-size: 80%">&nbsp;gg/mm/aaaa</span><?php } ?>
</p>
<p class="input"><label>Data finale</label> 
	<input 	type="text"
			name="datafinale" 
			value="<?=$evento->dettaglio['datafinale']?>"
			size="10" 
			class="centrato"
			<?= $this->readonly ?> /> 
	<?php if(!$show) {?><span style="font-size: 80%">&nbsp;gg/mm/aaaa</span><?php } ?></p>
<p class="input"><label>Ora iniziale</label> 
	<input 	type="text"
			name="orainiziale" 
			value="<?=$evento->dettaglio['orainiziale']?>"
			size="10" 
			class="centrato"
			<?= $this->readonly ?> /> 
	<?php if(!$show) {?><span style="font-size: 80%">&nbsp;hh:mm</span><?php } ?></p>
<p class="input"><label>Ora finale</label> 
	<input 	type="text"
			name="orafinale" 
			value="<?=$evento->dettaglio['orafinale']?>" 
			size="10"
			class="centrato" 
			<?= $this->readonly ?> />
	<?php if(!$show) {?><span style="font-size: 80%">&nbsp;hh:mm</span><?php } ?>
</p>
<p class="input"><label>Note</label> 
	<textarea 	name="note" 
				rows="10"
				cols="50" 
				<?= $this->readonly ?>><?=$evento->dettaglio['note']?></textarea>
</p>
<p class="input"><label>Categoria<sup>1</sup></label> 
	<select	name="icodc" 
			id="icodc" 
			
			<?= $show ?'disabled="disabled"' : ''?>>
		<option value="G">Scegli la categoria</option>
			<?= $evento->elencoOption('#__eventi_categorie', 'codc', 'categoria', $evento->dettaglio['codc']) ?>
	</select>
</p>

<p class="input">
<?php if (!$show) { ?> 
	<input type="submit" value="Registra" name="registra" class="pulsante" /> 
	<?php if (!empty($code)) { ?>
		<input type="submit" value="Elimina" name="elimina" class="pulsante" />
	<?php } ?> 
<?php } ?> 
	<input 	type="button" 
			value="Torna al calendario"
			class="pulsante" 
			onclick="window.location='<?= JRoute::_('index.php?option=com_agenda')?>'" /> 
	<?php if (!empty($code)) { ?>
		<input type="hidden" value="<?=$code?>" name="code" /> 
	<?php } ?>
</p>
<?php
if (!empty($code)) {
	print "<p>Evento inserito da {$evento->dettaglio['utente']} il {$evento->dettaglio['datainserimento']}";
	if ($evento->dettaglio['datamodifica'] != '') {
		print " - modificato il {$evento->dettaglio['datamodifica']}";
	}
	if ($evento->isOwner()){
		print " - ip {$evento->dettaglio['ip']}";
	}
	print "</p>";
}
?></fieldset>
</form>

<p>Note:</p>
<p>
<sup>1</sup> Le categorie servono per organizzare le
attivit&agrave; in base ai vari ambiti di appartenenza: <strong><br />
Classe</strong> indica attivit&agrave; riguardanti una o pi&ugrave;
classi, ad esempio cinema, teatro, visite guidate,
incontri con esperti;<br />
<strong>Generale</strong> indica attivit&agrave; riguardanti l'intera
scuola o gruppi di studenti appartenenti a diverse classi, ad esempio
scambi con l'estero, partecipazione a gare, ecc.<br />
<strong>Docenti</strong> indica attivit&agrave; destinate principalmente
ai docenti, quindi Consigli di Classe, riunioni di dipartimenti e
commissioni, ecc.<br />
<strong>Festivit&agrave;</strong> e <strong>chiusura scuola</strong>
sono generalmente inserite dalla Segreteria e riguardano appunto
giornate festive e di chiusura della scuola nel periodo estivo o per
ponti
</p>