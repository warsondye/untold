// Show scroll-to-top button when scrolling
window.addEventListener('scroll', function() {
    const scrollTopButton = document.getElementById('scrollTop');
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        scrollTopButton.style.display = 'flex';
    } else {
        scrollTopButton.style.display = 'none';
    }
});
