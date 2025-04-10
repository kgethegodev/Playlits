<template>
    <div class="min-h-[100vh] flex flex-col items-center justify-center px-4 sm:px-0">
        <div class="flex flex-col w-full max-w-lg gap-[1rem]">
            <h1 class="text-2xl font-black">Spotify Playlist Converter</h1>
            <form class="flex flex-col gap-6" @submit.prevent="submit">
                <div class="grid w-full items-center gap-1.5">
                    <Label for="playlist_name">Playlist Name</Label>
                    <Input id="playlist_name" v-model="form.playlist_name" placeholder="Enter playlist name."/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.playlist_name">{{form.errors.playlist_name}}</p>
                </div>

                <div class="grid w-full items-center gap-1.5">
                    <Label for="playlist_url">Playlist Link</Label>
                    <Input id="playlist_url" v-model="form.playlist_link" placeholder="Enter playlist link."/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.playlist_link">{{form.errors.playlist_link}}</p>
                </div>

                 <div class="grid w-full items-center gap-1.5">
                    <Label for="platform">Platform</Label>
                    <Select id="platform" v-model="form.platform">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a platform" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem :value="platform.value" v-for="platform in platforms">
                                    {{platform.name}}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                     <p class="text-red-500 text-sm pl-1" v-if="form.errors.platform">{{form.errors.platform}}</p>
                </div>

                <Button :disabled>Convert</Button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Label } from '@/components/ui/label'
import {Input} from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {useForm} from "@inertiajs/vue3";
import {Button} from "@/components/ui/button";
import {computed} from "vue";

defineProps({
    platforms: []
})

const form = useForm({
    playlist_name: 'Soul',
    playlist_link: 'https://music.apple.com/za/playlist/for-the-soul/pl.u-aZb0N67uPVBP0k4',
    platform: 'apple'
})

const disabled = computed(() => !form.playlist_link || !form.platform)

const  submit =   () => {
    form.post('/convert')
}
</script>

<style scoped lang="scss">

</style>
