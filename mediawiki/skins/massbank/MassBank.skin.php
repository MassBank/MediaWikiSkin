<?php
/**
 * Skin file for the MassBank skin.
 *
 * @file
 * @ingroup Skins
 */

/**
 * SkinTemplate class for the MassBank skin
 *
 * @ingroup Skins
 */
class SkinMassBank extends SkinTemplate {
	public $skinname = 'massbank', $stylename = 'massbank',
		$template = 'MassBankTemplate', $useHeadElement = true;

	/**
	 * Add JavaScript via ResourceLoader
	 *
	 * Uncomment this function if your skin has a JS file or files.
	 * Otherwise you won't need this function and you can safely delete it.
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$out->addModules( array( 
			'skins.massbank.js' 
		) );
	}
	
	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( array(
			'mediawiki.skinning.interface', 'skins.massbank.css'
		) );
	}
}

/**
 * BaseTemplate class for the MassBank skin
 *
 * @ingroup Skins
 */
class MassBankTemplate extends BaseTemplate {
	/**
	 * Outputs a single horizontal portlet of any kind.
	 */
	private function outputPortletTopMenu( $box ) {
		if ( !$box['content'] ) {
			return;
		}

		?>
		<div
			role="horizontal-navigation"
			class="mw-portlet <?php echo $box['cssClass'] ?>"
			id="<?php echo Sanitizer::escapeId( $box['id'] ) ?>"
			<?php echo Linker::tooltip( $box['id'] ) ?>
		>
			<?php
			if ( is_array( $box['content'] ) ) {
				echo '<ul class="h-menu">';
				foreach ( $box['content'] as $key => $item ) {
					echo $this->makeListItem( $key, $item );
				}
				echo '</ul>';
			} else {
				echo $box['content'];
			}?>
		</div>
		<?php
	}
	
	/**
	 * Outputs a single dropdown menu portlet of any kind.
	 */
	private function outputDropDownMenuPortlet( $box ) {
		if ( !$box['content'] ) {
			return;
		}
	
		?>
		<ul
			role="dd-navigation"
			id="<?php echo Sanitizer::escapeId( $box['id'] ) ?>"
			class="mw-dropdown-portlet <?php echo $box['cssClass'] ?>"
		>
			<li class="dropdown-menu">
				<a class="dropdown-menu-title"><?php echo $box['headerMessage']; ?></a>

				<?php
				if ( is_array( $box['content'] ) ) {
					echo '<ul  class="dropdown-submenu">';
					foreach ( $box['content'] as $key => $item ) {
						echo $this->makeListItem( $key, $item );
					}
					echo '</ul>';
				} else {
					echo $box['content'];
				}?>
			</li>
		</ul>
		<?php
	}
	
	/**
	 * Outputs a single sidebar portlet of any kind.
	 */
	private function outputPortlet( $box ) {
		if ( !$box['content'] ) {
			return;
		}

		?>
		<div
			role="navigation"
			class="mw-portlet <?php if (isset($box['cssClass'])) { echo $box['cssClass']; } ?>"
			id="<?php echo Sanitizer::escapeId( $box['id'] ) ?>"
			<?php echo Linker::tooltip( $box['id'] ) ?>
		>
			<?php
				if ( 
					( isset( $box['headerMessage'] ) && strlen( $box['headerMessage'] ) > 0 ) || 
					( isset( $box['header'] ) && strlen( $box['header'] ) > 0 ) ) {
			?>
			<h3>
				<?php
				if ( isset( $box['headerMessage'] ) ) {
					$this->msg( $box['headerMessage'] );
				} else {
					echo htmlspecialchars( $box['header'] );
				}
				?>
			</h3>
			<?php
				}
			?>

			<?php
			if ( is_array( $box['content'] ) ) {
				echo '<ul>';
				foreach ( $box['content'] as $key => $item ) {
					echo $this->makeListItem( $key, $item );
				}
				echo '</ul>';
			} else {
				echo $box['content'];
			}?>
		</div>
		<?php
	}

