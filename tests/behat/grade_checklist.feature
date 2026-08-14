@gradingform @gradingform_checklist @javascript
Feature: Converting checklist score to grades
  In order to use and refine checklist to grade students
  As a teacher
  I need to be able to use different grade settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name   | description     | course | idnumber | grade[modgrade_type]| advancedgradingmethod_submissions |
      | forum    | forum1 | C1 first forum  | C1     | forum1   | point               | checklist                         |
    And the following forum discussions exist in course "Course 1":
      | user     | forum  | name        | message     |
      | teacher1 | forum1 | discussion1 | message1    |
    And I log in as "teacher1"
    And I change window size to "large"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "forum1" "forum activity editing" page
    And I expand all fieldsets
    And I select "Point" from the "grade_forum[modgrade_type]" singleselect
    And I select "Checklist" from the "advancedgradingmethod_forum" singleselect
    And I press "Save and return to course"
    And I am on the "forum1" "forum activity" page
    And I navigate to "Advanced grading" in current page administration
    And I select "Checklist" from the "setmethod" singleselect
    And I follow "Define new grading form from scratch"
    And I set the following fields to these values:
      | Name | Assignment 1 checklist |
      | Description | Checklist test description |
    And I click on "#checklist-groups-NEWID1-description" "css_element"
    And "//textarea[@id='checklist-groups-NEWID1-description-input'][@maxlength='500']" "xpath" should exist
    And I set the field "checklist-groups-NEWID1-description-input" to "Group 1"
    And I click on "#checklist-groups-NEWID1-items-NEWID0-definition" "css_element"
    And "//textarea[@id='checklist-groups-NEWID1-items-NEWID0-definition-input'][@maxlength='1500']" "xpath" should exist
    And I set the field "checklist-groups-NEWID1-items-NEWID0-definition-input" to "Has title"
    And I click on "#checklist-groups-NEWID1-items-NEWID0-score" "css_element"
    And I set the field "checklist-groups-NEWID1-items-NEWID0-score-input" to "1.5"
    And I click on "#checklist-groups-NEWID1-items-NEWID1-definition" "css_element"
    And I set the field "checklist-groups-NEWID1-items-NEWID1-definition-input" to "Has description"
    And I click on "#checklist-groups-NEWID1-items-NEWID2-definition" "css_element"
    And I set the field "checklist-groups-NEWID1-items-NEWID2-definition-input" to "Has conclusions"
    And I press "Save checklist and make it ready"

  Scenario: Set checklist as a grading method for forums.
    Given I am on "Course 1" course homepage
    And I follow "forum1"
    When I click on "Grade users" "button"
    And I click on "[data-direction='1'][data-action='change-user']" "css_element"
    And I should see "Student 1"
    Then I should not see "Group points: 0/3.5"
    And I should not see "Overall points: 0/3.5"
    And I click on ".form-check-input" "css_element"
    And I should see "Group 1"
    And I should see "Has title"
    And I should see "Has description"
    And I should see "Has conclusions"
    And I should not see "Group points: 1.5/3.5"
    And I should not see "Overall points: 1.5/3.5"
    And "//div[contains(@id, 'criteria-')]//div//textarea" "xpath" should not exist
    And "//div[contains(@id, 'criteria-')]//textarea[contains(@id, '-items-0-remark')]" "xpath" should exist
    And I click on "button[data-action='savegrade']" "css_element"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "forum1"
    When I click on "View grades" "button"
    And I should see "Group 1"
    And I should see "Has title"
    And I should see "Has description"
    And I should see "Has conclusions"
    And I should not see "Group points: 1.5/3.5"
    And I should not see "Overall points: 1.5/3.5"
    And "//div[contains(@id, 'criteria-')]//div//textarea" "xpath" should not exist
    And "//div[contains(@id, 'criteria-')]//textarea[contains(@id, '-items-0-remark')]" "xpath" should exist

  Scenario: Checklist import controls are available on the advanced grading management page
    Given I am on the "forum1" "forum activity editing" page
    And I navigate to "Advanced grading" in current page administration
    And I select "Checklist" from the "setmethod" singleselect
    Then I should see "Import checklist"
    And I should see "Download Word template"
    And I should see "Download JSON example"
    And I should see "Download JSON schema"
    And "//div[contains(concat(' ', normalize-space(@class), ' '), ' gradingform-checklist-import-actions ') and contains(concat(' ', normalize-space(@class), ' '), ' is-defined ')]" "xpath_element" should exist
    And "//div[contains(concat(' ', normalize-space(@class), ' '), ' gradingform-checklist-import-primary ')]/a[contains(@class, 'action')][span[contains(@class, 'fa-file-import')] and span[contains(@class, 'action-text')]]" "xpath_element" should exist
    When I follow "Edit the current form definition"
    Then I should not see "Import checklist"
    And I should not see "Download Word template"
    And I should not see "Download JSON example"
    And I should not see "Download JSON schema"

  Scenario: Teacher benchmark guidance is keyboard accessible
    Given I am on the "forum1" "forum activity editing" page
    And I navigate to "Advanced grading" in current page administration
    And I select "Checklist" from the "setmethod" singleselect
    And I follow "Edit the current form definition"
    And I click on "+ Add benchmarks" "button"
    And I set the field "Benchmark" to "Teacher benchmark guidance"
    And I press "Save"
    And I am on the "forum1" "forum activity" page
    When I click on "Grade users" "button"
    And I click on "[data-direction='1'][data-action='change-user']" "css_element"
    And I click on "Open to view Benchmarks" "button"
    And I wait "1" seconds
    Then the focused element is "Close benchmark" "button"
    And I press the escape key
    And the focused element is "Open to view Benchmarks" "button"

  Scenario: Enable display of item points during evaluation and disable feedback of groups
    And I am on the "forum1" "forum activity editing" page
    And I navigate to "Advanced grading" in current page administration
    And I select "Checklist" from the "setmethod" singleselect
    And I follow "Edit the current form definition"
    And I click on "Display points for each item during evaluation" "checkbox"
    And I click on "Allow grader to add text remarks for each checklist group" "checkbox"
    And I press "Save"
    And I am on the "forum1" "forum activity" page
    When I click on "Grade users" "button"
    And I click on "[data-direction='1'][data-action='change-user']" "css_element"
    And I should see "Student 1"
    Then I should not see "Group points: 0/3.5"
    And I should not see "Overall points: 0/3.5"
    And I should see "1.5 points"
    And "//div[contains(@id, 'criteria-')]//div//textarea" "xpath" should not exist
    And "//div[contains(@id, 'criteria-')]//textarea[contains(@id, '-items-0-remark')]" "xpath" should not exist
    And I log out
    And I am on the "forum1" "forum activity" page logged in as "student1"
    And I click on "View grades" "button"
    Then I should not see "Group points: 0/3.5"
    And I should not see "Overall points: 0/3.5"
    And "//div[contains(@id, 'criteria-')]//div//textarea" "xpath" should not exist
    And "//div[contains(@id, 'criteria-')]//textarea[contains(@id, '-items-0-remark')]" "xpath" should not exist

  Scenario: Enable display points for those being graded and item feedback
    And I am on the "forum1" "forum activity editing" page
    And I navigate to "Advanced grading" in current page administration
    And I select "Checklist" from the "setmethod" singleselect
    And I follow "Edit the current form definition"
    And I click on "Display points for each item to those being graded" "checkbox"
    And I click on "Allow grader to add text remarks for each checklist item" "checkbox"
    And I click on "Show all remarks to those being graded" "checkbox"
    And I press "Save"
    And I am on the "forum1" "forum activity" page
    When I click on "Grade users" "button"
    And I click on "[data-direction='1'][data-action='change-user']" "css_element"
    And I should see "Student 1"
    Then I should not see "Group points: 0/3.5"
    And I should not see "Overall points: 0/3.5"
    And "//div[contains(@id, 'criteria-')]//div//textarea" "xpath" should exist
    And "//div[contains(@id, 'criteria-')]//textarea[contains(@id, '-items-0-remark')]" "xpath" should exist
    And I log out
    And I am on the "forum1" "forum activity" page logged in as "student1"
    And I click on "View grades" "button"
    Then I should not see "Group points: 0/3.5"
    And I should not see "Overall points: 0/3.5"
    And "//div[contains(@id, 'criteria-')]//div//textarea" "xpath" should not exist
    And "//div[contains(@id, 'criteria-')]//textarea[contains(@id, '-items-0-remark')]" "xpath" should not exist

  Scenario: Enable display of group points independently from item points
    And I am on the "forum1" "forum activity editing" page
    And I navigate to "Advanced grading" in current page administration
    And I select "Checklist" from the "setmethod" singleselect
    And I follow "Edit the current form definition"
    And I click on "Display group and overall points during evaluation" "checkbox"
    And I press "Save"
    And I am on the "forum1" "forum activity" page
    When I click on "Grade users" "button"
    And I click on "[data-direction='1'][data-action='change-user']" "css_element"
    And I should see "Student 1"
    Then I should see "Group points: 0/3.5"
    And I should see "Overall points: 0/3.5"
    And I should not see "1.5 points"
