import { ref } from 'vue'

export function useFilePreview() {
    const preview = ref(null)

    const handleFilePreview = (file) => {
        if (!file) {
            preview.value = null
            return
        }

        const reader = new FileReader()
        reader.onload = (e) => {
            preview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }

    return {
        preview,
        handleFilePreview
    }
}
