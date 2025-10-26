import { nextTick } from 'vue'

export async function scrollToFirstError() {
    // Use nextTick to wait for Vue to finish rendering
    await nextTick()

    setTimeout(() => {
        // Find the first validation error element in the DOM
        const firstErrorElement = document.querySelector('[data-validation-error]')

        if (!firstErrorElement) {
            return
        }

        // Find the parent FormSection
        const formSection = firstErrorElement.closest('section')

        if (!formSection) {
            // Fallback to scrolling to the error element itself
            firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'start' })
            return
        }

        // Find the section title (h2)
        const sectionTitle = formSection.querySelector('h2')

        if (!sectionTitle) {
            // Fallback to scrolling to the form section
            formSection.scrollIntoView({ behavior: 'smooth', block: 'start' })
            return
        }

        // Scroll the section title to the top of the viewport
        sectionTitle.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        })

        // Adjust for sticky header after scroll animation
        setTimeout(() => {
            const stickyHeader = document.querySelector('.sticky.top-0')
            const headerHeight = stickyHeader ? stickyHeader.offsetHeight : 0
            const buffer = 20

            // Scroll up to account for sticky header, but only if we have enough room
            if (window.scrollY > (headerHeight + buffer)) {
                window.scrollBy({
                    top: -(headerHeight + buffer),
                    behavior: 'smooth'
                })
            }
        }, 300)
    }, 300)
}
