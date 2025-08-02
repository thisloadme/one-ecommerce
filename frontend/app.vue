<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup>
useHead({
  title: 'One E-commerce',
  htmlAttrs: {
    lang: 'id'
  },
  bodyAttrs: {
    class: 'bg-[#2e1f1b] text-white min-h-screen'
  },
  meta: [
    { name: 'description', content: 'One E-commerce' },
    { name: 'keywords', content: 'e-commerce, one' },
    { name: 'author', content: 'Dion Budi Riyanto' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1' },
    { property: 'og:title', content: 'One E-commerce' },
    { property: 'og:description', content: 'One E-commerce' },
    { property: 'og:type', content: 'website' },
    { name: 'twitter:title', content: 'One E-commerce' },
    { name: 'twitter:description', content: 'One E-commerce' },
  ],
  link: [
    { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
  ]
})
</script>