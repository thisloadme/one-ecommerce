<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup>
useHead({
  title: 'Dota 2 Draft Simulator',
  htmlAttrs: {
    lang: 'id'
  },
  bodyAttrs: {
    class: 'bg-[#2e1f1b] text-white min-h-screen'
  },
  meta: [
    { name: 'description', content: 'Dota 2 Draft Simulator' },
    { name: 'keywords', content: 'dota 2, draft, simulator' },
    { name: 'author', content: 'Dion Budi Riyanto' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1' },
    { property: 'og:title', content: 'Dota 2 Draft Simulator' },
    { property: 'og:description', content: 'Dota 2 Draft Simulator' },
    { property: 'og:type', content: 'website' },
    { property: 'og:url', content: 'https://yourwebsite.com' },
    { property: 'og:image', content: 'https://yourwebsite.com/image.jpg' },
    { name: 'twitter:card', content: 'summary_large_image' },
    { name: 'twitter:title', content: 'Dota 2 Draft Simulator' },
    { name: 'twitter:description', content: 'Dota 2 Draft Simulator' },
    { name: 'twitter:image', content: 'https://yourwebsite.com/image.jpg' }
  ],
  link: [
    { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
  ]
})
</script>