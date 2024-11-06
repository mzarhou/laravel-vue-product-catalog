<script setup>
import { ref } from 'vue'
import { useFilePreview } from '../composables/use-file-preview'

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    csrfToken: {
        type: String,
        required: true
    },
    submitUrl: {
        type: String,
        required: true
    }
})

const formData = ref({
    name: '',
    description: '',
    price: '',
    categories: [],
    image: null
})

const errors = ref({})
const isSubmitting = ref(false)
const { preview, handleFilePreview } = useFilePreview()

const handleSubmit = async () => {
    isSubmitting.value = true

    try {
        const form = new FormData()

        for (const [key, value] of Object.entries(formData.value)) {
            if (key === 'categories') {
                value.forEach(categoryId => {
                    form.append('categories[]', categoryId)
                })
            } else if (value !== null) {
                form.append(key, value)
            }
        }

        const response = await fetch(props.submitUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': props.csrfToken,
                'Accept': 'application/json'
            },
            body: form
        })

        const data = await response.json()

        if (!response.ok) {
            errors.value = data.errors || {}
            throw new Error(data.message || 'Failed to create product')
        }

        window.location.href = '/'
    } catch (error) {
        console.error('Error:', error)
    } finally {
        isSubmitting.value = false
    }
}

const handleImageChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        formData.value.image = file
        handleFilePreview(file)
    }
}
</script>

<template>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Create New Product</h2>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input
                        id="name"
                        v-model="formData.name"
                        type="text"
                        class="form-input"
                        :class="{ 'border-red-500': errors.name }"
                    >
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                        {{ errors.name }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        id="description"
                        v-model="formData.description"
                        rows="4"
                        class="form-input"
                        :class="{ 'border-red-500': errors.description }"
                    ></textarea>
                    <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                        {{ errors.description }}
                    </p>
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="form-label">Price</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            $
                        </span>
                        <input
                            id="price"
                            v-model="formData.price"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-input pl-7"
                            :class="{ 'border-red-500': errors.price }"
                        >
                    </div>
                    <p v-if="errors.price" class="mt-1 text-sm text-red-600">
                        {{ errors.price }}
                    </p>
                </div>

                <!-- Categories -->
                <div>
                    <label class="form-label">Categories</label>
                    <div class="mt-2 space-y-2">
                        <template v-for="category in categories" :key="category.id">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        :id="`category-${category.id}`"
                                        v-model="formData.categories"
                                        type="checkbox"
                                        :value="category.id"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600"
                                    >
                                </div>
                                <label :for="`category-${category.id}`" class="ml-3 text-sm">
                                    {{ category.name }}
                                </label>
                            </div>
                            <!-- Nested categories -->
                            <div
                                v-for="child in category.children"
                                :key="child.id"
                                class="ml-6"
                            >
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            :id="`category-${child.id}`"
                                            v-model="formData.categories"
                                            type="checkbox"
                                            :value="child.id"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600"
                                        >
                                    </div>
                                    <label :for="`category-${child.id}`" class="ml-3 text-sm">
                                        {{ child.name }}
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>
                    <p v-if="errors.categories" class="mt-1 text-sm text-red-600">
                        {{ errors.categories }}
                    </p>
                </div>

                <!-- Image -->
                <div>
                    <label class="form-label">Product Image</label>
                    <div class="mt-2">
                        <input
                            type="file"
                            accept="image/*"
                            @change="handleImageChange"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                    </div>
                    <div v-if="preview" class="mt-4">
                        <img :src="preview" alt="Preview" class="h-32 w-32 object-cover rounded">
                    </div>
                    <p v-if="errors.image" class="mt-1 text-sm text-red-600">
                        {{ errors.image }}
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="isSubmitting"
                    >
                        <span v-if="isSubmitting">
                            Creating...
                        </span>
                        <span v-else>
                            Create Product
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
