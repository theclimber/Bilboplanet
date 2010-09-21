<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Olivier Meunier and contributors. All rights
# reserved.
#
# Clearbricks is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Clearbricks is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Clearbricks; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****


function __($str)
{
	return (!empty($GLOBALS['__l10n'][$str])) ? $GLOBALS['__l10n'][$str] : $str;
}

class l10n
{
	public static $text_direction;
	
	protected static $langs = array();
	
	public static function init()
	{
		$GLOBALS['__l10n'] = array();
		$GLOBALS['__l10n_files'] = array();
	}
	
	public static function set($file)
	{
		$lang_file = $file.'.lang';
		$po_file = $file.'.po';
		$php_file = $file.'.lang.php';
		
		if (file_exists($php_file))
		{
			require $php_file;
		}
		elseif (($tmp = self::getPoFile($po_file)) !== false)
		{
			$GLOBALS['__l10n_files'][] = $po_file;
			$GLOBALS['__l10n'] = array_merge($GLOBALS['__l10n'],$tmp);
		}
		elseif (($tmp = self::getLangFile($lang_file)) !== false)
		{
			$GLOBALS['__l10n_files'][] = $lang_file;
			$GLOBALS['__l10n'] = array_merge($GLOBALS['__l10n'],$tmp);
		}
		else
		{
			return false;
		}
	}
	
	public static function getLangFile($file)
	{
		if (!file_exists($file)) {
			return false;
		}
		
		$fp = @fopen($file,'r');
		
		if ($fp === false) {
			return false;
		}
		
		$res = array();
		while ($l = fgets($fp))
		{
			$l = trim($l);
			# Comment
			if (substr($l,0,1) == '#') {
				continue;
			}
			
			# Original text
			if (substr($l,0,1) == ';' && ($t = fgets($fp)) !== false && trim($t) != '') {
				$res[$l] = trim($t);
			}
			
		}
		fclose($fp);
		
		return $res;
	}
	
	public static function getPoFile($file)
	{
		if (!file_exists($file)) {
			return false;
		}
		
		$fc = implode('',file($file));
		
		$res = array();
		
		$matched = preg_match_all('/(msgid\s+("([^"]|\\\\")*?"\s*)+)\s+'.
		'(msgstr\s+("([^"]|\\\\")*?(?<!\\\)"\s*)+)/',
		$fc, $matches);
		
		if (!$matched) {
			return false;
		}
		
		for ($i=0; $i<$matched; $i++)
		{
			$msgid = preg_replace('/\s*msgid\s*"(.*)"\s*/s','\\1',$matches[1][$i]);
			$msgstr= preg_replace('/\s*msgstr\s*"(.*)"\s*/s','\\1',$matches[4][$i]);
			
			$msgstr = self::poString($msgstr);
			
			if ($msgstr) {
				$res[self::poString($msgid)] = $msgstr;
			}
		}
		
		if (!empty($res[''])) {
			$meta = $res[''];
			unset($res['']);
		}
		
		return $res;
	}
	
	private static function poString($string,$reverse=false)
	{
		if ($reverse) {
			$smap = array('"', "\n", "\t", "\r");
			$rmap = array('\\"', '\\n"' . "\n" . '"', '\\t', '\\r');
			return trim((string) str_replace($smap, $rmap, $string));
		} else {
			$smap = array('/"\s+"/', '/\\\\n/', '/\\\\r/', '/\\\\t/', '/\\\"/');
			$rmap = array('', "\n", "\r", "\t", '"');
			return trim((string) preg_replace($smap, $rmap, $string));
		}
	}
	
	public static function getFilePath($dir,$file,$lang)
	{
		$f = $dir.'/'.$lang.'/'.$file;
		if (!file_exists($f)) {
			$f = $dir.'/en/'.$file;
		}
		
		return file_exists($f) ? $f : false;
	}
	
