// Theme Toggle (Light / Dark Mode with localStorage persistence — CSP safe)
(function() {
  const meta = document.querySelector('meta[name="color-scheme"]');
  function readSavedTheme() {
    try {
      const saved = localStorage.getItem('gn_theme');
      if (saved === 'light' || saved === 'dark') return saved;
    } catch (e) {}
    return document.documentElement.getAttribute('data-theme') || 'dark';
  }

  function applyTheme(theme) {
    const mode = theme === 'light' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', mode);
    document.documentElement.style.colorScheme = mode;
    if (meta) meta.setAttribute('content', mode);
    const btn = document.getElementById('theme-toggle');
    if (btn) {
      const isLight = mode === 'light';
      const icon = btn.querySelector('.theme-toggle__icon');
      const text = btn.querySelector('.theme-toggle__text');
      if (icon) icon.textContent = isLight ? '☾' : '☀';
      if (text) text.textContent = isLight ? 'Tối' : 'Sáng';
      btn.setAttribute('aria-label', isLight ? 'Tối — Đổi giao diện' : 'Sáng — Đổi giao diện');
      btn.setAttribute('aria-pressed', isLight ? 'true' : 'false');
    }
  }

  let current = readSavedTheme();
  applyTheme(current);

  const btn = document.getElementById('theme-toggle');
  if (btn) {
    btn.addEventListener('click', () => {
      current = (document.documentElement.getAttribute('data-theme') === 'light') ? 'dark' : 'light';
      try { localStorage.setItem('gn_theme', current); } catch (e) {}
      applyTheme(current);
    });
  }

  window.addEventListener('pageshow', () => {
    current = readSavedTheme();
    applyTheme(current);
  });
})();

document.querySelectorAll('[data-go-back]').forEach(button => {
  button.addEventListener('click', () => history.length > 1 ? history.back() : location.assign('./'));
});

const source = document.getElementById('content');
const editorRoot = document.getElementById('rich-editor');
const postForm = document.getElementById('post-form');
let editorInstance = null;

if (source && editorRoot && window.pell) {
  editorRoot.hidden = false;
  const actions = [
    {name:'bold',title:'B - In đậm'}, {name:'italic',title:'I - In nghiêng'},
    {name:'underline',title:'U - Gạch chân'}, {name:'heading2',title:'H2 - Tiêu đề mục'},
    {name:'paragraph',title:'Đoạn văn'}, {name:'quote',title:'Trích dẫn'},
    {name:'olist',title:'Danh sách đánh số'}, {name:'ulist',title:'Danh sách'},
    {name:'code',title:'Đoạn mã'},
    {name:'link',title:'Chèn liên kết',result:()=>{
      const href = prompt('Nhập liên kết bắt đầu bằng https://, http:// hoặc mailto:');
      if (href && /^(https?:\/\/|mailto:)/i.test(href.trim())) window.pell.exec('createLink',href.trim());
    }},
    {name:'undo',icon:'↶',title:'Hoàn tác',result:()=>window.pell.exec('undo')},
    {name:'redo',icon:'↷',title:'Làm lại',result:()=>window.pell.exec('redo')}
  ];
  editorInstance = window.pell.init({
    element:editorRoot,
    defaultParagraphSeparator:'p',
    styleWithCSS:false,
    actions,
    onChange:html=>{ source.value=html; }
  });
  editorInstance.content.innerHTML=source.value;
  editorInstance.content.setAttribute('role','textbox');
  editorInstance.content.setAttribute('aria-label','Nội dung bài viết');
  editorInstance.content.setAttribute('aria-multiline','true');
  editorInstance.content.dataset.testid='editor-content';
  source.hidden=true;
  if (postForm) {
    postForm.addEventListener('submit',()=>{source.value=editorInstance.content.innerHTML;});
  }
  editorRoot.querySelectorAll('button').forEach(button=>{
    button.type='button';
    button.setAttribute('aria-label',button.title);
  });
}

const cover=document.getElementById('cover');
if(cover){
  const previewEl=document.getElementById('cover-preview');
  const initialPreviewSrc=previewEl?previewEl.src:'';
  let blob;
  cover.addEventListener('change',()=>{
    const file=cover.files[0];
    if(!file){
      cover.setCustomValidity('');
      if(blob){URL.revokeObjectURL(blob);blob=null;}
      if(previewEl&&initialPreviewSrc)previewEl.src=initialPreviewSrc;
      return;
    }
    if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>2097152){
      cover.setCustomValidity('Chọn ảnh JPEG, PNG hoặc WebP không quá 2 MB.');cover.reportValidity();return;
    }
    cover.setCustomValidity('');
    if(blob)URL.revokeObjectURL(blob);
    blob=URL.createObjectURL(file);
    if(previewEl)previewEl.src=blob;
  });
}

