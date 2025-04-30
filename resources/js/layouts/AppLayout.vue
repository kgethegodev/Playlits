<script setup>
import { Badge } from '@/components/ui/badge'
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import { AudioLines } from 'lucide-vue-next'
import {Button} from "@/components/ui/button/index.js";

const props = computed(() => usePage().props)
</script>

<template>
    <div class="min-h-[100dvh] flex flex-col py-2 sm:gap-8 gap-4">
        <header class="sticky sm:top-4 top-0 z-20 transition-all">
            <div class="container mx-auto flex items-center justify-between p-4 shadow-sm sm:rounded-xl rounded-b-xl bg-[rgba(255,255,255,.5)] backdrop-blur-lg">
                <Link class="text-xl font-bold" href="/">playlits.</Link>

                <div class="flex gap-4 items-center" v-if="props.auth">
                    <Link href="/playlist/add" class="group">
                        <AudioLines :size="20" class="group-hover:text-blue-400 cursor-pointer"/>
                    </Link>

                    <Link href="/auth/logout" as="button" method="post" class="group cursor-pointer">
                        <Button href="#" class="cursor-pointer">Logout</Button>
                    </Link>
                </div>

                <Link href="/auth/login" v-else class="group">
                    <Button href="#" class="cursor-pointer">Login</Button>
                </Link>
<!--                <Link href="/playlists">-->
<!--                    <Badge class="p-1 transition-all" :class="{'bg-orange-600':pending_playlists}">{{pending_playlists ? '⏳' : '🎧'}} my playlists ({{playlists.length}})</Badge>-->
<!--                </Link>-->
            </div>
        </header>
        <div class="flex flex-col items-center justify-center px-4 sm:px-0 w-full container mx-auto mb-auto">
            <slot />
        </div>
        <footer class="text-right text-sm p-2 container mx-auto">
            euria.
        </footer>
    </div>
</template>

<style scoped lang="scss">

</style>
