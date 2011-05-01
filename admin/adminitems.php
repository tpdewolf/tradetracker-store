<?php
function adminstore_items()
{
	global $wpdb;
	if( get_option( Tradetracker_update ) == "" ){
		$update= "24";
	} else {
		$update= get_option( Tradetracker_update );
	}
	$Tradetracker_xml = get_option( Tradetracker_xml );
	if ($Tradetracker_xml == null) 
	{
		echo "No XML filled in yet please change the settings first.";
	} else {
	$context = stream_context_create(array(
    'http' => array(
        'timeout' => 3      // Timeout in seconds
    )
	));
		$cache_time = 3600*$update; // 24 hours
		$cache_file = WP_PLUGIN_DIR . '/tradetracker-store/cache.xml';
		$timedif = @(time() - filemtime($cache_file));
		if (file_exists($cache_file) && $timedif < $cache_time) 
		{
			if ('' == file_get_contents($cache_file))
				{
		     			$string = file_get_contents(''.$Tradetracker_xml.'', 0, $context);
		    			if ($f = @fopen($cache_file, 'w')) {
        					fwrite ($f, $string, strlen($string));
        					fclose($f);
    					}
					fill_database();
				}  

		} else {
    			$string = file_get_contents(''.$Tradetracker_xml.'', 0, $context);
    			if ($f = @fopen($cache_file, 'w')) {
        			fwrite ($f, $string, strlen($string));
        			fclose($f);
    			}
			fill_database();
		}
	return adminshow_items();
	}
}

