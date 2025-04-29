<script setup>

import AppLayout from "@/layouts/AppLayout.vue";
import PlaylistCard from "@/components/PlaylistCard.vue";
import {usePoll} from "@inertiajs/vue3";

const props = defineProps({
    playlists: Array
})

usePoll(5000)
</script>

<template>
    <app-layout>
        <div class="home-banner rounded-2xl h-25 w-full mb-10 overflow-hidden relative text-white">
            <img src="http://playlits.test/images/pexels-theshuttervision-15447298.jpg" class="object-cover w-full h-full absolute left-1/2 top-1/2 translate-[-50%]" alt="home-banner">
            <div class="h-full w-full flex flex-col justify-center z-10 relative px-4 gap-2">
                <div class="flex flex-col">
                    <p class="font-bold text-xl">Discover, Share, and Convert Playlists</p>
                    <p class="text-xs">Join the community sharing the best playlists across Spotify and Apple Music.</p>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-4 w-full">
            <PlaylistCard v-for="playlist in playlists" :playlist/>
        </div>
    </app-layout>
</template>

<style scoped lang="scss">
.home-banner {
    &:after {
        content: "";
        display: block;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.7);
        z-index: 1;
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
