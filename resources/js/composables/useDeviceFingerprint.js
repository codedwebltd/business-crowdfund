import { ref } from 'vue';
import FingerprintJS from '@fingerprintjs/fingerprintjs';

const fingerprintCache = ref(null);

export function useDeviceFingerprint() {
    const getFingerprint = async () => {
        if (fingerprintCache.value) {
            return fingerprintCache.value;
        }

        try {
            const fp = await FingerprintJS.load();
            const result = await fp.get();

            fingerprintCache.value = {
                hash: result.visitorId,
                details: {
                    components: result.components,
                    confidence: result.confidence
                }
            };

            return fingerprintCache.value;
        } catch (error) {
            console.error('Fingerprint generation failed:', error);
            return null;
        }
    };

    const attachToRequest = (requestData) => {
        if (fingerprintCache.value) {
            return {
                ...requestData,
                device_fingerprint: fingerprintCache.value.hash,
                fingerprint_details: fingerprintCache.value.details
            };
        }
        return requestData;
    };

    return {
        getFingerprint,
        attachToRequest
    };
}
