<?php
/*
Plugin Name: V7 AI Chatbot Pro
Plugin URI: https://github.com/TheVaibhaw/v7-ai-chatbot
Description: Enterprise-grade AI-powered chatbot for WordPress. Supports multiple AI providers with advanced security, analytics, and GDPR compliance.
Version: 3.0.0
Author: Vaibhaw Kumar
Author URI: https://vaibhawkumar.in
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: v7-ai-chatbot
Domain Path: /languages
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 8.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('V7_AI_CHATBOT_VERSION', '3.0.0');
define('V7_AI_CHATBOT_PATH', plugin_dir_path(__FILE__));
define('V7_AI_CHATBOT_URL', plugin_dir_url(__FILE__));
define('V7_AI_CHATBOT_TEXTDOMAIN', 'v7-ai-chatbot');
define('V7_AI_CHATBOT_BASENAME', plugin_basename(__FILE__));

require_once V7_AI_CHATBOT_PATH . 'includes/class-database.php';
require_once V7_AI_CHATBOT_PATH . 'includes/class-ai-provider.php';
require_once V7_AI_CHATBOT_PATH . 'includes/class-security.php';
require_once V7_AI_CHATBOT_PATH . 'includes/class-analytics.php';
require_once V7_AI_CHATBOT_PATH . 'includes/provider-models.php';

class V7_AI_Chatbot {
	private static $instance = null;
	private $db;
	private $ai_provider;
	private $security;
	private $analytics;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->db = new V7_AI_Chatbot_Database();
		$this->ai_provider = new V7_AI_Chatbot_AI_Provider();
		$this->security = new V7_AI_Chatbot_Security();
		$this->analytics = new V7_AI_Chatbot_Analytics();

		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'wp_footer', [ $this, 'render_chatbot' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );
		add_action( 'wp_ajax_v7_ai_chatbot_query', [ $this, 'handle_query' ] );
		add_action( 'wp_ajax_nopriv_v7_ai_chatbot_query', [ $this, 'handle_query' ] );
		add_action( 'wp_ajax_v7_ai_chatbot_settings', [ $this, 'handle_settings_ajax' ] );
		add_filter( 'plugin_action_links_' . V7_AI_CHATBOT_BASENAME, [ $this, 'settings_link' ] );
		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
		add_action( 'plugin_action_links_' . V7_AI_CHATBOT_BASENAME, [ $this, 'add_plugin_action_links' ] );
		register_activation_hook( __FILE__, [ $this, 'activate_plugin' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate_plugin' ] );
		register_uninstall_hook( __FILE__, [ 'V7_AI_Chatbot', 'uninstall_plugin' ] );
	}

	public function load_textdomain() {
		load_plugin_textdomain( V7_AI_CHATBOT_TEXTDOMAIN, false, dirname( V7_AI_CHATBOT_BASENAME ) . '/languages' );
	}

	public function activate_plugin() {
		$this->db->create_tables();
		add_option( 'v7_ai_chatbot_redirect', 1 );
		add_option( 'v7_ai_chatbot_version', V7_AI_CHATBOT_VERSION );
	}

	public function deactivate_plugin() {
		// Cleanup scheduled events if any
		wp_clear_scheduled_hook( 'v7_ai_chatbot_cleanup_logs' );
	}

	public static function uninstall_plugin() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}v7_ai_chatbot_conversations" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}v7_ai_chatbot_messages" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}v7_ai_chatbot_usage_logs" );

		// Remove all options
		delete_option( 'v7_ai_chatbot_settings' );
		delete_option( 'v7_ai_chatbot_provider_settings' );
		delete_option( 'v7_ai_chatbot_api_keys' );
		delete_option( 'v7_ai_chatbot_version' );
	}

	public function settings_link( $links ) {
		array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=v7-ai-chatbot' ) . '">' . esc_html__( 'Settings', V7_AI_CHATBOT_TEXTDOMAIN ) . '</a>' );
		array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=v7-ai-chatbot-analytics' ) . '">' . esc_html__( 'Analytics', V7_AI_CHATBOT_TEXTDOMAIN ) . '</a>' );
		return $links;
	}

	public function add_plugin_action_links( $links ) {
		return $this->settings_link( $links );
	}

	public function add_menu() {
		add_menu_page(
			esc_html__( 'V7 AI Chatbot', V7_AI_CHATBOT_TEXTDOMAIN ),
			esc_html__( 'AI Chatbot', V7_AI_CHATBOT_TEXTDOMAIN ),
			'manage_options',
			'v7-ai-chatbot',
			[ $this, 'settings_page' ],
			'dashicons-format-chat',
			30
		);

		add_submenu_page(
			'v7-ai-chatbot',
			esc_html__( 'Analytics', V7_AI_CHATBOT_TEXTDOMAIN ),
			esc_html__( 'Analytics', V7_AI_CHATBOT_TEXTDOMAIN ),
			'manage_options',
			'v7-ai-chatbot-analytics',
			[ $this, 'analytics_page' ]
		);

		add_submenu_page(
			'v7-ai-chatbot',
			esc_html__( 'Conversations', V7_AI_CHATBOT_TEXTDOMAIN ),
			esc_html__( 'Conversations', V7_AI_CHATBOT_TEXTDOMAIN ),
			'manage_options',
			'v7-ai-chatbot-conversations',
			[ $this, 'conversations_page' ]
		);

		add_submenu_page(
			'v7-ai-chatbot',
			esc_html__( 'Documentation', V7_AI_CHATBOT_TEXTDOMAIN ),
			esc_html__( 'Documentation', V7_AI_CHATBOT_TEXTDOMAIN ),
			'manage_options',
			'v7-ai-chatbot-docs',
			[ $this, 'documentation_page' ]
		);
	}

	public function register_settings() {
		register_setting( 'v7_ai_chatbot_group', 'v7_ai_chatbot_settings', [
			'sanitize_callback' => [ $this, 'sanitize_settings' ],
			'type' => 'object',
		] );

		register_setting( 'v7_ai_chatbot_group', 'v7_ai_chatbot_provider_settings', [
			'sanitize_callback' => [ $this, 'sanitize_provider_settings' ],
			'type' => 'object',
		] );

		register_setting( 'v7_ai_chatbot_group', 'v7_ai_chatbot_api_keys', [
			'sanitize_callback' => [ $this, 'sanitize_api_keys' ],
			'type' => 'object',
		] );
	}

	public function sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			$input = [];
		}

		return [
			'enabled'              => ! empty( $input['enabled'] ) ? 1 : 0,
			'position'             => isset( $input['position'] ) ? sanitize_text_field( $input['position'] ) : 'bottom-right',
			'primary_color'        => isset( $input['primary_color'] ) ? sanitize_hex_color( $input['primary_color'] ) : '#0073aa',
			'bubble_color'         => isset( $input['bubble_color'] ) ? sanitize_hex_color( $input['bubble_color'] ) : '#0073aa',
			'text_color'           => isset( $input['text_color'] ) ? sanitize_hex_color( $input['text_color'] ) : '#ffffff',
			'greeting'             => isset( $input['greeting'] ) ? sanitize_text_field( $input['greeting'] ) : esc_html__( 'Hi! How can I help you today?', V7_AI_CHATBOT_TEXTDOMAIN ),
			'placeholder'          => isset( $input['placeholder'] ) ? sanitize_text_field( $input['placeholder'] ) : esc_html__( 'Type your message...', V7_AI_CHATBOT_TEXTDOMAIN ),
			'max_tokens'           => isset( $input['max_tokens'] ) ? absint( $input['max_tokens'] ) : 500,
			'temperature'          => isset( $input['temperature'] ) ? floatval( $input['temperature'] ) : 0.7,
			'include_pages'        => ! empty( $input['include_pages'] ) ? 1 : 0,
			'include_posts'        => ! empty( $input['include_posts'] ) ? 1 : 0,
			'include_products'     => ! empty( $input['include_products'] ) ? 1 : 0,
			'show_to_roles'        => isset( $input['show_to_roles'] ) ? (array) $input['show_to_roles'] : [],
			'show_on_pages'        => isset( $input['show_on_pages'] ) ? (array) $input['show_on_pages'] : [],
			'logging_enabled'      => ! empty( $input['logging_enabled'] ) ? 1 : 0,
			'rate_limit_enabled'   => ! empty( $input['rate_limit_enabled'] ) ? 1 : 0,
			'rate_limit_requests'  => isset( $input['rate_limit_requests'] ) ? absint( $input['rate_limit_requests'] ) : 10,
			'rate_limit_period'    => isset( $input['rate_limit_period'] ) ? absint( $input['rate_limit_period'] ) : 3600,
			'encryption_enabled'   => ! empty( $input['encryption_enabled'] ) ? 1 : 0,
			'gdpr_compliant'       => ! empty( $input['gdpr_compliant'] ) ? 1 : 0,
			'delete_conversations' => isset( $input['delete_conversations'] ) ? absint( $input['delete_conversations'] ) : 30,
		];
	}

	public function sanitize_provider_settings( $input ) {
		if ( ! is_array( $input ) ) {
			$input = [];
		}

		return [
			'provider'           => isset( $input['provider'] ) ? sanitize_text_field( $input['provider'] ) : 'wordpress-ai',
			'model'              => isset( $input['model'] ) ? sanitize_text_field( $input['model'] ) : 'claude-3-5-sonnet-20241022',
			'anthropic_api_url'  => isset( $input['anthropic_api_url'] ) ? esc_url_raw( $input['anthropic_api_url'] ) : 'https://api.anthropic.com/v1/messages',
			'openai_api_url'     => isset( $input['openai_api_url'] ) ? esc_url_raw( $input['openai_api_url'] ) : 'https://api.openai.com/v1/chat/completions',
			'ollama_api_url'     => isset( $input['ollama_api_url'] ) ? esc_url_raw( $input['ollama_api_url'] ) : 'http://localhost:11434',
			'timeout'            => isset( $input['timeout'] ) ? absint( $input['timeout'] ) : 30,
			'verify_ssl'         => ! empty( $input['verify_ssl'] ) ? 1 : 0,
		];
	}

	public function sanitize_api_keys( $input ) {
		if ( ! is_array( $input ) ) {
			$input = [];
		}

		$sanitized = [];
		foreach ( $input as $key => $value ) {
			if ( ! empty( $value ) ) {
				$sanitized[ sanitize_key( $key ) ] = $this->security->encrypt_value( sanitize_text_field( $value ) );
			}
		}
		return $sanitized;
	}

	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$settings = get_option( 'v7_ai_chatbot_settings', $this->get_defaults() );
		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		?>
		<div class="wrap v7-ai-chatbot-admin">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php settings_errors( 'v7_ai_chatbot_settings' ); ?>

			<nav class="nav-tab-wrapper">
				<a href="#general" class="nav-tab nav-tab-active"><?php esc_html_e( 'General', V7_AI_CHATBOT_TEXTDOMAIN ); ?></a>
				<a href="#api" class="nav-tab"><?php esc_html_e( 'API Configuration', V7_AI_CHATBOT_TEXTDOMAIN ); ?></a>
				<a href="#advanced" class="nav-tab"><?php esc_html_e( 'Advanced Settings', V7_AI_CHATBOT_TEXTDOMAIN ); ?></a>
				<a href="#appearance" class="nav-tab"><?php esc_html_e( 'Appearance', V7_AI_CHATBOT_TEXTDOMAIN ); ?></a>
				<a href="#security" class="nav-tab"><?php esc_html_e( 'Security & Privacy', V7_AI_CHATBOT_TEXTDOMAIN ); ?></a>
			</nav>

			<form method="post" action="options.php">
				<?php settings_fields( 'v7_ai_chatbot_group' ); ?>

				<!-- General Settings Tab -->
				<div id="general" class="v7-ai-chatbot-tab-content" style="display: block;">
					<div class="v7-ai-chatbot-card">
						<h2><?php esc_html_e( 'General Settings', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Enable Chatbot', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[enabled]" value="1" <?php checked( $settings['enabled'], 1 ); ?>>
										<?php esc_html_e( 'Show chatbot on frontend', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Position', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<select name="v7_ai_chatbot_settings[position]">
										<option value="bottom-right" <?php selected( $settings['position'], 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="bottom-left" <?php selected( $settings['position'], 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="top-right" <?php selected( $settings['position'], 'top-right' ); ?>><?php esc_html_e( 'Top Right', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="top-left" <?php selected( $settings['position'], 'top-left' ); ?>><?php esc_html_e( 'Top Left', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Greeting Message', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="text" name="v7_ai_chatbot_settings[greeting]" value="<?php echo esc_attr( $settings['greeting'] ); ?>" class="regular-text">
									<p class="description"><?php esc_html_e( 'Message shown when chatbot first loads', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Input Placeholder', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="text" name="v7_ai_chatbot_settings[placeholder]" value="<?php echo esc_attr( $settings['placeholder'] ); ?>" class="regular-text">
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- API Configuration Tab -->
				<div id="api" class="v7-ai-chatbot-tab-content">
					<div class="v7-ai-chatbot-card">
						<h2><?php esc_html_e( 'API Provider Configuration', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
						<p class="description" style="margin-bottom: 15px;">🚀 <?php esc_html_e( 'Select from 8+ AI providers with comprehensive model support', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'AI Provider', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<select name="v7_ai_chatbot_provider_settings[provider]" id="v7-ai-provider">
										<option value="">-- <?php esc_html_e( 'Select Provider', V7_AI_CHATBOT_TEXTDOMAIN ); ?> --</option>
										<option value="wordpress-ai" <?php selected( $provider_settings['provider'] ?? '', 'wordpress-ai' ); ?>>📘 <?php esc_html_e( 'WordPress AI Client (Recommended)', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="anthropic" <?php selected( $provider_settings['provider'] ?? '', 'anthropic' ); ?>>🧠 <?php esc_html_e( 'Anthropic Claude', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="openai" <?php selected( $provider_settings['provider'] ?? '', 'openai' ); ?>>🤖 <?php esc_html_e( 'OpenAI GPT', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="google" <?php selected( $provider_settings['provider'] ?? '', 'google' ); ?>>✨ <?php esc_html_e( 'Google Gemini', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="xai" <?php selected( $provider_settings['provider'] ?? '', 'xai' ); ?>>⚡ <?php esc_html_e( 'xAI Grok', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="mistral" <?php selected( $provider_settings['provider'] ?? '', 'mistral' ); ?>>🚀 <?php esc_html_e( 'Mistral AI', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="cohere" <?php selected( $provider_settings['provider'] ?? '', 'cohere' ); ?>>🎯 <?php esc_html_e( 'Cohere', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="meta" <?php selected( $provider_settings['provider'] ?? '', 'meta' ); ?>>🦙 <?php esc_html_e( 'Meta Llama', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
										<option value="ollama" <?php selected( $provider_settings['provider'] ?? '', 'ollama' ); ?>>🏠 <?php esc_html_e( 'Ollama (Self-Hosted)', V7_AI_CHATBOT_TEXTDOMAIN ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( '8 major AI providers with 50+ models available', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Model', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<select name="v7_ai_chatbot_provider_settings[model]" id="v7-ai-model">
										<option value="">-- <?php esc_html_e( 'Select a provider first', V7_AI_CHATBOT_TEXTDOMAIN ); ?> --</option>
										<?php
										$current_provider = $provider_settings['provider'] ?? '';
										$current_model = $provider_settings['model'] ?? '';
										if ( $current_provider ) {
											$models = V7_AI_Chatbot_Provider_Models::get_models( $current_provider );
											foreach ( $models as $model ) {
												$selected = selected( $current_model, $model['id'], false );
												$recommended = $model['recommended'] ? ' ⭐ (Recommended)' : '';
												echo '<option value="' . esc_attr( $model['id'] ) . '"' . $selected . '>' . esc_html( $model['name'] ) . esc_html( $recommended ) . '</option>';
											}
										}
										?>
									</select>
									<p class="description"><?php esc_html_e( 'Models dynamically populated based on selected provider', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Anthropic API Key', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="password" name="v7_ai_chatbot_api_keys[anthropic]" value="" placeholder="<?php esc_attr_e( 'sk-ant-...', V7_AI_CHATBOT_TEXTDOMAIN ); ?>" class="regular-text">
									<p class="description">🧠 <?php echo wp_kses_post( __( 'Get your key from <a href="https://console.anthropic.com/" target="_blank" rel="noopener">Anthropic Console</a> - Required for Claude models', V7_AI_CHATBOT_TEXTDOMAIN ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'OpenAI API Key', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="password" name="v7_ai_chatbot_api_keys[openai]" value="" placeholder="<?php esc_attr_e( 'sk-...', V7_AI_CHATBOT_TEXTDOMAIN ); ?>" class="regular-text">
									<p class="description">🤖 <?php echo wp_kses_post( __( 'Get your key from <a href="https://platform.openai.com/account/api-keys" target="_blank" rel="noopener">OpenAI Platform</a> - Required for GPT models', V7_AI_CHATBOT_TEXTDOMAIN ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Google Gemini API Key', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="password" name="v7_ai_chatbot_api_keys[google]" value="" placeholder="<?php esc_attr_e( 'Your Google API key', V7_AI_CHATBOT_TEXTDOMAIN ); ?>" class="regular-text">
									<p class="description">✨ <?php echo wp_kses_post( __( 'Get your key from <a href="https://ai.google.dev/" target="_blank" rel="noopener">Google AI Studio</a> - Required for Gemini models', V7_AI_CHATBOT_TEXTDOMAIN ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'xAI Grok API Key', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="password" name="v7_ai_chatbot_api_keys[xai]" value="" placeholder="<?php esc_attr_e( 'Your Grok API key', V7_AI_CHATBOT_TEXTDOMAIN ); ?>" class="regular-text">
									<p class="description">⚡ <?php echo wp_kses_post( __( 'Get your key from <a href="https://console.x.ai/" target="_blank" rel="noopener">xAI Console</a> - Required for Grok models', V7_AI_CHATBOT_TEXTDOMAIN ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Mistral API Key', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="password" name="v7_ai_chatbot_api_keys[mistral]" value="" placeholder="<?php esc_attr_e( 'Your Mistral API key', V7_AI_CHATBOT_TEXTDOMAIN ); ?>" class="regular-text">
									<p class="description">🚀 <?php echo wp_kses_post( __( 'Get your key from <a href="https://console.mistral.ai/" target="_blank" rel="noopener">Mistral Console</a> - Required for Mistral models', V7_AI_CHATBOT_TEXTDOMAIN ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Cohere API Key', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="password" name="v7_ai_chatbot_api_keys[cohere]" value="" placeholder="<?php esc_attr_e( 'Your Cohere API key', V7_AI_CHATBOT_TEXTDOMAIN ); ?>" class="regular-text">
									<p class="description">🎯 <?php echo wp_kses_post( __( 'Get your key from <a href="https://dashboard.cohere.com/" target="_blank" rel="noopener">Cohere Dashboard</a> - Required for Cohere models', V7_AI_CHATBOT_TEXTDOMAIN ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Meta Llama Provider Key', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="password" name="v7_ai_chatbot_api_keys[meta]" value="" placeholder="<?php esc_attr_e( 'API key for Llama via Together.ai or Replicate', V7_AI_CHATBOT_TEXTDOMAIN ); ?>" class="regular-text">
									<p class="description">🦙 <?php echo wp_kses_post( __( 'Use <a href="https://together.ai/" target="_blank" rel="noopener">Together.ai</a> or <a href="https://replicate.com/" target="_blank" rel="noopener">Replicate</a> to access Llama models', V7_AI_CHATBOT_TEXTDOMAIN ) ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Ollama Self-Hosted URL', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="url" name="v7_ai_chatbot_provider_settings[ollama_api_url]" value="<?php echo esc_attr( $provider_settings['ollama_api_url'] ?? 'http://localhost:11434' ); ?>" class="regular-text" placeholder="http://localhost:11434">
									<p class="description">🏠 <?php esc_html_e( 'For self-hosted Ollama instance - free and runs locally', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
						</table>
					</div>

					<div class="v7-ai-chatbot-card">
						<h2><?php esc_html_e( 'Advanced Provider Settings', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Request Timeout', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="number" name="v7_ai_chatbot_provider_settings[timeout]" value="<?php echo esc_attr( $provider_settings['timeout'] ?? 30 ); ?>" min="5" max="120"> <?php esc_html_e( 'seconds', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									<p class="description"><?php esc_html_e( 'Maximum time to wait for API responses (5-120 seconds)', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'SSL Certificate Verification', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_provider_settings[verify_ssl]" value="1" <?php checked( $provider_settings['verify_ssl'] ?? 1, 1 ); ?>>
										<?php esc_html_e( 'Verify SSL/TLS certificates', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label>
									<p class="description">🔒 <?php esc_html_e( '✓ Enabled recommended for production (security)', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Connection Test', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<button type="button" class="button button-secondary v7-test-api-btn"><?php esc_html_e( 'Test Selected Provider', V7_AI_CHATBOT_TEXTDOMAIN ); ?></button>
									<p class="description"><?php esc_html_e( 'Verify your API configuration before deployment', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- Advanced Settings Tab -->
				<div id="advanced" class="v7-ai-chatbot-tab-content">
					<div class="v7-ai-chatbot-card">
						<h2><?php esc_html_e( 'Generation Settings', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Max Tokens', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="number" name="v7_ai_chatbot_settings[max_tokens]" value="<?php echo esc_attr( $settings['max_tokens'] ); ?>" min="50" max="4096">
									<p class="description"><?php esc_html_e( 'Maximum response length (higher = longer responses)', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Temperature', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="number" name="v7_ai_chatbot_settings[temperature]" value="<?php echo esc_attr( $settings['temperature'] ); ?>" min="0" max="2" step="0.1">
									<p class="description"><?php esc_html_e( '0-2: Lower = focused, Higher = creative', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
						</table>
					</div>

					<div class="v7-ai-chatbot-card">
						<h2><?php esc_html_e( 'Content Sources', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Include Content', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[include_pages]" value="1" <?php checked( $settings['include_pages'], 1 ); ?>>
										<?php esc_html_e( 'Pages', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label><br>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[include_posts]" value="1" <?php checked( $settings['include_posts'], 1 ); ?>>
										<?php esc_html_e( 'Posts', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label><br>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[include_products]" value="1" <?php checked( $settings['include_products'], 1 ); ?>>
										<?php esc_html_e( 'Products (WooCommerce)', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Select content types to include in AI context', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- Appearance Tab -->
				<div id="appearance" class="v7-ai-chatbot-tab-content">
					<div class="v7-ai-chatbot-card">
						<h2><?php esc_html_e( 'Appearance', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Primary Color', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="color" name="v7_ai_chatbot_settings[primary_color]" value="<?php echo esc_attr( $settings['primary_color'] ); ?>">
									<p class="description"><?php esc_html_e( 'Main UI color', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Chat Bubble Color', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="color" name="v7_ai_chatbot_settings[bubble_color]" value="<?php echo esc_attr( $settings['bubble_color'] ); ?>">
									<p class="description"><?php esc_html_e( 'AI response bubble color', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Text Color', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="color" name="v7_ai_chatbot_settings[text_color]" value="<?php echo esc_attr( $settings['text_color'] ); ?>">
									<p class="description"><?php esc_html_e( 'Text color in bubbles', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- Security & Privacy Tab -->
				<div id="security" class="v7-ai-chatbot-tab-content">
					<div class="v7-ai-chatbot-card">
						<h2><?php esc_html_e( 'Security & Privacy', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Enable Logging', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[logging_enabled]" value="1" <?php checked( $settings['logging_enabled'], 1 ); ?>>
										<?php esc_html_e( 'Log conversations for analytics and debugging', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Required for analytics dashboard', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Enable Encryption', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[encryption_enabled]" value="1" <?php checked( $settings['encryption_enabled'], 1 ); ?>>
										<?php esc_html_e( 'Encrypt sensitive data at rest', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Uses WordPress encryption methods', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Enable Rate Limiting', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[rate_limit_enabled]" value="1" <?php checked( $settings['rate_limit_enabled'], 1 ); ?>>
										<?php esc_html_e( 'Limit queries to prevent abuse', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Recommended for production sites', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Rate Limit Settings', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="number" name="v7_ai_chatbot_settings[rate_limit_requests]" value="<?php echo esc_attr( $settings['rate_limit_requests'] ); ?>" min="1"> <?php esc_html_e( 'requests per', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									<input type="number" name="v7_ai_chatbot_settings[rate_limit_period]" value="<?php echo esc_attr( $settings['rate_limit_period'] ); ?>" min="60"> <?php esc_html_e( 'seconds', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									<p class="description"><?php esc_html_e( 'Example: 10 requests per 3600 seconds (1 hour)', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'GDPR Compliance', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="v7_ai_chatbot_settings[gdpr_compliant]" value="1" <?php checked( $settings['gdpr_compliant'], 1 ); ?>>
										<?php esc_html_e( 'Enable GDPR-compliant data handling', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Auto-deletes old conversations and provides data export', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Auto-Delete Conversations', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
								<td>
									<input type="number" name="v7_ai_chatbot_settings[delete_conversations]" value="<?php echo esc_attr( $settings['delete_conversations'] ); ?>" min="1"> <?php esc_html_e( 'days', V7_AI_CHATBOT_TEXTDOMAIN ); ?>
									<p class="description"><?php esc_html_e( 'Conversations older than this will be deleted', V7_AI_CHATBOT_TEXTDOMAIN ); ?></p>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<?php submit_button(); ?>
			</form>

			<div class="v7-ai-chatbot-card" style="margin-top: 30px;">
				<h2><?php esc_html_e( 'Quick Stats', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
				<div class="v7-ai-chatbot-stats-grid">
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( $this->get_pages_count() ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Pages', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( $this->get_posts_count() ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Posts', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( $this->get_products_count() ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Products', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( $this->analytics->get_total_conversations() ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Conversations', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
				</div>
			</div>
		</div>

		<script>
		document.querySelectorAll('.nav-tab').forEach(tab => {
			tab.addEventListener('click', function(e) {
				e.preventDefault();
				const target = this.getAttribute('href');
				document.querySelectorAll('.v7-ai-chatbot-tab-content').forEach(el => el.style.display = 'none');
				document.querySelector(target).style.display = 'block';
				document.querySelectorAll('.nav-tab').forEach(el => el.classList.remove('nav-tab-active'));
				this.classList.add('nav-tab-active');
			});
		});
		</script>
		<?php
	}

	public function analytics_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}
		?>
		<div class="wrap v7-ai-chatbot-admin">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="v7-ai-chatbot-card">
				<h2><?php esc_html_e( 'Conversation Analytics', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
				<div class="v7-ai-chatbot-stats-grid">
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( $this->analytics->get_total_conversations() ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Total Conversations', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( $this->analytics->get_total_messages() ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Messages Processed', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( $this->analytics->get_avg_messages_per_conversation() ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Avg. Messages/Chat', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
					<div class="v7-ai-chatbot-stat">
						<div class="stat-number"><?php echo esc_html( number_format( $this->analytics->get_avg_response_time(), 2 ) ); ?>ms</div>
						<div class="stat-label"><?php esc_html_e( 'Avg. Response Time', V7_AI_CHATBOT_TEXTDOMAIN ); ?></div>
					</div>
				</div>
			</div>
			<div class="v7-ai-chatbot-card">
				<h2><?php esc_html_e( 'Recent Conversations', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
				<?php $this->display_conversations_table( 10 ); ?>
			</div>
		</div>
		<?php
	}

	public function conversations_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}
		?>
		<div class="wrap v7-ai-chatbot-admin">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="v7-ai-chatbot-card">
				<h2><?php esc_html_e( 'All Conversations', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
				<?php $this->display_conversations_table( 50 ); ?>
			</div>
		</div>
		<?php
	}

	public function documentation_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}
		?>
		<div class="wrap v7-ai-chatbot-admin">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="v7-ai-chatbot-card">
				<h2><?php esc_html_e( 'Setup Guide', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
				<ol>
					<li><?php esc_html_e( 'Go to Settings tab and enable the chatbot', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><?php esc_html_e( 'Configure API provider in API Configuration tab', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><?php esc_html_e( 'Set up your API keys for chosen provider', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><?php esc_html_e( 'Configure content sources in Advanced Settings', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><?php esc_html_e( 'Customize appearance and security settings', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><?php esc_html_e( 'Save changes and test on frontend', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
				</ol>
			</div>
			<div class="v7-ai-chatbot-card">
				<h2><?php esc_html_e( 'Supported Providers', V7_AI_CHATBOT_TEXTDOMAIN ); ?></h2>
				<ul>
					<li><strong>WordPress AI Client:</strong> <?php esc_html_e( 'Uses WordPress native AI integration', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><strong>Anthropic Claude:</strong> <?php esc_html_e( 'State-of-the-art reasoning model', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><strong>OpenAI GPT:</strong> <?php esc_html_e( 'Powerful and reliable models', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
					<li><strong>Ollama:</strong> <?php esc_html_e( 'Self-hosted local models', V7_AI_CHATBOT_TEXTDOMAIN ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}

	private function display_conversations_table( $limit = 10 ) {
		$conversations = $this->analytics->get_recent_conversations( $limit );
		?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
					<th><?php esc_html_e( 'User IP', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
					<th><?php esc_html_e( 'Messages', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
					<th><?php esc_html_e( 'Duration', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
					<th><?php esc_html_e( 'Start Time', V7_AI_CHATBOT_TEXTDOMAIN ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $conversations ) ) : ?>
					<tr>
						<td colspan="5"><?php esc_html_e( 'No conversations yet', V7_AI_CHATBOT_TEXTDOMAIN ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $conversations as $conv ) : ?>
						<tr>
							<td><?php echo esc_html( $conv->id ); ?></td>
							<td><?php echo esc_html( $this->security->mask_ip( $conv->user_ip ) ); ?></td>
							<td><?php echo esc_html( $conv->message_count ); ?></td>
							<td><?php echo esc_html( $this->format_duration( strtotime( $conv->updated_at ) - strtotime( $conv->created_at ) ) ); ?></td>
							<td><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $conv->created_at ) ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	private function format_duration( $seconds ) {
		if ( $seconds < 60 ) {
			return $seconds . 's';
		} elseif ( $seconds < 3600 ) {
			return floor( $seconds / 60 ) . 'm ' . ( $seconds % 60 ) . 's';
		} else {
			return floor( $seconds / 3600 ) . 'h';
		}
	}

	private function get_defaults() {
		return [
			'enabled'              => 0,
			'position'             => 'bottom-right',
			'primary_color'        => '#0073aa',
			'bubble_color'         => '#0073aa',
			'text_color'           => '#ffffff',
			'greeting'             => esc_html__( 'Hi! How can I help you today?', V7_AI_CHATBOT_TEXTDOMAIN ),
			'placeholder'          => esc_html__( 'Type your message...', V7_AI_CHATBOT_TEXTDOMAIN ),
			'max_tokens'           => 500,
			'temperature'          => 0.7,
			'include_pages'        => 1,
			'include_posts'        => 1,
			'include_products'     => 0,
			'show_to_roles'        => [],
			'show_on_pages'        => [],
			'logging_enabled'      => 1,
			'rate_limit_enabled'   => 1,
			'rate_limit_requests'  => 10,
			'rate_limit_period'    => 3600,
			'encryption_enabled'   => 1,
			'gdpr_compliant'       => 1,
			'delete_conversations' => 30,
		];
	}

	private function get_pages_count() {
		return wp_count_posts( 'page' )->publish;
	}

	private function get_posts_count() {
		return wp_count_posts( 'post' )->publish;
	}

	private function get_products_count() {
		if ( class_exists( 'WooCommerce' ) ) {
			return wp_count_posts( 'product' )->publish;
		}
		return 0;
	}

	public function enqueue_admin( $hook ) {
		if ( ! str_contains( $hook, 'v7-ai-chatbot' ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'v7-ai-chatbot-admin', V7_AI_CHATBOT_URL . 'assets/css/admin.css', [], V7_AI_CHATBOT_VERSION );
		wp_enqueue_script( 'v7-ai-chatbot-provider-models', V7_AI_CHATBOT_URL . 'assets/js/provider-models.js', [], V7_AI_CHATBOT_VERSION, true );
		wp_enqueue_script( 'v7-ai-chatbot-admin', V7_AI_CHATBOT_URL . 'assets/js/admin.js', [ 'jquery', 'v7-ai-chatbot-provider-models' ], V7_AI_CHATBOT_VERSION, true );
	}

	public function enqueue_frontend() {
		$settings = get_option( 'v7_ai_chatbot_settings', $this->get_defaults() );
		if ( empty( $settings['enabled'] ) ) {
			return;
		}

		wp_enqueue_style( 'v7-ai-chatbot', V7_AI_CHATBOT_URL . 'assets/css/chatbot.css', [], V7_AI_CHATBOT_VERSION );
		wp_enqueue_script( 'v7-ai-chatbot', V7_AI_CHATBOT_URL . 'assets/js/chatbot.js', [ 'jquery' ], V7_AI_CHATBOT_VERSION, true );

		wp_localize_script( 'v7-ai-chatbot', 'v7AiChatbotParams', [
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'v7_ai_chatbot_nonce' ),
			'settings' => [
				'greeting'     => $settings['greeting'],
				'placeholder'  => $settings['placeholder'],
				'position'     => $settings['position'],
				'primaryColor' => $settings['primary_color'],
				'bubbleColor'  => $settings['bubble_color'],
				'textColor'    => $settings['text_color'],
			],
		] );

		$custom_css = ":root{--v7-ai-chatbot-primary:{$settings['primary_color']};--v7-ai-chatbot-bubble:{$settings['bubble_color']};--v7-ai-chatbot-text:{$settings['text_color']}}";
		wp_add_inline_style( 'v7-ai-chatbot', $custom_css );
	}

	public function render_chatbot() {
		$settings = get_option( 'v7_ai_chatbot_settings', $this->get_defaults() );
		if ( empty( $settings['enabled'] ) ) {
			return;
		}

		include V7_AI_CHATBOT_PATH . 'templates/chatbot.php';
	}

	public function handle_query() {
		check_ajax_referer( 'v7_ai_chatbot_nonce', 'nonce' );

		$message = isset( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';
		$conversation_id = isset( $_POST['conversation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['conversation_id'] ) ) : '';

		if ( empty( $message ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please enter a message', V7_AI_CHATBOT_TEXTDOMAIN ) ] );
		}

		$settings = get_option( 'v7_ai_chatbot_settings', $this->get_defaults() );

		// Check rate limiting
		if ( ! empty( $settings['rate_limit_enabled'] ) ) {
			if ( ! $this->security->check_rate_limit( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', $settings['rate_limit_requests'], $settings['rate_limit_period'] ) ) {
				wp_send_json_error( [ 'message' => esc_html__( 'Too many requests. Please wait before sending another message.', V7_AI_CHATBOT_TEXTDOMAIN ) ] );
			}
		}

		$context = $this->get_site_context( $settings );
		$response = $this->query_ai( $message, $context, $settings );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( [ 'message' => $response->get_error_message() ] );
		}

		// Log conversation if enabled
		if ( ! empty( $settings['logging_enabled'] ) ) {
			$this->analytics->log_message( $conversation_id, $message, $response );
		}

		wp_send_json_success( [
			'message'           => $response,
			'conversation_id'   => $conversation_id ?: uniqid( 'conv_' ),
		] );
	}

	public function handle_settings_ajax() {
		check_ajax_referer( 'v7_ai_chatbot_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized', V7_AI_CHATBOT_TEXTDOMAIN ) ] );
		}

		$action = isset( $_POST['action_type'] ) ? sanitize_text_field( wp_unslash( $_POST['action_type'] ) ) : '';

		switch ( $action ) {
			case 'test_api':
				$result = $this->test_api_connection();
				wp_send_json( $result );
				break;
			case 'export_data':
				$this->export_user_data();
				break;
			default:
				wp_send_json_error( [ 'message' => esc_html__( 'Invalid action', V7_AI_CHATBOT_TEXTDOMAIN ) ] );
		}
	}

	private function test_api_connection() {
		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$provider = $provider_settings['provider'] ?? 'wordpress-ai';

		$test_result = $this->ai_provider->test_connection( $provider );

		if ( is_wp_error( $test_result ) ) {
			return [
				'success' => false,
				'message' => $test_result->get_error_message(),
			];
		}

		return [
			'success' => true,
			'message' => esc_html__( 'API connection successful!', V7_AI_CHATBOT_TEXTDOMAIN ),
		];
	}

	private function export_user_data() {
		$user_ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		$conversations = $this->db->get_conversations_by_ip( $user_ip );

		$export_data = [];
		foreach ( $conversations as $conv ) {
			$export_data[] = [
				'conversation_id' => $conv->id,
				'messages'        => $this->db->get_messages( $conv->id ),
				'created_at'      => $conv->created_at,
			];
		}

		header( 'Content-Type: application/json' );
		header( 'Content-Disposition: attachment; filename="chatbot-data-' . gmdate( 'Y-m-d' ) . '.json"' );
		echo wp_json_encode( $export_data );
		exit;
	}

	private function get_site_context( $settings ) {
		$context = "Site: " . get_bloginfo( 'name' ) . "\nURL: " . home_url() . "\nDescription: " . get_bloginfo( 'description' ) . "\n\n";
		$context .= "IMPORTANT: Only answer questions about this website and its content. If asked about anything else, politely decline and redirect to site-related topics.\n\n";
		$context .= "Available Content:\n";

		if ( ! empty( $settings['include_pages'] ) ) {
			$pages = get_posts( [ 'post_type' => 'page', 'posts_per_page' => 50, 'post_status' => 'publish' ] );
			$context .= "Pages:\n";
			foreach ( $pages as $page ) {
				$context .= "- " . $page->post_title . ": " . wp_trim_words( $page->post_content, 50 ) . "\n";
			}
		}

		if ( ! empty( $settings['include_posts'] ) ) {
			$posts = get_posts( [ 'post_type' => 'post', 'posts_per_page' => 50, 'post_status' => 'publish' ] );
			$context .= "Blog Posts:\n";
			foreach ( $posts as $post ) {
				$context .= "- " . $post->post_title . ": " . wp_trim_words( $post->post_content, 50 ) . "\n";
			}
		}

		if ( ! empty( $settings['include_products'] ) && class_exists( 'WooCommerce' ) ) {
			$products = get_posts( [ 'post_type' => 'product', 'posts_per_page' => 50, 'post_status' => 'publish' ] );
			$context .= "Products:\n";
			foreach ( $products as $product ) {
				$context .= "- " . $product->post_title . ": " . wp_trim_words( $product->post_content, 30 ) . "\n";
			}
		}

		return $context;
	}

	private function query_ai( $message, $context, $settings ) {
		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$provider = $provider_settings['provider'] ?? 'wordpress-ai';

		return $this->ai_provider->query( $provider, $message, $context, $settings );
	}
}

function v7_ai_chatbot_redirect() {
	if ( get_option( 'v7_ai_chatbot_redirect' ) ) {
		delete_option( 'v7_ai_chatbot_redirect' );
		$activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_VALIDATE_BOOLEAN );
		if ( ! $activate_multi ) {
			wp_safe_redirect( admin_url( 'admin.php?page=v7-ai-chatbot' ) );
			exit;
		}
	}
}
add_action( 'admin_init', 'v7_ai_chatbot_redirect' );

V7_AI_Chatbot::get_instance();
