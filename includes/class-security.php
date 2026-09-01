<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_AI_Chatbot_Security {
	public function encrypt_value( $value ) {
		if ( defined( 'ABSPATH' ) && function_exists( 'wp_encrypt_or_decrypt' ) ) {
			return wp_encrypt_or_decrypt( 'encrypt', $value );
		}
		return base64_encode( wp_json_encode( [ 'value' => $value, 'salt' => wp_salt() ] ) );
	}

	public function decrypt_value( $value ) {
		if ( defined( 'ABSPATH' ) && function_exists( 'wp_encrypt_or_decrypt' ) ) {
			return wp_encrypt_or_decrypt( 'decrypt', $value );
		}
		try {
			$decoded = json_decode( base64_decode( $value ), true );
			return $decoded['value'] ?? '';
		} catch ( Exception $e ) {
			return '';
		}
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
