<?php

// error_reporting(E_ALL ^ E_DEPRECATED); // Подавляем сообщения о mysql Deprecated, аналог в .htaccess строки php_value error_reporting 4437
// Этот код используется для углубленной отладки там, где не видно лога исключений, аналог в .htaccess строки #php_flag log_errors on
// ini_set("display_errors", "on");
// ini_set('html_errors', 'on');

// BUG: Scripts can work incorrect in some encodings
mb_internal_encoding("UTF-8");
mb_http_input("UTF-8");
mb_http_output("UTF-8");

header('Content-Type: text/html; charset=utf-8');

require_once 'catalogloader_config.php';

set_exception_handler('ExceptionHandler');
function ExceptionHandler($exception)
{
  echo "[ERROR] Unhandled error: " . $exception->getMessage();
}

set_error_handler("ErrorHelper");
function ErrorHelper($errno, $errstr, $errfile, $errline)
{
    $err="[$errno] $errstr in line $errline of file '$errfile'\n";
    ErrorHandler::$ERROR .= $err;

    //$adminMsg = $err;
    //$adminMsg .= "<pre>";
    //$adminMsg .= "<hr>".str_replace(">","&gt;",str_replace("<","&lt;",$this->_logMsg));
    //$adminMsg .= "</pre>";
    //mail($this->_adminMail, 'ERROR', $adminMsg, "MIME-Version: 1.0\r\nContent-type: text/html;
    //charset=windows-1251");
}

class ErrorHandler
{
    public static $ERROR = "";
}

/**
 * Класс для работы с БД
 */
class MainDb
{
    var $host = "localhost";//Имя хоста
    var $port = "3306";       //Порт
    var $user;              //Имя пользователя
    var $password;          //Пароль
    var $dbName;            //Имя базы данных
    var $dbPref;            //Префикс базы данных

    var $charset = "utf8";
    var $useMySQLi = false;

    var $log;               //Экземпляр класса LogWriter
    var $_dbConn = null;    //Resource DB

    function MainDb()
    {
        $this->log = new LogWriter();

        if (version_compare(phpversion(), '5.5.0', '>=') && function_exists('mysqli_connect'))
        {
            $this->useMySQLi = true;
        }

        /*$this->log->ToLog("MainDb.this->useMySQLi = " . $this->useMySQLi);
        if ($this->useMySQLi)
        {
            $this->log->ToLog("YES useMySQLi");
        }
        else
        {
            $this->log->ToLog("NO useMySQLi");
        }*/
    }

    function SqlQuery($sql)
    {
        if($this->useMySQLi)
        {
            return $this->MySqliQuery($sql);
        }
        else
        {
            return $this->MySqlQuery($sql);
        }
    }

    function MySqlQuery($sql)
    {
        try
        {
            if ($this->_dbConn === null)
            {
                if(empty($this->host) || empty($this->user))
                {
                    throw new Exception("Error connecting to database: host or username missed!");
                }

                $this->_dbConn = mysql_connect($this->host, $this->user, $this->password);
                if(!$this->_dbConn)
                {
                    throw new Exception("Error connecting to database!");
                }

                if(!mysql_select_db($this->dbName))
                {
                    throw new Exception("Error connecting to database [$this->user] @ [$this->host] : $this->port / [$this->dbName]");
                }

                mysql_set_charset('utf8', $this->_dbConn);
            }

            $result = mysql_query($sql);
            if(!$result)
            {
                $queryError = "$sql\n" . mysql_error() . "\n";
                trigger_error($queryError);
                return;
            }
            return $result;
        }
        catch (Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
            //$this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
        }
    }

    function MySqliQuery($sql)
    {
        try
        {
            if ($this->_dbConn === null)
            {
                if(empty($this->host) || empty($this->user))
                {
                    throw new Exception("Error connecting to database: host or username missed!");
                }

                $this->_dbConn = mysqli_connect($this->host, $this->user, $this->password, $this->dbName, $this->port);
                if(!$this->_dbConn)
                {
                    throw new Exception("Error connecting to database [$this->user] @ [$this->host] : $this->port / [$this->dbName]");
                }

                $this->_dbConn->set_charset("utf8");
            }

            $result = mysqli_query($this->_dbConn, $sql);
            if(!$result)
            {
                $queryError = "$sql\n".mysqli_error($this->_dbConn)."\n";
                trigger_error($queryError);
                return;
            }
            return $result;
        }
        catch (Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
            //$this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
        }
    }

    function ConnectionClose()
    {
        $connectionClosed = false;
        if($this->useMySQLi)
        {
            $connectionClosed = mysqli_close($this->_dbConn);
        }
        else
        {
            $connectionClosed = mysql_close($this->_dbConn);
        }

        /* YAR: этот код не нужен.
        Не смогли закрыть - фиг с ним, пусть сам закрывается когда сессия протухнет,
        это не повод валить сам апдейтер!
        более того этот код перекрывает вывод сообщения о ошибке выполнения запроса MySQL

        RemoteDataProvider.Error.On.OriginalSql: SELECT LID, EMAIL, `NAME`, SITE_NAME FROM `b_lang` ORDER BY ACTIVE = 'Y' DESC, DEF = 'Y' DESC;
        Server.Answer.Error: <?xml version="1.0" encoding="utf-8"?>
        <Error>[2] mysql_connect(): Access denied for user 'root'@'localhost'

        if(!$connectionClosed)
        {
            throw new Exception("Connection can not be closed!");
        }
        */
    }

    function GetQueryArray($sql)
    {
        try
        {
            $data = array();
            if(!$result = $this->SqlQuery($sql)) return $data;
            if($this->useMySQLi)
            {
                //$data = mysqli_fetch_array($result);
                while($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    array_push($data, $line);
                }
            }
            else
            {
                while($line = mysql_fetch_array($result, MYSQL_ASSOC))
                {
                    array_push($data, $line);
                }
            }
            return $data;
        }
        catch(Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
        }
    }

    function GetTable($sql)
    {
        try
        {
            $tableData = '';
            if(!$result = $this->SqlQuery($sql))
            {
                return $tableData;
            }

            /* заголовки */
            $headers='';
            if($this->useMySQLi)
            {
                $columnIdx = 0;
                while($result->field_count > $columnIdx)
                {
                    $col = mysqli_fetch_field_direct($result, $columnIdx);
                    if($headers != '') $headers .= "\t";
                    //$flagNotNull = ($col["flags"][0] == "not_null") ? "1" : "0";
                    $flagNotNull = "0";
                    $headers .= $col->type.'|'.$flagNotNull.'|'.$col->name;
                    $columnIdx++;
                }
            }
            else
            {
                while($col = mysql_fetch_field($result))
                {
                    if($headers != '') $headers .= "\t";
                    $headers .= $col->type.'|'.$col->not_null.'|'.$col->name;
                }
            }

            $tableData.=$headers;

            while(true)
            {
                $row = ($this->useMySQLi)
                                ? mysqli_fetch_row($result)
                                : mysql_fetch_row($result);

                if(!$row) break;

                $rowData='';
                $theFirstRow = TRUE;
                foreach($row as $cell)
                {
                    //if($rowData != '')
                    if ($theFirstRow == FALSE)
                    {
                        $rowData.="\t";
                    }
                    $rowData .= str_replace("\n", " ", str_replace("\t", " ", $cell));
                    $theFirstRow = FALSE;
                }
                $tableData .= "\n".$rowData;
            }
            return $tableData;
        }
        catch(Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
        }
    }

    function GetString($sql)
    {
        try
        {
            $data = $this->GetQueryArray($sql);
            if(count($data) > 0) foreach($data[0] as $value) return $value;
            return '';
        }
        catch(Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
        }
    }

    function ExecuteSQL($sql)
    {
        try
        {
            if(!$result = $this->SqlQuery($sql))
            {
                return '';
            }
            return '1';
        }
        catch(Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
        }
    }
}

class ImageChangerProperties
{
    var $resizeToHeigth;
    var $resizeToWidth;
    var $renameTo;
    var $saveToPath;
    var $backGroundFill;

    function ImageChangerProperties()
    {
        $this->resizeToHeigth = null;
        $this->resizeToWidth = null;
        $this->renameTo = '{0}';
        $this->saveToPath = '';
        $this->backGroundFill = true;
    }
}

abstract class CmsDataExtractor
{
    var $pathToImg;
    var $pathToImgTmp;
    var $pathToAdditionalFiles;
    var $watermarkPath;
    var $watermarkUse;
    var $donot_load_if_exists;

    var $imgName;

    var $confProp = array();
    var $_db;
    var $log;
    var $useMySQLi;

    function CmsDataExtractor()
    {
        $this->log = new LogWriter();
        $this->_db = new MainDb();

        $this->useMySQLi = $this->_db->useMySQLi;

        //$this->log->ToLog("CmsDataExtractor->useMySQLi = " . $useMySQLi);

        $this->watermarkPath = $this->GetWaterMarkPath();
        $this->donot_load_if_exists = false; // YAR: Этот флаг не работает, он проверяет временный файл вместо целевого!

        $this->GetDbSettings();
        $this->GetDataImages();
    }

