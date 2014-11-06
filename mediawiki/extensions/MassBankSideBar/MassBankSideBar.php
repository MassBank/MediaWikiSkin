<?php
/**
 * apply new format for mediawiki sidebar
 * this is a modification of http://www.mediawiki.org/wiki/Extension:CSS_MenuSidebar
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 ) ;
}
 
$wgExtensionCredits['other'][] = array(
    'name' => 'MassBankSidebar',
	'version' => '0.0.1',
    'author' => '',
    'description' => 'Sidebar can be displayed as a Menu',
	'url'     => 'http://www.mediawiki.org/wiki/Extension:CSS_MassBankSidebar',
);
 
$wgHooks['SkinBuildSidebar'][] = 'fnMassBankSidebar';
 
/* If this is set to true, each ListItem is parsed by the MediaWiki parser 
 * which allows more flexible inclusion of MediaWiki content e.g links to files. 
 * If set to false, a similar behaviour to the normal SideBar is used.
 * 
 * Be careful when using this!
 */
$wgParseListItems = false;
 
function fnMassBankSidebar($skin, &$bar) {
	global $wgParser, $wgUser, $wgTitle, $wgParseListItems;
 
	wfProfileIn( __METHOD__ );
 
	$title = Title::newFromText("MassBankSidebar",NS_MEDIAWIKI);	
	/** Use the revision directly to prevent other hooks to be called */
	$rev = Revision::newFromTitle( $title );
 
	if ($rev)
		$lines = explode("\n", $rev->getRawText());
 
	if ($lines && count($lines) > 0) {
 
		$opt = null; 
 
		/* init the parser */
		if ($wgParseListItems) {
			if (!is_object($wgParser)) {
			    $wgParser = new Parser();
			    $opt = $wgParser->mOptions;
			}
			if (!is_object($opt)) {
			    $opt = ParserOptions::newFromUser($wgUser);
			}
		}
 
		for ($i = 0; $i < sizeof($lines); $i++) {
			$line = $lines[$i];
 
			if (strpos($line, '**') === 0 && $i > 0) {// entry in a deeper level:
				$content = '
			<div class="massbank-sidebar">
				<ul>
					' . fnBuildList($lines,$i,1, $opt) . '
				</ul>
			</div>
			';
				$bar[$title] = $content;
				$i--;
			}
			else { // use Entry as Title:
				$title = trim($line, '* ');
			}	
		}
	}
	return true;
}
 
function fnBuildList($lines,&$i, $level, $opt) {
	global $wgParser, $wgTitle, $wgParseListItems;
 
	$content = "";
	$closeLI = false;
	$itemCount = 0;
	for (;$i < sizeof($lines); $i++) {
		$itemCount++;		
 
		$line = $lines[$i];
		$line = substr($line,$level);
 
  		$class = "item{$itemCount}"; 
  		$class .= " menu-" . fnCssClassByLine($line); 
  		$class .= ($itemCount % 2 == 0 ? " even" : " odd"); 
 
		if (strpos($line, '**') === 0) {// entry in a deeper level:		
			// inject a &gt; at the end of the line
			$content = rtrim($content); 
			if (strrpos($content,'</a>') === strlen($content) - 4) {
				$content = substr($content,0,-4) . "<em></em></a>"; 
// 				$content = substr($content,0,-4) . "<em>&gt;</em></a>"; 
			}
 
			$content .= '
				<div><ul>
					' .fnBuildList($lines,$i,$level+1, $opt) . '
				</ul></div>
				';
			$i--;
			$itemCount--;
		}
		else if (strpos($line, '*') === 0) { // entry in this level:
			if ($closeLI) { //workaround to close the last LI 
				$content .= "</li>";
				$closeLI = false;
			}
			if ($wgParseListItems) {
				$text = $wgParser->parse(trim($line, '* '),$wgTitle,$opt,true,true)->getText();
				$text = substr(trim($text),3,-5); // removes <p> and \n</p> that is generated by the parser
 
				if (trim($text) == "-"){
					$class .= " separator";
					$text = "";
				}
				if (strpos($text, '<a') !== 0) 
  					$text = "<a>" . $text . "</a>"; // this is needed to display normal text correctly
 
				$content .= "<li class=\"$class\">$text";
				$closeLI = true;
			}
			else
			{
				if (strpos($line, '|') !== false) {
					$line = array_map('trim', explode( '|' , trim($line, '* '), 2 ) );
					$link = wfMsgForContent( $line[0] );
					if ($link == '-')
						continue;
 
					$text = wfMsgExt($line[1], 'parsemag');
					if (wfEmptyMsg($line[1], $text))
						$text = $line[1];
					if (wfEmptyMsg($line[0], $link))
						$link = $line[0];
 
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
					$content .= "<li class=\"$class\"><a href=\"$href\">$text</a>";
					$closeLI = true;
				}
				else {
					if (trim($line) == "-") {
						$class = " separator";
						$text = "";
					}
 
					$text = htmlspecialchars( trim($line, '* '));
					$content .= "<li class=\"$class\"><a>$text</a>";
					$closeLI = true;
				}
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

function fnCssClassByLine($line) {
	$a = explode("|", $line);
	$b = explode(":", $a[0]);
	return str_replace(" ", "-", trim($b[1]));
// 	return (str_replace(" ", "-", trim(explode(":", explode("|", $line)[0])[1])));
}