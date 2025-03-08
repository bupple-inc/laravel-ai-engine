export default {
  title: "Bupple Laravel AI Engine",
  description: "A unified interface for multiple AI providers with built-in memory management and streaming capabilities",
  base: '/',
  outDir: 'dist',
  head: [
    ['link', { rel: 'icon', href: 'https://framerusercontent.com/images/CnM2ZH7e8kIXeOBCOJ7CnBzI4A.png' }],
    ['link', { rel: 'canonical', href: 'https://laravel-ai-engine.bupple.io' }]
  ],
  ignoreDeadLinks: true,
  themeConfig: {
    logo: 'https://framerusercontent.com/images/CnM2ZH7e8kIXeOBCOJ7CnBzI4A.png',
    nav: [
      { text: 'Home', link: '/' },
      { 
        text: 'v0.1.0', 
        items: [
          { text: 'v0.1.0 (Current)', link: '/v/v0.1.0/guide/getting-started/introduction' },
          { text: 'Release Notes', link: '/v/v0.1.0/release-notes' }
        ]
      },
      { text: 'API Reference', link: '/v/v0.1.0/api/overview' },
      { text: 'Changelog', link: '/changelogs' },
      { 
        text: 'Links',
        items: [
          { text: 'GitHub', link: 'https://github.com/bupple-inc/laravel-ai-engine' },
          { text: 'Packagist', link: 'https://packagist.org/packages/bupple/laravel-ai-engine' }
        ]
      }
    ],
    sidebar: {
      '/v/v0.1.0/': [
        {
          text: 'Getting Started',
          items: [
            { text: 'Introduction', link: '/v/v0.1.0/guide/getting-started/introduction' },
            { text: 'Installation', link: '/v/v0.1.0/guide/getting-started/installation' },
            { text: 'Configuration', link: '/v/v0.1.0/guide/getting-started/configuration' }
          ]
        },
        {
          text: 'Core Concepts',
          items: [
            { text: 'AI Providers', link: '/v/v0.1.0/guide/core/ai-providers' },
            { text: 'Memory Management', link: '/v/v0.1.0/guide/core/memory-management' },
            { text: 'Streaming', link: '/v/v0.1.0/guide/core/streaming' }
          ]
        },
        {
          text: 'Advanced Topics',
          items: [
            { text: 'Error Handling', link: '/v/v0.1.0/guide/advanced/error-handling' },
            { text: 'Best Practices', link: '/v/v0.1.0/guide/advanced/best-practices' }
          ]
        },
        {
          text: 'API Reference',
          items: [
            { text: 'Overview', link: '/v/v0.1.0/api/overview' },
            {
              text: 'Interfaces',
              collapsed: false,
              items: [
                { text: 'AI Interface', link: '/v/v0.1.0/api/interfaces/ai-interface' },
                { text: 'Memory Interface', link: '/v/v0.1.0/api/interfaces/memory-interface' }
              ]
            },
            {
              text: 'Providers',
              collapsed: false,
              items: [
                { text: 'OpenAI', link: '/v/v0.1.0/api/providers/openai' },
                { text: 'Gemini', link: '/v/v0.1.0/api/providers/gemini' },
                { text: 'Claude', link: '/v/v0.1.0/api/providers/claude' }
              ]
            }
          ]
        }
      ]
    },
    socialLinks: [
      { icon: 'github', link: 'https://github.com/bupple-inc/laravel-ai-engine' }
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2025 Bupple'
    }
  }
} 