@extends('layouts.app')

@section('content')
@push('css')
<style>
    /* RAPIDAPKAN & NO CLASS TAMBAHAN */
    /* Header */
    .page-header {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }
    .page-header h3 {
        font-weight: 700;
        color: #212529;
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: 0;
    }
    /* Stat cards */
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s;
        overflow: hidden;
        position: relative;
    }
    .stat-card::before {
        content: '';
        display: block;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-start), var(--accent-end));
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    .stat-card.primary   { --accent-start: #0d6efd; --accent-end: #0a58ca; }
    .stat-card.success   { --accent-start: #198754; --accent-end: #146c43; }
    .stat-card.warning   { --accent-start: #ffc107; --accent-end: #d39e00; }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,.12) !important;
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
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
        font-size: .85rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
        line-height: 1.2;
    }
    /* Table card */
    .table-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .table-card > .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f3f5;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .5rem;
    }
    .table-card > .card-header h5 {
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: .5rem;
        margin: 0;
    }
    .table-card > .card-body { padding: 0; }

    /* DataTable */
    table#approvalTable {
        margin: 0 !important;
        width: 100% !important;
    }
    table#approvalTable thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }
    table#approvalTable th {
        font-weight: 600;
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6c757d;
        padding: 1rem 1.25rem !important;
        border: none !important;
        white-space: nowrap;
        text-align: left;
    }
    table#approvalTable th.text-end,
    table#approvalTable td.text-end {
        text-align: right !important;
    }
    table#approvalTable th.text-center,
    table#approvalTable td.text-center {
        text-align: center !important;
    }
    table#approvalTable td {
        padding: 1rem 1.25rem !important;
        vertical-align: middle;
        border: none !important;
    }
    table#approvalTable tbody tr {
        border-bottom: 1px solid #f1f3f5;
        transition: all .2s;
        cursor: pointer;
        border-left: 4px solid transparent;
    }
    table#approvalTable tbody tr[data-rowclass="border-success"] { border-left-color: #198754; }
    table#approvalTable tbody tr[data-rowclass="border-warning"] { border-left-color: #ffc107; }
    table#approvalTable tbody tr:hover {
        background: #f8f9ff;
        transform: translateX(4px);
    }

    /* badge style */
    .badge-doc {
        font-size: .75rem;
        font-weight: 600;
        padding: .35rem .65rem;
        border-radius: 6px;
        letter-spacing: .3px;
        display: inline-block;
    }
    .badge-doc.hta { background: #d1e7dd; color: #0f5132; }
    .badge-doc.fui { background: #fff3cd; color: #664d03; }

    /* kode pengajuan link */
    .kode-link {
        color: #0d6efd;
        font-weight: 600;
        text-decoration: none;
        padding: 2px 6px;
        border-radius: 4px;
        transition: .2s;
    }
    .kode-link:hover {
        background: #e7f1ff; color: #084298;
    }

    /* Aksi Button */
    .btn-review {
        padding: .35rem .85rem;
        font-size: .8rem;
        font-weight: 500;
        border-radius: 8px;
        transition: .2s;
        box-shadow: 0 2px 4px rgba(13,110,253,.15);
    }
    .btn-review:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13,110,253,.25);
    }
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: linear-gradient(180deg,#f8f9fa 0,#fff 100%);
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
        margin-bottom: .5rem;
    }
    .empty-text {
        color: #6c757d;
        font-size: .95rem;
    }
    /* DataTables Controls Responsive */
    .dt-length select, .dt-search input {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: .375rem .75rem;
        font-size: .9rem;
    }
    .dt-search input { width: 200px; }
    @media (max-width:768px) {
        .stat-card { margin-bottom: 1rem; }
        table#approvalTable thead { display: none; }
        table#approvalTable tbody tr {
            display: block;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin: 0 1.25rem 0.75rem;
            border-left-width: 4px !important;
            padding: 1rem;
        }
        table#approvalTable tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem 0 !important;
            border-bottom: 1px dashed #f1f3f5;
        }
        table#approvalTable tbody td:last-child {
            border-bottom: none;
            justify-content: flex-end;
        }
        table#approvalTable tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #6c757d;
            font-size: .85rem;
            text-transform: uppercase;
        }
    }