function adminshow_items()
{
	if (!current_user_can('manage_options'))
	{
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
    	if( isset($_POST['posted']) && $_POST['posted'] == 'Y' ) 
	{
                $Tradetracker_items = $_POST['item'];
		$Tradetracker_items = implode(",", $Tradetracker_items);
		if($_POST['itemsother']!="")
		{
			$Tradetracker_items = $Tradetracker_items.",".$_POST['itemsother'];
		}
		if ( get_option(Tradetracker_productid)  != $Tradetracker_items) 
		{
			update_option(Tradetracker_productid, $Tradetracker_items );
  		}
		echo "<div class=\"updated\"><p><strong>Settings Saved</strong></p></div>";	
	}
	if($_GET['order']==null)
	{
		$order = "name";
	} else {
		$order = $_GET['order'];
	}
	global $wpdb; 
	$limit = $_GET['limit'];
	$currentpage = $_GET['currentpage'];
	if (!($limit))
	{
		$limit = 100;
	}
	if (!($currentpage)){
		$currentpage = 0;
	}
	$table = PRO_TABLE_PREFIX."store";
	$countquery=$wpdb->get_results("SELECT * FROM ".$table."");
	$numrows = $wpdb->num_rows;
	$pages = intval($numrows/$limit); // Number of results pages.
	if ($numrows%$limit) 
	{
		$pages++;
	} 
	$current = ($currentpage/$limit) + 1;
	if (($pages < 1) || ($pages == 0)) 
	{
		$total = 1;
	} else {
		$total = $pages;
	} 
	$first = $currentpage + 1;
	if (!((($currentpage + $limit) / $limit) >= $pages) && $pages != 1) 
	{
		$last = $currentpage + $limit;
	} else {
		$last = $numrows;
	}
?>
<style type="text/css" media="screen">
#screenshot{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:5px;
	display:none;
	color:#fff;
	max-width:400px;
	max-height:400px;
	}

/*  */
</style>
<?php
	echo '<div class="wrap">';
	if (get_option(Tradetracker_settings)==1){
		echo "<a href=\"admin.php?page=tradetracker-shop\">Basic Setup</a> 
			> 
			<a href=\"admin.php?page=tradetracker-shop-settings\">Basic Settings</a>
			> 
			<b><a href=\"admin.php?page=tradetracker-shop-items\">Basic Items Selection</a></b>
			>
			<a href=\"admin.php?page=tradetracker-shop-overview\">Overview</a>
			>
			<a href=\"admin.php?page=tradetracker-shop-feedback\">Feedback</a>";
		}
	echo "<h2>" . __( 'Tradetracker Item Selection', 'menu-test' ) . "</h2>";
	$file = WP_PLUGIN_DIR . '/tradetracker-store/store.css';
	$file_directory = dirname($file);
	if(is_writable($file_directory)){
	} else {
		echo "<div class=\"updated\"><p><strong>Please make sure the directory ".$file_directory."/ is writable.</strong></p></div>";
	}

?>
<table width="700" border="0">
	<tr>
		<td width="50%" align="left">
			Showing products <b><? echo $first; ?></b> - <b><?php echo $last; ?></b> of <b><?php echo $numrows; ?></b>
  		</td>
  		<td width="50%" align="right">
			Page <b><?php echo $current; ?></b> of <b><?php echo $total; ?></b>
  		</td>
 	</tr>
 	<tr>
  		<td colspan="2" align="right">
			Results per-page: <a href="admin.php?page=tradetracker-shop-items&order=<?php echo $order; ?>&currentpage=<?php echo $currentpage; ?>&limit=100">100</a> | <a href="admin.php?page=tradetracker-shop-items&order=<?php echo $order; ?>&currentpage=<?php echo $currentpage; ?>&limit=200">200</a> | <a href="admin.php?page=tradetracker-shop-items&order=<?php echo $order; ?>&currentpage=<?php echo $currentpage; ?>&limit=500">500</a> | <a href="admin.php?page=tradetracker-shop-items&order=<?php echo $order; ?>&currentpage=<?php echo $currentpage; ?>&limit=1000">1000</a>
  		</td>
 	</tr>
</table>
<?php
$visits=$wpdb->get_results("SELECT * FROM ".$table." ORDER BY ".$order." ASC LIMIT ".$currentpage.", ".$limit."");
	echo "<table width=\"700\">";
		echo "<tr><td width=\"100\">";
			echo "<b><a href=\"admin.php?page=tradetracker-shop-items&order=productID\">ProductID</a></b>";
		echo "</td><td width=\"480\">";
			echo "<b><a href=\"admin.php?page=tradetracker-shop-items&order=name\">Product name</a></b>";
		echo "</td><td width=\"50\">";
			echo "<b><a href=\"admin.php?page=tradetracker-shop-items&order=price\">Price</a></b>";
		echo "</td><td width=\"70\">";
			echo "<b>Currency</b>";
		echo "</td></tr>";
	echo "<form name=\"form2\" method=\"post\" action=\"\">";
		echo "<input type=\"hidden\" name=\"posted\" value=\"Y\">";
			$array2="";
			foreach ($visits as $product){
				$array2 .= ",".$product->productID."";
				echo "<tr><td>";
				$productID = get_option( Tradetracker_productid );
				$productID = explode(",",$productID);
				if(in_array($product->productID, $productID, true))
				{
					echo "<input type=\"checkbox\" checked=\"yes\" name=\"item[]\" value=".$product->productID." />";
				} else {
					echo "<input type=\"checkbox\" name=\"item[]\" value=".$product->productID." />";
				}
			echo $product->productID;
		echo "</td><td><a href=\"#thumb\" class=\"screenshot\" rel=\"".$product->imageURL."\">";
			echo $product->name;
		echo "</a></td><td>";
			echo $product->price;
		echo "</td><td>";
			echo $product->currency;
		echo "</td></tr>";


}
		if(!empty($array2)){
			$array1 = $productID;
			$array2 = explode(",", $array2);
			$result = array_diff($array1, $array2);
			$result = implode(",", $result);
		}
			echo "<input type=\"hidden\" name=\"itemsother\" value=\"".$result."\" />";
	echo "</table>";
	echo "<p class=\"submit\">";
	?>
	<b>Always save changes before pressing next.</b><br>
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" /> 	
	<?php
			if (get_option( Tradetracker_settings )==1){ ?> 
				<INPUT type="button" name="Next" value="<?php esc_attr_e('Next') ?>" onclick="location.href='admin.php?page=tradetracker-shop-overview'"> 
			<?php } ?>
		<INPUT type="button" name="Help" value="<?php esc_attr_e('Help') ?>" onclick="location.href='admin.php?page=tradetracker-shop-help'">
<?php
	echo "</p>";
	echo "</form>";
echo "<table width=\"700\"><tr><td>";
	if ($currentpage != 0) { // Don't show back link if current page is first page.
		$back_page = $currentpage - $limit;
			echo("<a href=\"admin.php?page=tradetracker-shop-items&order=$order&currentpage=$back_page&limit=$limit\">back</a>    \n");}

			for ($i=1; $i <= $pages; $i++) // loop through each page and give link to it.
			{
				$ppage = $limit*($i - 1);
					if ($ppage == $currentpage){
						echo("<b>$i</b> \n");} // If current page don't give link, just text.
					else{
						echo("<a href=\"admin.php?page=tradetracker-shop-items&order=$order&currentpage=$ppage&limit=$limit\">$i</a> \n");}
					}
					if (!((($currentpage+$limit) / $limit) >= $pages) && $pages != 1) { // If last page don't give next link.
						$next_page = $currentpage + $limit;
						echo("    <a href=\"admin.php?page=tradetracker-shop-items&order=$order&currentpage=$next_page&limit=$limit\">next</a>\n");}
						echo "</td></tr></table>";
	echo "</div>";
}
?>