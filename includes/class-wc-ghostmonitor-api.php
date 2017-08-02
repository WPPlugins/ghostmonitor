<?php
defined( 'ABSPATH' ) or die();
if ( ! class_exists( 'Ghostmonitor_API' ) ):
	class Ghostmonitor_API {
		public function __construct() {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_action( 'parse_request', array( $this, 'sniff_requests' ), 0 );
		}
		public function add_query_vars( $vars ) {
			$vars[] = 'recart_info';
			return $vars;
		}
		public function sniff_requests($wp) {
			if ( array_key_exists( 'recart_info', $wp->query_vars ) === true ) {
				$this->handle_request();
				die;
			}
		}
		private function handle_request() {
			$config = get_option( 'woocommerce_wc_ghostmonitor_settings');
			$ghostmonitorInfo = array(
				'account' => $config['ghostmonitor_id'],
				'domain' => $config['ghostmonitor_domain_name'],
				'emailFirst' => $config['ghostmonitor_email_first']
			);
			$this->send_response($ghostmonitorInfo);
		}
		private function send_response( $res, $status_code = 200 ) {
			http_response_code( $status_code );
			header( 'Content-Type: application/json; charset=utf-8' );
			echo json_encode( $res );
			die;
		}
	}
	new Ghostmonitor_API();
endif;