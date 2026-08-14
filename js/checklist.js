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

M.gradingform_checklist = M.gradingform_checklist || {};

/**
 * This function is called for each checklist on page.
 */
M.gradingform_checklist.init = function(Y, options) {
    M.gradingform_checklist.Y = Y;
    Y.on('click', M.gradingform_checklist.itemclick, '#checklist-'+options.name+' .item', null, Y, options.name);
    Y.one('#checklist-' + options.name).delegate('click', M.gradingform_checklist.bulkcheckclick, '.bulkcheckcontrols button');
    Y.one('#checklist-' + options.name).delegate('click', M.gradingform_checklist.benchmarkclick, '.benchmark-toggle');
    Y.all('#checklist-'+options.name+' .item').each(function (node) {
        if (node.one('input[type=checkbox]').get('checked')) {
            node.addClass('checked');
        }
    });
    M.gradingform_checklist.updatebulkcheckbutton(Y.one('#checklist-' + options.name));
};

M.gradingform_checklist.bulkcheckclick = function(e) {
    e.preventDefault();

    var Y = M.gradingform_checklist.Y;
    var button = e.currentTarget;
    var checklist = button.ancestor('.gradingform_checklist');
    if (!checklist) {
        return;
    }

    var checked = button.getAttribute('data-action') == 'tickall';
    checklist.all('.item input[type=checkbox]').each(function(checkbox) {
        checkbox.set('checked', checked);
        var item = checkbox.ancestor('.item');
        if (checked) {
            item.addClass('checked');
        } else {
            item.removeClass('checked');
        }
    });

    M.gradingform_checklist.recalculatetotals(Y, checklist.get('id').replace(/^checklist-/, ''));
    M.gradingform_checklist.updatebulkcheckbutton(checklist);
};

M.gradingform_checklist.itemclick = function(e, Y, name) {
    var el = e.target;

    if (el.test('textarea')) {
        return;
    }

    // check to see if the actual checkbox was checked and get it's new state if so
    var newcheckboxstate = null;
    if (el.hasAttribute('type') && el.get('type') == 'checkbox') {
        newcheckboxstate = el.get('checked');
    }

    // get the parent 'item' div
    if (!el.hasClass('item')) {
        el = el.ancestor('.item', false, '.group');
    }

    if (!el) {
        return;
    }

    // set the checkbox status and the item class
    var chb = el.one('input[type=checkbox]');
    if (newcheckboxstate || (newcheckboxstate == null && !chb.get('checked'))) {
        chb.set('checked', true);
        el.addClass('checked');
    } else {
        el.removeClass('checked');
        chb.set('checked', false);
    }

    // recalc the scores
    M.gradingform_checklist.recalculatetotals(Y, name);
    M.gradingform_checklist.updatebulkcheckbutton(Y.one('#checklist-' + name));
};

M.gradingform_checklist.updatebulkcheckbutton = function(checklist) {
    if (!checklist) {
        return;
    }

    var buttons = checklist.all('.bulkcheckcontrols button');
    if (!buttons.size()) {
        return;
    }

    var allchecked = true;
    checklist.all('.item input[type=checkbox]').each(function(checkbox) {
        if (!checkbox.get('checked')) {
            allchecked = false;
        }
    });

    if (allchecked) {
        buttons.setAttribute('data-action', 'untickall');
        buttons.set('text', M.str.gradingform_checklist.untickall);
    } else {
        buttons.setAttribute('data-action', 'tickall');
        buttons.set('text', M.str.gradingform_checklist.tickall);
    }
};

M.gradingform_checklist.recalculatetotals = function(Y, name) {
    var checklist = Y.one('#checklist-' + name);
    if (!checklist || !checklist.hasClass('evaluate')) {
        return;
    }

    var overalltotal = 0;
    var overallscored = 0;

    var checklistgroups = checklist.all('.group');

    // iterate through all groups
    checklistgroups.each(function(group) {
        var grouptotal = 0;
        var groupscored = 0;

        var groupitems = group.all('.item');

        // iterate through all group items
        groupitems.each(function(item) {
            var checked = item.one('input[type=checkbox]').get('checked');
            var scorevalue = item.one('.scorevalue');
            if (!scorevalue) {
                return;
            }
            var score = parseFloat(scorevalue.get('innerHTML'));

            grouptotal += score;
            if (checked) {
                groupscored += score;
            }
        });

        overalltotal += grouptotal;
        overallscored += groupscored;

        var grouppoints = group.one('.pointstotals .scoredpoints');
        if (grouppoints) {
            grouppoints.set('innerHTML', groupscored);
        }
    });

    var overallpoints = checklist.one('> .pointstotals .scoredpoints');
    if (overallpoints) {
        overallpoints.set('innerHTML', overallscored);
    }
};


