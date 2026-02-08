<h1>My Orders History</h1>
<a href="/dashboard/pharmacy">← Back to Dashboard</a>
<hr>

<?php if(count($orders) > 0): ?>
<?php
$grouped = [];
foreach($orders as $o){
    $grouped[$o->order_id][] = $o;
}
?>

<?php foreach($grouped as $order_id => $items): ?>
    <div style="border:1px solid #ccc;padding:10px;margin-bottom:15px;">
        <strong>Order #<?= $order_id ?></strong> | <?= $items[0]->created_at ?>
        <table border="1" width="100%" cellpadding="5">
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach($items as $item): ?>
            <tr>
                <td><?= $item->product_name ?></td>
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
