MediaWikiSkin
=============

customized media wiki skin for new generation of http://www.massbank.jp/

###### Apply MassBank skin as MediaWikiSkin

1. copy all the files and folders related to **massbank** skin into **/skins** directory.
2. Add following line to **LocalSetting.php** file for initialize massbank skin to mediawiki.

> require_once "$IP/skins/massbank/MassBank.php";

###### Setting the MassBank as default skin

change the **$wgDefaultSkin** in **LocalSettings.php** to the lowercase skin name as specified in the skin file.

> $wgDefaultSkin = 'massbank';
