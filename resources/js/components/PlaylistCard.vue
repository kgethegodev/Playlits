<script setup>
import {Card} from "@/components/ui/card/index.js";
import {Badge} from "@/components/ui/badge/index.js";
import { MessageCircle, Flame, AudioWaveform } from 'lucide-vue-next';
import {Link, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import {
    Dialog,
    DialogContent,
    DialogDescription, DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger
} from "@/components/ui/dialog/index.js";
import {Button} from "@/components/ui/button/index.js";
import {Textarea} from "@/components/ui/textarea/index.js";

const open_dialog = ref(false)
const comment = ref()
const props = defineProps({
    playlist: Object
})
const likes = computed(() => props.playlist.actions.filter(action => action.type === 'like' ?? []))
const comments =  computed(() => props.playlist.actions.filter(action => action.type === 'comment' ?? []))
const downloads =  computed(() => props.playlist.actions.filter(action => action.type === 'download' ?? []))
const has_liked = computed(() => {
    const auth = usePage().props.auth ?? null

    return auth && likes.value.some(like => like.user_id === auth.id)
})
const has_commented = computed(() => {
    const auth = usePage().props.auth ?? null

    return auth && comments.value.some(comment => comment.user_id === auth.id)
})
const has_downloaded = computed(() => {
    const auth = usePage().props.auth ?? null

    return auth && downloads.value.some(download => download.user_id === auth.id)
})

const clearComment = () => {
    comment.value = ""
    open_dialog.value = false
}
</script>

<template>
    <Card class="w-full flex flex-col gap-2 p-4">
        <div class="flex sm:flex-row flex-col-reverse sm:gap-2 gap-4">
            <img class="sm:w-20 w-full rounded-sm" v-if="playlist.cover" :src="playlist.cover" alt="">
            <div class="sm:w-20 w-full bg-gray-300 rounded-sm" v-else>

            </div>
            <div class="flex sm:flex-col flex-col-reverse justify-between flex-1 sm:gap-0 gap-1">
                <div class="flex justify-between">
                    <Link class="transition-all font-bold hover:text-blue-500" :href="`/playlists/${playlist.id}`">{{ playlist.name }}</Link>

                    <div class="flex gap-4 items-center">
                        <Link :href="`/playlists/${playlist.id}/action`" method="post" :data="{type: 'like'}" preserve-state preserve-scroll class="flex gap-0.5 group cursor-pointer">
                            <Flame :size="15" class="transition-all group-hover:stroke-orange-500" :class="has_liked ? 'fill-orange-500 stroke-orange-500 ' : ''"/>
                            <p class="text-xs transition-all group-hover:text-orange-500" :class="has_liked ? 'text-orange-500 ' : ''">{{ likes.length }}</p>
                        </Link>
                        <Link :href="`/playlists/${playlist.id}/action`" method="post" :data="{type: 'download'}" preserve-state preserve-scroll class="flex gap-0.5 group cursor-pointer">
                            <AudioWaveform :size="15" class="transition-all group-hover:stroke-green-500" :class="has_downloaded ? 'stroke-green-500 ' : ''"/>
                            <p class="text-xs transition-all group-hover:text-green-500" :class="has_downloaded ? 'text-green-500 ' : ''">{{downloads.length}}</p>
                        </Link>
                        <Dialog v-model:open="open_dialog">
                            <DialogTrigger class="flex gap-0.5 group cursor-pointer" @click="() => {open_dialog = true}">
                                    <MessageCircle :size="15" class="transition-all group-hover:stroke-blue-500" :class="has_commented ? 'fill-blue-500 stroke-blue-500' : ''"/>
                                    <p class="text-xs transition-all group-hover:text-blue-500" :class="has_commented ? 'text-blue-500' : ''">{{comments.length}}</p>
                            </DialogTrigger>

                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle class="text-left">comment here.</DialogTitle>
                                    <DialogDescription class="text-left text-xs">leave your thoughts about the playlist.</DialogDescription>
                                </DialogHeader>
                                    <Textarea class="text-xs" v-model="comment" placeholder="....."/>
                                <DialogFooter>
                                    <Link class="w-full"
                                          :href="`/playlists/${playlist.id}/action`"
                                          method="post"
                                          :data="{type: 'comment', meta: {message:  'What is wrong'}}"
                                          :disabled="!comment"
                                          :on-finish="clearComment"
                                    >
                                        <Button class="w-full" :disabled="!comment">Comment</Button>
                                    </Link>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                <div class="flex sm:justify-end gap-1">
                    <Badge class="text-xs bg-black" v-for="tag in playlist.tags">{{ tag.name }}</Badge>
                </div>
            </div>
        </div>
    </Card>
</template>

<style scoped lang="scss">

</style>
