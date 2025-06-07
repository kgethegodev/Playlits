<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import {Card, CardContent, CardDescription, CardTitle} from "@/components/ui/card/index.js";
import {computed} from "vue";

const props = defineProps({
    playlists: Array,
    playlist: Object
})

const comments = computed(() => props.playlist.actions.filter(action => action.type === 'comment'))
</script>

<template>
<app-layout>
    <div class="flex flex-col gap-[1rem] w-full">
        <p class="text-xl font-medium">{{playlist.name}}</p>

        <div class="card-container sm:grid sm:grid-cols-3 flex flex-col gap-4">
            <div class="col-span-2 grid sm:grid-cols-2 gap-4">
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

            <div class="flex flex-col gap-2">
                <p class="font-medium">comments.</p>
                <p class="text-sm" v-if="!comments">nothing right now...</p>

                <Card v-for="comment in comments" class="p-4">
                    <CardTitle>{{comment.user.name}}</CardTitle>
                    <CardDescription>{{comment.meta.message}}</CardDescription>
                </Card>
            </div>
        </div>
    </div>
</app-layout>
</template>

<style scoped lang="postcss">
</style>
