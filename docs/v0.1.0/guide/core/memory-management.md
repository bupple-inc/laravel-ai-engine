# Memory Management

The Bupple Laravel AI Engine provides a robust memory management system that allows you to maintain conversation history and context across multiple interactions.

## Overview

Memory management is essential for:
- Maintaining conversation context
- Storing chat history
- Managing parent-child relationships
- Implementing context-aware responses

## Basic Usage

### Initializing Memory

```php
use Bupple\Engine\Facades\BuppleEngine;

$memory = BuppleEngine::memory();
```

### Setting Parent Context

```php
// Set parent context for a user
$memory->setParent(User::class, auth()->id());

// Set parent context for a chat room
$memory->setParent(ChatRoom::class, $chatRoomId);
```

### Managing Messages

```php
// Add a message
$memory->addMessage('user', 'What is Laravel?');

// Add assistant's response
$memory->addMessage('assistant', 'Laravel is a PHP framework...');

// Get conversation history
$messages = $memory->getMessages();

// Clear conversation history
$memory->clear();
```

## Advanced Features

### Message Filtering

```php
// Get last N messages
$recentMessages = $memory->getMessages(limit: 10);

// Get messages after a specific date
$messages = $memory->getMessages(after: now()->subHours(24));

// Get messages before a specific date
$messages = $memory->getMessages(before: now()->subDays(7));
```

### Context Management

```php
// Add context variables
$memory->setContext([
    'user_name' => 'John',
    'preferences' => ['language' => 'PHP'],
]);

// Get context
$context = $memory->getContext();

// Update context
$memory->updateContext(['language_preference' => 'PHP']);

// Clear context
$memory->clearContext();
```

### Batch Operations

```php
// Process multiple messages
$memory->batch()->process([
    ['role' => 'user', 'content' => 'Hello'],
    ['role' => 'assistant', 'content' => 'Hi there!'],
]);

// Bulk delete
$memory->batch()->delete(['message_id_1', 'message_id_2']);
```

## Storage Options

### MySQL/PostgreSQL

Default configuration using Laravel's database:

```php
// config/bupple-engine.php
return [
    'memory' => [
        'use_mongodb' => false,
        'connection' => 'mysql',
        'table' => 'memories',
    ],
];
```

### MongoDB

MongoDB configuration for improved performance with large datasets:

```php
// config/bupple-engine.php
return [
    'memory' => [
        'use_mongodb' => true,
        'connection' => 'mongodb',
        'collection' => 'memories',
    ],
];
```

## Memory Optimization

### Cleanup Strategies

1. **Time-based Cleanup**:
```php
// Remove messages older than 30 days
$memory->cleanup(now()->subDays(30));

// Remove messages with specific condition
$memory->cleanup(function ($query) {
    return $query->where('created_at', '<', now()->subDays(7))
                 ->where('importance', 'low');
});
```

2. **Size-based Cleanup**:
```php
// Keep only last 100 messages
$memory->limitMessages(100);

// Compress old messages
$memory->compress(now()->subDays(30));
```

### Performance Optimization

1. **Indexing**:
```php
// In your migration
Schema::create('memories', function (Blueprint $table) {
    $table->id();
    $table->string('parent_type')->index();
    $table->unsignedBigInteger('parent_id')->index();
    $table->json('messages');
    $table->json('context')->nullable();
    $table->timestamps();
    
    $table->index(['parent_type', 'parent_id', 'created_at']);
});
```

2. **Chunking**:
```php
// Process large datasets in chunks
$memory->chunk(100, function ($messages) {
    foreach ($messages as $message) {
        // Process each message
    }
});
```

## Integration with AI

### Context-Aware Conversations

```php
// Get conversation history and send to AI
$messages = $memory->getMessages();
$response = BuppleEngine::ai()
    ->withSystemMessage('You are a helpful assistant')
    ->send($messages);

// Store AI response
$memory->addMessage('assistant', $response['content']);
```

### Memory-Aware Streaming

```php
$stream = BuppleEngine::ai()->stream($memory->getMessages());

foreach ($stream as $chunk) {
    echo $chunk['content'];
    flush();
}

// Store final response
$memory->addMessage('assistant', $finalContent);
```

## Error Handling

### Memory Exceptions

```php
use Bupple\Engine\Exceptions\MemoryException;

try {
    $messages = $memory->getMessages();
} catch (MemoryException $e) {
    // Handle memory-related errors
    Log::error('Memory Error', [
        'message' => $e->getMessage(),
        'parent' => $memory->getParent()
    ]);
    
    // Fallback to empty conversation
    $messages = [
        ['role' => 'system', 'content' => 'You are a helpful assistant']
    ];
}
```

### Data Integrity

```php
// Validate message format
$memory->validateMessage($message);

// Repair corrupted data
$memory->repair();

// Backup conversation history
$backup = $memory->export();
```

## Best Practices

1. **Regular Cleanup**:
   - Implement automated cleanup strategies
   - Monitor memory usage
   - Archive important conversations

2. **Context Management**:
   - Keep context data minimal
   - Update context when relevant
   - Clear context when switching topics

3. **Performance**:
   - Use appropriate storage engine
   - Implement proper indexing
   - Monitor query performance

4. **Security**:
   - Validate parent context
   - Sanitize input data
   - Implement access control 