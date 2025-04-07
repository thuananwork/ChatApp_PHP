document.addEventListener("DOMContentLoaded", function() {
    // Auto-scroll: cuộn khung chat xuống cuối khi trang load
    const chatBox = document.querySelector(".chat-box");
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Voice Chat Placeholder
    const startVoiceBtn = document.getElementById("start-voice");
    const endVoiceBtn = document.getElementById("end-voice");
    if (startVoiceBtn && endVoiceBtn) {
        startVoiceBtn.addEventListener("click", function() {
            alert("Voice chat bắt đầu (placeholder). Triển khai WebRTC với signaling server để sử dụng thật.");
            startVoiceBtn.disabled = true;
            endVoiceBtn.disabled = false;
        });
        endVoiceBtn.addEventListener("click", function() {
            alert("Voice chat kết thúc (placeholder).");
            startVoiceBtn.disabled = false;
            endVoiceBtn.disabled = true;
        });
    }

    // Chat form: submit khi nhấn Enter (không kèm Shift)
    const chatTextarea = document.querySelector(".chat-form textarea");
    if (chatTextarea) {
        chatTextarea.addEventListener("keydown", function(e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();  // Ngăn chặn xuống dòng
                const form = this.closest("form");
                if (form) {
                    form.submit();
                }
            }
        });
    }

    // Xử lý xác nhận hành động cho các liên kết (nếu có thuộc tính data-confirm)
    const confirmLinks = document.querySelectorAll("a[data-confirm]");
    confirmLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            const message = this.getAttribute("data-confirm") || "Bạn có chắc chắn muốn thực hiện hành động này không?";
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Toggle Sidebar (cho mobile): nếu có nút với id "toggle-sidebar"
    const toggleSidebarBtn = document.getElementById("toggle-sidebar");
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener("click", function() {
            const sidebar = document.querySelector(".sidebar");
            if (sidebar) {
                sidebar.classList.toggle("active");
            }
        });
    }

    //dashboard
    
});
