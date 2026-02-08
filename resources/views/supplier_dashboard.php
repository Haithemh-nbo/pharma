<?php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

$user = Session::get('user');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = request()->only(['email', 'old_password', 'new_password', 'confirm_password']);

    // Update email
    if ($data['email'] !== $user->email) {
        DB::table('users')->where('id', $user->id)->update([
            'email' => $data['email']
        ]);
    }

    // Update password if filled
    if (!empty($data['old_password']) || !empty($data['new_password']) || !empty($data['confirm_password'])) {

        if ($data['old_password'] != $user->password) {
            Session::flash('error', 'Current password is incorrect.');
        } 
        elseif ($data['new_password'] != $data['confirm_password']) {
            Session::flash('error', 'New password and confirmation do not match.');
        } 
        else {
            DB::table('users')->where('id', $user->id)->update([
                'password' => $data['new_password']
            ]);
            Session::flash('success', 'Password updated successfully.');
        }
    } else {
        if (!Session::has('error')) {
            Session::flash('success', 'Account updated successfully.');
        }
    }

    // Refresh session
    $updatedUser = DB::table('users')->where('id', $user->id)->first();
    Session::put('user', $updatedUser);

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<h1>Supplier Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($user->email) ?></p>

<?php if ($user->role === 'supplier' && Session::has('admin_original')): ?>
    <a href="/admin/return">Return to Admin</a>
<?php endif; ?>

<a href="/logout">Logout</a>

<hr>

<ul>
    <li><a href="/supplier/listings">My Listings</a></li>

    <!-- 🔥 NEW LINK -->
    <li><a href="/supplier/orders/validate">Validate Orders</a></li>

    <li><a href="/supplier/orders">Order History</a></li>
    <li><a href="#account-info">Account Info</a></li>
</ul>

<hr>

<h2 id="account-info">My Account</h2>

<?php if(Session::has('success')): ?>
    <p style="color:green;"><?= Session::get('success') ?></p>
<?php endif; ?>

<?php if(Session::has('error')): ?>
    <p style="color:red;"><?= Session::get('error') ?></p>
<?php endif; ?>

<form method="POST" action="/user/update">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user->email) ?>" required><br><br>

    <label>Old Password:</label><br>
    <input type="password" name="old_password"><br><br>

    <label>New Password:</label><br>
    <input type="password" name="new_password"><br><br>

    <label>Confirm New Password:</label><br>
    <input type="password" name="confirm_password"><br><br>

    <button type="submit">Update Account</button>
</form>
