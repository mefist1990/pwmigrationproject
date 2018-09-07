<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available tbrough the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it tbrough the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
require_once(dirname(__FILE__).'/tables.php'); 

if (!defined('_PS_VERSION_')) {
    exit;
}

class Pwmigrationproject extends Module
{
    
    const DB_FILE_PATH = __DIR__.'/tmp/db.htm';

    
    protected $config_form = false;

    public function __construct()
    {
        
        $this->name = 'pwmigrationproject';
        $this->tab = 'migration_tools';
        $this->version = '0.1.0';
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Migration Project PW';
        $this->description = 'Модуль для миграции данных, между магазинами';

        $this->confirmUninstall = 'Вы уверены что хотите удалить?';

        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
    }


    public function install()
    {

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
    
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        

        /**
         * If values have been submitted in the form, process.
         */
        $information = "";
        $checking_tables = 0;
        if (((bool)Tools::isSubmit('submitPwmigrationprojectModuleProductBackup')) == true) {
            $information = $this->postProcessProductBackup();
        }

        if (((bool)Tools::isSubmit('submitPwmigrationprojectModuleProductRestore')) == true) {
            $information = $this->postProcessProductRestore();
        }
        if (((bool)Tools::isSubmit('submitPwmigrationprojectModuleDBReset')) == true) {
            $information = $this->postProcessDBReset();
        }
        if (((bool)Tools::isSubmit('submitPwmigrationprojectModuleCheckingTables')) == true) {
            $data_checking_tables = $this->postCheckingTables($checking_tables);
            $information = $data_checking_tables[0];
            $checking_tables = $data_checking_tables[1];
        }

        if (!file_get_contents(self::DB_FILE_PATH)) $empty_file = 0;
        else $empty_file = 1;
        $this->context->smarty->assign('module_dir', $this->_path);

        $this->context->smarty->assign(
            array(
                'ps_version' => _PS_VERSION_,
                'empty_file' => $empty_file,
                'information' => $information,
                'checking_tables' => $checking_tables
                
            )
        );


        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }

   


    /**
     * Save form data.
     */
    protected function postProcessProductBackup()
    {
        
        $information = "";
        $fopen_db = fopen(self::DB_FILE_PATH, "w");
        fclose($fopen_db);
        foreach (PsTables::$ps_17_tables as $name) {
            $information = $this->fileDBWrite($name, $information);
        }
   
        return $information;
        
    }

    protected function postProcessProductRestore()
    {
        $information .= 'postProcessProductRestore<br>';
        return $information;
    }

    protected function postCheckingTables($checking_tables)
    {

        $information = "";
       
        foreach (PsTables::$ps_17_tables as $key => $name) {

            preg_match("'<".$name.">(.*?)</".$name.">'si", file_get_contents(self::DB_FILE_PATH), $json); //находим json строку из файла DB

            if (count($json) > 0) 
                {
                    $columns_one = $columns_two = "";
                    $table_array = json_decode($json[1]); //восстанавливаем массив таблицы $name из файла
                    $array_db = (array)$table_array[0]; //промежуточная переменная
                    $columns_one = array_column(Db::getInstance()->executeS('SHOW COLUMNS FROM '._DB_PREFIX_.$name), 'Field'); //получаем значения полей таблицы  $name  из Базы данный нового сайта
                    $columns_two = array_keys($array_db); //получаем значения полей таблицы  $name  из файла DB
                    $columns_one_count = count($columns_one);
                    $columns_two_count = count($columns_two);

                    if ($columns_one_count == $columns_two_count) 
                        {
                            $information .= '<p class="bg-success">Данные '.$name.' можно восстановить в базу данных, проблем с таблицей нет</p><br>';
                        }
                    else
                        {
                            $checking_tables--;
                            $w = max(count($columns_one), count($columns_two)); //промежуточная переменная
                            $columns_one_new = array_pad($columns_one, $w, 0); //уравниваем два массива
                            $columns_two_new = array_pad($columns_two, $w, 0); //уравниваем два массива
                            
                            if ($columns_one_count > $columns_two_count) {
                                
                                $problem_fields = array_diff($columns_one_new, $columns_two_new);

                            }

                            if ($columns_one_count < $columns_two_count) {
                                
                                $problem_fields = array_diff($columns_two_new, $columns_one_new);

                            }

                            $problem_fields_json = json_encode($problem_fields);
                            $information .= '<p class="bg-danger">Проблема с таблицей '.$name.', проблема с полями: ' .$problem_fields_json. '  </p><br>';


                        }
                }
        }
        return array($information, $checking_tables);

    }

    protected function fileDBWrite($name, $information)
    {
        if ($data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.$name)){
        $json = json_encode($data);
        $fopen_db = fopen(self::DB_FILE_PATH, "a");
        $fwrite_db = fwrite($fopen_db, '<'.$name.'>'.$json.'</'.$name.'>');
        if ($fwrite_db) $information .= '<p class="bg-success">Данные '.$name.' в файл успешно занесены </p><br>';
        else $information .= '<p class="bg-danger">Ошибка '.$name.' при записи в файл </p><br>';
        fclose($fopen_db); //Закрытие файла
        return $information;
        }
        else return $information;
    }

    protected function postProcessDBReset()
    {
        $fopen_db = fopen(self::DB_FILE_PATH, "w");
        fclose($fopen_db);
        $information = '<p class="bg-success">Файл DB очищен.</p>';
        return $information;

    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

}
