<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>

    <!-- Modal Container -->
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-3xl border border-white/10 shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all">

        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-orange-500 to-purple-600 p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">KYC Verification</h2>
                <p class="text-white/80 text-sm">Verify your identity to proceed</p>
              </div>
            </div>
            <button @click="$emit('close')" class="text-white/80 hover:text-white transition-colors p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <!-- Information Banner -->
          <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              <div>
                <p class="text-blue-300 text-sm font-semibold">Why KYC is Required</p>
                <p class="text-blue-200/80 text-xs mt-1">To comply with regulations and ensure the security of your account, we need to verify your identity. All documents are encrypted and stored securely.</p>
              </div>
            </div>
          </div>

          <!-- Basic Information (Always Required) -->
          <div class="bg-white/5 backdrop-blur-xl rounded-xl border border-white/10 p-5 space-y-4">
            <h3 class="text-white font-bold text-base flex items-center gap-2">
              <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Personal Information
            </h3>

            <!-- Full Name (Always shown - from user model) -->
            <div>
              <label class="text-gray-300 text-sm font-semibold mb-2 block">
                Full Name
              </label>
              <input
                :value="userInfo.full_name"
                type="text"
                disabled
                class="w-full px-4 py-3 bg-gray-700/30 border border-gray-600/50 rounded-xl text-gray-200 font-medium cursor-not-allowed"
              />
            </div>

            <!-- Email (Always shown - from user model) -->
            <div>
              <label class="text-gray-300 text-sm font-semibold mb-2 block">
                Email Address
              </label>
              <input
                :value="userInfo.email"
                type="email"
                disabled
                class="w-full px-4 py-3 bg-gray-700/30 border border-gray-600/50 rounded-xl text-gray-200 font-medium cursor-not-allowed"
              />
            </div>

            <!-- Date of Birth (Always shown) -->
            <div>
              <label class="text-gray-300 text-sm font-semibold mb-2 block">
                Date of Birth <span class="text-red-400">*</span>
              </label>
              <input
                v-if="!userInfo.date_of_birth"
                v-model="form.date_of_birth"
                type="date"
                :max="maxDateString"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-orange-500"
              />
              <div v-else class="w-full px-4 py-3 bg-gradient-to-r from-blue-900/40 to-purple-900/40 border-2 border-blue-500/30 rounded-xl">
                <p class="text-blue-200 text-sm font-bold">{{ formatDate(userInfo.date_of_birth) }}</p>
                <p class="text-blue-400/70 text-xs mt-1">From your account profile</p>
              </div>
            </div>

            <!-- NIN Number -->
            <div v-if="kycRequirements.nin_required">
              <label class="text-gray-300 text-sm font-semibold mb-2 block">
                National Identification Number (NIN) <span class="text-red-400">*</span>
              </label>
              <input
                v-model="form.nin"
                type="text"
                maxlength="11"
                placeholder="Enter your 11-digit NIN"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500"
              />
              <p class="text-gray-500 text-xs mt-1">Your National Identification Number</p>
            </div>

            <!-- BVN Number -->
            <div v-if="kycRequirements.bvn_required">
              <label class="text-gray-300 text-sm font-semibold mb-2 block">
                Bank Verification Number (BVN) <span class="text-red-400">*</span>
              </label>
              <input
                v-model="form.bvn"
                type="text"
                maxlength="11"
                placeholder="Enter your 11-digit BVN"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
              <p class="text-gray-500 text-xs mt-1">Your Bank Verification Number</p>
            </div>
          </div>

          <!-- Document Uploads -->
          <div class="space-y-4">

            <!-- NIN Upload -->
            <div v-if="kycRequirements.nin_required" class="bg-white/5 backdrop-blur-xl rounded-xl border border-white/10 p-5">
              <label class="text-white font-semibold text-sm mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                National Identification Number (NIN)
                <span class="text-red-400 text-xs">*</span>
              </label>
              <p class="text-gray-400 text-xs mb-3">Upload a clear photo of your National ID card (front and back)</p>

              <div v-if="!filePreviews.nin" class="relative">
                <input
                  type="file"
                  ref="ninInput"
                  @change="handleFileSelect($event, 'nin')"
                  accept="image/*,.pdf"
                  class="hidden"
                />
                <button
                  type="button"
                  @click="$refs.ninInput.click()"
                  :disabled="submitting"
                  class="w-full border-2 border-dashed border-white/20 rounded-xl p-6 hover:border-orange-500/50 transition-colors text-center group disabled:opacity-50"
                >
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-2 group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <p class="text-gray-300 text-sm font-medium">Click to select NIN document</p>
                  <p class="text-gray-500 text-xs mt-1">JPG, PNG or PDF (Max 10MB)</p>
                </button>
              </div>

              <!-- Preview -->
              <div v-else class="relative group">
                <div class="bg-white/10 rounded-xl p-4 border border-green-500/30">
                  <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                      <img v-if="filePreviews.nin !== 'pdf'" :src="filePreviews.nin" alt="NIN Preview" class="w-20 h-20 object-cover rounded-lg" />
                      <div v-else class="w-20 h-20 bg-orange-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-white font-medium text-sm truncate">
                        {{ fileObjects.nin?.name || 'NIN Document' }}
                        <span v-if="!fileObjects.nin && filePreviews.nin" class="text-blue-400 text-xs ml-1">(Existing)</span>
                      </p>
                      <p class="text-green-400 text-xs flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ fileObjects.nin ? 'Ready to upload' : 'Previously uploaded - Click X to replace' }}
                      </p>
                    </div>
                    <button
                      type="button"
                      @click="removeFile('nin')"
                      :disabled="submitting"
                      class="text-red-400 hover:text-red-300 transition-colors p-2 disabled:opacity-50"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Utility Bill Upload -->
            <div v-if="kycRequirements.utility_bill_required" class="bg-white/5 backdrop-blur-xl rounded-xl border border-white/10 p-5">
              <label class="text-white font-semibold text-sm mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Utility Bill
                <span class="text-red-400 text-xs">*</span>
              </label>
              <p class="text-gray-400 text-xs mb-3">Recent utility bill (not older than 3 months)</p>

              <div v-if="!filePreviews.utility_bill" class="relative">
                <input
                  type="file"
                  ref="utilityBillInput"
                  @change="handleFileSelect($event, 'utility_bill')"
                  accept="image/*,.pdf"
                  class="hidden"
                />
                <button
                  type="button"
                  @click="$refs.utilityBillInput.click()"
                  :disabled="submitting"
                  class="w-full border-2 border-dashed border-white/20 rounded-xl p-6 hover:border-purple-500/50 transition-colors text-center group disabled:opacity-50"
                >
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-2 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <p class="text-gray-300 text-sm font-medium">Click to select utility bill</p>
                  <p class="text-gray-500 text-xs mt-1">JPG, PNG or PDF (Max 10MB)</p>
                </button>
              </div>

              <!-- Preview -->
              <div v-else class="relative group">
                <div class="bg-white/10 rounded-xl p-4 border border-green-500/30">
                  <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                      <img v-if="filePreviews.utility_bill !== 'pdf'" :src="filePreviews.utility_bill" alt="Utility Bill Preview" class="w-20 h-20 object-cover rounded-lg" />
                      <div v-else class="w-20 h-20 bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-white font-medium text-sm truncate">
                        {{ fileObjects.utility_bill?.name || 'Utility Bill' }}
                        <span v-if="!fileObjects.utility_bill && filePreviews.utility_bill" class="text-blue-400 text-xs ml-1">(Existing)</span>
                      </p>
                      <p class="text-green-400 text-xs flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ fileObjects.utility_bill ? 'Ready to upload' : 'Previously uploaded - Click X to replace' }}
                      </p>
                    </div>
                    <button
                      type="button"
                      @click="removeFile('utility_bill')"
                      :disabled="submitting"
                      class="text-red-400 hover:text-red-300 transition-colors p-2 disabled:opacity-50"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Selfie Upload -->
            <div v-if="kycRequirements.selfie_required" class="bg-white/5 backdrop-blur-xl rounded-xl border border-white/10 p-5">
              <label class="text-white font-semibold text-sm mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Selfie with ID
                <span class="text-red-400 text-xs">*</span>
              </label>
              <p class="text-gray-400 text-xs mb-3">Take a selfie while holding your ID document next to your face</p>

              <div v-if="!filePreviews.selfie" class="relative">
                <input
                  type="file"
                  ref="selfieInput"
                  @change="handleFileSelect($event, 'selfie')"
                  accept="image/*"
                  capture="user"
                  class="hidden"
                />
                <button
                  type="button"
                  @click="$refs.selfieInput.click()"
                  :disabled="submitting"
                  class="w-full border-2 border-dashed border-white/20 rounded-xl p-6 hover:border-teal-500/50 transition-colors text-center group disabled:opacity-50"
                >
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-2 group-hover:text-teal-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <p class="text-gray-300 text-sm font-medium">📸 Take selfie or select from gallery</p>
                  <p class="text-gray-500 text-xs mt-1">JPG or PNG (Max 10MB) • Camera opens on mobile</p>
                </button>
              </div>

              <!-- Preview -->
              <div v-else class="relative group">
                <div class="bg-white/10 rounded-xl p-4 border border-green-500/30">
                  <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                      <img :src="filePreviews.selfie" alt="Selfie Preview" class="w-20 h-20 object-cover rounded-lg" />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-white font-medium text-sm truncate">
                        {{ fileObjects.selfie?.name || 'Selfie with ID' }}
                        <span v-if="!fileObjects.selfie && filePreviews.selfie" class="text-blue-400 text-xs ml-1">(Existing)</span>
                      </p>
                      <p class="text-green-400 text-xs flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ fileObjects.selfie ? 'Ready to upload' : 'Previously uploaded - Click X to replace' }}
                      </p>
                    </div>
                    <button
                      type="button"
                      @click="removeFile('selfie')"
                      :disabled="submitting"
                      class="text-red-400 hover:text-red-300 transition-colors p-2 disabled:opacity-50"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Error Messages -->
          <div v-if="errors && Object.keys(errors).length > 0" class="bg-red-500/10 border border-red-500/30 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
              <div class="flex-1">
                <p class="text-red-300 text-sm font-semibold">Please fix the following errors:</p>
                <ul class="text-red-200/80 text-xs mt-2 space-y-1">
                  <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Upload Progress -->
          <div v-if="submitting && uploadProgress > 0" class="bg-white/5 rounded-xl p-4 border border-white/10">
            <div class="flex items-center justify-between mb-2">
              <span class="text-white text-sm font-semibold">Uploading to server...</span>
              <span class="text-orange-400 text-sm font-bold">{{ uploadProgress }}%</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2.5">
              <div class="bg-gradient-to-r from-orange-500 to-purple-600 h-2.5 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="$emit('close')"
              :disabled="submitting"
              class="flex-1 bg-gray-600 hover:bg-gray-500 text-white py-3 px-6 rounded-xl font-semibold transition-colors disabled:opacity-50"
            >
              Cancel
            </button>
            <button
              type="button"
              @click="submitKYC"
              :disabled="!canSubmit || submitting"
              class="flex-1 bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg hover:shadow-orange-500/50 transition-all"
            >
              <svg v-if="submitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ submitting ? (uploadProgress > 0 ? 'Uploading...' : 'Submitting...') : 'Submit KYC Verification' }}
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  kycRequirements: {
    type: Object,
    default: () => ({
      nin_required: true,
      bvn_required: false,
      utility_bill_required: true,
      selfie_required: true
    })
  },
  userInfo: {
    type: Object,
    default: () => ({
      full_name: null,
      email: null,
      date_of_birth: null,
      nin: null,
      bvn: null
    })
  },
  existingKyc: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'submitted']);

