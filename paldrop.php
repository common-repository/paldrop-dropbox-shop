<?php
/*
   Plugin Name: PalDrop - Drobox Sales Tool
   Plugin URI: http://www.paldrop.com/wordpress.php
   Description: Paldrop is a simple Paypal to Dropbox connection tool, that lets you easily sell files out of Dropbox with your Wordpress blog. For more info, see <a href="http://www.paldrop.com/wordpress.php">Paldrop.com/wordpress</a>!
   Author: puzzler
   Version: 3.2.0
   Author URI: http://www.tradebit.com/channels/
*/

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', '1');

// create tradebit administration menu
function pd_adminoptions() {
	add_menu_page(__('Paldrop Configuration','paldrop-menu'), __('Paldrop Shop','paldrop-menu'), 'manage_options', 'paldrop-top-level-handle', 'paldrop_edit_settings' );
	add_submenu_page('paldrop-top-level-handle', __('Memberarea','paldrop-menu'), __('Paldrop Memberarea','paldrop-menu'), 'manage_options', 'paldrop-sub-page', 'paldrop_loadmember');
}

// -------------------------------------------------------------------------
// Settings menu in the top level
function paldrop_edit_settings()
{
	if($_REQUEST["pdropaction"]=="pdclear")
	{
		delete_option("pdroplogin");
		delete_option("pdroppw");
		delete_option("pdropuid");
		delete_option("pdropppal");
		delete_option("pdropactive");
	}

	$pdropactive=get_option("pdropactive");

	//print "pds: ".$_REQUEST["pds"]." - pdropactive: ".$pdropactive;

	if($pdropactive!="true" OR $_REQUEST["pds"]=="true")
	{
		// callback from paldrop - save!
		if($_REQUEST["pdropaction"]!="pdropcreate" AND $_REQUEST["pds"]!="true")
			pdropcreateuserform();
		else
			pdropcreateuserremote();
	}
	else
	{
		$pdroplogin=get_option("pdroplogin");
		$pdroppw=get_option("pdroppw");
		$pdropuid=get_option("pdropuid");
		$pdropppal=get_option("pdropppal");
		print "
			<div style='margin:15px; padding:5px; border: 2px dotted #AFAFAF; width:480px; background-color: #FFFFFF;'>
				<img src='http://www.paldrop.com/img/logo-sm.gif' alt='Paldrop Logo' border='0' align='right'>
				<div style='font-size: 14px;'>
				Your <a href='http://www.paldrop.com/' target='_blank'>Paldrop</a> Settings:<BR>
				</div>
				<BR clear='all'>
				<B>Login:</B> $pdroplogin<BR>\n
				<B>Paypal eMail:</B> $pdropppal<BR>\n
				Wordpress accounts are limited to 10 products!
				<BR clear='all'>
				<BR clear='all'>
				You may edit your settings in the <a href='http://www.paldrop.com/' target='_blank'>Paldrop member area</a> directly and pick up
				additional buttons for promotions.
				<BR><BR>
				Clear wordpress options:
				<form action='".$_SERVER["REQUEST_URI"]."' METHOD='POST' style='margin: 0px;'>
				<input type='hidden' name='pdropaction' value='pdclear'>
				<input type='submit' value='clear'> (No undo! This does not clear your paldrop account.)
				</form>
			</div>
		";
	}
	return;
}
// tradebit user creation form
function pdropcreateuserform()
{
	print "
		<div style='margin:15px; padding:5px; border: 2px dotted #AFAFAF; width:480px; background-color: #FFFFFF;'>
			<img src='http://www.paldrop.com/img/logo-sm.gif' alt='Paldrop Logo' border='0' align='right'>
			<div style='font-size: 14px;'>
			".__("Create your own <b>FREE</b> seller account<BR>with a few clicks for up to 10 products:","paldrop-pages")."</div>
			<div style='font-size: 11px;'>
			".__("If you already have a Paldrop seller account, enter the values below matching your details on Paldrop.","paldrop-pages")."
			</div>
			<BR><BR>
			<form action='".$_SERVER["REQUEST_URI"]."' METHOD='POST' style='margin: 0px;'>
			<input type='hidden' name='pdropaction' value='pdropcreate'>
			<div style='float: left; width: 150px;'>".__("Desired username:","paldrop-pages")."</div>
			<div><input type='text' name='paldroplogin' value='".$_REQUEST["paldroplogin"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'>".__("Password:","paldrop-pages")."</div>
			<div><input type='password' name='paldroppw' value='".$_REQUEST["paldroppw"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'>".__("Password confirm:","tradebit-pages")."</div>
			<div><input type='password' name='paldroppw2' value='".$_REQUEST["paldroppw2"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'>".__("Paypal payment eMail:","paldrop-pages")."</div>
			<div><input type='text' name='paldroppayoutmail' value='".$_REQUEST["paldroppayoutmail"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'><a href='http://www.paldrop.com/content/?c=privacy' target='_blank'>".__("Accept terms:","paldrop-pages")."</a></div>
			<div><input type='checkbox' name='paldropterms' value='yes'> ".__("Grant paldrop (limited) access to your Dropbox","paldrop-pages")."</div><BR clear='all'>
			<BR>
			<div style='font-size: 11px;'>
			".__("Submitting this form creates a free seller account on paldrop.com.<BR>","paldrop-pages")."
			".__("No monthly fees, no additional charges. The buyer pays DIRECTLY into your Paypal account.","paldrop-pages")."
			</div>
			<BR>
			<input type='submit' name='paldropsubmit' value='".__("Great, create the account now!","paldrop-pages")."' style='float: right; background-color: #EEFFEE;'>
			<BR clear='all'>
		</div>
	";
}
// contact tradebit.com to create the user
function pdropcreateuserremote()
{
	if($_REQUEST["pds"]!="true")
	{
		$mypaldroplogin=$_REQUEST["paldroplogin"];
		$mypaldroppw=$_REQUEST["paldroppw"];
		$mypaldroppw2=$_REQUEST["paldroppw2"];
		$mypaldroppayoutmail=$_REQUEST["paldroppayoutmail"];
		$mypaldropterms=$_REQUEST["paldropterms"];
		$mypaldroppluginurl = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

		$mytbiterror="";

		if(strlen($mypaldroplogin)<4) $mytbiterror="Login too short! Please use at least 4 characters...";
		if(strlen($mypaldroppw)<4) $mytbiterror="Password too short! Please use at least 4 characters...";
		if($mypaldroppw!=$mypaldroppw2) $mytbiterror="Password confirmation entry wrong: you entered 2 different passwords!";
		if($mypaldropterms!="yes") $mypaldroperror="Please accept the terms!";
		if(substr_count($mypaldroppayoutmail,"@")<1 OR substr_count($mypaldroppayoutmail,".")<1 ) $mypaldroperror="Payout email for paypal seems to be wrong!";
	}
	else $mytbiterror;
	if(strlen($mytbiterror)>2)
	{
		print "<B style='font-size: 14px;'>".$mytbiterror."</B><BR><BR>\n";
		pdropcreateuserform();
	}
	else
	{
		$myopenurl="http://www.paldrop.com/cbpay.php?wp=true&l=".urlencode($mypaldroplogin)."&p=".urlencode($mypaldroppw)."&pp=".urlencode($mypaldroppayoutmail)."&caller=".urlencode($mypaldroppluginurl);
		//$mytbitresult=file_get_contents($myopenurl);

		if($_REQUEST["pds"]!="true")
		{
			print "<iframe src='".$myopenurl."' width='500' height='200' style='padding-left: 10px;'></iframe>";
		}
		else
		{
			// .../admin.php?page=paldrop-top-level-handle&l=X&p=Y&u=ID&pds=true
			print "<div style='margin:15px; padding:5px; border: 2px dotted #AFAFAF; width:480px; background-color: #FFFFFF;'>
				<img src='http://www.paldrop.com/img/logo-sm.gif' alt='Paldrop Logo' border='0' align='right'>
				<div style='font-size: 14px;'>";
			print 	"Login saved: ".$_REQUEST["l"]."<BR><BR>\n";
			print 	"You can directly edit your products on <a href='http://www.paldrop.com/' target='_blank'>www.paldrop.com</a><BR><BR>\n";
			$pdropsuccess=true;

			//$tbitsuccess=true;
			//$myresparts[1]=1;

			if($pdropsuccess)
			{
				if(0+$_REQUEST["u"]>0)
				{
					add_option("pdroplogin",$_REQUEST["l"],"","no");
					add_option("pdroppw",md5($_REQUEST["p"]),"","no");
					add_option("pdropppal",$_REQUEST["pp"],"","no");
					add_option("pdropuid",(0+$_REQUEST["u"]),"","no");

					add_option("pdropactive","true","","no");
					echo "<b>User created!</b>";
				}
				else
				{
					print "Unknown user id!";
				}
			}
			else
			{

			}
			print "	</div>\n
				   </div>\n";
		}
	}
}
// remove settings on deactivation!
function pdropremoveplugin()
{
	echo "removing local settings";
}

