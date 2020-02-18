<?php

/*********************************************
 * CUSTOM_SETTINGS = false - автоматические настройки
 * CUSTOM_SETTINGS = true  - пользовательские настройки
 *********************************************/
define('CUSTOM_SETTINGS', false);

$CatalogloaderAppVersionCheck = '';

$imgParam = new DownloaderParametersHelper('customCms');
$imgParam->GetTemplate();
$dbParam = $imgParam->_dbConf;

$dbParam->host = 'localhost';
$dbParam->user = '';
$dbParam->password = '';
$dbParam->dbName = '';
$dbParam->dbPref = '';


$imgParam->pathToImg = $_SERVER['DOCUMENT_ROOT'].'/img/p'; //Путь к каталогу с картинками
$imgParam->pathToImgTmp = 'tmp'; //Путь к каталогу с временными файлами
$imgParam->pathToAdditionalFiles = 'tmp'; // Путь к каталогу с файлами-приложениями (pdf, архивы и т.п.)
$imgParam->watermarkPath = '';
$imgParam->watermarkUse = false;
$imgParam->donot_load_if_exists = false; //Не загружать файл, если существует

$prop = new ImageChangerProperties();
$prop->resizeToHeigth = null; // установить NULL если размер не нужно менять
$prop->resizeToWidth = null; // установить NULL если размер не нужно менять
$prop->renameTo = '{0}'; //Переименовать картинку {0} - имя по умолчанию
$prop->saveToPath = '';
$prop->backGroundFill = true;
$imgParam->config[0] = $prop;

$prop = new ImageChangerProperties();
$prop->resizeToHeigth = 80; // установить NULL если размер не нужно менять
$prop->resizeToWidth = 80; // установить NULL если размер не нужно менять
$prop->renameTo = '{0}-cart_default'; //Переименовать картинку {0} - имя по умолчанию
$prop->saveToPath = '';
$prop->backGroundFill = true;
$imgParam->config[1] = $prop;

$prop = new ImageChangerProperties();
$prop->resizeToHeigth = 98; // установить NULL если размер не нужно менять
$prop->resizeToWidth = 98; // установить NULL если размер не нужно менять
$prop->renameTo = '{0}-small_default'; //Переименовать картинку {0} - имя по умолчанию
$prop->saveToPath = '';
$prop->backGroundFill = true;
$imgParam->config[2] = $prop;

$prop = new ImageChangerProperties();
$prop->resizeToHeigth = 125; // установить NULL если размер не нужно менять
$prop->resizeToWidth = 125; // установить NULL если размер не нужно менять
$prop->renameTo = '{0}-medium_default'; //Переименовать картинку {0} - имя по умолчанию
$prop->saveToPath = '';
$prop->backGroundFill = false; //тут стояло false
$imgParam->config[3] = $prop;

$prop = new ImageChangerProperties();
$prop->resizeToHeigth = 250; // установить NULL если размер не нужно менять
$prop->resizeToWidth = 250; // установить NULL если размер не нужно менять
$prop->renameTo = '{0}-home_default'; //Переименовать картинку {0} - имя по умолчанию
$prop->saveToPath = '';
$prop->backGroundFill = true;
$imgParam->config[4] = $prop;

$prop = new ImageChangerProperties();
$prop->resizeToHeigth = 458; // установить NULL если размер не нужно менять
$prop->resizeToWidth = 458; // установить NULL если размер не нужно менять
$prop->renameTo = '{0}-large_default'; //Переименовать картинку {0} - имя по умолчанию
$prop->saveToPath = '';
$prop->backGroundFill = true;
$imgParam->config[5] = $prop;

$prop = new ImageChangerProperties();
$prop->resizeToHeigth = 800; // установить NULL если размер не нужно менять
$prop->resizeToWidth = 800; // установить NULL если размер не нужно менять
$prop->renameTo = '{0}-thickbox_default'; //Переименовать картинку {0} - имя по умолчанию
$prop->saveToPath = '';
$prop->backGroundFill = true;
$imgParam->config[6] = $prop;