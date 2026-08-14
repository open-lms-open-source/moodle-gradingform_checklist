# Release checklist

Use this checklist when preparing a Moodle Marketplace or tester package.

## Supported release

- Confirm the target Moodle branch is maintained and compatible with
  `version.php`.
- Confirm the package version and the final savepoint in `db/upgrade.php` are
  identical.
- Review `upgrade.txt` and verify both fresh installation and upgrade paths.

## Clean package

Create a package from Git rather than zipping the working directory:

```bash
git archive --format=zip --prefix=checklist/ HEAD -o checklist.zip
```

The repository attributes exclude source maps, `.DS_Store`, Git packaging
metadata, `.gitignore`, and candidate-only template assets. The package must
contain the supported DOCX template under `docs/` and must not contain `.git` or
editor-generated files.

## Verification

- Run PHP lint, Moodle PHPCS (`moodle-extra`), JavaScript, CSS, PHPDoc,
  savepoint, Mustache, and Grunt checks.
- Run all plugin PHPUnit tests and the checklist Behat feature with debugging
  enabled.
- Test fresh install and upgrade on MySQL/MariaDB and PostgreSQL.
- Manually test benchmark focus placement, Escape/close behaviour, focus
  restoration, responsive panel/modal layout, keyboard navigation, and RTL.
- Verify backup/restore, privacy export/delete, import validation, and grader
  capability boundaries.

## Asset licensing

`checklist-import-template.docx` was added by the repository maintainer in the
checklist import commits and is treated as an original project asset released
under the plugin's GPL-3.0-or-later licence. The release maintainer must confirm
that no third-party text, images, fonts, or other copyrighted material is
embedded before submitting the package.

`checklist-import-template-candidate.docx` is a development candidate and is
excluded from Marketplace packages.

## Third-party library review

`amd/build/grades/grader/gradingpanel.min.js` is generated first-party AMD output
from `amd/src/grades/grader/gradingpanel.js`. No bundled third-party library was
identified in the current package, so `thirdpartylibs.xml` is not required unless
a future release adds third-party code.
