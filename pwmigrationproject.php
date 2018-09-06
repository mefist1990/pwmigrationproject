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
require_once(dirname(__FILE__).'/vendor/autoload.php'); 
use DiDom\Document;
use DiDom\Query;

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

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
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

        if (((bool)Tools::isSubmit('submitPwmigrationprojectModuleProductBackup')) == true) {
            $information = $this->postProcessProductBackup();
        }

        if (((bool)Tools::isSubmit('submitPwmigrationprojectModuleProductRestore')) == true) {
            $information = $this->postProcessProductRestore();
        }
        if (((bool)Tools::isSubmit('submitPwmigrationprojectModuleDBReset')) == true) {
            $information = $this->postProcessDBReset();
        }

        if (!file_get_contents(self::DB_FILE_PATH)) $empty_file = 0;
        else $empty_file = 1;
        $this->context->smarty->assign('module_dir', $this->_path);

        $this->context->smarty->assign(
            array(
                'ps_version' => _PS_VERSION_,
                'empty_file' => $empty_file,
                'information' => $information
                
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

        $json_product = 
        $json_product_attachment = 
        $json_product_attribute = 
        $json_product_attribute_combination = 
        $json_product_attribute_image = 
        $json_product_attribute_shop =
        $json_product_carrier =
        $json_product_country_tax =
        $json_product_download =
        $json_product_group_reduction_cache =
        $json_product_lang =
        $json_product_sale =
        $json_product_shop =
        $json_product_supplier =
        $json_product_tag = "";


        $fopen_db = fopen(self::DB_FILE_PATH, "w");
        fclose($fopen_db);


        if ($product_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product')){
            $json_product = json_encode($product_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product>'.$json_product.'</product>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_attachment_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_attachment')){
            $json_product_attachment = json_encode($product_attachment_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_attachment>'.$json_product_attachment.'</product_attachment>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_attachment в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_attachment при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_attribute_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_attribute')){
            $json_product_attribute = json_encode($product_attribute_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_attribute>'.$json_product_attribute.'</product_attribute>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_attribute в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_attribute при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла

        }
        if ($product_attribute_combination_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_attribute_combination')){
            $json_product_attribute_combination = json_encode($product_attribute_combination_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_attribute_combination>'.$json_product_attribute_combination.'</product_attribute_combination>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_attribute_combination в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_attribute_combination при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_attribute_image_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_attribute_image')){
            $json_product_attribute_image = json_encode($product_attribute_image_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_attribute_image>'.$json_product_attribute_image.'</product_attribute_image>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_attribute_image в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_attribute_image при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_attribute_shop_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_attribute_shop')){
            $json_product_attribute_shop = json_encode($product_attribute_shop_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_attribute_shop>'.$json_product_attribute_shop.'</product_attribute_shop>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_attribute_shop в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_attribute_shop при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_carrier_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_carrier')){
            $json_product_carrier = json_encode($product_carrier_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_carrier>'.$json_product_carrier.'</product_carrier>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_carrier в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_carrier при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_country_tax_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_country_tax')){
            $json_product_country_tax = json_encode($product_country_tax_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_country_tax>'.$json_product_country_tax.'</product_country_tax>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_country_tax в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_country_tax при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_download_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_download')){
            $json_product_download = json_encode($product_download_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_download>'.$json_product_download.'</product_download>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_download в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_download при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_group_reduction_cache_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_group_reduction_cache')){
            $json_product_group_reduction_cache = json_encode($product_group_reduction_cache_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_group_reduction_cache>'.$json_product_group_reduction_cache.'</product_group_reduction_cache>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_group_reduction_cache в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_group_reduction_cache при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_lang_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_lang')){
            $json_product_lang = json_encode($product_lang_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_lang>'.$json_product_lang.'</product_lang>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_lang в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_lang при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла

        }
        if ($product_sale_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_sale')){
            $json_product_sale = json_encode($product_sale_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_sale>'.$json_product_sale.'</product_sale>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_sale в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_sale при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_shop_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_shop')){
            $json_product_shop = json_encode($product_shop_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_shop>'.$json_product_shop.'</product_shop>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_shop в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_shop при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_supplier_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_supplier')){
            $json_product_supplier = json_encode($product_supplier_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_supplier>'.$json_product_supplier.'</product_supplier>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_supplier в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_supplier при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
        if ($product_tag_data = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_tag')){
            $json_product_tag = json_encode($product_tag_data);

            $fopen_db = fopen(self::DB_FILE_PATH, "a");
            $fwrite_db = fwrite($fopen_db, '<product_tag>'.$json_product_tag.'</product_tag>');
            if ($fwrite_db) $information .= '<p style="bg-success">Данные product_tag в файл успешно занесены </p><br>';
            else $information .= '<p style="bg-danger">Ошибка product_tag при записи в файл </p><br>';
            fclose($fopen_db); //Закрытие файла
        }
       
        
        return $information;
        
    }

    protected function postProcessProductRestore()
    {
        $information = "";
        $db = new Document(self::DB_FILE_PATH, true);
        if ($db->has('product')) {
            Db::getInstance()->delete('product');
            $product = $db->find('product');
            $restore_product = json_decode($product[0]->text());

            foreach ($restore_product as $product) {
                 
                Db::getInstance()->insert('product', array(
                    'id_product' => (int)$product->id_product,
                    'id_supplier'      => (int)$product->id_supplier,
                    'id_manufacturer' => (int)$product->id_manufacturer,
                    'id_category_default' => (int)$product->id_category_default,
                    'id_shop_default' => (int)$product->id_shop_default,
                    'id_tax_rules_group' => (int)$product->id_tax_rules_group,
                    'on_sale' => $product->on_sale,
                    'online_only' => $product->online_only,
                    'ean13' => $product->ean13,
                    'isbn' => $product->isbn,
                    'upc' => $product->upc,
                    'ecotax' => $product->ecotax,
                    'quantity' => $product->quantity,
                    'minimal_quantity' => $product->minimal_quantity,
                    'low_stock_threshold' => $product->low_stock_threshold,
                    'low_stock_alert' => $product->low_stock_alert,
                    'price' => $product->price,
                    'wholesale_price' => $product->wholesale_price,
                    'unity' => $product->unity,
                    'unit_price_ratio' => $product->unit_price_ratio,
                    'additional_shipping_cost' => $product->additional_shipping_cost,
                    'reference' => $product->reference,
                    'supplier_reference' => $product->supplier_reference,
                    'location' => $product->location,
                    'width' => $product->width,
                    'height' => $product->height,
                    'depth' => $product->depth,
                    'weight' => $product->weight,
                    'out_of_stock' => $product->out_of_stock,
                    'additional_delivery_times' => $product->additional_delivery_times,
                    'quantity_discount' => $product->quantity_discount,
                    'customizable' => $product->customizable,
                    'uploadable_files' => $product->uploadable_files,
                    'text_fields' => $product->text_fields,
                    'active' => $product->active,
                    'redirect_type' => $product->redirect_type,
                    'id_type_redirected' => $product->id_type_redirected,
                    'available_for_order' => $product->available_for_order,
                    'available_date' => $product->available_date,
                    'show_condition' => $product->show_condition,
                    'condition' => $product->condition,
                    'show_price' => $product->show_price,
                    'indexed' => $product->indexed,
                    'visibility' => $product->visibility,
                    'cache_is_pack' => $product->cache_is_pack,
                    'cache_has_attachments' => $product->cache_has_attachments,
                    'is_virtual' => $product->is_virtual,
                    'cache_default_attribute' => $product->cache_default_attribute,
                    'date_add' => $product->date_add,
                    'date_upd' => $product->date_upd,
                    'advanced_stock_management' => $product->advanced_stock_management,
                    'pack_stock_type' => $product->pack_stock_type,
                    'state' => $product->state,
                ),$null_values = true);
             }
        $information .= '<p style="bg-success">Данные product восстановили в базу данных</p><br>';   
        }

         if ($db->has('product_attachment')) {
            Db::getInstance()->delete('product_attachment');
            $product_attachment = $db->find('product_attachment');
            $restore_product_attachment = json_decode($product_attachment[0]->text());

            foreach ($restore_product_attachment as $product_attachment) {
                 
                Db::getInstance()->insert('product_attachment', array(
                    'id_product' => (int)$product_attachment->id_product,
                    'id_attachment'      => (int)$product_attachment->id_attachment,
                ),$null_values = true);
             }
        $information .= '<p style="bg-success">Данные product_attachment восстановили в базу данных</p><br>';
        }

         if ($db->has('product_attribute')) {
            Db::getInstance()->delete('product_attribute');
            $product_attribute = $db->find('product_attribute');
            $restore_product_attribute = json_decode($product_attribute[0]->text());

            foreach ($restore_product_attribute as $product_attribute) {
                //if($product_attribute)     
                Db::getInstance()->insert('product_attribute', array(
                    'id_product_attribute' => (int)$product_attribute->id_product_attribute,
                    'id_product'      => (int)$product_attribute->id_product,
                    'reference'      =>  $product_attribute->reference,
                    'supplier_reference'      => $product_attribute->supplier_reference,
                    'location'      => $product_attribute->location,
                    'ean13'      => $product_attribute->ean13,
                    'isbn'      => $product_attribute->isbn,
                    'upc'      => $product_attribute->upc,
                    'wholesale_price'      => $product_attribute->wholesale_price,
                    'price'      => $product_attribute->price,
                    'ecotax'      => $product_attribute->ecotax,
                    'quantity'      => (int)$product_attribute->quantity,
                    'weight'      => $product_attribute->weight,
                    'unit_price_impact'      => $product_attribute->unit_price_impact,
                    'default_on'      => $product_attribute->default_on,
                    'minimal_quantity'      => (int)$product_attribute->minimal_quantity,
                    'low_stock_threshold'      => (int)$product_attribute->low_stock_threshold,
                    'low_stock_alert'      => (int)$product_attribute->low_stock_alert,
                    'available_date'      => $product_attribute->available_date,


                ),$null_values = true);
             }
        $information .= '<p style="bg-success">Данные product_attribute восстановили в базу данных</p><br>';
        }

         if ($db->has('product_attribute_combination')) {
            Db::getInstance()->delete('product_attribute_combination');
            $product_attribute_combination = $db->find('product_attribute_combination');
            $restore_product_attribute_combination = json_decode($product_attribute_combination[0]->text());

            foreach ($restore_product_attribute_combination as $product_attribute_combination) {
                 
                Db::getInstance()->insert('product_attribute_combination', array(
                    'id_attribute' => (int)$product_attribute_combination->id_attribute,
                    'id_product_attribute'      => (int)$product_attribute_combination->id_product_attribute,
                    

                ));
             }
        $information .= '<p style="bg-success">Данные product_attribute_combination восстановили в базу данных</p><br>';
        }

        return $information;


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
