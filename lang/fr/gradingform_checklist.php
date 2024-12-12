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

$string['addgroup'] = 'Ajouter un groupe';
$string['alwaysshowdefinition'] = 'Permet aux utilisateurs de prévisualiser la check-list d\'évaluation dans le module (sinon, la check-list ne sera visible qu\'après l\'évaluation)';
$string['backtoediting'] = 'Retour à l\'édition';
$string['checked'] = 'Coché';
$string['checkitem'] = 'Marquer le crédit complet pour "{$a}"';
$string['checklist'] = 'Check-list';
$string['checklistmapping'] = 'Règles de correspondances entre score et note';
$string['checklistmappingexplained'] = 'La note minimale possible pour cette liste de contrôle est de&nbsp;<b>{$a->minscore}&nbsp;point(s)</b> et elle sera convertie en la note minimale disponible dans ce module (qui est zéro sauf si l\'échelle est utilisée). Le résultat maximum est de&nbsp;<b>{$a->maxscore}&nbsp;point(s)</b> et sera converti en note maximale.<br />Les notes intermédiaires seront converties respectivement et arrondies à la note disponible la plus proche.<br />Si une échelle est utilisée à la place d\'une note, le score sera converti en éléments d\'échelle comme s\'il s\'agissait d\'entiers consécutifs.';
$string['checklistoptions'] = 'Options de check-list';
$string['checkliststatus'] = 'État actuel de la check-list';
$string['confirmdeletegroup'] = 'Voulez-vous vraiment supprimer ce groupe&nbsp;?';
$string['confirmdeleteitem'] = 'Voulez-vous vraiment supprimer cet élément&nbsp;?';
$string['definechecklist'] = 'Définir la check-list';
$string['description'] = 'Description';
$string['err_definitionmax'] = 'La définition de l\'élément ne doit pas dépasser 255&nbsp;caractères';
$string['err_descriptionmax'] = 'La description de groupe ne doit pas dépasser 255&nbsp;caractères';
$string['err_nodefinition'] = 'Vous devez renseigner la définition de l\'élément';
$string['err_nodescription'] = 'Vous devez renseigner la description de groupe';
$string['err_nogroups'] = 'La  check-list doit contenir au moins un groupe';
$string['err_scoreformat'] = 'Le nombre de points pour chaque élément doit être un nombre non négatif valide';
$string['err_scoremax'] = 'Le nombre de points pour chaque élément ne doit pas être supérieur à 1&nbsp;000';
$string['err_totalscore'] = 'Le nombre maximal de points possible lors de la notation au moyen de la check-list doit être supérieur à zéro';
$string['groupfeedback'] = 'Feed-back du groupe pour "{$a}"';
$string['gradingof'] = 'Évaluation de {$a}';
$string['groupadditem'] = 'Ajouter un élément';
$string['groupdelete'] = 'Supprimer un groupe';
$string['groupdescription'] = 'Description du groupe';
$string['groupempty'] = 'Cliquez pour modifier le groupe';
$string['groupmovedown'] = 'Descendre';
$string['groupmoveup'] = 'Monter';
$string['grouppoints'] = 'Points du groupe';
$string['groupremark'] = 'Remarque du groupe pour "{$a}"';
$string['itemdefinition'] = 'Définition de l\'élément';
$string['itemdelete'] = 'Supprimer élément';
$string['itemempty'] = 'Cliquer pour modifier l\'élément';
$string['itemfeedback'] = 'Feed-back pour "{$a}"';
$string['itemremark'] = 'Remarque de l\'élément pour "{$a}"';
$string['itemscore'] = 'Résultat de l\'élément';
$string['name'] = 'Nom';
$string['needregrademessage'] = 'La définition de la check-list a été modifiée après la notation de l\'étudiant. Il ne peut pas voir cette check-list tant que vous ne l\'avez pas passée en revue et que vous n\'avez pas mis la note à jour.';
$string['pluginname'] = 'Check-list';
$string['previewchecklist'] = 'Aperçu de la check-list';
$string['overallpoints'] = 'Points au total';
$string['regrademessage1'] = 'Vous êtes sur le point d\'enregistrer des modifications sur une check-list déjà utilisée pour une notation. Indiquez si les notes existantes doivent être réévaluées. Dans ce cas, les étudiants ne pourront pas voir la check-list, tant que l\'élément n\'est pas réévalué.';
$string['regrademessage5'] = 'Vous êtes sur le point d\'enregistrer des modifications importantes sur une check-list déjà utilisée pour une notation. La note dans le carnet de notes ne sera pas modifiée, mais les étudiants ne pourront pas voir la check-list, tant que leur élément n\'est pas réévalué.';
$string['regradeoption0'] = 'Ne pas marquer pour réévaluation';
$string['regradeoption1'] = 'Marquer pour réévaluation';
$string['restoredfromdraft'] = 'Remarque&nbsp;: la dernière tentative de notation de cette personne n\'a pas été enregistrée correctement, les notes brouillon ont été restaurées. Si vous voulez annuler ces modifications, cliquez sur le bouton Annuler ci-dessous.';
$string['save'] = 'Sauvegarder';
$string['savechecklist'] = 'Enregistrer la check-list et la tenir prête';
$string['savechecklistdraft'] = 'Enregistrer comme brouillon';
$string['scorepostfix'] = '{$a}&nbsp;points';
$string['showitempointseval'] = 'Afficher les points de chaque élément durant l\'évaluation';
$string['showitempointstudent'] = 'Afficher les points de chaque élément aux personnes notées';
$string['enableitemremarks'] = 'Permettre à l\'évaluateur d\'ajouter des remarques textuelles pour chaque élément de la check-list';
$string['enablegroupremarks'] = 'Permettre à l\'évaluateur d\'ajouter des remarques textuelles pour chaque groupe de la check-list';
$string['showremarksstudent'] = 'Afficher toutes les remarques aux personnes notées';
$string['unchecked'] = 'Décoché';
$string['maxlengthalert'] = 'La longueur maximale du champ est de {$a}&nbsp;caractères';
