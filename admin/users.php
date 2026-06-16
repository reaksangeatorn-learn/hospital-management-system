<?php
require_once '../includes/config.php';
requireAdmin();
$page_title = 'User Accounts';
$conn = getDB();

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== (int)$_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        setFlash('success', 'User deleted.');
    } else {
        setFlash('error', 'Cannot delete your own account.');
    }
    header('Location: users.php'); exit();
}

// EDIT USER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'edit') {
    $id       = (int)($_POST['id'] ?? 0);
    $name     = clean($_POST['name'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $role     = clean($_POST['role'] ?? 'receptionist');
    $password = $_POST['password'] ?? '';

    if ($password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        mysqli_query($conn, "UPDATE users SET name='$name',email='$email',password='$hash',role='$role' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE users SET name='$name',email='$email',role='$role' WHERE id=$id");
    }
    setFlash('success', 'User updated.');
    header('Location: users.php'); exit();
}

// ADD USER
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['action'])) {
    $name     = clean($_POST['name'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = clean($_POST['role'] ?? 'receptionist');

    if (!$name)                 $errors[] = 'Name is required.';
    if (!$email)                $errors[] = 'Email is required.';
    if (!$password)             $errors[] = 'Password is required.';
    if (strlen($password) < 6) $errors[] = 'Password min 6 characters.';

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($check) > 0) $errors[] = 'Email already exists.';

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        mysqli_query($conn, "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$hash','$role')");
        setFlash('success', "User '$name' created.");
        header('Location: users.php'); exit();
    }
}

// LOAD EDIT USER
$edit_user = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$eid"));
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
include '../includes/header.php';
?>

<p class="page-title"><i class="bi bi-gear me-2 text-primary"></i>User Accounts</p>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>All Users</span>
                <span class="badge bg-primary"><?= mysqli_num_rows($users) ?> users</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td>
                                    <strong><?= clean($u['name']) ?></strong>
                                    <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                    <span class="badge bg-info ms-1 small">You</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted"><?= clean($u['email']) ?></td>
                                <td>
                                    <span class="badge <?= $u['role']==='admin'?'bg-danger':($u['role']==='doctor'?'bg-primary':'bg-secondary') ?>">
                                        <?= ucfirst($u['role']) ?>
                                    </span>
                                </td>
                                <td class="text-muted small"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="users.php?edit=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete user?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <?php if ($edit_user): ?>
        <!-- Edit Form -->
        <div class="card">
            <div class="card-header bg-warning"><i class="bi bi-pencil me-2"></i>Edit User</div>
            <div class="card-body">
                <form method="POST" action="users.php?action=edit">
                    <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= clean($edit_user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?= clean($edit_user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select">
                            <option value="receptionist" <?= $edit_user['role']==='receptionist'?'selected':'' ?>>Receptionist</option>
                            <option value="doctor"       <?= $edit_user['role']==='doctor'?'selected':'' ?>>Doctor</option>
                            <option value="admin"        <?= $edit_user['role']==='admin'?'selected':'' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning w-100 fw-semibold">
                            <i class="bi bi-save me-1"></i>Update
                        </button>
                        <a href="users.php" class="btn btn-secondary w-100">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Add User Form -->
        <div class="card">
            <div class="card-header"><i class="bi bi-person-plus me-2 text-primary"></i>Add New User</div>
            <div class="card-body">
                <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', $errors) ?></div><?php endif; ?>
                <form method="POST" action="users.php">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= clean($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?= clean($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password * <small class="text-muted">(min 6)</small></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select">
                            <option value="receptionist">Receptionist</option>
                            <option value="doctor">Doctor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>Create User
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php mysqli_close($conn); include '../includes/footer.php'; ?>