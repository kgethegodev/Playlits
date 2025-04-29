<script setup>
import { Badge } from '@/components/ui/badge'
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import { AudioLines, LogOut, LogIn } from 'lucide-vue-next'

const props = computed(() => usePage().props)
</script>

<template>
    <div class="min-h-[100dvh] flex flex-col py-2 sm:gap-8 gap-4">
        <header>
            <div class="max-w-lg mx-auto flex items-center justify-between px-4 sm:px-0">
                <Link class="text-xl font-bold" href="/">playlits.</Link>

                <div class="flex gap-4" v-if="props.auth">
                    <Link href="/playlist/add" class="group">
                        <AudioLines :size="20" class="group-hover:text-blue-400 cursor-pointer"/>
                    </Link>
                    <Link href="/auth/logout" as="button" method="post" class="group">
                        <LogOut  :size="20"  class="group-hover:text-orange-400 cursor-pointer"/>
                    </Link>
                </div>

                <Link href="/auth/login" v-else class="group">
                    <LogIn :size="20"  class="group-hover:text-green-400 cursor-pointer"/>
                </Link>
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
