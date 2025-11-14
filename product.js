// ===== PRODUCT DETAIL PAGE FUNCTIONALITY =====

// Product data (defaults; will be hydrated from DOM on load)
const productData = {
    id: 1,
    name: 'Fortnite Pro Account',
    game: 'Fortnite',
    platform: 'PC',
    price: 99.99,
    originalPrice: 199.99,
    images: [
        'https://placehold.co/600x400/1FB6FF/ffffff?text=Fortnite+Pro+Main',
        'https://placehold.co/600x400/8A4DFF/ffffff?text=Fortnite+Skins',
        'https://placehold.co/600x400/4ECDC4/ffffff?text=Fortnite+Stats',
        'https://placehold.co/600x400/FF6B6B/ffffff?text=Fortnite+Inventory'
    ],
    level: '200+',
    rank: 'High',
    features: [
        'All Battle Passes (Chapter 1-4)',
        '50+ Rare & Legendary Skins',
        '15,000+ V-Bucks',
        'All Emotes & Pickaxes',
        'Victory Royale Wins: 500+',
        'K/D Ratio: 2.5+',
        'No VAC Bans or Restrictions',
        'Full Email Access'
    ],
    delivery: 'Instant',
    verified: true,
    badge: 'instant',
    rating: 4.9,
    reviews: 156,
    region: 'North America',
    seller: 'ProGamer123',
    description: 'Get this premium Fortnite account with incredible value! This high-level account comes with all the best features that any Fortnite player would want.',
    specifications: {
        stats: {
            level: '200+',
            victories: '500+',
            kd: '2.5+',
            matches: '2,000+'
        },
        inventory: {
            skins: '50+ (Rare & Legendary)',
            pickaxes: '30+',
            emotes: '40+',
            vbucks: '15,000+'
        },
        battlePass: {
            chapter1: 'Complete',
            chapter2: 'Complete',
            chapter3: 'Complete',
            chapter4: 'Complete'
        }
    }
};

let swalTheme = null;
if (window.Swal){
    swalTheme = Swal.mixin({ customClass: { popup: 'swal2-dark', confirmButton: 'btn btn-primary glow', cancelButton: 'btn btn-primary glow' }, buttonsStyling: false });
}

function hydrateFromDOM(){
    console.log('Hydrating product data from DOM...');
    
    // Price
    const priceEl = document.querySelector('.current-price');
    if (priceEl){
        const match = priceEl.textContent.replace(/[^0-9.]/g,'');
        const val = parseFloat(match);
        if (!isNaN(val)) productData.price = val;
    }
    const origEl = document.querySelector('.original-price');
    if (origEl){
        const match = origEl.textContent.replace(/[^0-9.]/g,'');
        const val = parseFloat(match);
        if (!isNaN(val)) productData.originalPrice = val; else productData.originalPrice = productData.price;
    } else {
        productData.originalPrice = productData.price;
    }
    
    // Name
    const nameEl = document.querySelector('.product-title');
    if (nameEl){ 
        productData.name = nameEl.textContent.trim(); 
        console.log('Product name:', productData.name);
    }
    
    // Images - prioritize main image
    const mainImg = document.getElementById('mainImage');
    if (mainImg && mainImg.src) {
        productData.image = mainImg.src;
        productData.images = [mainImg.src];
        console.log('Main image found:', mainImg.src);
    } else {
        // Fallback to thumbnails
        const thumbs = Array.from(document.querySelectorAll('.image-thumbnails .thumbnail img')).map(i=>i.getAttribute('src')).filter(Boolean);
        if (thumbs.length){ 
            productData.images = thumbs; 
            productData.image = thumbs[0]; 
            console.log('Using thumbnail image:', thumbs[0]);
        }
    }
    
    // Get product ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    if (productId) {
        productData.id = parseInt(productId);
        console.log('Product ID:', productId);
    }
    
    // Get game from breadcrumb
    const breadcrumbGame = document.querySelector('.breadcrumb a:nth-child(3)');
    if (breadcrumbGame) {
        productData.game = breadcrumbGame.textContent.trim();
        console.log('Game from breadcrumb:', productData.game);
    }
    
    // Get platform from attribute
    const platformAttr = document.querySelector('.attribute-info .attribute-value');
    if (platformAttr) {
        const platformText = platformAttr.textContent.trim();
        if (platformText && platformText !== '200+') {
            productData.platform = platformText;
            console.log('Platform:', productData.platform);
        }
    }
    
    // Get level from attribute
    const levelAttr = document.querySelector('.attribute-value');
    if (levelAttr && levelAttr.textContent.includes('200+')) {
        productData.level = '200+';
        console.log('Level: 200+');
    }
    
    // Get region
    const regionAttr = Array.from(document.querySelectorAll('.attribute-value')).find(el => 
        ['North America', 'Europe', 'Asia', 'Global'].includes(el.textContent.trim())
    );
    if (regionAttr) {
        productData.region = regionAttr.textContent.trim();
        console.log('Region:', productData.region);
    }
    
    // Get delivery
    const deliveryAttr = Array.from(document.querySelectorAll('.attribute-value')).find(el => 
        ['Instant', '24 Hours', '48 Hours'].includes(el.textContent.trim())
    );
    if (deliveryAttr) {
        productData.delivery = deliveryAttr.textContent.trim();
        console.log('Delivery:', productData.delivery);
    }
    
    console.log('Final product data:', productData);
}

