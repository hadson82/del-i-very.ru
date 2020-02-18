<?php
if(empty($_GET['b_id']))
	die();
	
require(dirname(__FILE__).'/../../config/config.inc.php');

$id = htmlspecialchars(trim($_GET['b_id']), ENT_QUOTES);

$banner = Db::getInstance()->getRow('SELECT custome_ads_code FROM '._DB_PREFIX_.'blmod_upl_banner WHERE id = "'.$id.'"');

if(empty($banner['custome_ads_code']))
	die();
	
echo htmlspecialchars_decode($banner['custome_ads_code'], ENT_QUOTES);
?>