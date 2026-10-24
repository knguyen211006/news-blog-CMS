document.querySelectorAll('[data-go-back]').forEach(button => {
  button.addEventListener('click', () => history.length > 1 ? history.back() : location.assign('./'));
});
const source = document.getElementById('content');
const editorRoot = document.getElementById('rich-editor');
if (source && editorRoot && window.pell) {
  editorRoot.hidden = false;
  const actions = [
    {name:'bold',title:'In đậm'}, {name:'italic',title:'In nghiêng'},
    {name:'underline',title:'Gạch chân'}, {name:'heading2',title:'Tiêu đề mục'},
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
  const editor = window.pell.init({
    element:editorRoot,
    defaultParagraphSeparator:'p',
    styleWithCSS:false,
    actions,
    onChange:html=>{ source.value=html; }
  });
  editor.content.innerHTML=source.value;
  editor.content.setAttribute('role','textbox');
  editor.content.setAttribute('aria-label','Nội dung bài viết');
  editor.content.setAttribute('aria-multiline','true');
  editor.content.dataset.testid='editor-content';
  source.hidden=true;
  document.getElementById('post-form').addEventListener('submit',()=>{source.value=editor.content.innerHTML;});
  editorRoot.querySelectorAll('button').forEach(button=>{
    button.type='button';
    button.setAttribute('aria-label',button.title);
  });
}
const cover=document.getElementById('cover');
if(cover){
  let blob;
  cover.addEventListener('change',()=>{
    const file=cover.files[0];
    if(!file)return;
    if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>2097152){
      cover.setCustomValidity('Chọn ảnh JPEG, PNG hoặc WebP không quá 2 MB.');cover.reportValidity();return;
    }
    cover.setCustomValidity('');
    if(blob)URL.revokeObjectURL(blob);
    blob=URL.createObjectURL(file);
    document.getElementById('cover-preview').src=blob;
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
