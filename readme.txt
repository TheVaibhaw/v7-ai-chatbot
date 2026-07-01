=== V7 AI Chatbot ===
Contributors: thevaibhaw
Tags: chatbot, ai, customer support, ai chatbot, site assistant
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered chatbot for WordPress. Provides intelligent customer support using your site content with the WordPress AI Client.

== Description ==

V7 AI Chatbot is an intelligent customer support solution that answers questions exclusively about your website content. It uses the WordPress AI Client, which means your site owner controls which AI provider is used — supporting multiple providers without requiring the plugin to know about them directly.

= Key Features =

* AI-powered responses via the WordPress AI Client
* Site-specific answers only — won't respond to off-topic queries
* Indexes pages, posts, and WooCommerce products
* Fully customizable appearance
* Responsive mobile-friendly design
* Clean, optimized code
* Easy setup with admin panel
* Multiple color customization options
* Adjustable position (bottom-right or bottom-left)
* Customizable greeting and placeholder text
* Secure AJAX implementation with nonces
* Compatible with any AI provider configured in WordPress

= Use Cases =

* Customer support automation
* Product information assistance
* Site navigation help
* Content discovery
* FAQ automation

= Requirements =

* WordPress 5.0+ (WordPress 7.0+ with AI provider configured recommended for full functionality)
* PHP 7.4+
* An AI provider configured in WordPress 7.0+ settings

== Installation ==

1. Upload plugin files to `/wp-content/plugins/v7-ai-chatbot/`
2. Activate the plugin through Plugins menu
3. Configure an AI provider in WordPress settings (WordPress 7.0+ required)
4. Go to V7 AI Chatbot settings
5. Adjust generation settings (max tokens, temperature)
6. Select content sources (pages, posts, products)
7. Enable the chatbot
8. Customize appearance to match your brand

== Frequently Asked Questions ==

= Which AI providers are supported? =

This plugin works with any AI provider configured in WordPress 7.0+. The provider is set at the WordPress level, not in the plugin settings. Check your WordPress settings for available AI provider options.

= Will it answer questions not related to my site? =

No, the chatbot is specifically configured to only answer questions about your website content. It will politely decline off-topic queries.

= What content can it access? =

The chatbot can access your published pages, posts, and WooCommerce products (if enabled in settings).

= Is it mobile responsive? =

Yes, the chatbot is fully responsive and works perfectly on all devices.

= Can I customize the appearance? =

Yes, you can customize colors, position, greeting message, and placeholder text from the admin panel.

= Does it work with WooCommerce? =

Yes, you can enable product indexing to help customers with product-related questions.

= What WordPress version do I need? =

WordPress 5.0+ will run the plugin, but WordPress 7.0+ is required for AI features to work (due to the AI Client API).

= Is my data secure? =

Yes, all communications are secured with WordPress nonces and sanitized inputs. Data handling depends on the AI provider configured in your WordPress settings. Consult your provider's privacy policy for details.

== Screenshots ==

1. Admin settings panel
2. Chatbot widget on frontend
3. Chat conversation example
4. Customization options

== Changelog ==

= 2.0.0 =
* Migrated to WordPress 7.0 AI Client API
* Removed direct third-party provider integrations (Groq, Gemini, OpenAI)
* Plugin now uses site-wide AI provider configuration
* Removed API key and provider selection from plugin settings
* Simplified setup process
* Improved security and maintainability

= 1.0.0 =
* Initial release
* Groq, Google Gemini, and OpenAI integration
* Content indexing system
* Customizable appearance
* Responsive design
* Admin settings panel

== Source Code ==

The source code for this plugin is fully open-source and human-readable. All JavaScript and CSS assets are included in their unminified, human-readable formats under the `assets/` directory.

The repository and development source code are publicly hosted at:
https://github.com/TheVaibhaw/v7-ai-chatbot

== External services ==

This plugin uses the WordPress AI Client API (available in WordPress 7.0+) to send user queries to an AI provider configured at the WordPress level. When a visitor uses the chatbot, user messages and your site content (pages, posts, and/or products as configured in the plugin settings) are processed by whichever AI provider your site owner has configured in WordPress.

The specific AI provider, service endpoints, and data handling practices depend on the provider configured in WordPress settings. Consult the documentation and privacy policies of your configured AI provider for details about data handling and service terms.

This plugin stores no data beyond the chatbot settings in the WordPress database. It does not collect user data, track visitors, or store conversation history. Credential management and provider selection are handled entirely by WordPress core, not by this plugin.
