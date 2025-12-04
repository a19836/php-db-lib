<?php
/*
 * Copyright (c) 2025 Bloxtor (http://bloxtor.com) and Joao Pinto (http://jplpinto.com)
 * 
 * Multi-licensed: BSD 3-Clause | Apache 2.0 | GNU LGPL v3 | HLNC License (http://bloxtor.com/LICENSE_HLNC.md)
 * Choose one license that best fits your needs.
 *
 * Original Bloxtor Repo: https://github.com/a19836/bloxtor
 *
 * YOU ARE NOT AUTHORIZED TO MODIFY OR REMOVE ANY PART OF THIS NOTICE!
 */

class TextSanitizer {

	//copied from https://www.php.net/manual/pt_BR/normalizer.normalize.php
	//remove accents and replace them by the correspondent character without accents.
	public static function normalizeAccents($s) {
		$original_string = $s;
		
		// Normalizer-class missing!
		if (!class_exists("Normalizer", $autoload = false))
			return $original_string;
		
		// maps German (umlauts) and other European characters onto two characters before just removing diacritics
		$s    = preg_replace( '@\x{00c4}@u'    , "AE",    $s );    // umlaut Ä => AE
		$s    = preg_replace( '@\x{00d6}@u'    , "OE",    $s );    // umlaut Ö => OE
		$s    = preg_replace( '@\x{00dc}@u'    , "UE",    $s );    // umlaut Ü => UE
		$s    = preg_replace( '@\x{00e4}@u'    , "ae",    $s );    // umlaut ä => ae
		$s    = preg_replace( '@\x{00f6}@u'    , "oe",    $s );    // umlaut ö => oe
		$s    = preg_replace( '@\x{00fc}@u'    , "ue",    $s );    // umlaut ü => ue
		$s    = preg_replace( '@\x{00f1}@u'    , "ny",    $s );    // ñ => ny
		$s    = preg_replace( '@\x{00ff}@u'    , "yu",    $s );    // ÿ => yu


		// maps special characters (characters with diacritics) on their base-character followed by the diacritical mark
		// exmaple:  Ú => U´,  á => a`
		$s    = Normalizer::normalize( $s, Normalizer::NFD );


		$s    = preg_replace( '@\pM@u'        , "",    $s );    // removes diacritics


		$s    = preg_replace( '@\x{00df}@u'    , "ss",    $s );    // maps German ß onto ss
		$s    = preg_replace( '@\x{00c6}@u'    , "AE",    $s );    // Æ => AE
		$s    = preg_replace( '@\x{00e6}@u'    , "ae",    $s );    // æ => ae
		$s    = preg_replace( '@\x{0132}@u'    , "IJ",    $s );    // ? => IJ
		$s    = preg_replace( '@\x{0133}@u'    , "ij",    $s );    // ? => ij
		$s    = preg_replace( '@\x{0152}@u'    , "OE",    $s );    // Œ => OE
		$s    = preg_replace( '@\x{0153}@u'    , "oe",    $s );    // œ => oe

		$s    = preg_replace( '@\x{00d0}@u'    , "D",    $s );    // Ð => D
		$s    = preg_replace( '@\x{0110}@u'    , "D",    $s );    // Ð => D
		$s    = preg_replace( '@\x{00f0}@u'    , "d",    $s );    // ð => d
		$s    = preg_replace( '@\x{0111}@u'    , "d",    $s );    // d => d
		$s    = preg_replace( '@\x{0126}@u'    , "H",    $s );    // H => H
		$s    = preg_replace( '@\x{0127}@u'    , "h",    $s );    // h => h
		$s    = preg_replace( '@\x{0131}@u'    , "i",    $s );    // i => i
		$s    = preg_replace( '@\x{0138}@u'    , "k",    $s );    // ? => k
		$s    = preg_replace( '@\x{013f}@u'    , "L",    $s );    // ? => L
		$s    = preg_replace( '@\x{0141}@u'    , "L",    $s );    // L => L
		$s    = preg_replace( '@\x{0140}@u'    , "l",    $s );    // ? => l
		$s    = preg_replace( '@\x{0142}@u'    , "l",    $s );    // l => l
		$s    = preg_replace( '@\x{014a}@u'    , "N",    $s );    // ? => N
		$s    = preg_replace( '@\x{0149}@u'    , "n",    $s );    // ? => n
		$s    = preg_replace( '@\x{014b}@u'    , "n",    $s );    // ? => n
		$s    = preg_replace( '@\x{00d8}@u'    , "O",    $s );    // Ø => O
		$s    = preg_replace( '@\x{00f8}@u'    , "o",    $s );    // ø => o
		$s    = preg_replace( '@\x{017f}@u'    , "s",    $s );    // ? => s
		$s    = preg_replace( '@\x{00de}@u'    , "T",    $s );    // Þ => T
		$s    = preg_replace( '@\x{0166}@u'    , "T",    $s );    // T => T
		$s    = preg_replace( '@\x{00fe}@u'    , "t",    $s );    // þ => t
		$s    = preg_replace( '@\x{0167}@u'    , "t",    $s );    // t => t

		// remove all non-ASCii characters
		$s    = preg_replace( '@[^\0-\x80]@u'    , "",    $s );
		
		//error_log("s ($original_string):$s\n", 3, "/var/www/html/livingroop/default/tmp/test.log");
		
		// possible errors in UTF8-regular-expressions
		if (empty($s))
			return $original_string;
		else
			return $s;
	}
	
