document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('book-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        const btn = form.querySelector('button[type="submit"]');
        if (btn && btn.disabled) {
        e.preventDefault();
        return;
        }
        // ยืนยันก่อนจอง
        const ok = confirm('ยืนยันการจองล็อกนี้สำหรับเดือนที่เลือกใช่หรือไม่?');
        if (!ok) e.preventDefault();
    });
});
