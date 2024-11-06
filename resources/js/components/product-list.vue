<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    initialProducts: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const products = ref(props.initialProducts)
const selectedCategory = ref(props.filters.category_id || '')
const sortPrice = ref(props.filters.sort_price || '')

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price)
}

const applyFilters = () => {
    const params = new URLSearchParams()

    if (selectedCategory.value) {
        params.append('category_id', selectedCategory.value)
    }
    if (sortPrice.value) {
        params.append('sort_price', sortPrice.value)
    }

    window.location.href = `${window.location.pathname}?${params.toString()}`
}

const hasProducts = computed(() => products.value.data.length > 0)
</script>

<template>
    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Category Filter -->
                <div>
                    <label class="form-label">Category</label>
                    <select
                        v-model="selectedCategory"
                        class="form-input"
                        @change="applyFilters"
                    >
                        <option value="">All Categories</option>
                        <template v-for="category in categories" :key="category.id">
                            <option :value="category.id">{{ category.name }}</option>
                            <option
                                v-for="child in category.children"
                                :key="child.id"
                                :value="child.id"
                            >
                                — {{ child.name }}
                            </option>
                        </template>
                    </select>
                </div>

                <!-- Price Sort -->
                <div>
                    <label class="form-label">Sort by Price</label>
                    <select
                        v-model="sortPrice"
                        class="form-input"
                        @change="applyFilters"
                    >
                        <option value="">Default</option>
                        <option value="asc">Low to High</option>
                        <option value="desc">High to Low</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                v-for="product in products.data"
                :key="product.id"
                class="bg-white rounded shadow overflow-hidden hover:shadow-lg transition-shadow"
            >
                <div v-if="product.image_url" class="aspect-w-16 aspect-h-9">
                    <img
                        :src="product.image_url"
                        :alt="product.name"
                        class="object-cover w-full h-48"
                        loading="lazy"
                    >
                </div>
                <div class="px-4 py-6">
                    <h3 class="text-lg font-semibold">{{ product.name }}</h3>
                    <p class="text-gray-600 mt-1">{{ formatPrice(product.price) }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span
                            v-for="category in product.categories"
                            :key="category.id"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                        >
                            {{ category.name }}
                        </span>
                    </div>
                    <p class="mt-2 text-gray-500 line-clamp-2">{{ product.description }}</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="hasProducts" class="flex justify-center mt-8">
            <nav class="relative z-0 inline-flex shadow-sm rounded-md">
                <a
                    v-for="(link, index) in products.links"
                    :key="index"
                    :href="link.url"
                    :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors',
                        {
                            'bg-blue-50 border-blue-500 text-blue-600': link.active,
                            'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active,
                            'rounded-l-md': index === 0,
                            'rounded-r-md': index === products.links.length - 1
                        }
                    ]"
                    v-html="link.label"
                ></a>
            </nav>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="text-center py-12 bg-white rounded shadow"
        >
            <p class="text-gray-500">No products found.</p>
            <a
                href="/products"
                class="mt-4 inline-flex items-center btn btn-primary"
            >
                Clear Filters
            </a>
        </div>
    </div>
</template>
