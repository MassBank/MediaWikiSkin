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
					<div class="massbank-' . $key . '-menu" >
						<ul class="menu-container">
							' . fnBuildMenuItemList($lines, $i, 1) . '
						</ul>
					</div>
				';
				$tpl->data['mbmenulinks'][$key]['content'] = $content;
				$i--;
			}
			else { // use Entry as Title:
				$key = strtolower(trim($line, '* '));
			}	
		}
		
	}
	return true;
}

?>