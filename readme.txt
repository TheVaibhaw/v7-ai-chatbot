=== V7 AI Chatbot ===
Contributors: thevaibhaw
Tags: chatbot, ai, customer support, woocommerce, assistant
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI chatbot that answers visitor questions using only your own site content, with your choice of AI provider and encrypted API key storage.

== Description ==

V7 AI Chatbot adds a support assistant to your site that answers questions using **only your published website content** — your pages, posts and WooCommerce products. It is built to stay on-topic and to avoid disclosing anything private.

You choose the AI provider and model. API keys are encrypted before being stored in your database, and the plugin never ships with or requires any hardcoded key.

= Supported AI providers =

* WordPress AI Client (uses whatever provider WordPress itself is configured with)
* Anthropic Claude
* OpenAI GPT
* Google Gemini
* Groq (ultra-fast LPU inference)
* xAI Grok
* Mistral AI
* Cohere
* Meta Llama (via Together.ai)
* Ollama (self-hosted, no third-party service)

= Model list stays current =

Providers retire and rename models regularly. Instead of relying on a bundled list that goes stale, the settings screen can fetch the exact models your own API key is allowed to use, directly from the provider. If a configured model is retired, the plugin detects it, switches to a working model automatically and tells you it did so — so your chatbot does not go down.

= Privacy and data protection =

The plugin is deliberately conservative about what leaves your server:

* Only **published, publicly visible** content is used. Password-protected posts, private posts, drafts and products hidden from your catalog are excluded.
* Post meta, user accounts, customer records and WooCommerce orders are **never read**, so sales counts, revenue, stock levels and customer details cannot be sent anywhere.
* Before any prompt is sent, a scrubbing pass redacts anything resembling an email address, API key, access token, labelled password or long card-like number.
* The assistant is instructed to refuse requests for credentials, login or admin URLs, staff details, customer or order information, and to ignore visitor attempts to override those rules.
* Internal error details are shown only to administrators; visitors receive a neutral message.

= Security =

