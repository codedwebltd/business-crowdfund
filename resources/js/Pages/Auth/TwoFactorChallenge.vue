<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md flex flex-col items-center">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Security Verification</h1>
        <p class="text-gray-300">Two-factor authentication is enabled on your account</p>
      </div>

      <TwoFactorVerify
        @verify="handleVerify"
        @verifyBackup="handleVerifyBackup"
      />
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import TwoFactorVerify from '@/Components/TwoFactorVerify.vue';
import Swal from 'sweetalert2';

const handleVerify = (code, done) => {
  router.post('/2fa/verify', { code }, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Verified!',
        text: 'Access granted',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
        timer: 1500,
        showConfirmButton: false,
      });
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Invalid Code',
        text: 'The code you entered is incorrect',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
      done();
    },
    onFinish: () => done()
  });
};

const handleVerifyBackup = (code, done) => {
  router.post('/2fa/verify-backup', { backup_code: code }, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Verified!',
        text: 'Backup code accepted',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
        timer: 1500,
        showConfirmButton: false,
      });
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Invalid Code',
        text: 'The backup code is incorrect or already used',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
      done();
    },
    onFinish: () => done()
  });
};
</script>
