<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_AI_Chatbot_Security {
	/**
	 * Derives a 256-bit encryption key from the site's own WordPress salts.
	 * Never a static/hardcoded key - it's unique per install.
	 */
	private function get_encryption_key() {
		$salt = ( defined( 'AUTH_KEY' ) && AUTH_KEY ) ? AUTH_KEY : wp_salt( 'auth' );
		return hash( 'sha256', $salt, true );
	}

	public function encrypt_value( $value ) {
		if ( '' === $value || null === $value ) {
			return '';
		}

		if ( ! function_exists( 'openssl_encrypt' ) ) {
			// No OpenSSL available - refuse to store the secret in plain text.
			return '';
		}

		$key = $this->get_encryption_key();
		$iv  = random_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
		$cipher_text = openssl_encrypt( $value, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv );

		if ( false === $cipher_text ) {
			return '';
		}

		return base64_encode( $iv . $cipher_text );
	}

	public function decrypt_value( $value ) {
		if ( empty( $value ) || ! function_exists( 'openssl_decrypt' ) ) {
			return '';
		}

		$raw = base64_decode( $value, true );
		if ( false === $raw ) {
			return '';
		}

		$iv_length = openssl_cipher_iv_length( 'aes-256-cbc' );
		if ( strlen( $raw ) <= $iv_length ) {
			return '';
		}

		$iv          = substr( $raw, 0, $iv_length );
		$cipher_text = substr( $raw, $iv_length );
		$key         = $this->get_encryption_key();

		$plain_text = openssl_decrypt( $cipher_text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv );

		return false === $plain_text ? '' : $plain_text;
	}

	public function check_rate_limit( $ip, $max_requests, $time_window ) {
		global $wpdb;
		$current_time = current_time( 'mysql' );
		$time_ago = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - $time_window );

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->prefix}v7_ai_chatbot_usage_logs WHERE user_ip = %s AND timestamp > %s",
				$ip,
				$time_ago
			)
		);

		return intval( $count ) < $max_requests;
	}

	public function mask_ip( $ip ) {
		$parts = explode( '.', $ip );
		if ( count( $parts ) === 4 ) {
			$parts[3] = '***';
			return implode( '.', $parts );
		}
		return '***';
	}

	public function sanitize_ip( $ip ) {
		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}
		return '0.0.0.0';
	}

	public function validate_nonce( $nonce, $action = 'v7_ai_chatbot_nonce' ) {
		return wp_verify_nonce( $nonce, $action ) !== false;
	}

	public function generate_unique_id( $prefix = '' ) {
		return $prefix . uniqid( '', true );
	}

	public function hash_conversation( $conversation_id ) {
		return wp_hash( $conversation_id );
	}
}
