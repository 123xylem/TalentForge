<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
        <!-- Navigation -->
        <header class="fixed top-0 w-full bg-white/80 backdrop-blur-sm dark:bg-gray-900/80">
            <nav class="container mx-auto flex items-center justify-between px-6 py-4">
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">TalentForge</div>
                <div class="flex gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard.index')"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-white transition-colors hover:bg-indigo-700"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-lg px-4 py-2 text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Log in
                        </Link>
                        <Link :href="route('register')" class="rounded-lg bg-indigo-600 px-4 py-2 text-white transition-colors hover:bg-indigo-700">
                            Register
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="container mx-auto px-6 pb-16 pt-32">
            <div class="mx-auto max-w-4xl text-center">
                <h1 class="mb-6 text-5xl font-bold text-gray-900 dark:text-white md:text-6xl">Find Your Perfect Match</h1>
                <p class="mb-8 text-xl text-gray-600 dark:text-gray-300">
                    Connect top talent with leading companies. Streamline your recruitment process with our intelligent matching platform.
                </p>
                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    <Link
                        :href="route('register')"
                        class="rounded-lg bg-indigo-600 px-8 py-4 text-lg font-medium text-white transition-colors hover:bg-indigo-700"
                    >
                        Get Started
                    </Link>
                    <Link
                        :href="route('login')"
                        class="rounded-lg border-2 border-indigo-600 px-8 py-4 text-lg font-medium text-indigo-600 transition-colors hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-gray-800"
                    >
                        Learn More
                    </Link>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-24 grid gap-8 md:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Smart Matching</h3>
                    <p class="text-gray-600 dark:text-gray-300">AI-powered algorithms to match the right candidates with the right opportunities.</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                            />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Verified Profiles</h3>
                    <p class="text-gray-600 dark:text-gray-300">Trust in our thorough verification process for both candidates and companies.</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Fast Hiring</h3>
                    <p class="text-gray-600 dark:text-gray-300">Streamline your recruitment process and hire faster with our efficient platform.</p>
                </div>
            </div>
        </main>
    </div>
</template>