// ===== PRODUCT FUNCTIONALITY =====
const product = {
    init: () => {
        product.initImageGallery();
        product.initTabs();
        product.initQuantityControls();
        product.initPurchaseButtons();
        product.updatePriceDisplay();
    },
    
    initImageGallery: () => {
        const mainImage = document.getElementById('mainImage');
        const thumbnails = document.querySelectorAll('.thumbnail');
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', () => {
                // Update active thumbnail
                thumbnails.forEach(t => t.classList.remove('active'));
                thumbnail.classList.add('active');
                
                // Update main image
                const newImageSrc = thumbnail.dataset.image;
                if (mainImage && newImageSrc) {
                    mainImage.src = newImageSrc;
                    
                    // Add loading effect
                    mainImage.style.opacity = '0.5';
                    setTimeout(() => {
                        mainImage.style.opacity = '1';
                    }, 200);
                }
            });
        });
    },
    
    initTabs: () => {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');
        function setActive(tab){
            tabButtons.forEach(btn => btn.classList.toggle('active', btn.dataset.tab === tab));
            tabPanels.forEach(panel => panel.classList.toggle('active', panel.id === tab));
        }
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.dataset.tab;
                setActive(targetTab);
                location.hash = targetTab;
                const panel = document.getElementById(targetTab);
                if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
        const initial = (window.location.hash || '').replace('#','') || 'description';
        if (document.getElementById(initial)) setActive(initial);
        window.addEventListener('hashchange', () => {
            const h = (window.location.hash || '').replace('#','');
            if (document.getElementById(h)) setActive(h);
        });
    },
    
    initQuantityControls: () => {
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decreaseQty');
        const increaseBtn = document.getElementById('increaseQty');
        
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                    product.updatePriceDisplay();
                }
            });
        }
        
        if (increaseBtn) {
            increaseBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                const maxValue = parseInt(quantityInput.max);
                if (currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                    product.updatePriceDisplay();
                }
            });
        }
        
        if (quantityInput) {
            quantityInput.addEventListener('input', () => {
                const value = parseInt(quantityInput.value);
                const min = parseInt(quantityInput.min);
                const max = parseInt(quantityInput.max);
                
                if (value < min) quantityInput.value = min;
                if (value > max) quantityInput.value = max;
                
                product.updatePriceDisplay();
            });
        }
    },
    
    initPurchaseButtons: () => {
        const addToCartBtn = document.getElementById('addToCart');
        const buyNowBtn = document.getElementById('buyNow');
        
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                // Validate cart object exists
                if (typeof cart === 'undefined' || !cart.add) {
                    console.error('Cart functionality not loaded');
                    alert('Cart functionality is not available. Please refresh the page.');
                    return;
                }
                
                const quantity = parseInt(document.getElementById('quantity').value) || 1;
                const productToAdd = { ...productData, quantity };
                
                cart.add(productToAdd);
                utils.showNotification(`${productData.name} added to cart!`);
            });
        }
        
        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', () => {
                // Validate cart object exists
                if (typeof cart === 'undefined' || !cart.add || !cart.open) {
                    console.error('Cart functionality not loaded');
                    alert('Cart functionality is not available. Please refresh the page.');
                    return;
                }
                
                const quantity = parseInt(document.getElementById('quantity').value) || 1;
                const productToAdd = { ...productData, quantity };
                
                // Add to cart and proceed to checkout
                cart.add(productToAdd);
                cart.open();
                
                // In a real app, this would redirect to checkout
                setTimeout(() => {
                    utils.showNotification('Redirecting to checkout...');
                }, 1000);
            });
        }
    },
    
    updatePriceDisplay: () => {
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const totalPrice = productData.price * quantity;
        const totalOriginalPrice = productData.originalPrice * quantity;
        
        // Update price in purchase buttons
        const addToCartBtn = document.getElementById('addToCart');
        const buyNowBtn = document.getElementById('buyNow');
        
        if (addToCartBtn) {
            const span = addToCartBtn.querySelector('span');
            if (span) {
                span.textContent = `Add to Cart - $${totalPrice.toFixed(2)}`;
            }
        }
        
        // Update main price display
        const currentPriceEl = document.querySelector('.current-price');
        const originalPriceEl = document.querySelector('.original-price');
        
        if (currentPriceEl) {
            currentPriceEl.textContent = `$${totalPrice.toFixed(2)}`;
        }
        
        if (originalPriceEl && quantity > 1) {
            originalPriceEl.textContent = `$${totalOriginalPrice.toFixed(2)}`;
        }
    }
};

