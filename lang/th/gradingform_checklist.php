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
 * @copyright  Copyright (c) 2020 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'เพิ่มกลุ่ม';
$string['alwaysshowdefinition'] = 'อนุญาตให้ผู้ใช้งานดูตัวอย่างรายการตรวจสอบที่ใช้ในโมดูล (มิฉะนั้น รายการตรวจสอบจะปรากฏเฉพาะหลังจากการให้เกรดเท่านั้น)';
$string['backtoediting'] = 'กลับไปที่การแก้ไข';
$string['checked'] = 'ตรวจสอบแล้ว';
$string['checkitem'] = 'ทำเครื่องหมายหน่วยกิตเต็มสำหรับ "{$a}"';
$string['checklist'] = 'รายการตรวจสอบ';
$string['checklistmapping'] = 'กฎการแมปคะแนนกับเกรด';
$string['checklistmappingexplained'] = 'คะแนนต่ำสุดที่เป็นไปได้สำหรับรายการตรวจสอบนี้คือ <b>{$a->minscore} คะแนน</b> และจะถูกแปลงเป็นเกรดต่ำสุดที่มีอยู่ในโมดูลนี้ (ซึ่งเป็นศูนย์เว้นแต่จะใช้เกณฑ์คะแนน)
  คะแนนสูงสุด <b>{$a->maxscore} คะแนน</b> จะถูกแปลงเป็นเกรดสูงสุด<br /> 
  คะแนนกลางจะถูกแปลงตามลำดับและถูกปัดเศษเป็นเกรดที่ใกล้ที่สุดที่มีอยู่<br /> 
  หากมีการใช้เกณฑ์คะแนนแทนเกรด คะแนนจะถูกแปลงเป็นองค์ประกอบของเกณฑ์คะแนนเสมือนว่าเป็นจำนวนเต็มต่อเนื่อง';