    private function GetWaterMarkPath()
    {
        foreach (glob("*") as $file)
        {
            preg_match("/watermark.gif/", $file, $matches);
            if(!empty($matches))
            {
                $this->watermarkUse = true;
                return $matches[0];
            }
        }

        $this->watermarkUse = false;
        return "";
    }

    abstract function GetDataImages();
    abstract function GetDbSettings();
}

class CustomCmsExtractor extends CmsDataExtractor
{
    function CustomCmsExtractor()
    {
        parent::CmsDataExtractor();
    }

    function GetDataImages(){}

    function GetDbSettings(){}
}

/*****************************************************************************
 * !Все работает
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в modules/CatalogLoader/
 *****************************************************************************/
class PrestaShopExtractor extends CmsDataExtractor
{
    function PrestaShopExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../img/p';
        $this->pathToImgTmp = '../../img/p/tmp';
        $this->pathToAdditionalFiles = '../../download';
    }

    function GetDataImages()
    {
        $queryCount = "SELECT `name`, `width`, `height` FROM {$this->_db->dbPref}image_type " .
            "WHERE `name` NOT IN ('category_default', 'scene_default', 'm_scene_default');";
        $result = $this->_db->SqlQuery($queryCount);

        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';

        $i = 1;
        while($line = ($this->useMySQLi) ? mysqli_fetch_array($result, MYSQLI_ASSOC) : mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $this->confProp[$i] = new ImageChangerProperties();
            $this->confProp[$i]->resizeToHeigth = $line['height'];
            $this->confProp[$i]->resizeToWidth = $line['width'];
            $this->confProp[$i]->renameTo = '{0}-'.$line['name'];
            $i++;
        }
    }

    function GetDbSettings()
    {
        require_once '../../config/settings.inc.php';

        $this->_db->host = _DB_SERVER_;
        $this->_db->user = _DB_USER_;
        $this->_db->password = _DB_PASSWD_;
        $this->_db->dbName = _DB_NAME_;
        $this->_db->dbPref = _DB_PREFIX_;
    }
}

/*****************************************************************************
 * !Все работает
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в modules/CatalogLoader/
 *****************************************************************************/
class PrestaShopImageColorExtractor extends CmsDataExtractor
{
    function PrestaShopImageColorExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../img';
        $this->pathToImgTmp = '../../img/p/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = 100;
        $this->confProp[0]->resizeToWidth = 100;
        $this->confProp[0]->renameTo = '{0}';
    }

    function GetDbSettings()
    {
        require_once '../../config/settings.inc.php';

        $this->_db->host = _DB_SERVER_;
        $this->_db->user = _DB_USER_;
        $this->_db->password = _DB_PASSWD_;
        $this->_db->dbName = _DB_NAME_;
        $this->_db->dbPref = _DB_PREFIX_;
    }
}

/*****************************************************************************
 * webasyst
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в wa-data/public/shop/CatalogLoader
 *****************************************************************************/
class ShopScript5Extractor extends CmsDataExtractor
{
    function ShopScript5Extractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../products';
        $this->pathToImgTmp = '../products/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = 970;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = "{0}.970";
        $this->confProp[0]->backGroundFill = false;

        $this->confProp[1] = new ImageChangerProperties();
        $this->confProp[1]->resizeToHeigth = 750;
        $this->confProp[1]->resizeToWidth = null;
        $this->confProp[1]->renameTo = "{0}.750x0";
        $this->confProp[1]->backGroundFill = false;

        $this->confProp[2] = new ImageChangerProperties();
        $this->confProp[2]->resizeToHeigth = 220;
        $this->confProp[2]->resizeToWidth = null;
        $this->confProp[2]->renameTo = "{0}.220";
        $this->confProp[2]->backGroundFill = false;
        
        $this->confProp[3] = new ImageChangerProperties();
        $this->confProp[3]->resizeToHeigth = 115;
        $this->confProp[3]->resizeToWidth = null;
        $this->confProp[3]->renameTo = "{0}.115x0";
        $this->confProp[3]->backGroundFill = false;

        // Корзина
        $this->confProp[4] = new ImageChangerProperties();
        $this->confProp[4]->resizeToHeigth = 100;
        $this->confProp[4]->resizeToWidth = null;
        $this->confProp[4]->renameTo = "{0}.100x0";
        $this->confProp[4]->backGroundFill = false;
                
        $this->confProp[5] = new ImageChangerProperties();
        $this->confProp[5]->resizeToHeigth = 200;
        $this->confProp[5]->resizeToWidth = null;
        $this->confProp[5]->renameTo = "{0}.200x0";
        $this->confProp[5]->backGroundFill = false;
                
        // Миниатюры в админке
        $this->confProp[6] = new ImageChangerProperties();
        $this->confProp[6]->resizeToHeigth = 48;
        $this->confProp[6]->resizeToWidth = 48;
        $this->confProp[6]->renameTo = "{0}.48x48";
        $this->confProp[6]->backGroundFill = false;
        
        // сравнение
        $this->confProp[7] = new ImageChangerProperties();
        $this->confProp[7]->resizeToHeigth = 96;
        $this->confProp[7]->resizeToWidth = 0;
        $this->confProp[7]->renameTo = "{0}.96x96";
        $this->confProp[7]->backGroundFill = false;
        
        // "Вы смотрели"
        $this->confProp[8] = new ImageChangerProperties();
        $this->confProp[8]->resizeToHeigth = 48;
        $this->confProp[8]->resizeToWidth = 48;
        $this->confProp[8]->renameTo = "{0}.48";
        $this->confProp[8]->backGroundFill = false;
    }

    function GetDbSettings()
    {
        $config = (require_once '../../../../wa-config/db.php');

        $this->_db->host = $config['default']['host'];
        $this->_db->user = $config['default']['user'];
        $this->_db->password = $config['default']['password'];
        $this->_db->dbName = $config['default']['database'];
        $this->_db->dbPref = '';
    }
}

/*********************************************************************************
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в bitrix/catalogloader/
 **********************************************************************************/
class BitrixExtractor extends CmsDataExtractor
{
    function BitrixExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../upload/iblock/';
        $this->pathToImgTmp = '../../upload/tmp/';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';
    }

    function GetDbSettings()
    {
        try
        {
            $config_path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php';
            if (file_exists($config_path) === false)
            {
                throw new Exception("Error on opening '{$config_path}' ! File not found");
            }

            $config = file_get_contents($config_path);
            if(empty($config))
            {
                throw new Exception("Error on opening '{$config_path}' ! No access to file");
            }

            if(empty($config))
            {
                throw new Exception("Error on opening '{$config_path}' ! File is empty");
            }

            $host = array();
            $user = array();
            $password = array();
            $dbName = array();

            preg_match_all("/(?<=DBHost = \")([^\"]+)/", $config, $host);
            preg_match_all("/(?<=DBLogin = \")([^\"]+)/", $config, $user);
            preg_match_all("/(?<=DBPassword = \")([^\"]+)/", $config, $password);
            preg_match_all("/(?<=DBName = \")([^\"]+)/", $config, $dbName);

            if (isset($host[0]) === false || isset($host[0][0]) === false)
            {
                $host[0][0] = 'localhost';
            }
            else
            {
                $host_params = preg_split("/:/", $host[0][0], 2, PREG_SPLIT_NO_EMPTY);
                $host[0][0] = $host_params[0];
                if (count($host_params) > 1)
                {
                    $this->_db->port = $host_params[1];
                }
            }

            if (isset($password[0]) === false || isset($password[0][0]) === false)
            {
                $password[0][0] = '';
            }

            $this->_db->host = $host[0][0];
            $this->_db->user = $user[0][0];
            $this->_db->password = $password[0][0];
            $this->_db->dbName = $dbName[0][0];

            $this->_db->dbPref = 'b_';
        }
        catch(Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
        }
    }
}

/*********************************************************************************
 * !Все работает
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в admin/controller/module/CatalogLoader/
 **********************************************************************************/
class OpenCartExtractor extends CmsDataExtractor
{
    function OpenCartExtractor()
    {
        parent::CmsDataExtractor();

        require_once '../../../../config.php';

        /*
         * Проверьте версию в админке либо в /index.php
         * Пример:
         *        define('VERSION', '2.0.3.1');
         *
         * в зависимости от версии фото будут расположены в
         * v1.5 = '/data'
         * v2.0 = '/catalog'
         */
        //$this->pathToImg = rtrim(DIR_IMAGE) . '/catalog';
        //$this->pathToImgTmp = $this->pathToImg . '/tmp';

        $this->pathToImg = '../../../../image/catalog';
        $this->pathToImgTmp = '../../../../image/catalog/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';
    }

    function GetDbSettings()
    {
        try
        {
            require_once '../../../../config.php';

            $this->_db->host = DB_HOSTNAME;
            $this->_db->user = DB_USERNAME;
            $this->_db->password = DB_PASSWORD;
            $this->_db->dbName = DB_DATABASE;
            $this->_db->dbPref = DB_PREFIX;
        }
        catch(Exception $ex)
        {
            ErrorHandler::$ERROR .= "[{$ex->getCode()}] {$ex->getMessage()} in line {$ex->getLine()} of file {$ex->getFile()}\n";
        }
    }
}

