MediaWikiSkin
=============

customized media wiki skin for new generation of http://www.massbank.jp/

###### Installation of MassBank skin

1. copy all the files and folders related to **massbank** skin into **/skins** directory.
2. Add following line into **LocalSetting.php** file for initialize massbank skin.

> require_once "$IP/skins/massbank/MassBank.php";

###### Setting the MassBank skin as default skin

change the **$wgDefaultSkin** in **LocalSettings.php** to the lowercase skin name as specified in the skin file.

> $wgDefaultSkin = 'massbank';

###### Installation of MassBankSidebar extension

1. copy folder and file related **MassBankSidebar** extension into **/extensions** directory.
2. Add following line into **LocalSetting.php** file for include **MassBankSidebar** extension.

> require_once "$IP/extensions/MassBankSideBar/MassBankSideBar.php";