// ===== REVIEW FUNCTIONALITY =====
const reviews = {
    init: () => {
        // Initialize any review-specific functionality
        reviews.loadReviews();
        reviews.initEditButtons();
        reviews.initAddForm();
        reviews.initDeleteButtons();
    },
    
    loadReviews: () => {
        // In a real app, this would load reviews from an API
        console.log('Loading reviews...');
    },
    
    submitReview: (rating, comment) => {
        // In a real app, this would submit to an API
        console.log('Submitting review:', { rating, comment });
        utils.showNotification('Thank you for your review!');
    },

    initEditButtons: () => {
        const buttons = document.querySelectorAll('.review-edit');
        const overlay = document.getElementById('editReviewModal');
        const form = document.getElementById('editReviewForm');
        const editStarsWrap = document.getElementById('editRatingStars');
        const editHidden = document.getElementById('editRating');
        const ti = form ? form.querySelector('input[name="title"]') : null;
        const ta = form ? form.querySelector('textarea[name="comment"]') : null;
        const closeBtn = document.getElementById('editModalClose');
        const cancelBtn = document.getElementById('editModalCancel');

        function openOverlay(){ if (overlay) overlay.style.display = 'flex'; }
        function closeOverlay(){ if (overlay) overlay.style.display = 'none'; }

        const eStars = editStarsWrap ? Array.from(editStarsWrap.querySelectorAll('.rating-star')) : [];
        function setActive(n){ eStars.forEach((s,i)=>{ s.classList.toggle('active', i < n); }); }
        eStars.forEach(star=>{ star.addEventListener('click', ()=>{ const v = parseInt(star.dataset.value||'0'); if (editHidden) editHidden.value = v>0?String(v):''; setActive(v);} ); });

        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const rating = parseInt(btn.dataset.rating || '');
                const title = btn.dataset.title || '';
                const comment = btn.dataset.comment || '';
                if (editHidden) editHidden.value = rating ? String(rating) : '';
                setActive(rating||0);
                if (ti) ti.value = title;
                if (ta) ta.value = comment;
                const reviewsTabBtn = document.querySelector('.tab-btn[data-tab="reviews"]');
                if (reviewsTabBtn && !document.getElementById('reviews').classList.contains('active')) { reviewsTabBtn.click(); }
                openOverlay();
            });
        });

        if (closeBtn) closeBtn.addEventListener('click', (e)=>{ e.preventDefault(); closeOverlay(); });
        if (cancelBtn) cancelBtn.addEventListener('click', (e)=>{ e.preventDefault(); closeOverlay(); });
        if (overlay) overlay.addEventListener('click', (e)=>{ if (e.target === overlay) closeOverlay(); });
        if (form) form.addEventListener('submit', (e)=>{
            e.preventDefault();
            if (!editHidden || !editHidden.value){
                if (swalTheme) { swalTheme.fire({ icon:'warning', title:'Please select a rating' }); }
                else if (typeof utils !== 'undefined' && utils.showNotification){ utils.showNotification('Please select a rating'); } else { alert('Please select a rating'); }
                return;
            }
            const fd = new FormData(form);
            const pid = productData.id;
            fetch(`product.php?id=${pid}`, { method:'POST', body: fd, headers: { 'X-Requested-With':'XMLHttpRequest' } })
            .then(r=>r.json())
            .then(data=>{
                if (!data || !data.ok) { throw new Error(data && data.error ? data.error : 'Failed'); }
                reviews.applyAjaxResult(data);
                closeOverlay();
                if (swalTheme) { swalTheme.fire({ icon:'success', title:'Review updated' }); }
                else if (typeof utils !== 'undefined' && utils.showNotification) { utils.showNotification('Review updated'); }
            })
            .catch(err=>{
                console.error(err);
                if (swalTheme) { swalTheme.fire({ icon:'error', title:'Error updating review' }); } else { alert('Error updating review'); }
            });
        });
    }
    ,
    initAddForm: () => {
        const form = document.getElementById('addReviewForm');
        const starsWrap = document.getElementById('addRatingStars');
        const hidden = document.getElementById('addRating');
        if (!form || !starsWrap || !hidden) return;
        const stars = Array.from(starsWrap.querySelectorAll('.rating-star'));
        function setActive(n){
            stars.forEach((s,i)=>{ s.classList.toggle('active', i < n); });
        }
        stars.forEach(star=>{
            star.addEventListener('click', ()=>{
                const val = parseInt(star.dataset.value||'0');
                hidden.value = val>0?String(val):'';
                setActive(val);
            });
        });
        form.addEventListener('submit', (e)=>{
            e.preventDefault();
            if (!hidden.value){
                if (swalTheme) { swalTheme.fire({ icon:'warning', title:'Please select a rating' }); }
                else if (typeof utils !== 'undefined' && utils.showNotification){ utils.showNotification('Please select a rating'); } else { alert('Please select a rating'); }
                return;
            }
            const fd = new FormData(form);
            const pid = productData.id;
            fetch(`product.php?id=${pid}`, { method:'POST', body: fd, headers: { 'X-Requested-With':'XMLHttpRequest' } })
            .then(r=>r.json())
            .then(data=>{
                if (!data || !data.ok) { throw new Error(data && data.error ? data.error : 'Failed'); }
                reviews.applyAjaxResult(data);
                form.reset(); hidden.value=''; setActive(0);
                if (swalTheme) { swalTheme.fire({ icon:'success', title:'Review added' }); }
                else if (typeof utils !== 'undefined' && utils.showNotification) { utils.showNotification('Review submitted'); }
            })
            .catch(err=>{
                console.error(err);
                if (swalTheme) { swalTheme.fire({ icon:'error', title:'Error submitting review' }); } else { alert('Error submitting review'); }
            });
        });
    },
    applyAjaxResult: (data) => {
        const rv = data.review;
        const owner = !!data.owner;
        const list = document.querySelector('.reviews-list');
        if (rv && list){
            const existing = list.querySelector(`.review-item[data-review-id="${rv.id}"]`);
            const html = reviews.renderItem(rv, owner);
            if (existing){ existing.outerHTML = html; } else { list.insertAdjacentHTML('afterbegin', html); }
            reviews.initEditButtons();
            reviews.initDeleteButtons();
        }
        const sum = data.summary;
        if (sum){
            const ratingScore = document.querySelector('.rating-overview .rating-score');
            const ratingCount = document.querySelector('.rating-overview .rating-count');
            if (ratingScore) ratingScore.textContent = Number(sum.avg).toFixed(1);
            if (ratingCount) ratingCount.textContent = `Based on ${sum.total} reviews`;
            const tabBtn = document.querySelector('.tab-btn[data-tab="reviews"]');
            if (tabBtn) tabBtn.textContent = `Reviews (${sum.total})`;
            const bars = document.querySelectorAll('.rating-breakdown .rating-bar');
            const tot = Math.max(sum.total,1);
            bars.forEach(bar=>{
                const label = bar.querySelector('span');
                const star = label && label.textContent ? parseInt(label.textContent) : null;
                if (!star) return;
                const count = sum.breakdown[star] || 0;
                const pct = Math.round((count/tot)*100);
                const fill = bar.querySelector('.fill');
                const right = bar.querySelectorAll('span')[1];
                if (fill) fill.style.width = pct + '%';
                if (right) right.textContent = pct + '%';
            });
            const headerStars = document.querySelectorAll('.product-rating .stars i');
            const rounded = Math.round(sum.avg);
            headerStars.forEach((s,i)=>{ s.style.color = i < rounded ? '#FFD700' : '#5B6B7C'; });
            const headerText = document.querySelector('.product-rating .rating-text');
            if (headerText) headerText.textContent = `${Number(sum.avg).toFixed(1)} (${sum.total} reviews)`;
        }
    },
    renderItem: (rv, owner) => {
        const av = rv.avatar_url || 'https://placehold.co/80x80/1FB6FF/ffffff?text=U';
        const dateStr = new Date(rv.created_at).toLocaleDateString(undefined, { month:'short', day:'numeric', year:'numeric' });
        let stars = '';
        for (let s=1;s<=5;s++){ stars += `<i class=\"fas fa-star\" style=\"color:${s<=rv.rating?'#FFD700':'#5B6B7C'}\"></i>`; }
        const vp = rv.is_verified_purchase ? '<span style=\"font-size:12px;color:#1FB6FF\">Verified Purchase</span>' : '';
        const actions = owner ? `
          <div class=\"review-actions\"> 
            <button type=\"button\" class=\"action-icon review-edit\" title=\"Edit\" data-review-id=\"${rv.id}\" data-rating=\"${rv.rating}\" data-title=\"${(rv.title||'').replace(/"/g,'&quot;')}\" data-comment=\"${(rv.comment||'').replace(/"/g,'&quot;')}\"><i class=\"fas fa-pen\"></i></button>
            <form method=\"post\" style=\"display:inline-flex;\"> 
              <input type=\"hidden\" name=\"delete_review\" value=\"1\"> 
              <input type=\"hidden\" name=\"review_id\" value=\"${rv.id}\"> 
              <button type=\"submit\" class=\"action-icon\" title=\"Delete\"><i class=\"fas fa-trash\"></i></button> 
            </form>
          </div>` : '';
        const ttl = rv.title ? `<h4 style=\"margin:0 0 6px 0; color:#CFE3FF;\">${rv.title.replace(/</g,'&lt;')}</h4>` : '';
        const cmt = (rv.comment||'').replace(/</g,'&lt;').replace(/\n/g,'<br>');
        return `
        <div class=\"review-item\" data-review-id=\"${rv.id}\"> 
          <div class=\"review-header\"> 
            <div class=\"reviewer-info\"> 
              <div class=\"reviewer-avatar\"><img src=\"${av}\" alt=\"${(rv.username||'User').replace(/</g,'&lt;')}\"></div> 
              <div class=\"reviewer-details\"> 
                <h4>${(rv.username||'User').replace(/</g,'&lt;')} ${vp}</h4> 
                <div class=\"review-rating\">${stars}</div> 
              </div> 
            </div> 
            <div class=\"review-date\">${dateStr}</div> 
          </div> 
          <div class=\"review-content\"> 
            ${ttl} 
            <p>${cmt}</p> 
            ${actions} 
          </div> 
        </div>`;
    }
    ,
    initDeleteButtons: () => {
        document.querySelectorAll('.review-actions form').forEach(form => {
            const btn = form.querySelector('button[title="Delete"]');
            if (!btn) return;
            if (btn._bound) return; btn._bound = true;
            btn.addEventListener('click', (e)=>{
                e.preventDefault();
                const run = () => {
                    const fd = new FormData(form);
                    const pid = productData.id;
                    fetch(`product.php?id=${pid}`, { method:'POST', body: fd, headers: { 'X-Requested-With':'XMLHttpRequest' } })
                    .then(r=>r.json())
                    .then(data=>{
                        if (!data || !data.ok) { throw new Error(data && data.error ? data.error : 'Failed'); }
                        const rid = data.deleted;
                        const item = document.querySelector(`.review-item[data-review-id="${rid}"]`);
                        if (item) item.remove();
                        reviews.applyAjaxResult(data);
                        if (swalTheme) { swalTheme.fire({ icon:'success', title:'Review deleted' }); } else if (typeof utils !== 'undefined' && utils.showNotification) { utils.showNotification('Review deleted'); } else { alert('Review deleted'); }
                    })
                    .catch(err=>{
                        console.error(err);
                        if (swalTheme) { swalTheme.fire({ icon:'error', title:'Error deleting review' }); } else { alert('Error deleting review'); }
                    });
                };
                if (swalTheme) {
                    swalTheme.fire({ icon:'question', title:'Delete review?', text:'This action cannot be undone', showCancelButton:true, confirmButtonText:'Delete', customClass:{ confirmButton:'btn btn-primary glow swal-danger', cancelButton:'btn btn-primary glow' } })
                    .then(res=>{ if (res.isConfirmed) run(); });
                } else {
                    if (confirm('Delete review?')) run();
                }
            });
        });
    }
};

