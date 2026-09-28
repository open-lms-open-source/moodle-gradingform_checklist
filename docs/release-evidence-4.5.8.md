# Enhanced Checklist 4.5.8 release evidence

This document is the sign-off record for plugin build `2026092300`. Replace every
`PENDING` value with evidence from the final tagged commit. The release must not
be approved while any required result is pending or failed.

## Immutable release identity

| Evidence | Result |
| --- | --- |
| Commit SHA | PENDING |
| Tag (`v4.5.8`) | PENDING |
| Release ZIP SHA-256 | PENDING |
| GitHub required checks URL | PENDING |
| GitHub browser checks URL | PENDING |

## Automated compatibility

Record the required workflow URL and result for Moodle 4.5, 5.0, 5.1, and 5.2.
The matrix must cover PostgreSQL and MariaDB at the minimum supported PHP and
PostgreSQL at the maximum supported PHP. Chrome must pass for every Moodle
version; Firefox must pass for Moodle 4.5 and 5.2.

| Moodle | Install / PHPUnit | Chrome | Firefox boundary |
| --- | --- | --- | --- |
| 4.5 | PENDING | PENDING | PENDING |
| 5.0 | PENDING | PENDING | N/A |
| 5.1 | PENDING | PENDING | N/A |
| 5.2 | PENDING | PENDING | PENDING |

## Upgrade and data integrity

- [ ] Fresh ZIP installation on PostgreSQL.
- [ ] Fresh ZIP installation on MariaDB.
- [ ] Upgrade a sanitised production clone from its deployed plugin version.
- [ ] Upgrade from tagged `v4.5.7`.
- [ ] Verify legacy benchmark text and files migrate without loss.
- [ ] Verify existing grades, remarks, required-comment settings, and observation dates.
- [ ] Verify backup/restore and privacy export/delete.
- [ ] Verify rollback by restoring the pre-upgrade database and `moodledata` backup.
- [ ] Confirm developer debugging produced no plugin notices, warnings, or deprecations.

## Functional and security acceptance

- [ ] Definition authoring and first/middle/last item movement.
- [ ] DOCX and JSON import, preview, confirmation, and invalid/oversized input rejection.
- [ ] Disabled web-service import and unauthorised/cross-context access rejection.
- [ ] Grading, regrading, notifications, student visibility, and required comments.
- [ ] Observation-date entry and display.
- [ ] Production-sized checklist/cohort smoke test with measurements attached.
- [ ] Confirm the settings page or document deployment of optional `local_checklistsettings`.

## WCAG 2.2 AA manual review

Test Chrome and Firefox at desktop width, 400% zoom/reflow, and a narrow mobile
viewport. Repeat the keyboard and layout checks in an RTL language.

- [ ] All controls have programmatic names and visible focus.
- [ ] Logical keyboard order; benchmark opens, closes with Escape, and restores focus.
- [ ] Dialog/panel content is announced and keyboard focus cannot become stranded.
- [ ] Text and controls meet contrast requirements; meaning is not colour-only.
- [ ] Content reflows without loss, overlap, or two-dimensional scrolling.
- [ ] Screen-reader smoke test completed for authoring and grading workflows.

## Approval

| Role | Name | Date | Decision |
| --- | --- | --- | --- |
| Moodle administrator | PENDING | PENDING | PENDING |
| Representative teacher | PENDING | PENDING | PENDING |

Production deployment is blocked unless both approvers record `APPROVED` and
all required checks above are complete.

## Pre-tag implementation verification

These results validate the working release candidate but do not replace the
tagged-commit evidence above:

- Moodle 4.5.11+, PHP 8.3, PostgreSQL 16 test-site upgrade: PASS.
- Moodle 4.5 PHPUnit: PASS — 76 tests, 560 assertions, `--fail-on-warning`.
- PHP syntax: PASS — 55 PHP files.
- XMLDB parse and deterministic preflight: PASS with maintainability/manual-test warnings.
- Local Behat: BLOCKED by unrelated test-site `mod_enhancedchoice` fatal error;
  the required GitHub browser matrix must supply the authoritative result.
