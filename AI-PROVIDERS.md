# V7 AI Chatbot Pro - AI Providers & Models Guide

Complete reference for all 8+ supported AI providers with 50+ models.

## 📋 Quick Provider Overview

| Provider | Models | Pricing | Setup | Status |
|----------|--------|---------|-------|--------|
| **WordPress AI** | Native | Varies | ✓ Easy | Stable |
| **Anthropic Claude** | 5 | $1-50/M | 🔑 API Key | Production |
| **OpenAI GPT** | 4+ | $0.15-60/M | 🔑 API Key | Production |
| **Google Gemini** | 4+ | $0.075-10/M | 🔑 API Key | Production |
| **xAI Grok** | 2 | $2-10/M | 🔑 API Key | Production |
| **Mistral AI** | 3+ | $0.14-6/M | 🔑 API Key | Production |
| **Cohere** | 2+ | $0.50-15/M | 🔑 API Key | Production |
| **Meta Llama** | 3 | Free-$2/M | 🔑 API Key | Production |
| **Ollama** | Multiple | Free | 🏠 Self-Hosted | Stable |

---

## 🧠 Anthropic Claude

**Most capable reasoning model for complex tasks**

### Models

#### Claude Opus 5 ⭐ (Recommended)
- **Description:** Most capable model - advanced reasoning, 1M context
- **Context:** 1M tokens (1,000,000)
- **Max Output:** 128K tokens
- **Pricing:** $5/$25 per 1M tokens (input/output)
- **Features:** Extended thinking, vision, extended context
- **Use Case:** Complex reasoning, long documents, code generation
- **Status:** Latest (2024)

```php
// PHP Example
$model = 'claude-opus-5';
$context = '1M tokens';
$cost = '$5 per 1M input tokens, $25 per 1M output tokens';
```

#### Claude Fable 5
- **Context:** 1M tokens
- **Max Output:** 128K tokens
- **Pricing:** $10/$50 per 1M tokens
- **Features:** Most capable, advanced reasoning
- **Use Case:** Ultra-demanding reasoning tasks

#### Claude Opus 4.8
- **Context:** 1M tokens
- **Max Output:** 128K tokens
- **Pricing:** $5/$25 per 1M tokens
- **Features:** High-performance reasoning, vision
- **Use Case:** General purpose, balanced performance

#### Claude Sonnet 5
- **Context:** 1M tokens
- **Max Output:** 128K tokens
- **Pricing:** $2/$10 per 1M tokens
- **Features:** Balanced intelligence and speed
- **Use Case:** Fast responses, cost-effective

#### Claude Haiku 4.5
- **Context:** 200K tokens
- **Max Output:** 4K tokens
- **Pricing:** $1/$5 per 1M tokens
- **Features:** Ultra-fast, efficient
- **Use Case:** Simple tasks, real-time needs

### Get Started

1. Visit: https://console.anthropic.com/
2. Create account
3. Generate API key
4. Format: `sk-ant-...`
5. Paste in V7 Chatbot settings

### Key Strengths
✓ Best reasoning capabilities
✓ Extended context support (1M tokens)
✓ Vision capabilities
✓ Excellent code generation
✓ Strong compliance & safety

---

## 🤖 OpenAI GPT

**Powerful and versatile language models**

### Models

#### GPT-4o (Recommended) ⭐
- **Description:** Most advanced model with vision and reasoning
- **Context:** 128K tokens
- **Max Output:** 16K tokens
- **Pricing:** $2.50/$10 per 1M tokens
- **Features:** Vision, extended context, function calling
- **Use Case:** Advanced reasoning, multimodal tasks

#### GPT-4 Turbo
- **Context:** 128K tokens
- **Pricing:** $10/$30 per 1M tokens
- **Features:** High performance, extended context
- **Use Case:** Complex queries, large document analysis

#### GPT-4o Mini
- **Context:** 128K tokens
- **Pricing:** $0.15/$0.60 per 1M tokens
- **Features:** Lightweight, cost-effective
- **Use Case:** High-volume, cost-sensitive tasks

#### O1 (Preview)
- **Context:** 128K tokens
- **Max Output:** 32K tokens
- **Pricing:** $15/$60 per 1M tokens
- **Features:** Advanced reasoning, complex problem-solving
- **Status:** Preview/Experimental

### Get Started

1. Visit: https://platform.openai.com/
2. Sign up for account
3. Generate API key from account settings
4. Add billing method
5. Format: `sk-...`
6. Paste in V7 Chatbot settings

