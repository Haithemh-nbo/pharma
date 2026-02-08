<h2>Orders To Validate</h2>
<a href="/dashboard/supplier">Back</a>
<hr>

<?php
$grouped=[];
foreach($orders as $o){
    $grouped[$o->order_id][] = $o;
}
?>

<?php foreach($grouped as $order_id => $items): ?>
<div style="border:1px solid #ccc;padding:10px;margin-bottom:10px;">
    <strong>Order #<?= $order_id ?></strong><br>
    Date: <?= $items[0]->created_at ?><br>
    Status: <b><?= $items[0]->status ?></b>

    <table border="1" cellpadding="5">
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

    <?php if($items[0]->status === 'pending'): ?>
        <form method="POST" action="/supplier/orders/accept/<?= $order_id ?>" style="display:inline;">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <button style="background:green;color:white;">Accept</button>
        </form>

        <form method="POST" action="/supplier/orders/reject/<?= $order_id ?>" style="display:inline;">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <button style="background:red;color:white;">Reject</button>
        </form>
    <?php endif; ?>
</div>
<?php endforeach; ?>
