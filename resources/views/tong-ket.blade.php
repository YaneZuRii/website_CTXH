@extends('layouts.app')

@section('title', 'Tổng kết - CTXH')
@section('page-title', 'Tổng kết')

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #000; font-size: 2rem;">Thống kê giờ CTXH.</h2>
            <p class="text-muted">Theo dõi tiến độ và tổng hợp <br> giờ CTXH của sinh viên</p>
        </div>
        <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm" style="background-color: #4318ff; border: none; border-radius: 8px;">
            Xuất báo cáo
        </button>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #4318ff;">
                        <i class="fa-solid fa-chart-simple text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">Tổng số SV</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-tong-sv">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #fcc43e;">
                        <i class="fa-solid fa-person text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">Giờ cao nhất</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-max-gio">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #b356eb;">
                        <i class="fa-solid fa-gem text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">Tỷ lệ Đạt</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-ty-le">0%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 1px; border: 1px solid #ddd !important;">
        <div class="table-responsive">
            <table class="table align-middle mb-0 text-center">
                <thead style="background-color: #fff;">
                    <tr style="border-bottom: 2px solid #000;">
                        <th class="py-3 fw-normal text-muted">Họ và Tên</th>
                        <th class="py-3 fw-normal text-muted">MSSV</th>
                        <th class="py-3 fw-normal text-muted">Khoa / Lớp</th>
                        <th class="py-3 fw-normal text-muted">Giờ tích lũy</th>
                        <th class="py-3 fw-normal text-muted">Giờ yêu cầu</th>
                        <th class="py-3 fw-normal text-muted">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="tongket-table-body">
                    <tr>
                        <td colspan="6" class="py-5 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div> Đang tải dữ liệu...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('head-scripts')
<script>
    const API_URL = '{{ url("/api") }}';

    async function loadTongKet() {
        try {
            // const response = await fetch(`${API_URL}/tongket`);
            // const data = await response.json();

            const data = [
                { hoTen: 'Lê Duy Khang', maSV: 'DH52200837', khoa: 'CNTT', gioTichLuy: 20, gioYeuCau: 15 },
                { hoTen: 'Lê Duy Khánh', maSV: 'DH..............', khoa: 'CNTT', gioTichLuy: 15, gioYeuCau: 15 },
                { hoTen: 'Nguyễn Trọng Dương', maSV: 'DH..............', khoa: 'CNTT', gioTichLuy: 5, gioYeuCau: 15 },
                { hoTen: 'Douw', maSV: 'DH..............', khoa: 'CNTT', gioTichLuy: 45, gioYeuCau: 15 }
            ];

            const tongSV = data.length;
            let maxGio = 0;
            let soSVDạt = 0;

            data.forEach(sv => {
                if (sv.gioTichLuy > maxGio) maxGio = sv.gioTichLuy;
                if (sv.gioTichLuy >= sv.gioYeuCau) soSVDạt++;
            });
            const tyLeDat = tongSV > 0 ? Math.round((soSVDạt / tongSV) * 100) : 0;
            document.getElementById('stat-tong-sv').innerText = tongSV;
            document.getElementById('stat-max-gio').innerText = maxGio;
            document.getElementById('stat-ty-le').innerText = tyLeDat + '%';

            const tbody = document.getElementById('tongket-table-body');
            
            if(data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="py-5 text-muted">Chưa có dữ liệu sinh viên nào.</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(sv => {
                const trangThai = sv.gioTichLuy >= sv.gioYeuCau ? 'Đạt' : 'Chưa đạt';
                
                return `
                    <tr>
                        <td class="py-3 text-start ps-4">${sv.hoTen}</td>
                        <td class="py-3">${sv.maSV}</td>
                        <td class="py-3">${sv.khoa}</td>
                        <td class="py-3 fw-bold ${sv.gioTichLuy >= sv.gioYeuCau ? 'text-success' : 'text-danger'}">${sv.gioTichLuy}</td>
                        <td class="py-3">${sv.gioYeuCau}</td>
                        <td class="py-3 fw-semibold" style="color: ${sv.gioTichLuy >= sv.gioYeuCau ? '#10b981' : '#ef4444'};">${trangThai}</td>
                    </tr>
                `;
            }).join('');

        } catch (error) {
            console.error('Lỗi khi load dữ liệu tổng kết:', error);
            document.getElementById('tongket-table-body').innerHTML = '<tr><td colspan="6" class="py-5 text-danger fw-bold">Lỗi kết nối đến Server!</td></tr>';
        }
    }
    document.addEventListener('DOMContentLoaded', loadTongKet);
</script>
@endsection