# V7 AI Chatbot Pro - Setup Guide

Version 3.0.0 | Professional-Grade WordPress AI Chatbot Plugin

## Overview

V7 AI Chatbot Pro is an enterprise-grade AI-powered chatbot plugin for WordPress with support for multiple AI providers, advanced security features, comprehensive analytics, and GDPR compliance.

## Features

### ✅ Multi-Provider Support
- **WordPress AI Client** - Native WordPress AI integration (recommended)
- **Anthropic Claude** - State-of-the-art reasoning models
- **OpenAI GPT** - Powerful and reliable models
- **Ollama** - Self-hosted local models

### ✅ Security & Privacy
- Rate limiting to prevent abuse
- API key encryption
- GDPR-compliant data handling
- Auto-deletion of old conversations
- User data export functionality
- IP masking for privacy

### ✅ Advanced Features
- Conversation logging and history
- Usage analytics and statistics
- Customizable AI parameters (temperature, max tokens)
- Multiple positioning options
- Content source selection (Pages, Posts, Products)
- Response time tracking

### ✅ Admin Dashboard
- Tabbed settings interface
- API provider configuration
- Analytics dashboard with charts
- Conversation management
- Usage statistics

## Installation

1. Upload the `v7-ai-chatbot` folder to `/wp-content/plugins/`
2. Activate the plugin from WordPress Admin > Plugins
3. Navigate to **AI Chatbot** in the admin menu
4. Configure your settings

## Quick Setup (5 minutes)

### Step 1: Enable the Chatbot
1. Go to **AI Chatbot > Settings**
2. Under **General**, check "Show chatbot on frontend"
3. Customize greeting message and position

### Step 2: Configure AI Provider
1. Click the **API Configuration** tab
2. Select your preferred provider:
   - **WordPress AI** (if already configured in WordPress settings)
   - **Anthropic Claude** (requires API key)
   - **OpenAI** (requires API key)
   - **Ollama** (requires local instance)