### Key Strengths
✓ Most widely used LLM
✓ Excellent reasoning
✓ Great API documentation
✓ Strong community support
✓ Reliable performance

### Cost Optimization Tips
- Use GPT-4o Mini for bulk processing
- Use GPT-4 Turbo for complex reasoning
- Monitor usage in dashboard
- Set monthly budgets

---

## ✨ Google Gemini

**Multimodal AI with strong reasoning capabilities**

### Models

#### Gemini 2.0 Flash ⭐ (Recommended)
- **Description:** Latest multimodal model with fast reasoning
- **Context:** 1M tokens
- **Max Output:** 8K tokens
- **Pricing:** $0.075/$0.30 per 1M tokens
- **Features:** Vision, extended context, multimodal
- **Use Case:** Fast responses, multimodal analysis

#### Gemini 2.0 Pro
- **Context:** 1M tokens
- **Pricing:** $2/$10 per 1M tokens
- **Features:** Advanced reasoning, multimodal
- **Use Case:** Complex tasks requiring reasoning

#### Gemini 1.5 Pro
- **Context:** 2M tokens (largest)
- **Pricing:** $1.25/$5 per 1M tokens
- **Features:** Ultra-long context, vision
- **Use Case:** Very long documents, books

#### Gemini 1.5 Flash
- **Context:** 1M tokens
- **Pricing:** $0.075/$0.30 per 1M tokens
- **Features:** Fast, efficient
- **Use Case:** Real-time responses

### Get Started

1. Visit: https://ai.google.dev/
2. Create Google account
3. Generate API key from AI Studio
4. Format: 64+ character key
5. Paste in V7 Chatbot settings

### Key Strengths
✓ 1M-2M context windows (largest)
✓ Multimodal capabilities
✓ Fast inference
✓ Cost-effective pricing
✓ Strong on long documents

---

## ⚡ xAI Grok

**Real-time information access with advanced reasoning**

### Models

#### Grok-2 ⭐ (Recommended)
- **Description:** Real-time information access with advanced reasoning
- **Context:** 128K tokens
- **Max Output:** 4K tokens
- **Pricing:** $2/$10 per 1M tokens
- **Features:** Real-time web access, reasoning, extended context
- **Use Case:** Current events, fact-checking, real-time data

#### Grok Vision Beta
- **Context:** 128K tokens
- **Pricing:** $2/$10 per 1M tokens
- **Features:** Vision capabilities, real-time access
- **Use Case:** Vision analysis with current information

### Get Started

1. Visit: https://console.x.ai/
2. Create account
3. Generate API key
4. Set up billing
5. Paste in V7 Chatbot settings

### Key Strengths
✓ Real-time web access
✓ Current information
✓ Advanced reasoning
✓ Fact-checking capable
✓ Extended context

---

## 🚀 Mistral AI

**Efficient and powerful European AI provider**

### Models

#### Mistral Large 2 ⭐ (Recommended)
- **Description:** High-performance reasoning and language understanding
- **Context:** 32K tokens
- **Max Output:** 4K tokens
- **Pricing:** $2/$6 per 1M tokens
- **Features:** Function calling, JSON mode
- **Use Case:** Advanced reasoning, structured output

#### Mistral Medium
- **Context:** 32K tokens
- **Pricing:** $0.81/$2.43 per 1M tokens
- **Features:** Balanced performance
- **Use Case:** General purpose, good quality

#### Mistral Small
- **Context:** 8K tokens
- **Pricing:** $0.14/$0.42 per 1M tokens
- **Features:** Lightweight, efficient
- **Use Case:** Simple tasks, cost-sensitive

### Get Started

1. Visit: https://console.mistral.ai/
2. Create account
3. Generate API key
4. Set up payment
5. Paste in V7 Chatbot settings

### Key Strengths
✓ European data residency
✓ Cost-effective
✓ Strong performance
✓ Function calling support
✓ Open-source options

---

## 🎯 Cohere

**Enterprise-grade NLP and language models**

### Models

#### Command R Plus ⭐ (Recommended)
- **Description:** Advanced reasoning and long-context understanding
- **Context:** 128K tokens
- **Max Output:** 4K tokens
- **Pricing:** $3/$15 per 1M tokens
- **Features:** Function calling, extended context
- **Use Case:** Enterprise applications, RAG systems

#### Command R
- **Context:** 128K tokens
- **Pricing:** $0.50/$1.50 per 1M tokens
- **Features:** Balanced performance, cost-effective
- **Use Case:** General purpose, RAG

### Get Started

