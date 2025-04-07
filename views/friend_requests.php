<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Yêu cầu kết bạn</title>
    <!-- Link Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pOtnjK6/6M+EvxgFVWsMekq5O8Eu5PZMxTIlhTObBYk1N1cIUxK54O3QH60+M3g9Dz+O2B57K7Kk5+WvQUo0cA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .btn-icon {
            text-decoration: none;
            font-size: 24px;
            padding: 5px 10px;
            border-radius: 50%;
            color: #fff;
            display: inline-block;
            margin-left: 10px;
        }
        .btn-accept {
            background-color: #28a745;
        }
        .btn-reject {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Yêu cầu kết bạn nhận được</h2>
    <?php if(empty($requests)): ?>
        <p>Không có yêu cầu kết bạn nào.</p>
    <?php else: ?>
        <ul class="friend-request-list">
            <?php foreach($requests as $req): ?>
                <li>
                    <strong><?php echo htmlspecialchars($req['username']); ?></strong> đã gửi yêu cầu vào <?php echo $req['created_at']; ?>
                    <a href="index.php?action=respondFriendRequest&request_id=<?php echo $req['id']; ?>&response=accept" class="btn-icon btn-accept" title="Chấp nhận">
                        <i class="fa-solid fa-circle-check"></i>
                    </a>
                    <a href="index.php?action=respondFriendRequest&request_id=<?php echo $req['id']; ?>&response=reject" class="btn-icon btn-reject" title="Từ chối">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html>