	public static function getISOcodes($flip=false,$name_with_code=false)
	{
		if (empty(self::$langs))
		{
			self::$langs = array(
			'aa' => 'Afaraf',
			'ab' => 'Аҧсуа',
			'ae' => 'avesta',
			'af' => 'Afrikaans',
			'ak' => 'Akan',
			'am' => 'አማርኛ',
			'an' => 'Aragonés',
			'ar' => '‫العربية',
			'as' => 'অসমীয়া',
			'av' => 'авар мацӀ',
			'ay' => 'aymar aru',
			'az' => 'azərbaycan dili',
			'ba' => 'башҡорт теле',
			'be' => 'Беларуская',
			'bg' => 'български език',
			'bh' => 'भोजपुरी',
			'bi' => 'Bislama',
			'bm' => 'bamanankan',
			'bn' => 'বাংলা',
			'bo' => 'བོད་ཡིག',
			'br' => 'brezhoneg',
			'bs' => 'bosanski jezik',
			'ca' => 'Català',
			'ce' => 'нохчийн мотт',
			'ch' => 'Chamoru',
			'co' => 'corsu',
			'cr' => 'ᓀᐦᐃᔭᐍᐏᐣ',
			'cs' => 'česky',
			'cu' => 'ѩзыкъ словѣньскъ',
			'cv' => 'чӑваш чӗлхи',
			'cy' => 'Cymraeg',
			'da' => 'dansk',
			'de' => 'Deutsch',
			'dv' => '‫ދިވެހި',
			'dz' => 'རྫོང་ཁ',
			'ee' => 'Ɛʋɛgbɛ',
			'el' => 'Ελληνικά',
			'en' => 'English',
			'eo' => 'Esperanto',
			'es' => 'español',
			'et' => 'Eesti keel',
			'eu' => 'euskara',
			'fa' => '‫فارسی',
			'ff' => 'Fulfulde',
			'fi' => 'suomen kieli',
			'fj' => 'vosa Vakaviti',
			'fo' => 'Føroyskt',
			'fr' => 'français',
			'fy' => 'Frysk',
			'ga' => 'Gaeilge',
			'gd' => 'Gàidhlig',
			'gl' => 'Galego',
			'gn' => "Avañe'ẽ",
			'gu' => 'ગુજરાતી',
			'gv' => 'Ghaelg',
			'ha' => '‫هَوُسَ',
			'he' => '‫עברית',
			'hi' => 'हिन्दी',
			'ho' => 'Hiri Motu',
			'hr' => 'Hrvatski',
			'ht' => 'Kreyòl ayisyen',
			'hu' => 'Magyar',
			'hy' => 'Հայերեն',
			'hz' => 'Otjiherero',
			'ia' => 'Interlingua',
			'id' => 'Bahasa Indonesia',
			'ie' => 'Interlingue',
			'ig' => 'Igbo',
			'ii' => 'ꆇꉙ',
			'ik' => 'Iñupiaq',
			'io' => 'Ido',
			'is' => 'Íslenska',
			'it' => 'Italiano',
			'iu' => 'ᐃᓄᒃᑎᑐᑦ',
			'ja' => '日本語',
			'jv' => 'basa Jawa',
			'ka' => 'ქართული',
			'kg' => 'KiKongo',
			'ki' => 'Gĩkũyũ',
			'kj' => 'Kuanyama',
			'kk' => 'Қазақ тілі',
			'kl' => 'kalaallisut',
			'km' => 'ភាសាខ្មែរ',
			'kn' => 'ಕನ್ನಡ',
			'ko' => '한국어',
			'kr' => 'Kanuri',
			'ks' => 'कश्मीरी',
			'ku' => 'Kurdî',
			'kv' => 'коми кыв',
			'kw' => 'Kernewek',
			'ky' => 'кыргыз тили',
			'la' => 'latine',
			'lb' => 'Lëtzebuergesch',
			'lg' => 'Luganda',
			'li' => 'Limburgs',
			'ln' => 'Lingála',
			'lo' => 'ພາສາລາວ',
			'lt' => 'lietuvių kalba',
			'lu' => 'Luba-Katanga	',
			'lv' => 'latviešu valoda',
			'mg' => 'Malagasy fiteny',
			'mh' => 'Kajin M̧ajeļ',
			'mi' => 'te reo Māori',
			'mk' => 'македонски јазик',
			'ml' => 'മലയാളം',
			'mn' => 'Монгол',
			'mo' => 'Limba moldovenească',
			'mr' => 'मराठी',
			'ms' => 'bahasa Melayu',
			'mt' => 'Malti',
			'my' => 'ဗမာစာ',
			'na' => 'Ekakairũ Naoero',
			'nb' => 'Norsk bokmål',
			'nd' => 'isiNdebele',
			'ne' => 'नेपाली',
			'ng' => 'Owambo',
			'nl' => 'Nederlands',
			'nl-be' => 'Nederlands (Belgium)',
			'nn' => 'Norsk nynorsk',
			'no' => 'Norsk',
			'nr' => 'Ndébélé',
			'nv' => 'Diné bizaad',
			'ny' => 'chiCheŵa',
			'oc' => 'Occitan',
			'oj' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ',
			'om' => 'Afaan Oromoo',
			'or' => 'ଓଡ଼ିଆ',
			'os' => 'Ирон æвзаг',
			'pa' => 'ਪੰਜਾਬੀ',
			'pi' => 'पाऴि',
			'pl' => 'polski',
			'ps' => '‫پښتو',
			'pt' => 'Português',
			'pt-br' => 'Português (Brasil)',
			'qu' => 'Runa Simi',
			'rm' => 'rumantsch grischun',
			'rn' => 'kiRundi',
			'ro' => 'română',
			'ru' => 'Русский',
			'rw' => 'Ikinyarwanda',
			'sa' => 'संस्कृतम्',
			'sc' => 'sardu',
			'sd' => 'सिन्धी',
			'se' => 'Davvisámegiella',
			'sg' => 'yângâ tî sängö',
			'sh' => 'Srpskohrvatski',
			'si' => 'සිංහල',
			'sk' => 'slovenčina',
			'sl' => 'slovenščina',
			'sm' => "gagana fa'a Samoa",
			'sn' => 'chiShona',
			'so' => 'Soomaaliga',
			'sq' => 'Shqip',
			'sr' => 'српски језик',
			'ss' => 'SiSwati',
			'st' => 'seSotho',
			'su' => 'Basa Sunda',
			'sv' => 'Svenska',
			'sw' => 'Kiswahili',
			'ta' => 'தமிழ்',
			'te' => 'తెలుగు',
			'tg' => 'тоҷикӣ',
			'th' => 'ไทย',
			'ti' => 'ትግርኛ',
			'tk' => 'Türkmen',
			'tl' => 'Tagalog',
			'tn' => 'seTswana',
			'to' => 'faka Tonga',
			'tr' => 'Türkçe',
			'ts' => 'xiTsonga',
			'tt' => 'татарча',
			'tw' => 'Twi',
			'ty' => 'Reo Mā`ohi',
			'ug' => 'Uyƣurqə',
			'uk' => 'Українська',
			'ur' => '‫اردو',
			'uz' => "O'zbek",
			've' => 'tshiVenḓa',
			'vi' => 'Tiếng Việt',
			'vo' => 'Volapük',
			'wa' => 'Walon',
			'wo' => 'Wollof',
			'xh' => 'isiXhosa',
			'yi' => '‫ייִדיש',
			'yo' => 'Yorùbá',
			'za' => 'Saɯ cueŋƅ',
			'zh' => '中文',
			'zh-hk' => '中文 (香港)',
			'zh-tw' => '中文 (臺灣)',
			'zu' => 'isiZulu'
			);
		}
		
		$langs = self::$langs;
		if ($name_with_code) {
			foreach ($langs as $k => &$v) {
				$v = '('.$k.') '.$v;
			}
		}
		
		if ($flip) {
			return array_flip($langs);
		}
		
		return $langs;
	}
	
	public static function getTextDirection($lang)
	{
		if (self::$text_direction) {
			return self::$text_direction;
		}
		
		if (preg_match('/^(ar|dv|fa|ha|he|ps|ur|yi)$/i',$lang)) {
			return 'rtl';
		}
		return 'ltr';
	}
}
?>