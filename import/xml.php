<?php
function xml_updater($xmlfilecount = "0", $itemsadded = "0") {
	//load all needed variables
	global $wpdb;
	global $processed;
	global $itemsadded;
	global $filenum;
	GLOBAL $errorfile;
	GLOBAL $oserrorfile;
	global $ttstoretable;
	global $foldersplits;

	//prepare database 

	if ( $xmlfilecount == "0" && isset($_GET['xmlfilecount'])){
		$xmlfilecount = $_GET['xmlfilecount'];
	}
	if (isset($_GET['itemsadded'])){
		$itemsadded = $_GET['itemsadded'];
	}

	if ($xmlfilecount == "0" && !isset($_GET['xmlfilecount'])){
		$emptytable = "TRUNCATE TABLE `$ttstoretable`";
		$wpdb->query($emptytable);
		$directory = dir($foldersplits); 
		while ((FALSE !== ($item = $directory->read())) && ( ! isset($directory_not_empty)))
		{  
			if ($item != '.' && $item != '..')
       			{  
				$files = glob($foldersplits."/*xml");
				if(count($files) > 0)
				{
					if (is_array($files)) {
						foreach($files as $file){
							unlink($file); 
						}
					}
				}
			}  
		}
		// Close the directory  
		$directory->close(); 
	} 


	//prepare cache file
	$basefilename = "TTStoreXML";
	//$filenum = "0"; // start chunk file number at 1
	if(!is_writable($foldersplits)){
		exit;
	}
	
	//get xml details from database
	$Tradetracker_xml = get_option("Tradetracker_xml");
	$Tradetracker_xmlname = get_option("Tradetracker_xmlname");


	//check if splits directory is empty else empty it

	$keys = array_keys($Tradetracker_xml);
	$key = $keys[$xmlfilecount];
	$value = $Tradetracker_xml[$key];


	$xmlfeedID = "0"; 
	$file = $Tradetracker_xml;
	$recordnum = "0";
	$processed = "0";
	$filenum++;
	$value($xmlfeedID, $basefilename, $key,$filenum,$recordnum,$processed,'products', 'itemXMLtag');
	$xmlfeedID++;
	fill_database1();
	$directory = dir($foldersplits); 
	while ((FALSE !== ($item = $directory->read())) && ( ! isset($directory_not_empty)))  
		{  
		if ($item != '.' && $item != '..')
   		{  
			$files = glob($foldersplits."/*xml");
			if(count($files) > 0)
				{	
				if (is_array($files)) {
					foreach($files as $filedel){
						unlink($filedel); 
					}
				}
			}
		}  
	}
	$directory->close();
	if ($xmlfilecount < count($Tradetracker_xml)-1){
		$xmlfilecount++;
		if($xmlfilecount == "10" || $xmlfilecount == "20" || $xmlfilecount == "20" || $xmlfilecount == "40" || $xmlfilecount == "50"){

?>
<script type="text/javascript">
window.location.href='<?php echo "admin.php?page=tt-store&update=yes&xmlfilecount=$xmlfilecount&itemsadded=$itemsadded"; ?>';
</script>
<?php
		} else {
			xml_updater($xmlfilecount, $filenum); 
		}
	} else {
		if(!empty($errorfile)){
			if(get_option("Tradetracker_debugemail")==1){
				$message = "Hi,". "\r\n" ."";
				$message .= "". "\r\n" ."You receive this message because you are using the TradeTracker-Store plugin. It just tried to import the XML feed(s) but encountered an error.". "\r\n" ."";
				$message .= "". "\r\n" ."The following XML splits are giving an error or are empty. So it could be there are no items imported from that feed.". "\r\n" ."";
				$message .= $errorfile;
				$message .= "". "\r\n" ."". "\r\n" ."To disable this email go to ".get_bloginfo('url')."/wp-admin/admin.php?page=tt-store&option=pluginsettings and select no for the option Get email when import fails:";
				$to      = ''.get_bloginfo('admin_email').'';
				$subject = 'There was an issue with importing the XML Feed';
				$headers = 'From: '.get_bloginfo('admin_email').'' . "\r\n" . 'Reply-To: '.get_bloginfo('admin_email').'' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);
			}
			$osmessage = "<strong>The following XML splits are giving an error or are empty. So they are possibly not imported.</strong>";
			$osmessage .= $oserrorfile;
			echo "<div class='error'>".$osmessage."</div>";
		}
	}
}
?>