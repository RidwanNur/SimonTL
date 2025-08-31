@extends('layouts.partials.app')

@section('content')
  <!-- Page Title -->
  <div class="container">
    <div class="page-inner">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
          <h3 class="fw-bold mb-3">Tindak Lanjut</h3>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
          <label>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, j F Y') }}</label>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                @php
  use Illuminate\Support\Facades\Storage;

  $status = $report->followup_status ?? 'Belum';

  // warna badge per status
  $badgeMap = [
    'Belum'   => ['bg'=>'#E5E7EB','fg'=>'#374151','dot'=>'#9CA3AF'],
    'Proses'  => ['bg'=>'#FDE68A','fg'=>'#7C3E00','dot'=>'#F59E0B'],
    'Selesai' => ['bg'=>'#A7F3D0','fg'=>'#065F46','dot'=>'#10B981'],
  ];
  $b = $badgeMap[$status] ?? $badgeMap['Belum'];

  // url file (pakai route milikmu kalau perlu)
  $fileUrl = ($report->link_file && Storage::disk('public')->exists($report->link_file))
              ? Storage::url($report->link_file) : null;

  // nama file (truncated nanti di UI)
  $fileName = $report->link_file ? basename($report->link_file) : null;
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

  {{-- Kiri: badge status --}}
  <div class="d-flex align-items-center gap-2">
    <span class="badge rounded-pill d-inline-flex align-items-center px-4 py-2"
          style="background:{{ $b['bg'] }};color:{{ $b['fg'] }};">
      <span class="me-2 d-inline-block rounded-circle"
            style="width:10px;height:10px;background:{{ $b['dot'] }};"></span>
      {{ $status }}
    </span>
    <small class="text-muted">Status tindak lanjut</small>
  </div>

  {{-- Kanan: link file (button group) --}}
  <div class="d-flex align-items-center gap-2 ms-md-auto min-w-0">
    @if($fileUrl)
      <div class="btn-group">
        <a href="{{ $fileUrl }}" target="_blank"
           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
          {{-- ikon file kecil (SVG, tanpa dependency) --}}
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"
                  stroke="currentColor" stroke-width="1.5" fill="none"/>
            <path d="M14 2v6h6" stroke="currentColor" stroke-width="1.5" fill="none"/>
          </svg>
          <span class="text-truncate" style="max-width:240px">
            File Lapoan Hasil Pengawasan
          </span>
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown" aria-expanded="false">
          <span class="visually-hidden">Toggle</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ $fileUrl }}" target="_blank">Lihat</a></li>
          <li><a class="dropdown-item" href="{{ $fileUrl }}" download>Unduh</a></li>
          {{-- <li><button class="dropdown-item" type="button"
                      data-copy="{{ $fileUrl }}" onclick="copyFileUrl(this)">Salin tautan</button></li> --}}
        </ul>
      </div>
    @else
      <span class="badge bg-light text-muted border">Tidak ada file</span>
    @endif
  </div>

</div>

  @php
  $report = $report ?? ($laporan->first() ?? null);
@endphp

@role('pegawai')
@if($report)
<form action="{{ route('pegawai.updateTL', $report->id) }}" method="POST" class="mt-2" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">No Laporan</label>
      <input type="text" name="report_number" class="form-control"
             value="{{ old('report_number', $report->report_number) }}" readonly>
    </div>

    <div class="col-md-8">
      <label class="form-label">Nama Laporan</label>
      <input type="text" name="report_name" class="form-control"
             value="{{ old('report_name', $report->report_name) }}" readonly>
    </div>

    <div class="col-md-4">
      <label class="form-label">Tanggal Laporan</label>
      <input type="date" name="report_date" class="form-control"
             value="{{ old('report_date', $report->report_date) }}" readonly>
    </div>

    <div class="col-md-4">
      <label class="form-label">Ketua Tim</label>
      <input type="text" name="team_lead" class="form-control"
             value="{{ old('team_lead', $report->team_lead) }}" readonly>
    </div>

    <div class="col-md-4">
      <label class="form-label">No HP Ketua Tim</label>
      <input type="text" name="phone_number_teamlead" class="form-control"
             value="{{ old('phone_number_teamlead', $report->phone_number_teamlead) }}" readonly>
    </div>

    <div class="col-md-4">
      <label class="form-label">Deadline Laporan</label>
      <input type="date" name="report_deadline" class="form-control"
             value="{{ old('report_deadline', optional($report->report_deadline)->format('Y-m-d')) }}">
    </div>

    <div class="col-md-4">
      <label class="form-label">No HP OPD</label>
      <input type="tel" name="phone_number_opd" class="form-control"
             placeholder="Isi No HP" 
             value="{{ old('phone_number_opd', $report->phone_number_opd) }}">
    </div>
    <div class="col-md-4">
  <div class="d-flex justify-content-between align-items-center">
    <label class="form-label mb-1">File Bukti kirim ke OPD</label>
    @if($report->sending_evidence && Storage::disk('public')->exists($report->sending_evidence))
      <a href="{{ Storage::url($report->sending_evidence) }}" target="_blank"
         class="small text-decoration-none">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="me-1">
          <path d="M14 2v6h6M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"
                stroke="currentColor" stroke-width="1.5" fill="none"/>
        </svg>
        Buka
      </a>
    @endif
  </div>

  <input type="file" name="sending_evidence" class="form-control"
         accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
