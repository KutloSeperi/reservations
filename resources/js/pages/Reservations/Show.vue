<template>
    <div class="max-w-2xl mx-auto p-6 space-y-6">
        <button type="button" @click="router.get('/')" class="text-sm text-blue-600 hover:underline cursor-pointer">
            ← Back to Resources
        </button>
        <div class="border rounded-lg p-6 space-y-3">
            <h1 class="text-xl font-bold text-gray-900">Reservation Details</h1>
            <p><span class="font-medium">Resource:</span> {{ reservation.resource.name }}</p>
            <p><span class="font-medium">Customer:</span> {{ reservation.customer_name }}</p>
            <p><span class="font-medium">Email:</span> {{ reservation.customer_email }}</p>
            <p><span class="font-medium">Start:</span> {{ new Date(reservation.start_at).toLocaleString() }}</p>
            <p><span class="font-medium">End:</span> {{ new Date(reservation.end_at).toLocaleString() }}</p>
            <p v-if="reservation.notes"><span class="font-medium">Notes:</span> {{ reservation.notes }}</p>
            <p>
                <span class="font-medium">Status:</span>
                <span :class="reservation.status === 'active' ? 'text-green-700' : 'text-red-700'">{{ reservation.status }}</span>
            </p>
            <button
                v-if="reservation.status === 'active'"
                type="button"
                @click="cancelReservation"
                class="mt-4 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 cursor-pointer"
            >
                Cancel Reservation
            </button>
        </div>
    </div>
</template>
<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    reservation: { type: Object, required: true },
});

function cancelReservation() {
    if (!confirm('Cancel this reservation?')) return;

    router.delete(`/reservations/${props.reservation.id}`, {
        onSuccess: () => router.get('/'),
        onError: (errors) => console.error('Cancel failed:', errors),
    });
}
</script>