### Step 3: Add API Keys (if using external provider)
1. In **API Configuration** tab
2. Paste your API key in the appropriate field:
   - [Get Anthropic API Key](https://console.anthropic.com/)
   - [Get OpenAI API Key](https://platform.openai.com/account/api-keys)
3. Click **Save Changes**

### Step 4: Configure Content Sources
1. Click the **Advanced Settings** tab
2. Under **Content Sources**, select what content to include:
   - Pages
   - Posts
   - Products (if WooCommerce is active)
3. Adjust generation settings (max tokens, temperature)

### Step 5: Customize Appearance
1. Click the **Appearance** tab
2. Choose colors for:
   - Primary color (header background)
   - Chat bubble color (AI responses)
   - Text color
3. Click **Save Changes**

### Step 6: Configure Security
1. Click the **Security & Privacy** tab
2. Enable desired features:
   - Conversation logging (recommended for analytics)
   - Data encryption (recommended)
   - Rate limiting (recommended for production)
   - GDPR compliance
3. Set auto-deletion days for old conversations
4. Click **Save Changes**

## API Provider Configuration

### WordPress AI Client (Recommended)
- No API key needed
- Uses WordPress native AI integration
- Works with configured WordPress AI provider
- Best for sites already using WordPress AI

### Anthropic Claude
**Model:** `claude-3-5-sonnet-20241022` (or any Anthropic model)

**Setup:**
1. Visit [Anthropic Console](https://console.anthropic.com/)
2. Create an API key
3. Paste in plugin settings
4. Select model name

**Pricing:** Pay-as-you-go based on tokens used

### OpenAI GPT
**Model:** `gpt-4o-mini` (recommended), `gpt-4`, `gpt-3.5-turbo`

**Setup:**
1. Visit [OpenAI Platform](https://platform.openai.com/account/api-keys)
2. Create an API key
3. Paste in plugin settings
4. Select model name

**Pricing:** Credit-based pricing varies by model

### Ollama (Self-Hosted)
**Default URL:** `http://localhost:11434`

**Setup:**
1. Install [Ollama](https://ollama.ai/)
2. Pull a model: `ollama pull llama2`
3. Start Ollama server
4. Enter Ollama URL in plugin settings
5. Select model name

**Pricing:** Free (self-hosted)

## Settings Reference

### General Settings
| Setting | Default | Description |
|---------|---------|-------------|
| Enable Chatbot | Off | Show chatbot on frontend |
| Position | Bottom Right | Chatbot window position |
| Greeting Message | Hi! How can I help you today? | Initial bot message |
| Input Placeholder | Type your message... | Input field hint text |

### Generation Settings
| Setting | Default | Range | Description |
|---------|---------|-------|-------------|
| Max Tokens | 500 | 50-4096 | Maximum response length |
| Temperature | 0.7 | 0-2 | Creativity level (lower=focused, higher=creative) |

### Content Sources
| Option | Default | Description |
|--------|---------|-------------|
| Include Pages | On | Use site pages in AI context |
| Include Posts | On | Use blog posts in AI context |
| Include Products | Off | Use WooCommerce products in AI context |

### Security & Privacy
| Setting | Default | Description |
|---------|---------|-------------|
| Enable Logging | On | Log conversations for analytics |
| Enable Encryption | On | Encrypt data at rest |
| Enable Rate Limiting | On | Limit requests per time period |
| Rate Limit Requests | 10 | Max requests allowed |
| Rate Limit Period | 3600 | Time window in seconds (3600 = 1 hour) |
| GDPR Compliant | On | Enable GDPR features |
| Auto-Delete Days | 30 | Delete conversations older than X days |

## Admin Features

### Analytics Dashboard
View conversation statistics:
- Total conversations
- Total messages processed
- Average messages per conversation
- Average response time

### Conversations
Browse all conversations with details:
- Conversation ID
- User IP (masked)
- Message count
- Duration
- Start time

### Settings Validation
- **Test API Connection** - Verify API credentials
- **Export User Data** - Download user conversations (GDPR)

## Frontend Customization

### CSS Classes
```css
#v7-ai-chatbot-container          /* Main container */
#v7-ai-chatbot-button             /* Toggle button */
#v7-ai-chatbot-window             /* Chat window */
#v7-ai-chatbot-header             /* Header area */
#v7-ai-chatbot-messages           /* Message area */
#v7-ai-chatbot-input              /* Input field */
.v7-ai-chatbot-message            /* Individual message */
.v7-ai-chatbot-user-message       /* User message */
.v7-ai-chatbot-bot-message        /* Bot message */
.v7-ai-chatbot-typing-indicator   /* Typing indicator */
```

### CSS Variables
```css
--v7-ai-chatbot-primary  /* Primary color */
--v7-ai-chatbot-bubble   /* Bubble color */
--v7-ai-chatbot-text     /* Text color */
```

### Position Classes
```css
.bottom-right  /* Bottom right corner */
.bottom-left   /* Bottom left corner */
.top-right     /* Top right corner */
.top-left      /* Top left corner */
```

## Troubleshooting

### Chatbot Not Showing
1. Check "Enable Chatbot" is checked in settings
2. Clear browser cache
3. Check console for JavaScript errors (F12)
4. Verify chatbot styling isn't hidden by theme CSS

### API Connection Failed
1. Verify API key is correct
2. Check internet connection
3. Verify API key has necessary permissions
4. Check API rate limits
5. Use "Test API Connection" button in settings

### Rate Limiting Issues
1. Reduce Rate Limit Requests setting
2. Increase Rate Limit Period setting
3. Check conversation frequency

### Database Issues
1. Verify WordPress database connection
2. Check database has required tables
3. Reinstall plugin to recreate tables

### Performance Issues
1. Reduce "Max Tokens" setting
2. Limit content sources (uncheck unused ones)
3. Enable data encryption only if needed
4. Check server CPU and memory usage

## Database Tables

The plugin creates three tables:

### wp_v7_ai_chatbot_conversations
- `id` - Unique conversation ID
- `user_ip` - User IP address
- `message_count` - Number of messages
- `created_at` - Conversation start time
- `updated_at` - Last message time

### wp_v7_ai_chatbot_messages
- `id` - Message ID
- `conversation_id` - Parent conversation
- `message_type` - 'user' or 'ai'
- `message_text` - Message content
- `response_time` - Milliseconds to respond
- `created_at` - Message timestamp

### wp_v7_ai_chatbot_usage_logs
- `id` - Log entry ID
- `user_ip` - User IP address
- `timestamp` - Request time
- `tokens_used` - API tokens consumed
- `cost` - Estimated API cost
- `status` - 'success' or 'error'
- `error_message` - Error details

## GDPR Compliance

The plugin includes GDPR-compliant features:

### Data Collection
- Only collects conversations and user interactions
- IP addresses are masked in admin interface
- No personal identifying information is stored

### Data Retention
- Automatically deletes conversations after configured period
- Default: 30 days
- Adjustable in "Security & Privacy" settings

### Data Export
- Users can download their conversation history
- JSON format for portability
- Accessible via frontend export button

### Data Deletion
- Conversations auto-delete after retention period
- Manual deletion available to admins
- Uninstalling plugin removes all data

## Security Best Practices

1. **Always use HTTPS** - Required for API communications
2. **Keep API keys secret** - Never share or commit to version control
3. **Enable encryption** - For sensitive conversations
4. **Enable rate limiting** - Prevent abuse and API quota exhaustion
5. **Monitor usage** - Check analytics dashboard regularly
6. **Update regularly** - Keep plugin updated for security patches
7. **Use strong authentication** - For WordPress admin access
8. **Review conversations** - Audit logged conversations regularly

## API Cost Estimation

### Anthropic Claude
- ~$3 per million input tokens
- ~$15 per million output tokens
- [Pricing Calculator](https://www.anthropic.com/pricing)

### OpenAI
- Varies by model
- GPT-4: ~$30 per million input tokens
- [Pricing Calculator](https://openai.com/pricing)

### Ollama
- Free (self-hosted)
- Only requires local server costs

## Support & Resources

- **Documentation** - See Documentation tab in admin
- **GitHub** - https://github.com/TheVaibhaw/v7-ai-chatbot
- **Issues** - Report bugs on GitHub issues
- **Discussions** - Join community discussions

## Changelog

### Version 3.0.0 (Current)
- ✨ Multi-provider support (Anthropic, OpenAI, Ollama)
- ✨ Advanced security and rate limiting
- ✨ Conversation logging and analytics
- ✨ GDPR compliance features
- ✨ API key encryption
- ✨ New tabbed admin interface
- ✨ IP masking and privacy features
- 🔄 Complete code refactoring
- 🐛 Improved error handling
- 📊 Analytics dashboard

### Version 2.0.0
- Initial WordPress AI Client support
- Basic chatbot functionality
- Simple settings page

## License

GPLv2 or later - See LICENSE file

## Credits

**Developed by** Vaibhaw Kumar  
**Website** https://vaibhawkumar.in

---

**Last Updated:** 2024
