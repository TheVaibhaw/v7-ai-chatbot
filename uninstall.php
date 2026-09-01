<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Drop database tables
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}v7_ai_chatbot_conversations" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}v7_ai_chatbot_messages" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}v7_ai_chatbot_usage_logs" );

// Remove all plugin options
$options = [
	'v7_ai_chatbot_settings',
	'v7_ai_chatbot_provider_settings',
	'v7_ai_chatbot_api_keys',
	'v7_ai_chatbot_version',
	'v7_ai_chatbot_redirect',
];

foreach ( $options as $option ) {
	delete_option( $option );
}

// Clear scheduled events
wp_clear_scheduled_hook( 'v7_ai_chatbot_cleanup_logs' );
