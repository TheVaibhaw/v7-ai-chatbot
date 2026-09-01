<?php
/**
 * AI Provider Models Configuration
 * Research-backed data for all major AI providers and their current models
 * Last updated: 2026-09-01
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
				'name'        => 'WordPress AI Client',
				'description' => 'Uses WordPress native AI integration (recommended)',
				'icon'        => '📘',
				'requires_key' => false,
				'documentation' => 'https://developer.wordpress.org/plugins/wordpress-org/blocks/',
				'pricing'     => 'Varies by WordPress configuration',
				'status'      => 'stable',
			],
			'anthropic'    => [
				'name'        => 'Anthropic Claude',
				'description' => 'Advanced reasoning and long-context understanding',
				'icon'        => '🧠',
				'requires_key' => true,
				'documentation' => 'https://console.anthropic.com/',
				'pricing'     => 'Variable based on model',
				'status'      => 'production',
				'website'     => 'https://www.anthropic.com',
			],
			'openai'       => [
				'name'        => 'OpenAI GPT',
				'description' => 'Powerful and versatile language models',
				'icon'        => '🤖',
				'requires_key' => true,
				'documentation' => 'https://platform.openai.com/',
				'pricing'     => 'Variable based on model',
				'status'      => 'production',
				'website'     => 'https://openai.com',
			],
			'google'       => [
				'name'        => 'Google Gemini',
				'description' => 'Multimodal AI with strong reasoning capabilities',
				'icon'        => '✨',
				'requires_key' => true,
				'documentation' => 'https://ai.google.dev/',
				'pricing'     => 'Variable based on model',
				'status'      => 'production',
				'website'     => 'https://deepmind.google/technologies/gemini/',
			],
			'xai'          => [
				'name'        => 'xAI Grok',
				'description' => 'Real-time information access with advanced reasoning',
				'icon'        => '⚡',
				'requires_key' => true,
				'documentation' => 'https://docs.x.ai/',
				'pricing'     => 'Variable based on model',
				'status'      => 'production',
				'website'     => 'https://x.ai/',
			],
			'meta'         => [
				'name'        => 'Meta Llama',
				'description' => 'Open-source large language model',
				'icon'        => '🦙',
				'requires_key' => false,
				'documentation' => 'https://www.llama.com/',
				'pricing'     => 'Free (open-source) or API pricing',
				'status'      => 'production',
				'website'     => 'https://www.llama.com/',
			],
			'mistral'      => [
				'name'        => 'Mistral AI',
				'description' => 'Efficient and powerful European AI provider',
				'icon'        => '🚀',
				'requires_key' => true,
				'documentation' => 'https://docs.mistral.ai/',
				'pricing'     => 'Variable based on model',
				'status'      => 'production',
				'website'     => 'https://mistral.ai/',
			],
			'cohere'       => [
				'name'        => 'Cohere',
				'description' => 'Enterprise-grade NLP and language models',
				'icon'        => '🎯',
				'requires_key' => true,
				'documentation' => 'https://docs.cohere.com/',
				'pricing'     => 'Variable based on model',
				'status'      => 'production',
				'website'     => 'https://cohere.com/',
			],
			'ollama'       => [
				'name'        => 'Ollama',
				'description' => 'Run models locally (self-hosted)',
				'icon'        => '🏠',
				'requires_key' => false,
				'documentation' => 'https://ollama.ai/',
				'pricing'     => 'Free (self-hosted)',
				'status'      => 'stable',
				'website'     => 'https://ollama.ai/',
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
					'description' => 'Most capable model - advanced reasoning, 1M context',
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
					'description' => 'High performance reasoning model - 1M context',
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
					'description' => 'Balanced intelligence and speed - 1M context',
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
					'description' => 'Fast and efficient model - 200K context',
					'context'     => '200K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$1/$5 per 1M tokens',
					'features'    => [ 'vision', 'fast' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'openai'     => [
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
					'description' => 'High-performance model with extended context',
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
					'description' => 'Lightweight and cost-effective variant',
					'context'     => '128K tokens',
					'max_output'  => '16K tokens',
					'pricing'     => '$0.15/$0.60 per 1M tokens',
					'features'    => [ 'vision', 'function-calling' ],
					'recommended' => false,
					'status'      => 'stable',
				],
				[
					'id'          => 'gpt-3.5-turbo',
					'name'        => 'GPT-3.5 Turbo',
					'description' => 'Fast and efficient model',
					'context'     => '16K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.50/$1.50 per 1M tokens',
					'features'    => [ 'function-calling' ],
					'recommended' => false,
					'status'      => 'legacy',
				],
				[
					'id'          => 'o1',
					'name'        => 'O1 (Preview)',
					'description' => 'Advanced reasoning model for complex problems',
					'context'     => '128K tokens',
					'max_output'  => '32K tokens',
					'pricing'     => '$15/$60 per 1M tokens',
					'features'    => [ 'reasoning', 'extended-context' ],
					'recommended' => false,
					'status'      => 'preview',
				],
			],
			'google'     => [
				[
					'id'          => 'gemini-2.0-flash',
					'name'        => 'Gemini 2.0 Flash',
					'description' => 'Latest multimodal model with fast reasoning',
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
					'description' => 'Advanced reasoning and understanding',
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
					'description' => 'High-quality reasoning with extended context',
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
					'description' => 'Fast and efficient model',
					'context'     => '1M tokens',
					'max_output'  => '8K tokens',
					'pricing'     => '$0.075/$0.30 per 1M tokens',
					'features'    => [ 'vision', 'extended-context', 'multimodal' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'xai'        => [
				[
					'id'          => 'grok-2',
					'name'        => 'Grok-2',
					'description' => 'Real-time information access with advanced reasoning',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$2/$10 per 1M tokens',
					'features'    => [ 'real-time', 'reasoning', 'extended-context' ],
					'recommended' => true,
					'status'      => 'stable',
				],
				[
					'id'          => 'grok-vision-beta',
					'name'        => 'Grok Vision Beta',
					'description' => 'Grok with vision capabilities',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$2/$10 per 1M tokens',
					'features'    => [ 'vision', 'real-time', 'extended-context' ],
					'recommended' => false,
					'status'      => 'beta',
				],
			],
			'meta'       => [
				[
					'id'          => 'llama-3.1-405b',
					'name'        => 'Llama 3.1 405B',
					'description' => 'Large open-source model - via Replicate/Together',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.45-2 per 1M tokens (varies by provider)',
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
			'mistral'    => [
				[
					'id'          => 'mistral-large-2',
					'name'        => 'Mistral Large 2',
					'description' => 'High-performance reasoning and language understanding',
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
					'description' => 'Balanced performance and cost',
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
			'cohere'     => [
				[
					'id'          => 'command-r-plus',
					'name'        => 'Command R Plus',
					'description' => 'Advanced reasoning and long-context understanding',
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
					'description' => 'Balanced performance and cost',
					'context'     => '128K tokens',
					'max_output'  => '4K tokens',
					'pricing'     => '$0.50/$1.50 per 1M tokens',
					'features'    => [ 'function-calling', 'extended-context' ],
					'recommended' => false,
					'status'      => 'stable',
				],
			],
			'ollama'     => [
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
			'openai'    => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://platform.openai.com/account/api-keys',
			],
			'google'    => [
				'header' => 'x-goog-api-key',
				'docs'   => 'https://console.cloud.google.com/apis/credentials',
			],
			'xai'       => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://console.x.ai/api-keys',
			],
			'mistral'   => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://console.mistral.ai/keys/',
			],
			'cohere'    => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://dashboard.cohere.com/api-keys',
			],
			'meta'      => [
				'header' => 'Authorization: Bearer',
				'docs'   => 'https://api.together.xyz/',
			],
		];

		return isset( $auth[ $provider ] ) ? $auth[ $provider ] : [];
	}

	/**
	 * Validate API key format for a provider
	 */
	public static function validate_api_key( $provider, $api_key ) {
		if ( empty( $api_key ) ) {
			return new WP_Error( 'empty_key', 'API key cannot be empty' );
		}

		switch ( $provider ) {
			case 'anthropic':
				if ( ! preg_match( '/^sk-ant-[a-zA-Z0-9\-_]{20,}$/', $api_key ) ) {
					return new WP_Error( 'invalid_key', 'Invalid Anthropic API key format. Should start with sk-ant-' );
				}
				break;

			case 'openai':
				if ( ! preg_match( '/^sk-[a-zA-Z0-9\-_]{20,}$/', $api_key ) ) {
					return new WP_Error( 'invalid_key', 'Invalid OpenAI API key format. Should start with sk-' );
				}
				break;

			case 'google':
				if ( strlen( $api_key ) < 20 ) {
					return new WP_Error( 'invalid_key', 'Invalid Google API key format' );
				}
				break;

			case 'mistral':
				if ( ! preg_match( '/^[a-zA-Z0-9\-_]{20,}$/', $api_key ) ) {
					return new WP_Error( 'invalid_key', 'Invalid Mistral API key format' );
				}
				break;

			case 'cohere':
				if ( strlen( $api_key ) < 20 ) {
					return new WP_Error( 'invalid_key', 'Invalid Cohere API key format' );
				}
				break;
		}

		return true;
	}

	/**
	 * Get system message examples for a provider
	 */
	public static function get_system_examples( $provider ) {
		$examples = [
			'anthropic' => 'You are a helpful assistant. Provide clear, concise answers based on the context provided.',
			'openai'    => 'You are a helpful assistant. Answer questions accurately and concisely.',
			'google'    => 'You are a helpful and knowledgeable assistant. Provide accurate information.',
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
}
