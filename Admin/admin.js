// Minimal admin interactions
(function(){
  const mq = window.matchMedia('(max-width:1100px)');
  const sidebar = document.querySelector('.admin-sidebar');
  const main = document.querySelector('.admin-main');

  function applyCompact(e){
    if (!sidebar) return;
    if (e.matches){
      sidebar.classList.add('compact');
    } else {
      sidebar.classList.remove('compact');
    }
  }
  applyCompact(mq);
  mq.addEventListener('change', applyCompact);

  // (placeholder removed to avoid interfering with real actions)

  // Section toggling
  const navItems = document.querySelectorAll('.admin-nav .nav-item[data-target]');
  const sections = [
    document.querySelector('#section-dashboard'),
    document.querySelector('#section-users'),
    document.querySelector('#section-sellers'),
    document.querySelector('#section-products'),
    document.querySelector('#section-games'),
    document.querySelector('#section-categories'),
    document.querySelector('#section-profile')
  ].filter(Boolean);

  function showSection(targetSel){
    sections.forEach(sec=>{ if (sec) sec.style.display = ("#"+sec.id) === targetSel ? '' : 'none'; });
    navItems.forEach(a=>{
      if (a.getAttribute('data-target') === targetSel) a.classList.add('active'); else a.classList.remove('active');
    });
    if (targetSel === '#section-products') {
      // load products whenever this section becomes visible
      if (typeof loadAdminProducts === 'function') loadAdminProducts();
    }
  }
  navItems.forEach(a=>{
    a.addEventListener('click', (e)=>{
      e.preventDefault();
      const target = a.getAttribute('data-target');
      if (!target) return;
      // persist in URL
      if (location.hash !== target) location.hash = target;
      showSection(target);
    });
  });

  // Support back/forward navigation on hash changes
  window.addEventListener('hashchange', ()=>{
    if (location.hash && document.querySelector(location.hash)){
      showSection(location.hash);
    }
  });

  // default visible is dashboard (override with URL hash if present)
  if (location.hash && document.querySelector(location.hash)) {
    showSection(location.hash);
  } else {
    showSection('#section-dashboard');
  }

  // Open profile when clicking admin profile in topbar
  const adminProfileBtn = document.querySelector('.admin-profile');
  if (adminProfileBtn){
    adminProfileBtn.addEventListener('click', (e)=>{
      e.preventDefault();
      const target = '#section-profile';
      if (location.hash !== target) location.hash = target;
      showSection(target);
    });
  }

  // View user details modal
  const userModal = document.getElementById('userModal');
  const userModalBody = document.getElementById('userModalBody');
  const userModalClose = document.getElementById('userModalClose');
  function openUserModal(){ if (userModal){ userModal.style.display = 'flex'; } }
  function closeUserModal(){ if (userModal){ userModal.style.display = 'none'; } }
  if (userModalClose){ userModalClose.addEventListener('click', closeUserModal); }
  if (userModal){ userModal.addEventListener('click', (e)=>{ if (e.target === userModal) closeUserModal(); }); }
  document.querySelectorAll('.view-user-btn').forEach(btn => {
    btn.addEventListener('click', async ()=>{
      const id = btn.getAttribute('data-user-id');
      if (!id) return;
      try{
        const res = await fetch('get_user.php?id=' + encodeURIComponent(id), { credentials: 'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          const u = data.user;
          if (userModalBody){
            const avatar = u.avatar_url ? ('../' + u.avatar_url) : 'https://placehold.co/72x72/1FB6FF/ffffff?text=' + (u.username ? u.username.charAt(0).toUpperCase() : 'U');
            userModalBody.innerHTML = `
              <div class="full" style="display:flex;align-items:center;gap:14px;margin-bottom:4px;">
                <img src="${avatar}" alt="Avatar" style="width:72px;height:72px;border-radius:999px;object-fit:cover;border:1px solid var(--border);"/>
                <div>
                  <div style="font-weight:700;font-size:18px;">${u.username || ''}</div>
                  <div style="color:var(--admin-muted);font-size:13px;">${u.email || ''}</div>
                </div>
              </div>
              <div><strong>ID</strong><div>${u.id}</div></div>
              <div><strong>Role</strong><div>${u.role || ''}</div></div>
              <div><strong>First Name</strong><div>${u.first_name ?? ''}</div></div>
              <div><strong>Last Name</strong><div>${u.last_name ?? ''}</div></div>
              <div class="full"><strong>Phone</strong><div>${u.phone ?? ''}</div></div>
            `;
          }
          openUserModal();
        } else {
          alert('Failed to load user: ' + (data.error || res.status));
        }
      }catch(err){
        alert('Network error');
      }
    });
  });
  // Simple search filter in Users table
  const userSearch = document.getElementById('userSearch');
  const usersTBody = document.getElementById('usersTableBody');
  if (userSearch && usersTBody){
    userSearch.addEventListener('input', ()=>{
      const q = userSearch.value.toLowerCase();
      usersTBody.querySelectorAll('tr').forEach(tr=>{
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // Delete user functionality
  function bindDeleteButtons(){
    document.querySelectorAll('.delete-user-btn').forEach(btn => {
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-user-id');
        if (!id) return;
        if (!confirm('Delete this user? This action cannot be undone.')) return;
        try {
          const form = new FormData();
          form.append('id', id);
          const res = await fetch('delete_user.php', { method: 'POST', body: form, credentials: 'same-origin' });
          const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
          if (res.ok && data.ok){
            const row = btn.closest('tr');
            if (row) row.remove();
            alert('User deleted');
          } else {
            alert('Failed to delete: ' + (data.error || res.status));
          }
        } catch (e) {
          alert('Network error');
        }
      });
    });
  }
  bindDeleteButtons();

  // Sellers search and delete
  const sellerSearch = document.getElementById('sellerSearch');
  const sellersTBody = document.getElementById('sellersTableBody');
  if (sellerSearch && sellersTBody){
    sellerSearch.addEventListener('input', ()=>{
      const q = sellerSearch.value.toLowerCase();
      sellersTBody.querySelectorAll('tr').forEach(tr=>{
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  function bindDeleteSeller(){
    document.querySelectorAll('.delete-seller-btn').forEach(btn => {
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-user-id');
        if (!id) return;
        if (!confirm('Delete this seller? This will remove their account and store.')) return;
        try {
          const form = new FormData();
          form.append('id', id);
          const res = await fetch('delete_seller.php', { method: 'POST', body: form, credentials: 'same-origin' });
          const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
          if (res.ok && data.ok){
            const row = btn.closest('tr');
            if (row) row.remove();
            alert('Seller deleted');
          } else {
            alert('Failed to delete: ' + (data.error || res.status));
          }
        } catch (e) {
          alert('Network error');
        }
      });
    });
  }
  bindDeleteSeller();

  // Approve seller
  function bindApproveSeller(){
    document.querySelectorAll('.approve-seller-btn').forEach(btn => {
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-user-id');
        if (!id) return;
        if (!confirm('Approve this seller?')) return;
        try {
          const form = new FormData();
          form.append('id', id);
          const res = await fetch('approve_seller.php', { method: 'POST', body: form, credentials: 'same-origin' });
          const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
          if (res.ok && data.ok){
            // Update row UI: status -> Active; replace approve button with Approved badge
            const row = btn.closest('tr');
            if (row){
              const statusCell = row.querySelector('td:nth-child(5)');
              if (statusCell){
                statusCell.innerHTML = '<span class="status-pill success">Active</span>';
              }
              const actionsCell = row.querySelector('td:nth-child(6)');
              if (actionsCell){
                btn.remove();
                const approved = document.createElement('span');
                approved.className = 'status-pill success';
                approved.title = 'Approved';
                approved.style.marginRight = '8px';
                approved.textContent = 'Approved';
                actionsCell.insertBefore(approved, actionsCell.firstChild);
              }
            }
            alert('Seller approved');
          } else {
            alert('Failed to approve: ' + (data.error || res.status));
          }
        } catch (e) {
          alert('Network error');
        }
      });
    });
  }
  bindApproveSeller();

  // Admin Products listing
  const adminProductsBody = document.getElementById('adminProductsBody');
  const adminProductSearch = document.getElementById('adminProductSearch');
  async function loadAdminProducts(){
    if (!adminProductsBody) return;
    adminProductsBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>';
    try{
      const res = await fetch('list_products.php', { credentials: 'same-origin' });
      const data = await res.json();
      if (!res.ok || !data.ok){
        adminProductsBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#FF6B6B">Failed to load products</td></tr>';
        return;
      }
      const rows = (data.products||[]).map(p=>{
        const img = p.image_url ? ('../' + p.image_url) : 'https://placehold.co/56x40/0F1620/FFFFFF?text=IMG';
        const statusPill = p.status === 'active' ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">'+ (p.status||'') +'</span>';
        return `
          <tr data-id="${p.id}">
            <td><img src="${img}" alt="" style="width:56px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border)"></td>
            <td>${escapeHtml(p.title||'')}</td>
            <td>$${Number(p.price||0).toFixed(2)}</td>
            <td>${statusPill}</td>
            <td>${escapeHtml(p.seller_username||'')}</td>
            <td>${formatDate(p.created_at)}</td>
            <td>
              <button class="icon-btn delete-product-btn" data-id="${p.id}" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
          </tr>`;
      }).join('');
      adminProductsBody.innerHTML = rows || '<tr><td colspan="7" style="text-align:center;color:var(--admin-muted)">No products found</td></tr>';
      bindDeleteProducts();
    }catch(e){
      adminProductsBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#FF6B6B">Network error</td></tr>';
    }
  }

  function bindDeleteProducts(){
    document.querySelectorAll('.delete-product-btn').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-id');
        if (!id) return;
        if (!confirm('Delete this product? This action cannot be undone.')) return;
        try{
          const form = new FormData();
          form.append('id', id);
          const res = await fetch('delete_product.php', { method:'POST', body: form, credentials:'same-origin' });
          const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
          if (res.ok && data.ok){
            const tr = btn.closest('tr');
            if (tr) tr.remove();
          } else {
            alert('Failed to delete: ' + (data.error || res.status));
          }
        }catch(err){
          alert('Network error');
        }
      });
    });
  }

  if (adminProductSearch && adminProductsBody){
    adminProductSearch.addEventListener('input', ()=>{
      const q = adminProductSearch.value.toLowerCase();
      adminProductsBody.querySelectorAll('tr').forEach(tr=>{
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // Load products when switching to the products section
  document.querySelectorAll('.admin-nav .nav-item[data-target="#section-products"]').forEach(a=>{
    a.addEventListener('click', ()=>{ loadAdminProducts(); });
  });

  function escapeHtml(str){
    return String(str||'').replace(/[&<>"']/g, s=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;"}[s]));
  }
  function formatDate(dt){
    if (!dt) return '';
    try { return new Date(dt.replace(' ', 'T')).toLocaleDateString(); } catch { return dt; }
  }

  // Categories Management
  const addCategoryBtn = document.getElementById('addCategoryBtn');
  const categoryModal = document.getElementById('categoryModal');
  const categoryModalClose = document.getElementById('categoryModalClose');
  const categoryForm = document.getElementById('categoryForm');
  const catName = document.getElementById('catName');
  const catSlug = document.getElementById('catSlug');
  const categoriesTableBody = document.getElementById('categoriesTableBody');
  const categorySearch = document.getElementById('categorySearch');

  const categoryModalTitle = document.getElementById('categoryModalTitle');
  const categorySubmitBtn = document.getElementById('categorySubmitBtn');
  function setCategoryMode(edit){
    if (categoryModalTitle) categoryModalTitle.textContent = edit ? 'Update Category' : 'Add Category';
    if (categorySubmitBtn) categorySubmitBtn.textContent = edit ? 'Update Category' : 'Create Category';
  }
  function openCategoryModal(){ if (categoryModal){ categoryModal.style.display = 'flex'; setCategoryMode(!!categoryForm?.getAttribute('data-edit-id')); } }
  function closeCategoryModal(){ if (categoryModal) categoryModal.style.display = 'none'; if (categoryForm){ categoryForm.reset(); categoryForm.removeAttribute('data-edit-id'); } }
  if (addCategoryBtn) addCategoryBtn.addEventListener('click', e=>{ e.preventDefault(); openCategoryModal(); });
  if (categoryModalClose) categoryModalClose.addEventListener('click', closeCategoryModal);
  if (categoryModal) categoryModal.addEventListener('click', e=>{ if (e.target === categoryModal) closeCategoryModal(); });

  // Auto slugify
  function slugify(s){ return String(s).toLowerCase().trim().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,''); }
  if (catName && catSlug){
    catName.addEventListener('input', ()=>{ if (!catSlug.value) catSlug.value = slugify(catName.value); });
  }

  // Submit create category
  if (categoryForm){
    categoryForm.addEventListener('submit', async (e)=>{
      if (categoryForm.getAttribute('data-edit-id')) return; // edit handled elsewhere
      e.preventDefault();
      try{
        const form = new FormData(categoryForm);
        const res = await fetch('create_category.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          closeCategoryModal();
          appendCategoryRow(data.category);
        } else {
          alert('Failed: ' + (data.error || res.status));
        }
      }catch(err){ alert('Network error'); }
    });
  }

  function appendCategoryRow(c){
    if (!categoriesTableBody || !c) return;
    const status = Number(c.is_active ?? 1) === 1 ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">Inactive</span>';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${c.id}</td>
      <td>${escapeHtml(c.name||'')}</td>
      <td>${escapeHtml(c.slug||'')}</td>
      <td>${status}</td>
      <td>${formatDate(c.created_at)}</td>
      <td><button class="icon-btn delete-category-btn" data-id="${c.id}" title="Delete"><i class="fas fa-trash"></i></button></td>`;
    categoriesTableBody.prepend(tr);
    bindDeleteCategory(tr.querySelector('.delete-category-btn'));
  }

  function bindDeleteCategory(btn){
    if (!btn) return;
    btn.addEventListener('click', async ()=>{
      const id = btn.getAttribute('data-id');
      if (!id) return;
      if (!confirm('Delete this category?')) return;
      try{
        const form = new FormData();
        form.append('id', id);
        const res = await fetch('delete_category.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          const tr = btn.closest('tr');
          if (tr) tr.remove();
        } else {
          alert('Failed to delete: ' + (data.error || res.status));
        }
      }catch(err){ alert('Network error'); }
    });
  }

  document.querySelectorAll('.delete-category-btn').forEach(bindDeleteCategory);
  if (categorySearch && categoriesTableBody){
    categorySearch.addEventListener('input', ()=>{
      const q = categorySearch.value.toLowerCase();
      categoriesTableBody.querySelectorAll('tr').forEach(tr=>{
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // Games Management - Add Game
  const addGameBtn = document.getElementById('addGameBtn');
  const gameModal = document.getElementById('gameModal');
  const gameModalClose = document.getElementById('gameModalClose');
  const gameForm = document.getElementById('gameForm');
  const gameName = document.getElementById('gameName');
  const gameSlug = document.getElementById('gameSlug');
  const gamesTableBody = document.getElementById('gamesTableBody');

  const gameModalTitle = document.getElementById('gameModalTitle');
  const gameSubmitBtn = document.getElementById('gameSubmitBtn');
  function setGameMode(edit){
    if (gameModalTitle) gameModalTitle.textContent = edit ? 'Update Game' : 'Add Game';
    if (gameSubmitBtn) gameSubmitBtn.textContent = edit ? 'Update Game' : 'Create Game';
  }
  function openGameModal(){ if (gameModal){ gameModal.style.display = 'flex'; setGameMode(!!gameForm?.getAttribute('data-edit-id')); } }
  function closeGameModal(){ if (gameModal) gameModal.style.display = 'none'; if (gameForm){ gameForm.reset(); gameForm.removeAttribute('data-edit-id'); } }
  if (addGameBtn) addGameBtn.addEventListener('click', e=>{ e.preventDefault(); openGameModal(); });
  if (gameModalClose) gameModalClose.addEventListener('click', closeGameModal);
  if (gameModal) gameModal.addEventListener('click', e=>{ if (e.target === gameModal) closeGameModal(); });

  // Auto slugify
  if (gameName && gameSlug){
    gameName.addEventListener('input', ()=>{ if (!gameSlug.value) gameSlug.value = slugify(gameName.value); });
  }

  if (gameForm){
    gameForm.addEventListener('submit', async (e)=>{
      if (gameForm.getAttribute('data-edit-id')) return; // edit handled elsewhere
      e.preventDefault();
      try{
        const form = new FormData(gameForm);
        const res = await fetch('create_game.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          closeGameModal();
          appendGameRow(data.game);
        } else {
          alert('Failed: ' + (data.error || res.status));
        }
      }catch(err){ alert('Network error'); }
    });
  }

  function appendGameRow(g){
    if (!gamesTableBody || !g) return;
    const status = Number(g.is_active ?? 1) === 1 ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">Inactive</span>';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${g.id}</td>
      <td>${escapeHtml(g.name||'')}</td>
      <td>${escapeHtml(g.slug||'')}</td>
      <td>${status}</td>
      <td>${formatDate(g.created_at)}</td>
      <td></td>`;
    gamesTableBody.prepend(tr);
  }

  // Delete Game
  function bindDeleteGame(btn){
    if (!btn) return;
    btn.addEventListener('click', async ()=>{
      const id = btn.getAttribute('data-id');
      if (!id) return;
      if (!confirm('Delete this game? Related products will also be removed.')) return;
      try{
        const form = new FormData();
        form.append('id', id);
        const res = await fetch('delete_game.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          const tr = btn.closest('tr');
          if (tr) tr.remove();
        } else { alert('Failed to delete: ' + (data.error || res.status)); }
      }catch(err){ alert('Network error'); }
    });
  }
  document.querySelectorAll('.delete-game-btn').forEach(bindDeleteGame);

  // Edit Category/Game using same modals
  document.querySelectorAll('.edit-category-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const tr = btn.closest('tr');
      if (!tr) return;
      openCategoryModal();
      if (catName) catName.value = tr.dataset.name || '';
      if (catSlug) catSlug.value = tr.dataset.slug || '';
      const select = categoryForm ? categoryForm.querySelector('select[name="is_active"]') : null;
      if (select) select.value = tr.dataset.active === '1' ? '1' : '0';
      categoryForm.setAttribute('data-edit-id', btn.getAttribute('data-id'));
      setCategoryMode(true);
    });
  });

  document.querySelectorAll('.edit-game-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const tr = btn.closest('tr');
      if (!tr) return;
      openGameModal();
      if (gameName) gameName.value = tr.dataset.name || '';
      if (gameSlug) gameSlug.value = tr.dataset.slug || '';
      const select = gameForm ? gameForm.querySelector('select[name="is_active"]') : null;
      if (select) select.value = tr.dataset.active === '1' ? '1' : '0';
      gameForm.setAttribute('data-edit-id', btn.getAttribute('data-id'));
      setGameMode(true);
    });
  });

  // Submit overrides for edit mode
  if (categoryForm){
    categoryForm.addEventListener('submit', async (e)=>{
      if (!categoryForm.getAttribute('data-edit-id')) return; // normal create handled above
      e.preventDefault();
      try{
        const id = categoryForm.getAttribute('data-edit-id');
        const form = new FormData(categoryForm);
        form.append('id', id);
        const res = await fetch('update_category.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          // Patch row in place
          const tr = document.querySelector(`#section-categories tr[data-id="${id}"]`);
          if (tr){
            tr.dataset.name = categoryForm.querySelector('input[name="name"]').value;
            tr.dataset.slug = categoryForm.querySelector('input[name="slug"]').value;
            tr.dataset.active = categoryForm.querySelector('select[name="is_active"]').value;
            tr.children[1].textContent = tr.dataset.name;
            tr.children[2].textContent = tr.dataset.slug;
            tr.children[3].innerHTML = (tr.dataset.active === '1') ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">Inactive</span>';
          }
          closeCategoryModal();
        } else { alert('Failed: ' + (data.error || res.status)); }
      }catch(err){ alert('Network error'); }
    }, { once: true });
  }
  if (gameForm){
    gameForm.addEventListener('submit', async (e)=>{
      if (!gameForm.getAttribute('data-edit-id')) return; // normal create handled above
      e.preventDefault();
      try{
        const id = gameForm.getAttribute('data-edit-id');
        const form = new FormData(gameForm);
        form.append('id', id);
        const res = await fetch('update_game.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          // Patch row in place without reload
          const tr = document.querySelector(`#section-games tr[data-id="${id}"]`);
          if (tr){
            tr.dataset.name = gameForm.querySelector('input[name="name"]').value;
            tr.dataset.slug = gameForm.querySelector('input[name="slug"]').value;
            tr.dataset.active = gameForm.querySelector('select[name="is_active"]').value;
            tr.children[1].textContent = tr.dataset.name;
            tr.children[2].textContent = tr.dataset.slug;
            tr.children[3].innerHTML = (tr.dataset.active === '1') ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">Inactive</span>';
          }
          closeGameModal();
        } else { alert('Failed: ' + (data.error || res.status)); }
      }catch(err){ alert('Network error'); }
    }, { once: true });
  }

  // Admin profile form submit
  const profileForm = document.getElementById('adminProfileForm');
  if (profileForm){
    // live preview for avatar input
    const avatarInput = profileForm.querySelector('input[name="avatar"]');
    const avatarPreview = document.getElementById('adminAvatarPreview');
    if (avatarInput && avatarPreview){
      avatarInput.addEventListener('change', ()=>{
        const f = avatarInput.files && avatarInput.files[0];
        if (f){
          const url = URL.createObjectURL(f);
          avatarPreview.src = url;
        }
      });
    }
    profileForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const formData = new FormData(profileForm);
      try{
        const res = await fetch('update_profile.php', { method: 'POST', body: formData, credentials: 'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          alert('Profile updated');
          // Update topbar name/email/avatar without full reload when possible
          const topbar = document.querySelector('.admin-topbar .admin-profile');
          if (topbar){
            const img = topbar.querySelector('img');
            if (img && avatarInput && avatarInput.files && avatarInput.files[0]){
              // bust cache
              const tmp = URL.createObjectURL(avatarInput.files[0]);
              img.src = tmp;
            }
            const nameEl = topbar.querySelector('.profile-info strong');
            const emailEl = topbar.querySelector('.profile-info small');
            if (nameEl){ nameEl.textContent = profileForm.querySelector('input[name="username"]').value; }
            if (emailEl){ emailEl.textContent = profileForm.querySelector('input[name="email"]').value; }
          }
        } else {
          alert('Failed to update: ' + (data.error || res.status));
        }
      }catch(err){
        alert('Network error');
      }
    });
  }
})();
