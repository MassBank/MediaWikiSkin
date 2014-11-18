<?php

/**
 * apply new format for mediawiki menu
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 ) ;
}

$wgExtensionCredits['other'][] = array(
		'name' => 'massbankmenu',
		'version' => '0.0.1',
		'author' => '',
		'description' => 'additional links can be displayed as a Menu',
		'url'     => '',
);

$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'fnMassBankMenu';

function fnMassBankMenu($skin, &$tpl) {
	global $wgContLang;

	wfProfileIn( __METHOD__ );
	
	$title = Title::newFromText("massbankmenu", NS_MEDIAWIKI);
	/** Use the revision directly to prevent other hooks to be called */
	$rev = Revision::newFromTitle( $title );
	
	if ($rev) {
		$lines = explode("\n", $rev->getRawText());
	}
	
	if ($lines && count($lines) > 0) {
		for ($i = 0; $i < sizeof($lines); $i++) {
			$line = $lines[$i];
			
			if (strpos($line, '**') === 0 && $i > 0) {// entry in a deeper level:
				$content = '
					<div id="massbank-' . $settings['id'] . '-menu" class="' . $settings['type'] . '-menu-container" >
						<ul class="menu-container">
							' . fnBuildMenuItemList($lines, $i, 1) . '
						</ul>
					</div>
				';
				$tpl->data['mbmenulinks'][$settings['id']]['content'] = $content;
				$i--;
			}
			else { // use Entry as Title:
				$settings = fnBuildMenuSettings( $wgContLang->lc( trim($line, '* ') ) );
// 				$key = $wgContLang->lc( trim($line, '* ') );
// 				$key = strtolower(trim($line, '* '));
			}	
		}
		
	}
	return true;
}

function fnBuildMenuSettings($line) {
	$a = preg_match("/\{\{([^]]+)\}\}/", $line, $matches);
	$result = array();
	if ($a > 0) {
		$args = explode(",", $matches[1]);
		foreach ($args as &$arg) {
			$arg = trim($arg);
			if (strpos($arg, "#id") === 0) {
				$result['id'] = trim(trim(trim(trim($arg, "#id")), ":"));
			} else if (strpos($arg, "#type") === 0) {
				$result['type'] = trim(trim(trim(trim($arg, "#type")), ":"));
			}
		}
	}
// 	print_r($result);
	return $result;
}

?>