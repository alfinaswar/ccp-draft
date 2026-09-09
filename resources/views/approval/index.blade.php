@extends('layouts.app')

@section('content')
@push('css')
{{-- DataTables CSS --}}

<style>
    /* ===== GLOBAL STYLES ===== */
    .page-header {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .page-title {
        font-weight: 700;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ===== STAT CARDS ===== */
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-start), var(--accent-end));
    }

    .stat-card.primary { --accent-start: #0d6efd; --accent-end: #0a58ca; }
    .stat-card.success { --accent-start: #198754; --accent-end: #146c43; }
    .stat-card.warning { --accent-start: #ffc107; --accent-end: #d39e00; }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12) !important;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
    }

    .stat-card.success .stat-icon {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .stat-card.warning .stat-icon {
        background: rgba(255, 193, 7, 0.15);
        color: #856404;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
        line-height: 1.2;
    }

    /* ===== TABLE CARD ===== */
    .table-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .table-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f3f5;
        padding: 1.25rem 1.5rem;
    }

    .table-card .card-title {
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-card .card-body {
        padding: 0;
    }

    /* ===== DATATABLES CUSTOM STYLING ===== */
    .approval-table.dataTable {
        margin: 0 !important;
        width: 100% !important;
    }

    .approval-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    .approval-table thead th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 1rem 1.25rem !important;
        border: none !important;
        white-space: nowrap;
    }

    .approval-table tbody tr {
        border-bottom: 1px solid #f1f3f5;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .approval-table tbody tr:hover {
        background: #f8f9ff;
        transform: translateX(4px);
    }

    .approval-table tbody td {
        padding: 1rem 1.25rem !important;
        vertical-align: middle;
        border: none !important;
    }

    /* Row accent border */
    .approval-table tbody tr {
        border-left: 4px solid transparent;
    }

    .approval-table tbody tr.border-success { border-left-color: #198754; }
    .approval-table tbody tr.border-warning { border-left-color: #ffc107; }

    /* Badge styling */
    .badge-doc {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        letter-spacing: 0.3px;
    }

    .badge-doc.hta {
        background: #d1e7dd;
        color: #0f5132;
    }

    .badge-doc.fui {
        background: #fff3cd;
        color: #664d03;
    }

    /* Kode pengajuan link */
    .kode-link {
        color: #0d6efd;
        font-weight: 600;
        text-decoration: none;
        padding: 2px 6px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .kode-link:hover {
        background: #e7f1ff;
        color: #084298;
        text-decoration: none;
    }

    /* Action button */
    .btn-review {
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.15);
    }

    .btn-review:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.25);
    }

    /* DataTables Controls */
    .dt-container {
        padding: 0 1.25rem 1.25rem;
    }

    .dt-length, .dt-search {
        margin: 1rem 0;
    }

    .dt-length select, .dt-search input {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    .dt-search input {
        width: 200px;
    }

    .dt-info {
        color: #6c757d;
        font-size: 0.85rem;
        margin: 1rem 0;
    }

    .dt-paging {
        margin: 1rem 0;
    }

    .pagination {
        gap: 0.25rem;
    }

    .pagination .page-link {
        border: none;
        border-radius: 8px !important;
        color: #6c757d;
        font-weight: 500;
        padding: 0.5rem 0.85rem;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background: #e9ecef;
        color: #212529;
    }

    .pagination .page-item.active .page-link {
        background: #0d6efd;
        color: #fff;
    }

    /* Loading & Empty States */
    .dataTables_wrapper .dataTables_processing {
        background: rgba(255,255,255,0.9);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 8px;
        padding: 1rem 2rem;
    }

    .dataTables_empty {
        text-align: center;
        padding: 3rem 2rem;
        color: #6c757d;
    }

    /* ===== EMPTY STATE (Non-DataTables) ===== */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
        border-radius: 12px;
        margin: 0 1.25rem 1.25rem;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #6c757d;
        font-size: 2rem;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #6c757d;
        font-size: 0.95rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .stat-card { margin-bottom: 1rem; }

        .approval-table thead { display: none; }

        .approval-table tbody tr {
            display: block;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin: 0 1.25rem 0.75rem;
            border-left-width: 4px !important;
        }

        .approval-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0 !important;
            border-bottom: 1px dashed #f1f3f5;
        }

        .approval-table tbody td:last-child {
            border-bottom: none;
            justify-content: flex-end;
        }

        .approval-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .dt-length, .dt-search, .dt-info, .dt-paging {
            text-align: center;
        }

        .dt-search input { width: 100%; margin-bottom: 0.5rem; }
    }
</style>
@endpush

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title mb-0">
                <i class="fa fa-tasks text-primary"></i>
                Approval Saya
            </h3>
        </div>
        <div class="col-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-muted">
                            <i class="fa fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-primary">Approval Saya</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card primary shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="fa fa-list-check"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Pending</div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card success shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="fa fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="stat-label">HTA / GPA</div>
                        <div class="stat-value">{{ $stats['hta'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card warning shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="fa fa-file-signature"></i>
                    </div>
                    <div>
                        <div class="stat-label">Usulan Investasi</div>
                        <div class="stat-value">{{ $stats['fui'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Table Card --}}
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-inbox text-primary"></i>
                        Daftar Approval Menunggu
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                        <i class="fa fa-layer-group me-1"></i>
                        <span id="totalItems">Loading...</span>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table approval-table mb-0" id="approvalTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Jenis Dokumen</th>
                                <th>Kode Pengajuan</th>
                                <th>Barang / Item</th>
                                <th width="8%">Urutan</th>
                                <th width="15%">Tanggal</th>
                                <th width="12%" class="text-end">Aksi</th>
                                {{-- Hidden columns for DataTables --}}
                                <th class="d-none">row_class</th>
                                <th class="d-none">doc_url</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be loaded via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Initialize DataTables with Server-Side Processing
    const table = $('#approvalTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        searching: false, // Disable built-in search
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        pageLength: 10,
        ajax: {
            url: "{{ route('approval-saya.index') }}",
            type: 'GET',
            error: function(xhr, error, code) {
                console.error('DataTables AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat mengambil data approval.',
                    confirmButtonColor: '#0d6efd'
                });
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'jenis_dokumen',
                name: 'jenis_dokumen',
                className: 'text-center'
            },
            {
                data: 'kode_pengajuan',
                name: 'kode_pengajuan'
            },
            {
                data: 'nama_barang',
                name: 'nama_barang'
            },
            {
                data: 'urutan',
                name: 'Urutan',
                className: 'text-center'
            },
            {
                data: 'tanggal',
                name: 'created_at',
                className: 'text-center'
            },
            {
                data: 'aksi',
                name: 'aksi',
                orderable: false,
                searchable: false,
                className: 'text-end'
            },
            // Hidden columns
            { data: 'row_class', name: 'row_class', visible: false },
            { data: 'doc_url', name: 'doc_url', visible: false }
        ],
        order: [[4, 'asc']], // Default sort by Urutan ascending
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: `
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa fa-check-circle"></i></div>
                    <h5 class="empty-title">🎉 Semua Approval Selesai!</h5>
                    <p class="empty-text mb-0">
                        Anda telah menyelesaikan semua approval yang diberikan.<br>
                        Silakan periksa kembali nanti untuk approval baru.
                    </p>
                </div>
            `,
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ approval",
            infoEmpty: "Tidak ada approval pending",
            infoFiltered: "(difilter dari _MAX_ total)",
            search: "",
            paginate: {
                first: '<i class="fa fa-angle-double-left"></i>',
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>',
                last: '<i class="fa fa-angle-double-right"></i>'
            },
            infoPostFix: "",

        },
        drawCallback: function(settings) {
            // Update total items counter
            const api = this.api();
            const total = api.page.info().recordsTotal;
            $('#totalItems').text(total + ' Item');

            // Re-attach click handlers for rows after draw
            attachRowClickHandlers();
        },
        createdRow: function(row, data, dataIndex) {
            // Add border accent class from hidden column
            const rowClass = $(row).find('td:eq(7)').text(); // row_class column
            if (rowClass) {
                $(row).addClass(rowClass);
            }

            // Make row clickable (except for links/buttons)
            const docUrl = $(row).find('td:eq(8)').text(); // doc_url column
            if (docUrl && docUrl !== '#') {
                $(row).css('cursor', 'pointer').attr('data-url', docUrl);
            }
        }
    });

    // Tampilkan 'show entries' dropdown (entri per halaman) secara manual jika tidak muncul
    // DataTables biasa menempatkan #approvalTable_length di DOM paling atas
    // Pastikan tetap ditampilkan; pindahkan ke tempat yang visible jika perlu
    $('#approvalTable_length').insertBefore('#approvalTable');

    // Remove search box UI
    $('#approvalTable_filter').remove();

    // Function to attach click handlers to rows
    function attachRowClickHandlers() {
        $('#approvalTable tbody tr').off('click').on('click', function(e) {
            // Ignore clicks on links, buttons, or inputs
            if ($(e.target).closest('a, button, input, select, .badge').length) {
                return;
            }

            const url = $(this).attr('data-url');
            if (url && url !== '#') {
                window.location.href = url;
            }
        });
    }

    // Smooth hover effect for rows
    $('#approvalTable').on('mouseenter', 'tbody tr', function() {
        $(this).css('background-color', '#f8f9ff');
    }).on('mouseleave', 'tbody tr', function() {
        $(this).css('background-color', '');
    });

    // Track clicks for analytics (optional)
    $(document).on('click', '.btn-review, .kode-link', function(e) {
        console.log('Approval clicked:', {
            type: $(this).closest('tr').find('.badge-doc').text().trim(),
            kode: $(this).closest('tr').find('.kode-link').text().trim(),
            url: this.href
        });
    });

    // Refresh table when page becomes visible (for real-time updates)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            table.ajax.reload(null, false); // Keep current pagination
        }
    });
});
</script>
@endpush