* API keys are encrypted at rest using AES-256-CBC with a key derived from your site's own WordPress salts. No static or shared key is used.
* Saved keys are never redisplayed in the admin screen, and browser password managers are blocked from auto-filling the key fields.
* Keys are format-validated before saving, so pasting the wrong provider's key (or an auto-filled password) is rejected with a clear message instead of failing silently later.
* All AJAX endpoints are nonce-verified, and administrative actions require the `manage_options` capability.
* All database queries use prepared statements; all output is escaped.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/v7-ai-chatbot/`, or install it through the Plugins screen.
2. Activate the plugin through the Plugins menu.
3. Go to **AI Chatbot > API Configuration**.
4. Choose your AI provider and paste that provider's API key. (Note: **Groq** keys start with `gsk_` and come from console.groq.com; **xAI Grok** keys start with `xai-` and come from console.x.ai — these are different companies.)
5. Save changes, then click **Load models from my account** and pick a model.
6. Click **Test Selected Provider** to confirm the key works.
7. On the **General** tab, enable the chatbot.
8. Optionally choose which content types to index and customize the appearance.

== Frequently Asked Questions ==

= Do I need my own API key? =

Yes, for every provider except Ollama (self-hosted) and the WordPress AI Client option. The plugin does not include or provide API access; you use your own account with the provider you choose.

= Will it answer questions unrelated to my site? =

No. The assistant is instructed to answer only from your indexed site content and to decline anything else, including attempts to talk it out of those instructions.

= Can it leak customer, order or sales data? =

No. The plugin never reads post meta, user accounts or WooCommerce orders, so that data is not available to it in the first place. It also never includes stock levels or how many times a product has sold.

= Is password-protected content used? =

No. Password-protected, private and unpublished content is excluded, as are WooCommerce products whose catalog visibility is set to hidden.

= Where are my API keys stored? =

Encrypted in your own WordPress database, using a key derived from your site's WordPress salts. They are never written to files, never displayed back in the admin screen, and never logged.

= What happens if my provider retires the model I selected? =

The plugin detects the error, asks your provider which models your key can use, switches to a working one, retries the request, and shows you an admin notice explaining the change.

= Does it work with WooCommerce? =

Yes. Enable product indexing to let the assistant answer questions about your published products, including their public prices.

= Are conversations stored? =

Only if you enable logging (it is on by default and can be turned off under Security & Privacy). See the Privacy section below for exactly what is stored.

== Changelog ==

= 1.0.0 =
* Initial public release
* Support for WordPress AI Client, Anthropic Claude, OpenAI, Google Gemini, Groq, xAI Grok, Mistral, Cohere, Meta Llama (via Together.ai) and self-hosted Ollama
* Live model loading from your provider, so the model list never goes stale
* Automatic recovery if a provider retires your configured model
* API keys encrypted at rest with AES-256-CBC keyed from your site salts
* API key format validation, including detection of a key pasted for the wrong provider
* Password-protected content, private content and hidden WooCommerce products excluded from AI context
* Secrets and contact details redacted from prompts before they are sent
* System prompt hardened against prompt-injection and data-disclosure requests
* Optional conversation logging with configurable retention and IP masking

== Source Code ==

All JavaScript and CSS is shipped unminified and human-readable under the `assets/` directory. Development happens publicly at:
https://github.com/TheVaibhaw/v7-ai-chatbot

== External services ==

This plugin sends data to a third-party AI provider **only when you configure one and a visitor uses the chatbot**. You choose which provider; only the one you select is contacted.

What is sent: the visitor's chat message, plus context assembled from your published pages, posts and/or products (whichever you enable), plus your site name and description. Password-protected and private content, post meta, user data and WooCommerce order data are never included, and likely secrets or contact details are redacted before sending.

Depending on your selection, requests go to one of:

* Anthropic — https://api.anthropic.com — [Terms](https://www.anthropic.com/legal/commercial-terms) / [Privacy](https://www.anthropic.com/legal/privacy)
* OpenAI — https://api.openai.com — [Terms](https://openai.com/policies/terms-of-use/) / [Privacy](https://openai.com/policies/privacy-policy/)
* Google Gemini — https://generativelanguage.googleapis.com — [Terms](https://ai.google.dev/gemini-api/terms) / [Privacy](https://policies.google.com/privacy)
* Groq — https://api.groq.com — [Terms](https://groq.com/terms-of-use/) / [Privacy](https://groq.com/privacy-policy/)
* xAI — https://api.x.ai — [Terms](https://x.ai/legal/terms-of-service) / [Privacy](https://x.ai/legal/privacy-policy)
* Mistral AI — https://api.mistral.ai — [Terms](https://mistral.ai/terms/) / [Privacy](https://mistral.ai/terms/#privacy-policy)
* Cohere — https://api.cohere.com — [Terms](https://cohere.com/terms-of-use) / [Privacy](https://cohere.com/privacy)
* Together.ai (for Meta Llama models) — https://api.together.xyz — [Terms](https://www.together.ai/terms-of-service) / [Privacy](https://www.together.ai/privacy)
* Ollama — your own self-hosted URL. No third-party service is contacted.

The plugin also contacts your selected provider's model-list endpoint when you click "Test Selected Provider" or "Load models from my account" in the admin screen. Those requests send only your API key for authentication.

== Privacy ==

With conversation logging enabled (default, and switchable under Security & Privacy), this plugin creates database tables in your own site and stores:

* chat messages and the assistant's replies
* the visitor's IP address
* timestamps and per-conversation message counts

This data stays in your database and is not transmitted anywhere by the plugin. IP addresses are masked when displayed in the admin. You can set an automatic retention period in days, and disable logging entirely. All tables and settings are removed when the plugin is uninstalled.
