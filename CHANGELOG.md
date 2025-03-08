# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Coming Soon
- Enhanced streaming performance with WebSocket support
- Advanced rate limiting and quota management
- Automatic failover between AI providers
- Real-time analytics and usage monitoring
- Batch processing capabilities
- Custom model fine-tuning support
- Advanced caching system for responses
- Multi-tenant support
- Enhanced security features
- Performance optimizations
- Extended provider-specific features
- Comprehensive test coverage
- API documentation improvements

## [0.1.01] - 2024-03-14

### Added
- Initial release with support for multiple AI providers:
  - OpenAI (GPT-4, GPT-3.5) integration
  - Google Gemini integration
  - Anthropic Claude integration
- Built-in memory management system with:
  - MongoDB support
  - Parent context support
  - Message history tracking
- Server-Sent Events (SSE) implementation with:
  - Custom event types
  - Message IDs
  - Retry timeout configuration
  - Custom headers support
  - Batch message sending
  - Keep-alive functionality
  - Error handling
  - Auto-flush control
- Facade and dependency injection support
- Basic error handling and retry mechanisms
- Configuration system with environment variables support
- Consistent response format across all providers

### Features
- Unified interface for all AI providers
- Built-in streaming capabilities
- Memory management with database storage
- Provider-specific optimizations
- Laravel integration with service provider
- Configuration publishing
- Database migrations for memory storage

### Documentation
- Initial README with comprehensive documentation
- Installation instructions
- Configuration guide
- Basic usage examples
- Advanced features documentation
- Best practices guide
- Troubleshooting section

[Unreleased]: https://github.com/bupple-inc/laravel-ai-engine/compare/v0.1.01...HEAD
[0.1.01]: https://github.com/bupple-inc/laravel-ai-engine/releases/tag/v0.1.01 