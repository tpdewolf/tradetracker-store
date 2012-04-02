<?php
function xmloption(){
	//global variables
	global $wpdb;
	global $ttstoresubmit;
	global $ttstorehidden;
	global $ttstoretable;

	//variables for this function
	$Tradetracker_xmlupdate_name = 'Tradetracker_xmlupdate';
	$Tradetracker_currency_name = 'Tradetracker_currency';
	$Tradetracker_currencyloc_name = 'Tradetracker_currencyloc';
	$Tradetracker_newcur_name = 'Tradetracker_newcur';
	$Tradetracker_extra_name = 'Tradetracker_extra';

	//filling variables from database
	$Tradetracker_xmlupdate_val = get_option( $Tradetracker_xmlupdate_name );
	$Tradetracker_currency_val = get_option( $Tradetracker_currency_name );
	$Tradetracker_currencyloc_val = get_option( $Tradetracker_currencyloc_name );
	$Tradetracker_newcur_val = get_option( $Tradetracker_newcur_name );
	$Tradetracker_extra_val = get_option( $Tradetracker_extra_name );


	//see if form has been submitted
	if( isset($_POST[ $ttstoresubmit ]) && $_POST[ $ttstoresubmit ] == 'Y' ) {

		//get posted data
		$Tradetracker_xmlupdate_val = $_POST[ $Tradetracker_xmlupdate_name ];
		$Tradetracker_currency_val = $_POST[ $Tradetracker_currency_name ];
		$Tradetracker_currencyloc_val = $_POST[ $Tradetracker_currencyloc_name ];
		if(isset($_POST['extra'])){
			$Tradetracker_extra_val = $_POST['extra'];
		} else {
			$Tradetracker_extra_val = "";
		}
		if(isset($_POST['oldcur'])){
			$Tradetracker_newcur_val = "";
			$a1=$_POST['oldcur']; 
			$a2 = $_POST['newcur']; 
			$Tradetracker_newcur_val = array_combine($a1,$a2);
		} else {
			$Tradetracker_newcur_val = "";
		}
		//save the posted value in the database
		if ( get_option("Tradetracker_xmlupdate")  != $Tradetracker_xmlupdate_val) {
			update_option( $Tradetracker_xmlupdate_name, $Tradetracker_xmlupdate_val );
			wp_clear_scheduled_hook('xmlscheduler');
		}
		if ( get_option("Tradetracker_currency")  != $Tradetracker_currency_val) {
			update_option( $Tradetracker_currency_name, $Tradetracker_currency_val );
		}
		if ( get_option("Tradetracker_currencyloc")  != $Tradetracker_currencyloc_val) {
			update_option( $Tradetracker_currencyloc_name, $Tradetracker_currencyloc_val );
		}
		if ( get_option("Tradetracker_newcur")  != $Tradetracker_newcur_val) {
			update_option( $Tradetracker_newcur_name, $Tradetracker_newcur_val );
		}
		if ( get_option("Tradetracker_extra")  != $Tradetracker_extra_val) {
			update_option( $Tradetracker_extra_name, $Tradetracker_extra_val );
		}

	        //put an settings updated message on the screen
		$saved = "<div id=\"ttstoreboxsaved\"><strong>Settings saved</strong></div>";
	}
?>
<div  id="TB_overlay" class="TB_overlayBG"></div>
<div id="TB_window1" style="left: auto;margin-left: auto;margin-right: auto; margin-top: 0;right: auto;top: 48px;visibility: visible;width: 1000px;">
	<div id="ttstorebox">
	<form name="form1" method="post" action="">
	<?php echo $ttstorehidden; ?>
		<div id="TB_title">
			<div id="TB_ajaxWindowTitle">Change XML options.</div>
			<div id="TB_closeAjaxWindow">
				<a title="Close" id="TB_closeWindowButton" href="admin.php?page=tt-store">
					<img src="<?php echo plugins_url( 'images/tb-close.png' , __FILE__ )?>">
				</a>
			</div>
		</div>
		<?php $adminheight = get_option("Tradetracker_adminheight"); ?>
		<div id="ttstoreboxoptions" style="max-height:<?php echo $adminheight; ?>px;">
			<table width="985">
				<tr>
					<td>
						<label for="tradetrackerextrafield" title="Which extra fields would you like to use?" class="info">
							<?php _e("Which extra fields?:", 'tradetracker-extra' ); ?> 
						</label>
					</td>
					<td>
					<?php
					$extra = get_option("Tradetracker_xml_extra");
					if(!empty($extra)){
						echo "<table width=\"400\">";
						$i="1";
						foreach($extra as $extraselect) {
							if($i=="1"){
								echo "<tr><td>";
							} else {
								echo "<td>";
							}
							if(!empty($Tradetracker_extra_val)){
								if(in_array($extraselect, $Tradetracker_extra_val, true)) {
									echo "<input type=\"checkbox\" checked=\"yes\" name=\"extra[]\" value=\"".$extraselect."\" />".$extraselect."<br />";
								} else {
									echo "<input type=\"checkbox\" name=\"extra[]\" value=\"".$extraselect."\" />".$extraselect."<br />";
								}
							} else {
									echo "<input type=\"checkbox\" name=\"extra[]\" value=\"".$extraselect."\" />".$extraselect."<br />";
							}
							if($i=="1"){
								echo "</td>";
								$i++;
							} else {
								echo "</td></tr>";
								$i--;
							}
						}
						echo "</table>";
					}
					?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="tradetrackerupdate" title="When should it update?, standard is 00:00:00" class="info">
							<?php _e("Update time:", 'tradetracker-update' ); ?> 
						</label>
					</td>
					<td>
						<input type="text" name="<?php echo $Tradetracker_xmlupdate_name; ?>" value="<?php if($Tradetracker_xmlupdate_val==""){ echo "00:00:00"; } else { echo $Tradetracker_xmlupdate_val;} ?>" size="20"> Time has to be in hh:mm:ss
					</td>
				</tr>
		<tr>
			<td>
				<label for="tradetrackercurrency" title="Do you like to use fill in your own currency or get it from the XML?" class="info">
					<?php _e("Use your own currency symbol:", 'tradetracker-currency' ); ?> 
				</label>
			</td>
			<td>
				<input type="radio" name="<?php echo $Tradetracker_currency_name; ?>" <?php if($Tradetracker_currency_val==1) {echo "checked";} ?> value="1"> Yes 
				<br>
				<input type="radio" name="<?php echo $Tradetracker_currency_name; ?>" <?php if($Tradetracker_currency_val==0){echo "checked";} ?> value="0"> No
			</td>
		</tr>
		<tr>
			<td>
				<label for="tradetrackercurrencyloc" title="Do you like to have the currency before or after the price?" class="info">
					<?php _e("Location of the currency:", 'tradetracker-currencyloc' ); ?> 
				</label>
			</td>
			<td>
				<input type="radio" name="<?php echo $Tradetracker_currencyloc_name; ?>" <?php if($Tradetracker_currencyloc_val==1) {echo "checked";} ?> value="1"> After the price
				<br>
				<input type="radio" name="<?php echo $Tradetracker_currencyloc_name; ?>" <?php if($Tradetracker_currencyloc_val==0){echo "checked";} ?> value="0"> Before the price
			</td>
		</tr>
		<?php 
		if($Tradetracker_currency_val==1) {
		?>
			<tr>
				<td colspan="2">
					<b>Adjust Currency:</b>
				</td>
			</tr>
			<tr>
				<td>
					<label for="tradetrackercurrency" title="Current currency in the XML?" class="info">
						<?php _e("XML Currency:", 'tradetracker-currency' ); ?> 
					</label>
				</td>
				<td>
					<label for="tradetrackercurrency" title="What would you like to show instead of the XML currency?" class="info">
						<?php _e("New Currency:", 'tradetracker-currency' ); ?> 
					</label>
				</td>
			</tr>
			<?php
			

			$currency=$wpdb->get_results("SELECT currency FROM ".$ttstoretable." group by currency");
			$i="0";
			foreach ($currency as $currency_val){
			$array = get_option( $Tradetracker_newcur_name );
			$key = $currency_val->currency; 
			$value = $array[$key]; 
			?>
				<tr>
					<td>
						<input type="text" readonly="readonly" name="oldcur[<?php echo $i; ?>]" value="<?php echo $currency_val->currency; ?>">
					</td>
					<td>
						<input type="text" name="newcur[<?php echo $i; ?>]" value="<?php echo $array[$key]; ?>">						
					</td>
				</tr>
			<?php
			$i++;
			}
		}
		?>
			</table>
		</div>
		<div id="ttstoreboxbottom">
			<?php
				if(isset($saved)){
					echo $saved;
				}
			?>
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" /> 
			<INPUT type="button" name="Close" class="button-secondary" value="<?php esc_attr_e('Close') ?>" onclick="location.href='admin.php?page=tt-store'"> 
		</div>
	</form>
	</div>
</div>
<?php	
}
?>