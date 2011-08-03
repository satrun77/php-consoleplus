<?php
include_once 'PhpConsole.php';
$phpconsole = new PhpConsole();

if (isset($_POST['action'])) {

    // save user settings
    if ($_POST['action'] == 'settings') {
        $phpconsole->saveUserSettings($_POST);
        die;
    }

    // submit code
    if ($_POST['action'] == 'code') {
        $phpconsole->renderPhpCode($_POST['code']);
        die;
    }

    // auto save
    if ($_POST['action'] == 'autosave') {
        $phpconsole->savePhpCode($_POST['code']);
        die;
    }
}

$settings = $phpconsole->getUserSettings();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>PHP Console+</title>
        <link rel="stylesheet" type="text/css" href="jquery/black-tie/jquery-ui-custom.css" />
        <link rel="stylesheet" type="text/css" href="styles.css" />
        <script type="text/javascript" src="jquery/jquery.min.js"></script>
        <script type="text/javascript" src="jquery/jquery-ui-custom.min.js"></script>

        <script src="ace/ace.js" type="text/javascript" charset="utf-8"></script>
        <script src="ace/theme-clouds.js" type="text/javascript" charset="utf-8"></script>
        <script src="ace/mode-php.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            var phpConsole = {<?php foreach ($settings as $name => $value): echo $name . ":'" . $value . "',"; endforeach; ?>last: true};
        </script>
        <script type="text/javascript" src="php-console.js"></script>
    </head>
    <body>
        <div id="toolbar" class="ui-widget-header ui-corner-all">

            <div id="credit">
                <?php echo $phpconsole->getName(); ?> v<?php echo $phpconsole->getVersion() ?> -
                by <a href="<?php echo $phpconsole->getAuthorWebsite(); ?>"><?php echo $phpconsole->getAuthorName(); ?></a> -
                inspired by <?php echo $phpconsole->getCreditAppName(); ?> for <a href="<?php echo $phpconsole->getCreditWebsite(); ?>"><?php echo $phpconsole->getCreditName(); ?></a> -
                <a href="<?php echo $phpconsole->getSourceCodeUrl(); ?>">source code in GitHub</a>
            </div>

            <span id="loading"><img src="loader.gif" /><span>Wait please...</span></span>

            <button id="ed-settings-btn">Editor Settings</button>
            <span id="actions">
                <input type="radio" id="ed-showeditor-btn" name="actions-btn" checked="checked"/><label for="ed-showeditor-btn">Editor</label>
                <input type="radio" id="ed-showoutput-btn" name="actions-btn" /><label for="ed-showoutput-btn">Show Output</label>
            </span>
            <button id="ed-submitcode-btn">Submit Code</button>
        </div>
        <div id="ed-settings" class="ui-widget ui-widget-content ui-corner-all">
            <?php echo $phpconsole->settingsForm(); ?>
        </div>

        <div id="ed-editor"></div>
        <div id="ed-output"></div>

        <div class="ui-widget-shadow ui-corner-all"></div>

    </body>
</html>
