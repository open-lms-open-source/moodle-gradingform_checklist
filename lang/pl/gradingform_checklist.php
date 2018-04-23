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

$string['addgroup'] = 'Dodaj grupę';
$string['alwaysshowdefinition'] = 'Zezwalaj użytkownikom na podgląd formularza kryteriów używanego w module (w przeciwnym razie formularz widoczny będzie dopiero po ocenieniu)';
$string['backtoediting'] = 'Powróć do edycji';
$string['checked'] = 'Sprawdzone';
$string['checkitem'] = 'Zaznacz pełny punkt dla „{$a}”';
$string['checklist'] = 'Lista kryteriów';
$string['checklistmapping'] = 'Przeliczenie oceny formularza na ocenę modułu';
$string['checklistmappingexplained'] = 'Minimalna możliwa liczba punktów dla tego formularza to <b>{$a->minscore}</b>Wynik ten zostanie przeliczony na minimalną dostępną w module ocenę (która wynosi zero, chyba że zastosowano skalę).
    Maksymalna liczba <b>{$a->maxscore} punktów</b> zostanie przeliczona na ocenę maksymalną.<br />
    Wyniki pośrednie będą przeliczane proporcjonalnie i zaokrąglane do najbliższego stopnia.<br />
    Jeśli zamiast stopnia użyto skalę, wynik zostanie przeliczony na elementy skali, tak jakby były one kolejnymi liczbami całkowitymi.';
$string['checklistoptions'] = 'Opcje formularza';
$string['checkliststatus'] = 'Bieżący status formularza';
$string['confirmdeletegroup'] = 'Czy na pewno chcesz usunąć tę grupę?';
$string['confirmdeleteitem'] = 'Czy na pewno chcesz usunąć tą pozycję?';
$string['definechecklist'] = 'Definiuj formularz';
$string['description'] = 'Opis';
$string['err_definitionmax'] = 'Definicja przedmiotu nie może być dłuższa niż 255 znaków';
$string['err_descriptionmax'] = 'Opis grupy nie może być dłuższy niż 255 znaków';
$string['err_nodefinition'] = 'Definicja przedmiotu nie może być pusta';
$string['err_nodescription'] = 'Opis grupy nie może być pusty';
$string['err_nogroups'] = 'Formularz musi zawierać co najmniej jedną grupę';
$string['err_scoreformat'] = 'Liczba punktów za każdy przedmiot musi być prawidłową liczbą nieujemną';
$string['err_scoremax'] = 'Liczba punktów za każdy przedmiot nie może przekraczać 1000';
$string['err_totalscore'] = 'Maksymalna możliwa liczba punktów do otrzymania przy ocenianiu według listy kryteriów musi być większa od zera';
$string['groupfeedback'] = 'Komunikat zwrotny grupy dla "{$a}"';
$string['gradingof'] = '{$a}: zasady oceniania';
$string['groupadditem'] = 'Dodaj element';
$string['groupdelete'] = 'Usuń grupę';
$string['groupdescription'] = 'Opis grupy';
$string['groupempty'] = 'Kliknij, aby edytować grupę';
$string['groupmovedown'] = 'Przesuń w dół';
$string['groupmoveup'] = 'Przesuń do góry';
$string['grouppoints'] = 'Punkty grupowe';
$string['groupremark'] = 'Uwaga grupowa dla „{$a}”';
$string['itemdefinition'] = 'Definicja przedmiotu';
$string['itemdelete'] = 'Usuń element';
$string['itemempty'] = 'Kliknij, aby edytować przedmiot';
$string['itemfeedback'] = 'Informacja zwrotna dla „{$a}”';
$string['itemremark'] = 'Uwaga na temat przedmiotu dla „{$a}”';
$string['itemscore'] = 'Wynik z przedmiotu';
$string['name'] = 'Nazwa';
$string['needregrademessage'] = 'Definicja formularza kryteriów została zmieniona po ocenieniu tego studenta. Dlatego student ten nie zobaczy tego formularza, dopóki go nie przejrzysz i nie zaktualizujesz oceny.';
$string['pluginname'] = 'Lista kryteriów';
$string['previewchecklist'] = 'Podgląd formularza kryteriów';
$string['overallpoints'] = 'Ogólna liczba punktów';
$string['regrademessage1'] = 'Użytkownik ma zamiar zapisać zmiany w formularzu kryteriów, który był już wykorzystywany do oceniania. Należy wskazać, czy istniejące oceny wymagają sprawdzenia. Jeżeli tak, formularz będzie ukryty dla studentów do czasu ponownej oceny ich prac.';
$string['regrademessage5'] = 'Użytkownik ma zamiar zapisać istotne zmiany w formularzu kryteriów, który był już wykorzystywany do oceniania. Wartość w dzienniku ocen nie ulegnie zmianie, ale informacje szczegółowe będą ukryte dla studentów do czasu ponownej oceny ich prac.';
$string['regradeoption0'] = 'Nie zaznaczaj do ponownego ocenienia';
$string['regradeoption1'] = 'Zaznacz do ponownego ocenienia';
$string['restoredfromdraft'] = 'UWAGA: ostatnia próba ocenienia tego użytkownika nie została poprawnie zapisana, dlatego przywrócono tymczasowo zachowane oceny. Aby zrezygnować z tych zmian, należy nacisnąć przycisk „Anuluj” poniżej.';
$string['save'] = 'Zapisz';
$string['savechecklist'] = 'Zapisz formularz i ustaw go jako gotowy';
$string['savechecklistdraft'] = 'Zapisz jako szkic';
$string['scorepostfix'] = 'Liczba punktów: {$a}';
$string['showitempointseval'] = 'Podczas oceniania wyświetlaj punktację za każdy przedmiot';
$string['showitempointstudent'] = 'Oceniani widzą punktację za każdy przedmiot';
$string['enableitemremarks'] = 'Pozwól oceniającemu na dodawanie uwag tekstowych do każdego kryterium';
$string['enablegroupremarks'] = 'Pozwól oceniającemu na dodawanie uwag tekstowych do każdej grupy kryteriów';
$string['showremarksstudent'] = 'Oceniani widzą wszystkie uwagi';
$string['unchecked'] = 'Niesprawdzone';