M.gradingform_checklist.benchmarkclick = function(e) {
    e.preventDefault();
    var Y = M.gradingform_checklist.Y;
    var button = e.currentTarget;
    var benchmarkid = button.getAttribute('data-benchmark-id');
    var source = Y.one('[data-benchmark-content="' + benchmarkid + '"]');
    if (!source) {
        return;
    }
    var title = source.one('.benchmark-content-title') ? source.one('.benchmark-content-title').get('text') : M.str.gradingform_checklist.benchmark;
    var body = source.one('.benchmark-content-body') ? source.one('.benchmark-content-body').get('innerHTML') : source.get('innerHTML');
    Y.all('.benchmark-toggle').setAttribute('aria-expanded', 'false');
    if (M.gradingform_checklist.showbenchmark(Y, title, body, benchmarkid)) {
        button.setAttribute('aria-expanded', 'true');
    }
};

M.gradingform_checklist.showbenchmark = function(Y, title, body, benchmarkid) {
    var usemodal = M.gradingform_checklist.shouldusemodal(Y);
    if (usemodal) {
        var modal = Y.one('.gradingform_checklist-benchmark-modal');
        if (modal && !modal.hasClass('hiddenelement') && modal.getAttribute('data-current-benchmark') === benchmarkid) {
            modal.addClass('hiddenelement');
            return false;
        }
        Y.one('body').removeClass('gradingform_checklist-benchmark-panel-open');
        M.gradingform_checklist.showbenchmarkmodal(Y, title, body, benchmarkid);
    } else {
        var panel = Y.one('.gradingform_checklist-benchmark-panel');
        if (panel && panel.hasClass('open') && panel.getAttribute('data-current-benchmark') === benchmarkid) {
            panel.removeClass('open');
            Y.one('body').removeClass('gradingform_checklist-benchmark-panel-open');
            return false;
        }
        M.gradingform_checklist.showbenchmarkpanel(Y, title, body, benchmarkid);
    }
    return true;
};

M.gradingform_checklist.shouldusemodal = function(Y) {
    if (window.innerWidth < 1100) {
        return true;
    }
    return !!Y.one('[data-region="pdf"]') || !!Y.one('.assignfeedback_editpdf_widget') || !!Y.one('.drawingregion') ||
        !!Y.one('[data-region="review-panel"]');
};

M.gradingform_checklist.showbenchmarkpanel = function(Y, title, body, benchmarkid) {
    var panel = Y.one('.gradingform_checklist-benchmark-panel');
    if (!panel) {
        Y.one('body').append('<aside class="gradingform_checklist-benchmark-panel" role="complementary" aria-live="polite">' +
            '<button type="button" class="benchmark-panel-close" aria-label="' + M.str.gradingform_checklist.closebenchmark + '">&times;</button>' +
            '<h5 class="benchmark-display-title"></h5><div class="benchmark-panel-body"></div></aside>');
        panel = Y.one('.gradingform_checklist-benchmark-panel');
        panel.one('.benchmark-panel-close').on('click', function() {
            panel.removeClass('open');
            Y.all('.benchmark-toggle').setAttribute('aria-expanded', 'false');
            Y.one('body').removeClass('gradingform_checklist-benchmark-panel-open');
        });
    }
    panel.setAttribute('data-current-benchmark', benchmarkid);
    panel.one('.benchmark-display-title').set('text', title);
    panel.one('.benchmark-panel-body').set('innerHTML', body);
    panel.addClass('open');
    Y.one('body').addClass('gradingform_checklist-benchmark-panel-open');
    var focusclosebutton = function() {
        var closebutton = panel.one('.benchmark-panel-close');
        if (closebutton) {
            closebutton.getDOMNode().focus();
        }
    };
    focusclosebutton();
    window.setTimeout(focusclosebutton, 100);
};

M.gradingform_checklist.showbenchmarkmodal = function(Y, title, body, benchmarkid) {
    var modal = Y.one('.gradingform_checklist-benchmark-modal');
    if (!modal) {
        Y.one('body').append('<div class="gradingform_checklist-benchmark-modal hiddenelement" role="dialog" aria-modal="true">' +
            '<div class="benchmark-modal-dialog"><button type="button" class="benchmark-modal-close" aria-label="' + M.str.gradingform_checklist.closebenchmark + '">&times;</button>' +
            '<h5 class="benchmark-display-title"></h5><div class="benchmark-modal-body"></div></div></div>');
        modal = Y.one('.gradingform_checklist-benchmark-modal');
        modal.one('.benchmark-modal-close').on('click', function() {
            modal.addClass('hiddenelement');
            Y.all('.benchmark-toggle').setAttribute('aria-expanded', 'false');
            Y.one('body').removeClass('gradingform_checklist-benchmark-panel-open');
        });
    }
    modal.setAttribute('data-current-benchmark', benchmarkid);
    modal.one('.benchmark-display-title').set('text', title);
    modal.one('.benchmark-modal-body').set('innerHTML', body);
    modal.removeClass('hiddenelement');
    var focusclosebutton = function() {
        var closebutton = modal.one('.benchmark-modal-close');
        if (closebutton) {
            closebutton.getDOMNode().focus();
        }
    };
    focusclosebutton();
    window.setTimeout(focusclosebutton, 100);
};