// Calculate max date (18 years ago from today)
const maxDate = new Date();
maxDate.setFullYear(maxDate.getFullYear() - 18);
const maxDateString = maxDate.toISOString().split('T')[0];

const form = ref({
  date_of_birth: props.userInfo.date_of_birth || null,
  nin: props.userInfo.nin || null,
  bvn: props.userInfo.bvn || null,
  nin_url: null,
  utility_bill_url: null,
  selfie_url: null
});

// Store actual file objects (not uploaded yet)
const fileObjects = ref({
  nin: null,
  utility_bill: null,
  selfie: null
});

// Preview URLs for display
const filePreviews = ref({
  nin: null,
  utility_bill: null,
  selfie: null
});

const errors = ref({});
const submitting = ref(false);
const uploadProgress = ref(0);

// Initialize with existing KYC data if resubmitting
watch(() => props.show, (newVal) => {
  if (newVal && props.existingKyc) {
    // Set existing URLs as previews
    if (props.existingKyc.nin_url) {
      filePreviews.value.nin = props.existingKyc.nin_url;
    }
    if (props.existingKyc.utility_bill_url) {
      filePreviews.value.utility_bill = props.existingKyc.utility_bill_url;
    }
    if (props.existingKyc.selfie_url) {
      filePreviews.value.selfie = props.existingKyc.selfie_url;
    }
  } else if (!newVal) {
    // Reset when modal closes
    filePreviews.value = {
      nin: null,
      utility_bill: null,
      selfie: null
    };
  }
});

