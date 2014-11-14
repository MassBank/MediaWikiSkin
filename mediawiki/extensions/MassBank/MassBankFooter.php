<?php

$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'fnMassBankFooter';
function fnMassBankFooter( $sk, &$tpl ) {
	$tpl->set( 'termsofservice', $sk->footerLink( 'termsofservice', 'termsofservicepage' ) );
	$tpl->data['footerlinks']['places'][] = 'termsofservice';
	return true;
}

?>