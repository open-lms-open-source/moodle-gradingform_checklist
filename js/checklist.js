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
    while (el && !el.hasClass('item')) {
        el = el.get('parentNode');
    }
    if (!el) {
        return;
    }
    e.preventDefault();

    var chb = el.one('input[type=checkbox]');
    if (!chb.get('checked')) {
        chb.set('checked', true)
        el.addClass('checked');
    } else {
        el.removeClass('checked');
        chb.set('checked', false);
    }
}