$string['checklistoptions'] = 'ตัวเลือกรายการตรวจสอบ';
$string['checkliststatus'] = 'สถานะรายการตรวจสอบปัจจุบัน';
$string['confirmdeletegroup'] = 'คุณแน่ใจหรือว่าต้องการลบกลุ่มนี้';
$string['confirmdeleteitem'] = 'คุณแน่ใจหรือว่าต้องการลบรายการนี้';
$string['definechecklist'] = 'กำหนดรายการตรวจสอบ';
$string['description'] = 'คำอธิบาย';
$string['err_definitionmax'] = 'คำอธิบายรายการต้องมีอักขระไม่เกิน 255 ตัว';
$string['err_descriptionmax'] = 'คำอธิบายกลุ่มต้องมีอักขระไม่เกิน 255 ตัว';
$string['err_nodefinition'] = 'คำอธิบายรายการต้องไม่ว่างเปล่า';
$string['err_nodescription'] = 'คำอธิบายกลุ่มต้องไม่ว่างเปล่า';
$string['err_nogroups'] = 'รายการตรวจสอบต้องมีอย่างน้อยหนึ่งกลุ่ม';
$string['err_scoreformat'] = 'จำนวนคะแนนสำหรับแต่ละรายการจะต้องเป็นจำนวนที่ไม่เป็นลบที่ถูกต้อง';
$string['err_scoremax'] = 'จำนวนคะแนนสำหรับแต่ละรายการต้องไม่มากกว่า 1,000';
$string['err_totalscore'] = 'จำนวนคะแนนสูงสุดที่เป็นไปได้เมื่อให้คะแนนตามรายการตรวจสอบต้องมากกว่าศูนย์';
$string['groupfeedback'] = 'ผลตอบรับของกลุ่มสำหรับ "{$a}"';
$string['gradingof'] = 'การให้เกรด {$a}';
$string['groupadditem'] = 'เพิ่มรายการ';
$string['groupdelete'] = 'ลบกลุ่ม';
$string['groupdescription'] = 'คำอธิบายกลุ่ม';
$string['groupempty'] = 'คลิกเพื่อแก้ไขกลุ่ม';
$string['groupmovedown'] = 'เลื่อนลง';
$string['groupmoveup'] = 'เลื่อนขึ้น';
$string['grouppoints'] = 'คะแนนกลุ่ม';
$string['groupremark'] = 'ข้อสังเกตกลุ่มสำหรับ "{$a}"';
$string['itemdefinition'] = 'คำอธิบายรายการ';
$string['itemdelete'] = 'ลบรายการ';
$string['itemempty'] = 'คลิกเพื่อแก้ไขรายการ';
$string['itemfeedback'] = 'ผลตอบรับสำหรับ "{$a}"';
$string['itemremark'] = 'ข้อสังเกตรายการสำหรับ "{$a}"';
$string['itemscore'] = 'คะแนนรายการ';
$string['name'] = 'ชื่อ';
$string['needregrademessage'] = 'คำอธิบายรายการตรวจสอบมีการเปลี่ยนแปลงหลังจากที่ผู้เรียนคนนี้ได้รับเกรด ผู้เรียนไม่สามารถเห็นรายการตรวจสอบนี้จนกว่าคุณจะตรวจสอบรายการตรวจสอบและอัปเดตเกรด';
$string['pluginname'] = 'รายการตรวจสอบ';
$string['previewchecklist'] = 'ดูตัวอย่างรายการตรวจสอบ';
$string['overallpoints'] = 'คะแนนโดยรวม';
$string['regrademessage1'] = 'คุณกำลังจะบันทึกการเปลี่ยนแปลงในรายการตรวจสอบที่ใช้สำหรับการให้เกรดแล้ว โปรดระบุว่าจำเป็นต้องทบทวนเกรดที่มีอยู่เดิมหรือไม่ หากคุณตั้งค่านี้ รายการตรวจสอบจะถูกซ่อนจากผู้เรียนจนกว่ารายการของพวกเขาจะได้รับการให้เกรดใหม่';
$string['regrademessage5'] = 'คุณกำลังจะบันทึกการเปลี่ยนแปลงที่สำคัญในรายการตรวจสอบที่ใช้สำหรับการให้เกรดแล้ว ค่าของสมุดเกรดจะไม่เปลี่ยนแปลง แต่รายการตรวจสอบจะถูกซ่อนจากผู้เรียนจนกว่ารายการของพวกเขาจะได้รับการให้เกรดใหม่';
$string['regradeoption0'] = 'ไม่ต้องทำเครื่องหมายสำหรับการให้เกรดใหม่';
$string['regradeoption1'] = 'ทำเครื่องหมายสำหรับการให้เกรดใหม่';
$string['restoredfromdraft'] = 'หมายเหตุ: ความพยายามครั้งล่าสุดเพื่อให้คะแนนบุคคลนี้ไม่ได้ถูกบันทึกอย่างถูกต้อง ดังนั้นเกรดแบบร่างจึงได้รับการกู้คืนข้อมูล หากคุณต้องการยกเลิกการเปลี่ยนแปลงเหล่านี้ ใช้ปุ่ม \'ยกเลิก\' ด้านล่าง';
$string['save'] = 'บันทึก';
$string['savechecklist'] = 'บันทึกรายการตรวจสอบและทำให้พร้อม';
$string['savechecklistdraft'] = 'บันทึกเป็นร่าง';
$string['scorepostfix'] = '{$a} คะแนน';
$string['showitempointseval'] = 'แสดงคะแนนสำหรับแต่ละรายการระหว่างการประเมิน';
$string['showitempointstudent'] = 'แสดงคะแนนสำหรับแต่ละรายการต่อผู้ที่ได้รับเกรด';
$string['enableitemremarks'] = 'อนุญาตผู้ให้เกรดเพิ่มข้อความข้อสังเกตสำหรับแต่ละรายการในรายการตรวจสอบ';
$string['enablegroupremarks'] = 'อนุญาตผู้ให้เกรดเพิ่มข้อความข้อสังเกตสำหรับรายการตรวจสอบแต่ละกลุ่ม';
$string['showremarksstudent'] = 'แสดงข้อสังเกตทั้งหมดต่อผู้ที่ได้รับเกรด';
$string['unchecked'] = 'ไม่ถูกตรวจสอบ';
