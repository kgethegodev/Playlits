<script setup>
import {Button} from "@/components/ui/button/index.js";
import {Label} from "@/components/ui/label/index.js";
import {Input} from "@/components/ui/input/index.js";
import {useForm} from "@inertiajs/vue3";

const form = useForm({
    name: '',
    email: '',
    password:  '',
    password_confirmation: ''
});
const submit = () => {
    form.post('/auth/register')
}
</script>

<template>
    <div class="min-h-[100vh] flex flex-col items-center justify-center px-4 sm:px-0">
        <div class="flex flex-col w-full max-w-lg gap-[1rem]">
            <h1 class="text-2xl font-black">Register</h1>
            <form class="flex flex-col gap-6" @submit.prevent="submit">
                <div class="grid w-full items-center gap-1.5">
                    <Label for="name">Name</Label>
                    <Input id="name" v-model="form.name" type="text" placeholder="Enter name." autocomplete="name"/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.name">{{form.errors.name}}</p>
                </div>

                <div class="grid w-full items-center gap-1.5">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="Enter email." autocomplete="email"/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.email">{{form.errors.email}}</p>
                </div>

                <div class="grid w-full items-center gap-1.5">
                    <Label for="password">Password</Label>
                    <Input id="password" v-model="form.password" type="password" placeholder="Enter password."/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.password">{{form.errors.password}}</p>
                </div>

                <div class="grid w-full items-center gap-1.5">
                    <Label for="password_confirmation">Confirm Password</Label>
                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Enter password."/>
                    <p class="text-red-500 text-sm pl-1" v-if="form.errors.password_confirmation">{{form.errors.password_confirmation}}</p>
                </div>
                <Button :disabled>Register</Button>
            </form>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>
