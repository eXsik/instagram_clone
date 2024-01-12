<script setup>
import { ref, onMounted, toRefs } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';

import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Navigation } from "vue3-carousel";
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'

import MainLayout from '@/Layouts/MainLayout.vue';
import LikesSection from '@/Components/LikesSection.vue';
import ShowPostOverlay from "@/Components/ShowPostOverlay.vue";

let windowWidth = ref(window.innerWidth);
let currentSlide = ref(0);
let currentPost = ref(null);
let openOverlay = ref(false);

const props = defineProps({ posts: Object, allUsers: Object });
const { posts, allUsers } = toRefs(props);

onMounted(() => {
    window.addEventListener("resize", () => {
        windowWidth.value = window.innerWidth;
    });
});

const addComment = (comment) => {
    router.post('/comments', {
        post_id: comment.post.id,
        user_id: comment.user.id,
        comment: comment.comment
    }, {
        onFinish: () => updatePost(comment)
    })
};

const deleteFunction = (object) => {
    let url = '';

    if (object.deleteType === 'Post') {
        url = '/posts/' + object.id
    } else {
        url = '/comments/' + object.id
    }

    router.delete(url, {
        onFinish: () => updatePost(object)
    });

    if (object.deleteType === 'Post') {
        openOverlay.value = false;
    }
};

const updateLike = (object) => {
    let deleteLike = false;
    let id = null;

    for (let index = 0; index < object.post.likes.length; index++) {
        const like = object.post.likes[index];
        if (like.user_id === object.user.id && like.post_id === object.post.id) {
            deleteLike = true;
            id = like.id;
        }
    }

    if (deleteLike) {
        router.delete('/likes/' + id, {
            onFinish: () => updatePost(object)
        });
    } else {
        router.post('/likes', {
            post_id: object.post.id,
        }, {
            onFinish: () => updatePost(object)
        });
    }
};

const updatePost = (object) => {
   for (let index = 0; index < posts.value.data.length; index++) {
    const post = posts.value.data[index];
    
    if (post.id === object.post.id) {
        currentPost.value = post
    }
   } 
};
</script>

<template>
    <Head title="Instagram" />

    <MainLayout>
        <div class="mx-auto lg:pl-0 md:pl-[80px] pl-0">
            <Carousel v-model="currentSlide" class="max-w-[700px] mx-auto" :items-to-show="windowWidth >= 768 ? 8 : 6"
                :items-to-scroll="4" :wrap-around="true" :transition="500" snapAlign="start">
                <slide v-for="user in allUsers" :key="user">
                    <Link :href="route('users.show', { id: user.id })" class="relative mx-auto text-center mt-4 px-2 cursor-pointer">
                    <div
                        class="absolute z-[-1] -top-[5px] left-[4px] rounded-full rotate-45 w-[64px] h-[64px] contrast-[1.3] bg-gradient-to-t from-[#f9ce34] to-[#6228d7] via-[#ee2a7b]">
                        <div class="rounded-full ml-[3px] mt-[3px] w-[58px] h-[58px] bg-white"></div>
                    </div>
                    <img class="rounded-full w-[56px] h-[56px] -mt-[1px]" :src="user.file" :alt="user.name + ' picture'">
                    <div class="text-xs mt-2 w-[60px] truncate text-ellipsis overflow-hidden">
                        {{ user.name }}
                    </div>
                    </Link>
                </slide>

                <template #addons>
                    <Navigation></Navigation>
                </template>
            </Carousel>

            <div id="Posts" v-for="post in posts.data" :key="post" class="px-4 mx-w-[600px] mx-auto mt-10 pb-20">
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center">
                        <Link :href="route('users.show', { id: post.user.id })" class="flex items-center">
                            <img class="rounded-full w-[38px] h-[38px]" :src="post.user.file" :alt="post.user.name + ' profile picture'">
                            <div class="ml-4 font-extrabold text-[15px]">{{ post.user.name }}</div>
                        </Link>
                        <div class="flex items-center text-[15px] text-gray-500">
                            <span class="-mt-5 ml-2 mr-[5px] text-[35px]">
                                .
                            </span>
                            <div>{{ post.created_at }}</div>
                        </div>
                    </div>

                    <DotsHorizontal class="cursor-pointer" :size="27"></DotsHorizontal>
                </div>
                <div class="bg-black rounded-lg w-full min-h-[400px] flex items-center">
                    <img class="mx-auto w-full" :src="post.file" alt="">
                </div>

                <LikesSection :post="post" @like="$event => updateLike($event)"></LikesSection>
                
                <div class="text-black font-extrabold py-1">{{ post.likes.length }} likes</div>
                <div>
                    <span class="text-black font-extrabold">{{ post.user.name }}</span>
                    {{ post.text }}
                </div>
                <button class="text-gray-500 font-extrabold py-1" @click="$event => {
                    currentPost = post;
                    openOverlay = true
                } ">
                    View all {{ post.comments.length }} comments
                </button>
            </div>
        </div>
    </MainLayout>

    <ShowPostOverlay v-if="openOverlay" :post="currentPost" @addComment="$event => addComment($event)" @updateLike="$event => updateLike($event)" @deleteSelected="$event => { deleteFunction($event) }" @closeOverlay="$event => openOverlay = false"></ShowPostOverlay>
</template>

<style>
.carousel__prev,
.carousel__next,
.carousel__prev:hover,
.carousel__next:hover {
    color: rgba(49, 49, 49);
    background: rgba(255, 255, 255, 0.8);
    border-radius: 100%;
    margin: 0 20px;
}
</style>
