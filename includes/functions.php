<?php
include('typesfunctions.php');

/**
 * Retrieves the value of the setting name passed.
 */
function ch_getsetting($settingname) {
	$result = mysql_query("SELECT * FROM settings WHERE settingname='" . $settingname . "'");
	$setting = mysql_fetch_array($result);

	if(mysql_num_rows($result)) {
		return $setting['settingvalue'];
	}
	else {
		return null;
	}
}

/**
 * Updates the setting called $settingname with the value passed in $settingvalue.
 */
function ch_savesetting($settingname, $settingvalue) {
	mysql_query("UPDATE settings SET settingvalue='$settingvalue' WHERE settingname='$settingname'");
}

/**
 * Displays an HTML select (combobox) containing the names of social bookmark's folders, e.g. Group 1, Group 2...
 */
function ch_getsocialbookmarksfolders() {
	if ($handle = opendir('../images/sb')) {
		$selectedValue = ch_getsetting('iconset');
		$filesList = '<select id="sbfolders" name="sbfolders" style="width: 100px;" onchange="refreshsbicons();">';

		while (false !== ($file = readdir($handle))) {
			if($file != "." & $file != "..") {
				$filesList .= '<option ';

				if($file == $selectedValue)
					$filesList .= 'selected="selected" ';

				$filesList .= 'value="'. $file . '">Group ' . $file . '</option>';
			}
		}

		$filesList .= '</select>';
		return $filesList;
	}
	else {
		return null;
	}
}

/**
 * Displays the social bookmark icons. Each one's link is pointing to it's appropriate service.
 */
function ch_displaysocialbookmarks($currentTitle) {
	$iconSet = 	ch_getsetting('iconset');
	$currentUrl = urlencode($_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]);

	$html = '<a href="http://digg.com/submit?phase=2&url=' . $currentUrl . '"><img src="images/sb/' .
	$iconSet . '/digg.png" alt="Digg" title="Digg it!" /></a>' .

	'<a href="http://del.icio.us/post?url=' . $currentUrl . '&title=' . $currentTitle . '"><img src="images/sb/'
	. $iconSet . '/delicious.png" alt="Delicious" title="Bookmark in del.icio.us" /></a>'

	. '<a href="http://reddit.com/submit?url=' . $currentUrl . '&title=' . $currentTitle .
	'"><img src="images/sb/' . $iconSet . '/reddit.png" alt="Reddit" title="Bookmark in Reddit" /></a>'

	. '<a href="http://technorati.com/cosmos/search.html?url=' . $currentUrl . '"><img src="images/sb/' .
	$iconSet . '/technorati.png" alt="Technorati" title="Bookmark in Technorati" /></a>';

	echo $html;
}

/**
 * Displays the social bookmark icons with no links.
 */
function ch_displaysocialbookmarksonly() {
	$iconSet = ch_getsetting('iconset');

	$html = '<img src="../images/sb/' . $iconSet . '/digg.png" alt="Digg" title="Digg it!" />' .
	'<img src="../images/sb/' . $iconSet . '/delicious.png" alt="Delicious" title="Bookmark in del.icio.us" />' .
	'<img src="../images/sb/' . $iconSet . '/reddit.png" alt="Reddit" title="Bookmark in Reddit" />' .
	'<img src="../images/sb/' . $iconSet . '/technorati.png" alt="Technorati" title="Bookmark in Technorati" />';

	return $html;
}

/**
 * Displays the top menu
 */