// ===== SPECIFICATIONS FUNCTIONALITY =====
const specifications = {
    init: () => {
        // Initialize specifications display
        specifications.renderSpecs();
    },
    
    renderSpecs: () => {
        // Specifications are already rendered in HTML
        // In a real app, this might dynamically load from API
        console.log('Rendering specifications...');
    }
};

// ===== DELIVERY FUNCTIONALITY =====
const delivery = {
    init: () => {
        // Initialize delivery information
        console.log('Initializing delivery info...');
    },
    
    calculateDeliveryTime: () => {
        // Calculate estimated delivery time
        if (productData.delivery === 'Instant') {
            return 'Within 5 minutes';
        } else {
            return 'Within 24 hours';
        }
    }
};

// ===== RELATED PRODUCTS =====
const relatedProducts = {
    init: () => {
        relatedProducts.loadRelatedProducts();
    },
    
    loadRelatedProducts: () => {
        // In a real app, this would load from an API
        const relatedProductsData = [
            {
                id: 2,
                name: 'Valorant Immortal',
                price: 104.99,
                originalPrice: 149.99,
                image: 'https://via.placeholder.com/300x200/8A4DFF/ffffff?text=Valorant+Immortal',
                badge: 'verified'
            },
            {
                id: 3,
                name: 'PUBG Conqueror',
                price: 59.99,
                originalPrice: 79.99,
                image: 'https://via.placeholder.com/300x200/FF6B6B/ffffff?text=PUBG+Conqueror',
                badge: 'new'
            },
            {
                id: 4,
                name: 'GTA V Money Account',
                price: 45.99,
                originalPrice: 65.99,
                image: 'https://via.placeholder.com/300x200/4ECDC4/ffffff?text=GTA+V+Money',
                badge: 'verified'
            }
        ];
        
        // Add click handlers to related product buttons
        document.querySelectorAll('.related-products .btn').forEach((btn, index) => {
            btn.addEventListener('click', () => {
                const product = relatedProductsData[index];
                if (product) {
                    window.location.href = `product.php?id=${product.id}`;
                }
            });
        });
    }
};

