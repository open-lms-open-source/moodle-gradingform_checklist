M.gradingform_checklisteditor = {'templates' : {}, 'eventhandler' : null, 'name' : null, 'Y' : null};

/**
 * This function is called for each checklisteditor on page.
 */
M.gradingform_checklisteditor.init = function(Y, options) {
    M.gradingform_checklisteditor.name = options.name
    M.gradingform_checklisteditor.Y = Y
    M.gradingform_checklisteditor.templates[options.name] = {
        'group' : options.grouptemplate,
        'item'  : options.itemtemplate
    }
};
