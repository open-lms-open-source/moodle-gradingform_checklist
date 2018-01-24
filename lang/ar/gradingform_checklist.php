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

$string['addgroup'] = 'إضافة مجموعة';
$string['alwaysshowdefinition'] = 'السماح للمستخدمين بمعاينة قائمة الاختيارات المستخدمة في الوحدة النمطية (وإلا لن تكون قائمة التحقق مرئية إلا بعد منح التقدير فقط)';
$string['backtoediting'] = 'الرجوع إلى التحرير';
$string['checked'] = 'تم التحقق';
$string['checkitem'] = 'منح رصيد كامل للعلامة "{$a}"';
$string['checklist'] = 'قائمة الاختيارات';
$string['checklistmapping'] = 'درجة إلى قواعد تعيين التقدير';
$string['checklistmappingexplained'] = 'الحد الأدنى للدرجة المحتملة لقائمة الاختيار هذه هي <b>{$a->minscore} نقطة/نقاط</b> وسيتم تحويلها إلى الحد الأدنى للتقدير المتاح في هذه الوحدة النمطية (وهو صفر ما لم يتم استخدام المقياس).
سيتم تحويل الحد الأقصى للدرجات الذي يبلغ <b>{$a->maxscore} نقطة/نقاط</b> إلى الحد الأقصى للتقدير.<br />
سيتم تحويل الدرجات المتوسطة على التوالي وتقريبها إلى أقرب تقدير متاح.<br />
في حالة استخدام أحد المقاييس بدلًا من التقدير، سيتم تحويل الدرجة إلى عناصر المقياس كما لو أنها أعداد صحيحة متتالية.';
$string['checklistoptions'] = 'خيارات قائمة الاختيارات';
$string['checkliststatus'] = 'حالة قائمة الاختيارات الحالية';
$string['confirmdeletegroup'] = 'هل تريد بالتأكيد حذف هذه المجموعة؟';
$string['confirmdeleteitem'] = 'هل تريد بالفعل حذف هذا العنصر؟';
$string['definechecklist'] = 'تعريف قائمة الاختيارات';
$string['description'] = 'الوصف';
$string['err_definitionmax'] = 'يجب ألا يبلغ طول تعريف العنصر أكثر من 255 حرف';
$string['err_descriptionmax'] = 'يجب ألا يبلغ طول وصف المجموعة أكثر من 255 حرف';
$string['err_nodefinition'] = 'لا يمكن أن يكون تعريف العنصر فارغ';
$string['err_nodescription'] = 'لا يمكن أن يكون وصف المجموعة فارغ';
$string['err_nogroups'] = 'يجب أن تحتوي قائمة الاختيارات على مجموعة واحدة على الأقل';
$string['err_scoreformat'] = 'يجب أن يكون عدد النقاط لكل عنصر رقم غير سالب صالح';
$string['err_scoremax'] = 'يجب ألا يكون عدد النقاط الخاص بكل عنصر أكبر من 1000';
$string['err_totalscore'] = 'يجب أن يكون الحد الأقصى لعدد النقاط المحتملة عند تقديرها باستخدام قائمة الاختيارات أكبر من صفر';
$string['groupfeedback'] = 'ملاحظات المجموعة لـ "{$a}"';
$string['gradingof'] = '{$a} التقدير';
$string['groupadditem'] = 'إضافة عنصر';
$string['groupdelete'] = 'حذف المجموعة';
$string['groupdescription'] = 'وصف المجموعة';
$string['groupempty'] = 'انقر لتحرير المجموعة';
$string['groupmovedown'] = 'نقل لأسفل';
$string['groupmoveup'] = 'نقل لأعلى';
$string['grouppoints'] = 'نقاط المجموعة';
$string['groupremark'] = 'ملاحظة المجموعة لـ "{$a}"';
$string['itemdefinition'] = 'تعريف عنصر';
$string['itemdelete'] = 'حذف عنصر';
$string['itemempty'] = 'انقر لتحرير عنصر';
$string['itemfeedback'] = 'ملاحظات لـ "{$a}"';
$string['itemremark'] = 'ملاحظة العنصر لـ "{$a}"';
$string['itemscore'] = 'درجة العنصر';
$string['name'] = 'الاسم';
$string['needregrademessage'] = 'تم تغيير تعريف قائمة الاختيارات بعد منح تقدير لهذا الطالب. لا يمكن للطالب رؤية قائمة الاختيارات هذه حتى تقوم بمراجعة قائمة الاختيارات وتحديث التقدير.';
$string['pluginname'] = 'قائمة الاختيارات';
$string['previewchecklist'] = 'معاينة قائمة الاختيارات';
$string['overallpoints'] = 'إجمالي النقاط';
$string['regrademessage1'] = 'أنت على وشك حفظ التغييرات إلى قائمة الاختيارات التي تم استخدامها بالفعل في التقدير. يرجى
تحديد ما إذا كانت التقديرات الموجودة بحاجة إلى مراجعة أم لا. إذا قمت بتعيينها فسيتم إخفاء قائمة الاختيارات عن الطلاب حتى يتم إعادة تقدير العناصر الخاصة بهم.';
$string['regrademessage5'] = 'أنت على وشك حفظ تغييرات مهمة إلى قائمة الاختيارات التي تم استخدامها بالفعل في التقدير. لن تتغير قيمة دفتر التقديرات ولكن سيتم إخفاء قائمة الاختيارات عن الطلاب حتى يتم إعادة تقدير العناصر الخاصة بهم.';
$string['regradeoption0'] = 'عدم وضع علامة لإعادة التقدير';
$string['regradeoption1'] = 'وضع علامة لإعادة التقدير';
$string['restoredfromdraft'] = 'ملاحظة: لم يتم حفظ المحاولة الأخيرة لإعطاء تقدير لهذا الشخص بشكل سليم لذا فقد تم استرداد تقديرات المسودة. إذا كنت ترغب في إلغاء هذه التغييرات فاستخدم الزر \'إلغاء الأمر\' الموجود أدناه.';
$string['save'] = 'حفظ';
$string['savechecklist'] = 'حفظ قائمة الاختيارات وجعلها جاهزة';
$string['savechecklistdraft'] = 'حفظ كمسودة';
$string['scorepostfix'] = '{$a} النقاط';
$string['showitempointseval'] = 'عرض النقاط لكل عنصر أثناء التقييم';
$string['showitempointstudent'] = 'عرض النقاط لكل عنصر لهؤلاء الذين يتم منحهم تقديرًا';
$string['enableitemremarks'] = 'السماح لمصنف التقدير بإضافة ملاحظات نصية لكل عنصر قائمة اختيار';
$string['enablegroupremarks'] = 'السماح لمصنف التقدير بإضافة ملاحظات نصية لكل مجموعة من مجموعات قائمة الاختيارات';
$string['showremarksstudent'] = 'إظهار جميع الملاحظات لهؤلاء الذين يتم منحهم تقديرًا';
$string['unchecked'] = 'غير محدد';
