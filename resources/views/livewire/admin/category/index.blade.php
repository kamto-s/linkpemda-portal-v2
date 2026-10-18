<div>
    <div class="py-3 py-lg-4">
        <div class="row">
            <div class="col-lg-6">
                <h4 class="page-title mb-0">{{ $pageTitle }}</h4>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div>
                            <h4 class="">Daftar {{ $pageTitle }}</h4>
                            <p class="mb-0">
                                Kelola kategori untuk pengelompokan artikel dan materi.
                            </p>
                        </div>
                        <div>
                            <a href="" class="btn btn-primary"><i class="mdi mdi-plus me-1"></i> Tambah</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Nama</th>
                                    <th>Slug</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Dibuat Pada</th>
                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>Otto</td>
                                    <td>Otto</td>
                                    <td>Otto</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light dropdown-toggle text-end"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu shadow-sm" style="z-index: 9999;">
                                                <a class="dropdown-item" href="#"><i
                                                        class="mdi mdi-pencil me-1"></i> Edit</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#"><i
                                                        class="mdi mdi-trash-can me-1"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
