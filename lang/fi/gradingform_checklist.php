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

$string['addgroup'] = 'Lisää ryhmä';
$string['alwaysshowdefinition'] = 'Salli käyttäjien esikatsella moduulissa käytettyä tarkistuslistaa (muuten tarkistuslista tulee nähtäväksi vasta arvioinnin jälkeen)';
$string['backtoediting'] = 'Takaisin muokkaamaan';
$string['checked'] = 'Tarkistettu';
$string['checkitem'] = 'Merkitse täydet pisteet kohteelle {$a}';
$string['checklist'] = 'Tarkistuslista';
$string['checklistmapping'] = 'Pisteiden arvosanoihin yhdistämisen säännöt';
$string['checklistmappingexplained'] = 'Tämän tarkistuslistan pienin mahdollinen pistemäärä on <b>{$a->minscore} pistettä</b> ja se muunnetaan tämän moduulin alimmaksi mahdolliseksi arvosanaksi (joka on nolla, ellei asteikko ole käytössä).
    Suurin mahdollinen pistemäärä <b>{$a->maxscore} pistettä</b> muunnetaan korkeimmaksi arvosanaksi.<br />
    Muut pistemäärät muunnetaan ja pyöristetään lähimpään vastaavaan arvosanaan.<br />
    Jos arvosanan sijaan käytetään asteikkoa, pisteet muunnetaan skaalan elementteihin aivan kuin peräkkäisinä kokonaislukuina.';
$string['checklistoptions'] = 'Tarkistuslistan asetukset';
$string['checkliststatus'] = 'Tarkistuslistan tilanne';
$string['confirmdeletegroup'] = 'Haluatko varmasti poistaa tämän ryhmän?';
$string['confirmdeleteitem'] = 'Haluatko varmasti poistaa tämän?';
$string['definechecklist'] = 'Määritä tarkistuslista';
$string['description'] = 'Kuvaus';
$string['err_definitionmax'] = 'Kohteiden määritelmissä voi olla enintään 255 merkkiä';
$string['err_descriptionmax'] = 'Ryhmän kuvauksessa voi olla enintään 255 merkkiä';
$string['err_nodefinition'] = 'Kohteen määritelmä ei voi olla tyhjä';
$string['err_nodescription'] = 'Ryhmän kuvaus ei voi olla tyhjä';
$string['err_nogroups'] = 'Tarkistuslistassa on oltava vähintään yksi ryhmä';
$string['err_scoreformat'] = 'Kunkin kohteen pistemäärän on oltava kelvollinen ei-negatiivinen luku';
$string['err_scoremax'] = 'Kunkin kohteen pistemäärä ei voi olla suurempi kuin 1000';
$string['err_totalscore'] = 'Tarkistuslistaa käyttävän arvioinnin enimmäispistemäärän on oltava suurempi kuin nolla';
$string['groupfeedback'] = 'Ryhmäpalaute: {$a}';
$string['gradingof'] = '{$a}: arviointi';
$string['groupadditem'] = 'Lisää kohde';
$string['groupdelete'] = 'Poista ryhmä';
$string['groupdescription'] = 'Ryhmän kuvaus';
$string['groupempty'] = 'Muokkaa ryhmää napsauttamalla';
$string['groupmovedown'] = 'Siirrä alas';
$string['groupmoveup'] = 'Siirrä ylös';
$string['grouppoints'] = 'Ryhmän pisteet';
$string['groupremark'] = 'Ryhmän huomautus: {$a}';
$string['itemdefinition'] = 'Kohteen määritelmä';
$string['itemdelete'] = 'Poista kohde';
$string['itemempty'] = 'Muokkaa kohdetta napsauttamalla';
$string['itemfeedback'] = 'Palaute: {$a}';
$string['itemremark'] = 'Kohteen huomautus: {$a}';
$string['itemscore'] = 'Kohteen pisteet';
$string['name'] = 'Nimi';
$string['needregrademessage'] = 'Tarkistuslistan määritelmää on muokattu tämän opiskelijan arvioinnin jälkeen. Ko. opiskelija ei voi nähdä tarkistuslistaa, ennen kuin tarkistat tarkistuslistan ja päivität arvosanan.';
$string['pluginname'] = 'Tarkistuslista';
$string['previewchecklist'] = 'Esikatsele tarkistuslistaa';
$string['overallpoints'] = 'Kokonaispisteet';
$string['regrademessage1'] = 'Olet tallentamassa muutoksia tarkistuslistaan, jota on jo käytetty arviointiin. Osoita, onko jo annettuja
arvosanoja tarpeen korjata. Jos valitset tämän, tarkistuslista piilotetaan opiskelijoilta, kunnes olet korjannut heidän arvosanansa.';
$string['regrademessage5'] = 'Olet tallentamassa merkittäviä muutoksia tarkistuslistaan, jota on jo käytetty arviointiin. Arviointikirjan arvoa ei muuteta, mutta tarkistuslista piilotetaan opiskelijoilta, kunnes olet korjannut heidän arvosanansa.';
$string['regradeoption0'] = 'Ei tarvitse arvioida uudelleen';
$string['regradeoption1'] = 'Arvioi uudelleen';
$string['restoredfromdraft'] = 'HUOMAUTUS: Viimeisin tämän osallistujan arviointiyritys ei tallentunut oikein, joten arvosanoista on palautettu luonnokset. Jos haluat kumota nämä muutokset, käytä alla olevaa Peruuta-painiketta.';
$string['save'] = 'Tallenna';
$string['savechecklist'] = 'Tallenna tarkistuslista valmiiksi käyttöön';
$string['savechecklistdraft'] = 'Tallenna luonnoksena';
$string['scorepostfix'] = '{$a} pistettä';
$string['showitempointseval'] = 'Näytä kunkin kohteen pisteet arvioinnin aikana';
$string['showitempointstudent'] = 'Näytä kunkin kohteen pisteet arvioitaville opiskelijoille';
$string['enableitemremarks'] = 'Arvioija voi lisätä sanallista palautetta kuhunkin tarkistuslistan kohteeseen';
$string['enablegroupremarks'] = 'Arvioija voi lisätä sanallista palautetta kuhunkin tarkistuslistan ryhmään';
$string['showremarksstudent'] = 'Näytä sanallinen palaute arvioitaville opiskelijoille';
$string['unchecked'] = 'Tarkistamaton';
