<?php
// Simple Admin Dashboard UI (static) matching the site's theme
session_start();
require_once __DIR__ . '/../includes/db.php';

function admin_fetch_customer_users(): array {
  $conn = db_connect();
  $sql = "SELECT id, username, email, role, COALESCE(is_verified, 1) as is_verified FROM users WHERE role = 'customer' ORDER BY id ASC";
  $res = $conn->query($sql);
  if (!$res) return [];
  $rows = [];
  while ($row = $res->fetch_assoc()) { $rows[] = $row; }
  return $rows;
}

function admin_fetch_games(): array {
  $conn = db_connect();
  $res = $conn->query("SELECT id, name, slug, is_active, created_at FROM games ORDER BY name ASC");
  if (!$res) return [];
  $rows = [];
  while ($row = $res->fetch_assoc()) { $rows[] = $row; }
  return $rows;
}

function admin_fetch_categories(): array {
  $conn = db_connect();
  $res = $conn->query("SELECT id, name, slug, is_active, created_at FROM categories ORDER BY name ASC");
  if (!$res) return [];
  $rows = [];
  while ($row = $res->fetch_assoc()) { $rows[] = $row; }
  return $rows;
}

$function_exists = function_exists('db_connect');
function admin_fetch_sellers(): array {
  $conn = db_connect();
  $sql = "SELECT u.id, u.username, u.email, u.role, COALESCE(s.store_name,'') AS store_name, COALESCE(u.is_verified,0) AS is_verified
          FROM users u
          LEFT JOIN sellers s ON s.user_id = u.id
          WHERE u.role = 'seller'
          ORDER BY u.id ASC";
  $res = $conn->query($sql);
  if (!$res) return [];
  $rows = [];
  while ($row = $res->fetch_assoc()) { $rows[] = $row; }
  return $rows;
}