/*********************************************************************************
 * !Все работает
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в modules/CatalogLoader/
 **********************************************************************************/
class JoomShoppingExtractor extends CmsDataExtractor
{
    function JoomShoppingExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../components/com_jshopping/files/img_products';
        $this->pathToImgTmp = '../../components/com_jshopping/files/img_products/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = 'full_{0}';

        $this->confProp[1] = new ImageChangerProperties();
        //$this->confProp[1]->resizeToHeigth = 100;
        //$this->confProp[1]->resizeToWidth = 100;
        $this->confProp[1]->renameTo = 'thumb_{0}';

        $this->confProp[2] = new ImageChangerProperties();
        //$this->confProp[2]->resizeToHeigth = 200;
        //$this->confProp[2]->resizeToWidth = 200;
        $this->confProp[2]->renameTo = '{0}';
    }
    function GetDbSettings()
    {
        require_once '../../configuration.php';

        $conf = new JConfig();
        $this->_db->host = $conf->host;;
        $this->_db->user = $conf->user;
        $this->_db->password = $conf->password;
        $this->_db->dbName = $conf->db;
        $this->_db->dbPref = $conf->dbprefix;
    }
}

/***************************
 * !Все работает
 * Файл catalogloader_handler.php, catalogloader_settings.php и папку tmp ложить в _local/modules/CatalogLoader/
 ***************************/
class AmiroCmsExtractor extends CmsDataExtractor
{
    function AmiroCmsExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../../_mod_files/ce_images/eshop';
        $this->pathToImgTmp = '../../../_mod_files/ce_images/eshop/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';
    }
    function GetDbSettings()
    {
        $conf = parse_ini_file('../../config.ini.php');

        $this->_db->host = $conf['DB_Host'];
        $this->_db->user = $conf['DB_User'];
        $this->_db->password = $conf['DB_Password'];
        $this->_db->dbName = $conf['DB_Database'];
        $this->_db->dbPref = '';
    }
}

/********************************************************************
 * HostCMS пока пропустил.. непонятки с адресами картинок и доступом
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в modules/CatalogLoader
 ********************************************************************/
class HostCmsExtractor extends CmsDataExtractor
{
    function HostCmsExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../images';
        $this->pathToImgTmp = '../../images/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';
    }
    function GetDbSettings()
    {
        $conf = require_once '../core/config/database.php';
        $this->_db->host = $conf['default']['host'];
        $this->_db->user = $conf['default']['username'];
        $this->_db->password = $conf['default']['password'];
        $this->_db->dbName = $conf['default']['database'];
        $this->_db->dbPref = '';
    }
}

/**********************************************************************************
 * В cms нет данных про размер картинок.. Пока что автоматические настройки не работают..
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в core/modules/CatalogLoader
 **********************************************************************************/
class ShopCmsExtractor extends CmsDataExtractor
{
    function ShopCmsExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '';
        $this->pathToImgTmp = '';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';
    }
    function GetDbSettings()
    {
        require_once '../../config/connect.inc.php';
        $this->_db->host = DB_HOST;
        $this->_db->user = DB_USER;
        $this->_db->password = DB_PASS;
        $this->_db->dbName = DB_NAME;
        $this->_db->dbPref = DB_PRFX;
    }
}

/*********************************************************************************************
 * !Все работает
 * Файл CatalogLoader_handler.php вместе с папкой tmp ложить в files/CatalogLoader/
 *********************************************************************************************/
class SimplaCmsExtractor extends CmsDataExtractor
{
    function SimplaCmsExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../originals';
        $this->pathToImgTmp = '../originals/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';
    }

    function GetDbSettings()
    {
        if(file_exists("../../config/config.php"))
        {
            $conf = parse_ini_file("../../config/config.php");
            $this->_db->host = $conf['db_server'];
            $this->_db->user = $conf['db_user'];
            $this->_db->password = $conf['db_password'];
            $this->_db->dbName = $conf['db_name'];
            $this->_db->dbPref = $conf['db_prefix'];
        }
        else if(file_exists("../../Config.class.php"))
        {
            require_once "../../Config.class.php";
            $this->_db->host = $dbhost;
            $this->_db->user = $dbuser;
            $this->_db->password = $dbpass['db_password'];
            $this->_db->dbName = $dbname;
            $this->_db->dbPref = "";
        }
        else
        {
            $this->log->ToLog("Не найден файл конфигурации для подключения к базе данных!!!", LogWriter::ERROR);
            //ErrorHandler::$ERROR .= "Don't exists file configuration for \"Simpla\"";
            //return;
        }
    }
}

/*********************************************************************************
 * !Все работает
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в modules/CatalogLoader/
 **********************************************************************************/
class VirtueMartExtractor extends CmsDataExtractor
{
    function VirtueMartExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../images/stories/virtuemart/product';
        $this->pathToImgTmp = '../../images/stories/virtuemart/product/tmp';
    }

    function GetDataImages()
    {
        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->renameTo = '{0}';

        $this->confProp[1] = new ImageChangerProperties();
        $this->confProp[1]->resizeToHeigth = 90;
        $this->confProp[1]->resizeToWidth = 90;
        $this->confProp[1]->renameTo = '{0}_90x90';
        $this->confProp[1]->saveToPath = 'resized';
    }

    function GetDbSettings()
    {
        require_once '../../configuration.php';

        $conf = new JConfig();
        $this->_db->host = $conf->host;
        $this->_db->user = $conf->user;
        $this->_db->password = $conf->password;
        $this->_db->dbName = $conf->db;
        $this->_db->dbPref = $conf->dbprefix;
    }
}

/*******************************************************************************************
 * !Все работает(если переименовать .htaccess в каталоге images)
 * Файл catalogloader_handler.php, catalogloader_settings.php ложить в includes/modules/CatalogLoader
 *******************************************************************************************/
class VamShopExtractor extends CmsDataExtractor
{
    function VamShopExtractor()
    {
        parent::CmsDataExtractor();

        $this->pathToImg = '../../../images/product_images';
        $this->pathToImgTmp = '/tmp';
    }

    function GetDataImages()
    {
        $queryCount = "SELECT `configuration_key`, `configuration_value` FROM {$this->_db->dbPref}configuration " .
            "WHERE `configuration_key` IN ('PRODUCT_IMAGE_THUMBNAIL_WIDTH', 'PRODUCT_IMAGE_THUMBNAIL_HEIGHT', " .
            "'PRODUCT_IMAGE_INFO_WIDTH', 'PRODUCT_IMAGE_INFO_HEIGHT', 'PRODUCT_IMAGE_POPUP_WIDTH', " .
            "'PRODUCT_IMAGE_POPUP_HEIGHT')";
        $result = $this->_db->SqlQuery($queryCount);

        $list = array();
        while($line = ($this->useMySQLi) ? mysqli_fetch_array($result, MYSQLI_ASSOC) : mysql_fetch_array($result, MYSQL_ASSOC))
        {
            array_push($list, $line);
        }

        $this->confProp[0] = new ImageChangerProperties();
        $this->confProp[0]->resizeToHeigth = null;
        $this->confProp[0]->resizeToWidth = null;
        $this->confProp[0]->saveToPath = 'original_images';

        $this->confProp[1] = new ImageChangerProperties();
        $this->confProp[1]->resizeToHeigth = $list[1]['configuration_value'];
        $this->confProp[1]->resizeToWidth = $list[0]['configuration_value'];
        $this->confProp[1]->saveToPath = 'thumbnail_images';

        $this->confProp[2] = new ImageChangerProperties();
        $this->confProp[2]->resizeToHeigth = $list[3]['configuration_value'];
        $this->confProp[2]->resizeToWidth = $list[2]['configuration_value'];
        $this->confProp[2]->saveToPath = 'info_images';

        $this->confProp[3] = new ImageChangerProperties();
        $this->confProp[3]->resizeToHeigth = $list[5]['configuration_value'];
        $this->confProp[3]->resizeToWidth = $list[4]['configuration_value'];
        $this->confProp[3]->saveToPath = 'popup_images';
    }

    function GetDbSettings()
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/configure.php';

        $this->_db->host = DB_SERVER;
        $this->_db->user = DB_SERVER_USERNAME;
        $this->_db->password = DB_SERVER_PASSWORD;
        $this->_db->dbName = DB_DATABASE;
        $this->_db->dbPref = '';
    }
}

/*****************************************************************************
 *
 *
 *****************************************************************************/
class CSCartExtractor extends CmsDataExtractor
{
    function CSCartExtractor($imgName)
    {
        $this->imgName = $imgName;
        parent::CmsDataExtractor();
        $this->pathToImg = '../images/';
        $this->pathToImgTmp = '../images/tmp';

        //$this->log->ToLog("cscart useMySQLi = " . $this->useMySQLi);
    }

