@extends('layouts.partials.app')

@section('content')
    
      <!-- Page Title -->
      <div class="container">
        <div class="page-inner">
          <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
              <h3 class="fw-bold mb-3">DATA USER</h3>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
              <label>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, j F Y') }}</label>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#addRowModal">
                        <i class="fa fa-plus"></i>
                        Tambah User
                      </button>
                    </div>
                  </div>
                  <div class="card-body">

                    <!-- Modal Tambah Aktivitas -->
                    <div class="modal fade" id="addRowModal" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header border-0">
                            <h5 class="modal-title">
                              <span class="fw-mediumbold"> Tambah</span>
                              <span class="fw-light"> User </span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" action="close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <form action="{{ route('admin.storeUser') }}" method="POST">
                                @csrf
                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group">
                                    <label for="">Username</label>
                                    <input id="" type="text" name="username" class="form-control" placeholder="Isi Username" />
                                  </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="form-group">
                                    <label for="">Nama Instansi</label>
                                      <select name="instansi" class="form-select" placeholder="Isi asal Instansi">
                                        <option selected>Pilih Instansi User</option>
                                        @foreach($opds as $item => $opd)
                                        <option value="{{ $opd->nama_instansi }}">
                                          {{ $opd->nama_instansi }}
                                        </option>
                                      @endforeach
                                    </select>
                                  </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="form-group">
                                    <label for="">No. HP</label>
                                    <input id="" type="text" name="no_hp" class="form-control"
                                      placeholder="Isi No HP"></input>
                                  </div>
                                </div>
                                {{-- <div class="col-sm-12">
                                  <div class="form-group">
                                    <label>Wilayah Kerja</label>
                                    <select id="wilayah_kerja" type="" name="region" class="form-select" placeholder="Sesuaikan SKP">
                                        <option selected>Pilih wilayah kerja pegawai</option>
                                        @foreach($workRegion as $item => $region)
                                        <option value="{{ $region->name }}">
                                          {{ $region->name }}
                                        </option>
                                      @endforeach
                                    </select>
                                  </div>
                                </div> --}}
                                {{-- <div class="col-sm-12">
                                  <div class="form-group">
                                    <label>Nama Atasan</label>
                                    <select id="atasan" type="" name="nip_atasan" class="form-select" placeholder="Sesuaikan SKP">
                                      <option selected>Pilih atasan wilayah kerja</option>
                                      @foreach($atasan as $item => $atasanRegion)
                                      <option value="{{ $atasanRegion->nip }}">
                                        {{ $atasanRegion->name }}
                                      </option>
                                    @endforeach
                                    </select>
                                  </div>
                                </div> --}}
                              </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="submit" class="btn btn-primary" action="">
                                    Simpan
                                </button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal" action="">
                                    Batal
                                </button>
                            </div>
                        </form>
                        </div>
                      </div>
                    </div>

                    <!-- Header Tabel -->
                    <div class="table-responsive">
                      <table id="add-row" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Instansi</th>
                            <th>Status</th>
                            <th style="width: 10%">Aksi</th>
                          </tr>
                        </thead>

                        <!-- Isi Tabel -->
                        <tbody>
                            @foreach ($users as $index => $user)
                          <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->instansi }}</td>
                            @if ($user->status == 1)
                                <td>Active</td>
                            @else
                              <td>Inactive</td>
                            @endif
                            <td>
                              <div class="form-button-action">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#editSKPModal{{ $user->id }}" title=""
                                    class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task">
                                    <i class="fa fa-edit"></i>
                                  </button>
                                <button type="button"  onclick="confirmDelete({{ $user->id }})"
                                    class="btn btn-link btn-danger">
                                    <i class="fa fa-times"></i>
                                  </button>
                              </div>
                            </td>
                          </tr>

                          <div class="modal fade" id="editSKPModal{{ $user->id }}" tabindex="-1" aria-labelledby="editSKPModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                            <div class="modal-content">
                                
                                <!-- Header Modal -->
                                <div class="modal-header">
                                <h5 class="modal-title" id="editSKPModalLabel{{ $user->id }}">Edit Pegawai #{{ $user->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <!-- Form Edit -->
                                <div class="modal-body">
                                <form action="{{ route('admin.updateEmployee', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT') 
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="">Username</label>
                                          <input id="" type="text" name="username" class="form-control" placeholder="Isi Username" value="{{ $user->username }}"/>
                                        </div>
                                      </div>
                                        <div class="col-sm-12">
                                          <div class="form-group">
                                            <label for="">Nama Instansi</label>
                                            @php
                                              $current = trim((string) old('instansi', $user->instansi)); // nilai tersimpan / old()
                                              $master  = collect($opds)->pluck('nama_instansi')->map(fn($v) => trim((string) $v));
                                            @endphp

                                            <select name="instansi" class="form-select">
                                              <option value="" disabled {{ $current === '' ? 'selected' : '' }}>
                                                Pilih Instansi User
                                              </option>

                                              {{-- Jika nilai tersimpan tidak ada di master, tetap tampilkan sebagai opsi terpilih --}}
                                              @if($current !== '' && !$master->contains(fn($v) => mb_strtolower($v) === mb_strtolower($current)))
                                                <option value="{{ $current }}" selected>
                                                  {{ $current }} (tidak ada di master)
                                                </option>
                                              @endif

                                              @foreach ($opds as $opd)
                                                @php $val = trim((string) $opd->nama_instansi); @endphp
                                                <option value="{{ $val }}"
                                                  {{ mb_strtolower($current) === mb_strtolower($val) ? 'selected' : '' }}>
                                                  {{ $opd->nama_instansi }}
                                                </option>
                                              @endforeach
                                            </select>
                                          </div>
                                        </div>
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="">No HP</label>
                                          <input id="" type="text" name="no_hp" class="form-control"
                                            placeholder="Isi No HP" value="{{ $user->no_hp }}"></input>
                                        </div>
                                      </div>
                                      {{-- <div class="col-sm-12">
                                        <div class="form-group">
                                          <label>Wilayah Kerja</label>
                                          <select id="wilayah_kerja_edit" type="" name="region" class="form-select" placeholder="Sesuaikan SKP">
                                              <option selected>Pilih wilayah kerja pegawai</option>
                                              @foreach($workRegion as $item => $region)
                                              <option value="{{ $region->name }}" {{ $user->region == $region->name ? 'selected' : '' }}>
                                                {{ $region->name }}
                                              </option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label>Nama Atasan</label>
                                          <select id="atasan_edit" type="" name="nip_atasan" class="form-select" placeholder="Sesuaikan SKP">
                                            <option selected>Pilih atasan wilayah kerja</option>
                                            @foreach($atasan as $item => $atasanRegion)
                                            <option value="{{ $atasanRegion->nip }}" {{ $user->nip_atasan == $atasanRegion->nip ? 'selected' : '' }}>
                                              {{ $atasanRegion->name }}
                                            </option>
                                          @endforeach
                                          </select>
                                        </div>
                                      </div> --}}
                                    </div>
                                  </div>
                                
                                <!-- Footer Modal -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                    Save changes
                                    </button>
                                </div>
                                </form>
                                <!-- End Form Edit -->
        
                                                    <!-- Form DELETE -->
                            <form id="form-delete-{{ $user->id }}" 
                                action="{{ route('admin.softDeleteEmployee', $user->id) }}" 
                                method="POST" 
                                style="display: none;">
                            @csrf
                            @method('PUT')
                             </form>

                          @endforeach
                        </tbody>
                      </table>
                    </div>
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

<script>
    $(document).ready(function() {
        $('#asal_instansi,#wilayah_kerja_edit').on('change', function() {
            var wilayah = $(this).val();
            if (wilayah) {
                $.ajax({
                    url: '/admin/get-atasan/' + encodeURIComponent(wilayah),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#atasan,#atasan_edit').empty();
                        $('#atasan,#atasan_edit').append('<option value="">Pilih atasan wilayah kerja</option>');
                        $.each(data, function(nip, nama) {
                            $('#atasan,#atasan_edit').append('<option value="' + nip + '">' + nama + '</option>');
                        });
                    }
                });
            } else {
                $('#atasan,#atasan_edit').empty();
                $('#atasan,#atasan_edit').append('<option value="">Pilih atasan wilayah kerja</option>');
            }
        });
    });
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
        '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

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
