@extends('layouts.app')

@section('title', 'Quản lý Sự kiện - CTXH')
@section('page-title', 'Sự Kiện')

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #000; font-size: 2rem;">Sự Kiện</h2>
        <button type="button" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addEventModal" style="background-color: #314dff; border: none; border-radius: 8px;">
            Thêm Sự Kiện
        </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 4px; border: 1px solid #e2e8f0 !important;">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background-color: #fff;">
                    <tr style="border-bottom: 1px solid #cbd5e1;">
                        <th class="py-4 px-4 fw-semibold" style="color: #475569;">Tên sự kiện</th>
                        <th class="py-4 fw-semibold" style="color: #475569;">Mã SK</th>
                        <th class="py-4 fw-semibold" style="color: #475569;">Đơn vị tổ chức</th>
                        <th class="py-4 fw-semibold text-center" style="color: #475569;">Số lượng</th>
                        <th class="py-4 fw-semibold text-center" style="color: #475569;">Số giờ CTXH</th>
                        <th class="py-4 fw-semibold" style="color: #475569;">Địa điểm</th>
                    </tr>
                </thead>
                <tbody id="eventListContainer">
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div> Đang tải dữ liệu...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h4 class="modal-title fw-bold" style="color: #2b3674;">Thêm Sự Kiện Mới</h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addEventForm" onsubmit="event.preventDefault(); saveEvent();">
                    <div class="row g-3">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold text-muted small">Tên sự kiện</label>
                            <input type="text" id="event-name" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Mã SK</label>
                            <input type="text" id="event-code" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Đơn vị tổ chức</label>
                            <input type="text" id="event-org" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Địa điểm</label>
                            <input type="text" id="event-loc" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-muted small">Số lượng (SV)</label>
                            <input type="number" id="event-qty" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-muted small">Số giờ CTXH</label>
                            <input type="number" id="event-hours" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <button type="submit" id="btnSubmitEvent" class="btn w-100 fw-bold text-white py-3 shadow-sm" style="background-color: #314dff; border-radius: 8px; font-size: 1.05rem;">
                        Lưu sự kiện
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script>
    const API_URL = '{{ url("/api") }}';

    async function loadEvents() {
        try {
            // const response = await fetch(`${API_URL}/hoatdong`);
            // const events = await response.json();

            const events = [
                { tenSK: 'Hiến máu tình nguyện đợt 1', maSK: 'HM01', donVi: 'Đoàn Thanh Niên', soLuong: 200, gioCTXH: 15, diaDiem: 'Hội trường A' },
                { tenSK: 'Mùa hè Xanh - Dọn dẹp khuôn viên trường', maSK: 'MHX02', donVi: 'Đội CTXH', soLuong: 50, gioCTXH: 10, diaDiem: 'Cơ sở chính' },
                { tenSK: 'Hỗ trợ Tân sinh viên nhập học', maSK: 'TSV03', donVi: 'Phòng Công tác Sinh viên', soLuong: 100, gioCTXH: 20, diaDiem: 'Sân trường' },
                { tenSK: 'Thăm và tặng quà Mái ấm tình thương', maSK: 'MATT04', donVi: 'Khoa CNTT', soLuong: 30, gioCTXH: 25, diaDiem: 'Quận 9' }
            ];

            const tbody = document.getElementById('eventListContainer');
            
            if(events.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Chưa có sự kiện nào.</td></tr>';
                return;
            }

            tbody.innerHTML = events.map(event => `
                <tr>
                    <td class="py-4 px-4 text-dark" style="font-size: 0.95rem;">${event.tenSK}</td>
                    <td class="py-4 text-muted" style="font-size: 0.95rem;">${event.maSK}</td>
                    <td class="py-4 text-dark" style="font-size: 0.95rem;">${event.donVi}</td>
                    <td class="py-4 text-center text-dark" style="font-size: 0.95rem;">${event.soLuong}</td>
                    <td class="py-4 text-center text-dark" style="font-size: 0.95rem;">${event.gioCTXH}</td>
                    <td class="py-4 text-dark" style="font-size: 0.95rem;">${event.diaDiem}</td>
                </tr>
            `).join('');

        } catch (error) {
            console.error('Lỗi load sự kiện:', error);
            document.getElementById('eventListContainer').innerHTML = '<tr><td colspan="6" class="text-center py-5 text-danger">Lỗi kết nối dữ liệu!</td></tr>';
        }
    }

    async function saveEvent() {
        const btn = document.getElementById('btnSubmitEvent');
        
        // lấy dữ liệu từ form
        const payload = {
            tenSK: document.getElementById('event-name').value.trim(),
            maSK: document.getElementById('event-code').value.trim(),
            donVi: document.getElementById('event-org').value.trim(),
            diaDiem: document.getElementById('event-loc').value.trim(),
            soLuong: parseInt(document.getElementById('event-qty').value) || 0,
            gioCTXH: parseInt(document.getElementById('event-hours').value) || 0
        };

        if (!payload.tenSK || !payload.maSK) return;

        btn.disabled = true;
        btn.innerText = 'Đang xử lý...';

        try {
           
            // await fetch(`${API_URL}/hoatdong`, { method: 'POST', body: JSON.stringify(payload), ... });
            await new Promise(r => setTimeout(r, 600));

            alert('Thêm sự kiện thành công!');

            document.getElementById('addEventForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
            
            loadEvents();
        } catch (error) {
            alert('Có lỗi xảy ra!');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Lưu sự kiện';
        }
    }

    // Tự động nạp dữ liệu khi tải trang
    document.addEventListener('DOMContentLoaded', loadEvents);
</script>
@endsection