    function GetDataImages()
    {
        if(!empty($this->imgName))
        {
            //$this->log->ToLog("2 cscart useMySQLi = " . $this->useMySQLi);
            //$this->log->ToLog("Image name not found in CS-Cart!", LogWriter::ERROR);
            $queryImageId = "SELECT `image_id` FROM {$this->_db->dbPref}images " . "WHERE image_path='" . $this->imgName . "' ORDER BY image_id DESC";

            //$this->log->ToLog(" queryImageId = ". $queryImageId);

            $idImgsFromDB = array();
            if(!$result = $this->_db->SqlQuery($queryImageId))
            {
                return;
            }

            //$this->log->ToLog("result = ". var_dump($result));

            $idImgsFromDB = ($this->useMySQLi)
                                    ? mysqli_fetch_all($result)
                                    : mysql_fetch_array($result, MYSQL_NUM);

            //$this->log->ToLog("idImgsFromDB = ". var_dump($idImgsFromDB));

            $config_local_file_contents=file_get_contents('../config.local.php');
            preg_match("@define\('MAX_FILES_IN_DIR', (.*?)\);@smi", $config_local_file_contents, $nMAX_FILES_IN_DIR);
            $nMAX_FILES_IN_DIR=$nMAX_FILES_IN_DIR[1];
            if (empty($nMAX_FILES_IN_DIR)) $nMAX_FILES_IN_DIR=1000;


            if($this->useMySQLi)
            {
                //$this->log->ToLog("useMySQLi useMySQLi useMySQLi ");
                $lengthOfArray = count($idImgsFromDB);
                $lastSearchResult = ($lengthOfArray > 0) ? $idImgsFromDB[0][0] : 0;
            }
            else
            {
                //$this->log->ToLog("NOOOO useMySQLi useMySQLi useMySQLi ");
                $lengthOfArray = count($idImgsFromDB);
                $lastSearchResult = ($lengthOfArray > 0) ? $idImgsFromDB[$lengthOfArray - 1] : 0;
            }

            //$this->log->ToLog("lastSearchResult = " . $lastSearchResult);

            $cat_id = intval($lastSearchResult / $nMAX_FILES_IN_DIR);

            $this->confProp[0] = new ImageChangerProperties();
            $this->confProp[0]->resizeToHeigth = null;
            $this->confProp[0]->resizeToWidth = null;
            $this->confProp[0]->renameTo = '{0}';
            $this->confProp[0]->saveToPath = "detailed/$cat_id";

            $this->confProp[1] = new ImageChangerProperties();
            $this->confProp[1]->resizeToHeigth = null;
            $this->confProp[1]->resizeToWidth = 50;
            $this->confProp[1]->renameTo = '{0}';
            $this->confProp[1]->saveToPath = "thumbnails/[%width%]/[%height%]/detailed/$cat_id";
        }
    }

    function GetDbSettings()
    {
        $config_local_file_contents=file_get_contents('../config.local.php');

        preg_match("|config\['db_host'\] = ([^;]*)|", $config_local_file_contents, $dbHost);
        preg_match("|config\['db_user'\] = ([^;]*)|", $config_local_file_contents, $dbUser);
        preg_match("|config\['db_password'\] = ([^;]*)|", $config_local_file_contents, $dbPass);
        preg_match("|config\['db_name'\] = ([^;]*)|", $config_local_file_contents, $dbName);
        preg_match("|config\['table_prefix'\] = ([^;]*)|", $config_local_file_contents, $dbPref);

        $this->_db->host = trim($dbHost[1], '\'');
        $this->_db->user = trim($dbUser[1], '\'');
        $this->_db->password = trim($dbPass[1], '\'');
        $this->_db->dbName = trim($dbName[1], '\'');
        $this->_db->dbPref = trim($dbPref[1], '\'');

        //print_r($this->_db);
    }
}

class DownloaderParametersHelper
{
    var $pathToImg;
    var $pathToImgTmp;
    var $pathToAdditionalFiles;
    var $watermarkPath;
    var $watermarkUse;
    var $donot_load_if_exists;

    var $ImgName;

    var $_cmsName;
    var $_dbConf;
    var $config = array();

    var $log;

    function DownloaderParametersHelper($cmsName = null, $imgName = null)
    {
        $this->log = new LogWriter();
        $this->_cmsName = $cmsName;
        $this->ImgName = $imgName;

        if($this->_cmsName === null)
            $this->log->ToLog('Cms name is null', LogWriter::ERROR);

        $this->ChoiceOfSettings();
    }

    function ChoiceOfSettings()
    {
        $template = $this->GetTemplate();
        $this->pathToImg = dirname($_SERVER["SCRIPT_FILENAME"]) . '/' . $template->pathToImg;
        $this->pathToImgTmp = dirname($_SERVER["SCRIPT_FILENAME"]) . '/' . $template->pathToImgTmp;
        $this->pathToAdditionalFiles = dirname($_SERVER["SCRIPT_FILENAME"]) . '/' . $template->pathToAdditionalFiles;
        $this->watermarkPath = dirname($_SERVER["SCRIPT_FILENAME"]) . '/' . $template->watermarkPath;
        $this->watermarkUse = $template->watermarkUse;
        $this->donot_load_if_exists = $template->donot_load_if_exists;

        $this->config = $template->confProp;
    }

    function GetTemplate()
    {
        $template = null;
        $cms = strtolower($this->_cmsName);

        if($cms == 'customcms')
        {
            $template = new CustomCmsExtractor();
        }
        else if(strstr($cms, "shopscript"))
        {
            $template = new ShopScript5Extractor();
        }
        else if(strstr($cms, "bitrix"))
        {
            $template = new BitrixExtractor();
        }
        else if($cms == 'prestashop')
        {
            $template = new PrestaShopExtractor();
        }
        else if($cms == 'prestashop_image_color')
        {
            $template = new PrestaShopImageColorExtractor();
        }
        else if($cms == 'opencart')
        {
            $template = new OpenCartExtractor();
        }
        else if($cms == 'joomshopping')
        {
            $template = new JoomShoppingExtractor();
        }
        else if($cms == 'amirocms')
        {
            $template = new AmiroCmsExtractor();
        }
        else if($cms == 'hostcms')
        {
            $template = new HostCmsExtractor();
        }
        else if($cms == 'shopcms')
        {
            $template = new ShopCmsExtractor();
        }
        else if($cms == 'simpla')
        {
            $template = new SimplaCmsExtractor();
        }
        else if($cms == 'vamshop')
        {
            $template = new VamShopExtractor();
        }
        else if($cms == 'virtuemart')
        {
            $template = new VirtueMartExtractor();
        }
        else if($cms == 'cs-cart')
        {
            $template = new CSCartExtractor($this->ImgName);
        }
        else
        {
            $this->log->ToLog("Parameter cmsName is specified incorrectly!!!", LogWriter::ERROR);
        }

        if(empty($template->pathToAdditionalFiles))
        {
            $template->pathToAdditionalFiles = $template->pathToImgTmp;
        }
        $this->_dbConf = $template->_db;
        return $template;
    }
}

class TestIP
{
    var $_allowedIP = array();
    var $log;

    function TestIP()
    {
        $currentIp = getenv('REMOTE_ADDR');
        $this->log = new LogWriter();

        if(count($this->_allowedIP) == 0) return true;

        foreach($this->_allowedIP as $ipMask)
        {
            $ipRE = str_replace('.', '\.', $ipMask);
            $ipRE = str_replace('*', '.*', $ipRE);
            $ipRE = str_replace('?', '.{1}', $ipRE);
            $ipRE = '/^'.$ipRE.'$/';
            if(preg_match($ipRE, $currentIp)) return true;
        }
        $error = "[$currentIp] to access denied!";
        $this->log->ToLog($error, LogWriter::ERROR);
    }
}

class SqlQueryHandler
{
    var $_serverDb;
    var $_usernameDb;
    var $_passwordDb;
    var $_nameDb;

    var $_link = NULL;
    var $log;
    var $_mainDb;
    var $_customDb;
    var $_errors;

    function SqlQueryHandler(MainDb $customDb)
    {
        $this->log = new LogWriter();
        $this->_customDb = $customDb;
        $this->QueryProcessingSQL();
    }

    function MainProcess()
    {
        $this->_serverDb = $this->_mainDb->host;
        $this->_usernameDb = $this->_mainDb->user;
        $this->_passwordDb = $this->_mainDb->password;
        $this->_nameDb = $this->_mainDb->dbName;
    }

