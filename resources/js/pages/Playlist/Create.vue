<script setup lang="ts">
import { Label } from '@/components/ui/label'
import {Input} from "@/components/ui/input";
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
import { Separator } from '@/components/ui/separator'
import {Badge} from "@/components/ui/badge";

const props = defineProps({
    playlists: Array,
    tags: Object,
})

const form = useForm({
    playlist_name: '',
    playlist_link: '',
    mood: '',
    genre: '',
    activity: '',
})

const disabled = computed(() => Object.keys(form.data()).some(key => form[key] === '') || form.processing)

const  submit = () => {
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
</script>

<template>
    <app-layout>
        <div class="flex flex-col gap-[1rem] w-full max-w-lg">
            <Card>
                    <CardHeader>
                        <CardTitle class="text-xl font-bold">add playlist.</CardTitle>
                        <CardDescription>paste your playlist, describe it, and we'll handle the rest.</CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-6">
                        <div class="grid w-full items-center gap-1.5">
                            <Label for="playlist_url">playlist link.</Label>
                            <Input id="playlist_url" v-model="form.playlist_link" placeholder="Enter playlist link."/>
                            <p class="text-red-500 text-sm pl-1" v-if="form.errors.playlist_link">{{form.errors.playlist_link}}</p>
                        </div>

                        <div class="grid w-full items-center gap-1.5">
                            <Label for="playlist_name">playlist name.</Label>
                            <Input id="playlist_name" v-model="form.playlist_name" placeholder="Enter playlist name."/>
                            <p class="text-red-500 text-sm pl-1" v-if="form.errors.playlist_name">{{form.errors.playlist_name}}</p>
                        </div>


<!--                        -->
                        <div class="w-full flex flex-col gap-6">
                            <div class="w-full grid items-center gap-2">
                                <Label for="mood">mood.</Label>
                                <div class="flex gap-2 flex-wrap">
                                    <badge v-for="mood in tags.mood"
                                           class="py-1 px-2 cursor-pointer transition-all hover:bg-blue-500"
                                           :class="{'bg-blue-500':form.mood === mood.name}"
                                           @click="form.mood = mood.name"
                                    >
                                        {{mood.name}}
                                    </badge>
                                </div>
                                <p class="text-red-500 text-sm pl-1" v-if="form.errors.mood">{{form.errors.mood}}</p>
                            </div>

                            <Separator />

                            <div class="w-full grid items-center gap-2">
                                <Label for="genre">genre.</Label>
                                <div class="flex gap-2 flex-wrap">
                                    <badge v-for="genre in tags.genre"
                                           class="py-1 px-2 cursor-pointer transition-all hover:bg-blue-500"
                                           :class="{'bg-blue-500':form.genre === genre.name}"
                                           @click="form.genre = genre.name"
                                    >
                                        {{genre.name}}
                                    </badge>
                                </div>
                                <p class="text-red-500 text-sm pl-1" v-if="form.errors.genre">{{form.errors.genre}}</p>
                            </div>

                            <Separator />

                            <div class="w-full grid items-center gap-2">
                                <Label for="activity">activity.</Label>
                                <div class="flex gap-2 flex-wrap">
                                    <badge v-for="activity in tags.activity"
                                           class="py-1 px-2 cursor-pointer transition-all hover:bg-blue-500"
                                           :class="{'bg-blue-500':form.activity === activity.name}"
                                           @click="form.activity = activity.name"
                                    >
                                        {{activity.name}}
                                    </badge>
                                </div>
                                <p class="text-red-500 text-sm pl-1" v-if="form.errors.activity">{{form.errors.activity}}</p>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Button :disabled="disabled" class="w-full" @click.prevent="submit">convert.</Button>
                    </CardFooter>
                </Card>
        </div>
    </app-layout>
    <Toaster />
</template>

<style scoped lang="scss">

</style>
