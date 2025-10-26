<template>
    <div>
        <h1>Lista de Posts (Vindos do Laravel)</h1>

        <div v-if="pending">
            Buscando posts no Laravel...
        </div>

        <div v-else-if="error">
            <h3>Erro ao buscar posts:</h3>
            <pre>{{ error.message }}</pre>
            <p><strong>Dica:</strong> Verifique se o Laravel está rodando (porta 8000) e se o CORS está configurado corretamente.</p>
        </div>

        <ul v-else-if="posts">
            <li v-for="post in posts" :key="post.id">
                {{ post.title }}
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
// Pega a URL da API que definimos no nuxt.config
const config = useRuntimeConfig();
const apiBaseUrl = config.public.apiBaseUrl;

// useFetch é a forma moderna do Nuxt 3 de buscar dados (já é universal)
const { data: posts, pending, error } = await useFetch(
    `${apiBaseUrl}/posts`
);

// Para debug no console do navegador
console.log('Dados recebidos:', posts.value);
console.log('Erro:', error.value);
</script>
