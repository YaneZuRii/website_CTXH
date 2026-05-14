@extends('layouts.app')

@section('title', 'Sự kiện - CTXH')
@section('page-title', 'Sự kiện')

@section('content')
<div class="container-fluid px-4 pb-5 d-flex justify-content-center align-items-start" style="padding-top: 5vh;">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; width: 100%; max-width: 900px;">
        <div class="card-body p-5">
            
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 72px; height: 72px; background-color: rgba(5, 205, 153, 0.1); color: #05cd99; font-size: 2rem;">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h4 class="fw-bold mb-2" style="color: #2b3674;">Danh sách sự kiện</h4>
                <p class="text-muted small">Các sự kiện do Admin tạo sẽ hiển thị bên dưới.</p>
            </div>

            <div class="mb-4">
                <div class="fw-bold text-muted small mb-2">Thông báo sự kiện từ Admin</div>
                <div id="event-announcement" class="rounded-3 border bg-light p-3 small text-muted">
                    Đang tải thông báo sự kiện...
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modal-event-name">Chi tiết sự kiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small">
                <p><b>Mã sự kiện:</b> <span id="modal-event-code"></span></p>
                <p><b>Đơn vị tổ chức:</b> <span id="modal-event-org"></span></p>
                <p><b>Địa điểm:</b> <span id="modal-event-location"></span></p>
                <p><b>Thời gian tổ chức:</b> <span id="modal-event-time"></span></p>
                <p><b>Số ngày CTXH:</b> <span id="modal-event-hours"></span></p>
                <p><b>Nội dung:</b> <span id="modal-event-content"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-register-event">Đăng ký tham gia</button>
            </div>
        </div>
    </div>
</div>

<style>
    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(5, 205, 153, 0.2) !important;
    }
</style>
@endsection

@section('head-scripts')
<script>
    const currentMaSv = @json(strtoupper(Auth::check() ? Auth::user()->maSV : ''));
    let selectedEvent = null;

    document.addEventListener('DOMContentLoaded', async () => {
        const eventAnnouncement = document.getElementById('event-announcement');
        try {
            const cacheBuster = new Date().getTime();
            const response = await fetch(`http://127.0.0.1:5000/api/danh_sach_su_kien?maSV=${encodeURIComponent(currentMaSv)}&t=${cacheBuster}`);
            if (response.ok) {
                const events = await response.json();
                if (!events || events.length === 0) {
                    eventAnnouncement.innerHTML = 'Hiện chưa có sự kiện nào do Admin tạo.';
                } else {
                    window._eventsCache = events;
                    eventAnnouncement.innerHTML = events.map((ev, idx) => {
                        const tg = (ev.thoiGianToChuc || '').replace('T', ' ');
                        const statusBadge = ev.moDangKy
                            ? '<span class="badge text-bg-success">Mở đăng ký</span>'
                            : '<span class="badge text-bg-secondary">Đóng đăng ký</span>';
                        const registerStatus = ev.daDiemDanh
                            ? '<span class="badge text-bg-primary">Đã điểm danh</span>'
                            : (ev.daDangKy ? '<span class="badge text-bg-warning">Đã đăng ký</span>' : '');
                        const slotText = ev.choConLai < 0
                            ? 'Không giới hạn slot'
                            : `Còn ${Math.max(0, ev.choConLai)} / ${ev.soLuong} slot`;
                        return `
                            <div class="mb-2 pb-2 border-bottom position-relative pe-5">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="fw-bold text-dark">${ev.tenSK}</div>
                                    ${statusBadge}
                                    ${registerStatus}
                                </div>
                                <div><i class="fa-solid fa-location-dot me-1"></i>${ev.diaDiem || 'Chưa có địa điểm'}</div>
                                <div><i class="fa-regular fa-clock me-1"></i>${tg || 'Chưa có thời gian tổ chức'}</div>
                                <div><i class="fa-solid fa-users me-1"></i>${slotText}</div>
                                <button class="btn btn-sm btn-outline-primary position-absolute top-0 end-0" onclick="openEventDetail(${idx})">Xem chi tiết</button>
                            </div>
                        `;
                    }).join('');
                }
            }
        } catch (error) {
            console.error("Lỗi tải sự kiện:", error);
            eventAnnouncement.innerHTML = 'Không tải được thông báo sự kiện. Vui lòng thử lại sau.';
        }
    });

    function openEventDetail(index) {
        const events = window._eventsCache || [];
        const ev = events[index];
        if (!ev) return;

        selectedEvent = ev;
        document.getElementById('modal-event-name').innerText = ev.tenSK || 'Sự kiện';
        document.getElementById('modal-event-code').innerText = ev.maSK || '';
        document.getElementById('modal-event-org').innerText = ev.donVi || 'Chưa cập nhật';
        document.getElementById('modal-event-location').innerText = ev.diaDiem || 'Chưa cập nhật';
        document.getElementById('modal-event-time').innerText = (ev.thoiGianToChuc || '').replace('T', ' ') || 'Chưa cập nhật';
        document.getElementById('modal-event-hours').innerText = (ev.gioCTXH || 0) + ' ngày';
        document.getElementById('modal-event-content').innerText = ev.noiDung || 'Không có mô tả';
        const btnRegister = document.getElementById('btn-register-event');
        if (ev.daDiemDanh) {
            btnRegister.disabled = true;
            btnRegister.classList.remove('btn-success');
            btnRegister.classList.add('btn-secondary');
            btnRegister.innerText = 'Đã điểm danh';
        } else if (ev.daDangKy) {
            btnRegister.disabled = true;
            btnRegister.classList.remove('btn-success');
            btnRegister.classList.add('btn-secondary');
            btnRegister.innerText = 'Đã đăng ký';
        } else if (!ev.coTheDangKy) {
            btnRegister.disabled = true;
            btnRegister.classList.remove('btn-success');
            btnRegister.classList.add('btn-secondary');
            btnRegister.innerText = 'Không thể đăng ký';
        } else {
            btnRegister.disabled = false;
            btnRegister.classList.remove('btn-secondary');
            btnRegister.classList.add('btn-success');
            btnRegister.innerText = 'Đăng ký tham gia';
        }

        const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
        modal.show();
    }

    document.getElementById('btn-register-event').addEventListener('click', async () => {
        if (!selectedEvent) return;
        if (selectedEvent.daDangKy || selectedEvent.daDiemDanh || !selectedEvent.coTheDangKy) {
            alert('Sự kiện này hiện không thể đăng ký.');
            return;
        }
        if (!currentMaSv) {
            alert('Không xác định được mã sinh viên. Vui lòng đăng nhập lại.');
            return;
        }

        const ok = confirm('Cam kết tham gia đầy đủ sự kiện này. Nhấn OK để xác nhận đăng ký.');
        if (!ok) return;

        try {
            const response = await fetch('http://127.0.0.1:5000/api/dang_ky_su_kien', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    maSV: currentMaSv,
                    maSK: selectedEvent.maSK,
                    camKet: 'Cam kết tham gia đầy đủ'
                })
            });
            const result = await response.json();
            alert(result.thong_diep || 'Đã xử lý đăng ký.');
            if (response.ok && result.ok) {
                selectedEvent.daDangKy = true;
                selectedEvent.coTheDangKy = false;
                const btnRegister = document.getElementById('btn-register-event');
                btnRegister.disabled = true;
                btnRegister.classList.remove('btn-success');
                btnRegister.classList.add('btn-secondary');
                btnRegister.innerText = 'Đã đăng ký';
            }
        } catch (error) {
            alert('Không thể đăng ký lúc này. Vui lòng thử lại.');
        }
    });
</script>
@endsection