$ADMIN_USERS = admin_fetch_customer_users();
$ADMIN_SELLERS = admin_fetch_sellers();
$ADMIN_GAMES = admin_fetch_games();
$ADMIN_CATEGORIES = admin_fetch_categories();
$ADMIN_ME = null;
if (!empty($_SESSION['user_id'])) {
  $ADMIN_ME = db_get_user_by_id((int)$_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Just4U Gaming</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <div class="admin-brand">
        <a href="../index.php" class="admin-logo">
          <span class="logo-text">Just4U</span><span class="logo-accent">Gaming</span>
        </a>
      </div>

      <div class="admin-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search Dashboard" />
      </div>
      <nav class="admin-nav">
        <a class="nav-item active" href="#" data-target="#section-dashboard"><i class="fas fa-gauge"></i><span>Dashboard</span></a>
        <a class="nav-item" href="#" data-target="#section-users"><i class="fas fa-users"></i><span>Users</span></a>
        <a class="nav-item" href="#" data-target="#section-sellers"><i class="fas fa-store"></i><span>Sellers</span></a>
        <a class="nav-item" href="#" data-target="#section-products"><i class="fas fa-gamepad"></i><span>Products</span></a>
        <a class="nav-item" href="#" data-target="#section-games"><i class="fas fa-dice-d6"></i><span>Games</span></a>
        <a class="nav-item" href="#" data-target="#section-categories"><i class="fas fa-layer-group"></i><span>Categories</span></a>
        <a class="nav-item" href="#"><i class="fas fa-bag-shopping"></i><span>Orders</span></a>
        <a class="nav-item" href="#"><i class="fas fa-star"></i><span>Reviews</span></a>
        <a class="nav-item" href="#"><i class="fas fa-ticket"></i><span>Support</span></a>
        <a class="nav-item" href="#"><i class="fas fa-gear"></i><span>Settings</span></a>
      </nav>
      <a class="nav-item logout" href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </aside>

    <main class="admin-main">
      <header class="admin-topbar">
        <h1>Admin Dashboard</h1>
        <div class="topbar-right">
          <button class="top-btn"><i class="fas fa-bell"></i></button>
          <div class="admin-profile">
            <?php $avatar = !empty($ADMIN_ME['avatar_url']) ? $ADMIN_ME['avatar_url'] : null; ?>
            <img src="<?php echo $avatar ? htmlspecialchars('../' . $avatar) : 'https://placehold.co/36x36/1FB6FF/ffffff?text=A'; ?>" alt="Admin"/>
            <div class="profile-info">
              <strong><?php echo htmlspecialchars($ADMIN_ME['username'] ?? 'admin'); ?></strong>
              <small><?php echo htmlspecialchars($ADMIN_ME['email'] ?? ''); ?></small>
            </div>
          </div>
        </div>
      </header>

      <div id="section-dashboard">
      <section class="admin-kpis">
        <div class="kpi-card kpi-green">
          <div class="kpi-header"><i class="fas fa-users"></i><span>Total Users</span></div>
          <div class="kpi-value">2,431</div>
          <div class="kpi-sub">+2.0% this month</div>
        </div>
        <div class="kpi-card kpi-purple">
          <div class="kpi-header"><i class="fas fa-store"></i><span>Active Sellers</span></div>
          <div class="kpi-value">128</div>
          <div class="kpi-sub">+1.0% this month</div>
        </div>
        <div class="kpi-card kpi-orange">
          <div class="kpi-header"><i class="fas fa-bag-shopping"></i><span>Active Orders</span></div>
          <div class="kpi-value">36</div>
          <div class="kpi-sub">+4.0% this month</div>
        </div>
        <div class="kpi-card kpi-pink">
          <div class="kpi-header"><i class="fas fa-chart-line"></i><span>Revenue</span></div>
          <div class="kpi-value">$12.4k</div>
          <div class="kpi-sub">+12% this month</div>
        </div>
      </section>

      <section class="admin-panels">
        <div class="panel">
          <div class="panel-title">Recent Users</div>
          <ul class="panel-list">
            <li><span>ahmad_hassan</span><span class="status-pill success">Active</span></li>
            <li><span>sarah_k</span><span class="status-pill success">Active</span></li>
            <li><span>usman_ali</span><span class="status-pill warning">Pending</span></li>
          </ul>
        </div>
        <div class="panel">
          <div class="panel-title">Recent Sellers</div>
          <ul class="panel-list">
            <li><span>ProGamer Store</span><span class="status-pill success">Active</span></li>
            <li><span>Spin Palace</span><span class="status-pill success">Active</span></li>
            <li><span>Royal Bets</span><span class="status-pill success">Active</span></li>
          </ul>
        </div>
        <div class="panel">
          <div class="panel-title">Recent Uploads</div>
          <ul class="panel-list">
            <li><span>Valorant Immortal</span><span class="status-pill success">Validated</span></li>
            <li><span>Fortnite Pro</span><span class="status-pill warning">Pending</span></li>
            <li><span>PUBG Conqueror</span><span class="status-pill warning">Pending</span></li>
          </ul>
        </div>
      </section>

      <section class="admin-table">
        <div class="table-title">Affiliates Overview</div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Company</th>
                <th>Manager</th>
                <th>Email</th>
                <th>Traffic Sources</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Affiliate One</td>
                <td>John Doe</td>
                <td>john@affiliateone.com</td>
                <td>Social, Ads</td>
                <td><span class="status-pill success">Active</span></td>
                <td><button class="icon-btn"><i class="fas fa-ellipsis"></i></button></td>
              </tr>
              <tr>
                <td>Top Casino Reviews</td>
                <td>Jane Smith</td>
                <td>jane@tcreviews.com</td>
                <td>SEO</td>
                <td><span class="status-pill success">Active</span></td>
                <td><button class="icon-btn"><i class="fas fa-ellipsis"></i></button></td>
              </tr>
              <tr>
                <td>Gaming Partners</td>
                <td>Mike Johnson</td>
                <td>mike@gamingpartners.com</td>
                <td>Social</td>
                <td><span class="status-pill warning">Pending</span></td>
                <td><button class="icon-btn"><i class="fas fa-ellipsis"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
      </div>

      <div id="section-users" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Users Management</div>
          <div class="table-actions" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <input type="text" id="userSearch" placeholder="Search users..." class="search-input" style="max-width:260px;">
            <button class="btn btn-primary" id="addUserBtn"><i class="fas fa-user-plus"></i> Add User</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="usersTableBody">
                <?php if (!$ADMIN_USERS): ?>
                  <tr><td colspan="6" style="text-align:center;color:var(--text-secondary)">No users found.</td></tr>
                <?php else: ?>
                  <?php foreach ($ADMIN_USERS as $u): ?>
                  <tr>
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['role']); ?></td>
                    <td>
                      <?php if ((int)$u['is_verified'] === 1): ?>
                        <span class="status-pill success">Active</span>
                      <?php else: ?>
                        <span class="status-pill warning">Pending</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <button class="icon-btn view-user-btn" data-user-id="<?php echo (int)$u['id']; ?>" title="View"><i class="fas fa-eye"></i></button>
                      <button class="icon-btn delete-user-btn" data-user-id="<?php echo (int)$u['id']; ?>" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <div id="section-games" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Games</div>
          <div class="table-actions" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <input type="text" id="gameSearch" placeholder="Search games..." class="search-input" style="max-width:260px;">
            <button class="btn btn-primary" id="addGameBtn"><i class="fas fa-plus"></i> Add Game</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Slug</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="gamesTableBody">
                <?php if (!$ADMIN_GAMES): ?>
                  <tr><td colspan="6" style="text-align:center;color:var(--text-secondary)">No games found.</td></tr>
                <?php else: ?>
                  <?php foreach ($ADMIN_GAMES as $g): ?>
                    <tr data-id="<?php echo (int)$g['id']; ?>" data-name="<?php echo htmlspecialchars($g['name']); ?>" data-slug="<?php echo htmlspecialchars($g['slug']); ?>" data-active="<?php echo (int)($g['is_active'] ?? 1); ?>">
                      <td><?php echo (int)$g['id']; ?></td>
                      <td><?php echo htmlspecialchars($g['name']); ?></td>
                      <td><?php echo htmlspecialchars($g['slug']); ?></td>
                      <td><?php echo ((int)($g['is_active'] ?? 1) === 1) ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">Inactive</span>'; ?></td>
                      <td><?php echo htmlspecialchars($g['created_at']); ?></td>
                      <td>
                        <button class="icon-btn edit-game-btn" data-id="<?php echo (int)$g['id']; ?>" title="Edit"><i class="fas fa-pen"></i></button>
                        <button class="icon-btn delete-game-btn" data-id="<?php echo (int)$g['id']; ?>" title="Delete"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <div id="section-categories" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Categories</div>
          <div class="table-actions" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <input type="text" id="categorySearch" placeholder="Search categories..." class="search-input" style="max-width:260px;">
            <button class="btn btn-primary" id="addCategoryBtn"><i class="fas fa-plus"></i> Add Category</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Slug</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="categoriesTableBody">
                <?php if (!$ADMIN_CATEGORIES): ?>
                  <tr><td colspan="6" style="text-align:center;color:var(--text-secondary)">No categories found.</td></tr>
                <?php else: ?>
                  <?php foreach ($ADMIN_CATEGORIES as $c): ?>
                    <tr data-id="<?php echo (int)$c['id']; ?>" data-name="<?php echo htmlspecialchars($c['name']); ?>" data-slug="<?php echo htmlspecialchars($c['slug']); ?>" data-active="<?php echo (int)($c['is_active'] ?? 1); ?>">
                      <td><?php echo (int)$c['id']; ?></td>
                      <td><?php echo htmlspecialchars($c['name']); ?></td>
                      <td><?php echo htmlspecialchars($c['slug']); ?></td>
                      <td><?php echo ((int)($c['is_active'] ?? 1) === 1) ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">Inactive</span>'; ?></td>
                      <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                      <td>
                        <button class="icon-btn edit-category-btn" data-id="<?php echo (int)$c['id']; ?>" title="Edit"><i class="fas fa-pen"></i></button>
                        <button class="icon-btn delete-category-btn" data-id="<?php echo (int)$c['id']; ?>" title="Delete"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <div id="section-products" style="display:none;">
        <section class="admin-table">
          <div class="table-title">All Products</div>
          <div class="table-actions" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <input type="text" id="adminProductSearch" placeholder="Search products..." class="search-input" style="max-width:260px;">
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Price</th>
                  <th>Status</th>
                  <th>Seller</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="adminProductsBody">
                <tr><td colspan="7" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <div id="section-profile" style="display:none;">
        <section class="admin-table">
          <div class="table-title">My Profile</div>
          <div class="table-wrap" style="padding:16px;">
            <form id="adminProfileForm" class="support-form" enctype="multipart/form-data" style="max-width:560px;margin:auto;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
              <div style="grid-column:1 / -1; text-align:center; margin-bottom:6px;">
                <div style="font-weight:700; font-size:18px;">Update your account details</div>
              </div>
              <div class="full" style="display:flex;align-items:center;gap:14px;justify-content:center;">
                <img id="adminAvatarPreview" src="<?php echo $avatar ? htmlspecialchars('../' . $avatar) : 'https://placehold.co/72x72/1FB6FF/ffffff?text=A'; ?>" alt="Avatar" style="width:72px;height:72px;border-radius:999px;object-fit:cover;border:1px solid var(--border);"/>
                <div>
                  <label>Profile Image</label>
                  <input type="file" name="avatar" accept="image/*" />
                </div>
              </div>
              <div>
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($ADMIN_ME['first_name'] ?? ''); ?>" />
              </div>
              <div>
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($ADMIN_ME['last_name'] ?? ''); ?>" />
              </div>
              <div>
                <label>Username</label>
                <input type="text" name="username" required value="<?php echo htmlspecialchars($ADMIN_ME['username'] ?? ''); ?>" />
              </div>
              <div>
                <label>Email</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($ADMIN_ME['email'] ?? ''); ?>" />
              </div>
              <div style="grid-column:1 / -1;">
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($ADMIN_ME['phone'] ?? ''); ?>" />
              </div>
              <div style="grid-column:1 / -1; text-align:right; margin-top:8px;">
                <button type="submit" class="btn btn-primary" style="padding:10px 16px;">Save Changes</button>
              </div>
            </form>
          </div>
        </section>
      </div>

      <div id="section-sellers" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Sellers Management</div>
          <div class="table-actions" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <input type="text" id="sellerSearch" placeholder="Search sellers..." class="search-input" style="max-width:260px;">
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Store</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="sellersTableBody">
                <?php if (!$ADMIN_SELLERS): ?>
                  <tr><td colspan="6" style="text-align:center;color:var(--text-secondary)">No sellers found.</td></tr>
                <?php else: ?>
                  <?php foreach ($ADMIN_SELLERS as $s): ?>
                  <tr>
                    <td><?php echo (int)$s['id']; ?></td>
                    <td><?php echo htmlspecialchars($s['username']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['store_name']); ?></td>
                    <td>
                      <?php if ((int)$s['is_verified'] === 1): ?>
                        <span class="status-pill success">Active</span>
                      <?php else: ?>
                        <span class="status-pill warning">Pending</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ((int)$s['is_verified'] !== 1): ?>
                        <button class="icon-btn approve-seller-btn" data-user-id="<?php echo (int)$s['id']; ?>" title="Approve"><i class="fas fa-check"></i></button>
                      <?php else: ?>
                        <span class="status-pill success" title="Approved" style="margin-right:8px;">Approved</span>
                      <?php endif; ?>
                      <button class="icon-btn delete-seller-btn" data-user-id="<?php echo (int)$s['id']; ?>" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Add Category Modal (moved outside sidebar) -->
  <div id="categoryModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:5000;align-items:center;justify-content:center;">
    <div style="background:#0F1620;border:1px solid var(--border);border-radius:16px;max-width:640px;width:96%;box-shadow:var(--shadow);">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);">
        <strong id="categoryModalTitle">Add Category</strong>
        <button id="categoryModalClose" class="icon-btn" title="Close"><i class="fas fa-xmark"></i></button>
      </div>
      <div style="padding:16px;">
        <form id="categoryForm" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div>
            <label>Name</label>
            <input type="text" name="name" id="catName" required />
          </div>
          <div>
            <label>Slug</label>
            <input type="text" name="slug" id="catSlug" required />
          </div>
          <div class="full">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Optional" style="width:100%;background:#0D141B;border:1px solid var(--border);border-radius:10px;padding:10px 12px;color:#E6F1FF;"></textarea>
          </div>
          <div>
            <label>Image (optional)</label>
            <input type="file" name="image" accept="image/*" />
          </div>
          <div>
            <label>Status</label>
            <select name="is_active">
              <option value="1" selected>Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <div class="full" style="text-align:right;">
            <button type="submit" class="btn btn-primary" id="categorySubmitBtn">Create Category</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Add Game Modal -->
  <div id="gameModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:5000;align-items:center;justify-content:center;">
    <div style="background:#0F1620;border:1px solid var(--border);border-radius:16px;max-width:640px;width:96%;box-shadow:var(--shadow);">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);">
        <strong id="gameModalTitle">Add Game</strong>
        <button id="gameModalClose" class="icon-btn" title="Close"><i class="fas fa-xmark"></i></button>
      </div>
      <div style="padding:16px;">
        <form id="gameForm" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div>
            <label>Name</label>
            <input type="text" name="name" id="gameName" required />
          </div>
          <div>
            <label>Slug</label>
            <input type="text" name="slug" id="gameSlug" required />
          </div>
          <div class="full">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Optional" style="width:100%;background:#0D141B;border:1px solid var(--border);border-radius:10px;padding:10px 12px;color:#E6F1FF;"></textarea>
          </div>
          <div>
            <label>Image (optional)</label>
            <input type="file" name="image" accept="image/*" />
          </div>
          <div>
            <label>Status</label>
            <select name="is_active">
              <option value="1" selected>Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <div class="full" style="text-align:right;">
            <button type="submit" class="btn btn-primary" id="gameSubmitBtn">Create Game</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- User Details Modal -->
  <div id="userModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:5000;align-items:center;justify-content:center;">
    <div style="background:#0F1620;border:1px solid var(--border);border-radius:16px;max-width:560px;width:92%;box-shadow:var(--shadow);">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);">
        <strong>User Details</strong>
        <button id="userModalClose" class="icon-btn" title="Close"><i class="fas fa-xmark"></i></button>
      </div>
      <div id="userModalBody" style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px;"></div>
    </div>
  </div>

  <script src="admin.js"></script>
</body>
</html>
