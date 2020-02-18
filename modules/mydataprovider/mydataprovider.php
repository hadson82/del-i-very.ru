<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class mydataprovider extends Module
{
  public function __construct()
  {
    $this->name = 'mydataprovider';
    $this->tab = 'administration';
    $this->version = '1.0.0';
    $this->author = 'mydataprovider';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.4', 'max' => _PS_VERSION_); 
    $this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = $this->l('Mass direct data import from free data web scraping tool V4.IDE (mydataprovider)');
    $this->description = $this->l('Module is intended to import data from web scraping tool V4.IDE into prestashop database.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
 
    if (!Configuration::get('MYMODULE_NAME'))      
      $this->warning = $this->l('No name provided');
  }
  
    public function install()
    {
        if (!parent::install())
            return false;
        $file_scripts  = "http://catalogloader.com/downloads/Scripts.zip";
        $temp_path = $this->local_path."temp/";
        $fp = @fopen($file_scripts, "rb");
        if(!$fp)
            return false;
        $basename = basename($file_scripts);

        if(!file_exists($temp_path))  
            mkdir($temp_path);
            
        if(file_exists($temp_path.$basename))
            unlink($temp_path.$basename);

        $fd = fopen($temp_path.$basename,'w');

        if ($fp && $fd) {
            while (!feof($fp)) {
                $st = fread($fp, 4096);
                fwrite($fd, $st);
            }
        }
        @fclose($fp);
        @fclose($fd);

        $zip = new ZipArchive;
        $file = realpath($temp_path.$basename);
        $res = $zip->open($file);
        if ($res === TRUE) {
            $zip->extractTo($temp_path);
            $zip->close();
        } else {
            return false;
        }

        $this->read_dir($temp_path, array($basename));
        
        $this->dirDel($temp_path);
        
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        )
        return false;
        
        $this->dirDel($this->local_path, array(mb_strtolower($this->name).".php"), false);
        
        
        return true;
    }    
    private function read_dir($path, $exlfiles = array()){
        if(!is_dir($path))
            return false;
        
        if($curdir = opendir($path)) {
            while($file = readdir($curdir)) {
                if($file != '.' && $file != '..' && !in_array($file, $exlfiles) && $file != "tmp"){
                    if(is_file($path."/".$file) OR $file == ".htaccess") {
                        copy($path."/".$file, $this->local_path."/".$file);            
                    }
                    elseif(is_dir($path."/".$file)){
                        $this->read_dir($path."/".$file);    
                    }
                  
                }
            }
        }
    }
    
    private function dirDel ($dir, $exldel = array(), $delALL = true)
    { 
        $d=opendir($dir); 
        while(($entry=readdir($d))!==false)
        {
            if ($entry != "." && $entry != ".." && !in_array($entry, $exldel))
            {
                if (is_dir($dir."/".$entry))
                { 
                    $this->dirDel($dir."/".$entry); 
                }
                else
                { 
                    unlink ($dir."/".$entry); 
                }
            }
        }
        closedir($d); 
        if($delALL)
            rmdir ($dir); 
    }
    
    public function getContent()
    {
        $output = null;
        return $output.$this->displayForm();
    }
    
    
    public function displayForm()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
         
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Information'),
            ),
            'input' => array(
                array(
                    'label' => $this->l('Send this link to mydataprovider'),
                    'type' => "text",
                    'name' => 'mdp_link',
                    'size' => 100,
                    'required' => true
                )
            )
        );
         
        $helper = new HelperForm();
         
        // Module, t    oken and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
         
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
         
        // Load current value
        $helper->fields_value['mdp_link'] = $this->context->link->protocol_link.$this->context->shop->domain.$this->_path."catalogloader_handler.php";
         
        return $helper->generateForm($fields_form);
    }

}

?>