</style>
@endpush

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3>
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

<div class="row g-3 mb-4">
    {{-- <div class="col-md-4">
        <div class="card stat-card primary shadow-sm h-100">
            <div class="card-body p-4">
                <div style="display:flex;align-items:center">
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
    </div> --}}
    <div class="col-md-4">
        <div class="card stat-card success shadow-sm h-100">
            <div class="card-body p-4">
                <div style="display:flex;align-items:center">
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
                <div style="display:flex;align-items:center">
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

<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <h5>
                    <i class="fa fa-inbox text-primary"></i>
                    Daftar Approval Menunggu
                </h5>
                <span style="background:rgba(13,110,253,.06);color:#0d6efd;border:1px solid #b6d4fe;border-radius:6px;padding:6px 16px;display:inline-block;">
                    <i class="fa fa-layer-group me-1"></i>
                    <span id="totalItems">Loading...</span>
                </span>
            </div>
            <div class="card-body">
                <table id="approvalTable" class="table mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Jenis Dokumen</th>
                            <th>Kode Pengajuan</th>
                            <th>Barang / Item</th>
                            <th width="8%">Urutan</th>
                            <th width="15%">Tanggal</th>
                            <th width="12%" class="text-end">Aksi</th>
                            <th style="display:none">row_class</th>
                            <th style="display:none">doc_url</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- data by AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // DataTables
    const table = $('#approvalTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        searching: false,
        lengthMenu: [[10,25,50,100,-1],[10,25,50,100,"Semua"]],
        pageLength: 10,
        ajax: {
            url: "{{ route('approval-saya.index') }}",
            type: 'GET',
            error:function(xhr,error,code){
                Swal.fire({icon:'error',title:'Gagal Memuat Data',text:'Terjadi kesalahan saat mengambil data approval.',confirmButtonColor:'#0d6efd'})
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'jenis_dokumen', name: 'jenis_dokumen', className: 'text-center' },
            { data: 'kode_pengajuan', name: 'kode_pengajuan' },
            { data: 'nama_barang', name: 'nama_barang' },
            { data: 'urutan', name: 'Urutan', className: 'text-center' },
            { data: 'tanggal', name: 'created_at', className: 'text-center' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end' },
            { data: 'row_class', name: 'row_class', visible: false },
            { data: 'doc_url', name: 'doc_url', visible: false }
        ],
        order: [[4, 'asc']],
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
            paginate: {
                first: '<i class="fa fa-angle-double-left"></i>',
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>',
                last: '<i class="fa fa-angle-double-right"></i>'
            }
        },
        drawCallback: function() {
            const total = this.api().page.info().recordsTotal;
            $('#totalItems').text(total+' Item');
            attachRowClickHandlers();
        },
        createdRow: function(row, data) {
            // border accent by data
            if(data.row_class) $(row).attr('data-rowclass',data.row_class);
            if(data.doc_url && data.doc_url !== '#') {
                $(row).css('cursor','pointer').attr('data-url',data.doc_url);
            }
        }
    });

    $('#approvalTable_length').insertBefore('#approvalTable');
    $('#approvalTable_filter').remove();

    // row click
    function attachRowClickHandlers(){
        $('#approvalTable tbody tr').off('click').on('click',function(e){
            if ($(e.target).closest('a,button,input,select,.badge').length) return;
            const url = $(this).attr('data-url');
            if(url&&url!=="#") window.location.href = url;
        });
    }
    $('#approvalTable').on('mouseenter','tbody tr',function(){
        $(this).css('background-color','#f8f9ff');
    }).on('mouseleave','tbody tr',function(){
        $(this).css('background-color','');
    });
    // Analytics (optional)
    $(document).on('click','.btn-review,.kode-link',function(e){
        console.log('Approval clicked:',{
            type: $(this).closest('tr').find('.badge-doc').text().trim(),
            kode: $(this).closest('tr').find('.kode-link').text().trim(),
            url: this.href
        });
    });
    document.addEventListener('visibilitychange',function(){
        if(!document.hidden) table.ajax.reload(null,false);
    });
});
</script>
@endpush
