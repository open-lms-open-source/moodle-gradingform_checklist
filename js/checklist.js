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
}