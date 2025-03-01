<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3';
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    posts: Array, // Определяем тип данных, которые мы ожидаем
});

function deletePost(id) {

    Inertia.post(`/posts/${id}`)
}

</script>

<template>
    <div class="w-96 mx-auto pt-8">
    <Link
        :href="route('posts.create')"
        class="ml-10 rounded-md px-3 py-2 text-black ring-1 ring-transparent transition bg-cyan-500  hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] "
    >
        Posts
    </Link>

<div class="mt-16">
    <div v-if="posts">
    <div v-for="post in posts" class="mt-4 pt-4 border-t border-gray-300">
        <div>{{ post.title}}</div>
        <div>{{ post.content}}</div>
        <div class="text-sm text-right">{{ post.date}}</div>
        <div class="text-sm text-right text-sky-500"><Link :href="route('posts.show', post.id)">Show</Link></div>
        <div class="text-sm text-right text-sky-500"><Link :href="route('posts.edit', post.id)">Edit</Link></div>
        <div class="text-sm text-right text-red-500"><p @click="deletePost(post.id)">Delete</p></div>
    </div>
    </div>
</div>

    </div>
</template>

<style scoped>
</style>
