<template>
  <div class="activity-photo-uploader">
    <!-- Upload Status -->
    <div v-if="photoCount > 0" class="mb-4">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">
          Laporan Foto ({{ photoCount }}/{{ maxPhotos }})
        </span>
        <span v-if="!canAddMore" class="text-xs text-yellow-600 font-semibold">
          Maksimum tercapai
        </span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div
          class="bg-blue-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: (photoCount / maxPhotos) * 100 + '%' }"
        ></div>
      </div>
    </div>

    <!-- Photo Gallery -->
    <div v-if="photos.length > 0" class="mb-6">
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
        <div
          v-for="(photo, index) in photos"
          :key="photo.id"
          class="relative group"
        >
          <img
            :src="photo.photo_url"
            :alt="`Foto ${index + 1}`"
            class="w-full h-40 object-cover rounded-lg shadow-md"
          />
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg transition-all duration-200 flex items-center justify-center">
            <div class="opacity-0 group-hover:opacity-100 flex gap-2 transition-opacity duration-200">
              <button
                type="button"
                @click="editPhoto(photo)"
                class="p-2 bg-blue-500 text-white rounded-full hover:bg-blue-600"
                title="Edit"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
              </button>
              <button
                type="button"
                @click="deletePhoto(photo)"
                class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600"
                title="Delete"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
          </div>
          <div class="absolute top-2 left-2 bg-gray-800 text-white px-2 py-1 rounded text-xs font-semibold">
            {{ index + 1 }}
          </div>
          <div v-if="photo.description" class="absolute bottom-2 left-2 right-2 bg-black bg-opacity-50 text-white p-1 rounded text-xs truncate">
            {{ photo.description }}
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Area / Buttons -->
    <div class="space-y-3">
      <div v-if="canAddMore" class="grid grid-cols-2 gap-3">
        <!-- Camera Button -->
        <button
          type="button"
          @click="capturePhoto"
          :disabled="uploading"
          class="flex items-center justify-center gap-2 p-3 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Ambil Foto</span>
        </button>

        <!-- Gallery Button -->
        <button
          type="button"
          @click="pickFromGallery"
          :disabled="uploading"
          class="flex items-center justify-center gap-2 p-3 bg-green-500 hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span>Pilih dari Galeri</span>
        </button>
      </div>

      <!-- File Input (hidden) -->
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        @change="handleFileSelect"
        class="hidden"
      />

      <!-- Upload Progress -->
      <div v-if="uploading" class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm text-blue-700 font-medium">Mengunggah foto...</span>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
        <strong>Error:</strong> {{ error }}
      </div>

      <!-- Success Message -->
      <div v-if="success" class="p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
        {{ success }}
      </div>
    </div>

    <!-- Edit Description Modal -->
    <div v-if="editingPhoto" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <h3 class="text-lg font-semibold mb-4">Edit Deskripsi Foto</h3>
        <textarea
          v-model="editDescription"
          class="w-full border border-gray-300 rounded-lg p-2 mb-4 resize-none"
          rows="3"
          placeholder="Tambahkan deskripsi untuk foto ini..."
        ></textarea>
        <div class="flex gap-2">
          <button
            type="button"
            @click="savePhotoDescription"
            :disabled="editingPhoto?.saving"
            class="flex-1 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white py-2 rounded-lg font-semibold"
          >
            Simpan
          </button>
          <button
            type="button"
            @click="editingPhoto = null"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 rounded-lg font-semibold"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Camera } from '@capacitor/camera'
import { CameraResultType, CameraSource } from '@capacitor/camera'
import axios from 'axios'

interface ActivityPhoto {
  id: number
  photo_url: string
  photo_name: string
  description: string | null
  sequence: number
  file_size: number
  created_at: string
}

interface EditingPhoto extends ActivityPhoto {
  saving?: boolean
}

const props = defineProps<{
  activityId: number
  maxPhotos?: number
}>()

const emit = defineEmits<{
  updated: [photos: ActivityPhoto[]]
  error: [message: string]
}>()

// State
const photos = ref<ActivityPhoto[]>([])
const uploading = ref(false)
const error = ref<string | null>(null)
const success = ref<string | null>(null)
const fileInput = ref<HTMLInputElement>()
const editingPhoto = ref<EditingPhoto | null>(null)
const editDescription = ref('')
const maxPhotos = computed(() => props.maxPhotos || 5)
const photoCount = computed(() => photos.value.length)
const canAddMore = computed(() => photoCount.value < maxPhotos.value)

// Load photos on mount
onMounted(async () => {
  await fetchPhotos()
})

