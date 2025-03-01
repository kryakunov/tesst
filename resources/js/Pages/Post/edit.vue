<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

const title = ref('')
const content = ref('')


function update() {
     Inertia.post('/posts', {title: post.title.value, content: post.content.value})
}

const props = defineProps({
    post: Array, // Определяем тип данных, которые мы ожидаем
});



</script>

<template>
    <div class="w-96 mx-auto pt-2">
    <h1 class="text-lg mb-2">Create</h1>
        <Link :href="route('posts.index')" class="mb-8">back</Link>
    <form @submit.prevent="store">
        <div class="mb-4">
            <input v-model="post.title" type="text" placeholder="text" value="" class="w-full rounded-full border-gray-300">
        </div>
        <div class="mb-4">
            <textarea v-model="post.content"  placeholder="content" class="w-full rounded-full border-gray-300"></textarea>
        </div>
        <div class="mb-4">
            <button type="submit" class="ml-auto hover:bg-sky-100 block mt-5 w-48 p-4 text-center text-white rounded-full outline-blue-500 bg-cyan-500 shadow-lg shadow-cyan-500/50">Store</button>
        </div>
    </form>
    </div>
</template>

<style scoped>
body {
    background-color: #9ca3af;
}
</style>
