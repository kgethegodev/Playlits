<script setup>
import {Label} from "@/components/ui/label/index.js";
import {Input} from "@/components/ui/input/index.js";
import {Link, useForm} from "@inertiajs/vue3";
import {Button} from "@/components/ui/button/index.js";
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { AlertCircle } from 'lucide-vue-next'
import {computed} from "vue";

const props = defineProps({
    errors: {}
});

const form = useForm({
    "email": '',
    "password": ''
});

const submit = () => {
    form.post('/auth/login', {
        onSuccess: () => {
            // window.location.href = '/spotify_auth';
        }
    })
}

const disabled = computed(() => Object.keys(form.data()).some(key => form[key] === '') || form.processing)
</script>

<template>
    <div class="min-h-[100dvh] flex flex-col items-center justify-center px-4 sm:px-0">
        <div class="flex flex-col w-full max-w-lg gap-[1rem]">
            <h1 class="text-2xl font-black">Login</h1>
            <Alert variant="destructive" v-if="errors.message">
                <AlertCircle class="w-4 h-4" />
                <AlertTitle>Error</AlertTitle>
                <AlertDescription>
                    {{errors.message}}
                </AlertDescription>
            </Alert>
            <form class="flex flex-col gap-6" @submit.prevent="submit">
                <div class="grid w-full items-center gap-1.5">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="Enter email."/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.email">{{form.errors.email}}</p>
                </div>

                <div class="grid w-full items-center gap-1.5">
                    <Label for="password">Password</Label>
                    <Input id="password" v-model="form.password" type="password" placeholder="Enter password."/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.password">{{form.errors.password}}</p>
                </div>

                <div class="w-full">
                    <Button class="w-full" :disabled>Login</Button>
                    <p class="text-sm text-center font-medium mt-2">New here? <Link href="/auth/register" class="italic text-blue-600">Create an account</Link> to get started.</p>
                </div>
            </form>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