    function QueryProcessingSQL()
    {
        $PostData = '';
        $result = '';
        $Response = '';

        $fpost = fopen("php://input", "r");
        while ($data = fread($fpost, 1024))
        {
            $PostData .= $data;
        }
        fclose($fpost);

        $REQUEST = new Request($PostData);

        if(CUSTOM_SETTINGS)
        {
            $this->_mainDb = $this->_customDb;
        }
        else
        {
            $template = new DownloaderParametersHelper($REQUEST->CmsName);
            $this->_mainDb = $template->_dbConf;
        }
        $this->MainProcess();

        $sqlParts = explode('[nextsql]', $REQUEST->RequestData);

        foreach($sqlParts as $sqlPart)
        {
            switch($REQUEST->Type)
            {
                case 'sql':
                    switch($REQUEST->ResponseType)
                    {
                        case 'table':
                            $result = $this->_mainDb->GetTable($sqlPart);
                            break;

                        case 'string':
                            $result = $this->_mainDb->GetString($sqlPart);
                            break;

                        case 'serial':
                            $result = serialize($this->_mainDb->GetQueryArray($sqlPart));
                            break;

                        case 'result':
                            $result = $this->_mainDb->ExecuteSQL($sqlPart);
                            break;

                        default:
                            break;
                    }
                    break;

                default:
                    break;
            }
        }


        //$this->log->ToLog('SQL query returned no results!!!');
        //$this->_errors = "Error!!!!!!!!!";
        //$this->log->ToLog(ErrorHandler::ERROR); exit;


        if(ErrorHandler::$ERROR == null)
        {
            $Response = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
            $Response .= "<Response type=\"".$REQUEST->ResponseType."\" version=\"".phpversion()."\">";
            $Response .= "<![CDATA[".preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/','',$result)."]]>";
            $Response .= "</Response>";
        }
        else
        {
            $Response = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
            $Response .= "<Error>" . ErrorHandler::$ERROR . "</Error>";
        }

        $Response = $this->ConvertToUtf8($Response);

        $responseLen = strlen($Response, '8bit');
        if (($responseLen == null) || ($responseLen == ""))
        {
            $responseLen = mb_strlen($Response, '8bit');
        }

        header("Content-Type: text/xml");
        header("Content-Length: " . $responseLen);

        $this->_mainDb->ConnectionClose();
        print $Response;
    }

    function ConvertToUtf8($str)
    {
        if (function_exists('mb_detect_encoding'))
        {
            $encoding = mb_detect_encoding($str, 'UTF-8, windows-1251, koi8-r, UTF-7, ASCII, ISO-8859-1', true);
            if ($encoding != 'UTF-8')
            {
                return mb_convert_encoding($str, 'UTF-8', $encoding);
            }
        }

        return $str;
    }
}

class Request
{
    public $Type;
    public $ResponseType;
    public $RequestData;
    public $CmsName;
    public $log;

    function __construct($requestData)
    {
        $this->log = new LogWriter();

        if(preg_match("/<VSClientRequest type=\"([a-z]+)\" response=\"([a-z]+)\">".
            "<!\[CDATA\[(.*)\]\]><\/VSClientRequest>/ims", $requestData, $matches))
        {
            $this->Type = $matches[1];
            $this->ResponseType = $matches[2];
            $this->RequestData = $matches[3];
        }
        if(preg_match("/<VSClientRequestExtra cms=\"([a-z0-9-]+)\"><\/VSClientRequestExtra>/ims",
            $requestData, $match))
        {
            $this->CmsName = $match[1];
        }
    }
}

class LogWriter
{
    var $_logFileName;
    var $_adminMail;
    var $_logMsg;

    const ERROR = '******* Error *******';

    function LogWriter()
    {
        date_default_timezone_set('Europe/Moscow');
        $this->_logFileName = 'vsclient.log';
        $this->_adminMail = 'support@mydataprovider.com';


        if(!file_exists($this->_logFileName))
        {
            $text = "====================================================================\r\n";
            $text .= "\t\tFile $this->_logFileName is created! " . date('d.m.y H:i:s') . "\r\n";
            $text .= "====================================================================\r\n\n";
            $fp = fopen($this->_logFileName, 'w');
            fwrite($fp, $text);
            fclose($fp);
        }
    }

    function ToLog($logData, $title = '')
    {
        //try
        //{
            if($title == LogWriter::ERROR)
            {
                $logData = '[FAILED]' . $logData;
            }
            $logStr="*** ".date('d.m.y H:i:s')."\t$title\r\n";
            $logStr.=$logData;
            echo $logData . "<br>";

            if(is_writable($this->_logFileName))
            {
                if(!file_put_contents($this->_logFileName, $logStr . "\r\n", FILE_APPEND))
                {
                    throw new Exception("Log can't be written! Free space = " . disk_free_space("."));
                }
            }
            else
            {
                throw new Exception("Log file not writable!");
            }

            if($title == LogWriter::ERROR)
            {
                $this->_logMsg = $logData;
                exit;
            }
        //}
        //catch(Exception $ex)
        //{
            //$this->ToLog($ex->getMessage());
        //}

    }
}

class ImageDownloader
{
    var $_link;
    var $_name;
    var $_relPath;
    var $_requestString;
    var $_cmsName;
    var $_errorString;

    var $_pathToImg;
    var $_pathToImgTmp;
    var $_watermarkPath;
    var $_watermarkUse;
    var $_config = array();
    var $donot_load_if_exists;

    var $_createdFileName;

    var $log;
    protected $curl;

    var $inputWidth = null;
    var $inputHeight = null;

    function MainProcess($params)
    {
        $this->log = new LogWriter();
        $this->donot_load_if_exists = $params->donot_load_if_exists;
        $this->_pathToImg = $params->pathToImg;
        $this->_pathToImgTmp = $params->pathToImgTmp;
        $this->_watermarkPath = $params->watermarkPath;
        $this->_watermarkUse = $params->watermarkUse;
        $this->_config = $params->config;
    }

    function ImageDownloader($link, $name, $params, $cmsName, $requestString = null)
    {
        $this->MainProcess($params);

        if(!$link)
            $this->log->ToLog('Link to the file is not found!', LogWriter::ERROR);
        if(!$name)
            $this->log->ToLog('Image name is null!', LogWriter::ERROR);
        if(!$params)
            $this->log->ToLog('No parameters are specified!', LogWriter::ERROR);
        if(!$cmsName)
            $this->log->ToLog('Name cms is null!', LogWriter::ERROR);

        if($requestString)
        {
            $this->_requestString = $requestString;
            $this->_errorString = "Request: " . $this->_requestString . "<br>";
        }

        //$this->_link = (stripos($link, 'http://') === false && stripos($link, 'https://') === false) ? 'http://' . $link : $link; # Устанавливаем ссылку
        $linkStartWithHttp = preg_match('/^http[s]?/', $link);
        $linkStartWithFtp = preg_match('/^ftp[s]?/', $link);

        $this->_link = (!$linkStartWithHttp && !$linkStartWithFtp) ? 'http://' . $link : $link;

        //NIK161021 $this->_link = str_replace('https://', 'http://', $this->_link);

        $path_parts = $this->pathinfo_utf8($name);
        $this->_name = $path_parts['basename']; # basename($name);
        $this->_relPath = $path_parts['dirname']; # dirname($name);

        /*if ($this->donot_load_if_exists)
        {
            //check whether such image already exists
            $_saveToPath = rtrim($this->_config[0]->saveToPath, "/") . "/".$this->FilterDirPath($this->_relPath) . "/";
            $_saveToPath = rtrim($_saveToPath, "/") . "/";

            $_path = $this->_pathToImg . '/' . rtrim($_saveToPath, "/") . "/";

            $_file = $_path . $this->CreateName($this->_config[0]->renameTo, $this->_name);

            if (file_exists($_file)) {
                die("<br />Such file already exists<br />");
            }
        }*/

        //die("DEBUG FINISH!");
        if (disk_free_space($this->_pathToImg) < 2048 * 1024)
        {
            $this->log->ToLog("Free space '$this->_pathToImg' is less than 2Mb.", LogWriter::ERROR);
        }

        $this->CreateTempFolderIfNotExists();

        $this->tmp = $this->CreateTmp(); # Создаем временный файл

// DEBUG section
        if (IsDebug())
        {
            $this->log->ToLog('TmpFile = ' . $this->tmp);
            $exists = file_exists($this->tmp);
            $this->log->ToLog('TmpFileExists = ' . $exists);
            if ($exists)
            {
                $filesize = filesize($this->tmp);
                $this->log->ToLog('TmpFileSize = ' . $filesize. ' bytes');
            }
        }

        if(!$this->tmp)
        {
            return;
        }

//----------------------------------------------------------------------------------------------------------------------
/* Не рабочий код
        try
        {
            $includePath = ini_get('include_path');

            if(strstr(strtoupper($includePath), "PEAR"))
            {
                // BUG: Методоа finfo_open() нет в PHP до версии 5.3, будет падать с ошибкой которую невозможно затраить
                //$finfo = finfo_open(FILEINFO_MIME_ENCODING); // возвращает mime-тип аля mimetype расширения
                //if (!$finfo)
                //{
                //    $this->_errorString .= "Opening fileinfo database failed";
                //    throw new Exception($this->_errorString);
                //}
                if($finfo)
                {
                    $mimeEncode = finfo_file($finfo, $this->tmp);

                    if($mimeEncode != "binary")
                    {
                        $strFromFile = file_get_contents($this->tmp);
                        $this->_errorString .= "TempFile is not binary. File content: " . strip_tags($strFromFile);
                        unlink($this->tmp);
                        throw new Exception($this->_errorString);
                    }
                }
                else
                {
                   $this->_errorString .= "[Notice]Opening fileinfo database failed<br>\r\n";
                }
            }
        }
        catch (Exception $ex)
        {
            $this->log->ToLog($this->_errorString, LogWriter::ERROR);
        }
*/
//----------------------------------------------------------------------------------------------------------------------
        if ($this->_watermarkUse == true)
        {
            $this->Hookwatermark($this->tmp); # Обрамляем временный файл ватермарком
        }

        $temp_file = fopen($this->tmp, 'rb');

        if(!$temp_file)
        {
            $this->log->ToLog('Did not open temp file.', LogWriter::ERROR);
        }

        foreach ($this->_config as $conf)
        {
            //added by gon
            $conf->saveToPath = rtrim($conf->saveToPath, "/") . "/".$this->FilterDirPath($this->_relPath) . "/";
            $conf->saveToPath = rtrim($conf->saveToPath, "/") . "/";

            $path = rtrim($this->_pathToImg, "/") . '/' . trim($conf->saveToPath, "/");

            // $this->log->ToLog("Path = $path.", LogWriter::ERROR);
            try
            {
                if ((is_dir($path) && is_writable($path)) || $this->_cmsName == "cs-cart")
                {
                    if(!$this->UProcess($conf, $temp_file, true))
                    {
                        $this->_errorString .= "Image " . $this->_name . " was not resized";
                        throw new Exception($this->_errorString);
                    }
                }
                else
                {
                    mkdir($path, 0777, true);
                    if(!$this->UProcess($conf, $temp_file))
                    {
                        $this->_errorString .= "Image " . $this->_name . " was not resized";
                        throw new Exception($this->_errorString);
                    }
                }
                //$newfile = rtrim($path, "/") . "/" . trim($this->_name, "/");

                if (!file_exists($this->_createdFileName))
                {
                    $this->log->ToLog("File $this->_createdFileName was not downloaded from $link", LogWriter::ERROR);
                    //die("<br />File $newfile was not downloaded from <a href=\"$link\">$link</a><br />");
                }
            }
            catch(Exception $ex)
            {
                $this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
                break;
            }
        }

        fclose($temp_file) or $this->log->ToLog('Did not close temp file.', LogWriter::ERROR);

        if (!$this->donot_load_if_exists)
        {
            $this->DelTmp();
        }


        //$this->log->ToLog('File was loaded.');
    }

