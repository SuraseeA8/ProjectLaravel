document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.vendor-stalls');
    if (!root) return;

    const monthEl = document.getElementById('month');
    const initMonth = parseInt(root.dataset.month, 10);
    const initYear  = parseInt(root.dataset.year, 10);

    // ถ้า value ไม่ตรง data ที่มาจากเซิร์ฟเวอร์ ให้ sync
    const want = `${String(initYear).padStart(4,'0')}-${String(initMonth).padStart(2,'0')}`;
    if (monthEl && monthEl.value !== want) {
        monthEl.value = want;
    }

    // เปลี่ยนเดือน/ปี -> ตั้ง query แล้ว reload
    if (monthEl) {
        monthEl.addEventListener('change', () => {
        const [y, m] = monthEl.value.split('-');
        const url = new URL(window.location.href);
        url.searchParams.set('year', y);
        url.searchParams.set('month', parseInt(m, 10));
        window.location = url.toString();
        });
    }

    // ปรับลิงก์ของแต่ละล็อกเวลาเปลี่ยนค่า (กรณีกดโดยไม่ reload)
    const detailTpl = root.dataset.detailUrlTemplate || '';
    function buildDetailHref(stallId, y, m) {
        if (!detailTpl) return '#';
        return detailTpl.replace('STALL_ID', stallId)
                        .replace('YYYY', String(y))
                        .replace('MM', String(m).padStart(2,'0'));
    }

    function syncLinksToMonth() {
        if (!monthEl) return;
        const [y, m] = monthEl.value.split('-');
        document.querySelectorAll('.stall[data-stall-id]').forEach(a => {
        const id = a.getAttribute('data-stall-id');
        a.href = buildDetailHref(id, y, m);
        });
    }

    syncLinksToMonth();
    });
