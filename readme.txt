##
##
##        Mod title:  Adsense after First Post
##
##      Mod version:  1.1.1
##  Works on FluxBB:  1.2.*
##     Release date:  2006-07-29
##           Author:  Smartys (smartys@punbb-hosting.com)
##
##      Description:  This mod allows you to place a Google ad after the first
##                    post in every thread, disguised as a post.
##
##   Affected files:  viewtopic.php
##                    include/common.php
##		              include/cache.php
##
##       Affects DB:  Yes
##
##            Notes:  This doesn't change the color of the ad based on the style used.
##		      If people need it, I'll try and implement it in the next version.
##		      If you need to upgrade, just replace the plugin
##
##       DISCLAIMER:  Please note that "mods" are not officially supported by
##                    FluxBB. Installation of this modification is done at your
##                    own risk. Backup your forum database and any and all
##                    applicable files before proceeding.
##
##


#
#---------[ 1. UPLOAD ]-------------------------------------------------------
#

install_mod.php to /
AP_Adsense_Options.php to plugins/


#
#---------[ 2. RUN ]----------------------------------------------------------
#

install_mod.php


#
#---------[ 3. DELETE ]-------------------------------------------------------
#

install_mod.php


#
#---------[ 4. OPEN ]---------------------------------------------------------
#

include/common.php


#
#---------[ 5. FIND ]---------------------------------------------------------
#

	// Load cached config
	@include PUN_ROOT.'cache/cache_config.php';
	if (!defined('PUN_CONFIG_LOADED'))
	{
		require PUN_ROOT.'include/cache.php';
		generate_config_cache();
		require PUN_ROOT.'cache/cache_config.php';
	}


#
#---------[ 6. AFTER, ADD ]---------------------------------------------------
#

	@include PUN_ROOT.'cache/cache_adsense_config.php';
	if (!defined('PUN_ADSENSE_CONFIG_LOADED'))
	{
		require_once PUN_ROOT.'include/cache.php';
		generate_adsense_config_cache();
		require PUN_ROOT.'cache/cache_adsense_config.php';
	}


#
#---------[ 7. OPEN ]---------------------------------------------------------
#

include/cache.php


#
#---------[ 8. FIND ]---------------------------------------------------------
#

				fwrite($fh, $output);

		fclose($fh);
	}
}


#
#---------[ 9. AFTER, ADD ]---------------------------------------------------
#

//
// Generate the adsense config cache PHP script
//
function generate_adsense_config_cache()
{
	global $db;

	// Get the forum config from the DB
	$result = $db->query('SELECT * FROM '.$db->prefix.'adsense_config', true) or error('Unable to fetch adsense config', __FILE__, __LINE__, $db->error());
	while ($cur_config_item = $db->fetch_row($result))
		$output[$cur_config_item[0]] = $cur_config_item[1];

	// Output config as PHP code
	$fh = @fopen(PUN_ROOT.'cache/cache_adsense_config.php', 'wb');
	if (!$fh)
		error('Unable to write adsense configuration cache file to cache directory. Please make sure PHP has write access to the directory \'cache\'', __FILE__, __LINE__);

	fwrite($fh, '<?php'."\n\n".'define(\'PUN_ADSENSE_CONFIG_LOADED\', 1);'."\n\n".'$adsense_config = '.var_export($output, true).';'."\n\n".'?>');

	fclose($fh);
}


#
#---------[ 10. OPEN ]-------------------------------------------------------
#

	viewtopic.php


#
#---------[ 11. FIND ]--------------------------------------------------------
#

				<div class="postfootright"><?php echo (count($post_actions)) ? '<ul>'.implode($lang_topic['Link separator'].'</li>', $post_actions).'</li></ul></div>'."\n" : '<div>&nbsp;</div></div>'."\n" ?>
		</div>
	</div>
</div>

<?php


#
#---------[ 12. AFTER, ADD ]---------------------------------------------------
#

	if ($post_count == '1' && $adsense_config['google_adsense_enabled'] == '1' && strpos($adsense_config['google_exclude_forums'], ','.$cur_topic['forum_id'].',') === FALSE && strpos($adsense_config['google_exclude_groups'], ','.$pun_user['g_id'].',') === FALSE)
	{
?>
<div class="blockpost<?php echo $vtbg ?>">
	<h2><span><?php echo format_time($cur_post['posted']) ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postleft">
				<dl>
					<dt><strong><?php echo $adsense_config['google_bot_name'] ?></strong></dt>
					<dd class="usertitle"><?php echo $adsense_config['google_bot_tag'] ?></dd>
				</dl>
			</div>
			<div class="postright">
				<div class="postmsg">
					<?php echo "<br /><div style=\"TEXT-ALIGN: center\">
	<script type=\"text/javascript\">
	<!--
		google_ad_client = \"".$adsense_config['google_ad_client']."\";
		google_ad_width = ".$adsense_config['google_ad_width'].";
		google_ad_height = ".$adsense_config['google_ad_height'].";
		google_ad_format = \"".$adsense_config['google_ad_format']."\";
		google_ad_channel = \"".$adsense_config['google_ad_channel']."\";
		google_ad_type = \"".$adsense_config['google_ad_type']."\";
		google_color_border = \"".$adsense_config['google_color_border']."\";
		google_color_bg = \"".$adsense_config['google_color_bg']."\";
		google_color_link = \"".$adsense_config['google_color_link']."\";
		google_color_url = \"".$adsense_config['google_color_url']."\";
		google_color_text = \"".$adsense_config['google_color_text']."\";
		google_alternate_color = \"".$adsense_config['google_alternate_color']."\";
	//-->
	</script>
	<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\"></script>
</div><br />\n" ?>
				</div>
			</div>
			<div class="clearer"></div>
		</div>
	</div>
</div>
<?php
	}

#
#---------[ 13. SAVE/UPLOAD ]---------------------------------------------------
#