	/**
	* stripCSlashes: strip all slashes for all characters inside of $chars
	* Note that the stripcslashes and stripslashes have a diferent behaviour. The stripcslashes removes slashes for double quotes and the stripslashes remove slashes for a bunch of escaped chars. 
	* This method only removes the slashes for a specific chars and if the chars are escaped, this is, if there is "\\'", this method won't remove any slash.
	*/
	public static function stripCSlashes($text, $chars) {
		$chars = is_array($chars) ? $chars : self::mbStrSplit($chars);
		$t = count($chars);
		
		for ($i = 0; $i < $t; $i++) {
			$char = $chars[$i];
			
			if ($char != "\\")
				$text = self::stripCharSlashes($text, $char);
		}
		
		if (array_search("\\", $chars)) 
			$text = self::stripCharSlashes($text, "\\");
		
		return $text;
	}
	
	/**
	* stripCharSlashes: strip all slashes for a specific character
	*/
	public static function stripCharSlashes($text, $char) {
		$text_chars = self::mbStrSplit($text);
		$l = count($text_chars);
		$new_text = "";
		
		for ($i = 0; $i < $l; $i++) {
			$c = $text_chars[$i];
			
			if ($i + 1 < $l && $text_chars[$i + 1] == $char && $c == "\\" && !self::isMBCharEscaped($text, $i, $text_chars))
				$new_text .= "";
			else
				$new_text .= $c;
		}
		
		return $new_text;
	}
	//This method is deprecated bc is a little bit more slow
	/*public static function stripCharSlashesOld($text, $char) {
		$pos = 0;
		
		do {
			$pos = mb_strpos($text, $char, $pos);
			
			if ($pos !== false) {
				$prev = mb_substr($text, $pos - 1, 1);
				
				if ($prev == "\\" && !self::isMBSubstrCharEscaped($text, $pos - 1))
					$text = mb_substr($text, 0, $pos - 1) . mb_substr($text, $pos);
				else
					$pos++;
			}
		}
		while ($pos !== false);
		
		return $text;
	}*/
	
	/**
	* mbStrSplit: returns the multibyte character list of a string. 
	* This function splits a multibyte string into an array of characters. Comparable to str_split().
	* A (simpler) way to extract all characters from a UTF-8 string to array.
	*/
	public static function mbStrSplit($str) {
		# Split at all position not after the start: ^
		# and not before the end: $
		return function_exists("mb_str_split") ? mb_str_split($str) : preg_split('//u', $str, null, PREG_SPLIT_NO_EMPTY);
	}
	
	/**
	* isCharEscaped: checks if a char is escaped given its position 
	*/
	public static function isCharEscaped($str, $index) {
		$escaped = false;
		
		if (is_numeric($str))
			$str = (string)$str; //bc of php > 7.4 if we use $sql[$i] gives an warning
		
		for ($i = $index - 1; $i >= 0; $i--) {
			if ($str[$i] == "\\")
				$escaped = !$escaped;
			else
				break;
		}
		
		return $escaped;
	}
	
	/**
	* isCharEscaped: checks if a char is escaped given its position 
	*/
	public static function isMBCharEscaped($str, $index, $text_chars = null) {
		$escaped = false;
		$text_chars = $text_chars ? $text_chars : self::mbStrSplit($str);
		
		for ($i = $index - 1; $i >= 0; $i--) {
			if ($text_chars[$i] == "\\")
				$escaped = !$escaped;
			else
				break;
		}
		
		return $escaped;
	}
	
	/**
	* isMBSubstrCharEscaped: checks if a char is escaped given its position based in the mb_substr php function
	*/
	public static function isMBSubstrCharEscaped($str, $index) {
		$escaped = false;
		
		for ($i = $index - 1; $i >= 0; $i--) {
			if (mb_substr($str, $i, 1) == "\\")
				$escaped = !$escaped;
			else
				break;
		}
		
		return $escaped;
	}
}	

?>
