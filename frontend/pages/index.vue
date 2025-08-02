<template>
    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#1a0f0d] via-[#2e1f1b] to-[#0f1419]">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0">
            <!-- Floating Particles -->
            <div v-for="(particle, i) in particles" :key="i" 
                 class="absolute w-1 h-1 bg-yellow-400 rounded-full animate-pulse"
                 :style="{
                     left: particle.left + '%',
                     top: particle.top + '%',
                     animationDelay: particle.delay + 's',
                     animationDuration: particle.duration + 's'
                 }">
            </div>
            
            <!-- Radiant vs Dire Background Glow -->
            <div class="absolute top-0 left-0 w-1/2 h-full bg-gradient-to-r from-green-500/10 to-transparent animate-pulse"></div>
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-red-500/10 to-transparent animate-pulse"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex flex-col items-center justify-center min-h-screen px-8">
            <!-- Logo and Title Section -->
            <div class="text-center mb-12 animate-fade-in">
                <div class="flex flex-col items-center space-y-6">
                    <img src="/images/dota-logo.png" alt="Dota 2 Logo" class="w-24 h-24 object-contain mt-4">
                    <h1 class="text-6xl font-bold bg-gradient-to-r from-yellow-400 via-red-500 to-yellow-400 bg-clip-text text-transparent animate-glow mb-4 text-center">
                        Dota 2 Draft Simulator
                    </h1>
                    <p class="text-xl text-gray-300 text-center max-w-2xl">
                        Experience professional drafting like a pro player
                    </p>
                </div>
                <div class="w-32 h-1 bg-gradient-to-r from-transparent via-yellow-400 to-transparent mx-auto animate-pulse mt-6"></div>
            </div>

            <!-- Simulation Mode Selection -->
            <div class="mt-8 space-y-6">
                <h2 class="text-2xl font-bold text-white text-center mb-6">Choose Simulation Mode</h2>
                
                <div class="flex flex-col md:flex-row gap-6 max-w-6xl mx-auto">
                    <!-- Hero Analysis Mode -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-lg p-6 border border-gray-700 hover:border-blue-500 transition-all duration-500 cursor-pointer group flex-1 transform hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20 animate-fade-in-up" 
                         :class="{ 'border-blue-500 bg-blue-900/20 scale-105 shadow-xl shadow-blue-500/30': selectedMode === 'hero-analysis' }"
                         @click="selectMode('hero-analysis')"
                         style="animation-delay: 0.1s">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-500 transition-all duration-300 group-hover:rotate-12 group-hover:scale-110">
                                <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-125" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-300 transition-colors duration-300">Hero Analysis</h3>
                            <p class="text-gray-300 text-sm group-hover:text-gray-200 transition-colors duration-300">Analyze individual heroes with AI insights on strengths, weaknesses, and strategies</p>
                        </div>
                    </div>
                    
                    <!-- Draft Analysis Mode -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-lg p-6 border border-gray-700 hover:border-green-500 transition-all duration-500 cursor-pointer group flex-1 transform hover:scale-105 hover:shadow-2xl hover:shadow-green-500/20 animate-fade-in-up" 
                         :class="{ 'border-green-500 bg-green-900/20 scale-105 shadow-xl shadow-green-500/30': selectedMode === 'draft-analysis' }"
                         @click="selectMode('draft-analysis')"
                         style="animation-delay: 0.2s">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-green-600 rounded-full flex items-center justify-center group-hover:bg-green-500 transition-all duration-300 group-hover:rotate-12 group-hover:scale-110">
                                <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-125" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-green-300 transition-colors duration-300">Draft Analysis</h3>
                            <p class="text-gray-300 text-sm group-hover:text-gray-200 transition-colors duration-300">Analyze team compositions and get strategic recommendations for your 5-hero draft</p>
                        </div>
                    </div>
                    
                    <!-- Captain Mode Draft -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-lg p-6 border border-gray-700 hover:border-red-500 transition-all duration-500 cursor-pointer group flex-1 transform hover:scale-105 hover:shadow-2xl hover:shadow-red-500/20 animate-fade-in-up" 
                         :class="{ 'border-red-500 bg-red-900/20 scale-105 shadow-xl shadow-red-500/30': selectedMode === 'captain-mode' }"
                         @click="selectMode('captain-mode')"
                         style="animation-delay: 0.3s">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-red-600 rounded-full flex items-center justify-center group-hover:bg-red-500 transition-all duration-300 group-hover:rotate-12 group-hover:scale-110">
                                <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-125" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-red-300 transition-colors duration-300">Captain Mode Draft</h3>
                            <p class="text-gray-300 text-sm group-hover:text-gray-200 transition-colors duration-300">Full drafting simulation with AI opponents in professional Captain Mode format</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Captain Mode Configuration (only shown when captain-mode is selected) -->
            <div v-if="selectedMode === 'captain-mode'" class="bg-black/40 backdrop-blur-sm rounded-2xl p-8 border border-yellow-400/20 mb-8 animate-fade-in-up mt-8">
                <div class="text-center space-y-6">
                    <!-- Team Selection -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-yellow-400 mb-4">Choose Your Side</h3>
                        <div class="flex justify-center space-x-6">
                            <button @click="selectTeam('radiant')"
                                    :class="[
                                        'relative px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105',
                                        selectedTeam === 'radiant' 
                                            ? 'bg-green-600 text-white shadow-lg shadow-green-500/50 border-2 border-green-400' 
                                            : 'bg-green-600/20 text-green-400 border-2 border-green-600/50 hover:bg-green-600/30'
                                    ]">
                                <span class="relative z-10">⚡ RADIANT</span>
                                <div v-if="selectedTeam === 'radiant'" class="absolute inset-0 bg-green-400/20 rounded-lg animate-pulse"></div>
                            </button>
                            <button @click="selectTeam('dire')"
                                    :class="[
                                        'relative px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105',
                                        selectedTeam === 'dire' 
                                            ? 'bg-red-600 text-white shadow-lg shadow-red-500/50 border-2 border-red-400' 
                                            : 'bg-red-600/20 text-red-400 border-2 border-red-600/50 hover:bg-red-600/30'
                                    ]">
                                <span class="relative z-10">🔥 DIRE</span>
                                <div v-if="selectedTeam === 'dire'" class="absolute inset-0 bg-red-400/20 rounded-lg animate-pulse"></div>
                            </button>
                        </div>
                    </div>

                    <!-- Time Limit Configuration -->
                    <div class="space-y-4">
                        <label class="flex items-center justify-center gap-3 cursor-pointer group">
                            <input type="checkbox" v-model="useTimeLimit" 
                                   class="w-5 h-5 rounded border-2 border-yellow-400/50 bg-transparent checked:bg-yellow-400 checked:border-yellow-400 transition-all duration-300">
                            <span class="text-gray-300 group-hover:text-yellow-400 transition-colors duration-300">Use Time Limit</span>
                        </label>
                        <div v-if="useTimeLimit" class="flex items-center justify-center gap-3 animate-fade-in">
                            <input type="number" v-model="timeLimit" min="1" max="300"
                                   class="w-20 px-3 py-2 rounded-lg bg-black/50 border border-yellow-400/50 text-white text-center focus:border-yellow-400 focus:outline-none transition-all duration-300">
                            <span class="text-gray-400">seconds per pick</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start Button -->
            <button v-if="selectedMode" @click="startSimulation" 
                    class="group relative px-12 py-4 bg-gradient-to-r from-yellow-500 to-red-500 text-black font-bold text-xl rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-yellow-500/50 animate-bounce-slow mt-8">
                <span class="relative z-10">🎮 START {{ getModeTitle() }}</span>
                <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-red-400 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-pulse"></div>
            </button>

            <!-- Footer Info -->
            <div class="mt-8 text-center text-gray-500 text-sm animate-fade-in">
                <p>Captain's Mode draft simulation like professional tournaments</p>
                <p class="mt-1">Pick heroes, create strategies, win matches!</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useDraftStore } from '~/stores/draft'