// ===== URL PARAMETER HANDLING =====
const urlParams = {
    init: () => {
        const params = new URLSearchParams(window.location.search);
        const productId = params.get('id');
        
        if (productId) {
            // In a real app, this would load product data based on ID
            console.log('Loading product ID:', productId);
        }
    }
};

// ===== SOCIAL SHARING =====
const socialSharing = {
    init: () => {
        // Initialize social sharing functionality
        console.log('Initializing social sharing...');
    },
    
    shareOnFacebook: () => {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent(`Check out this ${productData.name} on Just4U Gaming!`);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`, '_blank');
    },
    
    shareOnTwitter: () => {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent(`Check out this ${productData.name} on Just4U Gaming!`);
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
    },
    
    shareOnDiscord: () => {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent(`Check out this ${productData.name} on Just4U Gaming!`);
        // Copy to clipboard
        navigator.clipboard.writeText(`${text} ${window.location.href}`);
        utils.showNotification('Link copied to clipboard!');
    }
};

// ===== WISHLIST FUNCTIONALITY =====
const wishlist = {
    init: () => {
        // Initialize wishlist functionality
        console.log('Initializing wishlist...');
    },
    
    addToWishlist: (productId) => {
        // In a real app, this would add to user's wishlist
        console.log('Adding to wishlist:', productId);
        utils.showNotification('Added to wishlist!');
    },
    
    removeFromWishlist: (productId) => {
        // In a real app, this would remove from user's wishlist
        console.log('Removing from wishlist:', productId);
        utils.showNotification('Removed from wishlist!');
    }
};

// ===== COMPARISON FUNCTIONALITY =====
const comparison = {
    init: () => {
        // Initialize product comparison
        console.log('Initializing comparison...');
    },
    
    addToComparison: (productId) => {
        // In a real app, this would add to comparison list
        console.log('Adding to comparison:', productId);
        utils.showNotification('Added to comparison!');
    }
};

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    hydrateFromDOM();
    product.init();
    reviews.init();
    specifications.init();
    delivery.init();
    relatedProducts.init();
    urlParams.init();
    socialSharing.init();
    wishlist.init();
    comparison.init();
    
    console.log('Product detail page initialized successfully!');
});

// ===== GLOBAL FUNCTIONS (for HTML onclick handlers) =====
window.product = product;
window.reviews = reviews;
window.socialSharing = socialSharing;
window.wishlist = wishlist;
window.comparison = comparison;
