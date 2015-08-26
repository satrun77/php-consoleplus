<?php

/**
 * PHP Console+
 *
 * A web-based php debug console
 *
 * Copyright (C) 2011, Mohamed Alsharaf
 * http://my.geek.nz
 *
 * Licensed under the new BSD License
 *
 * Source Code: https://github.com/satrun77/php-consoleplus
 */
class PhpConsole
{
    private $version = '1.0';
    private $author;
    private $credit;
    private $settings;
    private $userSettings;
    private $name = 'php-console+';

    public function __construct()
    {
        if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'), true)) {
            header('HTTP/1.1 401 Access unauthorized');
            die('ERR/401 Go Away');
        }

        ini_set('display_errors', 1);
        error_reporting(E_ALL | E_STRICT);

        $this->initData();
    }

    public function renderPhpCode($code)
    {
        $settings = $this->getUserSettings();
        if (isset($settings['phprenderer']) && $settings['phprenderer'] == 'eval') {
            eval($code);
        } else {
            $this->savePhpCode($code);
            include_once 'phpcode.php';
            @unlink('phpcode.php');
        }
        die;
    }

    public function savePhpCode($code)
    {
        file_put_contents('phpcode.php', "<?php \n" . stripslashes($code));
    }

    public function getUserSettings()
    {
        $file = 'settings.php';
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }
        return $this->userSettings;
    }

    public function saveUserSettings($data)
    {
        foreach ($this->userSettings as $name => $value) {
            if (!isset($data[$name])) {
                $data[$name] = '0';
            }
        }
        file_put_contents('settings.php', serialize($data));
        echo json_encode($this->getUserSettings());
    }

    public function settingsForm()
    {
        $settings = $this->getUserSettings();

        $form = '<form action="" method="post"><ol>';
        $form .= '<input type="hidden" name="action" value="settings"/>';

        foreach ($this->settings as $name => $setting) {
            $form .= '<li><label for="ed-' . $name . '">' . $setting['label'] . '</label>';

            switch ($setting['element']) {
                case 'select':
                    $form .= '<select id="ed-' . $name . '" name="' . $name . '">';
                    foreach ($setting['options'] as $optionValue => $optionLabel) {
                        $form .= '<option value="' . $optionValue . '" ' . ((isset($settings[$name]) && $settings[$name] == $optionValue) ? 'selected="selected"' : '') . '>' . $optionLabel . '</option>';
                    }
                    $form .= '</select>';
                    break;

                case 'checkbox':
                    $form .= '<input value="1" type="checkbox" id="ed-' . $name . '" name="' . $name . '" ' . ((isset($settings[$name]) && $settings[$name] == 1) ? 'checked="checked"' : '') . '/>';
                    break;
                case 'text':
                    $form .= '<input type="text" id="ed-' . $name . '" name="' . $name . '" value="' . (isset($settings[$name]) ? $settings[$name] : '') . '"/>';
                    break;
                case 'slider':
                    $form .= '<div id="ed-' . $name . 'slider" class="element"></div><input type="hidden" id="ed-' . $name . '" name="' . $name . '" value="' . (isset($settings[$name]) ? $settings[$name] : '') . '"/>';
                    $form .= '<script type="text/javascript">$("#ed-' . $name . 'slider").slider({'
                           . 'value:'.$settings[$name].',min: 0,max: 1000,step: 10,slide: function(event, ui) {$(this).prev().html("'.$setting['label'].' - "+ui.value+"px");'
                           . '$("#ed-' . $name . '").val(ui.value);}}).prev().html("'.$setting['label'].' - '.$settings[$name].'px");</script>';
                    break;
            }
            $form .= '</li>';
        }

        $form .= '</ol><button id="ed-savesettings-btn">Save</button><form>';


        return $form;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getAuthorWebsite()
    {
        return $this->author['website'];
    }

    public function getAuthorName()
    {
        return $this->author['name'];
    }

    public function getSourceCodeUrl()
    {
        return $this->author['sourceCode'];
    }

    public function getCreditWebsite()
    {
        return $this->credit['website'];
    }

    public function getCreditName()
    {
        return $this->credit['name'];
    }

    public function getCreditAppName()
    {
        return $this->credit['appName'];
    }

    private function initData()
    {
        $this->author = array(
            'website' => 'http://my.geek.nz',
            'name' => 'Mohamed Alsharaf',
            'sourceCode' => 'https://github.com/satrun77/php-consoleplus'
        );
        $this->credit = array(
            'website' => 'http://seld.be/',
            'name' => 'Jordi Boggiano',
            'appName' => 'php-console'
        );

        $this->settings = array(
            'theme' => array(
                'label' => 'Theme',
                'default' => 'clouds',
                'element' => 'select',
                'options' => array(
                    'eclipse' => 'Eclipse',
                    'dawn' => 'Dawn',
                    'idle_fingers' => 'idleFingers',
                    'pastel_on_dark' => 'Pastel on dark',
                    'twilight' => 'Twilight',
                    'clouds' => 'Clouds',
                    'clouds_midnight' => 'Clouds Midnight',
                    'crimson_editor' => 'Crimson Editor',
                    'kr_theme' => 'krTheme',
                    'mono_industrial' => 'Mono Industrial',
                    'monokai' => 'Monokai',
                    'merbivore' => 'Merbivore',
                    'merbivore_soft' => 'Merbivore Soft',
                    'vibrant_ink' => 'Vibrant Ink',
                    'solarized_dark' => 'Solarized Dark',
                    'solarized_light' => 'Solarized Light',
                )
            ),
            'fontsize' => array(
                'label' => 'Font Size',
                'default' => '12px',
                'element' => 'select',
                'options' => array(
                    '10px' => '10px', '11px' => '11px', '12px' => '12px',
                    '14px' => '14px', '16px' => '16px', '20px' => '20px',
                    '24px' => '24px'
                )
            ),
            'highlightactive' => array(
                'label' => 'Highlight Active Line',
                'default' => 'checked',
                'element' => 'checkbox'
            ),
            'show_print_margin' => array(
                'label' => 'Show Print Margin',
                'default' => 'checked',
                'element' => 'checkbox'
            ),
            'soft_tab' => array(
                'label' => 'Use Soft Tab',
                'default' => 'checked',
                'element' => 'checkbox'
            ),
            'highlight_selected_word' => array(
                'label' => 'Highlight selected word',
                'default' => 'checked',
                'element' => 'checkbox'
            ),
            'phprenderer' => array(
                'label' => 'PHP Renderer',
                'default' => 'temporary',
                'element' => 'select',
                'options' => array(
                    'temporary' => 'Temporary PHP file',
                    'eval' => 'eval()'
                )
            ),
            'height' => array(
                'label' => 'Height',
                'default' => 400,
                'element' => 'slider'
            )
        );

        $this->userSettings = array(
            'theme' => 'clouds',
            'fontsize' => '12px',
            'highlightactive' => '1',
            'show_print_margin' => '1',
            'soft_tab' => '1',
            'highlight_selected_word' => '1',
            'phprenderer' => 'temporary',
            'height' => 400
        );
    }

}
