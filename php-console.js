
$(function() {
    var settings = $('#ed-settings'), output = $('#ed-output'),
    editor = $('#ed-editor'), aceeditor, loading = $('#loading');

    // editor
    aceeditor = ace.edit("ed-editor");
    var CodeMode = require("ace/mode/php").Mode;
    aceeditor.getSession().setMode(new CodeMode());
    aceeditor.getSession().setTabSize(4);

    $.getScript('ace/theme-'+phpConsole.theme+'.js', function() {
        aceeditor.setTheme("ace/theme/"+phpConsole.theme);
    });
    editor.css('font-size', phpConsole.fontsize);

    if (phpConsole.highlightactive == 1) {
        aceeditor.setHighlightActiveLine(true);
    } else {
        aceeditor.setHighlightActiveLine(false);
    }

    if (phpConsole.show_print_margin == 1) {
        aceeditor.setShowPrintMargin(true);
    } else {
        aceeditor.setShowPrintMargin(false);
    }

    if (phpConsole.soft_tab == 1) {
        aceeditor.getSession().setUseSoftTabs(true);
    } else {
        aceeditor.getSession().setUseSoftTabs(false);
    }

    if (phpConsole.highlight_selected_word == 1) {
        aceeditor.setHighlightSelectedWord(true);
    } else {
        aceeditor.setHighlightSelectedWord(false);
    }

    // toolbar
    $('#ed-settings-btn').button({
        icons: {
            primary: "ui-icon-gear",
            secondary: "ui-icon-triangle-1-s"
        },
        text: false
    }).click(function(e) {
        e.preventDefault();
        var link = $(this), linkPosition = link.position();

        if (link.find('.ui-button-icon-secondary').hasClass('ui-icon-triangle-1-s')) {
            settings.css({
                'left': (parseInt(link.css('width'))+linkPosition.left) - parseInt(settings.outerWidth()) + 'px',
                'top' : linkPosition.top + parseInt(link.css('height'))+5
            }).slideDown();
            link.find('.ui-button-icon-secondary').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-n');
        } else {
            settings.slideUp();
            link.find('.ui-button-icon-secondary').removeClass('ui-icon-triangle-1-n').addClass('ui-icon-triangle-1-s');
        }
    });

    $( "#actions" ).buttonset().find('label').click(function() {
        var button = $(this);
        button.parent().find('label').removeClass('ui-state-active');
        button.addClass('ui-state-active');
        switch(button.attr('for')) {
            case 'ed-showoutput-btn':
                openOutput();
                break;
            case 'ed-showeditor-btn':
                openEditor();
                break;
        }
    });
    $('#ed-submitcode-btn').button().click(function() {
        submitCode();
        $( "#actions" ).find('label').removeClass('ui-state-active');
        $( "#actions" ).find('label:last').addClass('ui-state-active');
    });
    $('#ed-savesettings-btn').button().click(function(e) {
        e.preventDefault();
        loading.css('display', 'inline-block');
        $.post('index.php', settings.find('form').serialize(), function(data) {
            loading.fadeOut();
            phpConsole = data;
            $.getScript('ace/theme-'+phpConsole.theme+'.js', function() {
                aceeditor.setTheme("ace/theme/"+phpConsole.theme);
            });
            editor.css('font-size', phpConsole.fontsize);

            if (phpConsole.highlightactive == 1) {
                aceeditor.setHighlightActiveLine(true);
            } else {
                aceeditor.setHighlightActiveLine(false);
            }

            if (phpConsole.show_print_margin == 1) {
                aceeditor.setShowPrintMargin(true);
            } else {
                aceeditor.setShowPrintMargin(false);
            }

            if (phpConsole.soft_tab == 1) {
                aceeditor.getSession().setUseSoftTabs(true);
            } else {
                aceeditor.getSession().setUseSoftTabs(false);
            }

            if (phpConsole.highlight_selected_word == 1) {
                aceeditor.setHighlightSelectedWord(true);
            } else {
                aceeditor.setHighlightSelectedWord(false);
            }
        }, 'json');
    });

    function openEditor() {
        editor.fadeIn('normal', function() {});
        output.fadeOut('normal');
    }
    function openOutput() {
        output.fadeIn('normal', function() {
            output.effect('highlight');
        });
        editor.fadeOut('normal');
    }
    function submitCode() {
        loading.css('display', 'inline-block');
        $.post('index.php', {
            code: aceeditor.getSession().getValue(),
            action: 'code'
        }, function(data) {
            loading.fadeOut();
            openOutput();
            output.html(data);
        }, 'html');
    }

//    aceeditor.getSession().on('change', function() {
//        $.post('index.php', {
//            code: aceeditor.getSession().getValue(),
//            action: 'autosave'
//        }, function(data) {
//        }, 'html');
//    });

//    $.get('phpcode.php', {}, function(data) {
//        aceeditor.getSession().setValue(data);
//    });
});