1. Visit: https://dashboard.cohere.com/
2. Sign up
3. Generate API key
4. Add billing
5. Paste in V7 Chatbot settings

### Key Strengths
✓ Enterprise-focused
✓ RAG capabilities
✓ Long context support
✓ Reliable performance
✓ Good documentation

---

## 🦙 Meta Llama

**Open-source large language model**

### Models

#### Llama 3.1 405B ⭐ (Recommended)
- **Description:** Largest Llama model - advanced reasoning
- **Context:** 128K tokens
- **Max Output:** 4K tokens
- **Pricing:** $0.45-2/M tokens (via provider)
- **Features:** Open-source, extended context
- **Use Case:** Complex reasoning, on-par with leading models
- **Provider Options:** Together.ai, Replicate

#### Llama 3.1 70B
- **Context:** 128K tokens
- **Pricing:** $0.18-0.90/M tokens
- **Features:** Open-source, mid-size
- **Use Case:** Good quality-to-cost ratio

#### Llama 3.1 8B
- **Context:** 128K tokens
- **Pricing:** $0.05-0.40/M tokens
- **Features:** Lightweight, efficient
- **Use Case:** High-volume, cost-sensitive

### Get Started - Option 1: Together.ai

1. Visit: https://www.together.ai/
2. Sign up
3. Generate API key
4. Set up payment
5. Paste in V7 Chatbot settings

### Get Started - Option 2: Replicate

1. Visit: https://replicate.com/
2. Create account
3. Generate API token
4. Add billing
5. Paste in V7 Chatbot settings

### Key Strengths
✓ Open-source (no vendor lock-in)
✓ Great performance
✓ Competitive pricing
✓ Community support
✓ Multiple deployment options

---

## 🏠 Ollama (Self-Hosted)

**Run models locally on your own infrastructure**

### Models Available

#### Llama 2
- **Context:** Variable
- **Pricing:** Free (self-hosted)
- **Features:** Open-source, runs locally
- **Use Case:** Privacy-sensitive, offline capability

#### Mistral
- **Context:** Variable
- **Pricing:** Free (self-hosted)
- **Features:** Efficient, lightweight
- **Use Case:** Fast inference, low resource usage

#### Neural Chat
- **Context:** Variable
- **Pricing:** Free (self-hosted)
- **Features:** Conversational, optimized for chat
- **Use Case:** Chat-focused applications

### Installation & Setup

1. **Download Ollama**
   ```bash
   # Visit: https://ollama.ai/
   # Download for your OS (Mac, Linux, Windows)
   ```

2. **Install Model**
   ```bash
   ollama pull llama2
   # or
   ollama pull mistral
   ```

3. **Start Ollama**
   ```bash
   ollama serve
   # Server runs on http://localhost:11434
   ```

4. **Configure in V7 Chatbot**
   - Provider: Ollama
   - Model: llama2 (or chosen model)
   - URL: http://localhost:11434
   - No API key needed

### Key Strengths
✓ Completely free
✓ 100% private (on-premises)
✓ No API costs
✓ Full control
✓ Offline capability
✓ No third-party dependencies

### System Requirements

- **Minimum:** 4GB RAM, 4GB disk
- **Recommended:** 8GB+ RAM, 20GB disk
- **Optimal:** 16GB+ RAM, GPU support

### Performance Tips

- Use GPU support for 10x faster inference
- Start with Mistral for balance of speed/quality
- Use smaller models (7B) for resource-constrained systems
- Monitor system resources during inference

---

## 📊 Pricing Comparison

### Cost Per 1M Tokens

```
Cheapest:
  Ollama (Local)           - $0 (free)
  Llama 3.1 8B (via API)   - $0.05-0.40
  Gemini Flash             - $0.075
  Gemini 1.5 Flash         - $0.075
  GPT-4o Mini              - $0.15

Mid-Range:
  Claude Haiku             - $1
  Llama 3.1 70B            - $0.18-0.90
  Mistral Small            - $0.14
  Claude Sonnet 5          - $2
  GPT-4o                   - $2.50-10

Premium:
  Claude Opus 5            - $5-25
  GPT-4 Turbo              - $10-30
  Cohere Command R Plus    - $3-15
  Grok-2                   - $2-10

Most Expensive:
  O1 (Preview)             - $15-60
  Claude Fable 5           - $10-50
```

### Choose Based On:

