<?php
/**
 * AI Provider Models Configuration
 * Research-backed data for all major AI providers and their current models
 * Last updated: 2026-09-01
 *
 * IMPROVED: Groq AI added + Dynamic field visibility
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_AI_Chatbot_Provider_Models {

	/**
	 * Get all available providers with their details
	 */
	public static function get_providers() {
		return [
			'wordpress-ai' => [
				'name'           => 'WordPress AI Client',
				'description'    => 'Uses WordPress native AI integration (recommended)',
				'icon'           => '📘',
				'requires_key'   => false,
				'documentation' => 'https://developer.wordpress.org/plugins/wordpress-org/blocks/',
				'pricing'        => 'Varies by WordPress configuration',
				'status'         => 'stable',
				'api_key_field'  => false,
			],
			'anthropic' => [
				'name'           => 'Anthropic Claude',
				'description'    => 'Advanced reasoning and long-context understanding',
				'icon'           => '🧠',
				'requires_key'   => true,
				'documentation' => 'https://console.anthropic.com/',
				'pricing'        => '$1-50 per 1M tokens',
				'status'         => 'production',
				'website'        => 'https://www.anthropic.com',
				'api_key_field'  => true,
				'api_key_name'   => 'anthropic',
				'get_key_url'    => 'https://console.anthropic.com/account/keys',
			],
			'openai' => [
				'name'           => 'OpenAI GPT',
				'description'    => 'Powerful and versatile language models',
				'icon'           => '🤖',
				'requires_key'   => true,
				'documentation' => 'https://platform.openai.com/',
				'pricing'        => '$0.15-60 per 1M tokens',
				'status'         => 'production',
				'website'        => 'https://openai.com',
				'api_key_field'  => true,
				'api_key_name'   => 'openai',
				'get_key_url'    => 'https://platform.openai.com/account/api-keys',
			],
			'google' => [
				'name'           => 'Google Gemini',
				'description'    => 'Multimodal AI with strong reasoning capabilities',
				'icon'           => '✨',
				'requires_key'   => true,
				'documentation' => 'https://ai.google.dev/',
				'pricing'        => '$0.075-10 per 1M tokens',
				'status'         => 'production',
				'website'        => 'https://deepmind.google/technologies/gemini/',
				'api_key_field'  => true,
				'api_key_name'   => 'google',
				'get_key_url'    => 'https://ai.google.dev/tutorials/get_api_key',
			],
			'groq' => [
				'name'           => 'Groq',
				'description'    => 'Ultra-fast LPU inference engine',
				'icon'           => '⚙️',
				'requires_key'   => true,
				'documentation' => 'https://console.groq.com/',
				'pricing'        => 'Free tier + paid plans',
				'status'         => 'production',
				'website'        => 'https://groq.com/',
				'api_key_field'  => true,
				'api_key_name'   => 'groq',
				'get_key_url'    => 'https://console.groq.com/keys',
			],
			'xai' => [
				'name'           => 'xAI Grok',
				'description'    => 'Real-time information access with advanced reasoning',
				'icon'           => '⚡',
				'requires_key'   => true,
				'documentation' => 'https://docs.x.ai/',
				'pricing'        => '$2-10 per 1M tokens',
				'status'         => 'production',
				'website'        => 'https://x.ai/',
				'api_key_field'  => true,
				'api_key_name'   => 'xai',
				'get_key_url'    => 'https://console.x.ai/api-keys',
			],
			'meta' => [
				'name'           => 'Meta Llama',
				'description'    => 'Open-source large language model',
				'icon'           => '🦙',
				'requires_key'   => true,
				'documentation' => 'https://www.llama.com/',
				'pricing'        => 'Free-$2 per 1M tokens',
				'status'         => 'production',
				'website'        => 'https://www.llama.com/',
				'api_key_field'  => true,
				'api_key_name'   => 'meta',
				'get_key_url'    => 'https://www.together.ai/',
			],
			'mistral' => [
				'name'           => 'Mistral AI',
				'description'    => 'Efficient and powerful European AI provider',
				'icon'           => '🚀',
				'requires_key'   => true,
				'documentation' => 'https://docs.mistral.ai/',
				'pricing'        => '$0.14-6 per 1M tokens',
				'status'         => 'production',
				'website'        => 'https://mistral.ai/',
				'api_key_field'  => true,
				'api_key_name'   => 'mistral',
				'get_key_url'    => 'https://console.mistral.ai/keys/',
			],
			'cohere' => [
				'name'           => 'Cohere',
				'description'    => 'Enterprise-grade NLP and language models',
				'icon'           => '🎯',
				'requires_key'   => true,
				'documentation' => 'https://docs.cohere.com/',
				'pricing'        => '$0.50-15 per 1M tokens',
				'status'         => 'production',
				'website'        => 'https://cohere.com/',
				'api_key_field'  => true,
				'api_key_name'   => 'cohere',
				'get_key_url'    => 'https://dashboard.cohere.com/api-keys',
			],
			'ollama' => [
				'name'           => 'Ollama',
				'description'    => 'Run models locally (self-hosted)',
				'icon'           => '🏠',
				'requires_key'   => false,
				'documentation' => 'https://ollama.ai/',
				'pricing'        => 'Free (self-hosted)',
				'status'         => 'stable',
				'website'        => 'https://ollama.ai/',
				'api_key_field'  => false,
			],
		];
	}

	/**
	 * Get models for a specific provider
	 */
	public static function get_models( $provider ) {
		$models = self::get_all_models();
		return isset( $models[ $provider ] ) ? $models[ $provider ] : [];
	}

	/**
	 * All models organized by provider
	 */
	private static function get_all_models() {
		return [
			'anthropic' => [
				[
					'id'          => 'claude-opus-5',
					'name'        => 'Claude Opus 5 (Latest)',
					'description' => 'Most capable model - advanced reasoning, 1M context',
					'context'     => '1M tokens',
					'max_output'  => '128K tokens',
					'pricing'     => '$5/$25 per 1M tokens',
					'features'    => [ 'thinking', 'extended-context', 'vision' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'claude-fable-5',
					'name'        => 'Claude Fable 5',
					'description' => 'Most capable model - advanced reasoning',
					'context'     => '1M tokens',
					'max_output'  => '128K tokens',
					'pricing'     => '$10/$50 per 1M tokens',
					'features'    => [ 'thinking', 'extended-context', 'vision' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'claude-opus-4-8',
					'name'        => 'Claude Opus 4.8',
					'description' => 'High performance reasoning model',
					'context'     => '1M tokens',
					'max_output'  => '128K tokens',
					'pricing'     => '$5/$25 per 1M tokens',
					'features'    => [ 'thinking', 'extended-context', 'vision' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'claude-sonnet-5',
					'name'        => 'Claude Sonnet 5',
					'description' => 'Balanced intelligence and speed',
					'context'     => '1M tokens',
					'max_output'  => '128K tokens',
					'pricing'     => '$2/$10 per 1M tokens',
					'features'    => [ 'thinking', 'extended-context', 'vision' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'claude-haiku-4-5',
					'name'        => 'Claude Haiku 4.5',
					'description' => 'Fast and efficient model',
					'context'     => '200K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$1/$5 per 1M tokens',
					'features'    => [ 'vision', 'fast' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'openai' => [
				[
					'id'          => 'gpt-4o',
					'name'        => 'GPT-4o (Latest)',
					'description' => 'Most advanced model with vision and reasoning',
					'context'     => '128K tokens',
					'max_output'  => '16K tokens',
					'pricing'     => '$2.50/$10 per 1M tokens',
					'features'    => [ 'vision', 'extended-context', 'function-calling' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'gpt-4-turbo',
					'name'        => 'GPT-4 Turbo',
					'description' => 'High-performance model',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$10/$30 per 1M tokens',
					'features'    => [ 'vision', 'extended-context', 'function-calling' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'gpt-4o-mini',
					'name'        => 'GPT-4o Mini',
					'description' => 'Lightweight and cost-effective',
					'context'     => '128K tokens',
					'max_output'  => '16K tokens',
					'pricing'     => '$0.15/$0.60 per 1M tokens',
					'features'    => [ 'vision', 'function-calling' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'o1',
					'name'        => 'O1 (Preview)',
					'description' => 'Advanced reasoning for complex problems',
					'context'     => '128K tokens',
					'max_output'  => '32K tokens',
					'pricing'     => '$15/$60 per 1M tokens',
					'features'    => [ 'reasoning', 'extended-context' ],
					'recommended' => false,
					'status'      => 'preview',
				],
			],
			'google' => [
				[
					'id'          => 'gemini-2.0-flash',
					'name'        => 'Gemini 2.0 Flash',
					'description' => 'Latest multimodal model',
					'context'     => '1M tokens',
					'max_output'  => '8K tokens',
					'pricing'     => '$0.075/$0.30 per 1M tokens',
					'features'    => [ 'vision', 'extended-context', 'multimodal' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'gemini-2.0-pro',
					'name'        => 'Gemini 2.0 Pro',
					'description' => 'Advanced reasoning',
					'context'     => '1M tokens',
					'max_output'  => '8K tokens',
					'pricing'     => '$2/$10 per 1M tokens',
					'features'    => [ 'vision', 'extended-context', 'multimodal' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'gemini-1.5-pro',
					'name'        => 'Gemini 1.5 Pro',
					'description' => 'High-quality reasoning with 2M context',
					'context'     => '2M tokens',
					'max_output'  => '8K tokens',
					'pricing'     => '$1.25/$5 per 1M tokens',
					'features'    => [ 'vision', 'extended-context', 'multimodal' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'gemini-1.5-flash',
					'name'        => 'Gemini 1.5 Flash',
					'description' => 'Fast and efficient',
					'context'     => '1M tokens',
					'max_output'  => '8K tokens',
					'pricing'     => '$0.075/$0.30 per 1M tokens',
					'features'    => [ 'vision', 'extended-context', 'multimodal' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'groq' => [
				[
					'id'          => 'llama-3.3-70b-versatile',
					'name'        => 'Llama 3.3 70B Versatile',
					'description' => 'Ultra-fast LPU inference - large, capable open model',
					'context'     => '128K tokens',
					'max_output'  => '32K tokens',
					'pricing'     => 'Free tier available',
					'features'    => [ 'fast', 'open-source', 'free-tier' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'llama-3.1-8b-instant',
					'name'        => 'Llama 3.1 8B Instant',
					'description' => 'Ultra-fast, lightweight open model',
					'context'     => '128K tokens',
					'max_output'  => '8K tokens',
					'pricing'     => 'Free tier available',
					'features'    => [ 'fast', 'open-source', 'free-tier' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'gemma2-9b-it',
					'name'        => 'Gemma 2 9B IT',
					'description' => 'Google lightweight instruction-tuned model',
					'context'     => '8K tokens',
					'max_output'  => '8K tokens',
					'pricing'     => 'Free tier available',
					'features'    => [ 'fast', 'lightweight', 'free-tier' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'xai' => [
				[
					'id'          => 'grok-3',
					'name'        => 'Grok 3',
					'description' => 'Real-time information access with advanced reasoning',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => 'Variable based on model',
					'features'    => [ 'real-time', 'reasoning', 'extended-context' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'grok-3-mini',
					'name'        => 'Grok 3 Mini',
					'description' => 'Faster, lighter-weight Grok model',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => 'Variable based on model',
					'features'    => [ 'real-time', 'fast' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'grok-2-1212',
					'name'        => 'Grok 2 (1212)',
					'description' => 'Previous-generation Grok model',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => 'Variable based on model',
					'features'    => [ 'real-time', 'reasoning', 'extended-context' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'meta' => [
				[
					'id'          => 'llama-3.1-405b',
					'name'        => 'Llama 3.1 405B',
					'description' => 'Large open-source model',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.45-2 per 1M tokens',
					'features'    => [ 'open-source', 'extended-context' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'llama-3.1-70b',
					'name'        => 'Llama 3.1 70B',
					'description' => 'Medium open-source model',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.18-0.90 per 1M tokens',
					'features'    => [ 'open-source', 'extended-context' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'llama-3.1-8b',
					'name'        => 'Llama 3.1 8B',
					'description' => 'Efficient open-source model',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.05-0.40 per 1M tokens',
					'features'    => [ 'open-source', 'extended-context' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'mistral' => [
				[
					'id'          => 'mistral-large-2',
					'name'        => 'Mistral Large 2',
					'description' => 'High-performance reasoning',
					'context'     => '32K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$2/$6 per 1M tokens',
					'features'    => [ 'function-calling', 'json-mode' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'mistral-medium',
					'name'        => 'Mistral Medium',
					'description' => 'Balanced performance',
					'context'     => '32K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.81/$2.43 per 1M tokens',
					'features'    => [ 'function-calling', 'json-mode' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'mistral-small',
					'name'        => 'Mistral Small',
					'description' => 'Lightweight and efficient',
					'context'     => '8K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.14/$0.42 per 1M tokens',
					'features'    => [ 'function-calling' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'cohere' => [
				[
					'id'          => 'command-r-plus',
					'name'        => 'Command R Plus',
					'description' => 'Advanced reasoning with long context',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$3/$15 per 1M tokens',
					'features'    => [ 'function-calling', 'extended-context' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'command-r',
					'name'        => 'Command R',
					'description' => 'Balanced performance',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.50/$1.50 per 1M tokens',
					'features'    => [ 'function-calling', 'extended-context' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'ollama' => [
				[
					'id'          => 'llama2',
					'name'        => 'Llama 2',
					'description' => 'Open-source model',
					'context'     => 'Variable',
					'max_output'  => 'Variable',
					'pricing'     => 'Free (self-hosted)',
					'features'    => [ 'open-source', 'self-hosted' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'mistral',
					'name'        => 'Mistral',
					'description' => 'Efficient open-source model',
					'context'     => 'Variable',
					'max_output'  => 'Variable',
					'pricing'     => 'Free (self-hosted)',
					'features'    => [ 'open-source', 'self-hosted' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'neural-chat',
					'name'        => 'Neural Chat',
					'description' => 'Conversational AI model',
					'context'     => 'Variable',
					'max_output'  => 'Variable',
					'pricing'     => 'Free (self-hosted)',
					'features'    => [ 'open-source', 'self-hosted', 'chat' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
		];
	}

	/**
	 * Get API endpoint for a provider
	 */
	public static function get_api_endpoint( $provider ) {
		$endpoints = [
			'anthropic' => 'https://api.anthropic.com/v1/messages',
			'openai'    => 'https://api.openai.com/v1/chat/completions',
			'google'    => 'https://generativelanguage.googleapis.com/v1beta/models',
			'groq'      => 'https://api.groq.com/openai/v1/chat/completions',
			'xai'       => 'https://api.x.ai/v1/chat/completions',
			'mistral'   => 'https://api.mistral.ai/v1/chat/completions',
			'cohere'    => 'https://api.cohere.com/v1/generate',
			'meta'      => 'https://api.together.xyz/v1/chat/completions',
			'ollama'    => 'http://localhost:11434/api/generate',
		];

		return isset( $endpoints[ $provider ] ) ? $endpoints[ $provider ] : '';
	}

	/**
	 * Get authentication details for a provider
	 */
	public static function get_auth_details( $provider ) {
		$auth = [
			'anthropic' => [
				'header'  => 'x-api-key',
				'version' => 'anthropic-version: 2023-06-01',
				'docs'    => 'https://console.anthropic.com/account/keys',
			],
			'openai' => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://platform.openai.com/account/api-keys',
			],
			'google' => [
				'header' => 'x-goog-api-key',
				'docs'   => 'https://ai.google.dev/tutorials/get_api_key',
			],
			'groq' => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://console.groq.com/keys',
			],
			'xai' => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://console.x.ai/api-keys',
			],
			'mistral' => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://console.mistral.ai/keys/',
			],
			'cohere' => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://dashboard.cohere.com/api-keys',
			],
			'meta' => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://www.together.ai/',
			],
		];

		return isset( $auth[ $provider ] ) ? $auth[ $provider ] : [];
	}

	public static function validate_api_key( $provider, $api_key ) {
		if ( empty( $api_key ) ) {
			return new WP_Error( 'empty_key', esc_html__( 'API key cannot be empty.', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		if ( preg_match( '/\s/', $api_key ) ) {
			return new WP_Error( 'invalid_key', esc_html__( 'That value contains spaces, so it is not a valid API key. Please paste the key exactly as shown in your provider dashboard.', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		if ( strlen( $api_key ) < 16 ) {
			return new WP_Error( 'invalid_key', esc_html__( 'That value is too short to be a valid API key. Please paste the full key from your provider dashboard.', V7_AI_CHATBOT_TEXTDOMAIN ) );
		}

		$expected_prefixes = [
			'anthropic' => [ 'prefix' => 'sk-ant-', 'label' => 'Anthropic Claude', 'console' => 'console.anthropic.com' ],
			'openai'    => [ 'prefix' => 'sk-',     'label' => 'OpenAI',           'console' => 'platform.openai.com' ],
			'groq'      => [ 'prefix' => 'gsk_',    'label' => 'Groq',             'console' => 'console.groq.com' ],
			'xai'       => [ 'prefix' => 'xai-',    'label' => 'xAI Grok',         'console' => 'console.x.ai' ],
		];

		// Work out which provider this key actually belongs to, checking the
		// most specific prefix first so 'sk-ant-' wins over 'sk-'.
		$by_specificity = $expected_prefixes;
		uasort(
			$by_specificity,
			static function ( $a, $b ) {
				return strlen( $b['prefix'] ) <=> strlen( $a['prefix'] );
			}
		);

		$detected_provider = null;
		foreach ( $by_specificity as $candidate => $candidate_data ) {
			if ( 0 === strpos( $api_key, $candidate_data['prefix'] ) ) {
				$detected_provider = $candidate;
				break;
			}
		}

		if ( ! isset( $expected_prefixes[ $provider ] ) || $detected_provider === $provider ) {
			return true;
		}

		$expected = $expected_prefixes[ $provider ];

		// The key clearly belongs to another supported provider - naming it
		// is far more useful than just reporting a bad prefix.
		if ( null !== $detected_provider ) {
			$detected = $expected_prefixes[ $detected_provider ];

			return new WP_Error(
				'wrong_provider_key',
				sprintf(
					/* translators: 1: detected provider name, 2: detected key prefix, 3: selected provider name, 4: selected provider's console URL, 5: selected provider's key prefix */
					esc_html__( 'This looks like a %1$s API key (it starts with "%2$s"), but the selected provider is %3$s. These are different services - either switch the "AI Provider" dropdown to %1$s, or paste a %3$s key from %4$s (those start with "%5$s").', V7_AI_CHATBOT_TEXTDOMAIN ),
					$detected['label'],
					$detected['prefix'],
					$expected['label'],
					$expected['console'],
					$expected['prefix']
				)
			);
		}

		return new WP_Error(
			'invalid_key',
			sprintf(
				/* translators: 1: provider name, 2: expected key prefix, 3: provider console URL */
				esc_html__( 'That does not look like a %1$s API key - they start with "%2$s". Get yours from %3$s, and make sure your browser did not auto-fill a saved password into this field.', V7_AI_CHATBOT_TEXTDOMAIN ),
				$expected['label'],
				$expected['prefix'],
				$expected['console']
			)
		);
	}

	/**
	 * Get system message examples for a provider
	 */
	public static function get_system_examples( $provider ) {
		$examples = [
			'anthropic' => 'You are a helpful assistant. Provide clear, concise answers based on the context provided.',
			'openai'    => 'You are a helpful assistant. Answer questions accurately and concisely.',
			'google'    => 'You are a helpful and knowledgeable assistant. Provide accurate information.',
			'groq'      => 'You are a helpful assistant. Provide fast and accurate responses.',
			'xai'       => 'You are Grok, a witty and helpful AI assistant. Provide accurate, real-time information.',
			'mistral'   => 'You are a helpful assistant created by Mistral. Provide accurate and helpful responses.',
			'cohere'    => 'You are a helpful assistant. Respond with clarity and accuracy.',
			'meta'      => 'You are a helpful assistant powered by Llama. Provide useful and accurate information.',
		];

		return isset( $examples[ $provider ] ) ? $examples[ $provider ] : 'You are a helpful assistant.';
	}

	/**
	 * Check if provider requires API key
	 */
	public static function requires_api_key( $provider ) {
		$providers = self::get_providers();
		return isset( $providers[ $provider ]['requires_key'] ) ? $providers[ $provider ]['requires_key'] : false;
	}

	/**
	 * Check if provider shows API key field
	 */
	public static function has_api_key_field( $provider ) {
		$providers = self::get_providers();
		return isset( $providers[ $provider ]['api_key_field'] ) ? $providers[ $provider ]['api_key_field'] : false;
	}

	/**
	 * Get API key field name for provider
	 */
	public static function get_api_key_name( $provider ) {
		$providers = self::get_providers();
		return isset( $providers[ $provider ]['api_key_name'] ) ? $providers[ $provider ]['api_key_name'] : '';
	}
}
