M.gradingform_checklisteditor = M.gradingform_checklisteditor|| {'templates' : {}, 'name' : null, 'Y' : null};

/**
 * This function is called for each checklisteditor on page.
 */
M.gradingform_checklisteditor.init = function(Y, options) {
    M.gradingform_checklisteditor.name = options.name;
    M.gradingform_checklisteditor.Y = Y;
    M.gradingform_checklisteditor.templates[options.name] = {
        'group' : options.grouptemplate,
        'item'  : options.itemtemplate
    };

    M.gradingform_checklisteditor.disablealleditors();
    Y.on('click', M.gradingform_checklisteditor.clickanywhere, 'body', null);
    YUI().use('event-touch', function (Y) {
        Y.one('body').on('touchstart', M.gradingform_checklisteditor.clickanywhere);
        Y.one('body').on('touchend', M.gradingform_checklisteditor.clickanywhere);
    });

    //Event handler for submit buttons
    Y.one('#checklist-' + options.name).delegate('click', M.gradingform_checklisteditor.buttonclick, 'input[type=submit]');
    Y.one('#checklist-' + options.name).delegate('key', M.gradingform_checklisteditor.handlekey, 'press:13', 'input[type=text]');
};

// switches all input text elements to non-edit mode
M.gradingform_checklisteditor.disablealleditors = function() {
    var Y = M.gradingform_checklisteditor.Y;
    var name = M.gradingform_checklisteditor.name;

    Y.all('#checklist-' + name + ' .item').each( function(node) {M.gradingform_checklisteditor.editmode(node, false)} );
    Y.all('#checklist-' + name + ' .description').each( function(node) {M.gradingform_checklisteditor.editmode(node, false)} );
};

M.gradingform_checklisteditor.handlekey = function(e) {
    e.preventDefault();
    M.gradingform_checklisteditor.disablealleditors();
};

// function invoked on each click on the page. If item and/or group description is clicked
// it switches this element to edit mode. If checklist button is clicked it does nothing so the 'buttonclick'
// function is invoked
M.gradingform_checklisteditor.clickanywhere = function(e) {
    if (e.type == 'touchstart') {
        return;
    }
    var el = e.target;
    // if clicked on button - disablecurrenteditor, continue
    if (el.get('tagName') == 'INPUT' && el.get('type') == 'submit') {
        return;
    }
    // else if clicked on item and this item is not enabled - enable it
    // or if clicked on description and this description is not enabled - enable it
    var focustb = false;
    while (el && !(el.hasClass('item') || el.hasClass('description'))) {
        if (el.hasClass('score')) {
            focustb = true;
        }
        el = el.get('parentNode');
    }
    if (el) {
        if (el.one('input[type=text]').hasClass('hiddenelement')) {
            M.gradingform_checklisteditor.disablealleditors();
            M.gradingform_checklisteditor.editmode(el, true, focustb);
        }
        return;
    }
    // else disablecurrenteditor
    M.gradingform_checklisteditor.disablealleditors();
};

