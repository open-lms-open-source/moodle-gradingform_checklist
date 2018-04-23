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

$string['addgroup'] = 'Gruppe hinzufügen';
$string['alwaysshowdefinition'] = 'Nutzern eine Vorschau der in dem Modul verwendeten Checkliste gestatten (andernfalls ist die Checkliste erst nach der Bewertung sichtbar).';
$string['backtoediting'] = 'Zurück zum Bearbeiten';
$string['checked'] = 'Geprüft';
$string['checkitem'] = 'Volle Punktzahl vergeben für "{$a}"';
$string['checklist'] = 'Checkliste';
$string['checklistmapping'] = 'Regeln der Zuordnung von Punkten zu Bewertungen';
$string['checklistmappingexplained'] = 'Die Mindestpunktzahl für diese Checkliste beträgt <b>{$a->minscore} Punkte</b>, die für die in diesem Modul verfügbare Mindestbewertung konvertiert werden (sofern nicht die Skala verwendet wird, ist diese Mindestbewertung 0).
Die Höchstpunktzahl, <b>{$a->maxscore} Punkte</b> wird in die Höchstbewertung konvertiert.<br />
Mittlere Punktzahlen werden entsprechend konvertiert und auf die nächste verfügbare Bewertung gerundet.<br />
Wenn anstelle einer Bewertung eine Skala verwendet wird, dann werden die Punkte in Skalenelemente konvertiert, als seien sie aufeinanderfolgende ganze Zahlen.';
$string['checklistoptions'] = 'Checklistenoptionen';
$string['checkliststatus'] = 'Aktueller Checklistenstatus';
$string['confirmdeletegroup'] = 'Möchten Sie diese Gruppe wirklich löschen?';
$string['confirmdeleteitem'] = 'Möchten Sie dieses Element wirklich löschen?';
$string['definechecklist'] = 'Checkliste definieren';
$string['description'] = 'Beschreibung';
$string['err_definitionmax'] = 'Die Elementdefinition darf nicht länger als 255 Zeichen sein.';
$string['err_descriptionmax'] = 'Die Gruppenbeschreibung darf nicht länger als 255 Zeichen sein.';
$string['err_nodefinition'] = 'Die Elementdefinition darf nicht leer sein.';
$string['err_nodescription'] = 'Die Gruppenbeschreibung darf nicht leer sein.';
$string['err_nogroups'] = 'Die Checkliste muss mindestens 1 Gruppe enthalten.';
$string['err_scoreformat'] = 'Die Anzahl der Punkte für jedes Element muss eine gültige, nicht negative Zahl sein.';
$string['err_scoremax'] = 'Die Anzahl der Punkte für jedes Element darf nicht größer als 1000 sein.';
$string['err_totalscore'] = 'Die höchste zu vergebende Punktzahl, wenn nach der Checkliste bewertet wird, muss größer als 0 sein.';
$string['groupfeedback'] = 'Gruppen-Feedback für "{$a}"';
$string['gradingof'] = '{$a} Bewertung';
$string['groupadditem'] = 'Objekt hinzufügen';
$string['groupdelete'] = 'Gruppe löschen';
$string['groupdescription'] = 'Gruppenbeschreibung';
$string['groupempty'] = 'Klicken, um die Gruppe zu bearbeiten';
$string['groupmovedown'] = 'Nach unten verschieben';
$string['groupmoveup'] = 'Nach oben verschieben';
$string['grouppoints'] = 'Gruppenpunkte';
$string['groupremark'] = 'Gruppenanmerkung für "{$a}"';
$string['itemdefinition'] = 'Elementdefinition';
$string['itemdelete'] = 'Objekt löschen';
$string['itemempty'] = 'Klicken, um das Element zu bearbeiten';
$string['itemfeedback'] = 'Feedback für "{$a}"';
$string['itemremark'] = 'Elementanmerkung für "{$a}"';
$string['itemscore'] = 'Elementpunktzahl';
$string['name'] = 'Name';
$string['needregrademessage'] = 'Die Checklistendefinition wurde geändert, nachdem dieser Teilnehmer bewertet wurde. Der Teilnehmer kann diese Checkliste erst wieder sehen, nachdem Sie die Checkliste überprüft und die Bewertung aktualisiert haben.';
$string['pluginname'] = 'Checkliste';
$string['previewchecklist'] = 'Vorschau der Checkliste';
$string['overallpoints'] = 'Gesamtpunktzahl';
$string['regrademessage1'] = 'Sie sind dabei, Änderungen an einer Checkliste zu speichern, die bereits zum Bewerten verwendet wurde. Geben Sie bitte an,
ob die vorhandenen Bewertungen überprüft werden müssen. Wenn Sie dieses festlegen, dann wird die Checkliste den Teilnehmern erst wieder angezeigt, nachdem ihre Elemente neu bewertet wurden.';
$string['regrademessage5'] = 'Sie sind dabei, wesentliche Änderungen an einer Checkliste zu speichern, die bereits zum Bewerten verwendet wurde. Der Bewertungsberichtwert bleibt unverändert, jedoch wird die Checkliste den Teilnehmern erst wieder angezeigt, nachdem ihre Elemente neu bewertet wurden.';
$string['regradeoption0'] = 'Nicht für Neubewertung markieren';
$string['regradeoption1'] = 'Für Neubewertung markieren';
$string['restoredfromdraft'] = 'HINWEIS: Der letzte Versuch zur Bewertung dieses Nutzers wurde nicht richtig gespeichert und nur als Entwurf hinterlegt. Mit der unteren Schaltfläche "Abbrechen" können Sie diese Änderungen bearbeiten.';
$string['save'] = 'Speichern';
$string['savechecklist'] = 'Checkliste speichern und als fertig markieren';
$string['savechecklistdraft'] = 'Als Entwurf speichern';
$string['scorepostfix'] = '{$a} Punkte';
$string['showitempointseval'] = 'Punkte für jedes Element während der Beurteilung anzeigen.';
$string['showitempointstudent'] = 'Punkte für jedes Element denjenigen anzeigen, die gerade bewertet werden.';
$string['enableitemremarks'] = 'Bewerter/in gestatten, Textanmerkungen bei jedem Checklistenelement hinzuzufügen.';
$string['enablegroupremarks'] = 'Bewerter/in gestatten, Textanmerkungen bei jeder Checklistengruppe hinzuzufügen.';
$string['showremarksstudent'] = 'Alle Anmerkungen denjenigen anzeigen, die gerade bewertet werden.';
$string['unchecked'] = 'Nicht geprüft';