    function CreateTempFolderIfNotExists()
    {
        if(!file_exists($this->_pathToImgTmp))
        {
            if (!mkdir($this->_pathToImgTmp, 0777, true))
            {
                $this->log->ToLog("Can`t create temp = $this->_pathToImgTmp", LogWriter::ERROR);
            }
        }
    }

    function UProcess($conf, $temp_file, $n_maked = false)
    {
        if ($n_maked === false)
        {
            $path = $this->_pathToImg . '/' . trim($conf->saveToPath, '/') . '/';

            if (!is_dir($path) || !is_writable($path))
            {
                $this->log->ToLog('There is no file with name :' . $path . ' or folder is not accessible for writing.', LogWriter::ERROR);
            }
        }

        return $this->ImageResize($conf);
    }

    function ImageResize($conf)
    {
        try
        {
            /**
             * получаем размеры изображения:
             *  $sourceWidth
             *  $sourceHeight
             * */
            list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($this->tmp);

            if (!($sourceWidth || $sourceHeight))
            {
                $this->_errorString .= "Width or height of " . $this->tmp . " not defined!<br>\r\n ";
                throw new Exception($this->_errorString);
            }

            if ($conf->resizeToWidth == null)
            {
                $destWidth = $sourceWidth;
            }
            else
            {
                $destWidth = (int)$conf->resizeToWidth;
            }

            if (($conf->resizeToHeigth == null) && $conf->resizeToWidth != null)
            {
                $destHeight = intval(($conf->resizeToWidth / $sourceWidth) * $sourceHeight);
                //[%width%]/[%height%]
                $conf->saveToPath = str_replace("[%width%]", $destWidth, $conf->saveToPath);
                $conf->saveToPath = str_replace("[%height%]", $destHeight, $conf->saveToPath);
            }
            else if ($conf->resizeToHeigth == null)
            {
                $destHeight = $sourceHeight;
            }
            else
            {
                $destHeight = (int)$conf->resizeToHeigth;
            }

            $fullPath = $this->_pathToImg . $conf->saveToPath;
            if(!file_exists($fullPath))
            {
                if (!mkdir($fullPath, 0777, true))
                {
                    $this->log->ToLog("Can`t create temp = $conf->saveToPath", LogWriter::ERROR);
                }
            }

            $this->_createdFileName = $fullPath;
            $fileType = $this->ReturnExtByType($type);
            $loadedFile = $this->_pathToImg . '/' . $conf->saveToPath . $this->CreateName($conf->renameTo, $this->_name);

            if (($conf->resizeToHeigth == null) && ($conf->resizeToWidth == null))
            {
                copy($this->tmp, $loadedFile);
                // $this->ReturnDestImage($fileType, $destImage, $loadedFile);
            }
            else
            {
                // tmp у нас это как полное имя до временного файла.
                $sourceImage = $this->createSrcImage($type, $this->tmp);

                $widthDiff = $destWidth / $sourceWidth; // Вычисляем отношение требуемых размеров к тем, которые имеем
                $heightDiff = $destHeight / $sourceHeight; // Вычисляем отношение требуемых размеров к тем, которые имеем

                if ($widthDiff > 1 and $heightDiff > 1) {
                    $nextWidth = $sourceWidth;
                    $nextHeight = $sourceHeight;
                }
                else
                {
                    if ($widthDiff > $heightDiff) // Если расхождение ширины больше высоты
                    {
                        $nextHeight = $destHeight;
                        $nextWidth = intval(($sourceWidth * $nextHeight) / $sourceHeight);
                        $destWidth = (intval(0) == 0 ? $destWidth : $nextWidth);
                    }
                    else
                    {
                        if ($widthDiff < $heightDiff)
                        {
                            $nextWidth = $destWidth; // Новая ширина = Старой ширине (так как расхождение ширины < высоты)
                            $nextHeight = intval($sourceHeight * $destWidth / $sourceWidth);
                            $destHeight = (intval(0) == 0 ? $destHeight : $nextHeight);
                        }
                        else
                        {
                            $nextWidth = $sourceWidth * $widthDiff;
                            $nextHeight = $sourceHeight * $heightDiff;
                        }
                    }
                }
                /*
                        if ($widthDiff > 1 && $heightDiff > 1 and ($conf->backGroundFill === true))
                        {
                            $borderWidth = intval(($destWidth - $nextWidth) / 2);
                            $borderHeight = intval(($destHeight - $nextHeight) / 2);
                        }
                        else
                        {
                            $borderWidth = 0;
                            $borderHeight = 0;
                        }

                        $borderWidth = (int)(($destWidth - $nextWidth) / 2);
                        $borderHeight = (int)(($destHeight - $nextHeight) / 2);
                */

                if ((($widthDiff > 1) || ($heightDiff > 1)) and ($conf->backGroundFill === true))
                {
                    $borderWidth = intval(($destWidth - $nextWidth) / 2);
                    $borderHeight = intval(($destHeight - $nextHeight) / 2);
                }
                else if(($widthDiff <= 1) && ($heightDiff <= 1) && ($conf->backGroundFill === true))
                {
                    if($widthDiff < $heightDiff)
                    {
                        $borderWidth = 0;
                        $borderHeight = intval(($destHeight - $nextHeight) / 2);

                    }
                    else if($widthDiff > $heightDiff)
                    {
                        $borderWidth = intval(($destWidth - $nextWidth) / 2);
                        $borderHeight = 0;
                    }
                    else
                    {
                        $borderWidth = 0;
                        $borderHeight = 0;
                    }
                }
                else
                {
                    $borderWidth = 0;
                    $borderHeight = 0;
                }

                # Отрицательный размер отрезает часть картинки
                if ($borderWidth < 0)
                {
                    $borderWidth =  0;
                }

                if ($borderHeight < 0)
                {
                    $borderHeight = 0;
                }

                $destImage = ($conf->backGroundFill === true)
                    ? imagecreatetruecolor($destWidth, $destHeight) //250x250
                    : imagecreatetruecolor($nextWidth, $nextHeight);//250x96

                // Заполнение фона: Чёрный = (0,0,0); Белый — (255,255,255)
                $colorOfFill = $this->getPixColor($sourceImage, 1, 1);

                $fillColor = imagecolorallocatealpha(
                    $destImage,
                    $colorOfFill['r'],
                    $colorOfFill['g'],
                    $colorOfFill['b'],
                    0 // alpha - прозрачность: 0 означает непрозрачный цвет, 127 означает полную прозрачность
                );

                imagealphablending($destImage, false);
                imagesavealpha($destImage, true);

                //$white = imagecolorallocate($destImage, 255, 255, 255);
                imagefill($destImage, 0, 0, $fillColor);
                imagecolortransparent($destImage, $fillColor);

                imagecopyresampled($destImage, $sourceImage, $borderWidth, $borderHeight, 0, 0,
                    $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);

                $this->ReturnDestImage($fileType, $destImage, $loadedFile);
            }

            if (!file_exists($loadedFile))
            {
                $this->log->ToLog('File ' . $loadedFile . ' was not loaded. Maybe there is no free space.');
            }

            if($this->inputWidth === null &&  $this->inputHeight === null)
            {
                $this->inputWidth = $sourceWidth;
                $this->inputHeight = $sourceHeight;
                $this->log->ToLog('[[[OriginalImage Url = '.$this->_link.
                    ' Width = '.$this->inputWidth.
                    ' Height = '.$this->inputHeight.']]]');
            }

            $this->log->ToLog('[[[ImageCreated FileName = '.$this->CreateName($conf->renameTo, $this->_name).
                ' Width = ' . $destWidth .
                ' Height = ' . $destHeight . ']]]');
        }
        catch(Exception $ex)
        {
            $this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
            return false;
        }
        return true;
    }