// menu for the page
function paldrop_loadmember()
{
	$mytbiturl="http://www.paldrop.com/";
	$pdropactive=get_option("pdropactive");

	if($pdropactive!="true")
	{
		if($_REQUEST["pdropaction"]!="pdropcreate")
			pdropcreateuserform();
		else
			pdropcreateuserremote();
	}
	else
	{
		$tbitlogin=get_option("pdroplogin");
		$tbitpw=get_option("pdroppw");
		$tbituid=get_option("pdropuid");
		$tbitppal=get_option("pdropppal");

		echo "<h2>" . __( 'Launching Paldrop', 'paldrop-menu' ) . "</h2>";
		echo __( 'If Paldrop does not open here, please edit your products in a new window via this link:', 'tradebit-menu' );
		echo "<BR><BR>\n";
		echo '
		<div class="dhtmlgoodies_window">
			<div class="dhtmlgoodies_window_top">
				<img src="'.WP_PLUGIN_URL."/paldrop/".'images/top_left.gif" align="left">
				<img src="'.WP_PLUGIN_URL."/paldrop/".'images/top_center.gif" class="topCenterImage">
				<div class="top_buttons">
					<img class="closeButton" src="'.WP_PLUGIN_URL."/paldrop/".'images/close.gif">
					<img src="'.WP_PLUGIN_URL."/paldrop/".'images/top_right.gif">
				</div>
			</div>
			<div class="dhtmlgoodies_windowMiddle">
				<div class="dhtmlgoodies_windowContent">
				<iframe src="'.$mytbiturl.'" width="100%" height="100%" id="tbitmember" name="tbitmember"></iframe>
				</div>
			</div>
			<div class="dhtmlgoodies_window_bottom">
				<img class="resizeImage" src="'.WP_PLUGIN_URL."/paldrop/".'images/bottom_right.gif">
			</div>
		</div>
		<form action="'.$mytbiturl.'mypaldrop/" id="tbdologin" method="POST" target="tbitmember">
		<input type="hidden" name="action" value="login">
		<input type="hidden" name="login" value="'.$tbitlogin.'">
		<input type="hidden" name="pw" value="'.$tbitpw.'">
		<input type="hidden" name="md5" value="y">
		<input type="hidden" value="Open member area">
		</form>
		<a href="'.$mytbiturl.'" target="_blank">Go to Paldrop.com</a>
		<script type="text/javascript">
		// Setting initial size of windows
		// These values could be overridden by cookies.
		windowSizeArray[1] = [996,500];	// Size of first window
		windowPositionArray[1] = [20,20]; // X and Y position of first window
		document.forms["tbdologin"].submit();
		</script>
		';
	}
}

