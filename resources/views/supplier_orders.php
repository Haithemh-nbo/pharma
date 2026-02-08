<?php $user = Session::get('user'); ?>
<h1>Orders historique</h1>
<a href="/dashboard/supplier">&laquo; Back to Dashboard</a>

<?php if(session('success')): ?>
    <p style="color:green"><?= session('success') ?></p>
<?php endif; ?>

<?php if(session('error')): ?>
    <p style="color:red"><?= session('error') ?></p>
<?php endif; ?>

<?php if(count($orders) > 0): ?>
    <?php 
    // Group orders by order_id
    $groupedOrders = [];
    foreach($orders as $order) {
        $groupedOrders[$order->order_id][] = $order;
    }
    ?>
    <?php foreach($groupedOrders as $order_id => $items): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:20px; border-radius:6px;">
            <h3>Order #<?= $order_id ?></h3>
            <p>Date: <?= $items[0]->created_at ?></p>
            <p>Status: <strong><?= ucfirst($items[0]->status) ?></strong></p>

            <table border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item->product_name) ?></td>
                        <td><?= $item->quantity ?></td>
                        <td><?= $item->subtotal ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

         
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No orders yet.</p>
<?php endif; ?>