function ch_displaytopmenu() {
	$top1text = ch_getsetting('topmenu1text');
	$top1url = ch_getsetting('topmenu1url');
	$top2text = ch_getsetting('topmenu2text');
	$top2url = ch_getsetting('topmenu2url');
	$top3text = ch_getsetting('topmenu3text');
	$top3url = ch_getsetting('topmenu3url');
	$top4text = ch_getsetting('topmenu4text');
	$top4url = ch_getsetting('topmenu4url');

	$html =
	'<ul>' .
    '<li><a href="' . ch_getsetting('topmenu1text') . '">' . ch_getsetting('topmenu1url') . '</a></li>' .
    '<li><a href="#">Contact</a></li>' .
    '</ul>';

	$html = '<ul>';

	if(!empty($top1text) & !empty($top1url))
		$html .= '<li><a href="' . $top1url . '">' . $top1text . '</a></li>';

	if(!empty($top2text) & !empty($top2url))
		$html .= '<li><a href="' . $top2url . '">' . $top2text . '</a></li>';

	if(!empty($top3text) & !empty($top3url))
		$html .= '<li><a href="' . $top3url . '">' . $top3text . '</a></li>';

	if(!empty($top4text) & !empty($top4url))
		$html .= '<li><a href="' . $top4url . '">' . $top4text . '</a></li>';

	$html .='</ul>';

	echo $html;
}

/**
 * Displays the logo image and sets it's title and alt tags to the title setting
 */
function ch_displaylogo() {
	$imageurl = ch_getsetting('logourl');
	$title = ch_getsetting('title');

	if(!empty($imageurl)) {
		$logo = '<img src="' . $imageurl . '" title="' . $title . '" alt="' . $title . '"/>';
		return $logo;
	}
	else {
		$heading = '<h1>' . $title . '</h1>';
		return $heading;
	}
}

/**
 * Clean an input
 */
function clean($value)
{
  $value = strip_tags($value);   // strip html tags
  $value = stripslashes($value);  // strip slashes
  $value = mysql_real_escape_string($value);  // strip mysql hacks
  return $value;  // return clean string
}

/**
 * Get the total number of snippets in the codes table
 */
function ch_gettotalsnippets() {
	$result = mysql_query("SELECT COUNT(*) FROM `codes`");
	list($total) = mysql_fetch_row($result);
	return $total;
}

/**
 * Get a snippet by its ID
 */
function ch_getcode($id) {
	if(empty($id))
		return null;

	$result = mysql_query("SELECT `code` FROM `codes` WHERE `id`=" . $id);
	$row = mysql_fetch_array($result);

	if(mysql_num_rows($result)) {
		return $row['code'];
	}
	else {
		return null;
	}
}

/**
 * Decode HTML in the code for displaying on pages
 */
function ch_formatCodeForDisplaying($code) {
	return html_entity_decode($code, ENT_QUOTES, "UTF-8");
}

/**
 * Encode HTML in the code for saving in database
 */
function ch_formatCodeForDatabase($code) {
	return htmlentities($code, ENT_QUOTES, "UTF-8");
}

/**
 * Get a snippets title by its ID
 */
function ch_getcodetitle($id) {
	$result = mysql_query("SELECT `codetitle` FROM codes WHERE id = '".$id."'");
	$row = mysql_fetch_array($result);

	if(mysql_num_rows($result)) {
		return $row['codetitle'];
	}
	else {
		return NULL;
	}
}

/**
 * Checks if the supplied ID exists in codes table
 */
function ch_codeexists($id) {
	$result = mysql_query("SELECT * FROM codes WHERE id = '".$id."'");

	if(mysql_num_rows($result) > 0)
		return true;
	else
		return false;
}

/**
 * if the code has a password it is returned, else returns NULL
 */
function ch_getcodepassword($id) {
	$result = mysql_query("SELECT `password` FROM codes WHERE id = '".$id."'");
	$row = mysql_fetch_array($result);

	if(mysql_num_rows($result)) {
		return $row['password'];
	}
	else {
		return NULL;
	}
}

/**
 * Checks if the code should ask for CAPTCHA. Returns true or false
 */
function ch_getcodecaptcha($id) {
	$result = mysql_query("SELECT `captcha` FROM codes WHERE id = '".$id."'");
	$row = mysql_fetch_array($result);

	if(mysql_num_rows($result)) {
		if($row['captcha'] == 0)
			return false;
		else
			return true;
	}
	else {
		return false;
	}
}

/**
 * Returns a snippet's code type numeric ID
 */
function ch_getcodetype($id) {
	$result = mysql_query("SELECT `type` FROM codes WHERE id = '".$id."'");
	$row = mysql_fetch_array($result);

	if(mysql_num_rows($result)) {
		return $row['type'];
	}
	else {
		return NULL;
	}
}

?>