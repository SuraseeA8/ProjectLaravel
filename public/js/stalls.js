// ป้องกันกดซ้ำ: disable ปุ่มทันทีหลัง submit
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form.stall-form').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.classList.add('is-submitting');
            }
        });
    });
});
