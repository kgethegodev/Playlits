<script setup>
import { Badge } from '@/components/ui/badge'
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

// const props = defineProps({
//     playlists: Array
// })
// const pending_playlists = computed(() => props.playlists.some(playlist => playlist['status'] !== 'complete'))
const props = usePage().props
</script>

<template>
    <div class="min-h-[100dvh] flex flex-col py-2 sm:gap-8 gap-4">
        <header>
            <div class="max-w-lg mx-auto flex items-center justify-between px-4 sm:px-0">
                <Link class="text-xl font-bold" href="/">playlits.</Link>
                <Link href="/auth/register" v-if="props.auth">Join the community</Link>
<!--                <Link href="/playlists">-->
<!--                    <Badge class="p-1 transition-all" :class="{'bg-orange-600':pending_playlists}">{{pending_playlists ? '⏳' : '🎧'}} my playlists ({{playlists.length}})</Badge>-->
<!--                </Link>-->
            </div>
        </header>
        <div class="flex flex-col items-center justify-center px-4 sm:px-0 w-full max-w-lg mx-auto">
            <slot />
        </div>
        <footer class="text-center text-sm p-2">
            euria.
        </footer>
    </div>
</template>

<style scoped lang="scss">

</style>
