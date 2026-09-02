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

	/**
	 * Turns a failed HTTP response into a specific, actionable error message
	 * instead of a generic "Connection test failed" - different providers
	 * shape their error JSON differently, so this checks the common shapes
	 * and always falls back to the HTTP status + a raw body snippet rather
	 * than a message with no diagnostic value.
	 */
	private function format_api_error( $response_code, $response_body ) {
		$decoded = json_decode( $response_body, true );

		if ( is_array( $decoded ) ) {
			if ( isset( $decoded['error']['message'] ) && is_string( $decoded['error']['message'] ) ) {
				$message = $decoded['error']['message'] . ' (HTTP ' . $response_code . ')';

				// A retired/unavailable model is the single most common cause
				// here, and the fix isn't obvious from the provider's wording.
				if ( false !== stripos( $decoded['error']['message'], 'model' ) && ( 404 === $response_code || 400 === $response_code ) ) {
					$message .= ' ' . __( 'Fix: go to AI Chatbot > API Configuration and click "Load models from my account", then pick a model from the refreshed list and save.', V7_AI_CHATBOT_TEXTDOMAIN );
				}

				return $message;
			}
			if ( isset( $decoded['error'] ) && is_string( $decoded['error'] ) ) {
				return $decoded['error'] . ' (HTTP ' . $response_code . ')';
			}
			if ( isset( $decoded['message'] ) && is_string( $decoded['message'] ) ) {
				return $decoded['message'] . ' (HTTP ' . $response_code . ')';
			}
		}

		$snippet = trim( wp_strip_all_tags( (string) $response_body ) );
		if ( '' !== $snippet ) {
			return sprintf(
				/* translators: 1: HTTP status code, 2: raw response snippet */
				__( 'Request failed with HTTP %1$d: %2$s', V7_AI_CHATBOT_TEXTDOMAIN ),
				$response_code,
				mb_substr( $snippet, 0, 200 )
			);
		}

		return sprintf(
			/* translators: %d: HTTP status code */
			__( 'Request failed with HTTP %d and no additional details from the provider.', V7_AI_CHATBOT_TEXTDOMAIN ),
			$response_code
		);
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
			case 'groq':
				return $this->query_openai_compatible( 'groq', 'https://api.groq.com/openai/v1/chat/completions', 'llama-3.1-8b-instant', $message, $context, $settings );
			case 'xai':
				return $this->query_openai_compatible( 'xai', 'https://api.x.ai/v1/chat/completions', 'grok-3', $message, $context, $settings );
			case 'mistral':
				return $this->query_openai_compatible( 'mistral', 'https://api.mistral.ai/v1/chat/completions', 'mistral-large-2', $message, $context, $settings );
			case 'meta':
				return $this->query_openai_compatible( 'meta', 'https://api.together.xyz/v1/chat/completions', 'meta-llama/Llama-3.1-8B-Instruct-Turbo', $message, $context, $settings );
			case 'google':
				return $this->query_google( $message, $context, $settings );
			case 'cohere':
				return $this->query_cohere( $message, $context, $settings );
			default:
				return new WP_Error( 'unknown_provider', __( 'Unknown AI provider', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}
	}

	/**
	 * Fetches the model IDs the configured API key can actually use, straight
	 * from the provider.
	 *
	 * A bundled model list goes stale the moment a provider retires or renames
	 * something, which surfaces to the user as an opaque "model does not exist"
	 * error. Asking the provider is the only way to be certain, so the settings
	 * screen uses this to populate the Model dropdown with real, usable IDs.
	 *
	 * @return array|WP_Error List of model ID strings, or WP_Error on failure.
	 */
	public function get_available_models( $provider ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key  = isset( $api_keys[ $provider ] ) ? $this->security->decrypt_value( $api_keys[ $provider ] ) : '';

		// provider => [ endpoint, auth header name, auth header value prefix ]
		//
		// Query args matter here: Anthropic's model list defaults to 20 items
		// per page, Google's to 50 and Cohere's to a small page too, so
		// without an explicit page size the newest models can be missing from
		// the dropdown. Cohere can also filter to chat-capable models server
		// side.
		$endpoints = [
			'openai'    => [ 'https://api.openai.com/v1/models', 'Authorization', 'Bearer ' ],
			'groq'      => [ 'https://api.groq.com/openai/v1/models', 'Authorization', 'Bearer ' ],
			'xai'       => [ 'https://api.x.ai/v1/models', 'Authorization', 'Bearer ' ],
			'mistral'   => [ 'https://api.mistral.ai/v1/models', 'Authorization', 'Bearer ' ],
			'meta'      => [ 'https://api.together.xyz/v1/models', 'Authorization', 'Bearer ' ],
			'anthropic' => [ 'https://api.anthropic.com/v1/models?limit=1000', 'x-api-key', '' ],
			'google'    => [ 'https://generativelanguage.googleapis.com/v1beta/models?pageSize=1000', 'x-goog-api-key', '' ],
			'cohere'    => [ 'https://api.cohere.com/v1/models?page_size=1000&endpoint=chat', 'Authorization', 'Bearer ' ],
		];

		if ( 'ollama' === $provider ) {
			$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
			$base = $provider_settings['ollama_api_url'] ?? 'http://localhost:11434';
			$response = wp_remote_get( trailingslashit( $base ) . 'api/tags', [ 'timeout' => 15, 'sslverify' => false ] );
		} elseif ( isset( $endpoints[ $provider ] ) ) {
			if ( empty( $api_key ) ) {
				return new WP_Error( 'missing_api_key', __( 'Save an API key for this provider first, then load its models.', V7_AI_CHATBOT_TEXTDOMAIN ) );
			}

			list( $url, $header_name, $header_prefix ) = $endpoints[ $provider ];
			$headers = [ $header_name => $header_prefix . $api_key ];
			if ( 'anthropic' === $provider ) {
				$headers['anthropic-version'] = '2023-06-01';
			}

			$response = wp_remote_get( $url, [ 'headers' => $headers, 'timeout' => 15 ] );
		} else {
			return new WP_Error( 'unsupported_provider', __( 'This provider does not support listing models.', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', $this->format_api_error( $response_code, wp_remote_retrieve_body( $response ) ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) ) {
			return new WP_Error( 'invalid_response', __( 'Could not read the model list returned by the provider.', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$models = $this->extract_model_ids( $body );

		if ( empty( $models ) ) {
			return new WP_Error( 'no_models', __( 'The provider returned no usable chat models for this API key.', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		sort( $models, SORT_NATURAL | SORT_FLAG_CASE );

		return $models;
	}

	/**
	 * Normalises the several different model-list formats the providers use.
	 *
	 * Shapes handled:
	 *  - OpenAI / Groq / xAI / Mistral / Anthropic: { data: [ { id } ] }
	 *  - Together.ai (Meta/Llama):                  [ { id, type } ]  <- bare array
	 *  - Google Gemini:  { models: [ { name: "models/x", supportedGenerationMethods } ] }
	 *  - Cohere:         { models: [ { name, endpoints } ] }
	 *  - Ollama:         { models: [ { name } ] }
	 *
	 * Where the provider tells us what a model can do, that is used to keep
	 * non-chat models (embeddings, speech, moderation) out of the list.
	 */
	private function extract_model_ids( $body ) {
		$models = [];

		// Together.ai answers with a bare top-level array rather than an
		// object, so handle that before looking for wrapper keys.
		$entries = null;
		if ( isset( $body['data'] ) && is_array( $body['data'] ) ) {
			$entries = $body['data'];
		} elseif ( isset( $body['models'] ) && is_array( $body['models'] ) ) {
			$entries = $body['models'];
		} elseif ( isset( $body[0] ) && is_array( $body[0] ) ) {
			$entries = $body;
		}

		if ( ! is_array( $entries ) ) {
			return [];
		}

		foreach ( $entries as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}

			$id = '';
			if ( ! empty( $entry['id'] ) && is_string( $entry['id'] ) ) {
				$id = $entry['id'];
			} elseif ( ! empty( $entry['name'] ) && is_string( $entry['name'] ) ) {
				$id = $entry['name'];
			}

			if ( '' === $id ) {
				continue;
			}

			// Google prefixes IDs with "models/"; generateContent doesn't want it.
			if ( 0 === strpos( $id, 'models/' ) ) {
				$id = substr( $id, strlen( 'models/' ) );
			}

			// Google: only models that can actually generate content.
			if ( isset( $entry['supportedGenerationMethods'] ) && is_array( $entry['supportedGenerationMethods'] )
				&& ! in_array( 'generateContent', $entry['supportedGenerationMethods'], true ) ) {
				continue;
			}

			// Cohere: only models exposing the chat endpoint.
			if ( isset( $entry['endpoints'] ) && is_array( $entry['endpoints'] )
				&& ! in_array( 'chat', $entry['endpoints'], true ) ) {
				continue;
			}

			// Together.ai labels model kinds; keep chat/language ones.
			if ( isset( $entry['type'] ) && is_string( $entry['type'] )
				&& ! in_array( strtolower( $entry['type'] ), [ 'chat', 'language', 'model' ], true ) ) {
				continue;
			}

			$models[] = $id;
		}

		return array_values( array_unique( array_filter( $models ) ) );
	}

	public function test_connection( $provider ) {
		switch ( $provider ) {
			case 'wordpress-ai':
				if ( function_exists( 'wp_supports_ai' ) && wp_supports_ai() ) {
					return true;
				}
				return new WP_Error( 'wordpress_ai_unavailable', __( 'WordPress AI Client is not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
			case 'anthropic':
				return $this->test_anthropic_connection();
			case 'openai':
				return $this->test_openai_connection();
			case 'ollama':
				return $this->test_ollama_connection();
			case 'groq':
				return $this->test_openai_compatible_connection( 'groq', 'https://api.groq.com/openai/v1/chat/completions', 'llama-3.1-8b-instant' );
			case 'xai':
				return $this->test_openai_compatible_connection( 'xai', 'https://api.x.ai/v1/chat/completions', 'grok-3' );
			case 'mistral':
				return $this->test_openai_compatible_connection( 'mistral', 'https://api.mistral.ai/v1/chat/completions', 'mistral-large-2' );
			case 'meta':
				return $this->test_openai_compatible_connection( 'meta', 'https://api.together.xyz/v1/chat/completions', 'meta-llama/Llama-3.1-8B-Instruct-Turbo' );
			case 'google':
				return $this->test_google_connection();
			case 'cohere':
				return $this->test_cohere_connection();
			default:
				return new WP_Error( 'unknown_provider', __( 'Unknown provider', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}
	}

	private function query_wordpress_ai( $message, $context, $settings ) {
		if ( ! function_exists( 'wp_ai_client_prompt' ) || ! wp_supports_ai() ) {
			return new WP_Error( 'ai_unavailable', __( 'AI features are not available. Please ask your site administrator to configure an AI provider in WordPress.', V7_AI_CHATBOT_TEXTDOMAIN ) );
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

	private function query_anthropic( $message, $context, $settings, $allow_model_recovery = true ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['anthropic'] ) ? $this->security->decrypt_value( $api_keys['anthropic'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Anthropic API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
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
			$raw_body = wp_remote_retrieve_body( $response );

			// Retired/unavailable model: switch to one this key can use
			// and retry once instead of failing the visitor's question.
			if ( $allow_model_recovery && '' !== $this->maybe_recover_model( 'anthropic', $model, 'claude-3-5-sonnet-20241022', $response_code, $raw_body ) ) {
				return $this->query_anthropic( $message, $context, $settings, false );
			}

			return new WP_Error( 'api_error', $this->format_api_error( $response_code, $raw_body ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['content'][0]['text'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from Anthropic API', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $body['content'][0]['text'] );
	}

	private function query_openai( $message, $context, $settings, $allow_model_recovery = true ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['openai'] ) ? $this->security->decrypt_value( $api_keys['openai'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'OpenAI API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
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
			$raw_body = wp_remote_retrieve_body( $response );

			// Retired/unavailable model: switch to one this key can use
			// and retry once instead of failing the visitor's question.
			if ( $allow_model_recovery && '' !== $this->maybe_recover_model( 'openai', $model, 'gpt-4o-mini', $response_code, $raw_body ) ) {
				return $this->query_openai( $message, $context, $settings, false );
			}

			return new WP_Error( 'api_error', $this->format_api_error( $response_code, $raw_body ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['choices'][0]['message']['content'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from OpenAI API', V7_AI_CHATBOT_TEXTDOMAIN ) );
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
			return new WP_Error( 'api_error', __( 'Ollama API error', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['response'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from Ollama', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $body['response'] );
	}

	private function test_anthropic_connection() {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['anthropic'] ) ? $this->security->decrypt_value( $api_keys['anthropic'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Anthropic API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$response = wp_remote_get(
			'https://api.anthropic.com/v1/models',
			[
				'headers' => [
					'x-api-key'         => $api_key,
					'anthropic-version' => '2023-06-01',
				],
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', $this->format_api_error( $response_code, wp_remote_retrieve_body( $response ) ) );
		}

		return true;
	}

	private function test_openai_connection() {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key = isset( $api_keys['openai'] ) ? $this->security->decrypt_value( $api_keys['openai'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'OpenAI API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$response = wp_remote_get(
			'https://api.openai.com/v1/models',
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $api_key,
				],
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', $this->format_api_error( $response_code, wp_remote_retrieve_body( $response ) ) );
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
			return new WP_Error( 'api_error', __( 'Cannot connect to Ollama instance', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return true;
	}

	/**
	 * True when a provider rejected the request because the model ID is gone
	 * or inaccessible - the signal we use to pick a working model instead.
	 */
	private function is_model_not_found( $response_code, $response_body ) {
		if ( ! in_array( (int) $response_code, [ 400, 403, 404 ], true ) ) {
			return false;
		}

		$decoded = json_decode( $response_body, true );
		$message = '';
		if ( is_array( $decoded ) ) {
			$message = $decoded['error']['message'] ?? ( is_string( $decoded['error'] ?? null ) ? $decoded['error'] : ( $decoded['message'] ?? '' ) );
		}
		if ( ! is_string( $message ) || '' === $message ) {
			$message = (string) $response_body;
		}

		if ( false === stripos( $message, 'model' ) ) {
			return false;
		}

		foreach ( [ 'does not exist', 'not found', 'do not have access', 'decommissioned', 'deprecated', 'unknown model', 'invalid model', 'no longer' ] as $needle ) {
			if ( false !== stripos( $message, $needle ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Chooses a sensible chat model out of a provider's live model list,
	 * skipping models that can't do chat completions at all (speech, embedding,
	 * moderation and similar), since those would fail just as hard.
	 */
	private function pick_usable_chat_model( array $models, $preferred_default = '' ) {
		$excluded = [ 'whisper', 'tts', 'embed', 'embedding', 'moderation', 'guard', 'rerank', 'dall-e', 'stable-diffusion', 'clip', 'audio', 'transcribe', 'speech', 'image' ];

		$candidates = array_values(
			array_filter(
				$models,
				static function ( $id ) use ( $excluded ) {
					foreach ( $excluded as $bad ) {
						if ( false !== stripos( $id, $bad ) ) {
							return false;
						}
					}
					return true;
				}
			)
		);

		if ( empty( $candidates ) ) {
			return '';
		}

		// The provider's own recommended default, when it's still offered.
		if ( '' !== $preferred_default && in_array( $preferred_default, $candidates, true ) ) {
			return $preferred_default;
		}

		// Otherwise favour something that clearly reads as an instruct/chat model.
		foreach ( [ 'instant', 'instruct', 'chat', 'turbo', 'flash', 'mini' ] as $hint ) {
			foreach ( $candidates as $id ) {
				if ( false !== stripos( $id, $hint ) ) {
					return $id;
				}
			}
		}

		return $candidates[0];
	}

	/**
	 * When a provider reports the configured model as retired, ask it what it
	 * does support, persist a working replacement and return it so the caller
	 * can retry. Returns '' when no recovery is possible or appropriate.
	 *
	 * Shared by every provider so a retired model never dead-ends a visitor.
	 */
	private function maybe_recover_model( $provider_key, $current_model, $default_model, $response_code, $raw_body ) {
		if ( ! $this->is_model_not_found( $response_code, $raw_body ) ) {
			return '';
		}

		$available = $this->get_available_models( $provider_key );
		if ( is_wp_error( $available ) ) {
			return '';
		}

		$replacement = $this->pick_usable_chat_model( $available, $default_model );
		if ( '' === $replacement || $replacement === $current_model ) {
			return '';
		}

		$provider_settings          = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$provider_settings['model'] = $replacement;
		update_option( 'v7_ai_chatbot_provider_settings', $provider_settings );

		// Surfaced to the admin as a notice - never a silent config change.
		set_transient(
			'v7_ai_chatbot_model_autoswitched',
			[
				'from' => $current_model,
				'to'   => $replacement,
			],
			WEEK_IN_SECONDS
		);

		return $replacement;
	}

	/**
	 * Groq, xAI, Mistral, and Together.ai (Meta/Llama) all expose an
	 * OpenAI-compatible chat completions endpoint, so they share this logic.
	 */
	private function query_openai_compatible( $provider_key, $endpoint, $default_model, $message, $context, $settings, $allow_model_recovery = true ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key  = isset( $api_keys[ $provider_key ] ) ? $this->security->decrypt_value( $api_keys[ $provider_key ] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'API key not configured for the selected provider', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$model = ! empty( $provider_settings['model'] ) ? $provider_settings['model'] : $default_model;

		$body = [
			'model'       => $model,
			'max_tokens'  => intval( $settings['max_tokens'] ),
			'temperature' => floatval( $settings['temperature'] ),
			'messages'    => [
				[ 'role' => 'system', 'content' => $context ],
				[ 'role' => 'user', 'content' => $message ],
			],
		];

		$response = wp_remote_post(
			$endpoint,
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
			$raw_body = wp_remote_retrieve_body( $response );

			// The configured model was retired or isn't available on this key.
			// Rather than dead-ending the visitor, switch to a working model
			// and retry once.
			if ( $allow_model_recovery && '' !== $this->maybe_recover_model( $provider_key, $model, $default_model, $response_code, $raw_body ) ) {
				return $this->query_openai_compatible( $provider_key, $endpoint, $default_model, $message, $context, $settings, false );
			}

			return new WP_Error( 'api_error', $this->format_api_error( $response_code, $raw_body ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['choices'][0]['message']['content'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from AI provider', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $body['choices'][0]['message']['content'] );
	}

	private function test_openai_compatible_connection( $provider_key, $endpoint, $default_model ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key  = isset( $api_keys[ $provider_key ] ) ? $this->security->decrypt_value( $api_keys[ $provider_key ] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'API key not configured for the selected provider', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$models_endpoint = str_replace( '/chat/completions', '/models', $endpoint );

		$response = wp_remote_get(
			$models_endpoint,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $api_key,
				],
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', $this->format_api_error( $response_code, wp_remote_retrieve_body( $response ) ) );
		}

		return true;
	}

	private function query_google( $message, $context, $settings, $allow_model_recovery = true ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key  = isset( $api_keys['google'] ) ? $this->security->decrypt_value( $api_keys['google'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Google Gemini API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$model = $provider_settings['model'] ?? 'gemini-2.0-flash';

		$response = wp_remote_post(
			'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode( $model ) . ':generateContent',
			[
				'headers'   => [
					'Content-Type'   => 'application/json',
					'x-goog-api-key' => $api_key,
				],
				'body'      => wp_json_encode( [
					'systemInstruction' => [ 'parts' => [ [ 'text' => $context ] ] ],
					'contents'          => [ [ 'role' => 'user', 'parts' => [ [ 'text' => $message ] ] ] ],
					'generationConfig'  => [
						'maxOutputTokens' => intval( $settings['max_tokens'] ),
						'temperature'     => floatval( $settings['temperature'] ),
					],
				] ),
				'timeout'   => $provider_settings['timeout'] ?? 30,
				'sslverify' => $provider_settings['verify_ssl'] ?? true,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			$raw_body = wp_remote_retrieve_body( $response );

			// Retired/unavailable model: switch to one this key can use
			// and retry once instead of failing the visitor's question.
			if ( $allow_model_recovery && '' !== $this->maybe_recover_model( 'google', $model, 'gemini-2.0-flash', $response_code, $raw_body ) ) {
				return $this->query_google( $message, $context, $settings, false );
			}

			return new WP_Error( 'api_error', $this->format_api_error( $response_code, $raw_body ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

		if ( null === $text ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from Google Gemini API', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $text );
	}

	private function test_google_connection() {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key  = isset( $api_keys['google'] ) ? $this->security->decrypt_value( $api_keys['google'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Google Gemini API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$response = wp_remote_get(
			'https://generativelanguage.googleapis.com/v1beta/models',
			[
				'headers' => [
					'x-goog-api-key' => $api_key,
				],
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', $this->format_api_error( $response_code, wp_remote_retrieve_body( $response ) ) );
		}

		return true;
	}

	private function query_cohere( $message, $context, $settings, $allow_model_recovery = true ) {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key  = isset( $api_keys['cohere'] ) ? $this->security->decrypt_value( $api_keys['cohere'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Cohere API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$provider_settings = get_option( 'v7_ai_chatbot_provider_settings', [] );
		$model = $provider_settings['model'] ?? 'command-r-plus';

		$response = wp_remote_post(
			'https://api.cohere.com/v2/chat',
			[
				'headers'   => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				],
				'body'      => wp_json_encode( [
					'model'        => $model,
					'messages'     => [
						[ 'role' => 'system', 'content' => $context ],
						[ 'role' => 'user', 'content' => $message ],
					],
					'max_tokens'   => intval( $settings['max_tokens'] ),
					'temperature'  => floatval( $settings['temperature'] ),
				] ),
				'timeout'   => $provider_settings['timeout'] ?? 30,
				'sslverify' => $provider_settings['verify_ssl'] ?? true,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			$raw_body = wp_remote_retrieve_body( $response );

			// Retired/unavailable model: switch to one this key can use
			// and retry once instead of failing the visitor's question.
			if ( $allow_model_recovery && '' !== $this->maybe_recover_model( 'cohere', $model, 'command-r-plus', $response_code, $raw_body ) ) {
				return $this->query_cohere( $message, $context, $settings, false );
			}

			return new WP_Error( 'api_error', $this->format_api_error( $response_code, $raw_body ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$text = $body['message']['content'][0]['text'] ?? null;

		if ( null === $text ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from Cohere API', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		return wp_kses_post( $text );
	}

	private function test_cohere_connection() {
		$api_keys = get_option( 'v7_ai_chatbot_api_keys', [] );
		$api_key  = isset( $api_keys['cohere'] ) ? $this->security->decrypt_value( $api_keys['cohere'] ) : null;

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Cohere API key not configured', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$response = wp_remote_get(
			'https://api.cohere.com/v1/models',
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $api_key,
				],
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'api_error', $this->format_api_error( $response_code, wp_remote_retrieve_body( $response ) ) );
		}

		return true;
	}
}
