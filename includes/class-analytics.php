<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_AI_Chatbot_Analytics {
	private $db;
	private $security;

	public function __construct() {
		require_once V7_AI_CHATBOT_PATH . 'includes/class-database.php';
		require_once V7_AI_CHATBOT_PATH . 'includes/class-security.php';
		$this->db = new V7_AI_Chatbot_Database();
		$this->security = new V7_AI_Chatbot_Security();
	}

	public function log_message( $conversation_id, $user_message, $ai_response ) {
		global $wpdb;
		$user_ip = $this->security->sanitize_ip( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );

		if ( empty( $conversation_id ) ) {
			$conversation_id = uniqid( 'conv_' );
			$this->db->create_conversation( $conversation_id, $user_ip );
		}

		$user_message_encrypted = get_option( 'v7_ai_chatbot_settings' )['encryption_enabled'] ?? 0 ? $this->security->encrypt_value( $user_message ) : $user_message;
		$ai_response_encrypted = get_option( 'v7_ai_chatbot_settings' )['encryption_enabled'] ?? 0 ? $this->security->encrypt_value( $ai_response ) : $ai_response;

		$this->db->add_message( $conversation_id, 'user', $user_message_encrypted );
		$this->db->add_message( $conversation_id, 'ai', $ai_response_encrypted );

		$this->db->log_usage( $user_ip, 0, 0, 'success' );
	}

	public function get_total_conversations() {
		global $wpdb;
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}v7_ai_chatbot_conversations" );
		return intval( $count );
	}

	public function get_total_messages() {
		global $wpdb;
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}v7_ai_chatbot_messages" );
		return intval( $count );
	}

	public function get_avg_messages_per_conversation() {
		global $wpdb;
		$avg = $wpdb->get_var( "SELECT AVG(message_count) FROM {$wpdb->prefix}v7_ai_chatbot_conversations" );
		return intval( $avg ) ?? 0;
	}

	public function get_avg_response_time() {
		global $wpdb;
		$avg = $wpdb->get_var( "SELECT AVG(response_time) FROM {$wpdb->prefix}v7_ai_chatbot_messages WHERE response_time IS NOT NULL" );
		return floatval( $avg ) ?? 0;
	}

	public function get_recent_conversations( $limit = 10 ) {
		return $this->db->get_recent_conversations( $limit );
	}

	public function get_conversation_trend( $days = 7 ) {
		global $wpdb;
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DATE(created_at) as date, COUNT(*) as count FROM {$wpdb->prefix}v7_ai_chatbot_conversations WHERE created_at > DATE_SUB(NOW(), INTERVAL %d DAY) GROUP BY DATE(created_at) ORDER BY date ASC",
				$days
			)
		);
		return $results;
	}

	public function get_usage_stats( $days = 7 ) {
		global $wpdb;
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT status, COUNT(*) as count, SUM(tokens_used) as total_tokens FROM {$wpdb->prefix}v7_ai_chatbot_usage_logs WHERE timestamp > DATE_SUB(NOW(), INTERVAL %d DAY) GROUP BY status",
				$days
			)
		);
		return $results;
	}

	public function cleanup_old_conversations( $days ) {
		$this->db->delete_old_conversations( $days );
	}
}
