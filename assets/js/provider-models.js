// V7 AI Chatbot Provider Models Data
// Research-backed AI provider and model information

const V7AIProviderModels = {
	// Provider configurations
	providers: {
		'wordpress-ai': {
			name: 'WordPress AI Client',
			description: 'Uses WordPress native AI integration (recommended)',
			requiresKey: false,
			documentationUrl: 'https://developer.wordpress.org/plugins/wordpress-org/blocks/',
			pricing: 'Varies by WordPress configuration',
			status: 'stable',
			website: 'https://wordpress.org'
		},
		'anthropic': {
			name: 'Anthropic Claude',
			description: 'Advanced reasoning and long-context understanding',
			requiresKey: true,
			documentationUrl: 'https://console.anthropic.com/',
			pricing: 'Variable based on model',
			status: 'production',
			website: 'https://www.anthropic.com'
		},
		'openai': {
			name: 'OpenAI GPT',
			description: 'Powerful and versatile language models',
			requiresKey: true,
			documentationUrl: 'https://platform.openai.com/',
			pricing: 'Variable based on model',
			status: 'production',
			website: 'https://openai.com'
		},
		'google': {
			name: 'Google Gemini',
			description: 'Multimodal AI with strong reasoning capabilities',
			requiresKey: true,
			documentationUrl: 'https://ai.google.dev/',
			pricing: 'Variable based on model',
			status: 'production',
			website: 'https://deepmind.google/technologies/gemini/'
		},
		'xai': {
			name: 'xAI Grok',
			description: 'Real-time information access with advanced reasoning',
			requiresKey: true,
			documentationUrl: 'https://docs.x.ai/',
			pricing: 'Variable based on model',
			status: 'production',
			website: 'https://x.ai/'
		},
		'meta': {
			name: 'Meta Llama',
			description: 'Open-source large language model',
			requiresKey: false,
			documentationUrl: 'https://www.llama.com/',
			pricing: 'Free (open-source) or API pricing',
			status: 'production',
			website: 'https://www.llama.com/'
		},
		'mistral': {
			name: 'Mistral AI',
			description: 'Efficient and powerful European AI provider',
			requiresKey: true,
			documentationUrl: 'https://docs.mistral.ai/',
			pricing: 'Variable based on model',
			status: 'production',
			website: 'https://mistral.ai/'
		},
		'cohere': {
			name: 'Cohere',
			description: 'Enterprise-grade NLP and language models',
			requiresKey: true,
			documentationUrl: 'https://docs.cohere.com/',
			pricing: 'Variable based on model',
			status: 'production',
			website: 'https://cohere.com/'
		},
		'ollama': {
			name: 'Ollama',
			description: 'Run models locally (self-hosted)',
			requiresKey: false,
			documentationUrl: 'https://ollama.ai/',
			pricing: 'Free (self-hosted)',
			status: 'stable',
			website: 'https://ollama.ai/'
		}
	},

	// Models by provider
	models: {
		'anthropic': [
			{
				id: 'claude-opus-5',
				name: 'Claude Opus 5 (Latest)',
				description: 'Most capable model - advanced reasoning, 1M context',
				context: '1M tokens',
				maxOutput: '128K tokens',
				pricing: '$5/$25 per 1M tokens',
				features: ['thinking', 'extended-context', 'vision'],
				recommended: true
			},
			{
				id: 'claude-fable-5',
				name: 'Claude Fable 5',
				description: 'Most capable model - advanced reasoning, 1M context',
				context: '1M tokens',
				maxOutput: '128K tokens',
				pricing: '$10/$50 per 1M tokens',
				features: ['thinking', 'extended-context', 'vision'],
				recommended: false
			},
			{
				id: 'claude-opus-4-8',
				name: 'Claude Opus 4.8',
				description: 'High performance reasoning model - 1M context',
				context: '1M tokens',
				maxOutput: '128K tokens',
				pricing: '$5/$25 per 1M tokens',
				features: ['thinking', 'extended-context', 'vision'],
				recommended: false
			},
			{
				id: 'claude-sonnet-5',
				name: 'Claude Sonnet 5',
				description: 'Balanced intelligence and speed - 1M context',
				context: '1M tokens',
				maxOutput: '128K tokens',
				pricing: '$2/$10 per 1M tokens',
				features: ['thinking', 'extended-context', 'vision'],
				recommended: false
			},
			{
				id: 'claude-haiku-4-5',
				name: 'Claude Haiku 4.5',
				description: 'Fast and efficient model - 200K context',
				context: '200K tokens',
				maxOutput: '4K tokens',
				pricing: '$1/$5 per 1M tokens',
				features: ['vision', 'fast'],
				recommended: false
			}
		],
		'openai': [
			{
				id: 'gpt-4o',
				name: 'GPT-4o (Latest)',
				description: 'Most advanced model with vision and reasoning',
				context: '128K tokens',
				maxOutput: '16K tokens',
				pricing: '$2.50/$10 per 1M tokens',
				features: ['vision', 'extended-context', 'function-calling'],
				recommended: true
			},
			{
				id: 'gpt-4-turbo',
				name: 'GPT-4 Turbo',
				description: 'High-performance model with extended context',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: '$10/$30 per 1M tokens',
				features: ['vision', 'extended-context', 'function-calling'],
				recommended: false
			},
			{
				id: 'gpt-4o-mini',
				name: 'GPT-4o Mini',
				description: 'Lightweight and cost-effective variant',
				context: '128K tokens',
				maxOutput: '16K tokens',
				pricing: '$0.15/$0.60 per 1M tokens',
				features: ['vision', 'function-calling'],
				recommended: false
			},
			{
				id: 'o1',
				name: 'O1 (Preview)',
				description: 'Advanced reasoning model for complex problems',
				context: '128K tokens',
				maxOutput: '32K tokens',
				pricing: '$15/$60 per 1M tokens',
				features: ['reasoning', 'extended-context'],
				recommended: false
			}
		],
		'google': [
			{
				id: 'gemini-2.0-flash',
				name: 'Gemini 2.0 Flash',
				description: 'Latest multimodal model with fast reasoning',
				context: '1M tokens',
				maxOutput: '8K tokens',
				pricing: '$0.075/$0.30 per 1M tokens',
				features: ['vision', 'extended-context', 'multimodal'],
				recommended: true
			},
			{
				id: 'gemini-2.0-pro',
				name: 'Gemini 2.0 Pro',
				description: 'Advanced reasoning and understanding',
				context: '1M tokens',
				maxOutput: '8K tokens',
				pricing: '$2/$10 per 1M tokens',
				features: ['vision', 'extended-context', 'multimodal'],
				recommended: false
			},
			{
				id: 'gemini-1.5-pro',
				name: 'Gemini 1.5 Pro',
				description: 'High-quality reasoning with extended context',
				context: '2M tokens',
				maxOutput: '8K tokens',
				pricing: '$1.25/$5 per 1M tokens',
				features: ['vision', 'extended-context', 'multimodal'],
				recommended: false
			},
			{
				id: 'gemini-1.5-flash',
				name: 'Gemini 1.5 Flash',
				description: 'Fast and efficient model',
				context: '1M tokens',
				maxOutput: '8K tokens',
				pricing: '$0.075/$0.30 per 1M tokens',
				features: ['vision', 'extended-context', 'multimodal'],
				recommended: false
			}
		],
		'groq': [
			{
				id: 'llama-3.3-70b-versatile',
				name: 'Llama 3.3 70B Versatile',
				description: 'Ultra-fast LPU inference - large, capable open model',
				context: '128K tokens',
				maxOutput: '32K tokens',
				pricing: 'Free tier + paid plans',
				features: ['fast', 'open-source', 'free-tier'],
				recommended: true
			},
			{
				id: 'llama-3.1-8b-instant',
				name: 'Llama 3.1 8B Instant',
				description: 'Ultra-fast, lightweight open model',
				context: '128K tokens',
				maxOutput: '8K tokens',
				pricing: 'Free tier + paid plans',
				features: ['fast', 'open-source', 'free-tier'],
				recommended: false
			},
			{
				id: 'gemma2-9b-it',
				name: 'Gemma 2 9B IT',
				description: 'Google lightweight instruction-tuned model',
				context: '8K tokens',
				maxOutput: '8K tokens',
				pricing: 'Free tier + paid plans',
				features: ['fast', 'lightweight', 'free-tier'],
				recommended: false
			}
		],
		'xai': [
			{
				id: 'grok-3',
				name: 'Grok 3',
				description: 'Real-time information access with advanced reasoning',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: 'Variable based on model',
				features: ['real-time', 'reasoning', 'extended-context'],
				recommended: true
			},
			{
				id: 'grok-3-mini',
				name: 'Grok 3 Mini',
				description: 'Faster, lighter-weight Grok model',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: 'Variable based on model',
				features: ['real-time', 'fast'],
				recommended: false
			},
			{
				id: 'grok-2-1212',
				name: 'Grok 2 (1212)',
				description: 'Previous-generation Grok model',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: 'Variable based on model',
				features: ['real-time', 'reasoning', 'extended-context'],
				recommended: false
			}
		],
		'mistral': [
			{
				id: 'mistral-large-2',
				name: 'Mistral Large 2',
				description: 'High-performance reasoning and language understanding',
				context: '32K tokens',
				maxOutput: '4K tokens',
				pricing: '$2/$6 per 1M tokens',
				features: ['function-calling', 'json-mode'],
				recommended: true
			},
			{
				id: 'mistral-medium',
				name: 'Mistral Medium',
				description: 'Balanced performance and cost',
				context: '32K tokens',
				maxOutput: '4K tokens',
				pricing: '$0.81/$2.43 per 1M tokens',
				features: ['function-calling', 'json-mode'],
				recommended: false
			},
			{
				id: 'mistral-small',
				name: 'Mistral Small',
				description: 'Lightweight and efficient',
				context: '8K tokens',
				maxOutput: '4K tokens',
				pricing: '$0.14/$0.42 per 1M tokens',
				features: ['function-calling'],
				recommended: false
			}
		],
		'cohere': [
			{
				id: 'command-r-plus',
				name: 'Command R Plus',
				description: 'Advanced reasoning and long-context understanding',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: '$3/$15 per 1M tokens',
				features: ['function-calling', 'extended-context'],
				recommended: true
			},
			{
				id: 'command-r',
				name: 'Command R',
				description: 'Balanced performance and cost',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: '$0.50/$1.50 per 1M tokens',
				features: ['function-calling', 'extended-context'],
				recommended: false
			}
		],
		'meta': [
			{
				id: 'llama-3.1-405b',
				name: 'Llama 3.1 405B',
				description: 'Large open-source model - via Replicate/Together',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: '$0.45-2 per 1M tokens (varies by provider)',
				features: ['open-source', 'extended-context'],
				recommended: true
			},
			{
				id: 'llama-3.1-70b',
				name: 'Llama 3.1 70B',
				description: 'Medium open-source model',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: '$0.18-0.90 per 1M tokens',
				features: ['open-source', 'extended-context'],
				recommended: false
			},
			{
				id: 'llama-3.1-8b',
				name: 'Llama 3.1 8B',
				description: 'Efficient open-source model',
				context: '128K tokens',
				maxOutput: '4K tokens',
				pricing: '$0.05-0.40 per 1M tokens',
				features: ['open-source', 'extended-context'],
				recommended: false
			}
		],
		'ollama': [
			{
				id: 'llama2',
				name: 'Llama 2',
				description: 'Open-source model',
				context: 'Variable',
				maxOutput: 'Variable',
				pricing: 'Free (self-hosted)',
				features: ['open-source', 'self-hosted'],
				recommended: false
			},
			{
				id: 'mistral',
				name: 'Mistral',
				description: 'Efficient open-source model',
				context: 'Variable',
				maxOutput: 'Variable',
				pricing: 'Free (self-hosted)',
				features: ['open-source', 'self-hosted'],
				recommended: false
			},
			{
				id: 'neural-chat',
				name: 'Neural Chat',
				description: 'Conversational AI model',
				context: 'Variable',
				maxOutput: 'Variable',
				pricing: 'Free (self-hosted)',
				features: ['open-source', 'self-hosted', 'chat'],
				recommended: false
			}
		]
	},

	// Initialize the provider/model selector
	init: function() {
		const providerSelect = document.getElementById('v7-ai-provider');
		if (providerSelect) {
			// Switching provider intentionally picks that provider's default.
			providerSelect.addEventListener('change', (e) => {
				this.updateModels(e.target.value, false);
				this.updateAPIKeyFields(e.target.value);
			});
			// On page load, keep the model that is actually saved.
			if (providerSelect.value) {
				this.updateModels(providerSelect.value, true);
				this.updateAPIKeyFields(providerSelect.value);
			}
		}
	},

	// Update which API key fields are visible based on selected provider
	updateAPIKeyFields: function(provider) {
		// Hide all API key fields first
		const allKeyFields = document.querySelectorAll('[data-api-key-field]');
		allKeyFields.forEach(field => {
			field.style.display = 'none';
		});

		// Show only the field for selected provider
		if (provider && provider !== '') {
			const visibleField = document.querySelector(`[data-api-key-field="${provider}"]`);
			if (visibleField) {
				visibleField.style.display = '';
			}
		}

		// Hide/show Ollama URL field
		const ollamaField = document.querySelector('[data-provider="ollama-url"]');
		if (ollamaField) {
			ollamaField.style.display = (provider === 'ollama') ? '' : 'none';
		}

		// Hide/show Advanced Settings based on provider
		const advancedSection = document.querySelector('[data-section="advanced-provider"]');
		if (advancedSection) {
			advancedSection.style.display = (provider === 'wordpress-ai') ? 'none' : '';
		}
	},

	// Update model dropdown based on selected provider.
	//
	// `preserveSelection` keeps the model that is already saved in the DB.
	// Without it, merely opening the settings page would reset the dropdown
	// to whatever this bundled list calls "recommended", and the next Save
	// would overwrite the admin's real choice with a possibly-retired ID.
	updateModels: function(provider, preserveSelection) {
		const modelSelect = document.getElementById('v7-ai-model');
		if (!modelSelect) return;

		const previous = modelSelect.value;
		const models = this.models[provider] || [];

		if (models.length === 0) {
			modelSelect.innerHTML = '<option value="">No models available - use "Load models from my account"</option>';
			return;
		}

		modelSelect.innerHTML = '';
		models.forEach(model => {
			const option = document.createElement('option');
			option.value = model.id;
			option.textContent = model.name;
			if (model.recommended) {
				option.textContent += ' ⭐ (Recommended)';
			}
			modelSelect.appendChild(option);
		});

		// Keep the saved model selectable even if this bundled list no longer
		// contains it, so it is never silently replaced.
		if (preserveSelection && previous && !models.some(m => m.id === previous)) {
			const kept = document.createElement('option');
			kept.value = previous;
			kept.textContent = previous + ' (currently saved)';
			modelSelect.insertBefore(kept, modelSelect.firstChild);
		}

		if (!this._modelChangeBound) {
			modelSelect.addEventListener('change', (e) => this.showModelDetails(provider, e.target.value));
			this._modelChangeBound = true;
		}

		const target = (preserveSelection && previous) ? previous : ((models.find(m => m.recommended) || models[0]).id);
		modelSelect.value = target;
		this.showModelDetails(provider, target);
	},

	// Show detailed information about selected model
	showModelDetails: function(provider, modelId) {
		const models = this.models[provider] || [];
		const model = models.find(m => m.id === modelId);

		if (!model) return;

		let detailsHTML = `
			<div class="v7-model-details" style="margin-top: 15px; padding: 12px; background: #f9f9f9; border-radius: 4px; border-left: 3px solid #0073aa;">
				<p><strong>📖 Description:</strong> ${model.description}</p>
				<p><strong>🔢 Context Window:</strong> ${model.context}</p>
				<p><strong>📤 Max Output:</strong> ${model.maxOutput}</p>
				<p><strong>💰 Pricing:</strong> ${model.pricing}</p>
				<p><strong>✨ Features:</strong> ${model.features.map(f => `<span style="display: inline-block; background: #e8f1f7; padding: 2px 8px; margin: 2px; border-radius: 3px; font-size: 12px;">${f}</span>`).join('')}</p>
		`;

		const providerInfo = this.providers[provider];
		if (providerInfo && providerInfo.website) {
			detailsHTML += `<p><a href="${providerInfo.website}" target="_blank" rel="noopener">🌐 Visit ${providerInfo.name}</a> | <a href="${providerInfo.documentationUrl}" target="_blank" rel="noopener">📚 Documentation</a></p>`;
		}

		detailsHTML += '</div>';

		// Insert or update details
		let detailsContainer = document.querySelector('.v7-model-details');
		if (detailsContainer) {
			detailsContainer.outerHTML = detailsHTML;
		} else {
			const modelSelect = document.getElementById('v7-ai-model');
			if (modelSelect && modelSelect.parentElement) {
				modelSelect.parentElement.insertAdjacentHTML('afterend', detailsHTML);
			}
		}
	},

	// Get all providers
	getProviders: function() {
		return Object.entries(this.providers).map(([id, data]) => ({
			id,
			...data
		}));
	},

	// Get models for a provider
	getModels: function(provider) {
		return this.models[provider] || [];
	}
};

// Initialize on document ready
document.addEventListener('DOMContentLoaded', () => {
	V7AIProviderModels.init();
});
