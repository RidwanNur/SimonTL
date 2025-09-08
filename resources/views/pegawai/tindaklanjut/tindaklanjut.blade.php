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
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    @role('pegawai')
                                        <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                                            data-bs-target="#addRowModal" id="btnTambahTL">
                                            <i class="fa fa-plus"></i>
                                            Tambah Tindak Lanjut
                                        </button>
                                    @endrole
                                </div>
                            </div>
                            <div class="card-body">
                                @role('inspektorat')
                                    <!-- Modal Tambah Aktivitas -->
                                    <div class="modal fade" id="addRowModal" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title">
                                                        <span class="fw-mediumbold"> Tambah</span>
                                                        <span class="fw-light"> Tindak Lanjut </span>
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close" action="close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('atasan.storeActivity') }}" method="POST">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Jam Mulai</label>
                                                                    <input id="addPosition" name="start_time" type="time"
                                                                        class="form-control" placeholder="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Jam Selesai</label>
                                                                    <input id="addOffice" name="end_time" type="time"
                                                                        class="form-control" placeholder="" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>SKP Acuan</label>
                                                                    <select class="btn btn-light dropdown-toggle" type="button"
                                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                                        name="skp_id" id="skp_id">
                                                                        <option value="" class="dropdown-item">Pilih SKP
                                                                        </option>
                                                                        @foreach ($skp as $item => $rowSkp)
                                                                            <option value="{{ $rowSkp->id }}">
                                                                                {{ $rowSkp->name_skp }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="">Aktivitas</label>
                                                                    <input id="activity" type="text" name="activity"
                                                                        class="form-control"
                                                                        placeholder="Isi sesuai aktivitas anda" />
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="">Deskripsi</label>
                                                                    <textarea id="description" type="text" name="description" class="form-control"
                                                                        placeholder="Isi sesuai aktivitas anda"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="submit" class="btn btn-primary">
                                                        Add
                                                    </button>
                                                    {{-- <button type="button" class="btn btn-danger" data-dismiss="modal" action="">
                                Close
                            </button> --}}
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endrole
                                <!-- End Modal -->

                                <!-- Tabel Aktivitas -->
                                @role('admin')
                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nomor Laporan</th>
                                                    <th>Nama Laporan</th>
                                                    <th>Ketua Tim</th>
                                                    <th>Deadline</th>
                                                    <th>No HP OPD</th>
                                                    <th>File Bukti</th>
                                                    <th style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($tl as $item => $row)
                                                    <tr>
                                                        <td>{{ $row->report_name }}</td>
                                                        <td>{{ $row->report_name }}</td>
                                                        <td>{{ $row->team_lead }}</td>
                                                        <td>{{ $row->report_dateline }}</td>
                                                        <td>{{ $row->phone_number_opd }}</td>
                                                        <td><a href="{{ Storage::url($row->sending_evidence) }}"
                                                                target="_blank">Lihat</a></td>
                                                        <td>
                                                            <div class="form-button-action">

                                                                <button type="button" data-bs-toggle="modal"
                                                                    data-bs-target="#editRowModal{{ $row->id }}"
                                                                    title="" class="btn btn-link btn-primary btn-lg"
                                                                    data-original-title="Lihat Tindak Lanjut">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                                <a href="{{ route('pegawai.viewTL', ['laporan' => $row->id]) }}"
                                                                    class="btn btn-link btn-primary btn-lg" title="Lihat">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="editRowModal{{ $row->id }}"
                                                        tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header border-0">
                                                                    <h5 class="modal-title">
                                                                        <span class="fw-mediumbold">Detail</span>
                                                                        <span class="fw-light">Tindak Lanjut</span>
                                                                    </h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close"
                                                                        action="close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>

                                                                <!-- Body Modal: Form Edit -->
                                                                <div class="modal-body">
                                                                    <form action="{{ route('admin.updateTL', $row->id) }}"
                                                                        method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>No Laporan</label>
                                                                                    <input type="text" name="report_number"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_number }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Nama Laporan</label>
                                                                                    <input type="text" name="report_name"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_name }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Tanggal Laporan</label>
                                                                                    <input type="date"
                                                                                        name="report_dateline"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_dateline }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Ketua Tim</label>
                                                                                    <input type="text" name="team_lead"
                                                                                        class="form-control"
                                                                                        value="{{ $row->team_lead }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>No. HP OPD</label>
                                                                                    <input type="text"
                                                                                        name="phone_number_opd"
                                                                                        class="form-control"
                                                                                        value="{{ $row->phone_number_opd }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <textarea name="tl_description" id="tl_descriotion" rows="4"
                                                                                        class="form-control @error('tl_description') is-invalid @enderror"
                                                                                        placeholder="Tuliskan keterangan singkat (opsional)" disabled>{{ old('tl_description', $row->tl_description ?? '') }}</textarea>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Assign ke Pegawai</label>
                                                                                    <select name="assign_to"
                                                                                        class="form-control @error('assign_to') is-invalid @enderror">
                                                                                        <option value="">— Pilih Pegawai
                                                                                            —</option>
                                                                                        @foreach ($pegawai as $u)
                                                                                            <option
                                                                                                value="{{ $u->id }}"
                                                                                                {{ old('assign_to', $row->assign_to ?? null) == $u->id ? 'selected' : '' }}>
                                                                                                {{ $u->username }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @error('assign_to')
                                                                                        <div class="invalid-feedback">
                                                                                            {{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>

                                                                            <!-- Deskripsi -->
                                                                            <div class="col-sm-12 mb-4">
                                                                                <div class="form-group">
                                                                                    <label>Upload File Laporan</label>
                                                                                    <input
                                                                                        id="file_name_edit_{{ $row->id }}"
                                                                                        type="file" name="file_name_edit"
                                                                                        class="form-control filepond-input-edit"
                                                                                        placeholder="Silahkan Upload File Laporan"
                                                                                        value="{{ $row->link_file }}" />
                                                                                    {{-- <input id="file_name_edit_{{ $row->id }}" type="file" name="file_name_edit" class="form-control filepond-input-edit"
                                                     placeholder="Silahkan Upload File Laporan" value="{{ $row->link_file }}" /> --}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- End Row -->

                                                                        <!-- Footer Modal -->
                                                                        <div class="modal-footer border-0">
                                                                            {{-- <button type="submit"  class="btn btn-primary">
                                                        Simpan
                                                    </button> --}}
                                                                            <button type="submit" class="btn btn-primary"
                                                                                data-dismiss="modal">
                                                                                Assign
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div> <!-- modal-body -->
                                                            </div> <!-- modal-content -->
                                                        </div> <!-- modal-dialog -->
                                                    </div> <!-- modal fade -->
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endrole

                                @role('pegawai')
                                    <!-- Modal Tambah Aktivitas -->
                                    <div class="modal fade" id="addRowModal" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title">
                                                        <span class="fw-mediumbold"> Tambah</span>
                                                        <span class="fw-light"> Tindak Lanjut </span>
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close" action="close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('pegawai.storeTindakLanjut') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label class="form-label">Laporan</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">
                                                                            <i class="fa fa-file-alt"></i>
                                                                        </span>
                                                                        <select name="report_number" id="report_number"
                                                                            class="form-select">
                                                                            <option value="" disabled selected>Pilih
                                                                                Laporan</option>
                                                                            @foreach ($laporan as $item => $rowReport)
                                                                                <option value="{{ $rowReport->id }}">
                                                                                    {{ $rowReport->report_number }} —
                                                                                    {{ $rowReport->report_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <small class="form-text text-muted">Pilih nomor laporan
                                                                        yang akan ditindaklanjuti.</small>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Deadline Lapoan</label>
                                                                    <input id="report_dateline" type="date"
                                                                        name="report_dateline" class="form-control"
                                                                        placeholder="Isi Tanggal Laporan" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>No.HP OPD</label>
                                                                    <input id="phone_number_opd" type="text"
                                                                        name="phone_number_opd" class="form-control"
                                                                        placeholder="Isi Nomor HP OPD" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="submit" class="btn btn-primary">
                                                        Tambah
                                                    </button>
                                                    {{-- <button type="button" class="btn btn-danger" data-dismiss="modal" action="">
                                Close
                            </button> --}}
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endrole
                                <!-- End Modal -->

                                <!-- Tabel Aktivitas -->
                                @role('opd')
                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nomor Laporan</th>
                                                    <th>Nama Laporan</th>
                                                    <th>Ketua Tim</th>
                                                    <th>Deadline</th>
                                                    <th>No HP OPD</th>
                                                    <th>File Bukti</th>
                                                    <th style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($tl as $item => $row)
                                                    <tr>
                                                        <td>{{ $row->report_name }}</td>
                                                        <td>{{ $row->report_name }}</td>
                                                        <td>{{ $row->team_lead }}</td>
                                                        <td>{{ $row->report_dateline }}</td>
                                                        <td>{{ $row->phone_number_opd }}</td>
                                                        <td><a href="{{ Storage::url($row->sending_evidence) }}"
                                                                target="_blank">Lihat</a></td>
                                                        <td>
                                                            <div class="form-button-action">

                                                                <button type="button" data-bs-toggle="modal"
                                                                    data-bs-target="#editRowModal{{ $row->id }}"
                                                                    title="" class="btn btn-link btn-primary btn-lg"
                                                                    data-original-title="Lihat Tindak Lanjut">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                                <a href="{{ route('opd.viewTL', ['laporan' => $row->id]) }}"
                                                                    class="btn btn-link btn-primary btn-lg" title="Lihat">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="editRowModal{{ $row->id }}"
                                                        tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header border-0">
                                                                    <h5 class="modal-title">
                                                                        <span class="fw-mediumbold">Detail</span>
                                                                        <span class="fw-light">Tindak Lanjut</span>
                                                                    </h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close"
                                                                        action="close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>

                                                                <!-- Body Modal: Form Edit -->
                                                                <div class="modal-body">
                                                                    <form
                                                                        action="{{ route('pegawai.updateActivity', $row->id) }}"
                                                                        method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>No Laporan</label>
                                                                                    <input type="text" name="report_number"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_number }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Nama Laporan</label>
                                                                                    <input type="text" name="report_name"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_name }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Tanggal Laporan</label>
                                                                                    <input type="date"
                                                                                        name="report_dateline"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_dateline }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Ketua Tim</label>
                                                                                    <input type="text" name="team_lead"
                                                                                        class="form-control"
                                                                                        value="{{ $row->team_lead }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>No. HP OPD</label>
                                                                                    <input type="text"
                                                                                        name="phone_number_opd"
                                                                                        class="form-control"
                                                                                        value="{{ $row->phone_number_opd }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <textarea name="tl_description" id="tl_descriotion" rows="4"
                                                                                        class="form-control @error('tl_description') is-invalid @enderror"
                                                                                        placeholder="Tuliskan keterangan singkat (opsional)" disabled>{{ old('tl_description', $row->tl_description ?? '') }}</textarea>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Deskripsi -->
                                                                            <div class="col-sm-12 mb-4">
                                                                                <div class="form-group">
                                                                                    <label>Upload File Laporan</label>
                                                                                    <input
                                                                                        id="file_name_edit_{{ $row->id }}"
                                                                                        type="file" name="file_name_edit"
                                                                                        class="form-control filepond-input-edit"
                                                                                        placeholder="Silahkan Upload File Laporan"
                                                                                        value="{{ $row->link_file }}" />
                                                                                    {{-- <input id="file_name_edit_{{ $row->id }}" type="file" name="file_name_edit" class="form-control filepond-input-edit"
                                                     placeholder="Silahkan Upload File Laporan" value="{{ $row->link_file }}" /> --}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- End Row -->

                                                                        <!-- Footer Modal -->
                                                                        <div class="modal-footer border-0">
                                                                            {{-- <button type="submit"  class="btn btn-primary">
                                                        Simpan
                                                    </button> --}}
                                                                            <button type="button" class="btn btn-danger"
                                                                                data-dismiss="modal" action="">
                                                                                Close
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div> <!-- modal-body -->
                                                            </div> <!-- modal-content -->
                                                        </div> <!-- modal-dialog -->
                                                    </div> <!-- modal fade -->
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endrole
                                <!-- End Tabel Aktivitas -->

                                <!-- Tabel Aktivitas -->
                                @role('pegawai')

                                    {{-- NAV TABS --}}
                                    <ul class="nav nav-pills card-header-pills gap-2" id="tlTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="buat-tab" data-bs-toggle="tab"
                                                data-bs-target="#buat-pane" type="button" role="tab">
                                                <i class="fa fa-clipboard-list me-1"></i> Buat Tindak Lanjut
                                                <span class="badge bg-secondary ms-2">{{ $tl->count() ?? 0 }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="verif-tab" data-bs-toggle="tab"
                                                data-bs-target="#verif-pane" type="button" role="tab">
                                                <i class="fa fa-check-circle me-1"></i> Verifikasi Tindak Lanjut
                                                <span
                                                    class="badge bg-secondary ms-2">{{ $tl_verifikasi->count() ?? 0 }}</span>
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content pt-3">

                                        {{-- TAB 1: BUAT --}}
                                        <div class="tab-pane fade show active" id="buat-pane" role="tabpanel"
                                            aria-labelledby="buat-tab">
                                            <div class="table-responsive">
                                                <table id="dt-buat" class="table table-striped table-hover w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>Nomor Laporan</th>
                                                            <th>Nama Laporan</th>
                                                            <th>Ketua Tim</th>
                                                            <th>Deadline</th>
                                                            <th>No HP OPD</th>
                                                            <th>File Bukti</th>
                                                            <th style="width:10%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($tl as $row)
                                                            <tr>
                                                                <td>{{ $row->report_number }}</td>
                                                                <td>{{ $row->report_name }}</td>
                                                                <td>{{ $row->team_lead }}</td>
                                                                <td>{{ $row->report_dateline }}</td>
                                                                <td>{{ $row->phone_number_opd }}</td>
                                                                <td>
                                                                    @if ($row->sending_evidence && Storage::disk('public')->exists($row->sending_evidence))
                                                                        <a href="{{ Storage::url($row->sending_evidence) }}"
                                                                            target="_blank">Lihat</a>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-nowrap">
                                                                    <a href="{{ route('pegawai.viewTL', ['laporan' => $row->id]) }}"
                                                                        class="btn btn-link btn-primary btn-sm"
                                                                        title="Buat/Update">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <button type="button"
                                                                        class="btn btn-link btn-danger btn-sm"
                                                                        onclick="confirmDelete({{ $row->id }})"
                                                                        title="Hapus">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- TAB 2: VERIFIKASI --}}
                                        <div class="tab-pane fade" id="verif-pane" role="tabpanel"
                                            aria-labelledby="verif-tab">
                                            <div class="table-responsive">
                                                <table id="dt-verif" class="table table-striped table-hover w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>Nomor Laporan</th>
                                                            <th>Nama Laporan</th>
                                                            <th>Ketua Tim</th>
                                                            <th>Deadline</th>
                                                            <th>No HP OPD</th>
                                                            <th>File Bukti</th>
                                                            <th style="width:10%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($tl_verifikasi as $row)
                                                            <tr>
                                                                <td>{{ $row->report_number }}</td>
                                                                <td>{{ $row->report_name }}</td>
                                                                <td>{{ $row->team_lead }}</td>
                                                                <td>{{ $row->report_dateline }}</td>
                                                                <td>{{ $row->phone_number_opd }}</td>
                                                                <td>
                                                                    @if ($row->sending_evidence && Storage::disk('public')->exists($row->sending_evidence))
                                                                        <a href="{{ Storage::url($row->sending_evidence) }}"
                                                                            target="_blank">Lihat</a>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-nowrap">
                                                                    <a href="{{ route('pegawai.viewTL', ['laporan' => $row->id]) }}"
                                                                        class="btn btn-link btn-primary btn-sm"
                                                                        title="Verifikasi">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- HIDDEN DELETE FORMS (di luar tabel, aman untuk DataTables) --}}
                                    @foreach ($tl as $row)
                                        <form id="form-delete-{{ $row->id }}"
                                            action="{{ route('pegawai.softDeleteTL', $row->id) }}" method="POST"
                                            class="d-none">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    @endforeach

                                    {{-- MODAL TAMBAH tetap pakai punyamu yang sudah ada (tidak diubah) --}}
                                @endrole

                                <!-- End Tabel Aktivitas -->

                                <!-- Tabel Aktivitas -->
                                @role('o')
                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nomor Laporan</th>
                                                    <th>Nama Laporan</th>
                                                    <th>Ketua Tim</th>
                                                    <th>Deadline</th>
                                                    <th>No HP OPD</th>
                                                    <th>File Bukti</th>
                                                    <th style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($tl as $item => $row)
                                                    <tr>
                                                        <td>{{ $row->report_name }}</td>
                                                        <td>{{ $row->report_name }}</td>
                                                        <td>{{ $row->team_lead }}</td>
                                                        <td>{{ $row->report_dateline }}</td>
                                                        <td>{{ $row->phone_number_opd }}</td>
                                                        <td><a href="{{ Storage::url($row->sending_evidence) }}"
                                                                target="_blank">Lihat</a></td>
                                                        <td>
                                                            <div class="form-button-action">

                                                                <button type="button" data-bs-toggle="modal"
                                                                    data-bs-target="#editRowModal{{ $row->id }}"
                                                                    title="" class="btn btn-link btn-primary btn-lg"
                                                                    data-original-title="Lihat Tindak Lanjut">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                                <a href="{{ route('pegawai.viewTL', ['laporan' => $row->id]) }}"
                                                                    class="btn btn-link btn-primary btn-lg" title="Lihat">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <button type="button" data-bs-toggle="tooltip"
                                                                    title=""
                                                                    onclick="confirmDeleteo({{ $row->id }})"
                                                                    class="btn btn-link btn-danger"
                                                                    data-original-title="Remove">
                                                                    <i class="fa fa-times"></i>
                                                                </button>

                                                                {{-- <button type="button" 
                                                 onclick="cannotEditWarning()"
                                                  class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task">
                                                  <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" onclick="cannotEditWarning()"
                                                  class="btn btn-link btn-danger" data-original-title="Remove">
                                                  <i class="fa fa-times"></i>
                                                </button> --}}

                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="editRowModal{{ $row->id }}"
                                                        tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header border-0">
                                                                    <h5 class="modal-title">
                                                                        <span class="fw-mediumbold">Detail</span>
                                                                        <span class="fw-light">Tindak Lanjut</span>
                                                                    </h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close"
                                                                        action="close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>

                                                                <!-- Body Modal: Form Edit -->
                                                                <div class="modal-body">
                                                                    <form
                                                                        action="{{ route('pegawai.updateActivity', $row->id) }}"
                                                                        method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>No Laporan</label>
                                                                                    <input type="text" name="report_number"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_number }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Nama Laporan</label>
                                                                                    <input type="text" name="report_name"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_name }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Tanggal Laporan</label>
                                                                                    <input type="date"
                                                                                        name="report_dateline"
                                                                                        class="form-control"
                                                                                        value="{{ $row->report_dateline }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>Ketua Tim</label>
                                                                                    <input type="text" name="team_lead"
                                                                                        class="form-control"
                                                                                        value="{{ $row->team_lead }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>No. HP OPD</label>
                                                                                    <input type="text"
                                                                                        name="phone_number_opd"
                                                                                        class="form-control"
                                                                                        value="{{ $row->phone_number_opd }}"
                                                                                        readonly />
                                                                                </div>
                                                                            </div>

                                                                            <!-- Deskripsi -->
                                                                            <div class="col-sm-12 mb-4">
                                                                                <div class="form-group">
                                                                                    <label>Upload File Laporan</label>
                                                                                    <input
                                                                                        id="file_name_edit_{{ $row->id }}"
                                                                                        type="file" name="file_name_edit"
                                                                                        class="form-control filepond-input-edit"
                                                                                        placeholder="Silahkan Upload File Laporan"
                                                                                        value="{{ $row->link_file }}" />
                                                                                    {{-- <input id="file_name_edit_{{ $row->id }}" type="file" name="file_name_edit" class="form-control filepond-input-edit"
                                                     placeholder="Silahkan Upload File Laporan" value="{{ $row->link_file }}" /> --}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- End Row -->

                                                                        <!-- Footer Modal -->
                                                                        <div class="modal-footer border-0">
                                                                            {{-- <button type="submit"  class="btn btn-primary">
                                                        Simpan
                                                    </button> --}}
                                                                            <button type="button" class="btn btn-danger"
                                                                                data-dismiss="modal" action="">
                                                                                Close
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                    <form id="form-delete-{{ $row->id }}"
                                                                        action="{{ route('pegawai.softDeleteActivity', $row->id) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('PUT')
                                                                    </form>
                                                                </div> <!-- modal-body -->
                                                            </div> <!-- modal-content -->
                                                        </div> <!-- modal-dialog -->
                                                    </div> <!-- modal fade -->
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endrole
                                <!-- End Tabel Aktivitas -->
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
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});

            $("#multi-filter-select").DataTable({
                pageLength: 5,
                initComplete: function() {
                    this.api()
                        .columns()
                        .every(function() {
                            var column = this;
                            var select = $(
                                    '<select class="form-select"><option value=""></option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on("change", function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column
                                        .search(val ? "^" + val + "$" : "", true, false)
                                        .draw();
                                });

                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
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

            $("#addRowButton").click(function() {
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

    <script>
        FilePond.registerPlugin(FilePondPluginFileValidateSize, FilePondPluginFileValidateType);

        let pond; // avoid double init inside modal
        $('#addRowModal').on('shown.bs.modal', function() {
            if (pond) return;
            pond = FilePond.create(document.querySelector('#file_name'), {
                credits: false,
                storeAsFile: true, // keep normal form submit
                allowMultiple: false,
                required: true,
                maxFileSize: '5MB',
                acceptedFileTypes: [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'image/png', 'image/jpeg'
                ],
                labelIdle: `Drop your file(s) here or <span class="filepond--label-action">browse</span>
                  <div class="small text-muted mt-1">Max. File Size: 5 MB</div>`
            });
        });

        $('#addRowModal').on('hidden.bs.modal', function() {
            if (pond) {
                pond.destroy();
                pond = null;
            }
        });
    </script>

    <script>
        FilePond.registerPlugin(
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType
        );

        const pondOptions = {
            credits: false,
            storeAsFile: true, // submit bareng form (bukan XHR)
            allowMultiple: false,
            required: false, // di edit opsional
            maxFileSize: '10MB',
            acceptedFileTypes: [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/png', 'image/jpeg'
            ],
            labelIdle: `Drop file di sini atau <span class="filepond--label-action">browse</span>
                <div class="small text-muted mt-1">Max. 10 MB</div>`
        };

        // Saat modal EDIT dibuka, buat FilePond pada input di dalam modal
        $(document).on('shown.bs.modal', '[id^="editRowModal"]', function() {
            const input = this.querySelector('.filepond-input-edit');
            if (input) {
                input.name = 'file_name_edit'; // pastikan name benar
                input.removeAttribute('disabled'); // jangan disabled
                if (!input._pond) FilePond.create(input, pondOptions);
            }
        });

        // Jangan hancurkan terlalu cepat (kadang submit memicu hide)
        $(document).on('hidden.bs.modal', '[id^="editRowModal"]', function() {
            setTimeout(() => {
                const input = this.querySelector('.filepond-input-edit');
                if (input && input._pond) input._pond.destroy();
            }, 500);
        });
    </script>


<script>
  $(function () {
    function initDT(sel) {
      if ($.fn.dataTable.isDataTable(sel)) return $(sel).DataTable();
      return $(sel).DataTable({
        pageLength: 10,
        lengthMenu: [10,25,50,100],
        order: [],
        autoWidth: false,
        stateSave: true,
        language: {
          search: "Cari:",
          lengthMenu: "Tampilkan _MENU_ entri",
          info: "Menampilkan _START_–_END_ dari _TOTAL_ entri",
          infoEmpty: "Tidak ada data",
          zeroRecords: "Tidak ada data yang cocok",
          paginate: { previous: "Sebelumnya", next: "Berikutnya" }
        }
      });
    }

    const dtBuat = initDT('#dt-buat');
    let dtVerif;

    // tombol tambah hanya tampil di tab Buat
    const toggleAdd = target => $('#btnTambahTL').toggleClass('d-none', target !== '#buat-pane');
    toggleAdd('#buat-pane');

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
      const target = $(e.target).attr('data-bs-target');
      if (target === '#verif-pane' && !dtVerif) dtVerif = initDT('#dt-verif');
      $.fn.dataTable.tables({visible:true, api:true}).columns.adjust();
      toggleAdd(target);
    });
  });
</script>


@endpush
