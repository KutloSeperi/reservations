<template>
    <div class="max-w-5xl mx-auto p-6 space-y-8">
        <h1 class="text-2xl font-bold text-gray-900">Resource Reservations</h1>

<div v-if="successMessage" class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-900 shadow-sm" role="status">
            {{ successMessage }}
        </div>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Resources</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="resource in resources"
                    :key="resource.id"
                    class="border border-slate-200 rounded-2xl bg-white p-5 flex flex-col justify-between shadow-sm transition hover:shadow-lg"
                >
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ resource.name }}</h3>
                        <p class="text-sm text-gray-500">{{ resource.type }}</p>
                        <p class="text-sm mt-1">
                            Status:
                            <span
                                :class="{
                                    'text-green-700': resource.status === 'available',
                                    'text-yellow-700': resource.status === 'maintenance',
                                    'text-red-700': resource.status === 'unavailable',
                                }"
                                class="font-medium"
                            >
                                {{ resource.status }}
                            </span>
                        </p>
                       <p class="text-sm mt-1">
                            Availability:
                            <span :class="resource.available_capacity > 0 ? 'text-green-700' : 'text-red-700'" class="font-medium">
                                {{ resource.available_capacity }} of {{ resource.capacity }} available
                            </span>
                        </p>                    </div>
                    <button
                        type="button"
                        :disabled="resource.status !== 'available' || resource.available_capacity <= 0"
                        @click="selectedResourceId = resource.id; isReservationFormOpen = true; validationErrors = {}"
                        class="mt-4 inline-flex justify-center rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 cursor-pointer disabled:bg-slate-300 disabled:text-slate-600 disabled:cursor-not-allowed"
                    >
                        Reserve
                    </button>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Upcoming Reservations</h2>
            <div v-if="upcomingReservations.length === 0" class="text-gray-500 text-sm">
                No upcoming reservations.
            </div>
            <ul v-else class="divide-y border border-slate-200 rounded-2xl bg-white shadow-sm">
                <li
                    v-for="reservation in upcomingReservations"
                    :key="reservation.id"
                    class="p-4 flex items-center justify-between rounded-2xl transition hover:bg-slate-50 cursor-pointer"
                    @click="router.get(`/reservations/${reservation.id}`)"
                >
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ reservation.resource.name }} — {{ reservation.customer_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ new Date(reservation.start_at).toLocaleString() }} to {{ new Date(reservation.end_at).toLocaleString() }}
                        </p>
                    </div>
                    <button
                        type="button"
                        @click.stop="cancelReservation(reservation.id)"
                        class="text-sm text-red-600 hover:text-red-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                </li>
            </ul>
        </section>

        <div v-if="isReservationFormOpen" class="fixed inset-0 bg-slate-900/40 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-6 w-full max-w-md space-y-5 shadow-2xl ring-1 ring-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">New Reservation</h3>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Customer Name</label>
                    <input v-model="customerName" @input="clearError('customer_name')" type="text" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-black focus:ring-2 focus:ring-black/10" />
                    <p v-if="validationErrors.customer_name" class="text-sm text-red-700 mt-1">{{ validationErrors.customer_name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Customer Email</label>
                    <input v-model="customerEmail" @input="clearError('customer_email')" type="email" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-black focus:ring-2 focus:ring-black/10" />
                    <p v-if="validationErrors.customer_email" class="text-sm text-red-700 mt-1">{{ validationErrors.customer_email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Start</label>
                    <input v-model="reservationStartAt" @input="clearError('start_at')" type="datetime-local" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-black focus:ring-2 focus:ring-black/10" />
                    <p v-if="validationErrors.start_at" class="text-sm text-red-700 mt-1">{{ validationErrors.start_at }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">End</label>
                    <input v-model="reservationEndAt" @input="clearError('end_at')" type="datetime-local" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-black focus:ring-2 focus:ring-black/10" />
                    <p v-if="validationErrors.end_at" class="text-sm text-red-700 mt-1">{{ validationErrors.end_at }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Notes (optional)</label>
                    <textarea v-model="reservationNotes" @input="clearError('notes')" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-black focus:ring-2 focus:ring-black/10" rows="2"></textarea>
                </div>

                <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button type="button" @click="isReservationFormOpen = false" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="button" @click="submitReservation" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Confirm Reservation
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
    resources: { type: Array, required: true },
    upcomingReservations: { type: Array, required: true },
});

// Every input above is wired straight to one of these refs via v-model —
// type in the field, the ref updates immediately.
const isReservationFormOpen = ref(false);
const selectedResourceId = ref(null);
const customerName = ref('');
const customerEmail = ref('');
const reservationStartAt = ref('');
const reservationEndAt = ref('');
const reservationNotes = ref('');
const validationErrors = ref({});
const successMessage = ref('');

function clearError(field) {
    if (validationErrors.value[field]) {
        delete validationErrors.value[field];
    }
}

// No frontend validation rules here on purpose — server is the single source of truth.
function submitReservation() {
    router.post('/reservations', {
        resource_id: selectedResourceId.value,
        customer_name: customerName.value,
        customer_email: customerEmail.value,
        start_at: reservationStartAt.value,
        end_at: reservationEndAt.value,
        notes: reservationNotes.value,
    }, {
        onSuccess: () => {
            isReservationFormOpen.value = false;
            customerName.value = '';
            customerEmail.value = '';
            reservationStartAt.value = '';
            reservationEndAt.value = '';
            reservationNotes.value = '';
            successMessage.value = 'Reservation created successfully.';
            setTimeout(() => { successMessage.value = ''; }, 4000);
        },
        onError: (errors) => { validationErrors.value = errors; },
    });
}

function cancelReservation(reservationId) {
    if (confirm('Cancel this reservation?')) {
        router.delete(`/reservations/${reservationId}`);
    }
}
</script>