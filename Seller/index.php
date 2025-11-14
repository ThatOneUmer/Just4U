<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
$SELLER_ME = null;
if (!empty($_SESSION['user_id'])) {
  $SELLER_ME = db_get_user_by_id((int)$_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seller Dashboard | Just4U Gaming</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="seller.css">
  <style>
    /* Make sure TinyMCE editor is fully interactive in the modal */
    #productModal .tox{ z-index: 6000 !important; }
    #productModal .tox .tox-edit-area__iframe{ pointer-events: auto !important; }
    #productModal .tox .tox-edit-area{ pointer-events: auto !important; }
    /* Quill adjustments */
    #productModal #productDescEditor,
    #productModal #productSpecEditor{ 
      border:1px solid var(--border); 
      border-radius:8px; 
      overflow:hidden;
      pointer-events:auto !important;
      position:relative;
      z-index:0;
      margin-bottom:12px;
    }
    #productModal #productDescEditor .ql-toolbar,
    #productModal #productSpecEditor .ql-toolbar{ 
      background:#0D141B !important; 
      border:none !important; 
      border-bottom:1px solid var(--border) !important;
      pointer-events:auto !important;
      z-index:6000 !important;
    }
    #productModal #productDescEditor .ql-toolbar button,
    #productModal #productSpecEditor .ql-toolbar button{ 
      pointer-events:auto !important;
    }
    #productModal #productDescEditor .ql-container,
    #productModal #productSpecEditor .ql-container{ 
      height: 140px !important; 
      overflow-y:auto; 
      background:#fff !important;
      border:none !important;
      display:block !important;
      pointer-events:auto !important;
      position:relative;
      z-index:5999 !important;
    }
    #productModal #productDescEditor .ql-editor,
    #productModal #productSpecEditor .ql-editor{ 
      cursor:text !important; 
      min-height:140px !important;
      height:140px !important;
      color:#111 !important;
      background:#fff !important;
      padding:12px 15px !important;
      pointer-events:auto !important;
    }
    #productModal #productDescEditor .ql-editor *,
    #productModal #productSpecEditor .ql-editor *{ 
      color:#111 !important;
    }
    #productModal #productDescEditor .ql-editor p,
    #productModal #productSpecEditor .ql-editor p{ 
      margin:0 0 8px 0 !important;
    }
    #productModal .ql-tooltip{ z-index: 6001; }
    #productModal .ql-toolbar .ql-stroke{ stroke:#CFE3FF; }
    #productModal .ql-toolbar .ql-fill{ fill:#CFE3FF; }
    #productModal .ql-toolbar .ql-picker-label{ color:#CFE3FF !important; }
    #productModal .ql-toolbar button:hover,
    #productModal .ql-toolbar button.ql-active{ background:rgba(31,182,255,0.1); }
    #productModal .ql-toolbar .ql-picker{ pointer-events:auto !important; }
    #productModal .ql-toolbar .ql-picker-options{ 
      z-index:6002 !important; 
      background:#1a2332 !important; 
      border:1px solid var(--border) !important;
      pointer-events:auto !important;
    }
    #productModal .ql-toolbar .ql-picker-item{ 
      color:#CFE3FF !important;
      pointer-events:auto !important;
    }
    #productModal .ql-toolbar .ql-picker-item:hover{ 
      background:rgba(31,182,255,0.1) !important;
    }
    /* Custom scrollbar for modal */
    #productModal ::-webkit-scrollbar{ width: 8px; height: 8px; }
    #productModal ::-webkit-scrollbar-track{ background: #0D141B; border-radius: 4px; }
    #productModal ::-webkit-scrollbar-thumb{ background: #1FB6FF; border-radius: 4px; }
    #productModal ::-webkit-scrollbar-thumb:hover{ background: #1a9ad6; }
    #productModal { scrollbar-width: thin; scrollbar-color: #1FB6FF #0D141B; }
    /* Ensure submit button stays above editors */
    #productModal #productSubmitBtn{ position:relative; z-index:10; }
  </style>
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
        <a class="nav-item active" href="#dashboard"><i class="fas fa-gauge"></i><span>Dashboard</span></a>
        <a class="nav-item" href="#products"><i class="fas fa-gamepad"></i><span>My Products</span></a>
        <a class="nav-item" href="#orders"><i class="fas fa-bag-shopping"></i><span>Orders</span></a>
        <a class="nav-item" href="#reviews"><i class="fas fa-star"></i><span>Reviews</span></a>
        <a class="nav-item" href="#analytics"><i class="fas fa-chart-line"></i><span>Analytics</span></a>
        <a class="nav-item" href="#support"><i class="fas fa-headset"></i><span>Support</span></a>
        <a class="nav-item" href="#settings"><i class="fas fa-gear"></i><span>Settings</span></a>
      </nav>
      <a class="nav-item logout" href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </aside>

    <main class="admin-main">
      <header class="admin-topbar">
        <h1>Seller Dashboard</h1>
        <div class="topbar-right">
          <button class="top-btn"><i class="fas fa-bell"></i></button>
          <div class="admin-profile" id="sellerProfileBtn">
            <?php $avatar = !empty($SELLER_ME['avatar_url']) ? $SELLER_ME['avatar_url'] : null; ?>
            <img src="<?php echo $avatar ? htmlspecialchars('../' . $avatar) : 'https://placehold.co/36x36/8A4DFF/ffffff?text=S'; ?>" alt="Seller"/>
            <div class="profile-info">
              <strong><?php echo htmlspecialchars($SELLER_ME['username'] ?? 'Seller'); ?></strong>
              <small><?php echo htmlspecialchars($SELLER_ME['email'] ?? ''); ?></small>
            </div>
          </div>
        </div>
      </header>

      <div id="section-dashboard">
      <section class="admin-kpis">
        <div class="kpi-card kpi-green">
          <div class="kpi-header"><i class="fas fa-bag-shopping"></i><span>Total Sales</span></div>
          <div class="kpi-value" id="kpiTotalSales">$0.00</div>
          <div class="kpi-sub" id="kpiTotalSalesSub">—</div>
        </div>
        <div class="kpi-card kpi-purple">
          <div class="kpi-header"><i class="fas fa-gamepad"></i><span>Active Products</span></div>
          <div class="kpi-value" id="kpiActiveProducts">0</div>
          <div class="kpi-sub" id="kpiActiveProductsSub">—</div>
        </div>
        <div class="kpi-card kpi-orange">
          <div class="kpi-header"><i class="fas fa-box"></i><span>Orders Pending</span></div>
          <div class="kpi-value" id="kpiOrdersPending">0</div>
          <div class="kpi-sub" id="kpiOrdersPendingSub">—</div>
        </div>
        <div class="kpi-card kpi-pink">
          <div class="kpi-header"><i class="fas fa-star"></i><span>Avg Rating</span></div>
          <div class="kpi-value" id="kpiAvgRating">0.0</div>
          <div class="kpi-sub" id="kpiAvgRatingSub">—</div>
        </div>
      </section>

      <section class="admin-panels">
        <div class="panel">
          <div class="panel-title">Recent Orders</div>
          <ul class="panel-list" id="recentOrdersList">
            <li style="color:var(--admin-muted)">Loading...</li>
          </ul>
        </div>
        <div class="panel">
          <div class="panel-title">Top Products</div>
          <ul class="panel-list" id="topProductsList">
            <li style="color:var(--admin-muted)">Loading...</li>
          </ul>
        </div>
        <div class="panel">
          <div class="panel-title">Alerts</div>
          <ul class="panel-list" id="alertsList">
            <li style="color:var(--admin-muted)">Loading...</li>
          </ul>
        </div>
      </section>

      <section class="admin-table">
        <div class="table-title">Orders Overview</div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Order</th>
                <th>Product</th>
                <th>Buyer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="ordersOverviewBody">
              <tr><td colspan="6" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </section>
      </div>

      <div id="section-products" style="display:none;">
        <section class="admin-table">
          <div class="table-title">My Products</div>
          <div class="table-actions" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <input type="text" id="sellerProductSearch" placeholder="Search my products..." class="search-input" style="max-width:260px;">
            <button class="btn btn-primary" id="addProductBtn"><i class="fas fa-plus"></i> Add Product</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Price</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="sellerProductsBody">
                <tr><td colspan="6" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <!-- Orders Section -->
      <div id="section-orders" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Orders</div>
          <p style="color:var(--admin-muted);padding:20px;text-align:center;">Orders section coming soon...</p>
        </section>
      </div>

      <!-- Reviews Section -->
      <div id="section-reviews" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Reviews</div>
          <div class="table-actions" style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <input type="text" id="sellerReviewSearch" placeholder="Search reviews..." class="search-input" style="max-width:260px;">
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Customer</th>
                  <th>Rating</th>
                  <th>Title</th>
                  <th>Comment</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="sellerReviewsBody">
                <tr><td colspan="7" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <!-- Analytics Section -->
      <div id="section-analytics" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Analytics</div>
          <p style="color:var(--admin-muted);padding:20px;text-align:center;">Analytics section coming soon...</p>
        </section>
      </div>

      <!-- Support Section -->
      <div id="section-support" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Support</div>
          <p style="color:var(--admin-muted);padding:20px;text-align:center;">Support section coming soon...</p>
        </section>
      </div>

      <!-- Settings Section -->
      <div id="section-settings" style="display:none;">
        <section class="admin-table">
          <div class="table-title">Settings</div>
          <p style="color:var(--admin-muted);padding:20px;text-align:center;">Settings section coming soon...</p>
        </section>
      </div>

      <!-- Add Product Modal -->
      <div id="productModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:5000;align-items:center;justify-content:center;">
        <div style="background:#0F1620;border:1px solid var(--border);border-radius:16px;max-width:600px;width:94%;max-height:90vh;display:flex;flex-direction:column;box-shadow:var(--shadow);">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);flex-shrink:0;">
            <strong id="productModalTitle">Add Product</strong>
            <button id="productModalClose" class="icon-btn" title="Close"><i class="fas fa-xmark"></i></button>
          </div>
          <div style="padding:16px;overflow-y:auto;flex:1;">
            <form id="productForm" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
              <div class="full" style="display:flex;align-items:center;gap:12px;">
                <img id="productImagePreview" src="https://placehold.co/96x72/0F1620/FFFFFF?text=IMG" style="width:96px;height:72px;object-fit:cover;border-radius:8px;border:1px solid var(--border);" alt="Preview"/>
                <div>
                  <label>Primary Image</label>
                  <input type="file" name="image" accept="image/*" />
                </div>
              </div>
              <div>
                <label>Title</label>
                <input type="text" name="title" required />
              </div>
              <div>
                <label>Price (USD)</label>
                <input type="number" name="price" step="0.01" min="0" required />
              </div>
              <div>
                <label>Game</label>
                <select name="game_id" id="gameSelect" required></select>
              </div>
              <div>
                <label>Platform</label>
                <select name="platform" required>
                  <option value="PC">PC</option>
                  <option value="Mobile">Mobile</option>
                  <option value="Console">Console</option>
                  <option value="Cross-Platform">Cross-Platform</option>
                </select>
              </div>
              <div>
                <label>Region</label>
                <input type="text" name="region" placeholder="e.g. North America" required />
              </div>
              <div class="full">
                <label>Description</label>
                <div id="productDescEditor">
                  <p></p>
                </div>
                <textarea id="productDesc" name="description" rows="1" style="display:none"></textarea>
              </div>
              <div class="full">
                <label>Specification</label>
                <div id="productSpecEditor">
                  <p></p>
                </div>
                <textarea id="productSpec" name="specification" rows="1" style="display:none"></textarea>
              </div>
              <div class="full" style="text-align:right;margin-top:8px;">
                <button type="button" class="btn btn-primary" id="productSubmitBtn" style="padding:10px 16px;">Create Product</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <div id="profileModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:5000;align-items:center;justify-content:center;">
        <div style="background:#0F1620;border:1px solid var(--border);border-radius:16px;max-width:560px;width:94%;max-height:80vh;display:flex;flex-direction:column;box-shadow:var(--shadow);">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);flex-shrink:0;">
            <strong>Profile</strong>
            <button id="profileModalClose" class="icon-btn" title="Close"><i class="fas fa-xmark"></i></button>
          </div>
          <div style="padding:16px;overflow-y:auto;flex:1;">
            <form id="sellerProfileForm" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
              <div class="full" style="display:flex;align-items:center;gap:14px;justify-content:center;">
                <img id="sellerAvatarPreview" src="<?php echo $avatar ? htmlspecialchars('../' . $avatar) : 'https://placehold.co/72x72/8A4DFF/ffffff?text=S'; ?>" alt="Avatar" style="width:72px;height:72px;border-radius:999px;object-fit:cover;border:1px solid var(--border);"/>
                <div>
                  <label>Profile Image</label>
                  <input type="file" name="avatar" accept="image/*" />
                </div>
              </div>
              <div>
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($SELLER_ME['first_name'] ?? ''); ?>" />
              </div>
              <div>
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($SELLER_ME['last_name'] ?? ''); ?>" />
              </div>
              <div>
                <label>Username</label>
                <input type="text" name="username" required value="<?php echo htmlspecialchars($SELLER_ME['username'] ?? ''); ?>" />
              </div>
              <div>
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($SELLER_ME['phone'] ?? ''); ?>" />
              </div>
              <div class="full">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($SELLER_ME['email'] ?? ''); ?>" disabled />
              </div>
              <div class="full" style="text-align:right;margin-top:8px;">
                <button type="submit" class="btn btn-primary" id="sellerProfileSubmit" style="padding:10px 16px;">Update Profile</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Quill (free WYSIWYG) -->
  <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="seller.js"></script>
</body>
</html>
