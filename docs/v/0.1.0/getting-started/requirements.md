# Requirements

## PHP Requirements
- PHP 8.1 or higher
- Laravel 10.0 or higher
- Composer 2.0 or higher

## Required PHP Extensions
- JSON
- OpenSSL
- PDO
- Mbstring

## Optional Requirements

### For Database Memory Driver
- MySQL 5.7+ or PostgreSQL 10+
- PDO PHP Extension

### For MongoDB Memory Driver
- MongoDB 4.0+
- MongoDB PHP Extension
- `mongodb/laravel-mongodb` package

### For Redis Memory Driver
- Redis 5.0+
- Redis PHP Extension
- `predis/predis` package

## API Keys

Depending on which AI engine you plan to use, you'll need one or more of the following API keys:

### OpenAI
- OpenAI API Key
- (Optional) Organization ID

### Google Gemini
- Google Cloud API Key
- (Optional) Project ID

### Anthropic Claude
- Anthropic API Key

## Environment Setup

Make sure your environment variables are properly configured in your `.env` file. Here's a template of the required variables:

```env
# Default Engine Driver
BUPPLE_DEFAULT_ENGINE_DRIVER=openai

# OpenAI Configuration
BUPPLE_ENGINE_OPENAI_API_KEY=your-openai-api-key
BUPPLE_ENGINE_OPENAI_MODEL=gpt-4
BUPPLE_ENGINE_OPENAI_TEMPERATURE=0.7
BUPPLE_ENGINE_OPENAI_MAX_TOKENS=1000
BUPPLE_ENGINE_OPENAI_ORGANIZATION_ID=

# Gemini Configuration
BUPPLE_ENGINE_GEMINI_API_KEY=your-gemini-api-key
BUPPLE_ENGINE_GEMINI_MODEL=gemini-pro
BUPPLE_ENGINE_GEMINI_TEMPERATURE=0.7
BUPPLE_ENGINE_GEMINI_MAX_TOKENS=1000
BUPPLE_ENGINE_GEMINI_PROJECT_ID=

# Claude Configuration
BUPPLE_ENGINE_CLAUDE_API_KEY=your-claude-api-key
BUPPLE_ENGINE_CLAUDE_MODEL=claude-3-opus-20240229
BUPPLE_ENGINE_CLAUDE_TEMPERATURE=0.7
BUPPLE_ENGINE_CLAUDE_MAX_TOKENS=1000

# Memory Driver Configuration
BUPPLE_MEMORY_DRIVER=file
BUPPLE_MEMORY_DB_MONGODB_ENABLED=false
BUPPLE_MEMORY_DB_CONNECTION=mysql
BUPPLE_MEMORY_DB_TABLE_NAME=engine_memory
BUPPLE_MEMORY_FILE_PATH=app/engine-memory
BUPPLE_MEMORY_REDIS_CONNECTION=redis
BUPPLE_MEMORY_REDIS_KEY=engine_memory
BUPPLE_MEMORY_REDIS_TTL=2592000
``` 