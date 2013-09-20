<?php
/*
 Plugin Name: WP-ISPConfig
 Description: This plugin allow manage some features of ISPConfig with remote user.
 Version: 1.0
 Author: Esteban Truelsegaard <esteban@netmdp.com>
 Author URI: http://www.netmdp.com
 */
# @charset utf-8

if ( ! function_exists( 'add_filter' ) )
	exit;

if ( ! class_exists( 'WPISPConfig' ) ) {

	add_action( 'init', array( 'WPISPConfig', 'init' ) );

	#register_aktivation_hook( plugin_basename( __FILE__ ), array( 'WPISPConfig', 'activate' ) );
	#register_deactivation_hook( plugin_basename( __FILE__ ), array( 'WPISPConfig', 'deactivate' ) );
	register_uninstall_hook( plugin_basename( __FILE__ ), array( 'WPISPConfig', 'uninstall' ) );

	class WPISPConfig {

		const TEXTDOMAIN = 'wpispconfig';

		const VERSION = '1.0';

		/**		 * Option Key		 */
		const OPTION_KEY = 'WPISPConfig_Options';

		/**		 * $uri
		 * absolute uri to the plugin with trailing slash
		 */
		public static $uri = '';

		/**		 * $dir
		 * filesystem path to the plugin with trailing slash
		 */
		public static $dir = '';

		/**		 * $default_options
		 * Some settings to use by default
		 */
		protected static $default_options = array(
			'soapusername' => 'remote_user',
			'soappassword' => 'remote_user_pass',
			'soap_location' => 'http://localhost:8080/ispconfig3/interface/web/remote/index.php',
			'soap_uri' => 'http://localhost:8080/ispconfig3/interface/web/remote/',
			'template_id' => '1',
			'domain' => '',
			'cliente' => '',
			'empresa' => '',
			'username' => '',
			'password' => '',
			'ip' => '',
			'ns1' => '',
			'ns2' => '',
			'email' => '',
		);

		/**		 * $options		 */
		protected $options = array();

		/**		 * init		 */
		public function init() {
		
			self :: $uri = plugin_dir_url( __FILE__ );
			self :: $dir = plugin_dir_path( __FILE__ );
			self :: load_textdomain_file();
			new self( TRUE );
		}

		/**		 * constructor		 */
		public function __construct( $hook_in = FALSE ) {
			$this->load_options();
			
			if ( $hook_in ) {
				add_action( 'admin_init', array( &$this, 'admin_init' ) );
				add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			}
		}

		public function admin_init() {
			wp_register_style( 'oplugincss', plugin_dir_url( __FILE__ ).'oplugins.css');
			wp_register_script( 'opluginjs', plugin_dir_url( __FILE__ ).'oplugins.js');
		}

		/**
		 * admin menu
		 *
		 * @access public
		 * @return void
		 */
		public function admin_menu() {
		
			$page= add_menu_page(__('WP-ISPConfig'), __('WP-ISPConfig'), 'edit_themes', 'ispconfig_allinone',  array( &$this, 'add_ispconfig_allinone' ), self :: $uri.'/prou.png', 3.2); 
			$page= add_submenu_page('ispconfig_allinone', __( 'Add All in One', self :: TEXTDOMAIN ), __( 'Add All in One', self :: TEXTDOMAIN ), 'edit_themes', 'ispconfig_allinone', array( &$this, 'add_ispconfig_allinone' ) );
			add_action('admin_print_styles-' . $page,  array( &$this, 'WPISPConfig_adminfiles') );  // agrego en settings el javascript para lista de plugins

			$page= add_submenu_page('ispconfig_allinone', __('Settings', self :: TEXTDOMAIN), __('Settings', self :: TEXTDOMAIN), 'edit_themes', 'ispconfig_settings',  array( &$this, 'add_admin_submenu_page') );			
			add_action('admin_print_styles-' . $page,  array( &$this, 'WPISPConfig_adminfiles') );  // agrego en settings el javascript para lista de plugins
		}
		
		public function WPISPConfig_adminfiles () {
			wp_enqueue_style( 'oplugincss' );
			wp_enqueue_script( 'opluginjs' );
		}

		/**
		 * an admin submenu page
		 *
		 * @access public
		 * @return void
		 */
		public function add_ispconfig_allinone () {
			if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
				if ( get_magic_quotes_gpc() ) {
					$_POST = array_map( 'stripslashes_deep', $_POST );
				}

				$this->load_options();
				$cfg = $this->options;
				$clientdata = $_POST;

				# creando
				?><div class="updated"><p><?php 
				
				include('allinone.php');
				
				?></p></div><?php
			}
			
			$this->load_options();
			$cfg = $this->options;
			?>
			<div class="wrap">
				<h2><?php _e( 'WP-ISPConfig ADD Client All in One', self :: TEXTDOMAIN );?></h2>
				<form method="post" action="">
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="side-info-column" class="inner-sidebar">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h3 class="handle"><?php _e( 'Donate', self :: TEXTDOMAIN );?></h3>
								<div class="inside">
									<p>WP-ISPConfig <?php echo self :: VERSION ; ?></p>
									<p><?php _e( 'Thanks for test, use and enjoy this plugin.', self :: TEXTDOMAIN );?></p>
									<p><?php _e( 'If you like it, I really appreciate a donation.', self :: TEXTDOMAIN );?></p>
									<p>
									<input type="button" class="button-primary" name="donate" value="<?php _e( 'Click for Donate', self :: TEXTDOMAIN );?>" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7267TH4PT3GSW');return false;"/>
									</p>
									<p><?php /*/ _e('Help', self :: TEXTDOMAIN ); ?><a href="#" onclick="javascript:window.open('https://www.paypal.com/ar/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');"><img  src="https://www.paypal.com/es_XC/Marketing/i/logo/bnr_airlines1_205x67.gif" border="0" alt="Paypal Help"></a>  */ ?>
									</p>
									<p></p>
								</div>
							</div>
							<div class="postbox">
								<h3 class="handle"><?php _e( 'Knows my plugins', self :: TEXTDOMAIN );?></h3>
								<div class="inside" style="margin: 0 -12px -12px -10px;">
									<div class="wpeplugname" id="wpebanover"><a href="http://wordpress.org/extend/plugins/wpebanover/" target="_Blank" class="wpelinks">WPeBanOver</a>
									<div id="wpebanoverdesc" class="tsmall" style="display:none;">Show a small banner and on mouse event (over, out, click, dblclick) show another big or 2nd banner anywhere in your template, post, page or widget.</div></div>
									<p></p>
									<div class="wpeplugname" id="WPeMatico"><a href="http://wordpress.org/extend/plugins/wpematico/" target="_Blank" class="wpelinks">WPeMatico</a>
									<div id="WPeMaticodesc" class="tsmall" style="display:none;"> WPeMatico is for autoblogging. Drink a coffee meanwhile WPeMatico publish your posts. Post automatically from the RSS/Atom feeds organized into campaigns.</a></div></div>
									<p></p>
									<div class="wpeplugname" id="WPeDPC"><a href="http://wordpress.org/extend/plugins/etruel-del-post-copies/" target="_Blank" class="wpelinks">WP-eDel post copies</a>
									<div id="WPeDPCdesc" class="tsmall" style="display:none;">WPeDPC search for duplicated title name or content in posts in the categories that you selected and let you TRASH all duplicated posts in manual mode or automatic scheduled with WordPress Cron.</a></div></div>
									<p></p>
									<div class="wpeplugname" id="WPeBacklinks"><a href="http://www.netmdp.com/2011/10/wpebacklinks/" target="_Blank" class="wpelinks">WPeBacklinks</a>
									<div id="WPeBacklinksdesc" class="tsmall" style="display:none;">Backlinks.com’s original plugin allow only one key for wordpress site.
									This plugin makes it easier to use different keys to use Backlinks assigned for each page or section of wordpress. If you want to make some money, please register on <a href="http://www.backlinks.com/?aff=52126" class="wpeoverlink" target="_Blank">Backlinks.com here.</a></div></div>
									<p></p>
								</div>
							</div>
						</div>
					</div>
					<div id="post-body">
						<div id="post-body-content">
							<div id="normal-sortables" class="meta-box-sortables ui-sortable">
								<div class="postbox inside">
									<h3><?php _e( 'Add All in One site data', self :: TEXTDOMAIN );?></h3>
									<div class="inside">
									<div><strong><?php _e( 'data for All in One parameters.', self :: TEXTDOMAIN );?></strong><br />
											<?php 
											echo '<div> '. __( 'DNS Template ID:' ) .'<input type="number" class="small-text" name="template_id" value="'.$cfg['template_id'].'" /></div>';
											echo '<div> '. __( 'New Domain:' ) .'<input type="text" class="regular-text" name="domain" value="'.$cfg['domain'].'" /></div>';
											echo '<div> '. __( 'New Client Name:' ) .'<input type="text" class="regular-text" name="cliente" value="'.$cfg['cliente'].'" /></div>';
											echo '<div> '. __( 'Company Name:' ) .'<input type="text" class="regular-text" name="empresa" value="'.$cfg['empresa'].'" /></div>';
											echo '<div> '. __( 'Client UserName:' ) .'<input type="text" class="normal-text" name="username" value="'.$cfg['username'].'" /></div>';
											echo '<div> '. __( 'Client Password:' ) .'<input type="text" class="normal-text" name="password" value="'.$cfg['password'].'" /></div>';
											echo '<div> '. __( 'Client IP:' ) .'<input type="text" class="normal-text" name="ip" value="'.$cfg['ip'].'" /></div>';
											echo '<div> '. __( 'NameServer 1:' ) .'<input type="text" class="regular-text" name="ns1" value="'.$cfg['ns1'].'" /></div>';
											echo '<div> '. __( 'NameServer 2:' ) .'<input type="text" class="regular-text" name="ns2" value="'.$cfg['ns2'].'" /></div>';
											echo '<div> '. __( 'e-mail:' ) .'<input type="text" class="regular-text" name="email" value="'.$cfg['email'].'" /></div>';
											?>
										</div>
										<p></p>
										
										<p><input type="submit" class="button-primary" name="submit" value="<?php _e('Click to create AllinOne Client');?>" /></p>
										<p></p>									
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				</form>

			</div><?php
		}
		// FIN ADD ALL IN ONE		

		
		public function add_admin_submenu_page () {
			if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
				if ( get_magic_quotes_gpc() ) {
					$_POST = array_map( 'stripslashes_deep', $_POST );
				}

				# evaluation goes here
				$this->options = $_POST;

				# saving
				if ( $this->update_options() ) {
					?><div class="updated"><p> <?php _e( 'Settings saved', self :: TEXTDOMAIN );?></p></div><?php
				}
			}
			
			$this->load_options();
			$cfg = $this->options;
			?>
			<div class="wrap">
				<h2><?php _e( 'WP-ISPConfig Settings', self :: TEXTDOMAIN );?></h2>
				<form method="post" action="">
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="side-info-column" class="inner-sidebar">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h3 class="handle"><?php _e( 'Donate', self :: TEXTDOMAIN );?></h3>
								<div class="inside">
									<p>WP-ISPConfig <?php echo self :: VERSION ; ?></p>
									<p><?php _e( 'Thanks for test, use and enjoy this plugin.', self :: TEXTDOMAIN );?></p>
									<p><?php _e( 'If you like it, I really appreciate a donation.', self :: TEXTDOMAIN );?></p>
									<p>
									<input type="button" class="button-primary" name="donate" value="<?php _e( 'Click for Donate', self :: TEXTDOMAIN );?>" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7267TH4PT3GSW');return false;"/>
									</p>
									<p><?php /*/ _e('Help', self :: TEXTDOMAIN ); ?><a href="#" onclick="javascript:window.open('https://www.paypal.com/ar/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');"><img  src="https://www.paypal.com/es_XC/Marketing/i/logo/bnr_airlines1_205x67.gif" border="0" alt="Paypal Help"></a>  */ ?>
									</p>
									<p></p>
								</div>
							</div>
							<div class="postbox">
								<h3 class="handle"><?php _e( 'Knows my plugins', self :: TEXTDOMAIN );?></h3>
								<div class="inside" style="margin: 0 -12px -12px -10px;">
									<div class="wpeplugname" id="wpebanover"><a href="http://wordpress.org/extend/plugins/wpebanover/" target="_Blank" class="wpelinks">WPeBanOver</a>
									<div id="wpebanoverdesc" class="tsmall" style="display:none;">Show a small banner and on mouse event (over, out, click, dblclick) show another big or 2nd banner anywhere in your template, post, page or widget.</div></div>
									<p></p>
									<div class="wpeplugname" id="WPeMatico"><a href="http://wordpress.org/extend/plugins/wpematico/" target="_Blank" class="wpelinks">WPeMatico</a>
									<div id="WPeMaticodesc" class="tsmall" style="display:none;"> WPeMatico is for autoblogging. Drink a coffee meanwhile WPeMatico publish your posts. Post automatically from the RSS/Atom feeds organized into campaigns.</a></div></div>
									<p></p>
									<div class="wpeplugname" id="WPeDPC"><a href="http://wordpress.org/extend/plugins/etruel-del-post-copies/" target="_Blank" class="wpelinks">WP-eDel post copies</a>
									<div id="WPeDPCdesc" class="tsmall" style="display:none;">WPeDPC search for duplicated title name or content in posts in the categories that you selected and let you TRASH all duplicated posts in manual mode or automatic scheduled with WordPress Cron.</a></div></div>
									<p></p>
									<div class="wpeplugname" id="WPeBacklinks"><a href="http://www.netmdp.com/2011/10/wpebacklinks/" target="_Blank" class="wpelinks">WPeBacklinks</a>
									<div id="WPeBacklinksdesc" class="tsmall" style="display:none;">Backlinks.com’s original plugin allow only one key for wordpress site.
									This plugin makes it easier to use different keys to use Backlinks assigned for each page or section of wordpress. If you want to make some money, please register on <a href="http://www.backlinks.com/?aff=52126" class="wpeoverlink" target="_Blank">Backlinks.com here.</a></div></div>
									<p></p>
								</div>
							</div>
						</div>
					</div>
					<div id="post-body">
						<div id="post-body-content">
							<div id="normal-sortables" class="meta-box-sortables ui-sortable">
								<div class="postbox inside">
									<h3><?php _e( 'Remote server data', self :: TEXTDOMAIN );?></h3>
									<div class="inside">
										<a style="float:right;" target="_Blank" href="http://www.netmdp.com"><img src="<?php echo self :: $uri ; ?>NetMdP.png"></a>
										<p></p>
										<div><strong><?php _e( 'Complete necessary data to connect to ISPConfig remote server.', self :: TEXTDOMAIN );?></strong><br />
											<div style="display: table;margin: 10px 0;">
											<?php 
											echo '<div> '. __( 'SOAP UserName:' ) .'<input type="text" class="normal-text" name="soapusername" value="'.$cfg['soapusername'].'" /></div>';
											echo '<div> '. __( 'SOAP Password:' ) .'<input type="text" class="normal-text" name="soappassword" value="'.$cfg['soappassword'].'" /></div>';
											echo '<div> '. __( 'SOAP Location:' ) .'<input type="text" class="regular-text" name="soap_location" value="'.$cfg['soap_location'].'" /></div>';
											echo '<div> '. __( 'SOAP URI:' ) .'<input type="text" class="regular-text" name="soap_uri" value="'.$cfg['soap_uri'].'" /></div>';
/* 
											echo '<div><input type="checkbox" class="checkbox" name="cpostypes['.$post_type.']" value="1" '; 
											if(!isset($cpostypes[$post_type])) $cpostypes[$post_type] = false;
											checked( $cpostypes[$post_type],true);
											echo ' /> '. __( $post_type ) .'</div>'; */
												
											?>
											</div>
										</div>
										<p></p>
										<p><input type="submit" class="button-primary" name="submit" value="<?php _e('Save');?>" /></p>
										<p></p>									
										
									</div>
								</div>
								<div class="postbox inside">
									<h3><?php _e( 'Add All in One sites default data', self :: TEXTDOMAIN );?></h3>
									<div class="inside">
									<div><strong><?php _e( 'Default data for All in One parameters.', self :: TEXTDOMAIN );?></strong><br />
											<?php 
											echo '<div> '. __( 'DNS Template ID:' ) .'<input type="number" class="small-text" name="template_id" value="'.$cfg['template_id'].'" /></div>';
											echo '<div> '. __( 'New Domain:' ) .'<input type="text" class="regular-text" name="domain" value="'.$cfg['domain'].'" /></div>';
											echo '<div> '. __( 'Client Name:' ) .'<input type="text" class="regular-text" name="cliente" value="'.$cfg['cliente'].'" /></div>';
											echo '<div> '. __( 'Company Name:' ) .'<input type="text" class="regular-text" name="empresa" value="'.$cfg['empresa'].'" /></div>';
											echo '<div> '. __( 'Client UserName:' ) .'<input type="text" class="normal-text" name="username" value="'.$cfg['username'].'" /></div>';
											echo '<div> '. __( 'Client Password:' ) .'<input type="text" class="normal-text" name="password" value="'.$cfg['password'].'" /></div>';
											echo '<div> '. __( 'Client IP:' ) .'<input type="text" class="normal-text" name="ip" value="'.$cfg['ip'].'" /></div>';
											echo '<div> '. __( 'NameServer 1:' ) .'<input type="text" class="regular-text" name="ns1" value="'.$cfg['ns1'].'" /></div>';
											echo '<div> '. __( 'NameServer 2:' ) .'<input type="text" class="regular-text" name="ns2" value="'.$cfg['ns2'].'" /></div>';
											echo '<div> '. __( 'e-mail:' ) .'<input type="text" class="regular-text" name="email" value="'.$cfg['email'].'" /></div>';
											?>
										</div>
										<p></p>
										
										<p><input type="submit" class="button-primary" name="submit" value="<?php _e('Save');?>" /></p>
										<p></p>									
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				</form>

			</div><?php
		}
		/**
		 * load_textdomain_file
		 *
		 * @access protected
		 * @return void
		 */
		protected function load_textdomain_file() {
			# load plugin textdomain
			load_plugin_textdomain( self :: TEXTDOMAIN, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang' );			
			//load_plugin_textdomain( self :: TEXTDOMAIN, FALSE, basename( plugin_basename( __FILE__ ) ) . '/lang' );
			# load tinyMCE localisation file
			#add_filter( 'mce_external_languages', array( &$this, 'mce_localisation' ) );
		}

		/**
		 * mce_localisation
		 *
		 * @access public
		 * @param array $mce_external_languages
		 * @return array
		 */
		public function mce_localisation( $mce_external_languages ) {

			if ( file_exists( self :: $dir . 'lang/mce_langs.php' ) )
				$mce_external_languages[ 'inpsydeOembedVideoShortcode' ] = self :: $dir . 'lang/mce-langs.php';
			return $mce_external_languages;
		}
		/**
		 * load_options
		 *
		 * @access protected
		 * @return void
		 */
		protected function load_options() {

			if ( ! get_option( self :: OPTION_KEY ) ) {
				if ( empty( self :: $default_options ) )
					return;
				$this->options = self :: $default_options;
				add_option( self :: OPTION_KEY, $this->options , '', 'yes' );
			}
			else {
				$this->options = get_option( self :: OPTION_KEY );
			}
		}

		/**
		 * update_options
		 *
		 * @access protected
		 * @return bool True, if option was changed
		 */
		public function update_options() {
			return update_option( self :: OPTION_KEY, $this->options );
		}

		/**
		 * activation
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public static function activate() {

		}

		/**
		 * deactivation
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public static function deactivate() {

		}

		/**
		 * uninstallation
		 *
		 * @access public
		 * @static
		 * @global $wpdb, $blog_id
		 * @return void
		 */
		public static function uninstall() {
			global $wpdb, $blog_id;
			if ( is_network_admin() ) {
				if ( isset ( $wpdb->blogs ) ) {
					$blogs = $wpdb->get_results(
						$wpdb->prepare(
							'SELECT blog_id ' .
							'FROM ' . $wpdb->blogs . ' ' .
							"WHERE blog_id <> '%s'",
							$blog_id
						)
					);
					foreach ( $blogs as $blog ) {
						delete_blog_option( $blog->blog_id, self :: OPTION_KEY );
					}
				}
			}
			delete_option( self :: OPTION_KEY );
		}
	}
}

