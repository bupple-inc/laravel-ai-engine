export default {
  title: "Bupple Laravel AI Engine",
  description: "A unified interface for multiple AI providers with built-in memory management and streaming capabilitiess",
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
      { text: 'Guide', link: '/v/0.1.0/getting-started/requirements' },
      { text: 'API', link: '/v/0.1.0/api-reference/overview' },
      { text: 'Changelog', link: '/changelogs' },
    ],
    sidebar: {
      '/v/0.1.0/': [
        {
          text: 'Getting Started',
          items: [
            { text: 'Requirements', link: '/v/0.1.0/getting-started/requirements' },
            { text: 'Installation', link: '/v/0.1.0/getting-started/installation' },
            { text: 'Configuration', link: '/v/0.1.0/getting-started/configuration' },
          ]
        },
        {
          text: 'Basic Usage',
          items: [
            { text: 'Engine', link: '/v/0.1.0/basic-usage/engine' },
            { text: 'Memory', link: '/v/0.1.0/basic-usage/memory' },
            { text: 'SSE', link: '/v/0.1.0/basic-usage/sse' },
          ]
        },
        {
          text: 'Advanced Usage',
          items: [
            { text: 'Engine', link: '/v/0.1.0/advanced-usage/engine' },
            { text: 'Memory', link: '/v/0.1.0/advanced-usage/memory' },
            { text: 'SSE', link: '/v/0.1.0/advanced-usage/sse' },
            { text: 'Error Handling', link: '/v/0.1.0/advanced-usage/error-handling' },
          ]
        },
        {
          text: 'API Reference',
          items: [
            { text: 'Overview', link: '/v/0.1.0/api-reference/overview' },
            { text: 'Engine', link: '/v/0.1.0/api-reference/engine-interface'},
            { text: 'Memory', link: '/v/0.1.0/api-reference/memory-interface' },
          ]
        }
      ]
    },
    socialLinks: [
      { icon: 'github', link: 'https://github.com/bupple-inc/laravel-ai-engine' }
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2025 Bupple Inc.'
    }
  }
} 