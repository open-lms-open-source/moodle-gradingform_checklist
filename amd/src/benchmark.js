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
 * Benchmark display controls for gradingform_checklist.
 *
 * @module     gradingform_checklist/benchmark
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import jQuery from 'jquery';

let activeButton = null;
let activeTarget = null;

const escapeAttribute = value => String(value).replace(/[&<>"']/g, character => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
}[character]));

/**
 * Closes the active benchmark display.
 *
 * @param {Object} root Root jQuery collection.
 */
const closeBenchmark = root => {
    if (!activeTarget) {
        return;
    }
    activeTarget.addClass('hiddenelement').removeClass('open').attr('aria-hidden', 'true');
    activeTarget.find('.benchmark-modal-close, .benchmark-panel-close').attr('tabindex', '-1');
    activeTarget.find('.benchmark-modal-body, .benchmark-panel-body').empty();
    root.find('.benchmark-toggle').attr('aria-expanded', 'false');
    jQuery('body').removeClass('gradingform_checklist-benchmark-panel-open');
    if (activeButton && activeButton.length) {
        activeButton.get(0).focus();
    }
    activeButton = null;
    activeTarget = null;
};

/**
 * Returns the shared display target, creating it under body when needed.
 *
 * @param {Boolean} constrained Whether to use the modal target.
 * @param {String} closeLabelAttribute Escaped close label.
 * @param {Object} root Root jQuery collection.
 * @return {Object}
 */
const getDisplayTarget = (constrained, closeLabelAttribute, root) => {
    const body = jQuery('body');
    const container = root.length && root.get(0) !== document ? root.first() : body;
    let target = container.find('.gradingform_checklist-benchmark-' + (constrained ? 'modal' : 'panel')).first();
    if (target.length) {
        return target;
    }

    if (constrained) {
        const modal = '<div class="gradingform_checklist-benchmark-modal hiddenelement" ' +
            'role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">' +
            '<div class="benchmark-modal-dialog"><button type="button" ' +
            'class="benchmark-modal-close" aria-label="' + closeLabelAttribute + '">' +
            '<span aria-hidden="true">&times;</span><span class="sr-only">' + closeLabelAttribute + '</span></button>' +
            '<h5 class="benchmark-display-title"></h5><div class="benchmark-modal-body"></div>' +
            '</div></div>';
        container.append(modal);
        target = container.find('.gradingform_checklist-benchmark-modal').first();
    } else {
        const panel = '<aside class="gradingform_checklist-benchmark-panel hiddenelement" ' +
            'role="complementary" aria-hidden="true" tabindex="-1">' +
            '<button type="button" class="benchmark-panel-close" aria-label="' + closeLabelAttribute + '">' +
            '<span aria-hidden="true">&times;</span><span class="sr-only">' + closeLabelAttribute + '</span></button>' +
            '<h5 class="benchmark-display-title"></h5><div class="benchmark-panel-body"></div></aside>';
        container.append(panel);
        target = container.find('.gradingform_checklist-benchmark-panel').first();
    }

    target.find('.benchmark-modal-close, .benchmark-panel-close').on('click', () => closeBenchmark(root));
    target.on('keydown.gradingformChecklistBenchmark', event => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeBenchmark(root);
        }
    });

    return target;
};

/**
 * Finds the hidden benchmark content for a clicked button.
 *
 * @param {Object} button Button jQuery collection.
 * @param {Object} root Root jQuery collection.
 * @param {String|Number} benchmarkId Benchmark identifier.
 * @return {Object}
 */
const findSource = (button, root, benchmarkId) => {
    const selector = '[data-benchmark-content="' + benchmarkId + '"]';
    let source = button.closest('.benchmark-control').find(selector).first();
    if (!source.length) {
        source = button.closest('form, .gradingform_checklist, .gradingform_checklist-description').find(selector).first();
    }
    if (!source.length) {
        source = root.find(selector).first();
    }
    if (!source.length) {
        source = jQuery(document).find(selector).first();
    }
    return source;
};

/**
 * Returns a clean title from the clicked benchmark button.
 *
 * @param {Object} button Button jQuery collection.
 * @return {String}
 */
const getButtonTitle = button => {
    const clone = button.clone();
    clone.find('i, svg, .icon, [aria-hidden="true"]').remove();
    return clone.text().replace(/\s+/g, ' ').trim();
};

/**
 * Initializes benchmark panel/modal controls inside a checklist root.
 *
 * @param {String|null} rootSelector Root selector, or null to bind to the document.
 * @param {String} closeLabel Accessible label for close controls.
 */
export const initBenchmarkDisplay = (rootSelector, closeLabel) => {
    const root = rootSelector ? jQuery(rootSelector) : jQuery(document);
    if (!root.length) {
        return;
    }

    const closeLabelAttribute = escapeAttribute(closeLabel || 'Close');
    root.off('click.gradingformChecklistBenchmark', '.benchmark-toggle');
    root.on('click.gradingformChecklistBenchmark', '.benchmark-toggle', function(event) {
        event.preventDefault();
        event.stopPropagation();

        const button = jQuery(this);
        const benchmarkId = button.data('benchmark-id');
        const source = findSource(button, root, benchmarkId);
        if (!source.length) {
            return;
        }

        const title = getButtonTitle(button) || source.find('.benchmark-content-title').text();
        const body = source.find('.benchmark-content-body').html();
        const constrained = window.innerWidth < 1100 ||
            jQuery('[data-region="pdf"], .assignfeedback_editpdf_widget, .drawingregion, [data-region="review-panel"]').length > 0;
        const target = getDisplayTarget(constrained, closeLabelAttribute, root);
        const currentBenchmark = String(benchmarkId);
        const isOpen = constrained ? !target.hasClass('hiddenelement') : target.hasClass('open');

        if (isOpen && target.attr('data-current-benchmark') === currentBenchmark) {
            closeBenchmark(root);
            return;
        }

        if (activeTarget && activeTarget[0] !== target[0]) {
            closeBenchmark(root);
        }
        root.find('.benchmark-toggle').attr('aria-expanded', 'false');
        button.attr('aria-expanded', 'true');
        activeButton = button;
        activeTarget = target;
        target.attr('data-current-benchmark', currentBenchmark);
        target.attr('aria-hidden', 'false');
        target.find('.benchmark-modal-close, .benchmark-panel-close').removeAttr('tabindex');

        if (constrained) {
            jQuery('body').removeClass('gradingform_checklist-benchmark-panel-open');
            target.find('.benchmark-display-title').text(title);
            target.find('.benchmark-modal-body').html(body);
            target.removeClass('hiddenelement');
        } else {
            target.find('.benchmark-display-title').text(title);
            target.find('.benchmark-panel-body').html(body);
            target.removeClass('hiddenelement').addClass('open');
            jQuery('body').addClass('gradingform_checklist-benchmark-panel-open');
        }

        const focusBenchmarkClose = () => window.setTimeout(() => {
            const closeButton = target.find('.benchmark-modal-close, .benchmark-panel-close').get(0);
            if (closeButton && target.attr('aria-hidden') === 'false') {
                closeButton.focus();
            }
        }, 0);
        focusBenchmarkClose();
        window.requestAnimationFrame(focusBenchmarkClose);
        window.setTimeout(focusBenchmarkClose, 100);
        window.setTimeout(focusBenchmarkClose, 300);
        window.setTimeout(focusBenchmarkClose, 500);
    });
};
