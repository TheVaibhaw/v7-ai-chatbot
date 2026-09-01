<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_AI_Chatbot_Database {
	public function create_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "
		CREATE TABLE IF NOT EXISTS {$wpdb->prefix}v7_ai_chatbot_conversations (
			id VARCHAR(50) PRIMARY KEY,
			user_ip VARCHAR(45) NOT NULL,
			message_count INT DEFAULT 0,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_user_ip (user_ip),
			INDEX idx_created_at (created_at)
		) $charset_collate;

		CREATE TABLE IF NOT EXISTS {$wpdb->prefix}v7_ai_chatbot_messages (
			id INT AUTO_INCREMENT PRIMARY KEY,
			conversation_id VARCHAR(50) NOT NULL,
			message_type ENUM('user', 'ai') DEFAULT 'user',
			message_text LONGTEXT NOT NULL,
			response_time INT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			FOREIGN KEY (conversation_id) REFERENCES {$wpdb->prefix}v7_ai_chatbot_conversations(id) ON DELETE CASCADE,
			INDEX idx_conversation_id (conversation_id),
			INDEX idx_created_at (created_at)
		) $charset_collate;

		CREATE TABLE IF NOT EXISTS {$wpdb->prefix}v7_ai_chatbot_usage_logs (
			id INT AUTO_INCREMENT PRIMARY KEY,
			user_ip VARCHAR(45) NOT NULL,
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
			tokens_used INT DEFAULT 0,
			cost DECIMAL(10, 4) DEFAULT 0,
			status VARCHAR(20),
			error_message LONGTEXT,
			INDEX idx_user_ip (user_ip),
			INDEX idx_timestamp (timestamp)
		) $charset_collate;
		";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public function create_conversation( $conversation_id, $user_ip ) {
		global $wpdb;
		$wpdb->insert(
			"{$wpdb->prefix}v7_ai_chatbot_conversations",
			[
				'id'      => $conversation_id,
				'user_ip' => $user_ip,
			],
			[ '%s', '%s' ]
		);
	}

	public function add_message( $conversation_id, $message_type, $message_text, $response_time = null ) {
		global $wpdb;
		$wpdb->insert(
			"{$wpdb->prefix}v7_ai_chatbot_messages",
			[
				'conversation_id' => $conversation_id,
				'message_type'    => $message_type,
				'message_text'    => $message_text,
				'response_time'   => $response_time,
			],
			[ '%s', '%s', '%s', '%d' ]
		);

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}v7_ai_chatbot_conversations SET message_count = message_count + 1, updated_at = NOW() WHERE id = %s",
				$conversation_id
			)
		);
	}

	public function get_conversations_by_ip( $user_ip ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}v7_ai_chatbot_conversations WHERE user_ip = %s ORDER BY created_at DESC",
				$user_ip
			)
		);
	}

	public function get_messages( $conversation_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}v7_ai_chatbot_messages WHERE conversation_id = %s ORDER BY created_at ASC",
				$conversation_id
			)
		);
	}

	public function log_usage( $user_ip, $tokens_used, $cost, $status, $error_message = null ) {
		global $wpdb;
		$wpdb->insert(
			"{$wpdb->prefix}v7_ai_chatbot_usage_logs",
			[
				'user_ip'       => $user_ip,
				'tokens_used'   => $tokens_used,
				'cost'          => $cost,
				'status'        => $status,
				'error_message' => $error_message,
			],
			[ '%s', '%d', '%f', '%s', '%s' ]
		);
	}

	public function delete_old_conversations( $days ) {
		global $wpdb;
		$cutoff_date = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( $days * 24 * 60 * 60 ) );
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}v7_ai_chatbot_conversations WHERE created_at < %s",
				$cutoff_date
			)
		);
	}

	public function get_recent_conversations( $limit = 10 ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}v7_ai_chatbot_conversations ORDER BY created_at DESC LIMIT %d",
				$limit
			)
		);
	}
}
