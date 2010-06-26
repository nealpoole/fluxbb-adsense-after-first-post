<?php
/***********************************************************************/

// Some info about your mod.
$mod_title      = 'Adsense after First Post';
$mod_version    = '1.1.1';
$release_date   = '2006-07-29';
$author         = 'Smartys';
$author_email   = 'smartys@punbb-hosting.com';

// Versions of FluxBB this mod was created for. Minor variations (i.e. 1.2.4 vs 1.2.5) will be allowed, but a warning will be displayed.
$fluxbb_versions	= array('1.2.5', '1.2.6', '1.2.7', '1.2.8', '1.2.9', '1.2.10', '1.2.11', '1.2.12', '1.2.20', '1.2.21', '1.2.22');

// Set this to false if you haven't implemented the restore function (see below)
$mod_restore	= true;


// This following function will be called when the user presses the "Install" button.
function install()
{
	global $db, $db_type, $pun_config;

	switch ($db_type)
	{
		default:
			$db->query("CREATE TABLE ".$db->prefix."adsense_config (
						conf_name VARCHAR(255) not null default '',
						conf_value TEXT,
						PRIMARY KEY (conf_name)
						) TYPE = MyISAM;")
						or error('Unable to create table adsense_config', __FILE__, __LINE__, $db->error());
						
			$db->query('INSERT INTO '.$db->prefix.'adsense_config values ("google_bot_name", "AdBot"), ("google_bot_tag", "Google AdSense Posting Bot"),
						("google_adsense_enabled", 1), ("google_ad_client", NULL), ("google_ad_width", 728),
						("google_ad_height", 90), ("google_ad_format", "720x90_as"), ("google_ad_channel", NULL),
						("google_ad_type", "text"), ("google_color_border", "FFFFFF"), ("google_color_bg", "FFFFFF"),
						("google_color_link", "005CB1"), ("google_color_url", "005CB1"), ("google_color_text", "333333"),
						("google_alternate_color", "FFFFFF"), ("google_exclude_forums", NULL), ("google_exclude_groups", NULL)')
						or error('Unable to create table adsense_config', __FILE__, __LINE__, $db->error());
			break;
	}

}

// This following function will be called when the user presses the "Restore" button (only if $mod_uninstall is true (see above))
function restore()
{
	global $db, $db_type, $pun_config;

	switch ($db_type)
	{
		default:
			$db->query("DROP TABLE ".$db->prefix."adsense_config") or error('Unable to drop table adsense_config', __FILE__, __LINE__, $db->error());
			break;
	}

}

/***********************************************************************/

// DO NOT EDIT ANYTHING BELOW THIS LINE!


// Circumvent maintenance mode
define('PUN_TURN_OFF_MAINT', 1);
define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';

// We want the complete error message if the script fails
if (!defined('PUN_DEBUG'))
	define('PUN_DEBUG', 1);

// Make sure we are running a FluxBB version that this mod works with
$version_warning = false;
if(!in_array($pun_config['o_cur_version'], $fluxbb_versions))
{
	foreach ($fluxbb_versions as $temp)
	{
		if (substr($temp, 0, 3) == substr($pun_config['o_cur_version'], 0, 3))
		{
			$version_warning = true;
			break;
		}
	}

	if (!$version_warning)
		exit('You are running a version of FluxBB ('.$pun_config['o_cur_version'].') that this mod does not support. This mod supports FluxBB versions: '.implode(', ', $fluxbb_versions));
}


$style = (isset($cur_user)) ? $cur_user['style'] : $pun_config['o_default_style'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $mod_title ?> installation</title>
<link rel="stylesheet" type="text/css" href="style/<?php echo $pun_config['o_default_style'].'.css' ?>" />
</head>
<body>

<div id="punwrap">
<div id="puninstall" class="pun" style="margin: 10% 20% auto 20%">

<?php

if (isset($_POST['form_sent']))
{
	if (isset($_POST['install']))
	{
		// Run the install function (defined above)
		install();

?>
<div class="block">
	<h2><span>Installation successful</span></h2>
	<div class="box">
		<div class="inbox">
			<p>Your database has been successfully prepared for <?php echo pun_htmlspecialchars($mod_title) ?>. See readme.txt for further instructions.</p>
		</div>
	</div>
</div>
<?php

	}
	else
	{
		// Run the restore function (defined above)
		restore();

?>
<div class="block">
	<h2><span>Restore successful</span></h2>
	<div class="box">
		<div class="inbox">
			<p>Your database has been successfully restored.</p>
		</div>
	</div>
</div>
<?php

	}
}
else
{

?>
<div class="blockform">
	<h2><span>Mod installation</span></h2>
	<div class="box">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?foo=bar">
			<div><input type="hidden" name="form_sent" value="1" /></div>
			<div class="inform">
				<p>This script will update your database to work with the following modification:</p>
				<p><strong>Mod title:</strong> <?php echo pun_htmlspecialchars($mod_title).' '.$mod_version ?></p>
				<p><strong>Author:</strong> <?php echo pun_htmlspecialchars($author) ?> (<a href="mailto:<?php echo pun_htmlspecialchars($author_email) ?>"><?php echo pun_htmlspecialchars($author_email) ?></a>)</p>
				<p><strong>Disclaimer:</strong> Mods are not officially supported by FluxBB. Mods generally can't be uninstalled without running SQL queries manually against the database. Make backups of all data you deem necessary before installing.</p>
<?php if ($mod_restore): ?>				<p>If you've previously installed this mod and would like to uninstall it, you can click the restore button below to restore the database.</p>
<?php endif; ?><?php if ($version_warning): ?>				<p style="color: #a00"><strong>Warning:</strong> The mod you are about to install was not made specifically to support your current version of FluxBB (<?php echo $pun_config['o_cur_version']; ?>). However, in most cases this is not a problem and the mod will most likely work with your version as well. If you are uncertain about installning the mod due to this potential version conflict, contact the mod author.</p>
<?php endif; ?>			</div>
			<p><input type="submit" name="install" value="Install" /><?php if ($mod_restore): ?><input type="submit" name="restore" value="Restore" /><?php endif; ?></p>
		</form>
	</div>
</div>
<?php

}

?>

</div>
</div>

</body>
</html>