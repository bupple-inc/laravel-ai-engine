# Bupple Engine

Bupple AI Engine is a powerful Laravel package that provides a seamless integration with multiple AI engines (OpenAI, Google Gemini, and Anthropic Claude) along with a robust memory management system. It offers a unified interface for interacting with different AI models while maintaining conversation history and context.

## Features

- **Multiple AI Engine Support**
  - OpenAI (GPT-4 and other models)
  - Google Gemini
  - Anthropic Claude
  - Easily extensible for additional engines

- **Memory Management**
  - Multiple storage drivers (File, Database, Redis)
  - MongoDB support
  - Conversation history tracking
  - Context preservation

- **Stream Support**
  - Server-Sent Events (SSE) for real-time responses
  - Efficient streaming implementation for all supported engines

- **Flexible Configuration**
  - Environment-based configuration
  - Multiple model support
  - Customizable parameters for each engine
  - Easy switching between engines

## Quick Start

```php
use Bupple\Engine\Facades\Engine;

// Send a message and get a response
$response = Engine::engine()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Stream a response
$stream = Engine::engine()->stream([
    ['role' => 'user', 'content' => 'Tell me a story']
]);

// Use memory management
Engine::memory()->addUserMessage('Hello!');
$messages = Engine::memory()->getMessages();
```

## Documentation

- [Getting Started](./v/0.1.0/getting-started/installation.md)
  - [Requirements](./v/0.1.0/getting-started/requirements.md)
  - [Installation](./v/0.1.0/getting-started/installation.md)
  - [Configuration](./v/0.1.0/getting-started/configuration.md)

- [Basic Usage](./v/0.1.0/basic-usage/engine.md)
  - [Engine](./v/0.1.0/basic-usage/engine.md)
  - [Memory](./v/0.1.0/basic-usage/memory.md)
  - [SSE](./v/0.1.0/basic-usage/sse.md)

- [Advanced Usage](./v/0.1.0/advanced-usage/engine.md)
  - [Engine](./v/0.1.0/advanced-usage/engine.md)
  - [Memory](./v/0.1.0/advanced-usage/memory.md)
  - [SSE](./v/0.1.0/advanced-usage/sse.md)
  - [Error Handling](./v/0.1.0/advanced-usage/error-handling.md)

- [API Reference](./v/0.1.0/api-reference/overview.md)
  - [Engine Interface](./v/0.1.0/api-reference/engine-interface.md)
  - [Memory Interface](./v/0.1.0/api-reference/memory-interface.md)

## License

This package is open-sourced software licensed under the MIT license. 