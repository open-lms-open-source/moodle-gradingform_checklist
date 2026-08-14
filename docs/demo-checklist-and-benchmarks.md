# Moodle Checklist Demo: Groups, Items, and Benchmarks

I checked the plugin installed in `moodle-docker-webserver-1`.

Important note: that container is running Moodle `4.5.11+ (Build: 20260604)`, not Moodle 4.2. The installed checklist plugin has group-level Benchmarks. Each checklist group has:

- Group description
- Benchmark
- Checklist items

Each checklist item has:

- Item definition
- Item score

## Activity Setup

- Activity type: Assignment
- Activity name: Research Brief Demo
- Maximum grade: 20
- Advanced grading method: Checklist

Activity description:

Write a short research brief that answers one focused question connected to the course topic. Your brief should identify the issue, explain why it matters, use evidence from course materials or credible external sources, and finish with a clear conclusion. The aim is to show that you can define a small research problem, support your response with evidence, and communicate your thinking clearly.

## Checklist Setup

- Name: Research Brief Checklist Demo
- Criteria comment title: Marker comment

Checklist description:

Use this checklist to assess whether the research brief is complete, focused, evidence-based, and clearly communicated. Award each item only when the submitted work meets the stated requirement. Use the group benchmarks as teacher-only guidance when deciding whether the checked items are justified.

## Recommended Checklist Options

Enable these for a full plugin test:

- Allow users to preview checklist used in the module
- Show all remarks to those being graded
- Allow grader to select or unselect all checklist items
- Display points for each item to those being graded
- Display group and overall points to those being graded
- Allow grader to add text remarks for each checklist group
- Require group comments for groups with any checked item
- Require at least one group comment
- Display points for each item during evaluation
- Display group and overall points during evaluation
- Allow grader to add text remarks for each checklist item
- Require item comments for checked items
- Require at least one item comment

## Checklist

Total: 20 points

### Group 1: Task Completion

Group description:

The submission is complete, on time, and follows the required activity instructions.

Items:

| Item definition | Score |
| --- | ---: |
| Submitted the research brief before the due date. | 1 |
| Submitted in the correct Moodle activity location. | 1 |
| Included the required title, student name, and course details. | 1 |
| Stayed within the required length or time limit. | 1 |

Group total: 4

### Group 2: Research Focus

Group description:

The submission has a clear research question, relevant scope, and a focused argument.

Items:

| Item definition | Score |
| --- | ---: |
| States a clear research question or problem. | 2 |
| Explains why the topic is relevant to the course. | 1.5 |
| Defines the scope clearly enough for a short brief. | 1.5 |
| Maintains focus on the stated question throughout the response. | 2 |

Group total: 7

### Group 3: Evidence and Analysis

Group description:

The submission uses credible evidence and explains how that evidence supports the response.

Items:

| Item definition | Score |
| --- | ---: |
| Uses at least two relevant course sources or external sources. | 2 |
| Summarises evidence accurately without copying large sections. | 1.5 |
| Explains how the evidence supports the main point. | 2 |
| Identifies at least one limitation, uncertainty, or counterpoint. | 1.5 |

Group total: 7

### Group 4: Communication

Group description:

The submission is readable, organised, and suitable for an academic audience.

Items:

| Item definition | Score |
| --- | ---: |
| Uses clear paragraphs, headings, or other readable structure. | 1 |
| Uses an appropriate academic tone with minimal errors affecting meaning. | 1 |

Group total: 2

## Benchmarks

Add these in the Benchmark field for each group. These are teacher-only guidance notes shown from the benchmark control.

### Benchmark for Group 1: Task Completion

Full credit means the learner submitted the correct work, in the correct place, by the due date, with all required identifying details. Do not award checklist items for work that is only partially submitted, uploaded to the wrong place, or missing required identifying information.

### Benchmark for Group 2: Research Focus

Full credit means the brief has a specific research question, a clear connection to the course, and a scope narrow enough for the assignment. A vague topic, broad theme, or unfocused summary should lose the relevant items even if the writing is otherwise competent.

### Benchmark for Group 3: Evidence and Analysis

Full credit means the learner uses relevant evidence and explains it. Award evidence items only when the source is identifiable and connected to the argument. Award analysis items only when the learner explains why the evidence matters, not just that the evidence exists.

### Benchmark for Group 4: Communication

Full credit means the submission is easy to read and uses an academic tone. Minor spelling or grammar issues are acceptable if meaning is clear. Do not award the tone item if informal language, unclear wording, or frequent errors make the brief difficult to assess.

## Test Grading Profiles

Use these fake learner outcomes to test scoring and validation.

| Profile | Checked items | Expected score |
| --- | --- | ---: |
| Excellent | Check every item in every group. | 20/20 |
| Solid pass | Group 1 all; Group 2 first 3; Group 3 first 3; Group 4 all. | 16/20 |
| Borderline | Group 1 first 3; Group 2 first 2; Group 3 first 2; Group 4 first item only. | 10/20 |
| Incomplete | Group 1 first item only. | 1/20 |
| Validation failure | Check several items but leave item and group remarks empty. | Save should fail when required-comment options are enabled. |

## Sample Remarks

Use these while grading to test item remarks and group remarks.

Excellent:

- Item remark: Strong evidence and clear explanation. This item is fully met.
- Marker comment: This group meets the benchmark with no major concerns.

Solid pass:

- Item remark: Good work, but the scope could be tighter in the final section.
- Marker comment: Most benchmarks are met. One area needs more detail or clearer evidence.

Borderline:

- Item remark: The submission shows a basic attempt but needs more specific explanation.
- Marker comment: Some checklist items are met, but the group does not yet show consistent achievement.

Incomplete:

- Item remark: Submission was received, but the required work is mostly missing.
- Marker comment: This group needs resubmission or substantial revision before it can meet the benchmark.
