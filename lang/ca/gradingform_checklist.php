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
 * @copyright  Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'Afegeix un grup';
$string['alwaysshowdefinition'] = 'Permet als usuaris obtenir una visualització prèvia de la llista de comprovació utilitzada en el mòdul (en cas contrari, només es mostra després de qualificar)';
$string['backtoediting'] = 'Torna a l\'edició';
$string['checked'] = 'Marcat';
$string['checkitem'] = 'Marca crèdit complet per a "{$a}"';
$string['checklist'] = 'Llista de comprovació';
$string['checklistmapping'] = 'Puntuació per qualificar el mapatge de les regles.';
$string['checklistmappingexplained'] = 'La puntuació mínima possible per a aquesta llista de verificació és <b>{$a->minscore} punts</b> i es convertirà a la qualificació mínima disponible en aquest mòdul (que és zero tret que s\'utilitzi l\'escala). La puntuació màxima,&nbsp;<b>{$a->maxscore} punts</b>, es convertirà a la qualificació màxima.<br />Les puntuacions intermèdies es convertiran respectivament i s\'arrodoniran al grau disponible més proper.<br />Si s\'utilitza una escala en lloc d\'un grau, la puntuació es convertirà als elements d\'escala com si fossin enters consecutius.';
$string['checklistoptions'] = 'Opcions de la llista de comprovació';
$string['checkliststatus'] = 'Estat actual de la llista de comprovació';
$string['confirmdeletegroup'] = 'Segur que voleu suprimir aquest grup?';
$string['confirmdeleteitem'] = 'Confirmeu que voleu suprimir aquest element?';
$string['definechecklist'] = 'Defineix la llista de comprovació';
$string['description'] = 'Descripció';
$string['err_definitionmax'] = 'La definició de l’element no ha de superar els 255 caràcters.';
$string['err_descriptionmax'] = 'La descripció del grup no ha de superar els 255 caràcters.';
$string['err_nodefinition'] = 'La definició de l’element no pot ser buida';
$string['err_nodescription'] = 'La descripció del grup no pot ser buida';
$string['err_nogroups'] = 'La llista de comprovació ha de tenir almenys un grup';
$string['err_scoreformat'] = 'El nombre de punts per a cada element ha de ser un nombre vàlid no negatiu';
$string['err_scoremax'] = 'El nombre de punts per a cada element no ha de ser superior a 1.000';
$string['err_totalscore'] = 'El nombre de punts màxim possible quan es qualifica amb la llista de comprovació ha de ser superior a zero';
$string['groupfeedback'] = 'Retroacció de grup per a "{$a}"';
$string['gradingof'] = 'S\'està qualificant {$a}';
$string['groupadditem'] = 'Afegeix un element';
$string['groupdelete'] = 'Suprimeix el grup';
$string['groupdescription'] = 'Descripció del grup';
$string['groupempty'] = 'Feu clic per editar el grup';
$string['groupmovedown'] = 'Mou avall';
$string['groupmoveup'] = 'Mou amunt';
$string['grouppoints'] = 'Punts del grup';
$string['groupremark'] = 'Observacions del grup per a "{$a}"';
$string['itemdefinition'] = 'Definició d’element';
$string['itemdelete'] = 'Suprimeix l\'element';
$string['itemempty'] = 'Feu clic per editar l’element';
$string['itemfeedback'] = 'Retroacció per a "{$a}"';
$string['itemremark'] = 'Observacions de l’element per a "{$a}"';
$string['itemscore'] = 'Puntuació de l\'element';
$string['name'] = 'Nombre';
$string['needregrademessage'] = 'La definició de la llista de comprovació ha canviat després que l’estudiant hagi rebut la qualificació. L’estudiant no pot veure aquesta llista de comprovació fins que no la reviseu i actualitzeu la qualificació.';
$string['pluginname'] = 'Llista de comprovació';
$string['previewchecklist'] = 'Visualització prèvia de la llista de comprovació';
$string['overallpoints'] = 'Punts globals';
$string['regrademessage1'] = 'Esteu a punt de desar els canvis a la llista de comprovació que ja s’ha utilitzat per qualificar. Indiqueu si hi ha qualificacions que calgui revisar. Si marqueu aquesta opció, la llista de comprovació quedarà oculta per als estudiants fins que es revisin les qualificacions dels seus elements.';
$string['regrademessage5'] = 'Esteu a punt de desar canvis significatius en una llista de comprovació que ja s’ha utilitzat per qualificar. Si marqueu aquesta opció, no es faran canvis a la qualificació del butlletí de qualificacions, però la llista de comprovació quedarà oculta per als estudiants fins que es revisin les qualificacions dels seus elements.';
$string['regradeoption0'] = 'No marqueu per requalificar';
$string['regradeoption1'] = 'Marqueu per requalificar';
$string['restoredfromdraft'] = 'NOTA: L\'últim intent de qualificar aquesta persona no s’ha desat correctament, per la qual cosa s\'han restaurat els esborranys de les qualificacions. Si voleu cancel·lar aquests canvis, feu clic al botó \'Cancel·la\' a continuació.';
$string['save'] = 'Desa';
$string['savechecklist'] = 'Desa la llista de comprovació i fes-la efectiva';
$string['savechecklistdraft'] = 'Desa com a esborrany';
$string['scorepostfix'] = '{$a} punts';
$string['showitempointseval'] = 'Mostra els punts de cada element durant l\'avaluació';
$string['showitempointstudent'] = 'Mostra els punts de cada element als alumnes qualificats';
$string['enableitemremarks'] = 'Permet al qualificador afegir observacions en format text per a cada element de la llista de comprovació';
$string['enablegroupremarks'] = 'Permet al qualificador afegir observacions en format text per a cada grup de la llista de comprovació';
$string['showremarksstudent'] = 'Mostra totes les observacions als alumnes que s\'estan qualificant';
$string['unchecked'] = 'Desmarcat';
$string['maxlengthalert'] = 'Aquest camp d\'entrada té una longitud màxima de {$a} caràcters';