// switch the group description or item to edit mode or switch back
M.gradingform_checklisteditor.editmode = function(el, editmode, focustb) {
    var ta = el.one('input[type=text]');
    if (!editmode && ta.hasClass('hiddenelement')) return;
    if (editmode && !ta.hasClass('hiddenelement')) return;
    var pseudotablink = '<input type="text" size="1" class="pseudotablink"/>',
        taplain = ta.get('parentNode').one('.plainvalue'),
        tbplain = null,
        tb = el.one('.score input[type=text]');
    // add 'plainvalue' next to textbox for description/definition and next to input text field for score (if applicable)
    if (!taplain) {
        ta.get('parentNode').append('<div class="plainvalue">' + pseudotablink + '<span class="textvalue">&nbsp;</span></div>');
        taplain = ta.get('parentNode').one('.plainvalue');
        taplain.one('.pseudotablink').on('focus', M.gradingform_checklisteditor.clickanywhere);
        if (tb) {
            tb.get('parentNode').append('<span class="plainvalue">' + pseudotablink + '<span class="textvalue">&nbsp;</span></span>');
            tbplain = tb.get('parentNode').one('.plainvalue');
            tbplain.one('.pseudotablink').on('focus', M.gradingform_checklisteditor.clickanywhere);
        }
    }
    if (tb && !tbplain) tbplain = tb.get('parentNode').one('.plainvalue');
    if (!editmode) {
        // if we need to hide the input fields, copy their contents to plainvalue(s). If description/definition
        // is empty, display the default text ('Click to edit ...') and add/remove 'empty' CSS class to element
        var value = ta.get('value');
        if (value.length) {
            taplain.removeClass('empty');
        } else {
            value = (el.hasClass('item')) ? M.str.gradingform_checklist.itemempty : M.str.gradingform_checklist.groupempty;
            taplain.addClass('empty');
        }
        taplain.one('.textvalue').set('innerHTML', value);
        if (tb) {
            tbplain.one('.textvalue').set('innerHTML', tb.get('value'));
        }
        // hide/display textarea, textbox and plaintexts
        taplain.removeClass('hiddenelement');
        ta.addClass('hiddenelement');
        if (tb) {
            tbplain.removeClass('hiddenelement');
            tb.addClass('hiddenelement');
        }
    } else {
        // if we need to show the input fields, set the width/height for textarea so it fills the cell
//        try {
//            var width = parseFloat(ta.get('parentNode').getComputedStyle('width')),
//                height
//            if (el.hasClass('item')) height = parseFloat(el.getComputedStyle('height')) - parseFloat(el.one('.score').getComputedStyle('height'))
//            else height = parseFloat(ta.get('parentNode').getComputedStyle('height'))
//            ta.setStyle('width', Math.max(width,50)+'px')
//            ta.setStyle('height', Math.max(height,20)+'px')
//        }
//        catch (err) {
//            // this browser do not support 'computedStyle', leave the default size of the textbox
//        }
        // hide/display textarea, textbox and plaintexts
        taplain.addClass('hiddenelement');
        ta.removeClass('hiddenelement');
        if (tb) {
            tbplain.addClass('hiddenelement');
            tb.removeClass('hiddenelement');
        }
    }
    // focus the proper input field in edit mode
    if (editmode) {
        if (tb && focustb)  {
            tb.focus();
        } else {
            ta.focus();
        }
    }
};

