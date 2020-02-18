<?php
/*
Author: Bl Modules
Email: blmodules@gmail.com
Page: http://www.blmodules.com
*/
/*
//themes/your_themes/product-list.top, 27 line (after {if isset($products)} )
<!-- BlModules banner upload -->
{if !empty($banner_category)}
	<div style="clear: both;">
		{include file="$tpl_dir../../modules/banner_upload/horizontal_banner.tpl" banner=$banner_category}
	</div>
{/if}
<!-- END BlModules banner upload -->
*/

if (!defined('_PS_VERSION_'))
	exit;
	
class banner_upload extends Module 
{	
	public $full_address_no_t = false;
	public $token_url = false;
	
	public function __construct() 
	{
		$this->name = 'banner_upload';
		$this->tab = 'advertising_marketing';
		$this->author = 'BL Modules';
		$this->version = 3.4;
		$this->module_key = '634ce79bd247d88ad790ad13690a6d10';

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Banner (ads) uploader Pro');
        $this->description = $this->l('Displays banners in your shop');
		$this->confirmUninstall = $this->l('Are you sure you want to delete a module?');
	}

	public function install() 
	{
        if(!parent::install())
			return false;
			
		if(!$this->registerHook('header')
			or !$this->registerHook('rightColumn') 
			or !$this->registerHook('leftColumn')
			or !$this->registerHook('footer')
			or !$this->registerHook('home')
			or !$this->registerHook('productFooter')
			)
			return false;
		
		$sql_blmod_banner =
			'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_upl_banner
			(
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`recordListingID` int(5) DEFAULT NULL,
				`position` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
				`image` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
				`type` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
				`url` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
				`new_window` tinyint(1) DEFAULT NULL,
				`alt` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
				`resize` tinyint(1) DEFAULT NULL,
				`width` int(11) DEFAULT NULL,
				`height` int(11) DEFAULT NULL,
				`start_show` date DEFAULT NULL,
				`end_show` date DEFAULT NULL,
				`show_qty` int(11) DEFAULT "0",
				`show_qty_now` int(11) DEFAULT "0",
				`visible` tinyint(1) DEFAULT NULL,
				`display_type` tinyint(1) DEFAULT NULL,
				`click_unique` int(11) NOT NULL DEFAULT "0",
				`click_total` int(11) NOT NULL DEFAULT "0",
				`status` tinyint(1) DEFAULT NULL,
				`custome_ads_code` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
				`ads_type` tinyint(1) DEFAULT NULL,
				`active_pages` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
				PRIMARY KEY (`id`)
			)';
		$sql_blmod_banner_res = Db::getInstance()->Execute($sql_blmod_banner);
		
		$sql_blmod_banner_block =
			'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_upl_banner_block
			(
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
				`rand` tinyint(1) DEFAULT NULL,
				`val_x` int(10) DEFAULT NULL,
				`val_y` int(10) DEFAULT NULL,
				`pos_x` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
				`pos_y` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
				`slides` tinyint(1) DEFAULT NULL,
				`width` int(11) DEFAULT NULL,
				`status` tinyint(1) DEFAULT NULL,
				`filter_text` TEXT CHARACTER SET utf8 DEFAULT NULL,				
				`filter_type` tinyint(1) NOT NULL DEFAULT  "0",
				`filter_type_name` tinyint(1) DEFAULT NULL,
				`filter_type_desc` tinyint(1) DEFAULT NULL,
				`filter_type_att_name` tinyint(1) DEFAULT NULL,
				PRIMARY KEY (`id`)
			)';
		$sql_blmod_banner_block_res = Db::getInstance()->Execute($sql_blmod_banner_block);
		
		$blmod_upl_banner_block_val =
		'INSERT INTO '._DB_PREFIX_.'blmod_upl_banner_block
			(`id`, `name`, `rand`) 
			VALUES 
			(1, "home", "0"),
			(2, "header", "0"),
			(3, "left", "0"),
			(4, "right", "0"),
			(5, "footer", "0"),
			(6, "footer_circle", "1")';
		$blmod_upl_banner_block_val_res = Db::getInstance()->Execute($blmod_upl_banner_block_val);
		
		$sql_blmod_banner_lang =
			'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_upl_lang
			(
				`id` int(11) NOT NULL auto_increment,
				`lang_id` int(10) NOT NULL,
				`banner` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			)';
		$sql_blmod_banner_lang_res = Db::getInstance()->Execute($sql_blmod_banner_lang);	

		$sql_blmod_banner_name =
			'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_upl_name
			(
				`banner_id` INT(11) NOT NULL,
				`banner_name` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
				`lang_id` TINYINT(3) NOT NULL,
				PRIMARY KEY (`banner_id`, `lang_id`)
			)';
		$sql_blmod_banner_name_res = Db::getInstance()->Execute($sql_blmod_banner_name);		
		
		$sql_blmod_banner_clicks =
			'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_upl_clicks
			(
				`click_id` INT(11) NOT NULL AUTO_INCREMENT,
				`banner_id` INT(11) NOT NULL,
				`user_id` INT(11) NULL,
				`user_ip` VARCHAR(15) NULL,
				`date` DATETIME NULL,
				`page_address` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
				PRIMARY KEY (`click_id`)
			)';
		$sql_blmod_banner_clicks_res = Db::getInstance()->Execute($sql_blmod_banner_clicks);
		

		$sql_blmod_banner_views =
			'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_upl_views
			(
				`banner_id` INT(11) NOT NULL,
				`views` INT(11) NOT NULL DEFAULT "0",
				`date` DATE NULL,
				`click_unique` INT(11) NOT NULL DEFAULT "0",
				`click_total` INT(11) NOT NULL DEFAULT "0",
				PRIMARY KEY (`banner_id`, `date`)
			)';
		$sql_blmod_banner_views_res = Db::getInstance()->Execute($sql_blmod_banner_views);
		
		if(!$sql_blmod_banner_res or !$sql_blmod_banner_block_res or !$blmod_upl_banner_block_val_res or !$sql_blmod_banner_lang_res or !$sql_blmod_banner_name_res or !$sql_blmod_banner_clicks_res or !$sql_blmod_banner_views_res)
			return false;
			
		return true;		
    }
	
