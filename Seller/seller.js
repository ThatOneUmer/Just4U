(function(){
  // Section toggling with hash navigation
  const navItems = document.querySelectorAll('.admin-nav .nav-item[href^="#"]');
  const sections = [
    document.querySelector('#section-dashboard'),
    document.querySelector('#section-products'),
    document.querySelector('#section-orders'),
    document.querySelector('#section-reviews'),
    document.querySelector('#section-analytics'),
    document.querySelector('#section-support'),
    document.querySelector('#section-settings')
  ].filter(Boolean);
  
  function showSection(hash){
    // default to dashboard
    if (!hash || hash === '#') hash = '#dashboard';
    const sectionId = 'section-' + hash.substring(1);
    
    sections.forEach(sec=>{ 
      if (sec) sec.style.display = (sec.id === sectionId) ? '' : 'none'; 
    });
    navItems.forEach(a=>{
      if (a.getAttribute('href') === hash) a.classList.add('active'); 
      else a.classList.remove('active');
    });
    
    // lazy load products when switching to products
    if (hash === '#products') {
      loadMyProducts();
    }
    if (hash === '#dashboard') {
      loadDashboard();
    }
    if (hash === '#reviews') {
      loadMyReviews();
    }
  }
  
  // Handle hash changes
  window.addEventListener('hashchange', ()=>{
    showSection(window.location.hash);
  });
  
  // Show section on page load based on hash
  showSection(window.location.hash || '#dashboard');

  // Product modal controls
  const productModal = document.getElementById('productModal');
  const productModalClose = document.getElementById('productModalClose');
  const addProductBtn = document.getElementById('addProductBtn');
  const productForm = document.getElementById('productForm');
  const gameSelect = document.getElementById('gameSelect');
  const imageInput = productForm ? productForm.querySelector('input[name="image"]') : null;
  const imagePreview = document.getElementById('productImagePreview');
  const productModalTitle = document.getElementById('productModalTitle');
  const productSubmitBtn = document.getElementById('productSubmitBtn');
  const profileModal = document.getElementById('profileModal');
  const profileModalClose = document.getElementById('profileModalClose');
  const sellerProfileBtn = document.getElementById('sellerProfileBtn');
  const sellerProfileForm = document.getElementById('sellerProfileForm');
  const sellerAvatarPreview = document.getElementById('sellerAvatarPreview');

  function setProductMode(edit){
    if (productModalTitle) productModalTitle.textContent = edit ? 'Update Product' : 'Add Product';
    if (productSubmitBtn) productSubmitBtn.textContent = edit ? 'Update Product' : 'Create Product';
  }
  let quill;
  let quillSpec;
  function ensureEditor(){
    try{
      if (quill && quillSpec) return;
      if (!window.Quill){
        let tries = 0; const t = setInterval(()=>{ tries++; if (window.Quill){ clearInterval(t); ensureEditor(); } if (tries>20) clearInterval(t); }, 150);
        return;
      }
      const el = document.getElementById('productDescEditor');
      const elSpec = document.getElementById('productSpecEditor');
      if (!el || !elSpec) {
        console.error('Editor elements not found', {el, elSpec});
        return;
      }
      const toolbarConfig = [
        [{ header: [1, 2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ indent: '-1' }, { indent: '+1' }],
        [{ align: [] }],
        ['blockquote', 'code-block'],
        ['link'],
        ['clean']
      ];
      if (!quill){
        console.log('Creating Description editor...');
        quill = new Quill('#productDescEditor', {
          theme: 'snow',
          placeholder: 'Describe the account...',
          formats: ['header','bold','italic','underline','strike','list','indent','align','blockquote','code-block','link','clean'],
          bounds: '#productModal',
          modules: { toolbar: toolbarConfig }
        });
        console.log('Description editor created:', quill);
        const descHidden = productForm ? productForm.querySelector('#productDesc') : null;
        if (descHidden){
          quill.on('text-change', function(){
            let html = quill.root.innerHTML || '';
            const hasAnyTag = /<\w+[^>]*>/i.test(html);
            const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(html);
            if (!hasAnyTag || !hasBlock){
              const text = quill.getText() || '';
              html = text.replace(/\n/g, '<br>');
            }
            descHidden.value = html;
          });
        }
        const descToolbar = document.querySelector('#productDescEditor .ql-toolbar');
        if (descToolbar){
          descToolbar.addEventListener('mousedown', function(){ if (quill) quill.focus(); });
          descToolbar.addEventListener('click', function(){ if (quill) quill.focus(); });
        }
        // Force toolbar handlers
        const descTb = quill.getModule('toolbar');
        if (descTb){
          const apply = (name, value)=>{ try{ if (name==='clean'){ const sel = quill.getSelection(); if (sel) quill.removeFormat(sel.index, sel.length); } else { quill.format(name, value!==undefined ? value : true); } }catch(e){ console.error('desc format error', name, e); } };
          ['bold','italic','underline','strike','blockquote','code-block','link'].forEach(n=> descTb.addHandler(n, (v)=>apply(n,v)));
          descTb.addHandler('list', v=>apply('list', v));
          descTb.addHandler('align', v=>apply('align', v));
          descTb.addHandler('indent', v=>apply('indent', v));
          descTb.addHandler('header', v=>apply('header', v));
        }
      }
      if (!quillSpec){
        console.log('Creating Specification editor...');
        quillSpec = new Quill('#productSpecEditor', {
          theme: 'snow',
          placeholder: 'Add specifications...',
          formats: ['header','bold','italic','underline','strike','list','indent','align','blockquote','code-block','link','clean'],
          bounds: '#productModal',
          modules: { toolbar: toolbarConfig }
        });
        console.log('Specification editor created:', quillSpec);
        const specHidden = productForm ? productForm.querySelector('#productSpec') : null;
        if (specHidden){
          quillSpec.on('text-change', function(){
            let specHtml = quillSpec.root.innerHTML || '';
            const hasAnyTag = /<\w+[^>]*>/i.test(specHtml);
            const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(specHtml);
            if (!hasAnyTag || !hasBlock){
              const text = quillSpec.getText() || '';
              specHtml = text.replace(/\n/g, '<br>');
            }
            specHidden.value = specHtml;
          });
        }
        const specEditorEl = document.querySelector('#productSpecEditor .ql-editor');
        if (specEditorEl) {
          specEditorEl.addEventListener('click', function(e){
            console.log('Spec editor clicked!', e);
            if (quillSpec) {
              quillSpec.focus();
              console.log('Focused spec editor');
            }
          });
        }
        const specToolbar = document.querySelector('#productSpecEditor .ql-toolbar');
        if (specToolbar){
          specToolbar.addEventListener('mousedown', function(){ if (quillSpec) quillSpec.focus(); });
          specToolbar.addEventListener('click', function(){ if (quillSpec) quillSpec.focus(); });
        }
        // Force toolbar handlers
        const specTb = quillSpec.getModule('toolbar');
        if (specTb){
          const apply2 = (name, value)=>{ try{ if (name==='clean'){ const sel = quillSpec.getSelection(); if (sel) quillSpec.removeFormat(sel.index, sel.length); } else { quillSpec.format(name, value!==undefined ? value : true); } }catch(e){ console.error('spec format error', name, e); } };
          ['bold','italic','underline','strike','blockquote','code-block','link'].forEach(n=> specTb.addHandler(n, (v)=>apply2(n,v)));
          specTb.addHandler('list', v=>apply2('list', v));
          specTb.addHandler('align', v=>apply2('align', v));
          specTb.addHandler('indent', v=>apply2('indent', v));
          specTb.addHandler('header', v=>apply2('header', v));
        }
      }
      // Force enable immediately
      if (quill) quill.enable(true);
      if (quillSpec) {
        quillSpec.enable(true);
        // Try to make it editable
        const specEditor = document.querySelector('#productSpecEditor .ql-editor');
        if (specEditor) {
          specEditor.contentEditable = 'true';
          console.log('Set contentEditable to true on spec editor');
        }
        // Ensure container not marked disabled
        const specContainer = document.querySelector('#productSpecEditor .ql-container');
        if (specContainer) { specContainer.classList.remove('ql-disabled'); }
      }
      
      // And again after a delay
      setTimeout(()=>{
        if (quill) {
          quill.enable(true);
          console.log('Description editor enabled, disabled?', quill.isEnabled() === false);
          const descContainer = document.querySelector('#productDescEditor .ql-container');
          if (descContainer) { descContainer.classList.remove('ql-disabled'); }
        }
        if (quillSpec) {
          quillSpec.enable(true);
          console.log('Specification editor enabled, disabled?', quillSpec.isEnabled() === false);
          // Force contentEditable again
          const specEditor = document.querySelector('#productSpecEditor .ql-editor');
          if (specEditor) {
            specEditor.contentEditable = 'true';
            specEditor.setAttribute('contenteditable', 'true');
            console.log('ContentEditable attribute:', specEditor.getAttribute('contenteditable'));
          }
          const specContainer = document.querySelector('#productSpecEditor .ql-container');
          if (specContainer) { specContainer.classList.remove('ql-disabled'); }
        }
      }, 200);
    }catch(e){ console.error('Editor init error:', e); }
  }
  function openProductModal(){ 
    if (productModal){ 
      productModal.style.display='flex'; 
      setProductMode(!!productForm?.getAttribute('data-edit-id')); 
      loadGamesOnce(); 
      ensureEditor(); 
      setTimeout(()=>{
        try{ 
          if (quill) {
            quill.enable(true);
            console.log('Modal opened: Description enabled');
          }
          if (quillSpec) {
            quillSpec.enable(true);
            console.log('Modal opened: Specification enabled');
            // Force focus to test if it works
            const specEditor = document.querySelector('#productSpecEditor .ql-editor');
            if (specEditor) {
              console.log('Spec editor element found:', specEditor);
              console.log('Spec editor styles:', window.getComputedStyle(specEditor).pointerEvents);
            }
          }
        }catch(e){ console.error('Enable error:', e); }
      }, 300);
    } 
  }
  function closeProductModal(){ if (productModal){ productModal.style.display='none'; if (productForm){ productForm.reset(); productForm.removeAttribute('data-edit-id'); } try{ quill && quill.setContents([]); quillSpec && quillSpec.setContents([]); }catch(e){} if (imagePreview) imagePreview.src = 'https://placehold.co/96x72/0F1620/FFFFFF?text=IMG'; setProductMode(false); } }
  if (addProductBtn){ addProductBtn.addEventListener('click', (e)=>{ e.preventDefault(); openProductModal(); }); }
  if (productModalClose){ productModalClose.addEventListener('click', closeProductModal); }
  if (productModal){ productModal.addEventListener('click', (e)=>{ if (e.target === productModal) closeProductModal(); }); }

  function openProfileModal(){ if (profileModal){ profileModal.style.display = 'flex'; } }
  function closeProfileModal(){ if (profileModal){ profileModal.style.display = 'none'; if (sellerProfileForm){ sellerProfileForm.reset(); } } }
  if (sellerProfileBtn){ sellerProfileBtn.addEventListener('click', (e)=>{ e.preventDefault(); openProfileModal(); }); }
  if (profileModalClose){ profileModalClose.addEventListener('click', closeProfileModal); }
  if (profileModal){ profileModal.addEventListener('click', (e)=>{ if (e.target === profileModal) closeProfileModal(); }); }

  if (sellerProfileForm){
    const avatarInput = sellerProfileForm.querySelector('input[name="avatar"]');
    if (avatarInput && sellerAvatarPreview){
      avatarInput.addEventListener('change', ()=>{
        const f = avatarInput.files && avatarInput.files[0];
        if (f){ sellerAvatarPreview.src = URL.createObjectURL(f); }
      });
    }
    sellerProfileForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const formData = new FormData(sellerProfileForm);
      try{
        const res = await fetch('update_profile.php', { method: 'POST', body: formData, credentials: 'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          const topbar = document.querySelector('.admin-topbar .admin-profile');
          if (topbar){
            const img = topbar.querySelector('img');
            const nameEl = topbar.querySelector('.profile-info strong');
            const emailEl = topbar.querySelector('.profile-info small');
            const username = sellerProfileForm.querySelector('input[name="username"]').value;
            if (nameEl) nameEl.textContent = username;
            if (emailEl){ }
            const avatarInput2 = sellerProfileForm.querySelector('input[name="avatar"]');
            if (img && avatarInput2 && avatarInput2.files && avatarInput2.files[0]){
              const tmp = URL.createObjectURL(avatarInput2.files[0]);
              img.src = tmp;
            }
          }
          closeProfileModal();
        } else {
          alert(data.error || 'Update failed');
        }
      }catch(err){ alert('Network error'); }
    });
  }

  // Image preview
  if (imageInput && imagePreview){
    imageInput.addEventListener('change', ()=>{
      const f = imageInput.files && imageInput.files[0];
      if (f){ imagePreview.src = URL.createObjectURL(f); }
    });
  }

  let gamesLoaded = false;
  async function loadGamesOnce(){
    if (gamesLoaded || !gameSelect) return;
    try{
      const res = await fetch('list_games.php', { credentials: 'same-origin' });
      const data = await res.json();
      if (res.ok && data.ok){
        gameSelect.innerHTML = data.games.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
        gamesLoaded = true;
      }
    }catch(e){}
  }

  // Create/Update product submit
  if (productForm){
    productForm.addEventListener('submit', async (e)=>{
      // If in edit mode, let the edit handler handle it below
      if (productForm.getAttribute('data-edit-id')) return;
      e.preventDefault();
      try{
        let html = quill ? quill.root.innerHTML : (productForm.querySelector('#productDesc').value || '');
        if (quill){
          const hasAnyTag = /<\w+[^>]*>/i.test(html);
          const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(html);
          if (!hasAnyTag || !hasBlock){
            const text = quill.getText() || '';
            html = text.replace(/\n/g, '<br>');
          }
        }
        productForm.querySelector('#productDesc').value = html;
        let specHtml = quillSpec ? quillSpec.root.innerHTML : (productForm.querySelector('#productSpec').value || '');
        if (quillSpec){
          const hasAnyTag = /<\w+[^>]*>/i.test(specHtml);
          const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(specHtml);
          if (!hasAnyTag || !hasBlock){
            const text = quillSpec.getText() || '';
            specHtml = text.replace(/\n/g, '<br>');
          }
        }
        productForm.querySelector('#productSpec').value = specHtml;
      }catch(e){}
      const form = new FormData(productForm);
      try{
        const res = await fetch('create_product.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          closeProductModal();
          const tbody = document.getElementById('sellerProductsBody');
          if (tbody){ tbody.setAttribute('data-loaded','0'); }
          loadMyProducts();
          alert('Product created');
        } else {
          alert('Failed to create: ' + (data.error || res.status));
        }
      }catch(err){ alert('Network error'); }
    });
  }
  async function loadMyProducts(){
    const tbody = document.getElementById('sellerProductsBody');
    if (!tbody) return;
    // if already loaded, skip
    if (tbody.getAttribute('data-loaded') === '1') return;
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>';
    try{
      const res = await fetch('get_my_products.php', { credentials: 'same-origin' });
      const data = await res.json();
      if (!res.ok || !data.ok){
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#FF6B6B">Failed to load products</td></tr>';
        return;
      }
      const rows = (data.products || []).map(p=>{
        const img = p.image_url ? ('../' + p.image_url) : 'https://placehold.co/56x40/0F1620/FFFFFF?text=IMG';
        const statusPill = p.status === 'active' ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">'+ (p.status||'') +'</span>';
        return `
          <tr data-id="${p.id}">
            <td><img src="${img}" alt="" style="width:56px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border)"></td>
            <td>${escapeHtml(p.title || '')}</td>
            <td>$${Number(p.price||0).toFixed(2)}</td>
            <td>${statusPill}</td>
            <td>${formatDate(p.created_at)}</td>
            <td>
              <button class="icon-btn edit-product-btn" data-id="${p.id}" title="Edit"><i class="fas fa-pen"></i></button>
              <button class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
          </tr>`;
      }).join('');
      tbody.innerHTML = rows || '<tr><td colspan="6" style="text-align:center;color:var(--admin-muted)">No products yet</td></tr>';
      tbody.setAttribute('data-loaded', '1');
      bindSearch();
      bindEditProducts();
      bindDeleteMyProducts();
    }catch(e){
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#FF6B6B">Network error</td></tr>';
    }
  }

  function bindSearch(){
    const search = document.getElementById('sellerProductSearch');
    const tbody = document.getElementById('sellerProductsBody');
    if (!search || !tbody) return;
    search.addEventListener('input', ()=>{
      const q = search.value.toLowerCase();
      tbody.querySelectorAll('tr').forEach(tr=>{
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  function escapeHtml(str){
    return String(str).replace(/[&<>"]+/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]));
  }
  function formatDate(dt){
    if (!dt) return '';
    try { return new Date(dt.replace(' ', 'T')).toLocaleDateString(); } catch { return dt; }
  }

  function prepareEditors(){
    try{
      let html = quill ? quill.root.innerHTML : (productForm.querySelector('#productDesc').value || '');
      if (quill){
        const hasAnyTag = /<\w+[^>]*>/i.test(html);
        const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(html);
        if (!hasAnyTag || !hasBlock){
          const text = quill.getText() || '';
          html = text.replace(/\n/g, '<br>');
        }
      }
      productForm.querySelector('#productDesc').value = html;
      let specHtml = quillSpec ? quillSpec.root.innerHTML : (productForm.querySelector('#productSpec').value || '');
      if (quillSpec){
        const hasAnyTag = /<\w+[^>]*>/i.test(specHtml);
        const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(specHtml);
        if (!hasAnyTag || !hasBlock){
          const text = quillSpec.getText() || '';
          specHtml = text.replace(/\n/g, '<br>');
        }
      }
      productForm.querySelector('#productSpec').value = specHtml;
    }catch(e){}
  }

  async function submitCreate(){
    prepareEditors();
    const form = new FormData(productForm);
    try{
      const res = await fetch('create_product.php', { method:'POST', body: form, credentials:'same-origin' });
      const text = await res.text();
      let data; try{ data = JSON.parse(text); }catch{ data = { ok:false, error: text || 'Invalid response' }; }
      if (res.ok && data.ok){
        closeProductModal();
        const tbody = document.getElementById('sellerProductsBody');
        if (tbody){ tbody.setAttribute('data-loaded','0'); await loadMyProducts(); }
        if (window.Swal){ Swal.fire({ icon:'success', title:'Product created' }); } else { alert('Product created'); }
      } else {
        if (window.Swal){ Swal.fire({ icon:'error', title:'Failed to create', text: data.error || String(res.status) }); } else { alert('Failed to create: ' + (data.error || res.status)); }
      }
    }catch(err){ alert('Network error'); }
  }

  async function submitUpdate(){
    const id = productForm.getAttribute('data-edit-id');
    if (!id) return;
    prepareEditors();
    try{
      const form = new FormData(productForm);
      form.append('id', id);
      const res = await fetch('update_product.php', { method:'POST', body: form, credentials:'same-origin' });
      const text = await res.text();
      let data; try{ data = JSON.parse(text); }catch{ data = { ok:false, error: text || 'Invalid response' }; }
      if (res.ok && data.ok){
        const tr = document.querySelector(`#sellerProductsBody tr[data-id="${id}"]`);
        if (tr){
          tr.children[1].textContent = productForm.querySelector('input[name="title"]').value;
          tr.children[2].textContent = '$' + Number(productForm.querySelector('input[name="price"]').value || 0).toFixed(2);
          const statusCell = tr.children[3];
          statusCell.innerHTML = (data.status === 'active') ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">'+ (data.status||'') +'</span>';
          const imgCell = tr.children[0].querySelector('img');
          if (imgCell && data.image_url){ imgCell.src = '../' + data.image_url; }
        }
        closeProductModal();
        if (window.Swal){ Swal.fire({ icon:'success', title:'Product updated' }); } else { alert('Product updated'); }
      } else {
        if (window.Swal){ Swal.fire({ icon:'error', title:'Failed to update', text: data.error || String(res.status) }); } else { alert('Failed to update: ' + (data.error || res.status)); }
      }
    }catch(err){ alert('Network error'); }
  }

  if (productSubmitBtn){
    productSubmitBtn.addEventListener('click', async (e)=>{
      e.preventDefault();
      if (productForm.getAttribute('data-edit-id')){ await submitUpdate(); } else { await submitCreate(); }
    });
  }

  async function loadDashboard(){
    const section = document.getElementById('section-dashboard');
    if (!section) return;
    if (section.getAttribute('data-loaded') === '1') return;
    const elSales = document.getElementById('kpiTotalSales');
    const elActive = document.getElementById('kpiActiveProducts');
    const elPending = document.getElementById('kpiOrdersPending');
    const elRating = document.getElementById('kpiAvgRating');
    const recentList = document.getElementById('recentOrdersList');
    const topList = document.getElementById('topProductsList');
    const alertsList = document.getElementById('alertsList');
    const overviewBody = document.getElementById('ordersOverviewBody');
    if (recentList) recentList.innerHTML = '<li style="color:var(--admin-muted)">Loading...</li>';
    if (topList) topList.innerHTML = '<li style="color:var(--admin-muted)">Loading...</li>';
    if (alertsList) alertsList.innerHTML = '<li style="color:var(--admin-muted)">Loading...</li>';
    if (overviewBody) overviewBody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>';
    try{
      const res = await fetch('get_dashboard_data.php', { credentials: 'same-origin' });
      const data = await res.json();
      if (!res.ok || !data.ok){
        if (recentList) recentList.innerHTML = '<li style="color:#FF6B6B">Failed to load</li>';
        if (topList) topList.innerHTML = '<li style="color:#FF6B6B">Failed to load</li>';
        if (alertsList) alertsList.innerHTML = '<li style="color:#FF6B6B">Failed to load</li>';
        if (overviewBody) overviewBody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#FF6B6B">Failed to load</td></tr>';
        return;
      }
      const kpis = data.kpis || {};
      if (elSales) elSales.textContent = '$' + Number(kpis.total_sales || 0).toFixed(2);
      if (elActive) elActive.textContent = Number(kpis.active_products || 0);
      if (elPending) elPending.textContent = Number(kpis.orders_pending || 0);
      if (elRating) elRating.textContent = Number(kpis.avg_rating || 0).toFixed(1);

      const ro = (data.recent_orders || []).map(o=>{
        const ps = String(o.payment_status || '').toLowerCase();
        const cls = ps === 'paid' ? 'success' : 'warning';
        const st = ps === 'paid' ? 'Paid' : (o.status || 'Pending');
        return `<li><span>#${escapeHtml(o.order_number || '')}</span><span class="status-pill ${cls}">${escapeHtml(st)}</span></li>`;
      }).join('');
      if (recentList) recentList.innerHTML = ro || '<li style="color:var(--admin-muted)">No recent orders</li>';

      const tp = (data.top_products || []).map(p=>{
        const status = String(p.status || '').toLowerCase();
        const cls = status === 'active' ? 'success' : 'warning';
        return `<li><span>${escapeHtml(p.title || '')}</span><span class="status-pill ${cls}">${escapeHtml(p.status || '')}</span></li>`;
      }).join('');
      if (topList) topList.innerHTML = tp || '<li style="color:var(--admin-muted)">No top products</li>';

      const al = (data.alerts || []).map(a=>{
        const type = String(a.type || 'success');
        const cls = type === 'warning' ? 'warning' : 'success';
        const label = cls === 'warning' ? 'Action' : 'Good';
        return `<li><span>${escapeHtml(a.text || '')}</span><span class="status-pill ${cls}">${label}</span></li>`;
      }).join('');
      if (alertsList) alertsList.innerHTML = al || '<li style="color:var(--admin-muted)">No alerts</li>';

      const ob = (data.orders_overview || []).map(o=>{
        const ps = String(o.payment_status || '').toLowerCase();
        const cls = ps === 'paid' ? 'success' : 'warning';
        const st = ps === 'paid' ? 'Paid' : (o.status || 'Pending');
        return `<tr>
          <td>#${escapeHtml(o.order_number || '')}</td>
          <td>${escapeHtml(o.product_title || '')}</td>
          <td>${escapeHtml(o.buyer_username || '')}</td>
          <td>$${Number(o.total_amount || 0).toFixed(2)}</td>
          <td><span class="status-pill ${cls}">${escapeHtml(st)}</span></td>
          <td><button class="icon-btn"><i class="fas fa-ellipsis"></i></button></td>
        </tr>`;
      }).join('');
      if (overviewBody) overviewBody.innerHTML = ob || '<tr><td colspan="6" style="text-align:center;color:var(--admin-muted)">No orders</td></tr>';

      section.setAttribute('data-loaded', '1');
    }catch(e){
      if (recentList) recentList.innerHTML = '<li style="color:#FF6B6B">Network error</li>';
      if (topList) topList.innerHTML = '<li style="color:#FF6B6B">Network error</li>';
      if (alertsList) alertsList.innerHTML = '<li style="color:#FF6B6B">Network error</li>';
      if (overviewBody) overviewBody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#FF6B6B">Network error</td></tr>';
    }
  }

  async function loadMyReviews(){
    const tbody = document.getElementById('sellerReviewsBody');
    if (!tbody) return;
    if (tbody.getAttribute('data-loaded') === '1') return;
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--admin-muted)">Loading...</td></tr>';
    try{
      const res = await fetch('get_my_reviews.php', { credentials:'same-origin' });
      const data = await res.json();
      if (!res.ok || !data.ok){
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#FF6B6B">Failed to load reviews</td></tr>';
        return;
      }
      const rows = (data.reviews||[]).map(r=>{
        const ratingStars = '★'.repeat(Number(r.rating||0)) + '☆'.repeat(5-Number(r.rating||0));
        const title = escapeHtml(r.title||'');
        const comment = escapeHtml((r.comment||'').slice(0,140)) + ((r.comment||'').length>140?'…':'');
        return `
          <tr data-id="${r.id}">
            <td>${escapeHtml(r.product_title||'')}</td>
            <td>${escapeHtml(r.customer_username||'')}</td>
            <td>${ratingStars}</td>
            <td>${title}</td>
            <td>${comment}</td>
            <td>${formatDate(r.created_at)}</td>
            <td>
              <form class="seller-delete-review" method="post" action="delete_review.php" style="display:inline-flex">
                <input type="hidden" name="review_id" value="${r.id}">
                <button type="submit" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>`;
      }).join('');
      tbody.innerHTML = rows || '<tr><td colspan="7" style="text-align:center;color:var(--admin-muted)">No reviews yet</td></tr>';
      tbody.setAttribute('data-loaded','1');
      bindReviewSearch();
      bindDeleteReviews();
    }catch(e){
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#FF6B6B">Network error</td></tr>';
    }
  }

  function bindReviewSearch(){
    const search = document.getElementById('sellerReviewSearch');
    const tbody = document.getElementById('sellerReviewsBody');
    if (!search || !tbody) return;
    search.addEventListener('input', ()=>{
      const q = search.value.toLowerCase();
      tbody.querySelectorAll('tr').forEach(tr=>{
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  function bindDeleteReviews(){
    document.querySelectorAll('.seller-delete-review').forEach(form=>{
      const btn = form.querySelector('button[type="submit"]');
      if (!btn || btn._bound) return; btn._bound = true;
      btn.addEventListener('click', async (e)=>{
        e.preventDefault();
        const run = async ()=>{
          try{
            const fd = new FormData(form);
            const res = await fetch('delete_review.php', { method:'POST', body: fd, credentials:'same-origin' });
            const data = await res.json();
            if (res.ok && data.ok){
              const rid = String(fd.get('review_id'));
              const tr = document.querySelector(`#sellerReviewsBody tr[data-id="${rid}"]`);
              if (tr) tr.remove();
              if (window.Swal){ Swal.fire({ icon:'success', title:'Review deleted' }); } else { alert('Review deleted'); }
            } else {
              if (window.Swal){ Swal.fire({ icon:'error', title:'Failed to delete', text: data.error || res.status }); } else { alert('Failed: ' + (data.error || res.status)); }
            }
          }catch(err){ if (window.Swal){ Swal.fire({ icon:'error', title:'Network error' }); } else { alert('Network error'); } }
        };
        if (window.Swal){ Swal.fire({ icon:'question', title:'Delete review?', showCancelButton:true, confirmButtonText:'Delete' }).then(r=>{ if (r.isConfirmed) run(); }); } else { if (confirm('Delete review?')) run(); }
      });
    });
  }

  // Edit product flow
  function bindEditProducts(){
    document.querySelectorAll('.edit-product-btn').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-id');
        if (!id) return;
        try{
          // Ensure games loaded so we can set the select value
          await loadGamesOnce();
          const res = await fetch('get_product.php?id=' + encodeURIComponent(id), { credentials:'same-origin' });
          const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
          if (res.ok && data.ok){
            const p = data.product;
            productForm.setAttribute('data-edit-id', id);
            setProductMode(true);
            if (imagePreview) imagePreview.src = p.image_url ? ('../' + p.image_url) : 'https://placehold.co/96x72/0F1620/FFFFFF?text=IMG';
            productForm.querySelector('input[name="title"]').value = p.title || '';
            productForm.querySelector('input[name="price"]').value = p.price || '';
            if (gameSelect) gameSelect.value = String(p.game_id || '');
            const plat = productForm.querySelector('select[name="platform"]');
            if (plat) plat.value = p.platform || 'PC';
            productForm.querySelector('input[name="region"]').value = p.region || '';
            try{
              ensureEditor();
              const raw = p.description || '';
              const hasTags = /<\w+[^>]*>/i.test(raw);
              const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(raw);
              if (quill){
                if (hasTags && hasBlock){ quill.clipboard.dangerouslyPasteHTML(raw); }
                else {
                  const parts = raw.split(/\r?\n/);
                  const html = parts.map(line => line ? `<p>${line.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</p>` : '<p><br></p>').join('');
                  quill.clipboard.dangerouslyPasteHTML(html || '<p><br></p>');
                }
              } else {
                productForm.querySelector('textarea[name="description"]').value = raw;
              }
              const rawSpec = p.specification || '';
              const hasTagsSpec = /<\w+[^>]*>/i.test(rawSpec);
              const hasBlockSpec = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(rawSpec);
              if (quillSpec){
                if (hasTagsSpec && hasBlockSpec){ quillSpec.clipboard.dangerouslyPasteHTML(rawSpec); }
                else {
                  const parts = rawSpec.split(/\r?\n/);
                  const html = parts.map(line => line ? `<p>${line.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</p>` : '<p><br></p>').join('');
                  quillSpec.clipboard.dangerouslyPasteHTML(html || '<p><br></p>');
                }
              } else {
                productForm.querySelector('textarea[name="specification"]').value = rawSpec;
              }
            }catch(e){ productForm.querySelector('textarea[name="description"]').value = p.description || ''; productForm.querySelector('textarea[name="specification"]').value = p.specification || ''; }
            openProductModal();
          } else {
            alert('Failed to load product: ' + (data.error || res.status));
          }
        }catch(err){ alert('Network error'); }
      });
    });
  }

  function bindDeleteMyProducts(){
    const tbody = document.getElementById('sellerProductsBody');
    if (!tbody) return;
    tbody.querySelectorAll('tr').forEach(tr=>{
      const delBtn = tr.querySelector('button.icon-btn[title="Delete"]');
      if (!delBtn || delBtn._bound) return; delBtn._bound = true;
      delBtn.addEventListener('click', async (e)=>{
        e.preventDefault();
        const id = tr.getAttribute('data-id');
        if (!id) return;
        const run = async ()=>{
          try{
            const fd = new FormData(); fd.append('id', id);
            const res = await fetch('delete_product.php', { method:'POST', body: fd, credentials:'same-origin' });
            const data = await res.json();
            if (res.ok && data.ok){
              tr.remove();
              if (window.Swal){ Swal.fire({ icon:'success', title:'Product deleted' }); } else { alert('Product deleted'); }
            } else {
              if (window.Swal){ Swal.fire({ icon:'error', title:'Failed to delete', text: data.error || res.status }); } else { alert('Failed: ' + (data.error || res.status)); }
            }
          }catch(err){ if (window.Swal){ Swal.fire({ icon:'error', title:'Network error' }); } else { alert('Network error'); } }
        };
        if (window.Swal){ Swal.fire({ icon:'warning', title:'Delete product?', showCancelButton:true, confirmButtonText:'Delete' }).then(r=>{ if (r.isConfirmed) run(); }); } else { if (confirm('Delete product?')) run(); }
      });
    });
  }

  // Handle update submit when in edit mode
  if (productForm){
    productForm.addEventListener('submit', async (e)=>{
      if (!productForm.getAttribute('data-edit-id')) return; // create handler processes normal
      e.preventDefault();
      const id = productForm.getAttribute('data-edit-id');
      try{
        let html = quill ? quill.root.innerHTML : (productForm.querySelector('#productDesc').value || '');
        if (quill){
          const hasAnyTag = /<\w+[^>]*>/i.test(html);
          const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(html);
          if (!hasAnyTag || !hasBlock){
            const text = quill.getText() || '';
            html = text.replace(/\n/g, '<br>');
          }
        }
        productForm.querySelector('#productDesc').value = html;
        let specHtml = quillSpec ? quillSpec.root.innerHTML : (productForm.querySelector('#productSpec').value || '');
        if (quillSpec){
          const hasAnyTag = /<\w+[^>]*>/i.test(specHtml);
          const hasBlock = /<(p|div|ul|ol|li|br|h[1-6])\b/i.test(specHtml);
          if (!hasAnyTag || !hasBlock){
            const text = quillSpec.getText() || '';
            specHtml = text.replace(/\n/g, '<br>');
          }
        }
        productForm.querySelector('#productSpec').value = specHtml;
      }catch(e){}
      try{
        const form = new FormData(productForm);
        form.append('id', id);
        const res = await fetch('update_product.php', { method:'POST', body: form, credentials:'same-origin' });
        const data = await res.json().catch(()=>({ok:false,error:'Invalid response'}));
        if (res.ok && data.ok){
          // Patch row in table
          const tr = document.querySelector(`#sellerProductsBody tr[data-id="${id}"]`);
          if (tr){
            tr.children[1].textContent = productForm.querySelector('input[name="title"]').value;
            tr.children[2].textContent = '$' + Number(productForm.querySelector('input[name="price"]').value || 0).toFixed(2);
            const statusCell = tr.children[3];
            statusCell.innerHTML = (data.status === 'active') ? '<span class="status-pill success">Active</span>' : '<span class="status-pill warning">'+ (data.status||'') +'</span>';
            const imgCell = tr.children[0].querySelector('img');
            if (imgCell && data.image_url){ imgCell.src = '../' + data.image_url; }
          }
          closeProductModal();
          alert('Product updated');
        } else {
          alert('Failed to update: ' + (data.error || res.status));
        }
      }catch(err){ alert('Network error'); }
    });
  }
})();
