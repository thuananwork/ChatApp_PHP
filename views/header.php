<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chat Box</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Link css chung nếu có -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome cho icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Chat Box</h1>
        <?php if(isset($_SESSION['user'])): ?>
        <nav class="nav-menu">
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="menu-items">
                <li><a href="index.php?action=dashboard">Home</a></li>
                <li><a href="index.php?action=search">Search</a></li>
                <li><a href="index.php?action=viewFriendRequests">Friend Request</a></li>
                <li><a href="index.php?action=viewSentRequests">Request Sent</a></li>
                <li><a href="index.php?action=editProfile">Profile</a></li>
                <li><a href="index.php?action=logout">Logout</a></li>
            </ul>
        </nav>
        <?php endif; ?>
    </header>
    <main>
        <script>
            // JavaScript bật/tắt dropdown menu và tự đóng khi click bên ngoài
            document.addEventListener("DOMContentLoaded", function(){
                const toggle = document.querySelector(".menu-toggle");
                const navMenu = document.querySelector(".nav-menu");
                if (toggle) {
                    toggle.addEventListener("click", function(e){
                        e.stopPropagation();
                        navMenu.classList.toggle("active");
                    });
                }
                document.addEventListener("click", function(event){
                    if (!navMenu.contains(event.target)) {
                        navMenu.classList.remove("active");
                    }
                });
            });
        </script>
    </main>
</body>
</html>
