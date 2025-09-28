document.addEventListener("DOMContentLoaded", () => {
    const stalls = document.querySelectorAll(".stall");

    stalls.forEach(stall => {
        stall.addEventListener("click", () => {
            const status = stall.dataset.status;
            const id = stall.dataset.id;

            if (status === "available") {
                window.location.href = `/vendor/stalls/${id}`;
            } else {
                alert("ล็อกนี้ไม่ว่าง");
            }
        });
    });
});