    function ReturnExtByType($type)
    {
        if (empty($type))
            return 'jpg';

        switch ($type) {
            case 1:
                return 'gif';
                break;
            case 3:
                return 'png';
                break;
            case 2:
            default:
                return 'jpg';
                break;
        }
    }

    function pathinfo_utf8($path_file)
    {
        $path_file = strtr($path_file,array('\\'=>'/'));

        preg_match("~[^/]+$~",$path_file,$file);
        preg_match("~([^/]+)[.$]+(.*)~",$path_file,$file_ext);
        preg_match("~(.*)[/$]+~",$path_file,$dirname);

        return array('dirname' => $dirname[1],
        'basename' => $file[0],
        'extension' => (isset($file_ext[2]))?$file_ext[2]:false,
        'filename' => (isset($file_ext[1]))?$file_ext[1]:$file[0]);
    }

    function ReturnDestImage($type, $ressource, $filename)
    {
        $flag = false;

        switch ($type) {
            case 'gif':
                $flag = imagegif($ressource, $filename);
                break;
            case 'png':
                $flag = imagepng($ressource, $filename);
                break;
            case 'jpg':
            default:
                $flag = imagejpeg($ressource, $filename, 96);
                break;
        }
        imagedestroy($ressource);

        return $filename;
    }

    protected function IsCurlInstalled()
    {
        if  (in_array  ('curl', get_loaded_extensions()))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function CurlGet($url)
    {
        if (!$this->IsCurlInstalled())
        {
            $this->log->ToLog('PHP installed without cURL support, I cant download files!', LogWriter::ERROR);
        }

        $this->curl = curl_init();

        $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko'; // 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36';

        $header[0] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en";
        
        $curlOptions = array(
            CURLOPT_URL=>str_replace(' ', '%20', $url),
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_USERAGENT=>$agent,

            CURLOPT_AUTOREFERER=>true,//

            CURLOPT_CONNECTTIMEOUT=>10,
            CURLOPT_TIMEOUT=>600,
            CURLOPT_HEADER=>0, // При включении эта опция пишет заголовки ответа в результирующий файл. Только для дебага - фото битые будут
            CURLOPT_ENCODING=>'',
            CURLOPT_FOLLOWLOCATION=>true,
            CURLOPT_MAXREDIRS=>20,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_HTTPHEADER=>$headers
            //CURLOPT_RETURNTRANSFER=>1
        );

        curl_setopt_array($this->curl, $curlOptions);

        $result = curl_exec($this->curl);
        $status = curl_getinfo($this->curl);

        if($status['http_code']!=200)//NIK160211 http://stackoverflow.com/questions/21233771/php-curl-function-301-error
        {
            if($status['http_code'] == 301 || $status['http_code'] == 302)
            {
                list($header) = explode("\r\n\r\n", $result, 2);

                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $header_size);
                $this->log->ToLog('header'.$header, 'info');
                $matches = array();
                preg_match("/(Location:|URI:)[^(\n)]*/", $header, $matches);
                $url = trim(str_replace($matches[1],"",$matches[0]));
                $url_parsed = parse_url($url);
                $this->log->ToLog('Redirect 301 or 302 url ='.$url.' url_parsed = '.$url_parsed, 'info');

                if (isset($url_parsed)) 
                {
                    return CurlGet($url);
                }
            }
            
            throw new Exception(curl_error($this->curl) /* . var_dump($status) */);
        }

        return $result;
    }

    public function FilterDirPath($path)
    {
        $p = explode("/", $path);
        $result = "";
        foreach ($p as $chain) {
            // preg_match("/^[a-zA-Z0-9_-]$/", $chain, $match)) << Этот регэксп не будет работать, т.к. тут нет кода переноса строки!!! Тестируйте!!! псправлено!
            if (($chain || $chain === "0") && preg_match("/^[a-zA-Z0-9_-]/", $chain)) {
                $result .= $chain."/";
            }
        }
        return rtrim($result, "/");
    }

    function CreateTmp()
    {
        try
        {
                $nameTmpFile = rtrim($this->_pathToImgTmp, "/") . '/tmp_' . basename($this->_name);

                #$this->log->ToLog('temp file: '.$nameTmpFile, 'info');
                #$this->log->ToLog('source link: '.$this->_link, 'info');
                if (IsDebug())
                {
                    $this->log->ToLog('$this->donot_load_if_exists = '. $this->donot_load_if_exists);
                }
                
                if ($this->donot_load_if_exists)
                {
                    if (file_exists($nameTmpFile))
                    {
                        if (IsDebug())
                        {
                            $this->log->ToLog('TmpFile Was so fresh downloading was skipped');
                        }
                        return $nameTmpFile;
                        /*
                        $this->log->ToLog('exists:'.$nameTmpFile, 'info');
                        $handle = fopen($nameTmpFile, "rb");
                        $data = fread($handle, filesize($nameTmpFile));
                        fclose($handle);*/
                    }
                    //$data = $this->CurlGet($this->_link);
                }
                //else
                //{
                //    $this->log->ToLog('donot_load_if_exists is false ', 'info');
                //}

                //$data = null;
                //if($data == null)
                //{
                if (IsDebug())
                {
                    $this->log->ToLog('real data downloading from url: ' . $this->_link);
                }
                $data = $this->CurlGet($this->_link);
                //}

          //$imagesize = getimagesize($data);

          if(!$data)
          {
              $this->_errorString .= $this->_link . ' was not downloaded! Maybe broken url!';
              throw new Exception($this->_errorString);
          }
          /*
          else if(!$imagesize[0])
          {
              if($this->getImagen($this->_link, $nameTmpFile))
              {
                  return $nameTmpFile;
              }
              else
              {
                  $error = curl_error($this->curl);
                  $this->_errorString .= "The downloaded file \"" . $this->_link . "\" is corrupted! " . $error;
                  throw new Exception($this->_errorString);
              }
          }
          */
        }
        catch(Exception $ex)
        {
            $this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
            return null;
        }

        $this->fpc($nameTmpFile, $data);

        return $nameTmpFile;
    }

    function DelTmp()
    {
        try
        {
            if(!unlink($this->tmp))
            {
                $this->_errorString .= "Temporary file " . $this->tmp . " was not deleted!";
                throw new Exception($this->_errorString);
            }
        }
        catch(Exception $ex)
        {
            $this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
        }

    }

    function fpc($path, $data)
    {
        try
        {
            $fp = fopen($path, 'w') or $this->log->ToLog('Did not open file: ' . $path, LogWriter::ERROR);
            if(!fwrite($fp, $data))
            {
                $this->_errorString .= "I can not write a data to " . $fp;
                throw new Exception($this->_errorString);
            }
            if(!fclose($fp))
            {
                $this->_errorString .= "I can not close a file " . $fp;
                throw new Exception($this->_errorString);
            }
        }
        catch(Exception $ex)
        {
            $this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
            return false;
        }
        return true;
    }

    function CreateName($p, $name)
    {
        /*
        imageName=1989%2f9H.A2M01.8AE.jpg
        $t = (strpos($name, '.') !== false) ? explode('.', $name) : array(0 => $name);
        echo "p = $p, name = $name, t = $t[0], $t[1]";
        return (empty($p) || $p == null) ? $t[0] . '.' . $t[1] : str_ireplace('{0}', $t[0], $p) . '.' . $t[1];
        */

        $path_parts = $this->pathinfo_utf8($name);
        $newName = (empty($p) || $p == null) ? $name : str_ireplace('{0}', $path_parts['filename'], $p) . '.' . $path_parts['extension'];
        return $newName;
    }

    function Hookwatermark($file, $x = 'middle', $y = 'middle')
    {
        return $this->WatermarkByImage($file, $this->_watermarkPath, $file, $x, $y);
    }