// Fetch photos from API
const fetchPhotos = async () => {
  try {
    const response = await axios.get(
      `/api/activities/${props.activityId}/photos`
    )
    if (response.data.success) {
      photos.value = response.data.data.photos
      emit('updated', photos.value)
    }
  } catch (err) {
    console.error('Error fetching photos:', err)
  }
}

// Capture photo with camera
const capturePhoto = async () => {
  try {
    const image = await Camera.getPhoto({
      quality: 90,
      allowEditing: false,
      resultType: CameraResultType.Uri,
      source: CameraSource.Camera,
    })

    if (image.webPath) {
      await uploadPhoto(image.webPath)
    }
  } catch (err) {
    console.error('Camera error:', err)
    showError('Gagal mengakses kamera')
  }
}

// Pick photo from gallery
const pickFromGallery = async () => {
  try {
    const image = await Camera.getPhoto({
      quality: 90,
      allowEditing: false,
      resultType: CameraResultType.Uri,
      source: CameraSource.Photos,
    })

    if (image.webPath) {
      await uploadPhoto(image.webPath)
    }
  } catch (err) {
    console.error('Gallery error:', err)
    showError('Gagal membuka galeri')
  }
}

// Handle file select from web input
const handleFileSelect = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (input.files && input.files[0]) {
    const file = input.files[0]
    const reader = new FileReader()
    
    reader.onload = () => {
      // Upload the blob data
      uploadPhotoFile(file)
    }
    reader.readAsDataURL(file)
  }
  // Reset input
  input.value = ''
}

// Upload photo file
const uploadPhotoFile = async (file: File) => {
  const formData = new FormData()
  formData.append('photo', file)

  await performUpload(formData)
}

// Upload photo from URI
const uploadPhoto = async (uri: string) => {
  try {
    const response = await fetch(uri)
    const blob = await response.blob()
    const file = new File([blob], 'photo.jpg', { type: 'image/jpeg' })

    const formData = new FormData()
    formData.append('photo', file)

    await performUpload(formData)
  } catch (err) {
    showError('Gagal memproses foto')
  }
}

// Perform upload
const performUpload = async (formData: FormData) => {
  if (!canAddMore.value) {
    showError(`Maksimum ${maxPhotos.value} foto telah tercapai`)
    return
  }

  uploading.value = true
  error.value = null
  success.value = null

  try {
    const response = await axios.post(
      `/api/activities/${props.activityId}/photos`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    )

    if (response.data.success) {
      photos.value.push(response.data.data)
      showSuccess('Foto berhasil diunggah')
      emit('updated', photos.value)
    }
  } catch (err: any) {
    const message =
      err.response?.data?.message || 'Gagal mengunggah foto'
    showError(message)
    emit('error', message)
  } finally {
    uploading.value = false
  }
}

// Delete photo
const deletePhoto = async (photo: ActivityPhoto) => {
  if (!confirm('Yakin ingin menghapus foto ini?')) {
    return
  }

  try {
    const response = await axios.delete(`/api/photos/${photo.id}`)

    if (response.data.success) {
      photos.value = photos.value.filter(p => p.id !== photo.id)
      showSuccess('Foto berhasil dihapus')
      emit('updated', photos.value)
    }
  } catch (err: any) {
    const message = err.response?.data?.message || 'Gagal menghapus foto'
    showError(message)
  }
}

// Edit photo description
const editPhoto = (photo: ActivityPhoto) => {
  editingPhoto.value = { ...photo }
  editDescription.value = photo.description || ''
}

// Save photo description
const savePhotoDescription = async () => {
  if (!editingPhoto.value) return

  editingPhoto.value.saving = true

  try {
    const response = await axios.patch(`/api/photos/${editingPhoto.value.id}`, {
      description: editDescription.value,
    })

    if (response.data.success) {
      const index = photos.value.findIndex(p => p.id === editingPhoto.value?.id)
      if (index !== -1) {
        photos.value[index].description = editDescription.value
      }
      showSuccess('Deskripsi foto berhasil diperbarui')
      editingPhoto.value = null
      emit('updated', photos.value)
    }
  } catch (err: any) {
    const message =
      err.response?.data?.message || 'Gagal memperbarui deskripsi'
    showError(message)
  } finally {
    if (editingPhoto.value) {
      editingPhoto.value.saving = false
    }
  }
}

// Helper functions
const showError = (message: string) => {
  error.value = message
  setTimeout(() => {
    error.value = null
  }, 5000)
}

const showSuccess = (message: string) => {
  success.value = message
  setTimeout(() => {
    success.value = null
  }, 3000)
}
</script>

<style scoped>
.activity-photo-uploader {
  width: 100%;
}
</style>
