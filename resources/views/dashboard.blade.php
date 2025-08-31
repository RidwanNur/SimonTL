@extends('layouts.partials.app')

@section('content')
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Dashboard</h3>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <label>Senin, 10 Maret 2025</label>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-primary bubble-shadow-small">
                <i class="fas fa-users"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                @role('atasan')
                <p class="card-category">Bawahan</p>
                <h4 class="card-title">{{ $bawahan[0]->TOTAL ?? 0  }}</h4>
                @endrole
                @role('admin')
                <p class="card-category">Total Pegawai</p>
                <h4 class="card-title">{{ $total_employee[0]->TOTAL ?? 0  }}</h4>
                @endrole
                @role('pegawai')
                <p class="card-category">Bawahan</p>
                <h4 class="card-title">------</h4>
                @endrole
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Contoh kartu selanjutnya -->
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-info bubble-shadow-small">
                <i class="fas fa-user-check"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                @role('pegawai')
                <p class="card-category">Aktivitas</p>
                <h4 class="card-title">----</h4>
                @endrole
                @role('atasan')
                <p class="card-category">Aktivitas</p>
                <h4 class="card-title">{{ $atasan_activity[0]->TOTAL ?? 0  }}</h4>
                @endrole
                @role('admin')
                <p class="card-category">Aktivitas Disetujui</p>
                <h4 class="card-title">{{ $total_activity_appr[0]->TOTAL ?? 0  }}</h4>
                @endrole
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-success bubble-shadow-small">
                <i class="fas fa-luggage-cart"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                @role('pegawai')
                <p class="card-category">Aktivitas Sudah Disetujui</p>
                <h4 class="card-title">------</h4>
                @endrole
                @role('atasan')
                <p class="card-category">Aktivitas Sudah Disetujui</p>
                <h4 class="card-title">{{ $atasan_activity_approve[0]->TOTAL ?? 0  }}</h4>
                @endrole
                @role('admin')
                <p class="card-category">Aktivitas Belum Disetujui</p>
                <h4 class="card-title">{{ $total_activity_delay[0]->TOTAL ?? 0  }}</h4>
                @endrole
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-secondary bubble-shadow-small">
                <i class="far fa-check-circle"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                @role('pegawai')
                <p class="card-category">Aktivitas Belum Disetujui</p>
                <h4 class="card-title">-------</h4>
                @endrole
                @role('atasan')
                <p class="card-category">Aktivitas Belum Disetujui</p>
                <h4 class="card-title">{{ $atasan_activity_delay[0]->TOTAL ?? 0 }}</h4>
                @endrole
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @role('admin')
  <div class="table-responsive">
    <table id="add-row" class="display table table-striped table-hover">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>NIP</th>
          <th>Nama Pegawai</th>
          <th>Aktivitas</th>
          <th>Deskripsi</th>
        </tr>
      </thead>

      <tbody>
        @foreach ($last_activity as $item => $row)
        <tr>
          <td>{{ $item+1 }}</td>
          <td>{{ $row->TANGGAL }}</td>
          <td>{{ $row->NIP }}</td>
          <td>{{ $row->NAMA_PEGAWAI }}</td>
          <td>{{ $row->ACTIVITY}}</td>
          <td>{{ $row->DESCRIPTION }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endrole
@endsection

@push('scripts')
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil',
        text: '{{ session('success') }}',
        timer: 5000,
        width: 850,
        padding: '2em',
        showConfirmButton: false
    })
</script>
@endif
<script>
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#177dff",
      fillColor: "rgba(23, 125, 255, 0.14)",
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#f3545d",
      fillColor: "rgba(243, 84, 93, .14)",
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#ffa534",
      fillColor: "rgba(255, 165, 52, .14)",
    });
  </script>
@endpush
