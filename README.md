# Bupple Laravel AI Engine

<p align="center">
    <img src="https://framerusercontent.com/images/CnM2ZH7e8kIXeOBCOJ7CnBzI4A.png" alt="Bupple" width="120"/>
</p>

A powerful Laravel package that provides a unified interface for multiple AI providers (OpenAI, Google Gemini, and Anthropic Claude) with built-in memory management and streaming capabilities.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bupple/laravel-ai-engine.svg?style=flat-square)](https://packagist.org/packages/bupple/laravel-ai-engine)
[![Total Downloads](https://img.shields.io/packagist/dt/bupple/laravel-ai-engine.svg?style=flat-square)](https://packagist.org/packages/bupple/laravel-ai-engine)
[![License](https://img.shields.io/packagist/l/bupple/laravel-ai-engine.svg?style=flat-square)](https://packagist.org/packages/bupple/laravel-ai-engine)

## 🎯 Why We Built This

In today's rapidly evolving AI landscape, developers and companies face significant challenges:
- Managing multiple AI provider integrations
- Handling complex streaming implementations
- Building reliable memory management systems
- Ensuring consistent response formats
- Dealing with provider-specific quirks

The Bupple Laravel AI Engine solves these challenges by providing:
- A unified, provider-agnostic interface
- Built-in streaming with SSE support
- Robust memory management
- Consistent response formatting
- Production-ready error handling

Whether you're building a chatbot, content generation system, or AI-powered application, this package helps you focus on building features rather than wrestling with AI provider integrations.

## 🚧 Development Status

> **Important Notice**: This package is currently under heavy maintenance and active development. A significantly enhanced version 1.0.0 is scheduled for release on May 15st, 2025.

### 📅 Roadmap to v1.0.0

We're working hard to bring you the most comprehensive AI Engine for Laravel. Here's what's coming:

#### 🎯 Upcoming Features (March 15 - April 15)
- [ ] Enhanced streaming performance with WebSocket support
- [ ] Advanced rate limiting and quota management
- [ ] Automatic failover between AI providers
- [ ] Improved error handling and retry mechanisms
- [ ] Real-time analytics and usage monitoring
- [ ] Batch processing capabilities
- [ ] Custom model fine-tuning support

#### 🔨 Under Development (March 5-15)
- [ ] Advanced caching system for responses
- [ ] Multi-tenant support
- [ ] Enhanced security features
- [ ] Performance optimizations
- [ ] Extended provider-specific features
- [ ] Comprehensive test coverage
- [ ] API documentation improvements

#### 🌟 Already Implemented
- [x] Basic AI provider integration (OpenAI, Gemini, Claude)
- [x] Memory management system
- [x] SSE streaming support
- [x] MongoDB integration
- [x] Parent context support
- [x] Basic error handling

### 🔄 Weekly Updates
We're committed to regular updates and improvements. Follow our progress:
- Every Monday: New features and enhancements
- Every Wednesday: Bug fixes and optimizations
- Every Friday: Documentation updates

### 🤝 Early Adopters
We value our early adopters! If you're using the package in production, please:
1. Star the repository to show your support
2. Report any issues you encounter
3. Join our discussions for feature requests
4. Share your use cases and feedback

The current version is stable for basic use cases, but we recommend staying updated with the latest releases for new features and improvements.

## 📚 Documentation

For comprehensive documentation, including installation instructions, configuration options, and advanced usage examples, please visit our documentation site:

[https://laravel-ai-engine.bupple.io/](https://laravel-ai-engine.bupple.io/)

## Quick Start

### Requirements

- PHP ^8.1
- Laravel ^11.0|^12.0
- Guzzle ^7.8
- JSON PHP Extension

### Installation

1. Install the package via Composer:

```bash
composer require bupple/laravel-ai-engine
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider"
```

### Basic Usage

```php
use Bupple\Engine\Facades\BuppleEngine;

// Simple chat completion
$response = BuppleEngine::engine()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Using memory management
$memory = BuppleEngine::memory();
$memory->setParent('conversation', $conversationId);
$memory->addMessage('user', 'What is the capital of France?');
$response = BuppleEngine::engine()->send($memory->getMessages());
```

For more examples and detailed documentation, visit our [documentation site](https://laravel-ai-engine.bupple.io/).

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email dev@bupple.io instead of using the issue tracker.

## Credits

- [Bupple's Core Development Team](https://bupple.io/about-us)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🌟 About Bupple

Bupple is more than just a company – we're the architects of a new era in social media management. As the creators of the "Swiss Army Knife for Social Content," we've developed a comprehensive AI ecosystem that's revolutionizing how businesses, creators, and agencies approach content creation.

### 🚀 Our Vision
We believe in a world where creating compelling social media content shouldn't take days of work or years of skill. Through our innovative AI Engine, we're making this vision a reality, enabling anyone to create professional-grade content in minutes.

### 🔄 The AI Cycle
Our Laravel AI Engine sits at the heart of Bupple's intelligent content ecosystem, powering a seamless cycle of:
- **Ideation**: AI-driven content brainstorming and trend analysis
- **Creation**: Automated content generation across multiple formats
- **Optimization**: Smart performance analysis and enhancement
- **Learning**: Continuous improvement through usage patterns and results

> **Revolutionizing Social Media with AI-Powered Intelligence**
>
> Welcome to Bupple's Laravel AI Engine – the powerhouse behind the future of social media content creation. Born from an honest story and driven by innovation, Bupple is transforming how the world creates, manages, and optimizes social media content through the power of artificial intelligence.