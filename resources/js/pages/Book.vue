<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface Props {
    id: number;
}

const props = defineProps<Props>();

const bookRef = ref<HTMLElement>();
const imagesLoaded = ref(false);
const currentPage = ref(0);
const totalPages = ref(0);
const isFlipping = ref(false);
const flipDirection = ref<'left' | 'right'>('right');

const images = [
    '/asset/A.jpg',
    '/asset/B.jpg',
    '/asset/C (1).jpg',
    '/asset/D.jpg'
];

const imagePages = ref<string[]>([]);

const goToPreviousPage = () => {
    if (currentPage.value > 1 && !isFlipping.value) {
        flipDirection.value = 'left';
        isFlipping.value = true;

        if (navigator.vibrate) {
            navigator.vibrate(30);
        }

        setTimeout(() => {
            currentPage.value--;
            isFlipping.value = false;
        }, 600);
    }
};

const goToNextPage = () => {
    if (currentPage.value < totalPages.value && !isFlipping.value) {
        flipDirection.value = 'right';
        isFlipping.value = true;

        if (navigator.vibrate) {
            navigator.vibrate(30);
        }

        setTimeout(() => {
            currentPage.value++;
            isFlipping.value = false;
        }, 600);
    } else if (currentPage.value === 0) {
        // Start reading from cover
        currentPage.value = 1;
    }
};

const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value && !isFlipping.value) {
        currentPage.value = page;
    }
};

const handleKeyPress = (event: KeyboardEvent) => {
    if (event.key === 'ArrowLeft') {
        goToPreviousPage();
    } else if (event.key === 'ArrowRight') {
        goToNextPage();
    }
};

// Load images
const loadImages = async () => {
    try {
        totalPages.value = images.length;
        imagePages.value = images;
        imagesLoaded.value = true;
    } catch (error) {
        console.error('Error loading images:', error);
        imagesLoaded.value = true;
    }
};

onMounted(async () => {
    window.addEventListener('keydown', handleKeyPress);

    // Load images
    await loadImages();

    return () => {
        window.removeEventListener('keydown', handleKeyPress);
    };
});
</script>

<template>
    <Head title="Book Viewer" />

    <!-- Loading Screen -->
    <div v-if="!imagesLoaded" class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-100 to-yellow-100 flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-amber-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <h2 class="text-2xl font-bold text-amber-800 mb-2">Loading Menu Book</h2>
            <p class="text-amber-600">Please wait while we prepare your experience...</p>
        </div>
    </div>

    <!-- Main Book Interface -->
    <div v-else class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-100 to-yellow-100 flex items-center justify-center p-4">
        <!-- Custom Book Container -->
        <div class="book-wrapper">
            <div
                ref="bookRef"
                class="custom-book"
                style="width: 1000px; height: 700px;"
            >
                <!-- Book Cover -->
                <div
                    class="book-cover"
                    :class="{ 'hidden': currentPage > 0 }"
                >
                    <div class="cover-content">
                        <h1 class="text-4xl font-bold mb-4 text-white">Delawa Menue</h1>
                        <p class="text-xl text-white/90">Delicious Delights</p>
                                                            <button
                                        @click="goToNextPage"
                                        class="start-reading-btn"
                                    >
                                        Order Now
                                    </button>
                    </div>
                </div>

                <!-- PDF Pages -->
                <div class="book-pages">
                    <div
                        v-for="(page, index) in imagePages"
                        :key="index"
                        class="page"
                        :class="{
                            'active': currentPage === index + 1,
                            'flipping-left': isFlipping && flipDirection === 'left' && currentPage === index + 2,
                            'flipping-right': isFlipping && flipDirection === 'right' && currentPage === index + 1
                        }"
                        v-show="currentPage === index + 1 ||
                               (isFlipping && flipDirection === 'left' && currentPage === index + 2) ||
                               (isFlipping && flipDirection === 'right' && currentPage === index + 1)"
                    >
                        <div class="page-content">
                            <img
                                :src="page"
                                :alt="`Page ${index + 1}`"
                                class="page-image"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Controls -->
            <div class="navigation-controls">
                <button
                    @click="goToPreviousPage"
                    :disabled="currentPage <= 1 || isFlipping"
                    class="nav-button prev-button"
                >
                    <ChevronLeft class="w-8 h-8" />
                </button>

                <div class="page-indicators">
                    <div
                        v-for="(_, index) in totalPages"
                        :key="index"
                        class="page-dot"
                        :class="{ active: currentPage === index + 1 }"
                        @click="goToPage(index + 1)"
                    ></div>
                </div>

                <button
                    @click="goToNextPage"
                    :disabled="currentPage >= totalPages || isFlipping"
                    class="nav-button next-button"
                >
                    <ChevronRight class="w-8 h-8" />
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom Book Styles */
.book-wrapper {
    width: 100%;
    max-width: 1200px;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2rem;
}

.custom-book {
    position: relative;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow:
        0 20px 40px rgba(0,0,0,0.3),
        0 10px 20px rgba(0,0,0,0.2),
        0 5px 10px rgba(0,0,0,0.1);
    background: #662d37;
}

.book-cover {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #662d37 0%, #8b3d4a 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: opacity 0.3s ease;
}

.book-cover.hidden {
    opacity: 0;
    pointer-events: none;
}

.cover-content {
    text-align: center;
    color: white;
}

.start-reading-btn {
    margin-top: 2rem;
    padding: 1rem 2rem;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 2rem;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.start-reading-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: scale(1.05);
}

.book-pages {
    position: relative;
    width: 100%;
    height: 100%;
}

.page {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #662a37;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    transform-origin: center;
}

.page.active {
    opacity: 1;
    z-index: 5;
}

.page.flipping-left {
    animation: flip-left 0.6s ease-in-out;
    z-index: 15;
}

.page.flipping-right {
    animation: flip-right 0.6s ease-in-out;
    z-index: 15;
}

.page-content {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.page-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 0.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

@keyframes flip-left {
    0% {
        transform: rotateY(0deg);
        opacity: 1;
    }
    50% {
        transform: rotateY(-90deg);
        opacity: 0.5;
    }
    100% {
        transform: rotateY(-180deg);
        opacity: 0;
    }
}

@keyframes flip-right {
    0% {
        transform: rotateY(0deg);
        opacity: 1;
    }
    50% {
        transform: rotateY(90deg);
        opacity: 0.5;
    }
    100% {
        transform: rotateY(180deg);
        opacity: 0;
    }
}

/* Navigation Controls */
.navigation-controls {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 1rem 2rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.nav-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    border: none;
    border-radius: 50%;
    background: #662d37;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 45, 55, 0.3);
}

.nav-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 45, 55, 0.4);
}

.nav-button:active {
    transform: scale(0.95);
}

/* Page Indicators */
.page-indicators {
    display: flex;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 1rem;
}

.page-dot {
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    background: #d1d5db;
    cursor: pointer;
    transition: all 0.3s ease;
}

.page-dot:hover {
    transform: scale(1.2);
    background: #9ca3af;
}

.page-dot.active {
    background: #662d37;
    transform: scale(1.3);
    box-shadow: 0 2px 8px rgba(102, 45, 55, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .book-wrapper {
        padding: 1rem;
    }

    .custom-book {
        width: 100% !important;
        height: 60vh !important;
    }

    .navigation-controls {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
    }

    .nav-button {
        width: 2.5rem;
        height: 2.5rem;
    }

    .page-indicators {
        order: -1;
    }
}

/* Loading Animation */
@keyframes fade-in {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>
