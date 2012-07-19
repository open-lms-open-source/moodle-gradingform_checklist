M.gradingform_checklist = M.gradingform_checklist || {};

/**
 * This function is called for each checklist on page.
 */
M.gradingform_checklist.init = function(Y, options) {
    Y.on('click', M.gradingform_checklist.itemclick, '#checklist-'+options.name+' .item', null, Y, options.name);
    Y.all('#checklist-'+options.name+' .item').each(function (node) {
        if (node.one('input[type=checkbox]').get('checked')) {
            node.addClass('checked');
        }
    });
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
            var score   = parseFloat(item.one('.scorevalue').get('innerHTML'));

            grouptotal += score;
            if (checked) {
                groupscored += score;
            }
        });

        overalltotal += grouptotal;
        overallscored += groupscored;

        group.one('.pointstotals .scoredpoints').set('innerHTML', groupscored);
    });

    checklist.one('> .pointstotals .scoredpoints').set('innerHTML', overallscored);
};