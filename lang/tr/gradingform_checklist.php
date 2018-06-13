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

$string['addgroup'] = 'Grup ekle';
$string['alwaysshowdefinition'] = 'Kullanıcıların modülde kullanılan kontrol listesini önizlemesine izin ver (aksi takdirde kontrol listesi sadece not verildikten sonra gösterilecektir)';
$string['backtoediting'] = 'Düzenlemeye geri dön';
$string['checked'] = 'Kontrol edildi';
$string['checkitem'] = '"{$a}" için tam kredi işaretle';
$string['checklist'] = 'Kontrol listesi';
$string['checklistmapping'] = 'Puanı nota dönüştürme kuralları';
$string['checklistmappingexplained'] = 'Bu kontrol listesi için minimum olası puan <b>{$a->minscore} puandır</b> ve bu modülde kullanılabilen minimum nota dönüştürülecektir (bu not da ölçek kullanılmadıysa sıfırdır).
Maksimum puan olan <b>{$a->maxscore} puan</b>, maksimum nota dönüştürülecektir.<br />
Ara puanlar, sırasıyla dönüştürülecek ve kullanılabilen en yakın nota yuvarlanacaktır.<br />
Not yerine bir ölçek kullanılırsa puan, ardıl tam sayılarmış gibi ölçek öğelerine dönüştürülecektir.';
$string['checklistoptions'] = 'Kontrol listesi seçenekleri';
$string['checkliststatus'] = 'Mevcut kontrol listesi durumu';
$string['confirmdeletegroup'] = 'Bu grubu silmek istediğinize emin misiniz?';
$string['confirmdeleteitem'] = 'Bu öğeyi silmek istediğinize emin misiniz?';
$string['definechecklist'] = 'Kontrol listesi tanımla';
$string['description'] = 'Açıklama';
$string['err_definitionmax'] = 'Öğe tanımı, 255 karakterden uzun olamaz';
$string['err_descriptionmax'] = 'Grup açıklaması, 255 karakterden uzun olamaz';
$string['err_nodefinition'] = 'Öğe tanımı boş olamaz';
$string['err_nodescription'] = 'Grup açıklaması boş olamaz';
$string['err_nogroups'] = 'Kontrol listesi en az bir grup içermeli';
$string['err_scoreformat'] = 'Her öğe için puan sayısı, geçerli bir negatif olmayan sayı olmalı';
$string['err_scoremax'] = 'Her öğe için puan sayısı 1000’den büyük olmamalı';
$string['err_totalscore'] = 'Kontrol listesi tarafından not verilirken maksimum puan sayısı sıfırdan büyük olmalı';
$string['groupfeedback'] = '"{$a}" için grup geri bildirimi';
$string['gradingof'] = '{$a} için not verme';
$string['groupadditem'] = 'Öğe ekle';
$string['groupdelete'] = 'Grubu sil';
$string['groupdescription'] = 'Grup tanımı';
$string['groupempty'] = 'Grubu düzenlemek için tıklat';
$string['groupmovedown'] = 'Aşağıya taşı';
$string['groupmoveup'] = 'Yukarıya taşı';
$string['grouppoints'] = 'Grup puanları';
$string['groupremark'] = '"{$a}" için grup açıklaması';
$string['itemdefinition'] = 'Öğe tanımı';
$string['itemdelete'] = 'Öğeyi sil';
$string['itemempty'] = 'Öğeyi düzenlemek için tıklat';
$string['itemfeedback'] = '"{$a}" için geri bildirim';
$string['itemremark'] = '"{$a}" için öğe açıklaması';
$string['itemscore'] = 'Öğe puanı';
$string['name'] = 'Ad';
$string['needregrademessage'] = 'Kontrol listesi tanımı, bu öğrenciye not verdikten sonra değişti. Öğrenci, siz kontrol listesini kontrol edip notu güncelleştirene kadar bu kontrol listesini göremez.';
$string['pluginname'] = 'Kontrol listesi';
$string['previewchecklist'] = 'Kontrol listesini önizle';
$string['overallpoints'] = 'Genel puan';
$string['regrademessage1'] = 'Zaten not vermek için kullanılmış bir kontrol listesinde yapılan değişiklikleri kaydetmek üzeresiniz. Lütfen
mevcut notların gözden geçirilmesinin gerekip gerekmediğini belirtin. Bunu ayarlarsanız öğelerine yeniden not verilene kadar kontrol listesi öğrencilerden gizlenir.';
$string['regrademessage5'] = 'Zaten not vermek için kullanılmış bir kontrol listesinde yapılan önemli değişiklikleri kaydetmek üzeresiniz. Not defteri değeri değişmeyecek ancak öğelerine yeniden not verilene kadar kontrol listesi öğrencilerden gizlenecektir.';
$string['regradeoption0'] = 'Yeniden not verilmek üzere işaretleme';
$string['regradeoption1'] = 'Yeniden not verilmek üzere işaretle';
$string['restoredfromdraft'] = 'NOT: Bu kişinin son not verme denemesi uygun biçimde kaydedilmediği için taslak notlar geri yüklendi. Bu değişiklikleri iptal etmek istiyorsanız aşağıdaki \'İptal\' düğmesini kullanın.';
$string['save'] = 'Kaydet';
$string['savechecklist'] = 'Kontrol listesini kaydet ve hazır olmasını sağla';
$string['savechecklistdraft'] = 'Taslak olarak kaydet';
$string['scorepostfix'] = '{$a} puan';
$string['showitempointseval'] = 'Değerlendirme sırasında her öğe için puanları göster';
$string['showitempointstudent'] = 'Her öğe için puanları not verilenlere göster';
$string['enableitemremarks'] = 'Not verenin her bir kontrol listesi öğesi için metin açıklamaları eklemesine izin ver';
$string['enablegroupremarks'] = 'Not verenin her bir kontrol listesi grubu için metin açıklamaları eklemesine izin ver';
$string['showremarksstudent'] = 'Tüm açıklamaları not verilenlere göster';
$string['unchecked'] = 'Kontrol edilmedi';
