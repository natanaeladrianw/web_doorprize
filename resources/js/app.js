function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("-translate-x-full");
}

document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('slider')
    const prev = document.getElementById('btnPrev')
    const next = document.getElementById('btnNext')

    const scrollAmount = slider.offsetWidth

    prev?.addEventListener('click', () => {
        slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
    })

    next?.addEventListener('click', () => {
        slider.scrollBy({ left: scrollAmount, behavior: 'smooth' })
    })
})