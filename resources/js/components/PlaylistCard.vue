<script setup>
import {Card} from "@/components/ui/card/index.js";
import {Badge} from "@/components/ui/badge/index.js";
import { Download, MessageCircle, Flame } from 'lucide-vue-next';
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    playlist: Object
})

const likes = computed(() => props.playlist.actions.filter(action => action.type === 'like' ?? []))
const has_liked = computed(() => {
    const auth = usePage().props.auth ?? null

    return auth && likes.value.some(like => like.user_id === auth.id)
})
</script>

<template>
    <Card class="w-full flex flex-col gap-2 p-4">
        <div class="flex gap-2">
            <img class="w-20 h-20 rounded-sm" v-if="playlist.cover" :src="playlist.cover" alt="">
            <div class="w-20 h-20 bg-gray-300 rounded-sm" v-else>

            </div>
            <div class="flex flex-col justify-between flex-1">
                <div class="flex justify-between">
                    <Link class="transition-all font-bold hover:text-blue-500" :href="`/playlists/${playlist.id}`">{{ playlist.name }}</Link>

                    <div class="flex gap-4">
                        <Link :href="`/playlists/${playlist.id}/action`" method="post" :data="{type: 'like'}" preserve-state class="flex gap-0.5 group cursor-pointer">
                            <Flame :size="15" class="transition-all group-hover:stroke-orange-500" :class="has_liked ? 'fill-orange-500 stroke-orange-500 ' : ''"/>
                            <p class="text-xs transition-all group-hover:text-orange-500" :class="has_liked ? 'text-orange-500 ' : ''">{{ likes.length }}</p>
                        </Link>
                        <Link href="#" class="flex gap-0.5 group">
                            <Download :size="15" class="transition-all group-hover:stroke-green-500"/>
                            <p class="text-xs transition-all group-hover:text-green-500">0</p>
                        </Link>
                        <Link href="#" class="flex gap-0.5 group">
                            <MessageCircle :size="15" class="transition-all group-hover:stroke-blue-500"/>
                            <p class="text-xs transition-all group-hover:text-blue-500">0</p>
                        </Link>
                    </div>
                </div>

                <div class="flex justify-end gap-1">
                    <Badge class="text-xs bg-black" v-for="tag in playlist.tags">{{ tag.name }}</Badge>
                </div>
            </div>
        </div>
    </Card>
</template>

<style scoped lang="scss">

</style>