const draftStore = useDraftStore()
const selectedTeam = ref(draftStore.selectedTeam)
const useTimeLimit = ref(false)
const timeLimit = ref(30)
const selectedMode = ref(null)

// Initialize particles data to avoid hydration mismatch
const particles = ref([])

// Generate particles only on client-side
onMounted(() => {
    particles.value = Array.from({ length: 50 }, () => ({
        left: Math.random() * 100,
        top: Math.random() * 100,
        delay: Math.random() * 5,
        duration: 3 + Math.random() * 4
    }))
})

function selectTeam(team) {
    selectedTeam.value = team
    draftStore.setSelectedTeam(team)
}

function selectMode(mode) {
    selectedMode.value = mode
}

function getModeTitle() {
    switch(selectedMode.value) {
        case 'hero-analysis': return 'HERO ANALYSIS'
        case 'draft-analysis': return 'DRAFT ANALYSIS'
        case 'captain-mode': return 'SIMULATION'
        default: return 'SIMULATION'
    }
}

async function startSimulation() {
    if (!selectedMode.value) return
    
    // Play match ready sound
    try {
        const audio = new Audio('/sound/match_ready.mp3')
        await new Promise((resolve) => {
            audio.onended = resolve
            audio.onerror = resolve // Continue even if audio fails
            audio.play().catch(() => resolve()) // Continue even if play fails
        })
    } catch (error) {
        console.log('Audio playback failed:', error)
    }
    
    // Navigate based on selected mode
    switch(selectedMode.value) {
        case 'hero-analysis':
            await navigateTo('/hero-analysis')
            break
        case 'draft-analysis':
            await navigateTo('/draft-analysis')
            break
        case 'captain-mode':
            // Set draft store configuration for captain mode
            draftStore.setTimeLimit(useTimeLimit.value ? timeLimit.value : null)
            await navigateTo('/simulasi')
            break
    }
}
</script>
