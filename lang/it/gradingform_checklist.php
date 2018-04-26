<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @copyright  Copyright (c) 2018 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'Aggiungi gruppo';
$string['alwaysshowdefinition'] = 'Consenti agli utenti di visualizzare in anteprima l\'elenco di controllo utilizzato nel modulo (altrimenti l\'elenco di controllo sarà visibile solo dopo la valutazione)';
$string['backtoediting'] = 'Torna alla modifica';
$string['checked'] = 'Selezionato';
$string['checkitem'] = 'Contrassegna tutto il credito per "{$a}"';
$string['checklist'] = 'Elenco di controllo';
$string['checklistmapping'] = 'Punteggio per valutare regole di mapping';
$string['checklistmappingexplained'] = 'Il punteggio minimo di questo elenco di controllo è <b>{$a->minscore} punti</b> e sarà convertito nel voto minimo disponibile per questo modulo (pari a zero a meno che non venga utilizzata una scala).
Il punteggio massimo <b>{$a->maxscore} punti</b> sarà convertito nel voto massimo.<br />I punteggi intermedi saranno rispettivamente convertiti e arrotondati al voto disponibile più vicino.<br />
Se anziché il voto viene utilizzata una scala, il punteggio sarà convertito negli elementi della scala corrispondenti come se si trattasse di valori interi consecutivi.';
$string['checklistoptions'] = 'Opzioni elenco di controllo';
$string['checkliststatus'] = 'Stato corrente elenco di controllo';
$string['confirmdeletegroup'] = 'Eiminare questo gruppo?';
$string['confirmdeleteitem'] = 'Eliminare questo elemento?';
$string['definechecklist'] = 'Definisci elenco di controllo';
$string['description'] = 'Descrizione';
$string['err_definitionmax'] = 'La definizione dell\'elemento non può contenere più di 255 caratteri';
$string['err_descriptionmax'] = 'La descrizione del gruppo non può contenere più di 255 caratteri';
$string['err_nodefinition'] = 'Il campo Definizione elemento non può essere vuoto';
$string['err_nodescription'] = 'Il campo Descrizione gruppo non può rimanere vuoto';
$string['err_nogroups'] = 'L\'elenco di controllo deve contenere almeno un gruppo';
$string['err_scoreformat'] = 'Il numero di punti per ciascun elemento deve essere un numero valido non negativo';
$string['err_scoremax'] = 'Il numero di punti per ciascun elemento non deve essere maggiore di 1000';
$string['err_totalscore'] = 'Il punteggio massimo possibile di una valutazione deve essere maggiore di zero';
$string['groupfeedback'] = 'Feedback gruppo per "{$a}"';
$string['gradingof'] = 'Valutazione di {$a}';
$string['groupadditem'] = 'Aggiungi elemento';
$string['groupdelete'] = 'Elimina gruppo';
$string['groupdescription'] = 'Decrizione gruppo';
$string['groupempty'] = 'Fai clic per modificare il gruppo';
$string['groupmovedown'] = 'Sposta in basso';
$string['groupmoveup'] = 'Sposta in alto';
$string['grouppoints'] = 'Punti gruppo';
$string['groupremark'] = 'Nota gruppo per "{$a}"';
$string['itemdefinition'] = 'Definizione elemento';
$string['itemdelete'] = 'Elimina elemento';
$string['itemempty'] = 'Fai clic per modificare l\'elemento';
$string['itemfeedback'] = 'Feedback per "{$a}"';
$string['itemremark'] = 'Nota elemento per "{$a}"';
$string['itemscore'] = 'Punteggio elemento';
$string['name'] = 'Nome';
$string['needregrademessage'] = 'La definizione dell\'elenco di controllo è stata modificata dopo la valutazione di questo studente. Lo studente non può visualizzare questo elenco di controllo finché non esaminerai l\'elenco di controllo e aggiornerai il voto.';
$string['pluginname'] = 'Elenco di controllo';
$string['previewchecklist'] = 'Anteprima elenco di controllo';
$string['overallpoints'] = 'Punti totali';
$string['regrademessage1'] = 'Stai per salvare le modifiche apportate su un elenco di controllo già utilizzato per la valutazione. Indica i voti esistenti da rivedere. Con questa impostazione, l\'elenco di controllo non sarà visualizzabile dagli studenti finché i loro elementi non verranno nuovamente valutati.';
$string['regrademessage5'] = 'Stai per salvare modifiche rilevanti apportate a un elenco di controllo che è già stato utilizzato per la valutazione. I valori presenti nel registro voti non saranno modificati, ma l\'elenco di controllo non sarà visualizzabile dagli studenti finché i loro elementi non verranno nuovamente valutati.';
$string['regradeoption0'] = 'Non contrassegnare per rivalutazione';
$string['regradeoption1'] = 'Contrassegna per rivalutazione';
$string['restoredfromdraft'] = 'NOTA: l\'ultimo tentativo di valutare questa persona non è stato salvato correttamente, pertanto sono stati ripristinati i voti in bozza. Per annullare tali modifiche, premi il pulsante "Annulla" di seguito.';
$string['save'] = 'Salva';
$string['savechecklist'] = 'Salva elenco di controllo e rendilo disponibile';
$string['savechecklistdraft'] = 'Salva come bozza';
$string['scorepostfix'] = '{$a} punti';
$string['showitempointseval'] = 'Mostra i punteggi per ciascun elemento durante la valutazione';
$string['showitempointstudent'] = 'Mostra i punteggi per ciascun elemento a coloro che saranno valutati';
$string['enableitemremarks'] = 'Consenti al valutatore di aggiungere commenti testuali a ogni elemento della lista di controllo';
$string['enablegroupremarks'] = 'Consenti al valutatore di aggiungere commenti testuali a ogni gruppo dell\'elenco di controllo';
$string['showremarksstudent'] = 'Mostra tutte le note a coloro che riceveranno la valutazione';
$string['unchecked'] = 'Deselezionato';
