<?php
/**
 * MassBank skin
 *
 * This is an MassBank skin showcasing the best practices, a companion to the MediaWiki skinning
 * guide available at <https://www.mediawiki.org/wiki/Manual:Skinning>.
 *
 * The code is released into public domain, which means you can freely copy it, modify and release
 * as your own skin without providing attribution and with absolutely no restrictions. Remember to
 * change the license information if you do not intend to provide your changes on the same terms.
 *
 * @file
 * @ingroup Skins
 * @author ...
 * @license CC0 (public domain) <http://creativecommons.org/publicdomain/zero/1.0/>
 */

$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'MassBank',
	'namemsg' => 'skinname-massbank',
	'version' => '1.0',
	'url' => 'https://www.mediawiki.org/wiki/Skin:MassBank',
	'author' => '...',
	'descriptionmsg' => 'massbank-desc',
	// When modifying this skin, remember to change the license information if you do not want to
	// waive all of your rights to your work!
	'license' => 'CC0',
);

$wgValidSkinNames['massbank'] = 'MassBank';

$wgAutoloadClasses['SkinMassBank'] = __DIR__ . '/MassBank.skin.php';
$wgMessagesDirs['MassBank'] = __DIR__ . '/i18n';

$wgResourceModules['skins.massbank.js'] = array(
	'scripts' => array(
		'massbank/resources/js/massbank-main.js',
	),
	'dependencies' => array(
		'jquery.ui.accordion',
		'jquery.ui.button',
	),
	'remoteBasePath' => &$GLOBALS['wgStylePath'],
	'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);

$wgResourceModules['skins.massbank.css'] = array(
	'styles' => array(
		'massbank/resources/css/massbank-screen.css' => array( 'media' => 'screen' ),
		'massbank/resources/css/screen.css' => array( 'media' => 'screen' ),
	),
	'remoteBasePath' => &$GLOBALS['wgStylePath'],
	'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);
