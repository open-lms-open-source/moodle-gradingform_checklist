# Checklist 4.5.9 patch verification

Build: `2026092800`. This patch removes the settings companion information banner
from the Advanced grading page. It adds no detection logic and changes no grading,
import, settings, privacy, or database schema behaviour. The companion remains
version 1.0.2.

The regression test covers administrator output for defined and undefined forms,
with import/download features enabled and disabled. It must reject the banner
and companion repository link while preserving the configured actions. The test
was confirmed to fail against 4.5.8 because the banner was present.

Before publishing this patch:

- Run PHP lint, diff checks and the Checklist PHPUnit suite on Moodle 4.5 and 5.2.
- Verify the version-only upgrade from 4.5.8 on both sites.
- Visually check Advanced grading controls and the absence of the banner/empty gap.
- Confirm the administration settings page remains available.
- Require the existing required-check and browser workflows to pass on the release commit.
- Validate the tagged ZIP, version identity and SHA-256 checksum.

Record final results and workflow links in the 4.5.9 GitHub release notes.
Existing PHPUnit 11 doc-comment metadata deprecations are outside this patch.
The broader school staging, upgrade/rollback and accessibility acceptance recorded
in `release-evidence-4.5.8.md` remains separate; this patch does not complete it.
