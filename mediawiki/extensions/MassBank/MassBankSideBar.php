<?php

/**
 * apply new format for mediawiki sidebar
 * this is a modification of http://www.mediawiki.org/wiki/Extension:CSS_MenuSidebar
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 ) ;
}
 
$wgExtensionCredits['skin'][] = array(
    'name' => 'massbanksidebar',
	'version' => '0.0.2',
    'author' => '',
    'description' => 'Sidebar can be displayed as a Menu',
	'url'     => '',
);
 
$wgHooks['SkinBuildSidebar'][] = 'fnMassBankSidebar';
 
 
function fnMassBankSidebar($skin, &$bar) {
	
	wfProfileIn( __METHOD__ );
 
	$title = Title::newFromText("massbanksidebar", NS_MEDIAWIKI);	
	/** Use the revision directly to prevent other hooks to be called */
	$rev = Revision::newFromTitle( $title );
 
	$lines = NULL;
	if ( $rev ) {
		$lines = explode("\n", function_exists( "getRawText" )? $rev->getRawText(): $rev->getText( Revision::RAW ));
	}
 
	if ($lines && count($lines) > 0) {
 
		$opt = null; 
 
		for ($i = 0; $i < sizeof($lines); $i++) {
			$line = $lines[$i];
 
			if (strpos($line, '**') === 0 && $i > 0) {// entry in a deeper level:
				$content = '
				<div class="massbank-sidebar">
					<ul class="menu-container">
						' . fnBuildMenuItemList($lines, $i, 1, $opt) . '
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