// handler for clicking on submit buttons within checklisteditor element. Adds/deletes/rearranges groups and/or items on client side
M.gradingform_checklisteditor.buttonclick = function(e, confirmed) {
    var Y = M.gradingform_checklisteditor.Y;
    var name = M.gradingform_checklisteditor.name;
    if (e.target.get('type') != 'submit') {
        return;
    }
    M.gradingform_checklisteditor.disablealleditors();
    var chunks = e.target.get('id').split('-'),
        action = chunks[chunks.length-1];
    if (chunks[0] != name || chunks[1] != 'groups') {
        return;
    }
    var elements_str;
    if (chunks.length > 4 || action == 'additem') {
        elements_str = '#checklist-' + name + ' #' + name + '-groups-' + chunks[2] + '-items .item';
    } else {
        elements_str = '#checklist-' + name + ' .group';
    }
    // prepare the id of the next inserted item or group
    if (action == 'addgroup' || action == 'additem') {
        var newid = M.gradingform_checklisteditor.calculatenewid('#checklist-' + name + ' .group');
        var newlevid = M.gradingform_checklisteditor.calculatenewid('#checklist-' + name + ' .item');
    }
    var dialog_options = {
        'scope' : this,
        'callbackargs' : [e, true],
        'callback' : M.gradingform_checklisteditor.buttonclick
    };
    if (chunks.length == 3 && action == 'addgroup') {
        // ADD NEW GROUP
        var newscore= 1, levidx = 0;
        var parentel = Y.one('#' + name + '-groups');

        var itemsstr = '';
        for (levidx = 0; levidx < 3; levidx++) {
            itemsstr += M.gradingform_checklisteditor.templates[name]['item'].replace(/\{ITEM-id\}/g, 'NEWID' + (newlevid + levidx)).replace(/\{ITEM-score\}/g, newscore);
        }
        var newgroup = M.gradingform_checklisteditor.templates[name]['group'].replace(/\{ITEMS\}/, itemsstr);
        parentel.append(newgroup.replace(/\{GROUP-id\}/g, 'NEWID' + newid).replace(/\{.+?\}/g, ''));
        M.gradingform_checklisteditor.assignclasses('#checklist-' + name + ' #' + name + '-groups-NEWID' + newid + '-items .item');
        M.gradingform_checklisteditor.disablealleditors();
        M.gradingform_checklisteditor.assignclasses(elements_str);
        M.gradingform_checklisteditor.editmode(Y.one('#checklist-' + name + ' #' + name + '-groups-NEWID' + newid + '-description'),true);
    } else if (chunks.length == 5 && action == 'additem') {
        // ADD NEW ITEM
        var newscore = 1;
        var parent = Y.one('#' + name + '-groups-' + chunks[2] + '-items');
        var newitem = M.gradingform_checklisteditor.templates[name]['item'].
            replace(/\{GROUP-id\}/g, chunks[2]).replace(/\{ITEM-id\}/g, 'NEWID' + newlevid).replace(/\{ITEM-score\}/g, newscore).replace(/\{.+?\}/g, '');
        parent.append(newitem);
        M.gradingform_checklisteditor.disablealleditors();
        M.gradingform_checklisteditor.assignclasses(elements_str);
        M.gradingform_checklisteditor.editmode(parent.all('.item').item(parent.all('.item').size()-1), true);
    } else if (chunks.length == 4 && action == 'moveup') {
        // MOVE GROUP UP
        var el = Y.one('#' + name + '-groups-' + chunks[2]);
        var previous = el.previous();
        if (previous) {
            el.get('parentNode').insertBefore(el, previous);
        }
        M.gradingform_checklisteditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'movedown') {
        // MOVE GROUP DOWN
        var el = Y.one('#' + name + '-groups-' + chunks[2]);
        if (el.next()) el.get('parentNode').insertBefore(el.next(), el);
        M.gradingform_checklisteditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'delete') {
        // DELETE GROUP
        if (confirmed) {
            Y.one('#' + name + '-groups-' + chunks[2]).remove();
            M.gradingform_checklisteditor.assignclasses(elements_str)
        } else {
            dialog_options['message'] = M.str.gradingform_checklist.confirmdeletegroup;
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else if (chunks.length == 6 && action == 'delete') {
        // DELETE ITEM
        if (confirmed) {
            Y.one('#' + name + '-groups-' + chunks[2] + '-' + chunks[3] + '-' + chunks[4]).remove();
            M.gradingform_checklisteditor.assignclasses(elements_str)
        } else {
            dialog_options['message'] = M.str.gradingform_checklist.confirmdeleteitem;
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else {
        // unknown action
        return;
    }
    e.preventDefault();
};

// properly set classes (first/last/odd/even), item width and/or group sortorder for elements Y.all(elements_str)
M.gradingform_checklisteditor.assignclasses = function (elements_str) {
    var elements = M.gradingform_checklisteditor.Y.all(elements_str);
    for (var i=0;i<elements.size();i++) {
        elements.item(i).removeClass('first').removeClass('last').removeClass('even').removeClass('odd').
            addClass(((i%2)?'odd':'even') + ((i==0)?' first':'') + ((i==elements.size()-1)?' last':''));
        elements.item(i).all('input[type=hidden]').each(
            function(node) {if (node.get('name').match(/sortorder/)) node.set('value', i)}
        );
    }
};

// returns unique id for the next added element, it should not be equal to any of Y.all(elements_str) ids
M.gradingform_checklisteditor.calculatenewid = function (elements_str) {
    var newid = 1;
    M.gradingform_checklisteditor.Y.all(elements_str).each( function(node) {
        var idchunks = node.get('id').split('-'), id = idchunks.pop();
        if (id.match(/^NEWID(\d+)$/)) newid = Math.max(newid, parseInt(id.substring(5)) + 1);
    } );
    return newid
};
