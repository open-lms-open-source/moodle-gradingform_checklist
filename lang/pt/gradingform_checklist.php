<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'gradingform_checklist', language 'pt', version '3.9'.
 *
 * @package     gradingform_checklist
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'Adicionar grupo';
$string['alwaysshowdefinition'] = 'Permitir aos utilizadores pré-visualizar a lista de verificação utilizada no módulo (caso contrário, a lista de verificação só estará visível após a avaliação)';
$string['backtoediting'] = 'Voltar à edição';
$string['checked'] = 'Verificado';
$string['checkitem'] = 'Atribuir crédito total a "{$a}"';
$string['checklist'] = 'Lista de verificação';
$string['checklistmapping'] = 'Regras de correspondência entre pontuação e avaliação';
$string['checklistmappingexplained'] = 'A pontuação mínima possível para esta lista é de <b>{$a->minscore} pontos</b> e será convertida para a nota mínima disponível neste módulo (que é zero a menos que seja usada uma escala).
A pontuação máxima <b>{$a->maxscore} pontos</b> será convertida para a nota máxima.<br />
Resultados intermédios serão convertidos, respetivamente e arredondados, para a nota mais aproximada.<br />
Se for usada uma escala, em vez de uma nota, a pontuação será convertida para os elementos da escala como se fossem inteiros consecutivos.';
$string['checklistoptions'] = 'Opções da Lista de verificação';
$string['checkliststatus'] = 'Estado da Lista de verificação atual';
$string['confirmdeletegroup'] = 'Tem a certeza de que pretende apagar este grupo?';
$string['confirmdeleteitem'] = 'Tem a certeza de que pretende apagar este item?';
$string['definechecklist'] = 'Definir Lista de verificação';
$string['description'] = 'Descrição';
$string['enablegroupremarks'] = 'Permitir ao avaliador adicionar comentários a cada grupo da Lista de verificação';
$string['enableitemremarks'] = 'Permitir ao avaliador adicionar comentários a cada item da Lista de verificação';
$string['err_definitionmax'] = 'Definição do item não pode ter mais de 255 caracteres';
$string['err_descriptionmax'] = 'Descrição do grupo não pode ter mais de 255 caracteres';
$string['err_nodefinition'] = 'Definição do item não pode estar em branco';
$string['err_nodescription'] = 'Descrição do grupo não pode estar em branco';
$string['err_nogroups'] = 'Lista de verificação tem de ter pelo menos um grupo';
$string['err_scoreformat'] = 'O número de pontos para cada item tem de ser um número válido e não negativo';
$string['err_scoremax'] = 'O número de pontos para cada item não pode ser maior do que 1000';
$string['err_totalscore'] = 'O número máximo de pontos possíveis, quando avaliado pela Lista de verificação, tem de ser maior que zero';
$string['gradingof'] = 'avaliação de {$a}';
$string['groupadditem'] = 'Adicionar item';
$string['groupdelete'] = 'Apagar grupo';
$string['groupdescription'] = 'Descrição do grupo';
$string['groupempty'] = 'Clique para editar grupo';
$string['groupfeedback'] = 'Feedback do grupo para "{$a}"';
$string['groupmovedown'] = 'Mover para baixo';
$string['groupmoveup'] = 'Mover para cima';
$string['grouppoints'] = 'Pontos do grupo';
$string['groupremark'] = 'Observação do grupo para "{$a}"';
$string['itemdefinition'] = 'Definição do item';
$string['itemdelete'] = 'Apagar item';
$string['itemempty'] = 'Clique para editar o item';
$string['itemfeedback'] = 'Feedback para "{$a}"';
$string['itemremark'] = 'Observação do item para "{$a}"';
$string['itemscore'] = 'Pontuação do item';
$string['name'] = 'Nome';
$string['needregrademessage'] = 'A definição da Lista de verificação foi alterada após este aluno ter sido avaliado. O aluno não verá esta Lista de verificação até que reveja a Lista de verificação e atualize a nota.';
$string['overallpoints'] = 'Total de pontos';
$string['pluginname'] = 'Lista de verificação';
$string['previewchecklist'] = 'Pré-visualização da lista';
$string['regrademessage1'] = 'Está prestes a guardar alterações a uma Lista de verificação que já foi utilizada para a avaliação. Por favor, indique se é necessário rever as notas existentes. Se definir que sim, a lista de verificação estará oculta para os alunos até que os respetivos itens sejam reavaliados.';
$string['regrademessage5'] = 'Está prestes a guardar alterações significativas a uma Lista de verificação que já foi utilizada para avaliação. O valor na pauta não será alterado, mas a lista de verificação estará oculta para os alunos até que os respetivos itens sejam reavaliados.';
$string['regradeoption0'] = 'Não marcar para reavaliar';
$string['regradeoption1'] = 'Marcar para reavaliar';
$string['restoredfromdraft'] = 'ATENÇÃO: A última tentativa de avaliação deste utilizador não foi guardada corretamente, por isso foram recuperados os rascunhos das notas. Se pretende cancelar estas alterações, use o botão \'Cancelar\'.';
$string['save'] = 'Guardar';
$string['savechecklist'] = 'Guardar a Lista de verificação e torná-la disponível';
$string['savechecklistdraft'] = 'Guardar como rascunho';
$string['scorepostfix'] = '{$a} pontos';
$string['showitempointseval'] = 'Mostrar os pontos de cada item durante a avaliação';
$string['showitempointstudent'] = 'Mostrar os pontos de cada item aos alunos que estão a ser avaliados';
$string['showremarksstudent'] = 'Mostrar observações aos alunos que estão a ser avaliados';
$string['unchecked'] = 'Não verificado';
