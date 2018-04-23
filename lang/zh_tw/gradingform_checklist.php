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

$string['addgroup'] = '新增群組';
$string['alwaysshowdefinition'] = '允許使用者預覽模組中所使用的檢查清單 (否則檢查清單只在評分後才會看見)';
$string['backtoediting'] = '返回繼續編輯';
$string['checked'] = '已檢查';
$string['checkitem'] = '標記「{$a}」的完整記分';
$string['checklist'] = '檢查清單';
$string['checklistmapping'] = '得分到成績的對應規則';
$string['checklistmappingexplained'] = '此檢查清單的最低可能得分為 <b>{$a->minscore} 點</b>，將轉換為此模組中可用的最低成績 (除非使用等級，否則為零)。
    最高得分 <b>{$a->maxscore} 點</b>將轉換為最高成績。<br />
    中間的得分將分別轉換，取整數為最接近的可用成績。<br />
    若是使用等級而非成績，得分將轉換為視同連續整數的等級元素。';
$string['checklistoptions'] = '檢查清單選項';
$string['checkliststatus'] = '目前的檢查清單狀態';
$string['confirmdeletegroup'] = '您確定要刪除此群組嗎？';
$string['confirmdeleteitem'] = '您確定要刪除此項目嗎？';
$string['definechecklist'] = '定義檢查清單';
$string['description'] = '說明';
$string['err_definitionmax'] = '項目定義不得超過 255 個字元';
$string['err_descriptionmax'] = '群組說明不得超過 255 個字元';
$string['err_nodefinition'] = '項目定義不得留空';
$string['err_nodescription'] = '群組說明不得留空';
$string['err_nogroups'] = '檢查清單必須包含至少一個群組';
$string['err_scoreformat'] = '各項目的分數必須是有效的非負數';
$string['err_scoremax'] = '各項目的分數不得超過 1000';
$string['err_totalscore'] = '以檢查清單評分的滿分數量上限必須大於零';
$string['groupfeedback'] = '「{$a}」的群組意見回應';
$string['gradingof'] = '{$a} 評分';
$string['groupadditem'] = '新增項目';
$string['groupdelete'] = '刪除群組';
$string['groupdescription'] = '群組說明';
$string['groupempty'] = '按一下以編輯群組';
$string['groupmovedown'] = '往下移';
$string['groupmoveup'] = '往上移';
$string['grouppoints'] = '群組分數';
$string['groupremark'] = '「{$a}」的群組備註';
$string['itemdefinition'] = '項目定義';
$string['itemdelete'] = '刪除項目';
$string['itemempty'] = '按一下以編輯項目';
$string['itemfeedback'] = '「{$a}」的意見回應';
$string['itemremark'] = '「{$a}」的項目備註';
$string['itemscore'] = '項目得分';
$string['name'] = '名稱';
$string['needregrademessage'] = '此學員經評分後，檢查清單的定義曾作變更。在您檢閱檢查清單且更新成績之後，學員才能看見此檢查清單。';
$string['pluginname'] = '檢查清單';
$string['previewchecklist'] = '預覽檢查清單';
$string['overallpoints'] = '整體分數';
$string['regrademessage1'] = '您即將儲存變更至曾經用於評分的檢查清單。請指示是否需要審閱已有的成績。若您設定為是，在學員的項目完成重新評分之前，系統將對學員隱藏檢查清單。';
$string['regrademessage5'] = '您即將儲存針對已用於評分的檢查清單所進行的重大變更。成績單的值將維持不變，但在學員的項目完成重新評分之前，系統將對學員隱藏檢查清單。';
$string['regradeoption0'] = '不標記為重新評分';
$string['regradeoption1'] = '標記為重新評分';
$string['restoredfromdraft'] = '注意：由於最後一次嘗試對此人評分時未正確儲存，因此現將草稿成績還原。若您想要取消這些變更，請使用下方的「取消」按鈕。';
$string['save'] = '儲存';
$string['savechecklist'] = '儲存檢查清單並提供使用';
$string['savechecklistdraft'] = '另存為草稿';
$string['scorepostfix'] = '{$a} 點數';
$string['showitempointseval'] = '評估時顯示各項目的分數';
$string['showitempointstudent'] = '向受評者顯示各項目的分數';
$string['enableitemremarks'] = '允許評分者針對各個檢查清單項目新增文字備註';
$string['enablegroupremarks'] = '允許評分者針對各個檢查清單群組新增文字備註';
$string['showremarksstudent'] = '向受評者顯示所有備註';
$string['unchecked'] = '未檢查';
