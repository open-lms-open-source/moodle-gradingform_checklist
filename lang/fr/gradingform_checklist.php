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

$string['addgroup'] = 'Ajouter un groupe';
$string['alwaysshowdefinition'] = 'Permet aux utilisateurs de prévisualiser la check-list d\'évaluation dans le module (sinon, la check-list ne sera visible qu\'après l\'évaluation)';
$string['backtoediting'] = 'Retour à la modification';
$string['checked'] = 'Coché';
$string['checkitem'] = 'Marquer le crédit complet pour "{$a}"';
$string['checklist'] = 'Check-list';
$string['checklistmapping'] = 'Règles de correspondances entre résultat et note';
$string['checklistmappingexplained'] = 'Le résultat minimum possible pour cette check-list est de <b>{$a->minscore} points</b> et sera converti en note minimale possible pour le module (zéro, sauf en cas d\'utilisation d\'échelle).
    Le résultat maximum est de <b>{$a->maxscore} points</b> et sera converti en note maximale.<br />
    Les résultats intermédiaires seront convertis respectivement et arrondis à la note disponible la plus proche.<br />
    Si vous utilisez une échelle au lieu d\'une note, le résultat est converti en élément d\'échelle comme s\'il s\'agissait d\'une suite de nombres entiers.';
$string['checklistoptions'] = 'Options de check-list';
$string['checkliststatus'] = 'État actuel de la check-list';
$string['confirmdeletegroup'] = 'Voulez-vous vraiment supprimer ce groupe ?';
$string['confirmdeleteitem'] = 'Voulez-vous vraiment supprimer cet élément ?';
$string['definechecklist'] = 'Définir la check-list';
$string['description'] = 'Description';
$string['err_definitionmax'] = 'La définition de l\'élément ne doit pas dépasser 255 caractères';
$string['err_descriptionmax'] = 'La description de groupe ne doit pas dépasser 255 caractères';
$string['err_nodefinition'] = 'Vous devez renseigner la définition de l\'élément';
$string['err_nodescription'] = 'Vous devez renseigner la description de groupe';
$string['err_nogroups'] = 'La  check-list doit contenir au moins un groupe';
$string['err_scoreformat'] = 'Le nombre de points pour chaque élément doit être un nombre non négatif valide';
$string['err_scoremax'] = 'Le nombre de points pour chaque élément ne doit pas être supérieur à 1 000';
$string['err_totalscore'] = 'Le nombre maximal de points possible lors de la notation au moyen de la check-list doit être supérieur à zéro';
$string['groupfeedback'] = 'Feed-back du groupe pour "{$a}"';
$string['gradingof'] = 'Notation de {$a}';
$string['groupadditem'] = 'Ajouter un élément';
$string['groupdelete'] = 'Supprimer un groupe';
$string['groupdescription'] = 'Description du groupe';
$string['groupempty'] = 'Cliquez pour modifier le groupe';
$string['groupmovedown'] = 'Déplacer vers le bas';
$string['groupmoveup'] = 'Déplacer vers le haut';
$string['grouppoints'] = 'Points du groupe';
$string['groupremark'] = 'Remarque du groupe pour "{$a}"';
$string['itemdefinition'] = 'Définition de l\'élément';
$string['itemdelete'] = 'Supprimer l\'élément';
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
$string['restoredfromdraft'] = 'Remarque : la dernière tentative de notation de cette personne n\'a pas été enregistrée correctement, les notes brouillon ont été restaurées. Si vous voulez annuler ces modifications, cliquez sur le bouton Annuler ci-dessous.';
$string['save'] = 'Enregistrer';
$string['savechecklist'] = 'Enregistrer la check-list et la tenir prête';
$string['savechecklistdraft'] = 'Enregistrer comme brouillon';
$string['scorepostfix'] = '{$a} points';
$string['showitempointseval'] = 'Afficher les points de chaque élément durant l\'évaluation';
$string['showitempointstudent'] = 'Afficher les points de chaque élément aux personnes notées';
$string['enableitemremarks'] = 'Permettre à l\'évaluateur d\'ajouter des remarques textuelles pour chaque élément de la check-list';
$string['enablegroupremarks'] = 'Permettre à l\'évaluateur d\'ajouter des remarques textuelles pour chaque groupe de la check-list';
$string['showremarksstudent'] = 'Afficher toutes les remarques aux personnes notées';
$string['unchecked'] = 'Décoché';
