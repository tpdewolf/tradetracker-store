<?php
	global $wpdb;
	$pro_table_prefix=$wpdb->prefix.'tradetracker_';
	define('PRO_TABLE_PREFIX', $pro_table_prefix);
	$ttstoretable = PRO_TABLE_PREFIX."store";
	$ttstorelayouttable = PRO_TABLE_PREFIX."layout";
	$ttstoremultitable = PRO_TABLE_PREFIX."multi";
	wp_clear_scheduled_hook('xml_update');
	$structure = "drop table if exists $ttstoretable";
	$wpdb->query($structure);
	$structure2 = "drop table if exists $ttstorelayouttable";
	$wpdb->query($structure2); 
	$structure3 = "drop table if exists $ttstoremultitable";
	$wpdb->query($structure3); 
	delete_option("Tradetracker_xml");
	delete_option("Tradetracker_xmlname");
	delete_option("Tradetracker_xmlupdate");
	delete_option("Tradetracker_debugemail");
	delete_option("Tradetracker_currency");
	delete_option("Tradetracker_currencyloc");
	delete_option("Tradetracker_newcur");
	delete_option("Tradetracker_extra");
	delete_option("Tradetracker_xml_extra");
	delete_option("TTstoreversion");
	delete_option("Tradetracker_width");
	delete_option("Tradetracker_debugemail");
	delete_option("Tradetracker_importtool");
	delete_option("Tradetracker_removelayout");
	delete_option("Tradetracker_removestores");
	delete_option("Tradetracker_removeproducts");
	delete_option("Tradetracker_removexml");
	delete_option("Tradetracker_removeother");
	delete_option("tt_premium_provider");
	delete_option("tt_premium_function");
	delete_option("Tradetracker_premiumupdate");
	delete_option("Tradetracker_premiumaccepted");
	delete_option("Tradetracker_premiumapi");
	delete_option("Tradetracker_adminheight");
	delete_option("Tradetracker_adminwidth");
	delete_option("Tradetracker_searchlayout");
	delete_option("Tradetracker_loadextra");
?>