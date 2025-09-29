import { ref } from 'vue'

export function useEventModal(events) {
    const showMembersPopup = ref(false)
    const selectedSlot = ref(null)

    const openMembersPopup = (slot) => {
        selectedSlot.value = slot
        showMembersPopup.value = true
    }

    const closeMembersPopup = () => {
        showMembersPopup.value = false
        selectedSlot.value = null
    }

    const goToBookingSlot = (memberName) => {
        // Find the event for this specific member and use its direct URL
        const event = events.find(e =>
            e.meta_data.member === memberName &&
            e.meta_data.trainer === selectedSlot.value.trainer &&
            e.start_time === selectedSlot.value.start_time
        )

        if (event) {
            window.location.href = event.url
        }
    }

    return {
        showMembersPopup,
        selectedSlot,
        openMembersPopup,
        closeMembersPopup,
        goToBookingSlot
    }
}