	public function uninstall()
	{
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_upl_banner');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_upl_banner_block');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_upl_lang');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_upl_name');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_upl_clicks');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_upl_views');
		
		$this->delete_all_images();
		
		return parent::uninstall();
	}
	
	public function delete_all_images()
	{
		$folder = '../modules/banner_upload/banner_img/';
		
		if($handle = opendir($folder))
		{
			while(false !== ($entry = readdir($handle)))			
				if($entry != '.' and $entry != '..' and $entry != 'index.html')				
					@unlink($folder.$entry);		
			
			closedir($handle);
		}
	}
	
	public function getContent()
	{		
		$this->_html = '<h2>'.$this->displayName.' - V'. $this->version .'</h2>';
		$tab = Tools::getValue('tab');
		
		$full_address_no_t = 'http://' . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__.substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__)).'?tab='.$tab.'&configure='.Tools::getValue('configure');		
		$this->full_address_no_t = $full_address_no_t;
		
		$token = '&token='.Tools::getValue('token');
		$this->token_url = $token;		
		
		$this->_html .= '<script type="text/javascript" src="../modules/banner_upload/banner_upload_bo.js"></script>';
		
		if(_PS_VERSION_ >= '1.4')
		{
			$this->_html .= '
			<script type="text/javascript" src="../modules/banner_upload/ui/jquery.ui.core.min.js"></script>
			<script type="text/javascript" src="../modules/banner_upload/ui/jquery.ui.widget.min.js"></script>
			<script type="text/javascript" src="../modules/banner_upload/ui/jquery.ui.mouse.min.js"></script>
			<script type="text/javascript" src="../modules/banner_upload/ui/jquery.ui.sortable.min.js"></script>
			<script type="text/javascript" src="../modules/banner_upload/order_j14.js"></script>';			
			
			if(_PS_VERSION_ >= '1.5')
			{			
				$context = Context::getContext();
				$context->controller->addJqueryUI('ui.datepicker');
			
				$this->_html .= '
				<script type="text/javascript">
					$(document).ready(function() {
							$(".datepicker").datepicker({
								prevText: "",
								nextText: "",
								dateFormat: "yy-mm-dd"
							});
					});
				</script>';				
			}
			else
				$this->_html .= includeDatepicker(array('start_show', 'end_show'));
		}
		else
		{
			$this->_html .= '
			<script type="text/javascript" src="../modules/banner_upload/jquery-ui-1.7.1.custom.min.js"></script>
			<script type="text/javascript" src="../modules/banner_upload/order_j14.js"></script>';
			
			$this->_html .= includeDatepicker(array('start_show', 'end_show'));
		}
		
		$this->_html .= '<link rel="stylesheet" href="../modules/banner_upload/banner_uploader.css" type="text/css" />';	
		
		if(isset($_POST['btnDelete']) and isset($_POST['id']))
			$this->delete_image($_POST['id']);	
		
		$this->page_structure($full_address_no_t, $token);
		
		return $this->_html;
	}
	
	public function page_structure($full_address_no_t, $token)
	{
		$page = Tools::getValue('block');
		$edit_id = Tools::getValue('edit_id');
        $select = Tools::getValue('select');
		$product_id = Tools::getValue('product_id');
        $category_id = Tools::getValue('category_id');
		$filter_id = Tools::getValue('filter_id');
		
		if($page == 'extra')
			$page = Tools::getValue('cat_id');
		
		if(isset($_POST['dell_cat']))
			$this->dell_confirm($_POST['edit_id']);
				
		if(isset($_POST['dell_cat_yes']))
			$this->delete_cat_ban($_POST['edit_id']);
			
		if(isset($_POST['add_cat']) or isset($_POST['update_cat']))
		{
			$edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : false;
			$name = isset($_POST['name']) ? $_POST['name'] : false;
			$x_val = isset($_POST['x_val']) ? $_POST['x_val'] : 0;
			$y_val = isset($_POST['y_val']) ? $_POST['y_val'] : 0;
			$x = isset($_POST['x']) ? $_POST['x'] : false;
			$y = isset($_POST['y']) ? $_POST['y'] : false;
			$status = isset($_POST['status']) ? $_POST['status'] : 0;
			$rand = isset($_POST['rand']) ? $_POST['rand'] : 0;
			
			if(!$name or !Validate::isFloat($x_val) or !Validate::isFloat($y_val))
				$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('You must insert name and position must be a number!').'</div>';
			elseif($edit_id)
				$this->update_cat($name, $x_val, $y_val, $x, $y, $status, $rand, $edit_id);
			else
				$this->insert_cat($name, $x_val, $y_val, $x, $y, $status, $rand, $full_address_no_t, $token);
		}
		
		if(isset($_POST['update_block_s']))
		{
			$_POST['edit_id'] = isset($_POST['edit_id']) ? $_POST['edit_id'] : false;
			$_POST['status'] = isset($_POST['status']) ? $_POST['status'] : false;
			$_POST['rand'] = isset($_POST['rand']) ? $_POST['rand'] : false;
            $_POST['slides'] = isset($_POST['slides']) ? $_POST['slides'] : false;
            $_POST['width'] = isset($_POST['width']) ? $_POST['width'] : false;
            $_POST['real_name'] = isset($_POST['real_name']) ? $_POST['real_name'] : false;
			
			$this->update_block_s_update($_POST['edit_id'], $_POST['status'], $_POST['rand'], $_POST['slides'], $_POST['width'], $_POST['real_name']);
		}
		
		$this->_html .= '
		<div id="blmod_banner_upload">
		<div id="run_code_mask"></div>
		<div id="run_code_box">
			<div id="run_code"></div>
			<div title="'.$this->l('Close').'" id="run_code_exit">'.$this->l('X').'</div>
			<div class="bl_cb"></div>
		</div>		
		<div style="float: left; width: 230px;">';
			$this->categories($full_address_no_t, $token, $page);
			$this->block_extra($full_address_no_t, $token, $page, $edit_id);
			$this->block_statistics($full_address_no_t, $token, $page);
		$this->_html .= '</div>
		<div style="float: left; margin-left: 20px; width: 677px;">';

		if(empty($page))
			$page = 'header';

        if(!empty($product_id))
            $product_id = 'product-blmod_'.$product_id;

        if(isset($_POST['id']) and !empty($product_id))
            $page = 'product-blmod_'.$product_id;

        if(!empty($category_id))
            $product_id = 'category-blmod_'.$category_id;

        if(isset($_POST['id']) and !empty($category_id))
            $page = 'category-blmod_'.$category_id;
			
		//Filter
		if(!empty($filter_id))		
		{
			$product_id = 'filter-blmod_'.$filter_id;
			$page = 'filter-blmod_'.$filter_id;
		}
		
		if(isset($_POST['id']) and !empty($filter_id))
            $page = 'filter-blmod_'.$filter_id;
		//END Filter
		
		//Is statistic page
		if($page == 'statistics')
		{
			if($select == 'banner')
				$this->statistics_page_banner_id($full_address_no_t, $token, $select);
			else
				$this->statistics_page($full_address_no_t, $token, $select);
		}
		
		if(!isset($_POST['btnUpdate']) and $page != 'add_cat' and empty($select))
			$this->insert_form($id=null, $page);
		elseif(isset($_POST['id']))
			$this->insert_form($_POST['id'], $page);
		elseif(!empty($page) and $page != 'add_cat' and empty($select))
			$this->insert_form(null, $page);
		elseif(!empty($product_id))
			$this->insert_form(null, $product_id);

        if(!empty($product_id))
            $page = $product_id;

		if(!empty($page) and $page != 'add_cat' and (empty($select) or !empty($product_id)) and $page != 'statistics')
			$this->allBannerDisplayForm($full_address_no_t, $token, $page);
		
		if($page == 'add_cat')
			$this->add_cat($full_address_no_t, $token, $edit_id);

        if($select == 'products' and empty($product_id))
            $this->get_products($full_address_no_t, $token);

        if($select == 'categories' and empty($category_id))
            $this->get_categories($full_address_no_t, $token);

		$this->_html .= '
		</div>
		</div>
		<div style = "clear: both; font-size: 0px;"></div>
		';
	}
	
	public function change_to_friendly_name($title)
	{		
		$title = strtolower(trim($title));
		$title = preg_replace('/[^a-z0-9-.]/', '_', $title);
		$title = preg_replace('/-+/', "_", $title);
		$title = preg_replace('/"\'/', "", $title);		
				
		return $title;
	}

	public function categories($full_address_no_t, $token, $page)
	{
        $select = Tools::getValue('select');

        if(!empty($select))        
            $page = $select;        

		$style = 'style="font-weight:bold;"';
		$style_f = '';
		$style_fc = '';
		$style_h = '';
		$style_l = '';
		$style_r = '';
		$style_ho = '';
        $style_products = '';
        $style_categories = '';
		
		$all_block = Db::getInstance()->ExecuteS('
			SELECT COUNT(position) AS c, position
			FROM '._DB_PREFIX_.'blmod_upl_banner
			GROUP BY position
		');
		
		if(!empty($_GET['filter_id']))
			$page = 'is_filter';
			
		switch($page)
		{
			case "":
				$style_h = $style;
				break;
			case "footer":
				$style_f = $style;
				break;
			case "footer_circle":
				$style_fc = $style;
				break;
			case "header":
				$style_h = $style;
				break;
			case "left":
				$style_l = $style;
				break;
			case "right":
				$style_r = $style;
				break;
			case "home":
				$style_ho = $style;
				break;
            case "products":
				$style_products = $style;
				break;
             case "categories":
				$style_categories = $style;
				break;
			case "is_filter":
				$style_products = $style;
				break;
		}

		foreach($all_block as $count)
		{
			if($count['position'] == 'footer')
				$block_f = $count['c'];
			
			if($count['position'] == 'footer_circle')
				$block_fc = $count['c'];
				
			if($count['position'] == 'header')
				$block_h = $count['c'];
			
			if($count['position'] == 'left')
				$block_l = $count['c'];
			
			if($count['position'] == 'right')
				$block_r = $count['c'];
			
			if($count['position'] == 'home')
				$block_ho = $count['c'];
		}

        $count_prod = Db::getInstance()->getRow('
			SELECT COUNT(position) AS count_prod
			FROM '._DB_PREFIX_.'blmod_upl_banner
			WHERE position LIKE "%product-blmod_%" OR position LIKE "%filter-blmod_%"
		');

        $count_cat = Db::getInstance()->getRow('
			SELECT COUNT(position) AS count_cat
			FROM '._DB_PREFIX_.'blmod_upl_banner
			WHERE position LIKE "%category-blmod_%"
		');		
		
		$count_f_circle = Db::getInstance()->getRow('
			SELECT COUNT(position) AS count_cat
			FROM '._DB_PREFIX_.'blmod_upl_banner
			WHERE position LIKE "%footer_circle-blmod_%"
		');

		$block_f = isset($block_f) ? $block_f : 0;
		$block_fc = isset($block_fc) ? $block_fc : 0;
		$block_h = isset($block_h) ? $block_h : 0;
		$block_l = isset($block_l) ? $block_l : 0;
		$block_r = isset($block_r) ? $block_r : 0;
		$block_ho = isset($block_ho) ? $block_ho : 0;
        $block_products = isset($count_prod['count_prod']) ? $count_prod['count_prod'] : 0;
        $block_categories = isset($count_cat['count_cat']) ? $count_cat['count_cat'] : 0;
		
		$this->_html .= '		
			<fieldset><legend><img src="../img/admin/summary.png" alt="'.$this->l('Blocks').'" title="'.$this->l('Blocks').'" />'.$this->l('Blocks').'</legend>
				<table border="0" width="100%" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<img src="../img/admin/tab-categories.gif" alt="" title="" /><a '.$style_ho.' href = "'.$full_address_no_t.'&block=home'.$token.'">'.$this->l('Home page').'</a> <span id="block-home">('.$block_ho.')</span><br/>	
							<img src="../img/admin/tab-categories.gif" alt="" title="" /><a '.$style_h.' href = "'.$full_address_no_t.'&block=header'.$token.'">'.$this->l('Header').'</a> <span id="block-header">('.$block_h.')</span><br/>
							<img src="../img/admin/tab-categories.gif" alt="" title="" /><a '.$style_l.' href = "'.$full_address_no_t.'&block=left'.$token.'">'.$this->l('Left column').'</a> <span id="block-left">('.$block_l.')</span><br/>	
							<img src="../img/admin/tab-categories.gif" alt="" title="" /><a '.$style_r.' href = "'.$full_address_no_t.'&block=right'.$token.'">'.$this->l('Right column').'</a> <span id="block-right">('.$block_r.')</span><br/>	
							<img src="../img/admin/tab-categories.gif" alt="" title="" /><a '.$style_f.' href = "'.$full_address_no_t.'&block=footer'.$token.'">'.$this->l('Footer').'</a> <span id="block-footer">('.$block_f.')</span><br/>
							<img src="../img/admin/next.gif" alt="" title="" /><a '.$style_fc.' href = "'.$full_address_no_t.'&block=footer_circle'.$token.'">'.$this->l('Footer circle').'</a> <span id="block-footer_circle">('.$block_fc.')</span><br/>
							<img src="../img/admin/products.gif" alt="" title="" /><a '.$style_products.' href = "'.$full_address_no_t.'&select=products'.$token.'">'.$this->l('Products').'</a> <span id="block-products">('.$block_products.')</span><br/>
							<img src="../img/admin/navigation.png" alt="" title="" /><a '.$style_categories.' href = "'.$full_address_no_t.'&select=categories'.$token.'">'.$this->l('Categories').'</a> <span id="block-categories">('.$block_categories.')</span><br/>
						</td>
					</tr>
				</table>
		</fieldset><br/><br/>';
	}

    public function pagination($full_address_no_t, $token, $in_cat=0, $max_in_page=60)
	{
		if($max_in_page >= $in_cat)
			return array(0, $in_cat, false);			
		
		$page = Tools::getValue('page_number');
		$curent_page = $page;
		$this->_html2  = '<div class = "bl_pagination">';

		$order = '';
		$order_type = '';
		$order = Tools::getValue('order');
		$order_type = Tools::getValue('order_type');

		if(empty($page))
		{
			$page = 1;
			$curent_page = 1;
		}

		$start = ($max_in_page * $page) - $max_in_page;

		if ($in_cat <= $max_in_page)
			$num_of_pages = 1;
		elseif(($in_cat % $max_in_page) == 0)
			$num_of_pages = $in_cat / $max_in_page;
		else
			$num_of_pages = $in_cat / $max_in_page + 1;

		if($curent_page > 1)
		{
			$back = $curent_page - 1;
			$this->_html2 .= '<a href = "'.$full_address_no_t.'&page_number='.$back.$token.'"> << </a>' . ' ';
		}

		$next = $curent_page + 1;

		$this->_html2 .= ' | ';
		$num_of_pages_f = intval($num_of_pages);

		if($curent_page - 4 > 1)
			$this->_html2 .= '<a href = "'.$full_address_no_t.'&page_number=1'.$token.'">1</a> | ';

		if($curent_page - 5 > 1)
			$this->_html2 .= ' ... ';

		$firs_element = $curent_page - 4;

		if($firs_element < 1)
			$firs_element = 1;

		for($i = $firs_element; $i < $curent_page; $i++)
		{
			$this->_html2 .= '<a href = "'.$full_address_no_t.'&page_number='.$i.$token.'">'.$i.'</a> | ';
		}

		$this->_html2 .= $curent_page . ' | ';

		for($i = $curent_page + 1; $i < $curent_page + 5; $i++)
		{
			if($i > $num_of_pages_f)
				break;
			$this->_html2 .= '<a href = "'.$full_address_no_t.'&page_number='.$i.$token.'">'.$i.'</a> | ';
		}

		if($curent_page + 5 < $num_of_pages_f)
			$this->_html2 .= ' ... | ';

		if($curent_page + 4 < $num_of_pages_f)
			$this->_html2 .= '<a href = "'.$full_address_no_t.'&page_number='.$num_of_pages_f.$token.'">'.$num_of_pages_f.'</a> | ';

		if($curent_page + 1 < $num_of_pages)
		{
			$next = $curent_page + 1;
			$this->_html2 .= '<a href = "'.$full_address_no_t.'&page_number='.$next.$token.'"> >> </a>';
		}

		$this->_html2 .= '</div>';

		return array ($start, $max_in_page, $this->_html2);
	}

    public function get_products($full_address_no_t, $token)
    {
        global $cookie;
		
		$lang_id_user = (!isset($cookie) OR !is_object($cookie)) ? intval(Configuration::get('PS_LANG_DEFAULT')) : intval($cookie->id_lang);
		 
		$search_product_id = Tools::getValue('search_product_id');
		$search_product_name = Tools::getValue('search_product_name');
		$where = '';
		
		if(!empty($search_product_id))
		{
			$search_product_id = (int)$search_product_id;
			$where_id = 'p.id_product="'.$search_product_id.'"';
			$search_product_id_val = $search_product_id;
		}
		
		if(!empty($search_product_name))
		{
			$search_product_name = strtolower(htmlspecialchars($search_product_name, ENT_QUOTES));
			$where_name = 'LOWER(pl.name) LIKE "%'.$search_product_name.'%"';
			$search_product_name_val = $search_product_name;			
		}
		
		if(!empty($search_product_id) AND empty($search_product_name))		
			$where = 'WHERE '.$where_id;		
		elseif(!empty($search_product_name) AND empty($search_product_id))		
			$where = 'WHERE '.$where_name;		
		elseif(!empty($search_product_name) AND !empty($search_product_id))		
			$where = 'WHERE '.$where_id.' OR '.$where_name;		
		
        $full_address_no_t .= '&select=products';       
		
		$in_category = Db::getInstance()->getRow('
			SELECT COUNT(p.id_product) AS in_cat
			FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON
			(pl.id_product = p.id_product and pl.id_lang = "'.$lang_id_user.'")
			'.$where
		);

		$in_cat = isset($in_category['in_cat']) ? $in_category['in_cat'] : 0;
		
		$token_pag = '&search_product_id='.$search_product_id.'&search_product_name='.$search_product_name.$token;
		
        $pag = $this->pagination($full_address_no_t, $token_pag, $in_cat, 60);		

        $products = Db::getInstance()->ExecuteS('
			SELECT p.id_product, pl.name, im.id_image
			FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON
			(pl.id_product = p.id_product and pl.id_lang = "'.$lang_id_user.'")
			LEFT JOIN '._DB_PREFIX_.'image im ON
			(im.id_product = p.id_product and im.cover = 1)
			'.$where.'
			ORDER BY pl.name ASC
			LIMIT '.$pag[0].', '.$pag[1].'
		');
	
		$use_ps_images_class = false;
		
		$image_class_name = 'ImageCore';
				
		if(!class_exists($image_class_name, false))
			$image_class_name = 'Image';
				
		$img_class = new $image_class_name();			

		if(method_exists($img_class, 'getExistingImgPath'))
			$use_ps_images_class = true;
			
		$tab = Tools::getValue('tab');
		$configure = Tools::getValue('configure');
		$select = Tools::getValue('select');		
		$token_only =  Tools::getValue('token');	
		
		$this->_html .= $this->filter_products($full_address_no_t, $token);
	
        $this->_html .= '
			<fieldset>
				<legend><img src="../img/admin/search.gif" alt="'.$this->l('Product search').'" title="'.$this->l('Product search').'" />'.$this->l('Product search').'</legend>
				<form action="'.$full_address_no_t.$token.'&" method="get">
					<table border="0" width="100%">
						<tr>
							<td width="20"><img src="../img/admin/prefs.gif" /></td>
							<td width="98"><b>'.$this->l('Product id:').'</b></td>
							<td colspan="4">
								<input type = "text" name = "search_product_id" value = "'.@$search_product_id_val.'" size="30" />								
							</td>							
						</tr>				
						<tr>
							<td width="20"><img src="../img/admin/tab-preferences.gif" /></td>
							<td width="98"><b>'.$this->l('Product name:').'</b></td>
							<td colspan="4">
								<input type = "text" name = "search_product_name" value = "'.@$search_product_name_val.'" size="30" />								
							</td>							
						</tr>											
					</table>
					<center>
						<input type="hidden" name="tab" value="'.$tab.'" />
						<input type="hidden" name="configure" value="'.$configure.'" />
						<input type="hidden" name="select" value="'.$select.'" />
						<input type="hidden" name="token" value="'.$token_only.'" />
						<input type="submit" name="product_search" value="'.$this->l('Search').'" class="button" />
					</center>
				</form>
			</fieldset><br/><br/>				
				
			<fieldset><legend><img src="../img/admin/summary.png" alt="'.$this->l('Products').'" title="'.$this->l('Products').'" />'.$this->l('Products').'</legend>
				<table border="0" width="100%" cellpadding="3" cellspacing="0">
					<tr>
						<td>';

						if(!empty($products))
						{
							$img_name = '-medium.jpg';

							if(_PS_VERSION_ >= '1.5.1')
								$img_name = '-medium_default.jpg';
			
							foreach($products as $p)
							{
								 $count = Db::getInstance()->getRow('
									SELECT count(id) as count_p
									FROM '._DB_PREFIX_.'blmod_upl_banner
									WHERE position = "product-blmod_'.$p['id_product'].'"
								');

								$count['count_p'] = isset($count['count_p']) ? $count['count_p'] : 0;

								$this->_html .= '
									<a href="'.$full_address_no_t.'&product_id='.$p['id_product'].$token.'">
									<div class="bl_admin_product">';
								  
								if($use_ps_images_class)
								{
									$img_class = new $image_class_name($p['id_image']);							
									$img_dir_file = $img_class->getExistingImgPath().$img_name;											
									
									$img_dir = _PS_BASE_URL_._THEME_PROD_DIR_.$img_dir_file;	
									$img_dir_server = '../img/p/'.$img_dir_file;
									
									if(!file_exists($img_dir_server))
									{
										$img_dir = '../img/p/'.$p['id_product'].'-'.$p['id_image'].$img_name;
										$img_dir_server = $img_dir;	
									}
								}
								else
								{
									$img_dir = '../img/p/'.$p['id_product'].'-'.$p['id_image'].$img_name;
									$img_dir_server = $img_dir;						
								}
								
								if(!file_exists($img_dir_server))
									$img_dir = '../img/p/en-'.$img_name;
								
									
								$this->_html .= '<img src = "'.$img_dir.'" title = "'.$p['name'].'" alt = "'.$p['name'].'"/><br/>';
								
								$this->_html .='
									('.$count['count_p'].') '.$p['name'].'
									</div>
									</a>';
							}

							$this->_html .= '<div class="bl_cb"></div><br/><div class="blmod_pagination">'.$pag[2].'</div>';
						}
						else
							$this->_html .= '<div style="font-size:15px;color:#268CCD;font-weight:bold;">'.$this->l('The product was not found').'</div>';
						
						$this->_html .= '
						</td>
					</tr>
				</table>
		</fieldset><br/><br/>';
    }
	
	public function filter_products($full_address_no_t, $token)
	{
		if(!empty($_POST['add_filter']))
			$this->_html .= $this->filter_products_add();
			
		if(!empty($_GET['filter_del']))
			$this->_html .= $this->filter_products_delete($_GET['filter_del']);
			
		$this->_html .= '
			<fieldset>
				<legend><img src="../img/admin/edit.gif" alt="'.$this->l('Product filter').'" title="'.$this->l('Product filter').'" />'.$this->l('Product filter').'</legend>
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<div style="margin-bottom:10px;">
						<b>'.$this->l('Filtered text:').'</b><br/>
						<input style="width: 98%; margin-bottom: 3px;" type = "text" name = "filter_text" value = "" />
						<label for="filter_type_n">
							<input id="filter_type_n" type="checkbox" name="filter_type_n" value="1"> '.$this->l('Filter by name').'
						</label>
							| 
						<label for="filter_type_d">
							<input id="filter_type_d" type="checkbox" name="filter_type_d" value="1"> '.$this->l('Filter by description').'
						</label>
						| 
						<label for="filter_type_an">
							<input id="filter_type_an" type="checkbox" name="filter_type_an" value="1"> '.$this->l('Filter by attribute name').'
						</label>
					</div>	
					<center><input type="submit" name="add_filter" value="'.$this->l('Insert').'" class="button" /></center>';
				
					$this->_html .= $this->filter_products_get($full_address_no_t, $token);
					
				$this->_html .= '
				</form>
			</fieldset><br/><br/>';
	}
	
	public function filter_products_add()
	{		
		if(empty($_POST['filter_text']))
			return '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Please enter filter text').'</div>';
		
		if(empty($_POST['filter_type_n']) and empty($_POST['filter_type_d']) and empty($_POST['filter_type_an']))
			return '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Please select filter type').'</div>';			
			
		$_POST['filter_type_n'] = !empty($_POST['filter_type_n']) ? $_POST['filter_type_n'] : 0;
		$_POST['filter_type_d'] = !empty($_POST['filter_type_d']) ? $_POST['filter_type_d'] : 0;
		$_POST['filter_type_an'] = !empty($_POST['filter_type_an']) ? $_POST['filter_type_an'] : 0;
		
		$sql = Db::getInstance()->Execute('
			INSERT INTO '._DB_PREFIX_.'blmod_upl_banner_block 
			(`name`, `rand`, `status`, `filter_text`, `filter_type`, `filter_type_name`, `filter_type_desc`, `filter_type_att_name`) 
			VALUES 
			("0", "0", "1", "'.htmlspecialchars(trim($_POST['filter_text']), ENT_QUOTES).'", "1", "'.$_POST['filter_type_n'].'", "'.$_POST['filter_type_d'].'", "'.$_POST['filter_type_an'].'")
		');

		$id = Db::getInstance()->Insert_ID();		
		
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'blmod_upl_banner_block SET name = "filter-blmod_'.$id.'" WHERE id = "'.$id.'"');		
		
		return '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Save successfully').'</div>'; 		
	}
	
	public function filter_products_delete($id=false)
	{
		$id = (int)$id;
		
		if(empty($id))
			return false;
		
		$this->delete_cat_ban('filter-blmod_'.$id);
		
		$sql_b = Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_banner_block WHERE id = "'.$id.'"');
	}
	
	public function filter_products_get($full_address_no_t, $token)
	{		
		$filters = Db::getInstance()->ExecuteS('
			SELECT b.`id`, b.`filter_text`, b.`filter_type`, b.`filter_type_name`, b.`filter_type_desc`, b.`filter_type_att_name`,
			(SELECT COUNT(u.`id`) FROM '._DB_PREFIX_.'blmod_upl_banner u WHERE b.`name` = u.`position` GROUP BY u.`position`) AS product_total
			FROM '._DB_PREFIX_.'blmod_upl_banner_block b
			WHERE b.`filter_type` > 0
			ORDER BY b.`id` ASC
		');
		
		if(empty($filters))
			return false;
			
		$this->_html .= '<hr style="margin-bottom: 5px;">';
		
		$i = 1;
		$full_address_no_t_no_p = str_replace("&select=products", "", $full_address_no_t);
		
		foreach($filters as $f)
		{
			$types = array();
						
			if(!empty($f['filter_type_name']))
				$types[] = $this->l('Name');
				
			if(!empty($f['filter_type_desc']))
				$types[] = $this->l('Desc.');
					
			if(!empty($f['filter_type_att_name']))
				$types[] = $this->l('Att. name');
				
			$tyes_s = implode(', ', $types);
			
			$count = !empty($f['product_total']) ? $f['product_total'] : 0;
		
			$this->_html .= '
			<div class="filter_row">
				<span style="font-size: 12px;">#'.$i.'</span> <a href = "'.$full_address_no_t_no_p.'&block=extra&filter_id='.$f['id'].$token.'">'.htmlspecialchars_decode($f['filter_text'], ENT_QUOTES).'</a> 
				<span class="comments">['.$tyes_s.'] ('.$count.')</span> 
				<a href="'.$full_address_no_t.'&filter_del='.$f['id'].$token.'"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
			</div>';
			
			$i++;
		}
	}
	
	public function block_extra($full_address_no_t, $token, $page, $edit_id=false)
	{
		if($edit_id)
			$page = $edit_id;
		
		$all_cat = Db::getInstance()->ExecuteS('
			SELECT c.id, c.name, COUNT(b.position) AS b_count
			FROM '._DB_PREFIX_.'blmod_upl_banner_block c
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_banner b ON
			c.id = b.position
			WHERE c.id > 5 AND c.name NOT LIKE "%product-blmod_%" AND c.name NOT LIKE "%filter-blmod_%" AND  c.name NOT LIKE "%category-blmod_%" AND c.name NOT LIKE "%footer_circle%" AND c.filter_type = "0"
			GROUP BY c.id
		');
		
		$this->_html .= '		
			<fieldset><legend><img src="../img/admin/summary.png" alt="'.$this->l('Fixed blocks').'" title="'.$this->l('Fixed blocks').'" />'.$this->l('Fixed blocks').'</legend>
				<table border="0" width="100%" cellpadding="3" cellspacing="0">
					<tr>
						<td>';
						foreach($all_cat as $car)
						{
							$bold = false;
							$car['b_count'] = isset($car['b_count']) ? $car['b_count'] : 0;
							
							if($car['id'] == $page)
								$bold = 'style="font-weight:bold;"';
							
							$this->_html .= '<img src="../img/admin/tab-categories.gif" alt="" title="" /><a href = "'.$full_address_no_t.'&block=extra&cat_id='.$car['id'].$token.'"><span '.$bold.'>'.$car['name'].'</span></a> <span id="block-extra-'.$car['id'].'">('.$car['b_count'].')</span> <a href="'.$full_address_no_t.'&block=add_cat&edit_id='.$car['id'].$token.'"><img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a><br/>';
						}
						
						if($page == 'add_cat' and !$edit_id)
							$bold = 'style="font-weight:bold;"';
						else
							$bold = false;
							
						$this->_html .= '<br/><img src="../img/admin/add.gif" alt="" title="" /><a href = "'.$full_address_no_t.'&block=add_cat'.$token.'"><span '.$bold.'>'.$this->l('Add new').'</span></a>';
						$this->_html .= '
						</td>
					</tr>
				</table>
		</fieldset><br/><br/>';
	}
	
	public function block_statistics($full_address_no_t, $token, $page)
	{
		$block = Tools::getValue('block');
		
		$select_u = false;
		$select_t = false;
		
		if($block == 'statistics')
		{
			$select = Tools::getValue('select');
			$bold = 'style="font-weight:bold;"';
			
			switch($select)
			{
				case 'unique':
					$select_u = $bold;
					break;
				case 'total':
					$select_t = $bold;
					break;
			}	
		}
		
		$this->_html .= '		
			<fieldset><legend><img src="../img/admin/statsettings.gif" alt="'.$this->l('Clicks statistics').'" title="'.$this->l('Clicks statistics').'" />'.$this->l('Clicks statistics').'</legend>
				<table border="0" width="100%" cellpadding="3" cellspacing="0">
					<tr>
						<td>							
							<img src="../img/admin/add.gif" alt="" title="" /><a href = "'.$full_address_no_t.'&block=statistics&select=unique'.$token.'"><span '.$select_u.'>'.$this->l('Top click unique').'</span></a><br/>
							<img src="../img/admin/add.gif" alt="" title="" /><a href = "'.$full_address_no_t.'&block=statistics&select=total'.$token.'"><span '.$select_t.'>'.$this->l('Total top clicks').'</span></a><br/>							
						</td>
					</tr>
				</table>
		</fieldset><br/><br/>';
	}
	
	public function statistics_page($full_address_no_t, $token, $page=false)
	{
		$show_type = Tools::getValue('type');
		
		$where = false;
		$only_active = 1;
		$order_active = $this->l('[Show only active banners]');
		
		if(!empty($show_type))
		{
			$where = 'WHERE b.status = "1" AND (n.status = "1" OR n_e.status = "1")';
			$only_active = 0;
			$order_active = $this->l('[Show all banners]');
		}
					
		switch ($page)
		{
			case "unique":
				$page_name = $this->l('Top click unique');
				$sql_order = 'click_unique DESC, click_total ASC';
				break;
			case "total":
				$page_name = $this->l('Total top clicks');
				$sql_order = 'click_total DESC, show_qty_now ASC';
				break;
		}		
		
		if(empty($sql_order))
		{
			$this->_html .= '<div style="font-size:15px;color:#268CCD;font-weight:bold;">'.$this->l('Sorry, wrong page address').'</div>';
			
			return false;
		}
		
		$admin_lang = isset($cookie->id_lang) ? (int)$cookie->id_lang : false;
			
		if(empty($admin_lang))
			$admin_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));	
			
		$banners = Db::getInstance()->ExecuteS('
			SELECT b.id, b.position, b.visible, b.display_type, b.click_unique, b.click_total, b.show_qty_now, b.image, b.type, b.image,
			l.banner_name, n_e.id AS id_extra, b.custome_ads_code, b.ads_type
			FROM '._DB_PREFIX_.'blmod_upl_banner b
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_name l ON			
			(l.banner_id = b.id AND l.lang_id = "'.$admin_lang.'")
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_banner_block n ON	
			b.position = n.name
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_banner_block n_e ON	
			b.position = n_e.id
			'.$where.'
			ORDER BY '.$sql_order.'
			LIMIT 100
		');			
		
		if(empty($page_name) or empty($banners))
		{
			$this->_html .= '<div style="font-size:15px;color:#268CCD;font-weight:bold;">'.$this->l('Sorry, empty').'</div>';
			
			return false;
		}
			
		$this->_html .= '		
			<fieldset>
				<legend><img src="../img/admin/statsettings.gif" alt="'.$page_name.'" title="'.$page_name.'" />'.$page_name.'</legend>
				<div style="margin: 0px 5px 10px 0px; text-align: right;"><a href="'.$full_address_no_t.'&block=statistics&select='.$page.'&type='.$only_active.$token.'">'.$order_active.'</a></div>';
				
				$nr = 0;
				
				foreach($banners as $b)
				{
					$bg = $nr%2;
					
					if($bg == 0)
						$bg_table = 'line_dark';
					else
						$bg_table = '';
						
					$b['banner_name'] = isset($b['banner_name']) ? $b['banner_name'] : false;
					$b['click_unique'] = isset($b['click_unique']) ? $b['click_unique'] : 0;
					$b['click_total'] = isset($b['click_total']) ? $b['click_total'] : 0;
					$b['show_qty_now'] = isset($b['show_qty_now']) ? $b['show_qty_now'] : 0;
					$b['display_type'] = !empty($b['display_type']) ? ' '.$this->l('(Pop-up)') : '';
					
					
					$folder = '../modules/banner_upload/banner_img/'.$b['image'];
					$img_size = $this->resize($folder, 240, 100);
					
					$nr++;
					$this->_html .= '
					<div class = "content_images_line '.$bg_table.'" style="min-height: 100px; padding: 3px 0px 3px 0px;">
						<div class="banner_line_text banner_line_text_small">
							'.$this->l('#').$nr.' <br/>
							'.$this->l('Name').': '.$b['banner_name'].'<br/>
							'.$this->l('Position').': '.$this->get_position_name($b['position']).$b['display_type'].'<br/>
							<span class="blmod_unique_c">'.$this->l('Unique clicks').': '.$b['click_unique'].'</span><br/>
							<span class="blmod_total_c">'.$this->l('Total clicks').': '.$b['click_total'].'</span><br/>
							<span class="blmod_displays_c">'.$this->l('Display').': '.$b['show_qty_now'].'</span><br/>
							<span class="show_underline"><a href="'.$full_address_no_t.'&block=statistics&select=banner&ban_id='.$b['id'].$token.'">'.$this->l('View Statistics').'</a></span>
						</div>
						<div style="float: left; width: 246px; word-wrap: break-word;">';	
						
							if(empty($b['ads_type']))
							{
								if($b['type'] != 'x-shockwave-flash')
									$this->_html .= '<img src="'.$folder.'" style="width:'.$img_size[0].'px; height:'.$img_size[1].'px;" />';
								else
								{
									$this->_html .= '
									<object width="'.$img_size[0].'" height="'.$img_size[1].'">
										<param name="wmode" value="transparent" />
										<param name="quality" value="high" />
										<embed src="'.$folder.'" wmode=transparent allowfullscreen="true" allowscriptaccess="always" width="'.$img_size[0].'" height="'.$img_size[1].'">
										</embed>
									</object>';
								}
							}
							elseif(!empty($b['custome_ads_code']))							
								$this->_html .=	$b['custome_ads_code'];
									
						$this->_html .= '
						</div>
						<div style="clear: both; font-size: 0px; height: 0px;"></div>
					</div>';
				}
				
			$this->_html .= '</fieldset>';
	}
	
	public function get_position_name($name=false)
	{
		if(empty($name))
			return false;
			
		switch ($name)
		{
			case "left":
				return $this->l('Left column');
			case "right":
				return $this->l('Right column');
			case "header":
				return $this->l('Header');
			case "footer_circle":
				return $this->l('Footer circle');
			case "footer":
				return $this->l('Footer');
			case "home":
				return $this->l('Home page');			
		}	
		
		if((int)$name > 0)
			return $this->l('Fixed block');
			
		$find = strpos(' '.$name, 'product-blmod_');
		
		if(!empty($find))
			return $this->l('Product');
			
		$find = strpos(' '.$name, 'category-blmod_');
		
		if(!empty($find))
			return $this->l('Category');
			
		$find = strpos(' '.$name, 'filter-blmod_');
		
		if(!empty($find))
			return $this->l('Product filter');
	}
	
	public function get_product_name($product_id=false)
	{
	
	}
	
	public function get_category_name($cat_id=false)
	{
	
	}
	
	public function statistics_page_banner_id($full_address_no_t, $token, $page=false)
	{		
		$display_off = Tools::getValue('disp_off');
		$ban_id = Tools::getValue('ban_id');
		$now = date('Y-m-d');
		
		if(empty($_POST['start_show']) and !empty($_GET['start_show']))
			$_POST['start_show'] = $_GET['start_show'];
		
		if(empty($_POST['end_show']) and !empty($_GET['end_show']))
			$_POST['end_show'] = $_GET['end_show'];		
			
		$first_day_db = Db::getInstance()->getRow('SELECT `date` FROM '._DB_PREFIX_.'blmod_upl_views WHERE banner_id = "'.$ban_id.'" ORDER BY `date` ASC');
		$last_day_db = Db::getInstance()->getRow('SELECT `date` FROM '._DB_PREFIX_.'blmod_upl_views WHERE banner_id = "'.$ban_id.'" ORDER BY `date` DESC');
		
		if(!empty($_POST['start_show']))
		{
			$date_from = $_POST['start_show'];
			$date_from_val = $date_from;
		}
		else
		{
			$first_day_db['date'] = !empty($first_day_db['date']) ? $first_day_db['date'] : $now;
			$date_from = $first_day_db['date'];
			$date_from_val = '';
		}
		
		if(!empty($_POST['end_show']))
		{
			$date_to = $_POST['end_show'];
			$date_to_val = $date_to;
		}
		else
		{
			$last_day_db['date'] = !empty($last_day_db['date']) ? $last_day_db['date'] : $now;
			$date_to = date('Y-m-d', strtotime($last_day_db['date'].'+1 day'));
			$date_to_val = '';
		}		
		
		$admin_lang = isset($cookie->id_lang) ? (int)$cookie->id_lang : false;
			
		if(empty($admin_lang))
			$admin_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));	
			
		$ban = Db::getInstance()->getRow('
			SELECT b.id, b.position, b.visible, b.display_type, b.click_unique, b.click_total, b.show_qty_now, b.image, b.type, b.image,
			l.banner_name
			FROM '._DB_PREFIX_.'blmod_upl_banner b
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_name l ON
			(l.banner_id = b.id AND l.lang_id = "'.$admin_lang.'")
			WHERE b.id = "'.$ban_id.'"
		');	
		
		if(empty($ban['id']))
		{
			$this->_html .= '<div style="font-size:15px;color:#268CCD;font-weight:bold;">'.$this->l('Sorry, wrong banner id').'</div>';
			
			return false;
		}	
		
		$ban['banner_name'] = isset($ban['banner_name']) ? $ban['banner_name'] : false;
		
		$graphic = Db::getInstance()->ExecuteS('
			SELECT `views`, `date`, click_unique, click_total
			FROM '._DB_PREFIX_.'blmod_upl_views
			WHERE banner_id = "'.$ban_id.'" AND `date` >= "'.$date_from.'" AND `date` < "'.$date_to.'"
			ORDER BY date DESC
			LIMIT 200
		');	
		
		$date_display = 0;
		$date_unique = 0;
		$date_clicks = 0;
		
		if(!empty($graphic[0]))
		{
			$graphic_d = '';
			
			foreach($graphic as $g)
			{
				if(!empty($display_off))
					$graphic_d .= '"'.$g['date'].','.$g['click_unique'].','.$g['click_total'].'\n" +';
				else
					$graphic_d .= '"'.$g['date'].','.$g['click_unique'].','.$g['click_total'].','.$g['views'].'\n" +';
					
				$date_display += $g['views'];
				$date_unique += $g['click_unique'];
				$date_clicks += $g['click_total'];				
			}
		}
		
		$this->_html .= '<script type="text/javascript" src="../modules/banner_upload/dygraphs.js"></script>			
			<fieldset>
				<legend><img src="../img/admin/statsettings.gif" alt="'.$ban['banner_name'].'" title="'.$ban['banner_name'].'" />'.$this->l('Clicks statistics:').' '.$ban['banner_name'].'</legend>';
				
			$page_number = Tools::getValue('page_number');
			
			$current_page = $_SERVER['REQUEST_URI'];
			
			if(!empty($page_number))
				$current_page = str_replace('page_number='.$page_number, 'page_number=1', $_SERVER['REQUEST_URI']);
			
			$this->_html .= includeDatepicker(array('start_show', 'end_show')).'				
			<div id="status" style="float: left; width:115px; min-height: 70px; font-size: 11px;"></div>
			<div style="float: left;">
				<form action="'.$current_page.'" method="post" enctype="multipart/form-data">
					'.$this->l('Date from').' <input class="datepicker" type="text" id="start_show" name="start_show" value = "'.$date_from_val.'" size = "10"/>
					'.$this->l('to').' <input class="datepicker" type="text" id="end_show" name="end_show" value = "'.$date_to_val.'" size = "10"/>
					<input type="submit" name="change_date" value="Show" class="button">
				</form>
			</div>';			
			
			$this->_html .= '<div id="status" style="float: left; width:160px; min-height: 70px; font-size: 11px; margin-left: 20px;">';
			
			$this->_html .= '<div>'.$date_from.' - '.$date_to.':</div>';
		
			$this->_html .= '
				<span class="blmod_unique_c" style="font-weight: bold;">'.$this->l('Unique clicks').'</span>: '.$date_unique.'<br/>
				<span class="blmod_total_c" style="font-weight: bold;">'.$this->l('Total clicks').'</span>: '.$date_clicks.'<br/>
				<span class="blmod_displays_c" style="font-weight: bold;">'.$this->l('Display').'</span>: '.$date_display.'<br/>
			</div>';
			
			
			$this->_html .= '<div class="bl_cb"></div>';

		$click_where = ' WHERE banner_id = "'.$ban_id.'" AND `date` >= "'.$date_from.' 00:00:00" AND `date` < "'.$date_to.' 00:00:00"';
		
		$clicks_c = Db::getInstance()->getRow('
			SELECT COUNT(click_id) AS c_qty
			FROM '._DB_PREFIX_.'blmod_upl_clicks'
			.$click_where
		);	
		
		if(!empty($display_off))
			$display_off_address = '&disp_off=1';
		else
			$display_off_address = '';
		
		$c_qty = isset($clicks_c['c_qty']) ? $clicks_c['c_qty'] : 0;
		$token_pag = '&block=statistics&select=banner&ban_id='.$ban_id.'&start_show='.$date_from_val.'&end_show='.$date_to_val.$token.$display_off_address;
		
		$pag = $this->pagination($full_address_no_t, $token_pag, $c_qty, 40);		
			
		$clicks = Db::getInstance()->ExecuteS('
			SELECT user_id, user_ip, `date`, page_address
			FROM '._DB_PREFIX_.'blmod_upl_clicks
			'.$click_where.'
			ORDER BY `date` DESC
			LIMIT '.$pag[0].', '.$pag[1].'
		');	
		
		if(!empty($graphic[1]))
		{			
			$this->_html .= "
			<script type='text/javascript'>
			$(document).ready(function()
			{	
				$('#blmod_disabled_display').change(function()
				{					
					window.location.href = $('#blmod_current_page').text();					
				});	
			});
			</script>";			
			
			$display_off_cb = 'checked="checked"';
			
			if(empty($display_off))
			{
				$display_off_url = $_SERVER['REQUEST_URI'].'&disp_off=1';
				$display_off_cb = '';
			}
			else
				$display_off_url = str_replace('&disp_off=1', '', $_SERVER['REQUEST_URI']);
						
			$this->_html .= '			
			<div style="clear: both; font-size: 0px; height: 0px;">&ensp;</div>
			<script type="text/javascript" src="../modules/banner_upload/dygraphs.js"></script>		
			<div id="blmod_graphdiv"></div>				
			<script type="text/javascript">
			  g = new Dygraph(
					document.getElementById("blmod_graphdiv"),
					"'.$this->l('Date').', '.$this->l('Unique clicks').', '.$this->l('Total clicks').', '.$this->l('Display').'\n" +
					'.trim($graphic_d, ' +').',
					{
						labelsDiv: document.getElementById("status"),
						labelsSeparateLines: true,
						labelsKMB: true,
						legend: "always",
						colors: [
							"#f5a834",
							"#427fc3",
							"#008040",
						],
						width: 625,
						height: 380
					}
				  );
			</script>
			<div id="blmod_current_page" style="display: none;">'.$display_off_url.'</div>
			<div style="margin: 5px 0 0 37px; font-size: 12px;"><input id="blmod_disabled_display" style="margin-top: -3px;" type="checkbox" name="disabled_display" '.$display_off_cb.' value="1"> '.$this->l('Disabled "Display" curve').'</div>';
		}
		elseif(!empty($clicks))
			$this->_html .= '<div class="comments">'.$this->l('Not enough data to create graphic').'</div>';		
		
		if(!empty($clicks))
		{
			$this->_html .= '<div class="blmod_clicks_list">					
			<div class="blmod_clicks_row blmod_clicks_title">
				<div class="blmod_clicks_ip">'.$this->l('User IP').'</div>
				<div class="blmod_clicks_user_id">'.$this->l('User').'</div>
				<div class="blmod_clicks_date">'.$this->l('Date').'</div>
				<div class="blmod_clicks_url">'.$this->l('URL').'</div>
			</div>';
			
			$nr = 0;
			$day_before = false;
			
			foreach($clicks as $c)
			{
				$next_day = false;
				$date_only = date('Y-m-d', strtotime($c['date']));
				
				$bg = $nr%2;
				
				if($bg == 0)
					$bg_table = 'blmod_click_bg_dark';
				else
					$bg_table = '';				
				
				if($date_only != $day_before and !empty($day_before))
					$next_day = 'style="border-top: 1px solid #e6d2bd;"';
					
				$this->_html .= '
				<div class="blmod_clicks_row '.$bg_table.'" '.$next_day.'>
					<div class="blmod_clicks_ip">'.$c['user_ip'].'</div>
					<div class="blmod_clicks_user_id">'.$this->get_user_info($c['user_id']).'</div>
					<div class="blmod_clicks_date">'.$c['date'].'</div>
					<div class="blmod_clicks_url">';
					
						if(!empty($c['page_address']))
								$this->_html .= '<a href="'.$c['page_address'].'" target="_blank"><img src="../img/admin/subdomain.gif" alt="'.$c['page_address'].'" title="'.$c['page_address'].'" /></a>';
								
					$this->_html .= '
					</div>
				</div>
				';
				
				$day_before = $date_only;
				
				$nr++;
			}
			
			$this->_html .= '</div><br/><div class="blmod_pagination">'.$pag[2].'</div>';
		}
		else
			$this->_html .= '<div style="font-size:15px;color:#268CCD;font-weight:bold;">'.$this->l('There is no clicks statistical data collected').'</div>';
			
		$this->_html .= '</fieldset>';
	}
	
	public function get_user_info($user_id=false)
	{
		if(empty($user_id))
			return '-';
			
		$user = Db::getInstance()->getRow('
			SELECT firstname, lastname
			FROM '._DB_PREFIX_.'customer
			WHERE id_customer = "'.$user_id.'"
		');	
		
		$user['firstname'] = isset($user['firstname']) ? $user['firstname'] : false;
		$user['lastname'] = isset($user['lastname']) ? $user['lastname'] : false;
		
		return $user['firstname'].' '.$user['lastname'].', #'.$user_id;
	}

    public function recurseCategoryForInclude_pref($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL, $full_address_no_t, $token)
	{
		$img_type = 'gif';
		
		if(_PS_VERSION_ >= '1.4.0')
			$img_type = 'png';
			
		global $done, $cookie, $currentIndex;
		static $irow;
		$id_obj = intval(Tools::getValue($this->identifier));

		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;

		$todo = sizeof($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;
		$img = $level == 1 ? 'lv1.'.$img_type : 'lv'.$level.'_'.($todo == $doneC ? 'f' : 'b').'.'.$img_type;

        $count_cat = Db::getInstance()->getRow('
			SELECT COUNT(position) AS count_cat
			FROM '._DB_PREFIX_.'blmod_upl_banner
			WHERE position = "category-blmod_'.$id_category.'"
		');

        $count_cat['count_cat'] = isset($count_cat['count_cat']) ? $count_cat['count_cat'] : 0;

		$this->_html .= '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				'.$id_category.'
			</td>
			<td>
				<img src="../img/admin/'.$img.'" alt="" /> &nbsp;<label style="line-height: 26px;" for="categoryBox_'.$id_category.'" class="t">
				<a href="'.$full_address_no_t.'&select=categories&category_id='.$id_category.$token.'">'.stripslashes($current['infos']['name']).' ('.$count_cat['count_cat'].')</a></label>
			</td>
		</tr>';

		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				if ($key != 'infos')
					$this->recurseCategoryForInclude_pref($indexedCategories, $categories, $categories[$id_category][$key], $key, null, $full_address_no_t, $token);
	}

	static public function getCategories($id_lang, $active = true, $order = true)
	{
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());

		$result = Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`
		WHERE `id_lang` = '.intval($id_lang).'
		'.($active ? 'AND `active` = 1' : '').'
		ORDER BY `name` ASC');

		if (!$order)
			return $result;

		$categories = array();
		foreach ($result AS $row)
			$categories[$row['id_parent']][$row['id_category']]['infos'] = $row;

		return $categories;
	}

    public function get_categories($full_address_no_t, $token)
    {
        global $cookie;

		$this->_html .= $this->check_categories_tpl_file().'
		<fieldset>
			<legend><img src="../img/admin/tab-categories.gif" alt="" title="" />'.$this->l('Categories').'</legend>
			<div style = "margin: 10px;">
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<table cellspacing="0" cellpadding="0" class="table" id = "radio_div">
					<tr>
						<th>'.$this->l('ID').'</th>
						<th style="width: 400px">'.$this->l('Name').'</th>
					</tr>';

					$categories = Category::getCategories(intval($cookie->id_lang), false);
					$this->recurseCategoryForInclude_pref(null, $categories, $categories[0][1], 1, null,$full_address_no_t, $token);

				$this->_html .= '
				</table>
					<br/>
				</form>
			</div>
		</fieldset>';
    }
	
	public function check_categories_tpl_file()
	{
		$file = @file_get_contents(_PS_THEME_DIR_.'product-list.tpl');
		
		if(empty($file))
			return false;
			
		$module_line = strpos($file, 'banner_upload');
		
		$file_path = 'themes/'._THEME_NAME_.'/product-list.tpl';
		
		$line_nr = 1;
		
		if(_PS_VERSION_ >= '1.4')
			$line_nr = 27;
		
		if(empty($module_line))
			return '<div class="warning warn" style="width: 94%;">
				<img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.
					$this->l('Banners will not be displayed with the category. You need to insert the module code in the product-list.tpl file.					
					For more details please read').' <a style="text-decoration: underline;" href="../modules/banner_upload/read_me.pdf">'.$this->l('read_me.pdf').'</a> '.$this->l('file.')
				.'</div>
				<div>
					<div class="comments">'.$this->l('Open ').$file_path.$this->l(' file and after "{if isset($products)}" (~').$line_nr.$this->l(') line paste this code:').'</div>
<textarea style="width: 98%; height: 98px; font-size: 10px; margin-top: 5px; margin-bottom: 15px;">
<!-- BlModules banner uploader pro -->
{if !empty($banner_category)}
	<div style="clear: both;">
		{include file="$tpl_dir../../modules/banner_upload/horizontal_banner.tpl" banner=$banner_category}
	</div>
{/if}
<!-- END BlModules banner uploader pro -->
</textarea>
				</div>
				';		
	}

	public function add_cat($full_address_no_t, $token, $id=false)
	{
		if($id)
		{
			$b = Db::getInstance()->getRow('
				SELECT name, rand, val_x, val_y, pos_x, pos_y, status
				FROM '._DB_PREFIX_.'blmod_upl_banner_block c 
				WHERE id = "'.$id.'"
			');
		}
		
		$name = isset($b['name']) ? $b['name'] : false;
		$rand = isset($b['rand']) ? $b['rand'] : false;
		$val_x = isset($b['val_x']) ? $b['val_x'] : false;
		$val_y = isset($b['val_y']) ? $b['val_y'] : false;
		$pos_x = isset($b['pos_x']) ? $b['pos_x'] : false;
		$pos_y = isset($b['pos_y']) ? $b['pos_y'] : false;
		$status = isset($b['status']) ? $b['status'] : false;	

		$l = false;
		$r = false;
		$t = false;
		$b = false;		
		
		if($pos_x)
		{			
			if($pos_x == 'l')
				$l = 'selected';
			else
				$r = 'selected';
		}
		
		if($pos_y)
		{			
			if($pos_y == 't')
				$t = 'selected';
			else
				$b = 'selected';
		}
		
		$this->_html .= '		
			<fieldset><legend><img src="../img/admin/add.gif" alt="'.$this->l('Add fixed block').'" title="'.$this->l('Add fixed block').'" />'.$this->l('Add fixed block').'</legend>
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<table border="0" width="100%">
						<tr>
							<td width="20"><img src="../img/admin/tab-categories.gif" /></td>
							<td width="98"><b>'.$this->l('Name:').'</b></td>
							<td colspan="4">
								<input type = "text" name = "name" value = "'.$name.'" size="30" />								
							</td>							
						</tr>						
						<tr>
							<td width="20"><img src="../img/admin/themes.gif" /></td>
							<td width="98"><b>'.$this->l('Position X:').'</b></td>
							<td colspan="4">
								<select name="x">
									<option value="l" '.$l.'>'.$this->l('Left').'</option>
									<option value="r" '.$r.'>'.$this->l('Right').'</option>
								</select>
								<input type = "text" name = "x_val" value = "'.$val_x.'" size="3" />px
							</td>							
						</tr>
						
						<tr>
							<td width="20"><img src="../img/admin/themes.gif" /></td>
							<td width="98"><b>'.$this->l('Position Y:').'</b></td>
							<td colspan="4">
								<select name="y">
									<option value="t" '.$t.'>'.$this->l('Top').'</option>
									<option value="b" '.$b.'>'.$this->l('Bottom').'</option>
								</select>
								<input type = "text" name = "y_val" value = "'.$val_y.'" size="3" />px
							</td>							
						</tr>						
						<tr>
							<td width="20"><img src="../img/admin/access.png" /></td>
							<td width="98"><b>'.$this->l('Status:').'</b></td>
							<td colspan="4"><input type="checkbox" name="status"';
									
							if($id)									
								$this->_html .= $this->status($status);
							else
								$this->_html .= 'value = "1" checked/>';
									
							$this->_html .= '
							</td>
							</tr>						
						<tr>
							<td width="20"><img src="../img/admin/manufacturers.gif" /></td>
							<td width="98"><b>'.$this->l('Random:').'</b></td>
							<td colspan="4"><input type="checkbox" name="rand"';									
							if($id)									
								$this->_html .= $this->status($rand);
							else
								$this->_html .= 'value = "1"/>';
									
							$this->_html .= '
							</td>
							</tr>						
					</table>';
					
					if($id)
					$this->_html .= '<center><input type="hidden" name="edit_id" value="'.$id.'" /><input type="submit" name="update_cat" value="'.$this->l('Update').'" class="button" /> <input type="submit" name="dell_cat" value="'.$this->l('Delete').'" class="button" /></center>';
						else
					$this->_html .= '<center><input type="submit" name="add_cat" value="'.$this->l('Insert').'" class="button" /></center>';
					
					$this->_html .= '
				</form>
		</fieldset>';
	}
	
	public function dell_confirm($dell_id)
	{
		$this->_html .= '
		<fieldset><legend><img src="../img/admin/warning.gif" alt="" title="" />'.$this->l('Warning').'</legend>
			<table border="0" width="900" cellpadding="3" cellspacing="0">
				<tr>
					<td width="700">
						<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">						
							<table border="0" width="500" cellpadding="3" cellspacing="0">
								<tr>
									<td width="20"><img src="../img/admin/help.png" /></td>
									<td width="150"><b>'.$this->l('Attention ').'</b></td>
									<td width="300" colspan = "5">
										'.$this->l('Removing a block is erased and all the banners.<br/> Are you sure want to delete this? ').'
									</td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>		
							</table>							
							<table border="0" width="500" cellpadding="3" cellspacing="0">
								<tr>
									<td width="500" colspan="2">
										<center>
											<input type="hidden" name="edit_id" value = "'.$dell_id.'" />
											<input type="submit" name="dell_cat_yes" value="'.$this->l('Yes').'" class="button" />
											<input type="submit" name="del_block_no" value="'.$this->l('No').'" class="button" />
										</center>
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</fieldset>';
		$this->_html .='<br/><br/>';
	}
	
	public function delete_cat_ban($id)
	{		
		$sql_b = Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_banner_block WHERE id = "'.$id.'"');
		$sql_img = Db::getInstance()->ExecuteS('SELECT id FROM '._DB_PREFIX_.'blmod_upl_banner WHERE position = "'.$id.'"');
		
		if(!empty($sql_img))
		{
			foreach($sql_img as $img)
				$this->delete_image($img['id'], false);
		}
		
		$del_clicks = Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_clicks WHERE banner_id = "'.$id.'"');
		$del_names = Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_lang WHERE banner = "'.$id.'"');
		$del_views = Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_views WHERE banner_id = "'.$id.'"');
		
		if($sql_b)
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Delete successfully').'</div>';
		else
			$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Error').'</div>';
	}
	
	public function insert_cat($name, $x_val, $y_val, $x, $y, $status, $rand,$full_address_no_t, $token)
	{
		$wrong_name = array('home page', 'header', 'left column', 'right column', 'footer');
		
		if(in_array(strtolower($name), $wrong_name) or is_numeric($name))
			$name .= '_extra';
		
		$sql = Db::getInstance()->Execute(
			'INSERT INTO '._DB_PREFIX_.'blmod_upl_banner_block
			(`name`, `rand`, `val_x`, `val_y`, `pos_x`, `pos_y`, `status`) 
			VALUES 
			("'.htmlspecialchars($name, ENT_QUOTES).'", "'.$rand.'", "'.$x_val.'", "'.$y_val.'", "'.$x.'", "'.$y.'", "'.$status.'")
		');
		
		$id = Db::getInstance()->Insert_ID();
		
		if(!empty($id))
		{
			header('Location:'.$full_address_no_t.'&block=extra&cat_id='.$id.$token);
			die;
		}
		
		$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Error').'</div>';
	}
	
	public function update_cat($name, $x_val, $y_val, $x, $y, $status, $rand, $edit_id)
	{
		$wrong_name = array('home page', 'header', 'left column', 'right column', 'footer');

		if(in_array(strtolower($name), $wrong_name) or is_numeric($name))
			$name .= '_extra';
			
		$sql = Db::getInstance()->Execute(
			'UPDATE '._DB_PREFIX_.'blmod_upl_banner_block SET
			`name` = "'.htmlspecialchars($name, ENT_QUOTES).'", `rand` = "'.$rand.'", `val_x` = "'.$x_val.'", `val_y` = "'.$y_val.'", `pos_x` = "'.$x.'", `pos_y` = "'.$y.'", `status` = "'.$status.'"
			WHERE id = "'.$edit_id.'"
		');
		
		if($sql)
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Insert successfully').'</div>';
		else
			$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Error').'</div>';
	}
	
	public function update_block_s_update($edit_id, $status, $rand, $slides, $width, $real_name)
	{
        $check = Db::getInstance()->getRow('SELECT id FROM '._DB_PREFIX_.'blmod_upl_banner_block WHERE id = "'.$edit_id.'" OR name = "'.$edit_id.'"');

        $width = (int)$width;
        $message_ok = $this->l('Insert successfully');

        if(!empty($slides))
        {
            $rand = false;
            $message_ok = $this->l('Insert successfully');
        }

        if(empty($check['id']))
        {
            $sql = Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blmod_upl_banner_block (`name`, `rand`, `slides`, `width`, `status`) VALUES ("'.$real_name.'", "'.$rand.'", "'.$slides.'", "'.$width.'", "'.$status.'")');
        }
        else
        {
            $sql = Db::getInstance()->Execute(
                'UPDATE '._DB_PREFIX_.'blmod_upl_banner_block SET
                `rand` = "'.$rand.'", `slides` = "'.$slides.'", width="'.$width.'", `status` = "'.$status.'"
                WHERE id = "'.$check['id'].'"
            ');
        }
		
		if($sql)
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$message_ok.'</div>';
		else
			$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Error').'</div>';
	}
	
	
	public function resize($folder, $width_m=240, $height_m=240)
	{
		$img_size = @getimagesize($folder);		
		$data = array();
		$resize = false;
		
		if(isset($img_size['mime']))
		{
			$width = $img_size[0];						
			$height = $img_size[1];			

			if($width > $width_m)
			{	
				$resize = true;
						
				$width_p = $width / $width_m;
				$width = $width_m;
				$height = $height / $width_p;
				$height = (int)$height;
			}
					
			if($height > $height_m)
			{
				$resize = true;
						
				$height_p = $height / $height_m;
				$height = $height_m;

				$width = $width / $height_p;
				$width = (int)$width;						
			}						
		}
		
		$data[] = isset($width) ? $width : 0;
		$data[] = isset($height) ? $height : 0;
		$data[] = $resize;
		
		return $data;
	}
	
	public function allBannerDisplayForm($full_address_no_t, $token, $page=false)
	{
		global $cookie;
		
		$this->_html .=	'
		<form action="'.$this->get_current_address().'" method="post">
			<div id="block_name" class="'.$page.'"></div>
			<div id = "content_images_dd" class = "content_images_table"><div id="dd_message"></div><ul style="padding-left: 0px;">';		

			$admin_lang = isset($cookie->id_lang) ? (int)$cookie->id_lang : false;
			
			if(empty($admin_lang))
				$admin_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));			
			
			$all_banner_right = Db::getInstance()->ExecuteS('
				SELECT b.*, n.banner_name
				FROM '._DB_PREFIX_.'blmod_upl_banner b
				LEFT JOIN '._DB_PREFIX_.'blmod_upl_name n ON
				(n.banner_id = b.id AND n.lang_id = "'.$admin_lang.'")
				WHERE b.position = "'.$page.'"
				ORDER by b.recordListingID ASC
			');
			
			$i = 0;			
			
			if(!empty($all_banner_right))
			{
				foreach($all_banner_right as $banner_l)
				{
					$lang = array();

					$lang = Db::getInstance()->ExecuteS('
						SELECT lang_id
						FROM '._DB_PREFIX_.'blmod_upl_lang
						WHERE banner = "'.$banner_l['id'].'"
						ORDER by lang_id ASC
					');
					
					$flags = '';
					
					if(!empty($lang))
					{
						foreach($lang as $l)
							$flags .= '<img src= "../img/l/'.$l['lang_id'].'.jpg" alt="" title="" /> ';					
					}
					else
						$flags = '<span style="color:#b61010">'.$this->l('No assigned to the language').'</span>';
					
					$banner_l['alt'] = isset($banner_l['banner_name']) ? $banner_l['banner_name'] : false;
					$banner_l['url'] = isset($banner_l['url']) ? $banner_l['url'] : false;
					$banner_l['start_show'] = isset($banner_l['start_show']) ? $banner_l['start_show'] : '-';
					$banner_l['end_show'] = isset($banner_l['end_show']) ? $banner_l['end_show'] : '-';
					$banner_l['show_qty'] = isset($banner_l['show_qty']) ? $banner_l['show_qty'] : '-';
					$banner_l['show_qty_now'] = isset($banner_l['show_qty_now']) ? $banner_l['show_qty_now'] : 0;
					$banner_l['status'] = isset($banner_l['status']) ? $banner_l['status'] : false;
                    $banner_l['visible'] = isset($banner_l['visible']) ? $banner_l['visible'] : false;
					$banner_l['display_type'] = !empty($banner_l['display_type']) ? $this->l('pop-up') : $this->l('standart');
					
                    switch($banner_l['visible'])
                    {
                        case 1:
                            $visible = $this->l('Only for registered users');
                            break;
                        case 2:
                            $visible = $this->l('Only for unregistered users');
                            break;
                        case 3:
                            $visible = $this->l('All users');
                            break;
                        default:
                           $visible = $this->l('-');
                    }

					$i++;
					$bg = $i%2;
					
					if($bg == 0)
						$bg_table = 'line_dark';
					else
						$bg_table = '';
					
					$c_red = 'style="color:#b61010;"';
					$c_red_s = false;
					$c_red_e = false;
					$c_red_q = false;

					if($banner_l['start_show'] > date('Y-m-d') and $banner_l['start_show'] != '-' and $banner_l['start_show'] != '0000-00-00')
						$c_red_s = $c_red;
					
					if($banner_l['end_show'] <= date('Y-m-d') and $banner_l['end_show'] != '-' and $banner_l['end_show'] != '0000-00-00')
						$c_red_e = $c_red;
					
					if($banner_l['show_qty'] <= $banner_l['show_qty_now'] and !empty($banner_l['show_qty']) and $banner_l['show_qty'] != '0')
						$c_red_q = $c_red;
					
					if(empty($banner_l['start_show']) or $banner_l['start_show'] == '0000-00-00')
						$banner_l['start_show'] = $this->l('Unlimited');
						
					if(empty($banner_l['end_show']) or $banner_l['end_show'] == '0000-00-00')
						$banner_l['end_show'] = $this->l('Unlimited');
						
					if(empty($banner_l['show_qty']) or $banner_l['show_qty'] == '0')
						$banner_l['show_qty'] = $this->l('Unlimited');
						
					$folder = '../modules/banner_upload/banner_img/'.$banner_l['image'];
					$img_size = $this->resize($folder);
					
					$icon_type = 'picture';
					$icon_type_name = $this->l('Image');
					
					if(!empty($banner_l['ads_type']))
					{
						$icon_type = 'translation';
						$icon_type_name = $this->l('Custome code');
					}
					
					$this->_html .='<li id="recordsArray_'.$banner_l['id'].'" class = "content_images_line '.$bg_table.'">
						<div class="blmmod_handle" title="'.$this->l('Just drag and drop to sort').'">
							<div class="order_top">&nbsp;</div>
							<div class="order_down">&nbsp;</div>							
						</div>
						<div style="float: left; width: 605px;">
							<div class="banner_line_text banner_show_info">
								<img src="../img/admin/'.$icon_type.'.gif" alt="" title="'.$icon_type_name.'" /> <input type="radio" name="id" value="'.$banner_l['id'].'" /> <br/>
								<img src="../img/admin/home.gif" alt="" title="" /> <b>'.$this->l('Title:').'</b> <span>'.$banner_l['alt'].'</span><br/>
								<img src="../img/admin/subdomain.gif" alt="" title="" /> <b>'.$this->l('Link:').'</b> ';
								
									if($banner_l['url'] != 'http://')
										$this->_html .= '<a href = "'.$banner_l['url'].'" target = "_blank">'.$banner_l['url'].'</a>';
									else
										$this->_html .= $this->l('-');
										
								$this->_html .='	
								<br/>
								<img src="../img/admin/time.gif" alt="" title="" /> <b>'.$this->l('Start show:').'</b> <span '.$c_red_s.'>'.$banner_l['start_show'].'</span><br/>
								<img src="../img/admin/time.gif" alt="" title="" /> <b>'.$this->l('End show:').'</b> <span '.$c_red_e.'>'.$banner_l['end_show'].'</span><br/>
								<img src="../img/admin/invoice.gif" alt="" title="" /> <b>'.$this->l('Displays qty:').'</b> <span '.$c_red_q.'>'.$banner_l['show_qty'].' ('.$banner_l['show_qty_now'].')</span><br/>
								<img src="../img/admin/access.png" /> <b>'.$this->l('Status:').'</b> <input type="checkbox" name="status"' . $this->status($banner_l['status'], true).'<br/>
								<img src="../img/admin/summary.png" /> <b>'.$this->l('Display type:').'</b> '.$banner_l['display_type'].'<br/>
								<img src="../img/admin/tab-customers.gif" /> <b>'.$this->l('Visible:').'</b> '.$visible.'<br/>
								<img src="../img/admin/localization.gif" /> <b>'.$this->l('Languages:').'</b> '.$flags.'<br/>
								<img src="../img/admin/statsettings.gif" /> <span class="show_underline"><a href="'.$full_address_no_t.'&block=statistics&select=banner&ban_id='.$banner_l['id'].$token.'"><b>'.$this->l('View Statistics').'</b></a></span>
							</div>
							<div style="float: left; width: 325px; word-wrap: break-word;">';
											
							if(empty($banner_l['ads_type']))
							{
								if(strtolower($banner_l['type']) != 'x-shockwave-flash')
									$this->_html .='<a href = "'.$folder.'" target = "_blank"><img width="'.$img_size[0].'" height="'.$img_size[1].'" src = "'.$folder.'" alt = "'.$this->l('Show real size').'" title = "'.$this->l('Show real size').'"/></a><input type = "hidden" value = "'.$banner_l['image'].'" name = "image" />';
								else
								{
									$this->_html .='
									<object id="FlashID" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$img_size[0].'" height="'.$img_size[1].'">
									<param name="movie" value= "'.$folder.'" /><param name="quality" value="high" />
									<param name="wmode" value="opaque" />
									<param name="swfversion" value="6.0.65.0" />
									<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don\'t want users to see the prompt. -->
									<param name="expressinstall" value="Scripts/expressInstall.swf" />
									<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
									<!--[if !IE]>-->
									<object type="application/x-shockwave-flash" data= "'.$folder.'" width="'.$img_size[0].'" height="'.$img_size[1].'">
									<!--<![endif]-->
									<param name="quality" value="high" />
									<param name="wmode" value="opaque" />
									<param name="swfversion" value="6.0.65.0" />
									<param name="expressinstall" value="Scripts/expressInstall.swf" />
									<!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
									<div>
									<p>Content on this page requires a newer version of Adobe Flash Player.</p>
									<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" width="112" height="33" /></a></p>
									</div>
									<!--[if !IE]>-->
									</object>
									<!--<![endif]-->
									</object>
									';
								}
							}
							elseif(!empty($banner_l['custome_ads_code']))							
								$this->_html .=	$banner_l['custome_ads_code'];
						
						$this->_html .=	'
								</div>
							</div>
							<div style="clear: both; font-size: 0px;"></div>';
						
						if(!empty($banner_l['ads_type']) and !empty($banner_l['custome_ads_code']))
							$this->_html .=	'
								<div id="blmod_run_code-'.$banner_l['id'].'" class="blmod_run_code" style="width: 671px; text-align: right; margin-top: -33px; padding-bottom: 10px; cursor: pointer;">
									<img class="run_code_icon" title="'.$this->l('Run custome ads code').'" src="../modules/banner_upload/run_code.png" /> 
								</div>';
							
						$this->_html .=	'
					</li>';
				}
			}
				else $this->_html .='<div style="font-size:15px;color:#268CCD;font-weight:bold;">'.$this->l('Empty').'</div>';
			
			$this->_html .= '</ul>';
			
			if(!empty($all_banner_right))
			{
			$this->_html .='
				<br/></br>
				<center>
					<input type="submit" name="btnUpdate" class="button" value="'.$this->l('Edit').'">
					<input type="submit" name="btnDelete" class="button" value="'.$this->l('Delete').'">
				</center>';
			}
			
			$this->_html .='
			</div>			
		</form>';
	}	
		
	public function delete_image($banner_id, $show_error=true)
	{
		$img_name = Db::getInstance()->getRow('SELECT image FROM '._DB_PREFIX_.'blmod_upl_banner WHERE id = "'.$banner_id.'"');
		$folder_address = "../modules/banner_upload/banner_img/" . $img_name['image'];
		
		if (file_exists($folder_address))
			@unlink($folder_address);
			
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_banner WHERE id = "'.$banner_id.'"');
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_lang WHERE banner = "'.$banner_id.'"');
		
		if($show_error)
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Delete successfully').'</div>'; 
	}
	
	public function insert_image_to_db($Link, $NewTab, $NewFile, $ImgAlt, $Position, $status_banner, $start_show, $end_show, $show_qty, $visible, $display_type, $custome_ads_code, $ads_type, $active_pages)
	{
		$user_file = isset($NewFile['name']) ? $this->change_to_friendly_name($NewFile['name']) : false;
		$file_temp = isset($NewFile['tmp_name']) ? $NewFile['tmp_name'] : false;
		$folder = "../modules/banner_upload/banner_img/" . $user_file;

		$e_message = false;		
		
		$file_types = array('image/gif', 'image/jpeg', 'image/png', 'image/jpg', 'image/bmp', 'image/x-png', 'image/pjpeg');
		
		if($Position != 'footer_circle')
		{
			$add_flash = array('x-sh', 'application/x-shockwave-flash');
			
			$file_types = array_merge((array)$file_types, (array)$add_flash);
			$e_message = ', *.swf';			
		}
		
		$type = explode('/', $NewFile['type']);
		
		$status = false;
		
		do
		{
			$check_value = Db::getInstance()->getRow('SELECT image FROM '._DB_PREFIX_.'blmod_upl_banner WHERE image = "'.$this->change_to_friendly_name($NewFile['name']).'"');
			
			if(isset($check_value['image']))
			{
				$user_file = rand(0, 9999).$user_file;
				$folder = "../modules/banner_upload/banner_img/" . $user_file;
				$status = false;
				$NewFile['name'] = $user_file;
			}
			else			
				$status = true;			
		}
		while(!$status);

		if(!$status)		
			$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('This file exists').'</div>';
		else
		{		
			if((empty($NewFile['error']) and in_array($NewFile['type'], $file_types)))
			{	  
				$uploaded_file = move_uploaded_file($file_temp, $folder);
				$img_size = @getimagesize($folder);				
			}
			elseif(empty($NewFile['error']))
				$e_message_show = true;				
				
			$img_size[0] = isset($img_size[0]) ? $img_size[0] : 0;
			$img_size[1] = isset($img_size[1]) ? $img_size[1] : 0;
			$user_file = isset($user_file) ? $user_file : false;
			$type[1] = isset($type[1]) ? $type[1] : false;			
			
			if(!empty($img_size[0]) and empty($custome_ads_code))
				$ads_type = 0;
			elseif(!empty($custome_ads_code) and empty($img_size[0]))
				$ads_type = 1; 
				
			Db::getInstance()->Execute('
				INSERT INTO '._DB_PREFIX_.'blmod_upl_banner 
				(position, image, type, url, new_window, width, height, status, start_show, end_show, show_qty, visible, custome_ads_code, ads_type, display_type, active_pages)
				VALUES 
				("'.$Position.'", "'.$user_file.'", "'.$type[1].'", "'.htmlspecialchars($Link, ENT_QUOTES).'", "'.$NewTab.'",
				 "'.$img_size[0].'", "'.$img_size[1].'", "'.$status_banner.'", "'.$start_show.'", "'.$end_show.'", "'.$show_qty.'", 
				 "'.$visible.'", "'.$custome_ads_code.'", "'.$ads_type.'", "'.$display_type.'", "'.htmlspecialchars($active_pages, ENT_QUOTES).'")
			');			
			
			$banner_id = Db::getInstance()->Insert_ID();	
			
			$languages = Language::getLanguages(false);

			$insert_lang = '';
			
			foreach($languages as $l)			
				if(!empty($_POST['lang_'.$l['id_lang']]))
					$insert_lang .= '("'.$l['id_lang'].'", "'.$banner_id.'"),';			

			if(!empty($insert_lang))
			{
				$insert_lang = trim($insert_lang, ',');
				
				Db::getInstance()->Execute('
					INSERT INTO '._DB_PREFIX_.'blmod_upl_lang 
					(lang_id, banner)
					VALUES
					'.$insert_lang
				);
			}	

			//Insert name
			$this->insert_banner_name($banner_id);	
			$this->update_menu_ads_number();
				
			if(!empty($e_message_show))
				$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Ads info has been saved, but the image is not entered - bad format (support: *.jpg, *.jpeg, *.png, *.gif, *.bmp '.$e_message.')').'</div>';
			else
				$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Save successfully').'</div>';						
		}
	}
	
	public function update_old_image($Link, $NewTab, $NewFile, $ImgAlt, $Position, $OldImageName, $OldImageId, $status_banner, $start_show, $end_show, $show_qty, $visible, $display_type, $custome_ads_code, $ads_type, $active_pages)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_lang WHERE banner = "'.$OldImageId.'"');
		
		$languages = Language::getLanguages(false);
		$insert_lang = '';
				
		foreach($languages as $l)
		{
			if(!empty($_POST['lang_'.$l['id_lang']]))
				$insert_lang .= '("'.$l['id_lang'].'", "'.$OldImageId.'"),';
		}
				
		if(!empty($insert_lang))
		{
			$insert_lang = trim($insert_lang, ',');
				
			Db::getInstance()->Execute('
				INSERT INTO '._DB_PREFIX_.'blmod_upl_lang 
				(lang_id, banner)
				VALUES
				'.$insert_lang
			);
		}				
				
		if(!empty($NewFile['tmp_name']))
		{
			//Insert new image
			$folder_address = "../modules/banner_upload/banner_img/" . $OldImageId;
			@unlink($folder_address);
			
			$user_file = $this->change_to_friendly_name($NewFile['name']);
			$file_temp = $NewFile['tmp_name'];
			$folder = "../modules/banner_upload/banner_img/" . $user_file;
			$folder_old = "../modules/banner_upload/banner_img/" . $OldImageName;
			
			$e_message = false;		
		
			$file_types = array('image/gif', 'image/jpeg', 'image/png', 'image/jpg', 'image/bmp', 'image/x-png', 'image/pjpeg');
			
			if($Position != 'footer_circle')
			{
				$add_flash = array('x-sh', 'application/x-shockwave-flash');
				
				$file_types = array_merge((array)$file_types, (array)$add_flash);
				$e_message = ', *.swf';			
			}	
		
			$type = explode('/', $NewFile['type']);			
			
			$status = false;		
			
			do
			{
				$check_value = Db::getInstance()->getRow('SELECT image FROM '._DB_PREFIX_.'blmod_upl_banner WHERE image = "'.$this->change_to_friendly_name($NewFile['name']).'"');
				
				if(isset($check_value['image']))
				{
					$user_file = rand(0, 9999).$user_file;
					$folder = "../modules/banner_upload/banner_img/" . $user_file;
					$status = false;
					$NewFile['name'] = $user_file;
				}
				else			
					$status = true;			
			}
			while(!$status);
			
			if(!empty($NewFile['tmp_name']) and empty($NewFile['error']) and in_array($NewFile['type'], $file_types))
			{				
				$uploaded_file = move_uploaded_file($file_temp, $folder);
				$img_size = @getimagesize($folder);				
				
				$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Save successfully').'</div>'; 
			}
			else
			{
				$user_file = false;
				$this->_html .= '<div class="warning warn"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Ads info has been saved, but the image is not entered - bad format (support: *.jpg, *.jpeg, *.png, *.gif, *.bmp '.$e_message.')').'</div>';
			}
			
			$img_size[0] = isset($img_size[0]) ? $img_size[0] : 0;
			$img_size[1] = isset($img_size[1]) ? $img_size[1] : 0;
			$type[1] = isset($type[1]) ? $type[1] : false;
			
			if(!empty($img_size[0]) and empty($custome_ads_code))
				$ads_type = 0;
			elseif(!empty($custome_ads_code) and empty($img_size[0]))
				$ads_type = 1; 
			
			Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'blmod_upl_banner 
			SET position = "'.$Position.'", image = "'.$user_file.'", type = "'.$type[1].'", url = "'.htmlspecialchars($Link, ENT_QUOTES).'", 
			new_window = "'.$NewTab.'", width = "'.$img_size[0].'", height = "'.$img_size[1].'", status = "'.$status_banner.'",
			start_show = "'.$start_show.'", end_show = "'.$end_show.'", show_qty = "'.$show_qty.'",  visible = "'.$visible.'", display_type = "'.$display_type.'",
			custome_ads_code = "'.$custome_ads_code.'", ads_type = "'.$ads_type.'", active_pages = "'.htmlspecialchars($active_pages, ENT_QUOTES).'"
			WHERE id = "'.$OldImageId.'"');
			
			//delete old image
			@unlink($folder_old);
		
		}
		elseif(empty($NewFile['tmp_name']))
		{
			//$ads_type = 1;
			
			//Leave old image
			Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'blmod_upl_banner 
			SET position = "'.$Position.'", url = "'.htmlspecialchars($Link, ENT_QUOTES).'",
			new_window = "'.$NewTab.'", status = "'.$status_banner.'",
			start_show = "'.$start_show.'", end_show = "'.$end_show.'", show_qty = "'.$show_qty.'", visible = "'.$visible.'", display_type = "'.$display_type.'", 
			custome_ads_code = "'.$custome_ads_code.'", ads_type = "'.$ads_type.'", active_pages = "'.htmlspecialchars($active_pages, ENT_QUOTES).'"
			WHERE id = "'.$OldImageId.'"');
			
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Update successfully').'</div>';
		}
		
		//Insert name
		$this->insert_banner_name($OldImageId);		
	}
	
	public function update_menu_ads_number()
	{
		$block_id = Tools::getValue('block');
		$select = Tools::getValue('select'); //products and categories
		$fixed_id = Tools::getValue('cat_id'); //fixed block id
		$filter_id = Tools::getValue('filter_id'); //fixed block id
		
		if(!empty($filter_id))
			$select = 'products';
			
		if(!empty($select))
			$block_id = $select;
		
		if(empty($block_id))
			return false;
			
		if($block_id == 'extra')
			$block_id = 'extra-'.$fixed_id;
		
		$this->_html .= "
		<script type = 'text/javascript'>
		$(document).ready(function()
		{
			var count = $('#block-".$block_id."').text();
			count = parseInt($.trim(count.replace(/[)(]/g, '')))+1;
			
			$('#block-".$block_id."').text('('+count+')');
			
		});
		</script>";		
	}
	
	public function insert_banner_name($banner_id=false)
	{
		$languages = Language::getLanguages(false);
		
		if(!empty($banner_id))
		{
			Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_upl_name WHERE banner_id = "'.$banner_id.'"');
			
			foreach($languages as $language)
			{
				if(isset($_POST['name_'.$language['id_lang']]) AND !empty($_POST['name_'.$language['id_lang']]))
				{
					if(Db::getInstance()->Execute('
						INSERT INTO '._DB_PREFIX_.'blmod_upl_name
						(banner_id, banner_name, lang_id)
						VALUES
						("'.$banner_id.'", "'.htmlspecialchars($_POST['name_'.$language['id_lang']], ENT_QUOTES).'", "'.$language['id_lang'].'")'))
						$error = false;
					else
						$error = true;
				}
			}
		}
	}
	
	public function status($status, $disabled = false)
	{
		if($disabled)
			$disabled = 'disabled';
		else
			$disabled = '';
			
		if(isset($status) and $status == 1)
			$status_text = ' value = "1" checked '.$disabled.' /> <img src="../img/admin/enabled.gif" alt = "'.$this->l('Enabled').'" />' . $this->l('Enabled');
		else
			$status_text = ' value = "1" '.$disabled.'/> <img src="../img/admin/disabled.gif" alt = "'.$this->l('Disabled').'" />' . $this->l('Disabled');
			
		return $status_text;
	}
	
	public function base_block_settings($id=false)
	{
        $type_id = $this->check_type($id);
        $real_name = $id;

        if(!empty($type_id))
            $id = $type_id;

		$blcok = Db::getInstance()->getRow('
			SELECT rand, status, slides, width
			FROM '._DB_PREFIX_.'blmod_upl_banner_block
			WHERE id = "'.$id.'" OR name = "'.$id.'"
		');
		
		$rand = isset($blcok['rand']) ? $blcok['rand'] : false;
		$status = isset($blcok['status']) ? $blcok['status'] : false;
        $slides = isset($blcok['slides']) ? $blcok['slides'] : false;
        $width = isset($blcok['width']) ? $blcok['width'] : false;
		$disabled = false;
		
		if($id == 6)
		{
			$rand = 1;
			$disabled = 'DISABLED';
		}
		
		$this->_html .= '		
			<fieldset><legend><img src="../img/admin/add.gif" alt="'.$this->l('Block settings').'" title="'.$this->l('Block settings').'" />'.$this->l('Block settings').'</legend>
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<table border="0" width="100%">
						<tr>
							<td width="20"><img src="../img/admin/access.png" /></td>
							<td width="98"><b>'.$this->l('Status:').'</b></td>
							<td colspan="4">
								<label for="b_status_tab">
									<input id="b_status_tab" type="checkbox" name="status"';
									$this->_html .= $this->status($status);										
									$this->_html .= '
								</label>
							</td>
						</tr>						
						<tr>
							<td width="20"><img src="../img/admin/manufacturers.gif" /></td>
							<td width="98"><b>'.$this->l('Random:').'</b></td>
							<td colspan="4">
								<label for="b_random_tab">
									<input id="b_random_tab" type="checkbox" '.$disabled.' name="rand"';									
									$this->_html .= $this->status($rand);									
									$this->_html .= '
								</label>								
							</td>
						</tr>
					</table>
					<input type="hidden" name="real_name" value="'.$real_name.'" />';
					
					$this->_html .= '<center><input type="hidden" name="edit_id" value="'.$id.'" /><input type="submit" name="update_block_s" value="'.$this->l('Update').'" class="button" /></center>';
					
					$this->_html .= '
				</form>
		</fieldset><br/>';
	}

    public function check_type($name, $type='product-blmod')
    {
        if(strpos(' '.$name, $type))
        {
            $type_id = Db::getInstance()->getRow('SELECT id FROM '._DB_PREFIX_.'blmod_upl_banner_block WHERE name = "'.$name.'"');
            $type_id['id'] = isset($type_id['id']) ? $type_id['id'] : false;

            return $type_id['id'];
        }
        else
            return false;
    }
	
	public function get_front_page_link($type=false, $id=false)
	{
		$link = new Link();
		$lang_id_user = (!isset($cookie) OR !is_object($cookie)) ? intval(Configuration::get('PS_LANG_DEFAULT')) : intval($cookie->id_lang);
				
		if($type == 'category')
		{
			$category = new Category($id, true, $lang_id_user);			
			
			return $link->getCategoryLink($category);
		}
		elseif($type == 'product')
		{
			$product = new Product($id, true, $lang_id_user);			

			return $link->getProductLink($product);
		}
	}
	
	public function insert_form($id=false, $page=false)
	{
		global $cookie;
		
		$languages = Language::getLanguages(false);
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));		

		$product_id = Tools::getValue('product_id');
        $category_id = Tools::getValue('category_id');

		$lang_id_user = (!isset($cookie) OR !is_object($cookie)) ? intval(Configuration::get('PS_LANG_DEFAULT')) : intval($cookie->id_lang);
		
		//Check product id		
		if(!empty($product_id))
		{
			$product_status = Db::getInstance()->getRow('
				SELECT id_product
				FROM '._DB_PREFIX_.'product 
				WHERE id_product = "'.$product_id.'"
			');
			
			$product_status_banner = Db::getInstance()->getRow('
				SELECT id
				FROM '._DB_PREFIX_.'blmod_upl_banner 
				WHERE position = "product-blmod_'.$product_id.'"
			');
			
			if(empty($product_status['id_product']) and empty($product_status_banner['id']))
			{			
				$this->_html .= '
				<div class="warning warn" style="width: 645px;">
					<img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('This product does not exist').'
				</div>';
				
				return false;
			}
		}
		
		$page = htmlspecialchars($page, ENT_QUOTES);
		$this->_html .= includeDatepicker(array('start_show', 'end_show'));
		$block_stat_id = false;

        if(isset($id) and !empty($product_id))
            $page = 'product-blmod_'.$product_id;		
			
		if(!empty($_GET['filter_id']))
		{
			$_GET['filter_id'] = (int)$_GET['filter_id'];
			$filter_text = Db::getInstance()->getRow('SELECT filter_text FROM '._DB_PREFIX_.'blmod_upl_banner_block WHERE id = "'.$_GET['filter_id'].'"');
			$block_name = $this->l('Filter:').' '.$filter_text['filter_text'];
			$is_filter = true;
		}
		
		$show_display_type = true;
		
		if($page > 0 and empty($product_id) and empty($category_id) and empty($is_filter))
			$show_display_type = false;
			
		switch($page)
		{
			case "footer_circle":
				$block_name = $this->l('Footer circle');
				$block_stat_id = 6;
				$show_display_type = false;
				break;
			case "footer":
				$block_name = $this->l('Footer');
				$block_stat_id = 5;
				break;
			case "header":
				$block_name = $this->l('Header');
				$block_stat_id = 2;
				break;
			case "left":
				$block_name = $this->l('Left column');
				$block_stat_id = 3;
				break;
			case "right":
				$block_name = $this->l('Right column');
				$block_stat_id = 4;
				break;
			case "home":
				$block_name = $this->l('Home page');
				$block_stat_id = 1;
				break;
		}		
			
		if(empty($block_stat_id))
			$check_id = $page;
		else
			$check_id = $block_stat_id;

        $type_id = $this->check_type($page);

        if(!empty($type_id))
            $check_id = $type_id;

		$check_status = Db::getInstance()->getRow('SELECT status FROM '._DB_PREFIX_.'blmod_upl_banner_block WHERE id = "'.$check_id.'" OR name = "'.$page.'"');
		$check_status['status'] = isset($check_status['status']) ? $check_status['status'] : false;

		if($check_status['status'] != 1)
			$this->_html .= '<div class="warning warn" style="width: 634px;"><img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('This block is Disabled. If you wish see this block in page, please change <b>status</b>.').'</div>';
		
		if($block_stat_id and empty($product_id))
			$this->base_block_settings($block_stat_id);

        if(!empty($product_id) or !empty($category_id) or !empty($is_filter))
			$this->base_block_settings($page);

		if($id)		
			$banner_info = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'blmod_upl_banner WHERE id = "'.$id.'"');			
		
		if(isset($_POST['InsertNewImage']))
		{
			$_POST['NewTab'] = (isset($_POST['NewTab']) ? $_POST['NewTab'] : '0');
			$_POST['ImgAlt'] = (isset($_POST['ImgAlt']) ? $_POST['ImgAlt'] : '');
			$_POST['status'] = (isset($_POST['status']) ? $_POST['status'] : '0');
			$_POST['Link'] = (isset($_POST['Link']) ? $_POST['Link'] : '0');
			$_POST['start_show'] = (isset($_POST['start_show']) ? $_POST['start_show'] : '0000:00:00');
			$_POST['end_show'] = (isset($_POST['end_show']) ? $_POST['end_show'] : '0000:00:00');
			$_POST['show_qty'] = (isset($_POST['show_qty']) ? $_POST['show_qty'] : '0');
            $_POST['visible'] = (isset($_POST['visible']) ? $_POST['visible'] : '1');
			$_POST['display_type'] = (isset($_POST['display_type']) ? $_POST['display_type'] : '1');
			$_POST['custome_ads_code'] = (isset($_POST['custome_ads_code']) ? htmlspecialchars($_POST['custome_ads_code'], ENT_QUOTES) : false);
			$_POST['ads_type'] = (isset($_POST['ads_type']) ? $_POST['ads_type'] : '0');
			$_POST['active_pages'] = (isset($_POST['active_pages']) ? $_POST['active_pages'] : false);
			
			$this->insert_image_to_db($_POST['Link'], $_POST['NewTab'], $_FILES['NewFile'], $_POST['ImgAlt'], $_POST['Position'], $_POST['status'], $_POST['start_show'], $_POST['end_show'], $_POST['show_qty'], $_POST['visible'], $_POST['display_type'], $_POST['custome_ads_code'], $_POST['ads_type'], $_POST['active_pages']);
		}

		if(isset($_POST['UpdateOldImage']))
		{
			$_POST['NewTab'] = (isset($_POST['NewTab']) ? $_POST['NewTab'] : '0');
			$_POST['ImgAlt'] = (isset($_POST['ImgAlt']) ? $_POST['ImgAlt'] : '');
			$_POST['status'] = (isset($_POST['status']) ? $_POST['status'] : '0');
			$_POST['Link'] = (isset($_POST['Link']) ? $_POST['Link'] : '0');
			$_POST['start_show'] = (isset($_POST['start_show']) ? $_POST['start_show'] : '0000:00:00');
			$_POST['end_show'] = (isset($_POST['end_show']) ? $_POST['end_show'] : '0000:00:00');
			$_POST['show_qty'] = (isset($_POST['show_qty']) ? $_POST['show_qty'] : '0');
            $_POST['visible'] = (isset($_POST['visible']) ? $_POST['visible'] : '1');
			$_POST['display_type'] = (isset($_POST['display_type']) ? $_POST['display_type'] : '1');
			$_POST['custome_ads_code'] = (isset($_POST['custome_ads_code']) ? htmlspecialchars($_POST['custome_ads_code'], ENT_QUOTES) : false);
			$_POST['ads_type'] = (isset($_POST['ads_type']) ? $_POST['ads_type'] : '0');
			$_POST['active_pages'] = (isset($_POST['active_pages']) ? $_POST['active_pages'] : false);
			
			$this->update_old_image($_POST['Link'], $_POST['NewTab'], $_FILES['NewFile'], $_POST['ImgAlt'], $_POST['Position'], $_POST['OldImageName'], $_POST['OldImageId'], $_POST['status'], $_POST['start_show'], $_POST['end_show'], $_POST['show_qty'], $_POST['visible'], $_POST['display_type'], $_POST['custome_ads_code'], $_POST['ads_type'], $_POST['active_pages']);
		}			
		
		$banner_info['url'] = isset($banner_info['url']) ? $banner_info['url'] : false;
		$banner_info['new_window'] = isset($banner_info['new_window']) ? $banner_info['new_window'] : false;
		$banner_info['alt'] = isset($banner_info['alt']) ? $banner_info['alt'] : false;
		$banner_info['position'] = isset($banner_info['position']) ? $banner_info['position'] : false;
		$banner_info['image'] = isset($banner_info['image']) ? $banner_info['image'] : false;
		$banner_info['type'] = isset($banner_info['type']) ? $banner_info['type'] : false;
		$banner_info['start_show'] = isset($banner_info['start_show']) ? $banner_info['start_show'] : false;
		$banner_info['end_show'] = isset($banner_info['end_show']) ? $banner_info['end_show'] : false;
		$banner_info['status'] = isset($banner_info['status']) ? $banner_info['status'] : false;
		$banner_info['show_qty'] = isset($banner_info['show_qty']) ? $banner_info['show_qty'] : false;
		$banner_info['visible'] = isset($banner_info['visible']) ? $banner_info['visible'] : false;
		$banner_info['display_type'] = isset($banner_info['display_type']) ? $banner_info['display_type'] : false;
		$banner_info['custome_ads_code'] = isset($banner_info['custome_ads_code']) ? htmlspecialchars_decode($banner_info['custome_ads_code'], ENT_QUOTES) : false;
		$banner_info['ads_type'] = isset($banner_info['ads_type']) ? $banner_info['ads_type'] : false;
		$banner_info['active_pages'] = isset($banner_info['active_pages']) ? $banner_info['active_pages'] : false;

		if($page > 0)
		{
			$cat_name = Db::getInstance()->getRow('
				SELECT name FROM '._DB_PREFIX_.'blmod_upl_banner_block
				WHERE id = "'.$page.'"
			');
			
			$block_name = $cat_name['name'];
		}
		
		if(!empty($product_id) and empty($category_id))
		{
			$product_info = Db::getInstance()->getRow('
				SELECT p.id_product, pl.name
				FROM '._DB_PREFIX_.'product p
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON
				(pl.id_product = p.id_product and pl.id_lang = "'.$lang_id_user.'")
				WHERE p.id_product = "'.$product_id.'"				
			');
		
			$block_name = isset($product_info['name']) ? $product_info['name'] : $this->l('Product id: ').$product_id;
		}
        elseif(!empty($category_id))
        {
             $product_info = Db::getInstance()->getRow('
				SELECT name
				FROM '._DB_PREFIX_.'category_lang
				WHERE id_category = "'.$category_id.'" AND id_lang = "'.$lang_id_user.'"
			');

			$block_name = isset($product_info['name']) ? $product_info['name'] : $this->l('Category id: ').$category_id;
        }
		
		if(!empty($id))
		{
			$banner_name = Db::getInstance()->ExecuteS('
				SELECT banner_name, lang_id
				FROM '._DB_PREFIX_.'blmod_upl_name
				WHERE banner_id = "'.$id.'"
			');
			
			if(!empty($banner_name))
				foreach($banner_name as $t)
					$banner_name['name_'.$t['lang_id']] = $t['banner_name'];	
		}		
		
		$this->_html .= '
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
		<fieldset><legend><img src="../img/admin/tab-preferences.gif" alt="" title="" />'.$block_name.'</legend>';
		
			if(!empty($product_id))	
			{
				$open_name = $this->l('Open product');
				$open_link = $this->get_front_page_link('product', $product_id);
			}
				
			if(!empty($category_id))
			{
				$open_name = $this->l('Open category');
				$open_link = $this->get_front_page_link('category', $category_id);
			}
			
			if(!empty($open_name))			
				$this->_html .= '<div class="open_product_link"><a href="'.$open_link.'" target="_blank">['.$open_name.']</a></div>';	
				
			$this->_html .= '
			<table border="0" width="100%" cellpadding="3" cellspacing="0">
				<tr>
					<td width="970">
						<form action="'.$this->get_current_address().'" method="post" enctype="multipart/form-data">
							<table border="0" width="100%" cellpadding="3" cellspacing="0">
								<tr>
									<td width="20"><img src="../img/admin/home.gif" alt="" title="" /></td>
									<td width="150"><b>'.$this->l('Title:').'</b></td>
									<td colspan = "5">';									
										foreach($languages as $language)
										{
											if(!isset($banner_name['name_'.$language['id_lang']]))
												$banner_name['name_'.$language['id_lang']] = '';
				
											$this->_html .= '<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == intval(Configuration::get('PS_LANG_DEFAULT')) ? 'block' : 'none').'; float: left;">
												<input type = "text" name="name_'.$language['id_lang'].'" value = "'.$banner_name['name_'.$language['id_lang']].'" size="50" />
											</div>';
										}
										$this->_html .= $this->displayFlags($languages, intval(Configuration::get('PS_LANG_DEFAULT')), 'name', 'name', true);
															
										$this->_html .='
									</td>
								</tr>
								<tr>
									<td width="20"><img src="../img/admin/subdomain.gif" alt="" title="" /></td>
									<td width="150"><b>'.$this->l('Link:').'</b></td>
									<td colspan = "5">
										<input type="text" name="Link"';
										if(!$id)										
											$this->_html .= 'value ="http://"';										
										elseif($banner_info['url'])										
											$this->_html .= 'value = "' . $banner_info['url'] . '"';
										
										$this->_html .= 'size="30"/>
										<label for="tab_new_windows">'.$this->l('in a new window:'). '
										<input id="tab_new_windows" type="checkbox" name="NewTab" value="1"';
										if($banner_info['new_window'])										
											$this->_html .= ' checked ';
										
										$this->_html .= '
										/></label>
									</td>
								</tr>
								<tr>
									<td width="20"><img src="../img/admin/access.png" /></td>
									<td width="150"><b>'.$this->l('Status:').'</b></td>
									<td colspan="4"><input type="checkbox" name="status"';
									
									if($id)									
										$this->_html .= $this->status($banner_info['status']);
									else
										$this->_html .= 'value = "1" checked/>';
									
									$this->_html .= '
									</td>
								</tr>
								<tr>
									<td width="20"><img src="../img/admin/time.gif" /></td>
									<td width="150"><b>'.$this->l('Start show:').'</b></td>
									<td colspan="4"><input class="datepicker" type="text" id="start_show" name="start_show" value = "'.$banner_info['start_show'].'" size = "10"/>
									</td>
								</tr>							
								<tr>
									<td width="20"><img src="../img/admin/time.gif" /></td>
									<td width="150"><b>'.$this->l('End show:').'</b></td>
									<td colspan="4"><input class="datepicker" type="text" id="end_show" name="end_show" value = "'.$banner_info['end_show'].'" size = "10"/>
									</td>
								</tr>								
								<tr>
									<td width="20"><img src="../img/admin/invoice.gif" /></td>
									<td width="150"><b>'.$this->l('Displays qty:').'</b></td>
									<td colspan="4"><input type="text" name="show_qty" value = "'.$banner_info['show_qty'].'" size = "5"/>
									</td>
								</tr>';
								
								if($show_display_type)
								{
									$this->_html .= '
									<tr>
										<td width="20"><img src="../img/admin/summary.png" /></td>
										<td width="150"><b>'.$this->l('Display type:').'</b></td>
										<td colspan="4">
											<label for="display_type_popup">
												'.$this->l('pop-up').' <input id="display_type_popup" '; if($banner_info['display_type'] == 1) $this->_html .= ' checked '; $this->_html .= 'type="radio" name="display_type" value = "1"/> |
											</label>
											<label for="display_type_standart">
												'.$this->l('standart').' <input id="display_type_standart" '; if($banner_info['display_type'] == 0 or empty($banner_info['display_type'])) $this->_html .= ' checked '; $this->_html .= 'type="radio" name="display_type" value = "0"/>
											</label>
										</td>
									</tr>';
								}
								else
									$this->_html .= '<input type="hidden" name="display_type" value="0" />';									
								
								$this->_html .= '
								<tr>
									<td width="20"><img src="../img/admin/tab-customers.gif" /></td>
									<td width="150"><b>'.$this->l('Visible:').'</b></td>
									<td colspan="4">
										<label for="tab_register">
											'.$this->l('register').' <input id="tab_register" '; if($banner_info['visible'] == 1) $this->_html .= ' checked '; $this->_html .= 'type="radio" name="visible" value = "1"/>
										</label> |
										<label for="tab_unregistered">
											'.$this->l('unregistered').' <input id="tab_unregistered" '; if($banner_info['visible'] == 2) $this->_html .= ' checked '; $this->_html .= ' type="radio" name="visible" value = "2"/>
										</label> |
										<label for="tab_all_users">
											'.$this->l('all users').' <input id="tab_all_users" '; if($banner_info['visible'] == 3 or !$banner_info['visible']) $this->_html .= ' checked '; $this->_html .= ' type="radio" name="visible" value = "3"/>
										</label>
									</td>
								</tr>							
								<tr>
									<td width="20"><img src="../img/admin/localization.gif" /></td>
									<td width="150"><b>'.$this->l('Languages:').'</b></td>
									<td colspan="4">';
									if($id)
									{
										$lang_db = Db::getInstance()->ExecuteS('
											SELECT lang_id
											FROM '._DB_PREFIX_.'blmod_upl_lang
											WHERE banner = "'.$id.'"
										');
										
										$lang_m = array();
										
										if(!empty($lang_db))
										{
											foreach($lang_db as $lbd)
												$lang_m[] = $lbd['lang_id'];
										}
									}

									$languages = Language::getLanguages(false);
									
									$img_margin = 'style="margin-bottom: 3px;"';
									
									if(_PS_VERSION_ >= '1.4')
										$img_margin = '';
									
									foreach($languages as $l)
									{
										$checked = '';
										
										if(!$id or in_array($l['id_lang'], $lang_m))
											$checked = 'checked';
																			
										$this->_html .= '
											<label for="visible_flag_'.$l['id_lang'].'">
												<img '.$img_margin.' src= "../img/l/'.$l['id_lang'].'.jpg" alt="'.$l['name'].'" title="'.$l['name'].'" /><input id="visible_flag_'.$l['id_lang'].'" type="checkbox" name="lang_'.$l['id_lang'].'" value="1" '.$checked.' /> |
											</label>
										';
									}
								$this->_html .= '
									</td>
								</tr>
								<tr>
									<td width="20"><img src="../img/admin/affiliation.png" /></td>
									<td width="150"><b>'.$this->l('Active pages:').'</b></td>
									<td colspan="4">
										<textarea name="active_pages" style="width: 500px; height: 45px; font-size: 11px;">'.$banner_info['active_pages'].'</textarea>
										<div class="comments" style="font-size: 10px;">
											'.$this->l('[The banner will be shown only to the embedded pages, please insert full page address. Separate new line (ENTER). Leave blank to display in all pages. If you need only a first part address, add *]').'
										</div>
									</td>
								</tr>									
								<tr>
									<td width="20"><img src="../img/admin/picture.gif" /></td>
									<td width="150"><b>'.$this->l('Image:').'</b></td>
									<td colspan="4"><input type="file" name="NewFile" size = "49"/>
									</td>
								</tr>';
								if($banner_info['image'])
								{
									$this->_html .= '
									<tr>
										<td width="20"><img src="../img/admin/picture.gif" /></td>
										<td width="150"><b>'.$this->l('Old image').'</b></td>
										<td colspan="4">									
									';
									$folder = '../modules/banner_upload/banner_img/'.$banner_info['image'];
									$img_size = $this->resize($folder);
								
									if(strtolower($banner_info['type']) != 'x-shockwave-flash')
										$this->_html .='<img width="'.$img_size[0].'" height="'.$img_size[1].'" src = "'.$folder.'" /><input type = "hidden" value = "'.$banner_info['image'].'" name = "image" />';
									else
									{
										$this->_html .='
										<object id="FlashID" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$img_size[0].'" height="'.$img_size[1].'">
										<param name="movie" value= "'.$folder.'" /><param name="quality" value="high" />
										<param name="wmode" value="opaque" />
										<param name="swfversion" value="6.0.65.0" />
										<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don\'t want users to see the prompt. -->
										<param name="expressinstall" value="Scripts/expressInstall.swf" />
										<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
										<!--[if !IE]>-->
										<object type="application/x-shockwave-flash" data= "'.$folder.'" width="'.$img_size[0].'" height="'.$img_size[1].'">
										<!--<![endif]-->
										<param name="quality" value="high" />
										<param name="wmode" value="opaque" />
										<param name="swfversion" value="6.0.65.0" />
										<param name="expressinstall" value="Scripts/expressInstall.swf" />
										<!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
										<div>
										<p>Content on this page requires a newer version of Adobe Flash Player.</p>
										<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" width="112" height="33" /></a></p>
										</div>
										<!--[if !IE]>-->
										</object>
										<!--<![endif]-->
										</object>
										';
									}
									$this->_html .= '</td>
									</tr>';
								}
								
								if($block_stat_id != 6)
								{
									$this->_html .='
									<tr>
										<td width="20"><img src="../img/admin/prefs.gif" /></td>
										<td width="150"><b>'.$this->l('Custome ads code:').'</b></td>
										<td colspan="4">
											<textarea name="custome_ads_code" style="width: 500px; height: 150px;">'.$banner_info['custome_ads_code'].'</textarea>
										</td>
									</tr>									
									<tr>
										<td width="20"><img src="../img/admin/tab-tools.gif" /></td>
										<td width="150"><b>'.$this->l('Ads type:').'</b></td>
										<td colspan="4">
											<label for="tab_image">
												'.$this->l('Image').' <input id="tab_image" '; if(empty($banner_info['ads_type'])) $this->_html .= ' checked '; $this->_html .= 'type="radio" name="ads_type" value = "0"/>
											</label> |
											<label for="tab_custome_html">
												'.$this->l('Custome code').' <input id="tab_custome_html" '; if(!empty($banner_info['ads_type'])) $this->_html .= ' checked '; $this->_html .= ' type="radio" name="ads_type" value = "1"/>
											</label>
										</td>
									</tr>';
								}
								
								$this->_html .='
								<input type = "hidden" name = "Position" value = "'.$page.'" />
							</table>							
							<table border="0" width="500" cellpadding="3" cellspacing="0">
								<tr>
									<td width="500" colspan="2"><br/>';
									if(!$banner_info['position'])
									{
										$this->_html .= '<center><input type="submit" name="InsertNewImage" value="'.$this->l('Insert').'" class="button" /></center>';
									}
									else
									{
										$this->_html .= '<input type="hidden" name="OldImageId" value = "';
										$this->_html .= $id . '"/>';	
										$this->_html .= '<input type="hidden" name="OldImageName" value = "';
										$this->_html .= $banner_info['image'];
										$this->_html .= '"/><center><input type="submit" name="UpdateOldImage" value="'.$this->l('Update').'" class="button" /></center>';
									}
									$this->_html .= '</td>
								</tr>
							</table>
						</form>
					</td>
					<td width="400" valign="top">';
						
					$this->_html .= '</td>
				</tr>
			</table>
		</fieldset>';
	}
	
	public function update_qty($id)
	{
		Db::getInstance()->Execute('
			UPDATE '._DB_PREFIX_.'blmod_upl_banner SET
			show_qty_now = show_qty_now + 1
			WHERE id = "'.$id.'"
		');
		
		$now = date('Y-m-d');
		
		$check_date = Db::getInstance()->getRow('
			SELECT banner_id
			FROM '._DB_PREFIX_.'blmod_upl_views
			WHERE banner_id = "'.$id.'" AND date = "'.$now.'"
		');
		
		if(empty($check_date['banner_id']))		
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blmod_upl_views (`banner_id`, `date`, `views`) VALUES ("'.$id.'", "'.$now.'", "1")');
		else
			Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'blmod_upl_views SET views = views + 1 WHERE banner_id = "'.$id.'" AND `date` = "'.$now.'"');
	}
	
	public function get_banner($block, $type='=')
	{
		global $cookie;
		
		$id_lang = $cookie->id_lang;

        $visible = 2;

		if(_PS_VERSION_ >= '1.5')
		{
			if($this->context->customer->isLogged())
				$visible = 1;
		}
		else		
		{
			if($cookie->isLogged())
				$visible = 1;	
		}
		
		$banner_r = Db::getInstance()->ExecuteS('
			SELECT b.id, b.image, b.type, b.url, b.new_window, b.width, b.height, b.show_qty, b.display_type, e.slides,
			ln.banner_name, e.rand, b.custome_ads_code, b.ads_type, b.active_pages
			FROM '._DB_PREFIX_.'blmod_upl_banner_block e
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_banner b ON
			e.name = b.position
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_lang l ON
			(l.banner = b.id AND l.lang_id = "'.$id_lang.'")
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_name ln ON
			(ln.banner_id = b.id AND ln.lang_id = "'.$id_lang.'")
			WHERE l.lang_id = "'.$id_lang.'" AND e.rand = "1" AND e.status = "1" AND b.status = "1" AND (b.position '.$type.' "'.$block.'") AND b.start_show < NOW()
			AND (b.end_show > NOW() OR b.end_show = "0000-00-00") AND (b.show_qty > b.show_qty_now OR b.show_qty < 1) AND (b.visible = "3" OR b.visible = "'.$visible.'")
			ORDER BY RAND()
			LIMIT 1
		');
		
		$banner_a = Db::getInstance()->ExecuteS('
			SELECT b.id, b.image, b.type, b.url, b.new_window, b.width, b.height, b.show_qty, b.display_type, e.slides,
			ln.banner_name, b.custome_ads_code, b.ads_type, b.active_pages
			FROM '._DB_PREFIX_.'blmod_upl_banner_block e
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_banner b ON
			e.name = b.position
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_lang l ON
			(l.banner = b.id AND l.lang_id = "'.$id_lang.'")
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_name ln ON
			(ln.banner_id = b.id AND ln.lang_id = "'.$id_lang.'")
			WHERE l.lang_id = "'.$id_lang.'" AND e.rand != "1" AND e.status = "1" AND b.status = "1" AND (b.position '.$type.' "'.$block.'") AND b.start_show < NOW()
			AND (b.end_show > NOW() OR b.end_show = "0000-00-00") AND (b.show_qty > b.show_qty_now OR b.show_qty < 1) AND (b.visible = "3" OR b.visible = "'.$visible.'")
			ORDER by b.recordListingID ASC
		');
		
		$banner = array_merge($banner_r, $banner_a);
		
		$count = count($banner);
		
		for($i=0; $i<$count; $i++)		
		{
			if(!$this->check_active_pages($banner[$i]['active_pages']))
			{
				unset($banner[$i]);
				continue;
			}
			
			$this->update_qty($banner[$i]['id']);
			$banner[$i]['custome_ads_code'] = isset($banner[$i]['custome_ads_code']) ? htmlspecialchars_decode($banner[$i]['custome_ads_code'], ENT_QUOTES)  : false;
		}
		
		return $banner;
	}
	
	public function get_banner_extra()
	{
		global $cookie;
		
		$id_lang = $cookie->id_lang;

        $visible = 2;

        if($cookie->isLogged())
		    $visible = 1;

		$banner_r = Db::getInstance()->ExecuteS('
			SELECT b.image, b.type, b.url, b.new_window, b.width, b.height, b.show_qty, b.id AS b_id,
			e.id, e.rand, e.val_x, e.val_y, e.pos_x, e.pos_y,
			ln.banner_name, b.custome_ads_code, b.ads_type
			FROM '._DB_PREFIX_.'blmod_upl_banner_block e
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_banner b ON
			e.id = b.position
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_lang l ON
			(l.banner = b.id AND l.lang_id = "'.$id_lang.'")
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_name ln ON
			(ln.banner_id = b.id AND ln.lang_id = "'.$id_lang.'")
			WHERE l.lang_id = "'.$id_lang.'" AND e.rand = "1" AND b.status = "1" AND e.status = "1" AND b.start_show < NOW() AND (b.end_show > NOW() OR b.end_show = "0000-00-00")
			AND (b.show_qty > b.show_qty_now OR b.show_qty < 1) AND e.name NOT LIKE "%product-blmod_%" AND e.name NOT LIKE "%category-blmod_%" AND e.name NOT LIKE "%footer_circle%" 
			AND (b.visible = "3" OR b.visible = "'.$visible.'") AND e.filter_type = "0"
			ORDER BY RAND()
			LIMIT 1;
		');
		
		$banner_a = Db::getInstance()->ExecuteS('
			SELECT b.image, b.type, b.url, b.new_window, b.width, b.height, b.show_qty, b.id AS b_id,
			e.id, e.rand, e.val_x, e.val_y, e.pos_x, e.pos_y,
			ln.banner_name, b.custome_ads_code, b.ads_type
			FROM '._DB_PREFIX_.'blmod_upl_banner_block e
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_banner b ON
			e.id = b.position
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_lang l ON
			(l.banner = b.id AND l.lang_id = "'.$id_lang.'")
			LEFT JOIN '._DB_PREFIX_.'blmod_upl_name ln ON
			(ln.banner_id = b.id AND ln.lang_id = "'.$id_lang.'")
			WHERE l.lang_id = "'.$id_lang.'" AND e.rand != "1" AND b.status = "1" AND e.status = "1" AND b.start_show < NOW() AND (b.end_show > NOW() OR b.end_show = "0000-00-00")
			AND (b.show_qty > b.show_qty_now OR b.show_qty < 1) AND e.name NOT LIKE "%product-blmod_%" AND e.name NOT LIKE "%category-blmod_%" AND e.name NOT LIKE "%footer_circle%" 
			AND (b.visible = "3" OR b.visible = "'.$visible.'") AND e.filter_type = "0"
			ORDER by e.id, b.recordListingID ASC
		');
		
		$banner = array_merge($banner_r, $banner_a);
		$count = count($banner);
		
		for($i=0; $i<$count; $i++)
		{			
			if(!$this->check_active_pages($banner[$i]['active_pages']))
			{
				unset($banner[$i]);
				continue;
			}
			
			$this->update_qty($banner[$i]['b_id']);
			
			$n = isset($banner[$i+1]['id']) ? $banner[$i+1]['id'] : 'none2';
			$banner[$i]['next'] = $n;			
			$banner[$i]['custome_ads_code'] = isset($banner[$i]['custome_ads_code']) ? htmlspecialchars_decode($banner[$i]['custome_ads_code'], ENT_QUOTES)  : false;	
		}
	
		return $banner;
	}
	
	public function check_active_pages($active_pages=false)
	{
		if(empty($active_pages))
			return true;
			
		$active_pages_array = preg_split('/$\R?^/m', $active_pages);
		
		$current_address = $this->current_page_address();
				
		foreach($active_pages_array as $p)
		{
			$page = trim(htmlspecialchars_decode($p, ENT_QUOTES));
		
			if(empty($page))
				continue;
				
			$last_element = $page[strlen($page)-1];
			
			if($last_element == '*')
			{
				$find_address = strpos($current_address, substr($page, 0, -1));
								
				if($find_address === 0)
					return true;					
				
			}
			elseif($page == $current_address)			
				return true;			
		}
		
		return false;
	}
	
	function current_page_address()
	{
		$pageURL = 'http';
		
		if(@$_SERVER["HTTPS"] == "on") 
			$pageURL .= "s";
		
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else 
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		return $pageURL;
	}
	
	public function hookHome($params)
	{
		global $smarty;
		
		$banner = $this->get_banner('home');
		$smarty->assign('header_show_files', 2); 
		
		$smarty->assign('banner', $banner);
		$smarty->assign('header_status', false);

		return $this->display(__FILE__, 'horizontal_banner.tpl');
	}
	
	public function hookHeader($params)
	{
		global $smarty;
		
		$banner = $this->get_banner('header');
		$banner_extra = $this->get_banner_extra();
		
		$smarty->assign('banner', $banner);
		$smarty->assign('banner_extra', $banner_extra);
		$smarty->assign('header_status', true);        
		
		$smarty->assign('header_show_files', 1); 

        $category_id = Tools::getValue('id_category');

		if(!empty($category_id))
        {
            $banner_c = $this->get_banner('%category-blmod_%'.$category_id, 'LIKE');
            $smarty->assign('banner_category', $banner_c);			
        }
		
		return $this->display(__FILE__, 'horizontal_banner.tpl');
	}
	
	public function hookLeftColumn($params)
	{
		global $smarty;
		
		$banner = $this->get_banner('left');
		
		$smarty->assign('banner', $banner);
		return $this->display(__FILE__, 'vertical_banner.tpl');
	}
	
	public function hookRightColumn($params)
	{
		global $smarty;
		
		$banner = $this->get_banner('right');
		
		$smarty->assign('banner', $banner);
		return $this->display(__FILE__, 'vertical_banner.tpl');
	}
	
	public function hookFooter($params)
	{
		global $smarty;
		
		$banner = $this->get_banner('footer');
		
		$smarty->assign('header_show_files', 3); 
		
		$smarty->assign('banner', $banner);
		$smarty->assign('header_status', false);
		
		//Show footer cicle
		$banner_cicle = $this->get_banner('footer_circle');
		
		if(!empty($banner_cicle[0]))
		{
			$smarty->assign('banner_cicle', $banner_cicle[0]);
			$smarty->assign('banner_cicle_count', count($banner_cicle));
		}
		
		return $this->display(__FILE__, 'horizontal_banner.tpl');
	}
	
	public function hookProductFooter($params)
	{
		global $smarty;
		
		$smarty->assign('header_show_files', 4); 
		
		$product_id = Tools::getValue('id_product');

		$smarty->assign('is_header', false);
		
		if(!empty($product_id))
        {
            $product_id = (int)$product_id;

            $filters = Db::getInstance()->ExecuteS(' 
				SELECT filter_text, filter_type, name, filter_type_name, filter_type_desc, filter_type_att_name
				FROM '._DB_PREFIX_.'blmod_upl_banner_block
				WHERE filter_type > "0" AND status = "1"
			');			

			$sql_s = '';			
			
			if(!empty($filters))
			{				
				if(_PS_VERSION_ >= '1.5')
					$id_lang = $this->context->cookie->id_lang;
				else
				{
					global $cookie;	
					
					$id_lang = $cookie->id_lang;
				}
				
				$sql_s = '" OR ';
				
				foreach($filters as $f)
				{					
					$types = array();
					$sql = false;
					
					if(!empty($f['filter_type_name']))
						$types[] = 'LOWER(name) LIKE "%'.strtolower($f['filter_text']).'%"';
						
					if(!empty($f['filter_type_desc']))
						$types[] = 'LOWER(description) LIKE "%'.strtolower($f['filter_text']).'%"';
							
					if(!empty($types))
					{
						$sql = ' AND ('.implode(' OR ', $types).')';
						
						$find = Db::getInstance()->getRow(' 
							SELECT id_product, name, description, id_lang
							FROM '._DB_PREFIX_.'product_lang
							WHERE id_product = "'.$product_id.'" AND id_lang = "'.$id_lang.'" '.$sql.'
						');						
					}
				
					if(empty($find['id_product']) and !empty($f['filter_type_att_name']))
					{
						$product_class_name = 'ProductCore';
			
						if(!class_exists($product_class_name, false))
							$product_class_name = 'Product';

						$product_class = new $product_class_name();

						$product_class->id = $product_id;
						$attributes = $product_class->getAttributesGroups($id_lang);
						
						if(!empty($attributes))
						{
							$f['filter_type_att_name'] = strtolower($f['filter_type_att_name']);
							
							foreach($attributes as $a)
							{
								$find_att = strpos(' '.strtolower($a['attribute_name']), $f['filter_type_att_name']);
								
								if(!empty($find_att))
								{
									$find['id_product'] = $product_id;
									break;
								}
							}
						}							
					}
					
					if(!empty($find['id_product']))					
						$sql_s .= ' b.position = "'.$f['name'].'" OR';					
				}
				
				$sql_s = rtrim($sql_s, '" OR ');
			}
			
            $banner_p = $this->get_banner('product-blmod_'.$product_id.$sql_s);

            $smarty->assign('banner', $banner_p);
        }			
		
		return $this->display(__FILE__, 'horizontal_banner.tpl');
	}
	
	public function get_current_address()
	{
		return str_replace('&ch_m_qty=1', '', $_SERVER['REQUEST_URI']);
	}
	
	public function pr($text=false)
	{
		echo '<pre>';
		print_r($text);
		echo '</pre>';
	}
}
?>