## FPT Polytechnic - Ứng dụng tư vấn chọn ngành (PHP MVC)

### Yêu cầu
- PHP 8.1+
- MySQL 8.x

### Cài đặt
1. Tạo database `ai` và import file dump (SQL) bạn đã cung cấp.
2. Sao chép `.env.example` thành `.env` và cập nhật cấu hình DB, AI.
3. Chạy server cục bộ:
   ```bash
   php -S localhost:8000 -t AICK
   ```
   Sau đó mở `http://localhost:8000/`.

### Tính năng
- Form thu thập dữ liệu người dùng và gợi ý ngành bằng AI (OpenAI/Gemini) hoặc heuristic offline nếu chưa có API key.
- Lưu lịch sử tư vấn vào bảng `assessments`.
- Trang kết quả có QR Code, tải infographic PNG.
- Thống kê ngành được đề xuất (biểu đồ bar Chart.js).

### Cấu trúc
- `index.php` bootstrap + định tuyến
- `app/Core/*` MVC core
- `app/Controllers/*` controllers
- `app/Models/*` models (truy vấn bảng `majors`, `skills`, `curriculum`, `assessments`)
- `app/Views/*` views + layout
- `public/*` assets tĩnh

### Ghi chú
- Khi bật OpenAI/Gemini, đặt `AI_PROVIDER` tương ứng và API key trong `.env`.
- Nếu chỉ demo offline, để `AI_PROVIDER=mock` sẽ dùng heuristic nội bộ.


