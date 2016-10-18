<?php

$wgExtensionCredits['validextensionclass'][] = array(
		'path' => __FILE__,
		'name' => 'MassBank',
		'author' => '',
		'url' => '',
		'description' => 'This extension is the combination of all massbank extentions',
		'version'  => '1.0.1',
		'license-name' => ""
);

include_once 'MassBankMenu.php';
include_once 'MassBankSideBar.php';

function fnBuildMenuItemList($lines, &$i, $level, $opt) {
	global $wgParser, $wgTitle, $wgParseListItems;
	
	$content = "";
	$closeLI = false;
	$itemCount = 0;
	
	for (;$i < sizeof($lines); $i++) {
		$itemCount++;

		$line = $lines[$i];
		$line = substr($line, $level);

		$line = fnReplaceKeywords($line);
		$line = fnBuildMenuItemExpr($line);
		//   		$class = "item{$itemCount} level{$level}";

		if ($level > 1) { // nested menu case
			$class = 'submenu' . $level . '-item submenu-item';
		} else {
			$class = 'menu-item';
		}

		$class .= ' menu-' . fnBuildMenuItemCssClass($line); // append menu css class by menu path
		
		$class .= ($itemCount % 2 == 0 ? " even" : " odd");
		
		if (strpos($line, '**') === 0) {// entry in a deeper level:
			// inject a &gt; at the end of the line
			$content = rtrim($content);
			if (strrpos($content, '</a>') === strlen($content) - 4) {
				$content = substr($content,0,-4) . "<em class='menu-arrow'></em></a>";
			}

			$content .= '
				<ul class="submenu' . $level . '-container submenu-container">
					' . fnBuildMenuItemList($lines, $i, $level+1, $opt) . '
				</ul>
				';
			$i--;
			$itemCount--;
		}
		else if (strpos($line, '*') === 0) { // entry in this level:
			if ($closeLI) { //workaround to close the last LI
				$content .= "</li>";
				$closeLI = false;
			}
			
			if (strpos($line, '|') !== false) {
				$line = array_map('trim', explode( '|' , trim($line, '* '), 2) );
				
				$link = ( function_exists("wfMsgForContent") ) ? wfMsgForContent( $line[0] ): wfMessage( $line[0] );
				if ($link == '-') {
					continue;
				}

				$text = ( function_exists("wfMsgExt") ) ? wfMsgExt($line[1], 'parsemag'): wfMessage($line[1], 'parsemag'); // v1.27
				if (function_exists("wfEmptyMsg") ? wfEmptyMsg($line[1], $text): wfMessage($line[1], $text)) {
					$text = $line[1];
				}
				if (function_exists("wfEmptyMsg") ? wfEmptyMsg($line[0], $link): wfMessage($line[1], $link))  {
					$link = $line[0];
				}

				if ( preg_match( '/^(?:' . wfUrlProtocols() . ')/', $link ) ) {
					$href = $link;
				} else {
					$title = Title::newFromText( $link );
					if ( $title ) {
						$title = $title->fixSpecialName();
						$href = $title->getLocalURL();
					} else {
						$href = 'INVALID-TITLE';
					}
				}
				$href = htmlspecialchars($href);
				$text = htmlspecialchars($text);
				$text = fnBuildMenuItemIcon($text); // replace icon notation to icon image tag
				$text = fnBuildMenuItemWrapText($text);
				$content .= "<li class=\"$class\"><a href=\"$href\">$text</a>";
				$closeLI = true;
			}
			else {
				if (trim($line) == "-") {
					$class = " separator";
					$text = "";
				}
				
				$text = htmlspecialchars( trim($line, '* '));
				$text = fnBuildMenuItemIcon($text); // replace icon notation to icon image tag
				$text = fnBuildMenuItemWrapText($text);
				$content .= "<li class=\"$class\"><a>$text</a>";
				$closeLI = true;
			}
		}
		else {
			if ($closeLI) { //workaround to close the last LI
				$content .= "</li>";
				$closeLI = false;
			}
			break;
		}
	}
	if ($closeLI) { //workaround to close the last LI
		$content .= "</li>";
		$closeLI = false;
	}
	return $content;
}

function fnBuildMenuItemCssClass($line) {
	$line = trim($line, "*");
	$a = explode("|", $line);
	$b = explode(":", $a[0]);
	if (sizeof($b) > 1) {
		return str_replace(" ", "-", trim($b[1]));
	}
	return str_replace(" ", "-", trim($b[0]));
}

function fnBuildMenuItemIcon($line) {
	$a = preg_match("/\[\[([^]]+)\]\]/", $line, $matches);
	if ($a > 0) {
		$b = explode(":", $matches[1]);
		if (strcasecmp("icon", $b[0]) == 0) {
			$filename = $b[1];
			$file = wfFindFile($filename);
			if (is_object($file) && $file->exists()) {
				$url = $file->getFullUrl();
				$html = "<img class='menu-icon' src='" . $url . "'/>";
				$line = str_replace($matches[0], $html, $line);
				return $line;
			}
		}
		// 		$html = "<span class='not-exist hidden'>" . $matches[0] . "</span>";
		$line = str_replace($matches[0], "", $line);
	}
	return $line;
}

function fnBuildMenuItemWrapText($text) {
	return "<span>" . trim($text) . "</span>";
}

function fnBuildMenuItemExpr($line) {
	$a = preg_match("/\{\{([^]]+)\}\}/", $line, $matches);
	if ($a > 0) {
		if (strpos($matches[1], "#if:") == 0) {
			$b = str_replace("#if:", "", $matches[1]);
			
			$args = explode("?", $b);

			$expr = str_replace("'", "", $args[0]);
			$out = str_replace("'", "", $args[1]);
			
			$p = 0;
			$end = strlen( $expr );
			$operator = '';
			
			while ( $p < $end ) {
				$char = $expr[$p];
				$char2 = substr( $expr, $p, 2 );
				
				if ( $char2 == '==' || $char2 == '!=' || $char2 == '<=' || $char2 == '>=' ) {
					$operator = $char2;
					break;
				} else {
					$p++;
				}
				
			}
			
			$result = '';
			$operands = array_map('trim', explode($operator, $expr));
			$results = array_map('trim', explode("||", $out));
			if (sizeof($operands) == 2 && sizeof($results) == 2) {
				switch ($operator) {
					case '==':
						$result = ($operands[0] == $operands[1]) ?  $results[0] : $results[1];
						break;
					case '!=':
						$result = ($operands[0] != $operands[1]) ?  $results[0] : $results[1];
						break;
					case '<=':
						$result = ($operands[0] <= $operands[1]) ?  $results[0] : $results[1];
						break;
					case '>=':
						$result = ($operands[0] >= $operands[1]) ?  $results[0] : $results[1];
						break;
				}
			}
			$line = str_replace($matches[0], $result, $line);
			
		}
		
	}
	return $line;
}

function fnTrim($text) {
	return trim(trim($text), "'");
}

function fnReplaceKeywords($text) {
	global $wgLang;
	$text = str_replace('$CURRENT_LANG', $wgLang->getCode(), $text);
	return $text;
}

?>