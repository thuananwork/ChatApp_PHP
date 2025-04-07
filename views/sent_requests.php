<div class="container">
    <h2>Yêu cầu kết bạn đã gửi</h2>
    <?php if(empty($sentRequests)): ?>
        <p>Chưa có yêu cầu kết bạn nào được phản hồi.</p>
    <?php else: ?>

        <?php 
        // Phân chia mảng yêu cầu thành 2 nhóm: chờ phản hồi và lịch sử phản hồi
        $pendingRequests = array_filter($sentRequests, function($req) {
            return $req['status'] == 0;
        });
        $historyRequests = array_filter($sentRequests, function($req) {
            return $req['status'] != 0;
        });
        ?>

        <?php if (!empty($pendingRequests)): ?>
            <h3>Chờ phản hồi</h3>
            <ul class="sent-request-list">
                <?php foreach($pendingRequests as $req): ?>
                    <li>
                        Yêu cầu gửi đến <strong><?= htmlspecialchars($req['username']); ?></strong> vào <?= $req['created_at']; ?> - Chờ phản hồi...
                        <!-- Nút thu hồi gọi action withdrawRequest với request_id -->
                        <a href="index.php?action=withdrawRequest&request_id=<?= htmlspecialchars($req['id']); ?>" style="color: red; font-weight: bold; margin-left: 10px;">[Thu hồi]</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($historyRequests)): ?>
            <h3>Lịch sử phản hồi</h3>
            <ul class="sent-request-history">
                <?php foreach($historyRequests as $req): ?>
                    <li>
                        Yêu cầu gửi đến <strong><?= htmlspecialchars($req['username']); ?></strong> vào <?= $req['created_at']; ?> - 
                        <?php if($req['status'] == 1): ?>
                            Đã chấp nhận
                        <?php else: ?>
                            Đã từ chối
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php endif; ?>
</div>
