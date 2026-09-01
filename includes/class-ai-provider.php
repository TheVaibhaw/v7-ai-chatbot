<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_AI_Chatbot_AI_Provider {
	private $security;

	public function __construct() {
		require_once V7_AI_CHATBOT_PATH . 'includes/class-security.php';
		$this->security = new V7_AI_Chatbot_Security();
	}

	public function query( $provider, $message, $context, $settings ) {
		switch ( $provider ) {
			case 'wordpress-ai':
				return $this->query_wordpress_ai( $message, $context, $settings );
			case 'anthropic':
				return $this->query_anthropic( $message, $context, $settings );
			case 'openai':
				return $this->query_openai( $message, $context, $settings );
			case 'ollama':
				return $this->query_ollama( $message, $context, $settings );
			default:
				return new WP_Error( 'unknown_provider', esc_html__( 'Unknown AI provider', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}
	}

	public function test_connection( $provider ) {
		switch ( $provider ) {
			case 'wordpress-ai':
				if ( function_exists( 'wp_supports_ai' ) && wp_supports_ai() ) {
					return true;
				}
				return new WP_Error( 'wordpress_ai_unavailable', esc_html__( 'WordPress AI Client is not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
			case 'anthropic':
				return $this->test_anthropic_connection();
			case 'openai':
				return $this->test_openai_connection();
			case 'ollama':
				return $this->test_ollama_connection();
			default:
				return new WP_Error( 'unknown_provider', esc_html__( 'Unknown provider', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}
	}

	private function query_wordpress_ai( $message, $context, $settings ) {
		if ( ! function_exists( 'wp_ai_client_prompt' ) || ! wp_supports_ai() ) {
			return new WP_Error( 'ai_unavailable', esc_html__( 'AI features are not available. Please ask your site administrator to configure an AI provider in WordPress.', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		try {
			$result = wp_ai_client_prompt( $message )
				->using_system_instruction( $context )
				->using_max_tokens( intval( $settings['max_tokens'] ) )
				->using_temperature( floatval( $settings['temperature'] ) )
				->generate_text();

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return wp_kses_post( $result );
		} catch ( Exception $e ) {
			return new WP_Error( 'query_failed', $e->getMessage() );
		}
	}

	private function query_anthropic( $message, $context, $settings ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['anthropic'] ) ? $this->security->decrypt_value( $api_keys['anthropic'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', esc_html__( 'Anthropic API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$model = $provider_settings['model'] ?? 'claude-3-5-sonnet-20241022';

		$body = [
			'model'       => $model,
			'system'      => $context,
			'max_tokens'  => intval( $settings['max_tokens'] ),
			'temperature' => floatval( $settings['temperature'] ),
			'messages'    => [
				[
					'role'    => 'user',
					'content' => $message,
				],
			],
		];

		$response = wp_remote_post(
			'https://api.anthropic.com/v1/messages',
			[
				'headers'     => [
					'Content-Type'  => 'application/json',
					'x-api-key'     => $api_key,
					'anthropic-version' => '2023-06-01',
				],
				'body'        => wp_json_encode( $body ),
				'timeout'     => $provider_settings['timeout'] ?? 30,
				'sslverify'   => $provider_settings['verify_ssl'] ?? true,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			$error_body = json_decode( wp_remote_retrieve_body( $response ), true );
			return new WP_Error( 'api_error', $error_body['error']['message'] ?? esc_html__( 'Anthropic API error', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['content'][0]['text'] ) ) {
			return new WP_Error( 'invalid_response', esc_html__( 'Invalid response from Anthropic API', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $body['content'][0]['text'] );
	}

	private function query_openai( $message, $context, $settings ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['openai'] ) ? $this->security->decrypt_value( $api_keys['openai'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', esc_html__( 'OpenAI API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$model = $provider_settings['model'] ?? 'gpt-4o-mini';

		$body = [
			'model'       => $model,
			'max_tokens'  => intval( $settings['max_tokens'] ),
			'temperature' => floatval( $settings['temperature'] ),
			'messages'    => [
				[
					'role'    => 'system',
					'content' => $context,
				],
				[
					'role'    => 'user',
					'content' => $message,
				],
			],
		];

		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			[
				'headers'   => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				],
				'body'      => wp_json_encode( $body ),
				'timeout'   => $provider_settings['timeout'] ?? 30,
				'sslverify' => $provider_settings['verify_ssl'] ?? true,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			$error_body = json_decode( wp_remote_retrieve_body( $response ), true );
			return new WP_Error( 'api_error', $error_body['error']['message'] ?? esc_html__( 'OpenAI API error', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['choices'][0]['message']['content'] ) ) {
			return new WP_Error( 'invalid_response', esc_html__( 'Invalid response from OpenAI API', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $body['choices'][0]['message']['content'] );
	}

	private function query_ollama( $message, $context, $settings ) {
		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$ollama_url = $provider_settings['ollama_api_url'] ?? 'http://localhost:11434';
		$model = $provider_settings['model'] ?? 'llama2';

		$body = [
			'model'       => $model,
			'prompt'      => $context . "\n\nUser: " . $message,
			'stream'      => false,
		];

		$response = wp_remote_post(
			$ollama_url . '/api/generate',
			[
				'headers'   => [
					'Content-Type' => 'application/json',
				],
				'body'      => wp_json_encode( $body ),
				'timeout'   => $provider_settings['timeout'] ?? 30,
				'sslverify' => false,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', esc_html__( 'Ollama API error', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['response'] ) ) {
			return new WP_Error( 'invalid_response', esc_html__( 'Invalid response from Ollama', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $body['response'] );
	}

	private function test_anthropic_connection() {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['anthropic'] ) ? $this->security->decrypt_value( $api_keys['anthropic'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', esc_html__( 'Anthropic API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$response = wp_remote_post(
			'https://api.anthropic.com/v1/messages',
			[
				'headers' => [
					'Content-Type'      => 'application/json',
					'x-api-key'         => $api_key,
					'anthropic-version' => '2023-06-01',
				],
				'body'    => wp_json_encode( [
					'model'      => 'claude-3-5-sonnet-20241022',
					'max_tokens' => 100,
					'messages'   => [
						[
							'role'    => 'user',
							'content' => 'Test',
						],
					],
				] ),
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			$error_body = json_decode( wp_remote_retrieve_body( $response ), true );
			return new WP_Error( 'api_error', $error_body['error']['message'] ?? esc_html__( 'Connection test failed', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return true;
	}

	private function test_openai_connection() {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['openai'] ) ? $this->security->decrypt_value( $api_keys['openai'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', esc_html__( 'OpenAI API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			[
				'headers' => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				],
				'body'    => wp_json_encode( [
					'model'       => 'gpt-4o-mini',
					'max_tokens'  => 100,
					'temperature' => 0.7,
					'messages'    => [
						[
							'role'    => 'user',
							'content' => 'Test',
						],
					],
				] ),
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			$error_body = json_decode( wp_remote_retrieve_body( $response ), true );
			return new WP_Error( 'api_error', $error_body['error']['message'] ?? esc_html__( 'Connection test failed', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return true;
	}

	private function test_ollama_connection() {
		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$ollama_url = $provider_settings['ollama_api_url'] ?? 'http://localhost:11434';

		$response = wp_remote_get(
			$ollama_url . '/api/tags',
			[
				'timeout'   => 10,
				'sslverify' => false,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', esc_html__( 'Cannot connect to Ollama instance', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return true;
	}
}
