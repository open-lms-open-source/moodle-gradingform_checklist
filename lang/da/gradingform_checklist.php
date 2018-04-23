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

$string['addgroup'] = 'Tilføj gruppe';
$string['alwaysshowdefinition'] = 'Tillad brugere at se eksempel på tjeklisten, der bruges i modulet (ellers bliver tjeklisten kun synlig efter karaktergivning)';
$string['backtoediting'] = 'Tilbage til redigering';
$string['checked'] = 'Markeret';
$string['checkitem'] = 'Markér fuld pointtildeling for "{$a}"';
$string['checklist'] = 'Tjekliste';
$string['checklistmapping'] = 'Pointresultat for tilknytningsregler for karakter';
$string['checklistmappingexplained'] = 'Det mindst mulige pointresultat for denne tjekliste er <b>{$a->minscore} point</b>, og resultatet konverteres til den tilgængelige minimumskarakter i dette modul (hvilket er nul, medmindre skalaen bruges).
Det maksimale pointresultat på <b>{$a->maxscore} point</b> konverteres til maksimumskarakteren.<br />
Middelpointresultater konverteres henholdsvis og afrundes til den nærmeste tilgængelige karakter<br />
Hvis der anvendes en skala i stedet for en karakter, konverteres pointresultatet til skalaelementerne, som hvis de var fortløbende heltal.';
$string['checklistoptions'] = 'Indstillinger for tjekliste';
$string['checkliststatus'] = 'Aktuel tjeklistestatus';
$string['confirmdeletegroup'] = 'Er du sikker på, at du vil slette denne gruppe?';
$string['confirmdeleteitem'] = 'Er du sikker på, at du vil slette dette element?';
$string['definechecklist'] = 'Definer tjekliste';
$string['description'] = 'Beskrivelse';
$string['err_definitionmax'] = 'Elementdefinition må ikke være på mere end 255 tegn.';
$string['err_descriptionmax'] = 'Gruppebeskrivelse må ikke være på mere end 255 tegn.';
$string['err_nodefinition'] = 'Elementdefinition skal udfyldes';
$string['err_nodescription'] = 'Gruppebeskrivelse skal udfyldes';
$string['err_nogroups'] = 'Tjeklisten skal indeholde mindst en gruppe';
$string['err_scoreformat'] = 'Antal point for hvert element skal være et gyldigt positivt tal';
$string['err_scoremax'] = 'Antal point for hvert element må ikke være højere end 1000';
$string['err_totalscore'] = 'Maksimum antal mulige print, når der gives karakter af tjeklisten, skal være højere end nul';
$string['groupfeedback'] = 'Gruppefeedback for "{$a}"';
$string['gradingof'] = '{$a} karaktergivning';
$string['groupadditem'] = 'Tilføj element';
$string['groupdelete'] = 'Slet gruppe';
$string['groupdescription'] = 'Gruppebeskrivelse';
$string['groupempty'] = 'Klik for at redigere gruppe';
$string['groupmovedown'] = 'Flyt ned';
$string['groupmoveup'] = 'Flyt op';
$string['grouppoints'] = 'Gruppepoint';
$string['groupremark'] = 'Gruppebemærkning til "{$a}"';
$string['itemdefinition'] = 'Elementdefinition';
$string['itemdelete'] = 'Slet element';
$string['itemempty'] = 'Klik for at redigere element';
$string['itemfeedback'] = 'Feedback for "{$a}"';
$string['itemremark'] = 'Elementbemærkning til "{$a}"';
$string['itemscore'] = 'Elementpointresultat';
$string['name'] = 'Navn';
$string['needregrademessage'] = 'Tjeklistedefinitionen blev ændret, efter denne studerende havde fået karakter. Den studerende kan ikke se denne tjekliste, før du gennemgår den og opdaterer karakteren.';
$string['pluginname'] = 'Tjekliste';
$string['previewchecklist'] = 'Vis eksempel på tjekliste';
$string['overallpoints'] = 'Samlede point';
$string['regrademessage1'] = 'Du er ved at gemme ændringer til en tjekliste, der allerede er blevet brugt til karaktergivning. Angiv,
om eksisterende karakterer skal gennemses. Hvis du angiver dette, skjules tjeklisten for studerende, indtil deres elementer har fået ny karakter.';
$string['regrademessage5'] = 'Du er ved at gemme væsentlige ændringer til en tjekliste, der allerede er blevet brugt til karaktergivning. Karakterbogsværdien ændres ikke, men tjeklisten skjules for studerende, indtil deres elementer har fået ny karakter.';
$string['regradeoption0'] = 'Markér ikke for ny karakter';
$string['regradeoption1'] = 'Markér for ny karakter';
$string['restoredfromdraft'] = 'BEMÆRK: Det sidste forsøg på at give denne person karakter blev ikke gemt korrekt, så kladdekarakterer er blevet gendannet. Hvis du vil annullere disse ændringer, skal du bruge knappen \'Annuller\' herunder.';
$string['save'] = 'Gem';
$string['savechecklist'] = 'Gem tjekliste, og gør den klar';
$string['savechecklistdraft'] = 'Gem som kladde';
$string['scorepostfix'] = '{$a} point';
$string['showitempointseval'] = 'Vis point for hvert element under evaluering';
$string['showitempointstudent'] = 'Vis point for hvert element til dem, der får karakter';
$string['enableitemremarks'] = 'Tillad karaktergiver at tilføje tekstbemærkninger for hvert tjeklisteelement';
$string['enablegroupremarks'] = 'Tillad karaktergiver at tilføje tekstbemærkninger for hver tjeklistegruppe';
$string['showremarksstudent'] = 'Vis alle bemærkninger til dem, der får karakter';
$string['unchecked'] = 'Ikke markeret';