// enhance the editor with a button
function paldrop_edit_plug($initcontext)
{
	$mytbdir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$mybutton="<a href='http://www.paldrop.com/' ";
	$mybutton.="";
	$mybutton.="target='_blank' style='text-decoration:none'>Paldrop</a>";
	return $initcontext.$mybutton;
}

// ---------------------------------------------------------------------------------- Widget part
// display the sidebar widget
function pdropWidget_display()
{
	echo "<h3 class='widget-title'>".__("Download Shop","paldrop-pages")."</h3>\n";
	$mybutton="<a href='http://www.paldrop.com/' ";
	$mybutton.="";
	$mybutton.="target='_blank' style=\"font-weight: bold;\">Paldrop</a>";
	echo "<UL><LI>";
	echo $mybutton;
	echo "</LI></UL>\n";
	echo "<BR><BR>\n";
}
function pdropWidget_install()
{
	register_sidebar_widget(__('Download Shop',"paldrop-pages"), 'pdropWidget_display');
}
// --------------------------------------------------------------------------------------------
// Allow direct VERIFICATION call from tradebit.com to avoid spammers
if($_REQUEST["verification"]!="tradebit")
{
	wp_register_script("myfloating",plugins_url()."/paldrop/js/floating-window.js");
	wp_register_style("myfloatingstyle",plugins_url()."/paldrop/css/floating-window.css");
	wp_enqueue_script("myfloating");
	wp_enqueue_style("myfloatingstyle");

	// admin
	add_action('admin_menu', 'pd_adminoptions');
	add_filter('media_buttons_context','paldrop_edit_plug');

	// widget
	add_action("plugins_loaded", "pdropWidget_install");
}
else
{
	print "Paldrop Paypal2Dropbox Wordpress Plugin - see <a href='http://www.paldrop.com/wordpress.php'>here</a>.";
}
?>
