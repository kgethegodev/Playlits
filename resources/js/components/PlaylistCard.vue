<script setup>
import {Card} from "@/components/ui/card/index.js";
import {Badge} from "@/components/ui/badge/index.js";
import { ThumbsUp, Download, MessageCircle } from 'lucide-vue-next';
import {Link} from "@inertiajs/vue3";

defineProps({
    playlist: Object
})
</script>

<template>
    <Card class="w-full flex flex-col gap-2 p-4 cursor-pointer">
        <div class="flex gap-2">
            <div class="w-20 h-20 bg-gray-300 rounded-sm"></div>
            <div class="flex flex-col justify-between flex-1">
                <div class="flex justify-between">
                    <p class="font-bold">{{ playlist.name }}</p>

                    <div class="flex gap-4">
                        <Link href="#" class="flex gap-0.5">
                            <ThumbsUp :size="15"/>
                            <p class="text-xs">10</p>
                        </Link>
                        <Link href="#" class="flex gap-0.5">
                            <Download :size="15" />
                            <p class="text-xs">10</p>
                        </Link>
                        <Link href="#" class="flex gap-0.5">
                            <MessageCircle :size="15"/>
                            <p class="text-xs">10</p>
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