// Unsaved Changes Protection for Post Editor (#post-form)
if (postForm) {
  const titleInput = document.getElementById('title');
  const excerptInput = document.getElementById('excerpt');
  const categorySelect = document.getElementById('category_id');
  const featuredCheckbox = postForm.querySelector('input[name="is_featured"]');
  let isSubmittingPost = false;
  let isLeavingConfirmed = false;

  function snapshotEditorState() {
    const titleVal = titleInput ? titleInput.value.trim() : '';
    const excerptVal = excerptInput ? excerptInput.value.trim() : '';
    const contentVal = editorInstance ? editorInstance.content.innerHTML.trim() : (source ? source.value.trim() : '');
    const catVal = categorySelect ? categorySelect.value : '';
    const checkedTags = Array.from(postForm.querySelectorAll('input[name="tags[]"]:checked')).map(el => el.value).join(',');
    const isFeaturedVal = featuredCheckbox ? featuredCheckbox.checked : false;
    return JSON.stringify({ titleVal, excerptVal, contentVal, catVal, checkedTags, isFeaturedVal });
  }

  const initialSnapshot = snapshotEditorState();

  function isPostDirty() {
    if (cover && cover.files && cover.files.length > 0) return true;
    return snapshotEditorState() !== initialSnapshot;
  }

  postForm.addEventListener('submit', () => {
    isSubmittingPost = true;
  });

  // Warn when clicking any navigation link (<a href="...">) if there are unsaved changes
  document.addEventListener('click', event => {
    if (isSubmittingPost || isLeavingConfirmed) return;
    const link = event.target instanceof Element ? event.target.closest('a[href]') : null;
    if (!link) return;
    const href = link.getAttribute('href') || '';
    if (href === '' || href.startsWith('#') || href.startsWith('javascript:')) return;
    if (isPostDirty()) {
      if (!window.confirm('Bài viết có thay đổi chưa lưu. Bạn có chắc muốn rời trang và bỏ các thay đổi này không?')) {
        event.preventDefault();
        event.stopPropagation();
        return;
      }
      isLeavingConfirmed = true;
    }
  }, true);

  // Warn when closing tab, reloading, or navigating via browser controls
  window.addEventListener('beforeunload', event => {
    if (!isSubmittingPost && !isLeavingConfirmed && isPostDirty()) {
      event.preventDefault();
      event.returnValue = '';
    }
  });
}

// Run after form-specific handlers (including the editor). Native validation
// happens before submit; cancelled confirmations must leave the form usable.
const pendingForms = new Map();
document.addEventListener('submit', event => {
  const form = event.target;
  if (!(form instanceof HTMLFormElement) || event.defaultPrevented) return;
  if (pendingForms.has(form)) {
    event.preventDefault();
    return;
  }
  if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
    event.preventDefault();
    return;
  }

  const submitter = event.submitter;
  // Disabled controls are omitted from native POST data. Preserve the clicked
  // button's value (action=publish/delete, status=approved/hidden, etc.).
  let proxy;
  if (submitter && submitter.name && !submitter.disabled) {
    proxy = document.createElement('input');
    proxy.type = 'hidden';
    proxy.name = submitter.name;
    proxy.value = submitter.value;
    form.append(proxy);
  }
  const buttons = Array.from(form.elements).filter(control =>
    (control instanceof HTMLButtonElement || control instanceof HTMLInputElement) &&
    control.type === 'submit' && !control.disabled
  );
  const originals = buttons.map(button => [button, button instanceof HTMLInputElement ? button.value : button.innerHTML]);
  pendingForms.set(form, { originals, proxy });
  form.setAttribute('aria-busy', 'true');
  buttons.forEach(button => {
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    if (button === submitter || !submitter) {
      button.setAttribute('data-loading', '');
      if (button instanceof HTMLInputElement) button.value = 'Đang xử lý…';
      else button.textContent = 'Đang xử lý…';
    }
  });
});

window.addEventListener('pageshow', () => {
  pendingForms.forEach(({ originals, proxy }, form) => {
    originals.forEach(([button, label]) => {
      button.disabled = false;
      button.removeAttribute('data-loading');
      button.removeAttribute('aria-busy');
      if (button instanceof HTMLInputElement) button.value = label;
      else button.innerHTML = label;
    });
    proxy?.remove();
    form.removeAttribute('aria-busy');
  });
  pendingForms.clear();
});

// Responsive Workspace Menu (Open on desktop > 768px, collapsed into compact dropdown on mobile <= 768px)
(function() {
  const wsMenu = document.getElementById('workspace-menu');
  if (!wsMenu) return;
  let lastIsDesktop = null;
  function syncWorkspaceMenu() {
    const isDesktop = window.innerWidth > 768;
    if (isDesktop !== lastIsDesktop) {
      wsMenu.open = isDesktop;
      lastIsDesktop = isDesktop;
    }
  }
  syncWorkspaceMenu();
  window.addEventListener('resize', syncWorkspaceMenu);
})();