const canSubmit = computed(() => {
  // Check basic info
  if (!props.userInfo.date_of_birth && !form.value.date_of_birth) return false;

  // Check NIN
  if (props.kycRequirements.nin_required) {
    if (!form.value.nin || form.value.nin.length !== 11) return false;
    // Allow existing file OR new file
    if (!fileObjects.value.nin && !filePreviews.value.nin) return false;
  }

  // Check BVN
  if (props.kycRequirements.bvn_required) {
    if (!form.value.bvn || form.value.bvn.length !== 11) return false;
  }

  // Check documents - allow existing OR new files
  if (props.kycRequirements.utility_bill_required && !fileObjects.value.utility_bill && !filePreviews.value.utility_bill) return false;
  if (props.kycRequirements.selfie_required && !fileObjects.value.selfie && !filePreviews.value.selfie) return false;

  return true;
});

const isImage = (url) => {
  if (!url) return false;
  return url.match(/\.(jpeg|jpg|gif|png|webp)$/i);
};

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};

const handleFileSelect = (event, type) => {
  const file = event.target.files[0];
  if (!file) return;

  // Validate file size (10MB max)
  if (file.size > 10 * 1024 * 1024) {
    errors.value[type] = 'File size must be less than 10MB';
    return;
  }

  // Validate file type
  const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
  if (!validTypes.includes(file.type)) {
    errors.value[type] = 'Only JPG, PNG, WEBP or PDF files allowed';
    return;
  }

  // Clear previous error
  delete errors.value[type];

  // Store file object
  fileObjects.value[type] = file;

  // Create preview URL for images
  if (file.type.startsWith('image/')) {
    filePreviews.value[type] = URL.createObjectURL(file);
  } else {
    filePreviews.value[type] = 'pdf';
  }
};

