async function htmlToImage(el) {
  // Simple client-side render using html2canvas CDN if available; fallback to toDataURL of QR canvas only
  if (!window.html2canvas) {
    const s = document.createElement("script");
    s.src =
      "https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js";
    document.head.appendChild(s);
    await new Promise((res) => (s.onload = res));
  }
  const canvas = await html2canvas(el, {
    backgroundColor: "#111827",
    scale: 2,
    useCORS: true,
    allowTaint: false,
  });
  return await canvasToBlob(canvas);
}

window.htmlToImage = htmlToImage;

// Utilities for copy and download
window.copyText = async function (text) {
  try {
    await navigator.clipboard.writeText(text);
    return true;
  } catch (e) {
    return false;
  }
};

// Export with custom background/scale (for infographic)
async function htmlToImageWithBg(el, bg = "#ffffff", scale = 2) {
  if (!window.html2canvas) {
    const s = document.createElement("script");
    s.src =
      "https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js";
    document.head.appendChild(s);
    await new Promise((res) => (s.onload = res));
  }
  const canvas = await html2canvas(el, {
    backgroundColor: bg,
    scale,
    useCORS: true,
    allowTaint: false,
  });
  return await canvasToBlob(canvas);
}

window.htmlToImageWithBg = htmlToImageWithBg;

async function canvasToBlob(canvas) {
  // Use native toBlob if available, else fallback via dataURL
  const blob = await new Promise((resolve) => {
    if (canvas.toBlob) {
      canvas.toBlob(resolve);
    } else {
      resolve(null);
    }
  });
  if (blob) return blob;
  const dataUrl = canvas.toDataURL("image/png");
  const res = await fetch(dataUrl);
  return await res.blob();
}

// Reliable blob download helper
window.downloadBlob = function (blob, filename) {
  try {
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename || "download.png";
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
      URL.revokeObjectURL(url);
      a.remove();
    }, 0);
  } catch (e) {
    console.error(e);
    alert("Không thể tải file. Vui lòng thử lại.");
  }
};
