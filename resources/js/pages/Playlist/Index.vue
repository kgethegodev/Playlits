<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import {Card, CardContent, CardDescription, CardTitle} from "@/components/ui/card/index.js";

const props = defineProps({
    playlists: Array,
    playlist: Object
})
</script>

<template>
<app-layout :playlists>
    <div class="flex flex-col gap-[1rem] w-full">
        <p class="text-xl">{{playlist.name}}</p>

        <div class="card-container flex flex-col gap-4">
            <Card v-for="track in playlist.tracks">
                <div class="flex gap-4 px-4">
                    <img :src="track.meta.album.images[0].url" class="w-2/8 rounded-lg" v-if="track.meta">

                    <div class="flex flex-col gap-2">
                        <CardTitle>{{track.name}} - {{ track.duration }}</CardTitle>
                        <CardDescription>{{track.artist}}</CardDescription>
                    </div>
                </div>

            </Card>
        </div>
    </div>
</app-layout>
</template>

<style scoped lang="postcss">
</style>
