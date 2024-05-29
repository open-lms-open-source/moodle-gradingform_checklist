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

$string['addgroup'] = 'グループを追加する';
$string['alwaysshowdefinition'] = 'モジュールで使用するチェックリストをユーザがプレビューできるようにする (それ以外の場合は評定後にのみ閲覧可能)';
$string['backtoediting'] = '編集に戻る';
$string['checked'] = 'チェック済み';
$string['checkitem'] = '"{$a}"の満点をマークする';
$string['checklist'] = 'チェックリスト';
$string['checklistmapping'] = '評定マッピングルールの評点';
$string['checklistmappingexplained'] = 'このチェックリストの最小可能スコアは <b>{$a->minscore} ポイント</b>です。これは、このモジュールで利用可能な最小評点に変換されます（スケールが使用されない限りゼロになります）。最大スコア <b>{$a->maxscore} ポイント</b>が最大評点に変換されます。<br />中間スコアは個別に変換され、利用可能な評点に四捨五入されます。<br />評点の代わりにスケールが使用される場合、スコアは連続する整数のようにスケール要素に変換されます。';
$string['checklistoptions'] = 'チェックリストオプション';
$string['checkliststatus'] = '現在のチェックリストのステータス';
$string['confirmdeletegroup'] = 'このグループを削除しますか？';
$string['confirmdeleteitem'] = 'この項目を削除しますか？';
$string['definechecklist'] = 'チェックリストを定義する';
$string['description'] = '記述内容';
$string['err_definitionmax'] = '255文字を超える項目定義は指定できません';
$string['err_descriptionmax'] = '255文字を超えるグループ説明は指定できません';
$string['err_nodefinition'] = '項目定義は空白にできません';
$string['err_nodescription'] = 'グループ説明は空白にできません';
$string['err_nogroups'] = 'チェックリストには少なくとも1つのグループを含める必要があります';
$string['err_scoreformat'] = '各項目の点数には、有効な正の数字を指定する必要があります。';
$string['err_scoremax'] = '各項目の点数には、1000以下の値を指定する必要があります';
$string['err_totalscore'] = 'チェックリストで評定する場合、最大評点にはゼロ以上の値を指定する必要があります';
$string['groupfeedback'] = '"{$a}"のグループフィードバック';
$string['gradingof'] = '{$a} 評定';
$string['groupadditem'] = 'アイテムを追加する';
$string['groupdelete'] = 'グループを削除する';
$string['groupdescription'] = 'グループ説明';
$string['groupempty'] = 'グループを編集するにはクリック';
$string['groupmovedown'] = '下へ';
$string['groupmoveup'] = '上へ';
$string['grouppoints'] = 'グループの評点';
$string['groupremark'] = '"{$a}"のグループのコメント';
$string['itemdefinition'] = '項目定義';
$string['itemdelete'] = 'アイテムを削除する';
$string['itemempty'] = '項目を編集するにはクリック';
$string['itemfeedback'] = '"{$a}"のフィードバック';
$string['itemremark'] = '"{$a}"の項目のコメント';
$string['itemscore'] = '項目の評点';
$string['name'] = 'コース名順';
$string['needregrademessage'] = 'この学生の評定後に、チェックリスト定義が変更されました。チェックリストをレビューして評定を更新するまで、学生はこのチェックリストを閲覧できません。';
$string['pluginname'] = 'チェックリスト';
$string['previewchecklist'] = 'チェックリストをプレビューする';
$string['overallpoints'] = '全体の評点';
$string['regrademessage1'] = 'すでに評定に使用されているチェックリストの変更を保存しようとしています。既存の評定をレビューする必要があるかどうかを指定してください。この設定を有効にすると、再評定されるまでチェックリストは学生に表示されません。';
$string['regrademessage5'] = 'すでに評定に使用されているチェックリストの重要な変更を保存しようとしています。評定表の評点は変更されませんが、評定項目が再評定されるまで、チェックリストは学生に表示されません。';
$string['regradeoption0'] = '再評定をマークしない';
$string['regradeoption1'] = '再評定をマークする';
$string['restoredfromdraft'] = '注意：このユーザの前回の受験に関する評点が適切に保存されなかったため、下書きの評点がリストアされました。これらの変更をキャンセルする場合は、下の[キャンセル]ボタンを使用してください。';
$string['save'] = '保存する';
$string['savechecklist'] = 'チェックリストを保存して利用可能にする';
$string['savechecklistdraft'] = '下書きとして保存する';
$string['scorepostfix'] = '{$a} 点';
$string['showitempointseval'] = '評定中に各項目の評点を表示する';
$string['showitempointstudent'] = '評定中の学生に各項目の評点を表示する';
$string['enableitemremarks'] = '各チェックリストの項目に対して、評定者によるコメントの追加を許可する';
$string['enablegroupremarks'] = '各チェックリストグループに対して、評定者によるコメントの追加を許可する';
$string['showremarksstudent'] = '評定中の学生にコメントをすべて表示する';
$string['unchecked'] = '未チェック';
$string['maxlengthalert'] = 'この入力フィールドの最大文字数は {$a} 文字です。';
