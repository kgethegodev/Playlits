<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import {Card, CardDescription, CardTitle} from "@/components/ui/card/index.js";
import {Link, usePoll} from "@inertiajs/vue3";

const props = defineProps({
    playlists: Array
});

usePoll(5000)
</script>

<template>
    <app-layout>
        <div class="flex flex-col gap-[1rem] w-full">
            <Link :href="`/playlists/${playlist.id}`" v-for="playlist in playlists">
                <Card class="cursor-pointer transition-shadow hover:shadow-md">
                    <div class="flex justify-between items-center px-4">
                        <CardTitle>{{playlist.name}}</CardTitle>
                        <CardDescription :class="`${playlist.status === 'complete' ? 'text-green-500' : 'text-orange-500'}`" >{{ playlist.status === 'complete' ? 'Ready' : 'Pending'}}</CardDescription>
                    </div>
                </Card>
            </Link>
        </div>
    </app-layout>
</template>

<style scoped lang="scss">

</style>
