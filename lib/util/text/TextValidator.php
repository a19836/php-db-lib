<?php
/*
 * Copyright (c) 2025 Bloxtor (http://bloxtor.com) and Joao Pinto (http://jplpinto.com)
 * 
 * Multi-licensed: BSD 3-Clause | Apache 2.0 | GNU LGPL v3 | HLNC License (http://bloxtor.com/LICENSE_HLNC.md)
 * Choose one license that best fits your needs.
 *
 * Original PHP DB Lib Repo: https://github.com/a19836/php-db-lib/
 * Original Bloxtor Repo: https://github.com/a19836/bloxtor
 *
 * YOU ARE NOT AUTHORIZED TO MODIFY OR REMOVE ANY PART OF THIS NOTICE!
 */

include_once get_lib("util.text.TextSanitizer");

class TextValidator {
	
	public static function isBinary($value) {
		//note that if the $value contains accents, then it may be in this regex, so we need to remove accents and then check again
		if (is_string($value) && preg_match('~[^\x20-\x7E\t\r\n]~', $value)) {
			$value_without_accents = TextSanitizer::normalizeAccents($value);
			
			return preg_match('~[^\x20-\x7E\t\r\n]~', $value_without_accents);
		}
		
		return false;
	}
}
?>