    function WatermarkByImage($imagepath, $watermarkpath, $outputpath, $xAlign, $yAlign)
    {
        $Xoffset = $xpos = $ypos = 0;
        $Yoffset = 0;
        $image = null;
        if(exif_imagetype($imagepath) == IMAGETYPE_PNG)
        {
            if (!$image = imagecreatefrompng($imagepath))
            {
                return false;
            }
        }
        else if(exif_imagetype($imagepath) == IMAGETYPE_JPEG)
        {
            if (!$image = imagecreatefromjpeg($imagepath))
            {
                return false;
            }
        }
        if (!$imagew = imagecreatefromgif($watermarkpath))
            $this->log->ToLog('The watermark image is not a real gif, please CONVERT the image.');
        list($watermarkWidth, $watermarkHeight) = getimagesize($watermarkpath);
        list($imageWidth, $imageHeight) = getimagesize($imagepath);
        if (($imageWidth >= 800 && $imageHeight > 120) || ($imageWidth > 120 && $imageHeight >= 800))
        {
            if ($xAlign == 'middle')
                $xpos = $imageWidth / 2 - $watermarkWidth / 2 + $Xoffset;
            if ($xAlign == 'left')
                $xpos = 0 + $Xoffset;
            if ($xAlign == 'right')
                $xpos = $imageWidth - $watermarkWidth - $Xoffset;
            if ($yAlign == 'middle')
                $ypos = $imageHeight / 2 - $watermarkHeight / 2 + $Yoffset;
            if ($yAlign == 'top')
                $ypos = 0 + $Yoffset;
            if ($yAlign == 'bottom')
                $ypos = $imageHeight - $watermarkHeight - $Yoffset;
            if (!imagecopymerge($image, $imagew, $xpos, $ypos, 0, 0, $watermarkWidth, $watermarkHeight, 100))
                return false;
        }

        return imagejpeg($image, $outputpath, 96);
    }

    function getPixColor($pImage, $pX, $pY)
    {
        // Получаем цвет изображения
        $rgb = imagecolorat($pImage, $pX, $pY);
        // Преобразуем
        $red = ($rgb >> 16) & 0xFF;
        $green = ($rgb >> 8) & 0xFF;
        $blue = $rgb & 0xFF;
        // Преобразуем alpha, так как в PNG 127 это полная прозрачность, а 0 - не прозрачность
        $alpha = abs((($rgb >> 24) & 0xFF) / 127 - 1);
        return array('r' => $red, 'g' => $green, 'b' => $blue, 'a' => $alpha);
    }

    function createSrcImage($type, $filename)
    {
        try
        {
            switch ($type) {
                case 1:
                    if($src = imagecreatefromgif($filename))
                    {
                        return $src;
                    }
                    else
                    {
                        $this->_errorString .= "Cannot create image from GIF!";
                        throw new Exception($this->_errorString);
                    }
                    break;
                case 3:
                    if($src = imagecreatefrompng($filename))
                    {
                        imagealphablending($src, false);
                        imagesavealpha($src, true);
                        $fillColor = $this->getPixColor($src, 1, 1);
                        $color = imagecolorallocatealpha($src, $fillColor['r'], $fillColor['g'], $fillColor['b'], $fillColor['a']);
                        // imagefill($src, 0, 0, $color);
                        imagefill($src, 0, 0, 0xffffff); // white background
                        return $src;
                    }
                    else
                    {
                        $this->_errorString .= "Cannot create image from PNG!";
                        throw new Exception($this->_errorString);
                    }

                    break;
                case 2:
                default:
                    if($src = imagecreatefromjpeg($filename))
                    {
                        return $src;
                    }
                    else
                    {
                        $this->_errorString .= "Cannot create image from JPEG!";
                        throw new Exception($this->_errorString);
                    }
                    break;
            }
        }
        catch(Exception $ex)
        {
            $this->log->ToLog($ex->getMessage(), LogWriter::ERROR);
        }
    }
}

class AdditionalFileDownloader
{
    private $_fileUrl;
    private $_fileName;
    private $_pathToFilesStorage;
    private $_log;

    function AdditionalFileDownloader($fileUrl, $fileName, $parameters)
    {
        $this->_fileUrl = $fileUrl;
        $this->_fileName = $fileName;
        $this->_pathToFilesStorage = $parameters->pathToAdditionalFiles;
        $this->_log = new LogWriter();
    }

    function RemoteFileDownload()
    {
        $fileContent = file_get_contents($this->_fileUrl);
        if(!file_exists($this->_pathToFilesStorage))
        {
            $this->_log->ToLog("Directory \"" . $this->_pathToFilesStorage . "\" not exists!", LogWriter::ERROR);
        }
        if(!$fileContent)
        {
            $this->_log->ToLog("\"" . $this->_fileName . "\" was not downloaded!", LogWriter::ERROR);
        }
        else
        {
            $fullFileLocalName = $this->_pathToFilesStorage . '/' . $this->_fileName;
            if(!file_put_contents($fullFileLocalName, $fileContent))
            {
                $this->_log->ToLog("I cannot write data to the file!", LogWriter::ERROR);
            }
            $mimeType = mime_content_type($fullFileLocalName);
            $fileSize = filesize($fullFileLocalName);
            $this->_log->ToLog("[[[OriginalFile Url = $this->_fileUrl Length = $fileSize MIMEType = $mimeType]]]");
        }
    }
}

$testIP = new TestIP();
$log = new LogWriter();

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $imageUrl = isset($_GET['imageUrl']) ? trim($_GET['imageUrl']) : null; # init image url for dwn
    /*
     * Адреса картинок не нужно дополнительно перекодировать, правильные адреса, дважды кодированные, должны подаваться в аргументах URL
     * Для тестов раскомментировать следующую строку, адрес должен быть открываемом в браузере!
     *    die($imageUrl);
     */
    $imageName = isset($_GET['imageName']) ? trim($_GET['imageName']) : null; # init image name for save
    $fileName = isset($_GET['fileName']) ? trim($_GET['fileName']) : null;
    $fileUrl = isset($_GET['fileUrl']) ? trim($_GET['fileUrl']) : null;
    $cmsName = isset($_GET['cmsName']) ? trim($_GET['cmsName']) : null;
    $mode = isset($_GET['mode']) ? trim($_GET['mode']) : null;
    $request = isset($_GET['request']) ? trim($_GET['request']) : null;
    $versionApp = isset($_GET['app_version']) ? trim($_GET['app_version']) : null;

    $allRequest = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

      if (IsDebug())
      {
          $log->ToLog("isdebug enabled");
      }

    if($mode == 'test')
    {
        print "[[-available-]]";
    }
    else if($request == 'prefix')
    {
        print DbPrefixOfTemplateGet($imgParam, $cmsName);
    }
    else if($request == 'phpinfo')
    {
        print PhpInfoGet();
    }
    else if($request == 'extensions')
    {
        print PhpExtensionsGet();
    }
    else if($versionApp != null)
    {
        if(AppSupportPeriodIsExpired($CatalogloaderAppVersionCheck, $versionApp))
        {
            print "[[-expired-]]";
        }
    }
    else
    {
        if(empty($imageUrl) && empty($imageName) && empty($fileName) &&
            empty($fileUrl) && empty($cmsName) && empty($mode) && empty($request))
        {
            print "[SUCCESS] Connection is established, but parameters was not found!";
            print "<br>Useful parameters for test:
<br>- ?request=phpinfo
<br>- ?request=extensions
<br>- ?isdebug=1";
        }
        else if(!empty($fileName) && !empty($fileUrl))
        {
            $params = GetParamsOfTemplate($imgParam, $cmsName, null);
            $fileDownloader = new AdditionalFileDownloader($fileUrl, $fileName, $params);
            $fileDownloader->RemoteFileDownload();
        }
        else
        {
            if((mb_strtolower($cmsName) == 'prestashop') && (stripos(mb_strtolower($imageName), '/co/')))
            {
                $cmsName = prestashop_image_color;
            }
            $params = GetParamsOfTemplate($imgParam, $cmsName, $imageName);
            $imageDownloader = new ImageDownloader($imageUrl, $imageName, $params, $cmsName, $allRequest);
        }
    }
}
else
{
    $sqlQueryHandler = new SqlQueryHandler($imgParam->_dbConf);
}

function GetParamsOfTemplate($imgParam, $cmsName, $imgName)
{
    return $params = (CUSTOM_SETTINGS) ? $imgParam : new DownloaderParametersHelper($cmsName, $imgName);
}

function DbPrefixOfTemplateGet($imgParam, $cmsName)
{
    if(CUSTOM_SETTINGS && !empty($imgParam->_dbConf->dbPref))
    {
        return $imgParam->_dbConf->dbPref;
    }
    else
    {
        $parameters = new DownloaderParametersHelper($cmsName);
        return $parameters->_dbConf->dbPref;
    }
}

function IsDebug()
{
    return isset($_GET['isdebug']);
}

function PhpInfoGet()
{
    if (disk_free_space(".") < 1024 * 1024) {
        echo '<p align="center" style="color:#ff0000">Free space on current drive is less than 1Mb and will end soon.</p>';
    }
    else if (disk_free_space(".") < 50 * 1024 * 1024) {
        echo '<p style="color:#ff9f00">Free space on current drive is less than 50Mb and will end soon.</p>';
    }
    else {
        echo "<p>Free space on current drive is " . disk_free_space(".") . " bytes</p>";
    }

    phpinfo();
}

function PhpExtensionsGet()
{
    $list = get_loaded_extensions();
    $list2 = array_map('strtolower',$list);
    sort($list2);
    echo '<pre>'.print_r($list2,true).'</pre>';
}

function AppSupportPeriodIsExpired($supportDate, $currentAppVersion)
{
    $result = false;
    if($supportDate != null)
    {
        $result = strtotime($supportDate) < strtotime($currentAppVersion);
    }
    return $result;
}