- **Cost-Sensitive:** Ollama (free), GPT-4o Mini, Gemini Flash
- **Performance:** Claude Opus 5, GPT-4 Turbo, O1
- **Long Documents:** Gemini 1.5 Pro (2M context)
- **Real-Time Info:** Grok-2
- **Privacy:** Ollama (self-hosted)
- **Enterprise:** Cohere Command R Plus
- **Open-Source:** Llama 3.1

---

## 🔑 API Key Management

### How to Securely Store Keys

1. **In V7 Chatbot**
   - Keys are automatically encrypted
   - Stored in WordPress database
   - Never shown in plain text
   - Only readable by admin users

2. **Best Practices**
   ✓ Use separate API keys per provider
   ✓ Rotate keys regularly
   ✓ Monitor API usage
   ✓ Set spending limits
   ✓ Enable two-factor authentication on provider accounts

3. **Never Share**
   ✗ Don't commit keys to git
   ✗ Don't paste in public forums
   ✗ Don't log API keys
   ✗ Don't share with team without need

### Managing Keys in Settings

1. Go to AI Chatbot > Settings > API Configuration
2. Select provider
3. Paste API key (shown as dots)
4. Click "Test Selected Provider"
5. If successful, save changes

---

## 🧪 Testing Your Configuration

### Via Admin Panel

1. Select provider from dropdown
2. Enter API key
3. Click "Test Selected Provider"
4. See success/error message
5. If successful, your provider is configured

### Troubleshooting

**"Invalid API Key"**
- Double-check key format
- Verify key is not expired
- Check key has correct permissions
- Try creating new key on provider

**"Connection Timeout"**
- Check internet connection
- Verify API endpoint is accessible
- Check firewall/proxy settings
- Increase timeout in settings

**"Unauthorized (403)"**
- Verify API key is correct
- Check key has required permissions
- Confirm billing is active on account
- Try creating new key

**"Rate Limited (429)"**
- Reduce chatbot usage
- Upgrade account tier
- Wait before retrying
- Use lower-cost model

---

## 📈 Scaling & Production

### For Small Sites (< 1K users/month)
- **Recommendation:** Mistral Small or Ollama
- **Cost:** $5-10/month or free
- **Setup:** 30 minutes

### For Medium Sites (1K-100K users/month)
- **Recommendation:** Claude Sonnet 5 or GPT-4o Mini
- **Cost:** $50-500/month
- **Setup:** API key + monitoring

### For Large Sites (> 100K users/month)
- **Recommendation:** Multiple providers with load balancing
- **Cost:** $500-5000+/month
- **Setup:** Enterprise plan, SLA

### Monitoring Usage

1. Check provider dashboard regularly
2. Monitor V7 Chatbot analytics
3. Set up billing alerts
4. Track response times
5. Monitor error rates

---

## 🔐 Security Considerations

### API Key Security
- ✓ Keys encrypted in database
- ✓ Keys not visible in admin UI
- ✓ Keys cleared on uninstall
- ✓ Only admin can view
- ✓ No logging of full keys

### HTTPS Requirement
- ✓ All API calls use HTTPS
- ✓ SSL verification enabled by default
- ✓ Production sites must use HTTPS

### Rate Limiting
- ✓ Prevent abuse
- ✓ Configurable per site
- ✓ Default: 10 requests/hour
- ✓ Customizable in settings

### IP Whitelisting
- Check if provider supports IP whitelisting
- Optional additional security layer
- Good for production sites

---

## 🚀 Getting Started Checklist

- [ ] Choose your AI provider
- [ ] Create account on provider
- [ ] Generate API key
- [ ] Go to V7 Chatbot > Settings > API Configuration
- [ ] Select provider from dropdown
- [ ] Select model (auto-populated)
- [ ] Enter API key
- [ ] Click "Test Selected Provider"
- [ ] Verify "Connection successful" message
- [ ] Configure content sources
- [ ] Enable chatbot on frontend
- [ ] Test chatbot on your site

---

## 📚 Additional Resources

### Official Documentation
- [Anthropic API Docs](https://docs.anthropic.com)
- [OpenAI API Reference](https://platform.openai.com/docs)
- [Google AI Docs](https://ai.google.dev/docs)
- [Mistral Documentation](https://docs.mistral.ai)
- [Cohere Docs](https://docs.cohere.com)
- [Together.ai Docs](https://docs.together.ai)
- [Ollama GitHub](https://github.com/ollama/ollama)

### Support
- Check V7 Chatbot Documentation page (in settings)
- Review error messages in Connection Test
- Check provider's status page
- Contact provider support

---

**Last Updated:** 2026-09-01  
**V7 AI Chatbot Pro:** v3.0.0+
