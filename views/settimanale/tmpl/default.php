<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

//dump($this->elencoSessioni, "sessioni");



?>
<script language="javascript" type="text/javascript">

function conferma() {
	return confirm('Confermi la cancellazione?');
}

</script>

<h1>Agenda d'Istituto</h1>
<?php 
	$model = & $this->getModel( 'evento' );
	echo $model->navigaSettimanale();
	echo $model->navigaCategorie();
	echo $model->navigaClassi();
	echo $model->pulsantiPeriodo();
	if ($this->canAdd) {
		echo '&nbsp;<span class="pulsante nostampa"><a href="'.JRoute::_('index.php?option=com_agenda&task=evento',true).'" title="Aggiungi un evento">&nbsp;Nuovo evento&nbsp;</a></span>';
	}
	echo $model->tabellaSettimana();
	echo $model->navigaSettimanale();
	echo $model->navigaCategorie();
	echo $model->navigaClassi();
	echo $model->pulsantiPeriodo();
	if ($this->canAdd) {
		echo '&nbsp;<span class="pulsante nostampa"><a href="'.JRoute::_('index.php?option=com_agenda&task=evento',true).'" title="Aggiungi un evento">&nbsp;Nuovo evento&nbsp;</a></span>';
	}
?>