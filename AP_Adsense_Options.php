<?php
/***********************************************************************

  Copyright (C) 2002-2005  Smartys (smartys@punbb-hosting.com)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);

// If the "Save" button was clicked (most of this stuff is based on admin_options.php)
if (isset($_POST['save']))
{
	$form = array_map('trim', $_POST['form']);
	
	$form['adsense_enabled']  = intval($form['adsense_enabled']);
	
	if (!isset($form['bot_name']) || $form['bot_name'] == '')
		message('You must enter a name for the bot!');
		
	if ($form['adsense_enabled'] == '1' && $form['ad_client'] == '')
		message('You must enter a client ID if you\'re enabling the ads!');
	
	if ($form['ad_type'] == '')
		$form['ad_format'] = $_POST['format'].'_0ads_al';
	else
		$form['ad_format'] = $_POST['format'].'_as';
	
	$measurements = explode("x", $_POST['format']);
	$form['ad_width'] = intval($measurements[0]);
	$form['ad_height'] = intval($measurements[1]);

	foreach ($form as $key => $input)
	{
		// Only update values that have changed
		if (array_key_exists('google_'.$key, $adsense_config) && $adsense_config['google_'.$key] != $input)
		{
			if ($input != '' || is_int($input))
				$value='\''.$db->escape($input).'\'';
			else
				$value='NULL';

			$db->query('UPDATE '.$db->prefix.'adsense_config SET conf_value='.$value.' WHERE conf_name=\'google_'.$db->escape($key).'\'') or error('Unable to update adsense config', __FILE__, __LINE__, $db->error());
		}
	}

	// Regenerate the config cache
	require_once PUN_ROOT.'include/cache.php';
	generate_adsense_config_cache();

	redirect($redirect_url, 'Adsense options updated. Redirecting &hellip;');
}
	// Display the admin navigation menu
	generate_admin_menu($plugin);

?>
	<div id="exampleplugin" class="blockform">
		<h2><span>Adsense Options</span></h2>
		<div class="box">
			<form id="example" method="post" action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
				<p class="submittop"><input type="submit" name="save" value="Save changes" /></p>
				<div class="inform">
					<fieldset>
						<legend>Board Options</legend>
						<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row">Enable Ads</th>
								<td>
									<input type="radio" name="form[adsense_enabled]" value="1"<?php if ($adsense_config['google_adsense_enabled']  == '1') echo ' checked="checked"'?> />&nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="form[adsense_enabled]" value="0"<?php if ($adsense_config['google_adsense_enabled']  == '0') echo ' checked="checked"'?> />&nbsp;<strong>No</strong>
									<span>Click on No if you want to temporarily turn off the ads after the first post</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Bot Name</th>
								<td>
									<input type="text" name="form[bot_name]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_bot_name']) ?>" />
									<span>This will be the name on the posts with the Adsense in it</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Bot Tag</th>
								<td>
									<input type="text" name="form[bot_tag]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_bot_tag']) ?>" />
									<span>Fill this out if you want the bot posts to have a tag</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Exclude Forums</th>
								<td>
									<input type="text" name="form[exclude_forums]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_exclude_forums']) ?>" />
									<span>Fill this out if you want to exclude certain forums. Enter forum ids, put ","s around them (for example: ,5,6,7,)</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Exclude Groups</th>
								<td>
									<input type="text" name="form[exclude_groups]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_exclude_groups']) ?>" />
									<span>Fill this out if you want to exclude certain groups. Enter group ids, put ","s around them (for example: ,5,6,7,)</span>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
			<div class="inform">
					<fieldset>

						<legend>Adsense Options</legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row">Adsense Client ID</th>
								<td>
									<input type="text" name="form[ad_client]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_ad_client']) ?>" />
									<span>The unique identifier given to you by Google Adsense</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Ad Sizes</th>
								<td>
									<select id="text_img_format" name="format">
									<optgroup label="Text Ads:">
										<option value="728x90"<?php if ($adsense_config['google_ad_format'] == '728x90_as') echo ' selected="selected"' ?>> 728 x 90 Leaderboard </option>
										<option value="468x60"<?php if ($adsense_config['google_ad_format'] == '468x60_as') echo ' selected="selected"' ?>> 468 x 60 Banner </option>
										<option value="300x250"<?php if ($adsense_config['google_ad_format'] == '300x250_as') echo ' selected="selected"' ?>> 300 x 250 Medium Rectangle </option>
										<option value="160x600"<?php if ($adsense_config['google_ad_format'] == '160x600_as') echo ' selected="selected"' ?>> 160 x 600 Wide Skyscraper </option>
										<option value="120x600"<?php if ($adsense_config['google_ad_format'] == '120x600_as') echo ' selected="selected"' ?>> 120 x 600 Skyscraper </option>
										<option value="250x250"<?php if ($adsense_config['google_ad_format'] == '250x250_as') echo ' selected="selected"' ?>> 250 x 250 Square </option>
										<option value="336x280"<?php if ($adsense_config['google_ad_format'] == '336x280_as') echo ' selected="selected"' ?>> 336 x 280 Large Rectangle </option>
										<option value="125x125"<?php if ($adsense_config['google_ad_format'] == '125x125_as') echo ' selected="selected"' ?>> 125 x 125 Button </option>
										<option value="234x60"<?php if ($adsense_config['google_ad_format'] == '234x60_as') echo ' selected="selected"' ?>> 234 x 60 Half Banner </option>
										<option value="180x150"<?php if ($adsense_config['google_ad_format'] == '180x150_as') echo ' selected="selected"' ?>> 180 x 150 Small Rectangle </option>
										<option value="120x240"<?php if ($adsense_config['google_ad_format'] == '120x240_as') echo ' selected="selected"' ?>> 120 x 240 Vertical Banner </option>
									</optgroup>
									<optgroup label="Text / Image Ads:">
										<option value="728x90"<?php if ($adsense_config['google_ad_format'] == '728x90_as') echo ' selected="selected"' ?>> 728 x 90 Leaderboard </option>
										<option value="468x60"<?php if ($adsense_config['google_ad_format'] == '468x60_as') echo ' selected="selected"' ?>> 468 x 60 Banner </option>
										<option value="300x250"<?php if ($adsense_config['google_ad_format'] == '300x250_as') echo ' selected="selected"' ?>> 300 x 250 Medium Rectangle </option>
										<option value="160x600"<?php if ($adsense_config['google_ad_format'] == '160x600_as') echo ' selected="selected"' ?>> 160 x 600 Wide Skyscraper </option>
										<option value="120x600"<?php if ($adsense_config['google_ad_format'] == '120x600_as') echo ' selected="selected"' ?>> 120 x 600 Skyscraper </option>
										<option value="250x250"<?php if ($adsense_config['google_ad_format'] == '250x250_as') echo ' selected="selected"' ?>> 250 x 250 Square </option>
										<option value="336x280"<?php if ($adsense_config['google_ad_format'] == '336x280_as') echo ' selected="selected"' ?>> 336 x 280 Large Rectangle </option>
									</optgroup>
									<optgroup label="Link Units:">
										<option value="120x90"<?php if ($adsense_config['google_ad_format'] == '120x90_0ads_al') echo ' selected="selected"' ?>> 120 x 90 Vertical Link Unit </option>
										<option value="160x90"<?php if ($adsense_config['google_ad_format'] == '160x90_0ads_al') echo ' selected="selected"' ?>> 160 x 90 Vertical Link Unit </option>
										<option value="180x90"<?php if ($adsense_config['google_ad_format'] == '180x90_0ads_al') echo ' selected="selected"' ?>> 180 x 90 Vertical Link Unit </option>
										<option value="200x90"<?php if ($adsense_config['google_ad_format'] == '200x90_0ads_al') echo ' selected="selected"' ?>> 200 x 90 Vertical Link Unit </option>
										<option value="468x15"<?php if ($adsense_config['google_ad_format'] == '468x15_0ads_al') echo ' selected="selected"' ?>> 468 x 15 Horizontal Link Unit </option>
										<option value="728x15"<?php if ($adsense_config['google_ad_format'] == '728x15_0ads_al') echo ' selected="selected"' ?>> 728 x 15 Horizontal Link Unit </option>
									</optgroup>
									</select>
									<span>Select from among the different ad sizes</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Ad Channel</th>
								<td>
									<input type="text" name="form[ad_channel]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_ad_channel']) ?>" />
									<span>If you need this, feel free to use it: for the most part I don't think people do</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Ad Type</th>
								<td>
									<select name="form[ad_type]">
										<option value="text"<?php if ($adsense_config['google_ad_type'] == 'text') echo ' selected="selected"' ?> > Text only </option>
										<option value="image"<?php if ($adsense_config['google_ad_type'] == 'image') echo ' selected="selected"' ?> > Image only </option>
										<option value="text_image"<?php if ($adsense_config['google_ad_type'] == 'text_image') echo ' selected="selected"' ?> > Text and Image </option>
										<option value=""<?php if ($adsense_config['google_ad_type'] == NULL) echo ' selected="selected"' ?> > Link Units </option>
									</select>
									<span>Specify your ad type here</span>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
			<div class="inform">
					<fieldset>

						<legend>Color Choices</legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row">Border Color</th>
								<td>
									<input type="text" name="form[color_border]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_color_border']) ?>" />
									<span>The color for the border of the ad</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Background Color</th>
								<td>
									<input type="text" name="form[color_bg]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_color_bg']) ?>" />
									<span>The background color of the ad</span>
								</td>
							</tr>
							<tr>
								<th scope="row">Link Color</th>
								<td>
									<input type="text" name="form[color_link]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_color_link']) ?>" />
									<span>The color of the links in the ad</span>
								</td>
							</tr>
														<tr>
								<th scope="row">URL Color</th>
								<td>
									<input type="text" name="form[color_url]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_color_url']) ?>" />
									<span>The color of the URLs in the ad</span>
								</td>
							</tr>
														<tr>
								<th scope="row">Text Color</th>
								<td>
									<input type="text" name="form[color_text]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_color_text']) ?>" />
									<span>The color of the text in the ad</span>
								</td>
							</tr>
														<tr>
								<th scope="row">Alternate Color</th>
								<td>
									<input type="text" name="form[alternate_color]" size="25" tabindex="1" value="<?php echo pun_htmlspecialchars($adsense_config['google_alternate_color']) ?>" />
									<span></span>
								</td>
							</tr>
						</table>
						</div>
					</fieldset>
				</div>
				<p class="submitend"><input type="submit" name="save" value="Save changes" /></p>
			</form>
		</div>
	</div>
