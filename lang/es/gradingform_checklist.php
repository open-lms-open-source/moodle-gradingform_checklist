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

$string['addgroup'] = 'Agregar grupo';
$string['alwaysshowdefinition'] = 'Les permite a los usuarios ver una vista previa de la lista de verificación usada en el módulo (de otra manera, la lista de verificación solo se podrá ver después de la calificación).';
$string['backtoediting'] = 'Volver a la edición';
$string['checked'] = 'Verificado';
$string['checkitem'] = 'Marcar crédito completo para "{$a}"';
$string['checklist'] = 'Lista de verificación';
$string['checklistmapping'] = 'Reglas de mapeo de puntaje a calificación';
$string['checklistmappingexplained'] = 'La puntuación mínima posible para esta lista de verificación es <b>{$a->minscore} puntos</b> y se convertirá a la mínima calificación disponible en este módulo (que es cero, a menos que se use la escala).
    La puntuación máxima <b>{$a->maxscore} puntos</b> se convertirá a la calificación máxima.<br />
    Las puntuaciones intermedias se convertirán respectivamente y se redondearán a la calificación disponible más cercana.<br />
    Si se usa una escala en lugar de una calificación, la puntuación se convertirá a los elementos de la escala, como si fueran números enteros consecutivos.';
$string['checklistoptions'] = 'Opciones de la lista de verificación';
$string['checkliststatus'] = 'Estado actual de la lista de verificación';
$string['confirmdeletegroup'] = '¿Está seguro de que desea eliminar este grupo?';
$string['confirmdeleteitem'] = '¿Está seguro de que desea eliminar este elemento?';
$string['definechecklist'] = 'Definir lista de verificación';
$string['description'] = 'Descripción';
$string['err_definitionmax'] = 'La definición del elemento no puede contener más de 255 caracteres';
$string['err_descriptionmax'] = 'La descripción del grupo no puede contener más de 255 caracteres';
$string['err_nodefinition'] = 'La definición del elemento no puede estar vacía';
$string['err_nodescription'] = 'La descripción del grupo no puede estar vacía';
$string['err_nogroups'] = 'La lista de verificación debe contener al menos un grupo.';
$string['err_scoreformat'] = 'El número de puntos para cada elemento debe ser un número válido que no sea negativo';
$string['err_scoremax'] = 'El número de puntos para cada elemento debe ser mayor que 1000';
$string['err_totalscore'] = 'El número máximo de puntos posible cuando se califique según la lista de verificación debe ser superior a cero';
$string['groupfeedback'] = 'Retroalimentación de grupo para "{$a}"';
$string['gradingof'] = '{$a} calificación';
$string['groupadditem'] = 'Agregar elemento';
$string['groupdelete'] = 'Eliminar grupo';
$string['groupdescription'] = 'Descripción del grupo';
$string['groupempty'] = 'Haga clic para editar el grupo.';
$string['groupmovedown'] = 'Mover hacia abajo';
$string['groupmoveup'] = 'Mover hacia arriba';
$string['grouppoints'] = 'Puntos del grupo';
$string['groupremark'] = 'Observación de grupo para "{$a}"';
$string['itemdefinition'] = 'Definición del elemento';
$string['itemdelete'] = 'Eliminar elemento';
$string['itemempty'] = 'Haga clic para editar el elemento.';
$string['itemfeedback'] = 'Retroalimentación para "{$a}"';
$string['itemremark'] = 'Observación del elemento para "{$a}"';
$string['itemscore'] = 'Puntuación del elemento';
$string['name'] = 'Nombre';
$string['needregrademessage'] = 'La definición de la lista de verificación se cambió después de que se calificó a este estudiante. El estudiante no podrá ver esta lista de verificación hasta que usted la revise y actualice la calificación.';
$string['pluginname'] = 'Lista de verificación';
$string['previewchecklist'] = 'Vista previa de la lista de verificación';
$string['overallpoints'] = 'Puntos generales';
$string['regrademessage1'] = 'Está por guardar cambios en una lista de verificación que ya se usó para poner calificaciones. Indique si las calificaciones existentes deben revisarse. Si establece esto, las listas de verificación se ocultarán de los estudiantes hasta que su elemento se vuelva a calificar.';
$string['regrademessage5'] = 'Está a punto de guardar cambios importantes en una lista de verificación que ya se usó para poner calificaciones. El valor del libro de calificaciones no se cambiará, pero la lista de verificación se ocultará de los estudiantes hasta que su elemento se vuelva a calificar.';
$string['regradeoption0'] = 'No marcar para volver a calificar';
$string['regradeoption1'] = 'Marcar para volver a calificar';
$string['restoredfromdraft'] = 'NOTA: El último intento por calificar a esta persona no se ha guardado correctamente, por lo que se han restaurado las calificaciones del borrador. Si desea cancelar estos cambios utilice el botón "Cancelar" a continuación.';
$string['save'] = 'Guardar';
$string['savechecklist'] = 'Guarde la lista de verificación y prepárela';
$string['savechecklistdraft'] = 'Guardar como borrador';
$string['scorepostfix'] = '{$a} puntos';
$string['showitempointseval'] = 'Mostrar los puntos de cada elemento durante la evaluación.';
$string['showitempointstudent'] = 'Mostrar los puntos de cada elemento que se va a calificar.';
$string['enableitemremarks'] = 'Permite a los calificadores agregar observaciones en cada uno de los elementos de la lista de verificación.';
$string['enablegroupremarks'] = 'Permite a los calificadores agregar observaciones en cada uno de los grupos de la lista de verificación.';
$string['showremarksstudent'] = 'Mostrar todas las observaciones a los que son calificados';
$string['unchecked'] = 'Sin verificar';
