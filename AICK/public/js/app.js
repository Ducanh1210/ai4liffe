async function htmlToImage(el){
  // Simple client-side render using html2canvas CDN if available; fallback to toDataURL of QR canvas only
  if (!window.html2canvas){
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
    document.head.appendChild(s);
    await new Promise(res => s.onload = res);
  }
  const canvas = await html2canvas(el, {backgroundColor: '#111827', scale: 2});
  return await new Promise(resolve => canvas.toBlob(resolve));
}

window.htmlToImage = htmlToImage;