	/**
	 * Outputs the entire contents of the page
	 */
	public function execute() {
		$this->html( 'headelement' ) ?>
		<div id="mw-top">
			<div id="top-menu-container" class="navigation-bar mw-body-wrapper">
				<?php
					$this->outputPortletTopMenu( array(
						'id' => 'p-home-menu',
						'cssClass' => 'place-left',
						'content' => array ( 
							'main' => array (
								'id' => 'mw-nav-home',
								'text' => $this->data['sitename'], 
								'href' => htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] )
							)
						),
					) );
				?>
				<?php
					$this->outputPortletTopMenu( array(
						'id' => 'p-function-menus',
						'cssClass' => 'place-right',
						'content' => $this->getFunctionMenus()
					) );
				?>
				<br clear="all"/>
			</div>
		</div>
		<div id="mw-top-sub">
			<div id="top-submenu-container" class="navigation-bar mw-body-wrapper">
				<?php
					$this->outputDropDownMenuPortlet( array(
							'id' => 'p-namespaces',
							'headerMessage' => 'namespaces',
							'cssClass' => 'h-menu place-left',
							'content' => $this->data['content_navigation']['namespaces'],
					) );
					$this->outputDropDownMenuPortlet( array(
							'id' => 'p-variants',
							'headerMessage' => 'variants',
							'cssClass' => 'h-menu place-left',
							'content' => $this->data['content_navigation']['variants'],
					) );
					$this->outputDropDownMenuPortlet( array(
							'id' => 'p-views',
							'headerMessage' => 'views',
							'cssClass' => 'h-menu place-left',
							'content' => $this->data['content_navigation']['views'],
					) );
					$this->outputDropDownMenuPortlet( array(
							'id' => 'p-actions',
							'headerMessage' => 'actions',
							'cssClass' => 'h-menu place-left',
							'content' => $this->data['content_navigation']['actions'],
					) );
					foreach ( $this->getSidebar() as $boxName => $box ) {
						if ($box['id'] != 'p-general') {
							$this->outputDropDownMenuPortlet( array(
									'id' => $box['id'],
									'headerMessage' => $box['header'],
									'cssClass' => 'h-menu place-left',
									'content' => $box['content'],
							) );
						}
					}

 					$this->outputPortletTopMenu( array(
 						'id' => 'p-personal',
 						'cssClass' => 'place-right',
 						'content' => $this->getPersonalTools(),
 					) );
				?>
				<br clear="all"/>
			</div>
		</div>
		<div id="header_top_container" class="mw-body-wrapper"></div>
		<div id="mw-wrapper" class="mw-body-wrapper">

			<div class="mw-body" role="main">
				<?php if ( $this->data['sitenotice'] ) { ?>
					<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
				<?php } ?>

				<?php if ( $this->data['newtalk'] ) { ?>
					<div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
				<?php } ?>

				<h1 class="firstHeading">
					<span dir="auto"><?php $this->html( 'title' ) ?></span>
				</h1>

				<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>

				<div class="mw-body-content">
					<div id="contentSub">
						<?php if ( $this->data['subtitle'] ) { ?>
							<p><?php $this->html( 'subtitle' ) ?></p>
						<?php } ?>
						<?php if ( $this->data['undelete'] ) { ?>
							<p><?php $this->html( 'undelete' ) ?></p>
						<?php } ?>
					</div>

					<?php $this->html( 'bodytext' ) ?>

					<?php $this->html( 'catlinks' ) ?>

					<?php $this->html( 'dataAfterContent' ); ?>

				</div>
			</div>


			<div id="mw-navigation">
				<h2><?php $this->msg( 'navigation-heading' ) ?></h2>

				<form
					action="<?php $this->text( 'wgScript' ) ?>"
					role="search"
					class="mw-portlet"
					id="p-search"
				>
					<input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>" />

					<h3><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h3>

					<?php echo $this->makeSearchInput( array( "id" => "searchInput" ) ) ?>
					<?php echo $this->makeSearchButton( 'go' ) ?>

				</form>

				<?php
					foreach ( $this->getSidebar() as $boxName => $box ) {
						if ($box['id'] == 'p-general') {
							$this->outputPortlet( $box );
						}
					}
				?>
			</div>

			<div id="mw-footer">
				<?php foreach ( $this->getFooterLinks() as $category => $links ) { ?>
					<ul role="contentinfo">
						<?php foreach ( $links as $key ) { ?>
							<li><?php $this->html( $key ) ?></li>
						<?php } ?>
					</ul>
				<?php } ?>

				<ul role="contentinfo">
					<?php foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) { ?>
						<li>
							<?php
							foreach ( $footerIcons as $icon ) {
								echo $this->getSkin()->makeFooterIcon( $icon );
							}
							?>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php $this->printTrail() ?>
		</body></html>
		<?php
		
	}
	
	function getFunctionMenus() {
		return array (
					'sitemap' => array (
							'id' => 'mw-nav-sitemap',
							'text' => 'Site Map',
							'href' => 'page:sitemap'
					),
					'regulation' => array (
							'id' => 'mw-nav-regulation',
							'text' => 'Use Restrictions',
							'href' => 'page:regulation'
					),
					'copyright' => array (
							'id' => 'mw-nav-copyright',
							'text' => 'Copyright',
							'href' => 'page:copyright'
					),
					'language' => array (
							'id' => 'mw-nav-language',
							'text' => (true ? 'English Site' : 'Japanese Site'),
							'href' => (true ? 'English' : 'Japanese')
					)
			);
	}
	
	function getSidebarGeneral( $options = array() ) {
		$sidebar = $this->data['sidebar'];
	}
}
