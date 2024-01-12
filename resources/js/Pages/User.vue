<script setup>
import { toRefs, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

import MainLayout from '@/Layouts/MainLayout.vue';
import ContentOverlay from '@/Components/ContentOverlay.vue';
import ShowPostOverlay from '@/Components/ShowPostOverlay.vue';

import Cog from 'vue-material-design-icons/Cog.vue';
import Grid from 'vue-material-design-icons/Grid.vue';
import PlayBoxOutline from 'vue-material-design-icons/PlayBoxOutline.vue';
import AccountOutline from 'vue-material-design-icons/AccountOutline.vue';

let data = reactive({ post: null });
const form = reactive({ file: null });

const props = defineProps({ postsByUser: Object, user: Object });
const { postsByUser, user } = toRefs(props);

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
        setTimeout(() => {
            data.post = null
        }, 100);
    } else {
        url = '/comments/' + object.id
    }

    router.delete(url, {
        onFinish: () => updatePost(object)
    });

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
   for (let index = 0; index < postsByUser.value.data.length; index++) {
    const post = postsByUser.value.data[index];
    
    if (post.id === object.post.id) {
        data.post = post
    }
   } 
};

const getUploadedImage = (event) => {
    form.file = event.target.files[0];
    router.post(`/users`, form, {
        preserveState: false
    })
};
</script>

<template>
    <Head title="Instagram" />

    <MainLayout>
        <div class="pt-2 md:pt-6 max-w-[800px] lg:ml-0 md:ml-[80px] md:pl-20 px-4 w-[100vw]">
            <div class="flex items-center md:justify-between">
                <label for="fileUser">
                    <img class="rounded-full object-fit md:w-[200px] w-[100px] cursor-pointer"
                        :src="user.file" alt="">
                </label>
                <input v-if="user.id === $page.props.auth.user.id" id="fileUser" class="hidden" type="file" @input="$event => getUploadedImage($event)">

                <div class="ml-6 w-full">
                    <div class="flex items-center md:mb-8 mb-5">
                        <div class="md:mr-6 mr-3 roundeded-lg text-[22px]">{{ user.name }}</div>
                        <button
                            class="md:block hidden md:mr-6 p-1 px-4 rounded-lg text-[16px] font-extrabold bg-gray-100 hover:bg-gray-200">
                            Edit Profile
                        </button>
                        <Cog :size="28" class="cursor-pointer" />
                    </div>
                    <button
                        class="md:hidden mr-6 p-1 px-4 max-w-[260px] rounded-lg text-[17px] font-extrabold bg-gray-100 hover:bg-gray-200">
                        Edit Profile
                    </button>
                    <div class="md:block hidden">
                        <div class="flex items-center text-[18px]">
                            <div class="mr-6">
                                <span class="font-extrabold">{{ postsByUser.data.length }}</span> posts
                            </div>
                            <div class="mr-6">
                                <span class="font-extrabold">222</span> followers
                            </div>
                            <div class="mr-6">
                                <span class="font-extrabold">643</span> following
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:hidden">
            <div class="w-full flex items-center justify-around border-t border-t-gray-300 mt-8">
                <div class="text-center p-3">
                    <div class="font-extrabold">{{ postsByUser.data.length }}</div>
                    <div class="text-gray-400 font-semibold -mt-1 5">posts</div>
                </div>
                <div class="text-center p-3">
                    <div class="font-extrabold">43</div>
                    <div class="text-gray-400 font-semibold -mt-1 5">followers</div>
                </div>
                <div class="text-center p-3">
                    <div class="font-extrabold">55</div>
                    <div class="text-gray-400 font-semibold -mt-1 5">following</div>
                </div>
            </div>
            <div class="w-full flex items-center justify-between border-t border-t-gray-300">
                <div class="p-3 w-1/3 flex justify-center border-t border-t-gray-900">
                    <Grid :size="28" fillColor="#0095f6" class="cursor-pointer"></Grid>
                </div>
                <div class="p-3 w-1/3 flex justify-center">
                    <PlayBoxOutline :size="28" fillColor="#8E8E8E" class="cursor-pointer"></PlayBoxOutline>
                </div>
                <div class="p-3 w-1/3 flex justify-center">
                    <AccountOutline :size="28" fillColor="#8E8E8E" class="cursor-pointer"></AccountOutline>
                </div>
            </div>
        </div>

        <div id="ContentSection" class="md:pr-1.5 lg:pl-0 md:pl-[90px]">
            <div class="md:block mt-10 hidden border-t border-t-gray-300">
                <div
                    class="flex items-center justify-between max-w-[600px] mx-auto font-extrabold text-gray-400 text-[15px]">
                    <div class="p-[17px] w-1/3 flex justify-center items-center border-t border-t-gray-900">
                        <Grid :sze="15" fillColor="#000000" class="cursor-pointer"></Grid>
                        <div class="ml-2 -mb-[1px] text-gray-900">POSTS</div>
                    </div>
                    <div class="p-[17px] w-1/3 flex justify-center items-center">
                        <PlayBoxOutline :sze="15" fillColor="#000000" class="cursor-pointer"></PlayBoxOutline>
                        <div class="ml-2 -mb-[1px] text-gray-900">REELS</div>
                    </div>
                    <div class="p-[17px] w-1/3 flex justify-center items-center">
                        <AccountOutline :sze="15" fillColor="#000000" class="cursor-pointer"></AccountOutline>
                        <div class="ml-2 -mb-[1px] text-gray-900">TAGGED</div>
                    </div>
                </div>
            </div>
            <div class="grid md:gap-4 gap-1 grid-cols-3 relative pb-20">
                <div v-for="postByUser in postsByUser.data" :key="postByUser">
                    <ContentOverlay :postByUser="postByUser" @selectedPost="data.post = $event" />
                </div>
            </div>
        </div>
    </MainLayout>

    <ShowPostOverlay v-if="data.post" :post="data.post" @addComment="$event => addComment($event)" @updateLike="$event => updateLike($event)" @deleteSelected="$event => { deleteFunction($event) }" @closeOverlay="$event => data.post = null"></ShowPostOverlay>
</template>