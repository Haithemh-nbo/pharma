<?php
use App\Models\User;
$users = User::all();
?>
<h1>Admin Dashboard</h1>
<a href="/logout">Logout</a>
<h3>All Users</h3>
<table >
<tr><th>Email</th><th>Role</th></tr>
<?php foreach($users as $user): ?>
<tr>
    <td><?= $user->email ?></td>
    <td><?= $user->role ?></td>
</tr>
<?php endforeach; ?>
</table>
