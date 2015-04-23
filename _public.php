<?php
/*
# This is 'My Favicon', a plugin for Dotclear 2
#
# Copyright (c) 2008-2015
# Oleksandr Syenchuk and contributors.
#
# This is an open source software, distributed under the GNU
# General Public License (version 2) terms and  conditions.
#
# You should have received a copy of the GNU General Public
# License along with 'My Favicon' (see COPYING.txt);
# if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if (!defined('DC_RC_PATH')) { return; }

if (is_callable('dcTemplate','SysBehavior')) {
	$core->addBehavior('publicHeadContent',array('myFavicon','publicHeadContent'));
} else {
	$core->addBehavior('templateBeforeValue',array('myFavicon','templateBeforeValue'));
}

class myFavicon
{
	#FIXME Mimetypes in common/lib.files.php (Clearbricks) are not enough
	public static $allowed_mimetypes = array(
		'ico' => 'image/x-icon',
		'png' => 'image/png',
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'jpg' => 'image/jpeg',
		'mng' => 'video/x-mng'
	);
	
	public static function publicHeadContent($core)
	{
		$res = self::faviconHTML($core->blog->settings);
		if (!empty($res)) {
			echo $res."\n";
		}
	}
	
	public static function templateBeforeValue($core,$id,$attr)
	{
		if ($id == 'include' && isset($attr['src']) && $attr['src'] == '_head.html') {
			return
			'<?php if (method_exists("myFavicon","faviconHTML")) {'.
			'echo myFavicon::faviconHTML();} ?>';
		}
	}

	private static function faviconHTML($settings)
	{
			$favicon_url = $settings->myfavicon->favicon_url;
			$favicon_ie_url = $settings->myfavicon->favicon_ie_url;

		if (empty($favicon_url) && empty($favicon_ie_url)) {
			return;
		}

		$ie_link = '';
		if ($favicon_ie_url != null) {
			$ie_link = '
<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="'.html::escapeHTML($favicon_ie_url).'" /><![endif]-->';
			
		}
		
		$other_link = '';
		if (!empty($favicon_url)) {
			$extension = files::getExtension($favicon_url);
			
			if (!isset(self::$allowed_mimetypes[$extension])) {
				$mimetype = files::getMimeType($favicon_url);
				if (!in_array($mimetype,self::$allowed_mimetypes)) {
					return '<!-- Bad favicon MIME type. -->'."\n";
				}
			}
			else {
				$mimetype = self::$allowed_mimetypes[$extension];
			}
			
			$other_link = '<link rel="icon" type="'.$mimetype.
			'" href="'.html::escapeHTML($favicon_url).'" />';
			
		}
			
		return $other_link.$ie_link;
	}
}