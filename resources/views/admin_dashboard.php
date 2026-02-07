<h1>Admin Dashboard</h1>
<p>Welcome, <?= Session::get('user')->email ?></p>
<a href="/logout">Logout</a>

<hr>
<h2>All Users</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach($users as $u): ?>
    <tr>
        <td><?= $u->id ?></td>
        <td><?= $u->email ?></td>
        <td><?= $u->role ?></td>
        <td><?= $u->status ?></td>
        <td>
            <?php if($u->status === 'pending'): ?>
                <a href="/admin/users/approve/<?= $u->id ?>">Approve</a> |
                <a href="/admin/users/reject/<?= $u->id ?>">Reject</a>
            <?php endif; ?>
            <a href="/admin/users/impersonate/<?= $u->id ?>">Login as User</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
