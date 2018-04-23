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

$string['addgroup'] = 'Přidat skupinu';
$string['alwaysshowdefinition'] = 'Povolit uživatelům zobrazit náhled kontrolního seznamu použitého v modulu (jinak se kontrolní seznam zobrazí pouze po klasifikaci)';
$string['backtoediting'] = 'Zpět k úpravám';
$string['checked'] = 'Zaškrtnuté';
$string['checkitem'] = 'Označit úplný kredit pro "{$a}"';
$string['checklist'] = 'Kontrolní seznam';
$string['checklistmapping'] = 'Pravidla mapování skóre na klasifikaci';
$string['checklistmappingexplained'] = 'Minimální možné skóre pro tento kontrolní seznam je <b>{$a->minscore} bodů</b> a bude převedeno na minimální klasifikaci dostupnou v tomto modulu (která je nulová, není-li použita stupnice).
    Maximální skóre <b>{$a->maxscore} bodů </b> bude převedeno na maximální klasifikaci.<br />
    Střední skóre budou převedena jednotlivě a zaokrouhlena na nejbližší dostupnou klasifikaci.<br />
    Používá-li se místo klasifikace stupnice, bude skóre převedeno na prvky stupnice, které tvoří po sobě jdoucí celá čísla.';
$string['checklistoptions'] = 'Možnosti kontrolního seznamu';
$string['checkliststatus'] = 'Aktuální stav kontrolního seznamu';
$string['confirmdeletegroup'] = 'Opravdu chcete tuto skupinu odstranit?';
$string['confirmdeleteitem'] = 'Opravdu chcete tuto položku zrušit?';
$string['definechecklist'] = 'Definovat kontrolní seznam';
$string['description'] = 'Popis';
$string['err_definitionmax'] = 'Definice položky smí obsahovat maximálně 255 znaků.';
$string['err_descriptionmax'] = 'Popis skupiny smí obsahovat maximálně 255 znaků.';
$string['err_nodefinition'] = 'Definice položky nesmí být prázdná.';
$string['err_nodescription'] = 'Popis skupiny nesmí být prázdný.';
$string['err_nogroups'] = 'Kontrolní seznam musí obsahovat alespoň jednu skupinu.';
$string['err_scoreformat'] = 'Počet bodů pro každou položku musí být platné nezáporné číslo.';
$string['err_scoremax'] = 'Počet bodů pro každou položku nesmí být větší než 1000.';
$string['err_totalscore'] = 'Maximální možný počet bodů při klasifikaci musí být větší než nula.';
$string['groupfeedback'] = 'Zpětná vazba skupiny pro {$a}';
$string['gradingof'] = 'Klasifikace pro {$a}';
$string['groupadditem'] = 'Přidat položku';
$string['groupdelete'] = 'Odstranit skupinu';
$string['groupdescription'] = 'Popis skupiny';
$string['groupempty'] = 'Kliknout, chcete-li upravit skupinu';
$string['groupmovedown'] = 'Posunout dolů';
$string['groupmoveup'] = 'Posunout nahoru';
$string['grouppoints'] = 'Počet bodů skupiny';
$string['groupremark'] = 'Poznámka skupiny pro {$a}';
$string['itemdefinition'] = 'Definice položky';
$string['itemdelete'] = 'Odstranit položku';
$string['itemempty'] = 'Kliknout, chcete-li upravit položku';
$string['itemfeedback'] = 'Zpětná vazba pro položku {$a}';
$string['itemremark'] = 'Poznámka k položce pro {$a}';
$string['itemscore'] = 'Skóre položky';
$string['name'] = 'Název';
$string['needregrademessage'] = 'Definice kontrolního seznamu byla po klasifikaci studenta změněna. Student nebude moci kontrolní seznam zobrazit, dokud jej nezkontrolujete a neaktualizujete klasifikaci.';
$string['pluginname'] = 'Kontrolní seznam';
$string['previewchecklist'] = 'Náhled kontrolního seznamu';
$string['overallpoints'] = 'Celkový počet bodů';
$string['regrademessage1'] = 'Chystáte se uložit změny kontrolního seznamu, který již byla použit při klasifikaci. Rozhodněte, 
zda by existující klasifikace měly být zkontrolovány a revidovány. Pokud ano, bude kontrolní seznam studentům skrytý, dokud nebudou jejich položky znovu klasifikovány.';
$string['regrademessage5'] = 'Chystáte se uložit závažné změny kontrolního seznamu, který již byla použit při klasifikaci. Hodnota v centru klasifikace nebude změněna, ale kontrolní seznam bude pro studenty skrytý, dokud nebudou jejich položky znovu klasifikovány.';
$string['regradeoption0'] = 'Neoznačovat pro opětovnou klasifikaci';
$string['regradeoption1'] = 'Označit pro opětovnou klasifikaci';
$string['restoredfromdraft'] = 'POZNÁMKA: Poslední pokus o klasifikaci tohoto uživatele nebyl správně uložen. Byl proto obnoven koncept klasifikace. Chcete-li tyto změny zrušit, použijte níže tlačítko Zrušit.';
$string['save'] = 'Uložit';
$string['savechecklist'] = 'Uložit a připravit kontrolní seznam';
$string['savechecklistdraft'] = 'Uložit jako koncept';
$string['scorepostfix'] = 'Počet bodů: {$a}';
$string['showitempointseval'] = 'Během hodnocení zobrazovat body pro každou položku';
$string['showitempointstudent'] = 'Zobrazit klasifikovaným uživatelům body za každou položku';
$string['enableitemremarks'] = 'Povolit klasifikátorovi přidat ke každé položce kontrolního seznamu textové poznámky';
$string['enablegroupremarks'] = 'Povolit klasifikátorovi přidat ke každé skupině kontrolního seznamu textové poznámky';
$string['showremarksstudent'] = 'Zobrazit všechny poznámky studentům, kteří jsou právě klasifikováni';
$string['unchecked'] = 'Nezaškrtnuté';