const removeFile = (type) => {
  fileObjects.value[type] = null;
  if (filePreviews.value[type] && filePreviews.value[type] !== 'pdf') {
    URL.revokeObjectURL(filePreviews.value[type]);
  }
  filePreviews.value[type] = null;
  form.value[`${type}_url`] = null;
};

const submitKYC = async () => {
  if (!canSubmit.value) return;

  submitting.value = true;
  uploadProgress.value = 0;
  errors.value = {};

  try {
    // Upload files to Backblaze first
    const filesToUpload = [];
    if (fileObjects.value.nin) filesToUpload.push({ type: 'nin', file: fileObjects.value.nin });
    if (fileObjects.value.utility_bill) filesToUpload.push({ type: 'utility_bill', file: fileObjects.value.utility_bill });
    if (fileObjects.value.selfie) filesToUpload.push({ type: 'selfie', file: fileObjects.value.selfie });

    const totalFiles = filesToUpload.length;
    let uploadedFiles = 0;

    for (const { type, file } of filesToUpload) {
      const formData = new FormData();
      formData.append('file', file);
      formData.append('type', type);

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

      const response = await fetch('/api/kyc/upload', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        form.value[`${type}_url`] = data.url;
        uploadedFiles++;
        uploadProgress.value = Math.round((uploadedFiles / totalFiles) * 100);
      } else {
        throw new Error(data.message || `Failed to upload ${type}`);
      }
    }

    // Now submit the KYC form with all URLs
    router.post('/kyc/submit', form.value, {
      preserveScroll: true,
      onSuccess: () => {
        submitting.value = false;
        uploadProgress.value = 0;
        emit('submitted');
        emit('close');
      },
      onError: (errs) => {
        submitting.value = false;
        uploadProgress.value = 0;
        errors.value = errs;
      }
    });

  } catch (error) {
    submitting.value = false;
    uploadProgress.value = 0;
    errors.value = { upload: error.message || 'File upload failed. Please try again.' };
  }
};
</script>
