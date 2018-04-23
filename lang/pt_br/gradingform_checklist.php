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

$string['addgroup'] = 'Adicionar grupo';
$string['alwaysshowdefinition'] = 'Permitir que os usuários visualizem a lista de progresso usada no módulo (caso contrário, a lista de progresso só ficará visível após a avaliação)';
$string['backtoediting'] = 'Voltar para edição';
$string['checked'] = 'Marcado';
$string['checkitem'] = 'Marcar crédito total para "{$a}"';
$string['checklist'] = 'Lista de progresso';
$string['checklistmapping'] = 'Regras de mapeamento de pontuação para nota';
$string['checklistmappingexplained'] = 'A mínima pontuação possível para esta lista de progresso é de <b>{$a->minscore} pontos</b> e será convertida na mínima nota disponível neste módulo (que é zero, a menos que a escala seja usada).
    A pontuação máxima de <b>{$a->maxscore} pontos</b> será convertida na nota máxima.<br />
    Notas intermediárias serão convertidas e arredondadas para a nota disponível mais próxima.<br />
    Se uma escala for usada em vez de uma nota, a pontuação será convertida nos elementos da escala como se eles fossem valores inteiros consecutivos.';
$string['checklistoptions'] = 'Opções da lista de progresso';
$string['checkliststatus'] = 'Status atual da lista de progresso';
$string['confirmdeletegroup'] = 'Você tem certeza de que quer excluir este grupo?';
$string['confirmdeleteitem'] = 'Você tem certeza de que quer excluir este item?';
$string['definechecklist'] = 'Definir lista de progresso';
$string['description'] = 'Descrição';
$string['err_definitionmax'] = 'A definição do item não pode ter mais de 255 caracteres';
$string['err_descriptionmax'] = 'A descrição do grupo não pode ter mais de 255 caracteres';
$string['err_nodefinition'] = 'A definição do item não pode ficar vazia';
$string['err_nodescription'] = 'A descrição do grupo não pode ficar vazia';
$string['err_nogroups'] = 'É necessário que a lista de progresso contenha pelo menos um grupo';
$string['err_scoreformat'] = 'O número de pontos para cada item precisa ser um número não negativo válido';
$string['err_scoremax'] = 'O número de pontos para cada item não pode ser maior do que 1.000';
$string['err_totalscore'] = 'O número máximo de pontos possíveis para avaliação por lista de progresso precisa ser maior do que zero';
$string['groupfeedback'] = 'Feedback de grupo para "{$a}"';
$string['gradingof'] = 'Avaliação de {$a}';
$string['groupadditem'] = 'Adicionar item';
$string['groupdelete'] = 'Excluir grupo';
$string['groupdescription'] = 'Descrição do grupo';
$string['groupempty'] = 'Clique para editar grupo';
$string['groupmovedown'] = 'Mover para baixo';
$string['groupmoveup'] = 'Mover para cima';
$string['grouppoints'] = 'Pontos do grupo';
$string['groupremark'] = 'Comentário do grupo para "{$a}"';
$string['itemdefinition'] = 'Definição do item';
$string['itemdelete'] = 'Excluir item';
$string['itemempty'] = 'Clique para editar item';
$string['itemfeedback'] = 'Feedback para "{$a}"';
$string['itemremark'] = 'Comentário do item para "{$a}"';
$string['itemscore'] = 'Pontuação do item';
$string['name'] = 'Nome';
$string['needregrademessage'] = 'A definição da lista de progresso foi alterada após este aluno ter sido avaliado. O aluno não poderá ver esta lista de progresso até que você a revise e atualize a nota.';
$string['pluginname'] = 'Lista de progresso';
$string['previewchecklist'] = 'Visualizar lista de progresso';
$string['overallpoints'] = 'Total de pontos';
$string['regrademessage1'] = 'Você está prestes a salvar alterações em uma lista de progresso que já foi utilizada para avaliação. Indique se as notas existentes precisam ser revisadas. Se você escolher essa opção, a lista de progresso ficará oculta para os alunos até que seus itens sejam reavaliados.';
$string['regrademessage5'] = 'Você está prestes a salvar alterações significativas em uma lista de progresso que já foi utilizada para avaliação. O valor do boletim de notas não será alterado, mas a lista de progresso ficará oculta para os alunos até que seus itens sejam reavaliados.';
$string['regradeoption0'] = 'Não marcar para reavaliar';
$string['regradeoption1'] = 'Marcar para reavaliar';
$string['restoredfromdraft'] = 'OBSERVAÇÃO: a última tentativa de avaliar esta pessoa não foi salva corretamente; portanto, um rascunho das notas foi restaurado. Se você quiser descartar essas alterações, use o botão "Cancelar" abaixo.';
$string['save'] = 'Salvar';
$string['savechecklist'] = 'Salvar e disponibilizar lista de progresso';
$string['savechecklistdraft'] = 'Salvar como rascunho';
$string['scorepostfix'] = '{$a} pontos';
$string['showitempointseval'] = 'Exibir pontos correspondentes a cada item durante a avaliação';
$string['showitempointstudent'] = 'Exibir pontos correspondentes a cada item para quem está sendo avaliado';
$string['enableitemremarks'] = 'Permitir que o avaliador adicione texto para cada item da lista de progresso';
$string['enablegroupremarks'] = 'Permitir que o avaliador adicione comentários de texto para cada grupo da lista de progresso';
$string['showremarksstudent'] = 'Exibir todos os comentários para quem está sendo avaliado';
$string['unchecked'] = 'Não marcado';