</div>

  </div>

  <div class="d-flex justify-content-end mt-3 gap-3">
    <button type="submit" class="btn btn-success">Simpan</button>
    <button type="button"
          class="btn btn-primary"
          id="btnKirimNotif"
          {{ empty(old('phone_number_opd', $report->phone_number_opd)) ? 'disabled' : '' }}>
    Kirim Notifikasi
  </button>
  </div>
</form>
@else
  <p class="text-muted mb-0">Belum ada data laporan untuk diedit.</p>
@endif
@endrole


              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection


@push('scripts')

@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    })
</script>
@endif

@if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            })
        </script>
@endif

@if ($errors->any())
  <script>
    const errorHtml = `
    <div style="text-align: left;">
      <strong>Terjadi kesalahan:</strong></br>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  `;

    // Panggil SweetAlert
    Swal.fire({
      icon: 'error',
      title: 'Pengisian Gagal!',
        html: errorHtml,
        confirmButtonText: 'OK'
    });
  </script>
@endif


<script>
  $(function () {
    const $hp  = $('input[name="phone_number_opd"]');
    const $btn = $('#btnKirimNotif');


    function syncBtn() {
      $btn.prop('disabled');
    }

    // set kondisi awal & update saat berubah
    syncBtn();
    $hp.on('input change blur', syncBtn);
  });
</script>


<script>
    function cannotEditWarning() {
      Swal.fire({
        icon: 'warning',
        title: 'Tidak Dapat Diedit / Dihapus',
        text: 'Data ini sudah disetujui, Anda tidak dapat mengeditnya/menghapusnya lagi.',
      });
    }
    
    function cannotDeleteWarning() {
      Swal.fire({
        icon: 'warning',
        title: 'Tidak Dapat Dihapus',
        text: 'Data ini sudah disetujui, Anda tidak dapat menghapusnya lagi.',
      });
    }
    </script>

<script>
function confirmDelete(id) {
    let form = document.getElementById('form-delete-' + id);
        if (!form) {
            console.error('Form with ID form-delete-' + id + ' not found!');
            return;
        }
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: 'Data ini akan dihapus secara permanen!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      // Submit form
      document.getElementById('form-delete-' + id).submit();
    }
  })
}
</script>

!-- Search JS (DataTables init, dsb) -->
<script>
  $(document).ready(function () {
    $("#basic-datatables").DataTable({});

    $("#multi-filter-select").DataTable({
      pageLength: 5,
      initComplete: function () {
        this.api()
          .columns()
          .every(function () {
            var column = this;
            var select = $(
              '<select class="form-select"><option value=""></option></select>'
            )
              .appendTo($(column.footer()).empty())
              .on("change", function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                select.append(
                  '<option value="' + d + '">' + d + "</option>"
                );
              });
          });
      },
    });

    // Add Row
    $("#add-row").DataTable({
      pageLength: 5,
    });

    var action =
      '<td> <div class="form-button-action">' +
      '<button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task">' +
      '<i class="fa fa-edit"></i></button>' +
      '<button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove">' +
      '<i class="fa fa-times"></i></button></div></td>';

    $("#addRowButton").click(function () {
      $("#add-row")
        .dataTable()
        .fnAddData([
          $("#addName").val(),
          $("#addPosition").val(),
          $("#addOffice").val(),
          action,
        ]);
      $("#addRowModal").modal("hide");
    });
  });
</script>






@endpush