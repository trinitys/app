<?php

$app = F::app();
$dir = dirname(__FILE__) . '/';

// Kill
$wgEnableWikiaBarExt = false;

// Controllers
$app->registerClass('TrayController', $dir . 'TrayController.class.php');

// Hooks
//$app->registerHook('OutputPageBeforeHTML', 'TrayController', 'outputHook');
