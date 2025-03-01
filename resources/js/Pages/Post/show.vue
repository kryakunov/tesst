<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';


const props = defineProps({
    post: Array, // Определяем тип данных, которые мы ожидаем
});


</script>

<template>
    <div class="w-96 mx-auto pt-2">
    <h1 class="text-lg mb-2">Create</h1>
        <Link :href="route('posts.index')" class="mb-8">back</Link>
        <div class="mt-4 pt-4 border-t border-gray-300">
            <div>{{ post.title}}</div>
            <div>{{ post.content}}</div>
            <div class="text-sm text-right">{{ post.date}}</div>
        </div>
    </div>
</template>

<style scoped>
body {
    background-color: #9ca3af;
}
</style>
