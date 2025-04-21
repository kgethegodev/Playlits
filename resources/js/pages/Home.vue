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
import {useForm, usePoll} from "@inertiajs/vue3";
import {Button} from "@/components/ui/button";
import {computed, onBeforeMount} from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import { toast } from 'vue-sonner'
import { Toaster } from '@/components/ui/sonner'
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'

const props = defineProps({
    playlists: Array,
    platforms: Array,
    has_code: Boolean,
})

const form = useForm({
    playlist_name: '',
    playlist_link: '',
    platform: ''
})

const disabled = computed(() => !form.playlist_link || !form.platform)

const  submit =   () => {
    form.post('/convert', {
        onSuccess: () => {
            form.reset()
            toast('Your playlist is being converted.', {
                description: "You'll receive a notification once it is ready.",
            })
        },
        onError: (error) => {
            console.log(error)
        }
    })
}

usePoll(5000)

onBeforeMount(() => {
    if (!props.has_code)
        window.location.href = '/spotify_auth';
})
</script>

<template>
    <app-layout :playlists>
        <div class="flex flex-col gap-[1rem] w-full">
            <Card>
                    <CardHeader>
                        <CardTitle class="text-xl">Spotify playlist converter.</CardTitle>
                        <CardDescription>convert your playlist into a spotify playlist.</CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-6">
                        <div class="grid w-full items-center gap-1.5">
                            <Label for="playlist_name">playlist name.</Label>
                            <Input id="playlist_name" v-model="form.playlist_name" placeholder="Enter playlist name."/>
                            <p class="text-red-500 text-sm pl-1" v-if="form.errors.playlist_name">{{form.errors.playlist_name}}</p>
                        </div>

                        <div class="grid w-full items-center gap-1.5">
                            <Label for="playlist_url">playlist link.</Label>
                            <Input id="playlist_url" v-model="form.playlist_link" placeholder="Enter playlist link."/>
                            <p class="text-red-500 text-sm pl-1" v-if="form.errors.playlist_link">{{form.errors.playlist_link}}</p>
                        </div>

                        <div class="grid w-full items-center gap-1.5">
                            <Label for="platform">platform.</Label>
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
                    </CardContent>
                    <CardFooter>
                        <Button :disabled class="w-full" @click.prevent="submit">convert.</Button>
                    </CardFooter>
                </Card>
        </div>
    </app-layout>
    <Toaster />
</template>

<style scoped lang="scss">

</style>
