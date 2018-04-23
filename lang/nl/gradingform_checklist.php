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

$string['addgroup'] = 'Voeg toe aan groep';
$string['alwaysshowdefinition'] = 'Gebruikers toestaan om een voorbeeld van een checklist te bekijken die in de module wordt gebruikt (anders wordt de checklist alleen zichtbaar na beoordeling)';
$string['backtoediting'] = 'Terug naar bewerken';
$string['checked'] = 'Ingeschakeld';
$string['checkitem'] = 'Markeer volledige bonus voor "{$a}"';
$string['checklist'] = 'Checklist';
$string['checklistmapping'] = 'Score volgens regels voor cijferkoppelingen';
$string['checklistmappingexplained'] = 'De kleinst mogelijke score voor deze checklist is <b>{$a->minscore} punten</b> en zal worden omgezet naar het minimumcijfer dat beschikbaar is in deze module (wat nul is, tenzij de schaal wordt gebruikt).
    De maximumscore <b>{$a->maxscore} punten</b> wordt omgezet naar het maximumcijfer.<br />
    Tussenliggende scores worden omgezet en afgerond naar het dichtstbijzijnde beschikbare cijfer.<br />
     Als er een schaal wordt gebruikt in plaats van een cijfer, dan zal de score worden omgezet naar de schaalelementen alsof dit opeenvolgende gehele getallen waren.';
$string['checklistoptions'] = 'Opties checklist';
$string['checkliststatus'] = 'Huidige status checklist';
$string['confirmdeletegroup'] = 'Weet je zeker dat je deze groep wilt verwijderen?';
$string['confirmdeleteitem'] = 'Weet je zeker dat je dit item wil verwijderen?';
$string['definechecklist'] = 'Definieer checklist';
$string['description'] = 'Beschrijving';
$string['err_definitionmax'] = 'De beschrijving van het item mag niet meer dan 255 tekens bevatten';
$string['err_descriptionmax'] = 'De beschrijving van de groep mag niet meer dan 255 tekens bevatten';
$string['err_nodefinition'] = 'De definitie van het item mag niet leeg zijn';
$string['err_nodescription'] = 'De beschrijving van de groep mag niet leeg zijn';
$string['err_nogroups'] = 'Checklist moet minimaal één groep bevatten';
$string['err_scoreformat'] = 'Het aantal punten voor elk item moet een geldig positief getal zijn';
$string['err_scoremax'] = 'Aantal punten voor elk item mag niet groter zijn dan 1000';
$string['err_totalscore'] = 'Het maximale aantal mogelijke punten bij beoordeling door de checklist moet meer dan nul zijn';
$string['groupfeedback'] = 'Groepsfeedback voor "{$a}"';
$string['gradingof'] = '{$a} beoordeling';
$string['groupadditem'] = 'Voeg item toe';
$string['groupdelete'] = 'Verwijder groep';
$string['groupdescription'] = 'Groepsbeschrijving';
$string['groupempty'] = 'Klik om groep te bewerken';
$string['groupmovedown'] = 'Verplaats omlaag';
$string['groupmoveup'] = 'Verplaats omhoog';
$string['grouppoints'] = 'Groepspunten';
$string['groupremark'] = 'Groepsopmerking voor "{$a}"';
$string['itemdefinition'] = 'Itemdefinitie';
$string['itemdelete'] = 'Verwijder item';
$string['itemempty'] = 'Klik om het item te bewerken';
$string['itemfeedback'] = 'Feedback voor "{$a}"';
$string['itemremark'] = 'Itemopmerking voor "{$a}"';
$string['itemscore'] = 'Itemscore';
$string['name'] = 'Naam';
$string['needregrademessage'] = 'De checklistdefinitie is gewijzigd nadat deze student werd beoordeeld. De student kan deze checklist pas zien nadat je de checklist hebt beoordeeld en het cijfer heb aangepast.';
$string['pluginname'] = 'Checklist';
$string['previewchecklist'] = 'Voorbeeld checklist';
$string['overallpoints'] = 'Totale aantal punten';
$string['regrademessage1'] = 'Je staat op het punt om wijzigingen te bewaren in een checklist die al is gebruikt voor beoordeling. Geef aan of bestaande cijfers moeten worden herzien. Als je deze optie instelt, wordt de checklist verborgen voor studenten tot hun items opnieuw zijn beoordeeld.';
$string['regrademessage5'] = 'Je staat op het punt om belangrijke wijzigingen op te slaan in een checklist die al is gebruikt voor beoordeling. Het cijfer in de cijferlijst wordt niet gewijzigd, maar de checklist wordt verborgen voor studenten tot hun items opnieuw zijn beoordeeld.';
$string['regradeoption0'] = 'Niet markeren voor opnieuw beoordelen';
$string['regradeoption1'] = 'Markeren voor opnieuw beoordelen';
$string['restoredfromdraft'] = 'OPMERKING: de laatste beoordelingspoging voor deze persoon is niet behoorlijk bewaard en daarom zijn de voorlopige cijfers teruggezet. Als je deze wijzigingen wilt annuleren, klik dan op de knop \'Annuleer\' onderaan.';
$string['save'] = 'Bewaar';
$string['savechecklist'] = 'Bewaar checklist en maak deze gereed';
$string['savechecklistdraft'] = 'Bewaar als concept';
$string['scorepostfix'] = '{$a} punten';
$string['showitempointseval'] = 'Toon punten voor elk item tijdens evaluatie';
$string['showitempointstudent'] = 'Toon punten voor elk item aan wie beoordeeld wordt';
$string['enableitemremarks'] = 'Grader toestaan tekstopmerkingen toe te voegen voor elk item in de checklist';
$string['enablegroupremarks'] = 'Grader toestaan tekstopmerkingen toe te voegen voor elke groep in de checklist';
$string['showremarksstudent'] = 'Toon all opmerkingen aan diegenen die beoordeeld worden';
$string['unchecked'] = 'Uitgeschakeld';
