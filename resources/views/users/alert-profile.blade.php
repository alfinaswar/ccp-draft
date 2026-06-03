@if (auth()->check() && (empty(auth()->user()->UserUpdate) || is_null(auth()->user()->UserUpdate)))
    <div class="modal fade" id="alertLengkapiProfileModal" tabindex="-1" aria-labelledby="alertLengkapiProfileLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-gradient border-0"
                    style="background: linear-gradient(90deg,#ffecb3 0%,#fff8e1 100%);">
                    <h5 class="modal-title fw-bold text-warning-emphasis d-flex align-items-center gap-2"
                        id="alertLengkapiProfileLabel">
                        <span
                            class="badge rounded-circle bg-warning text-dark me-2 p-3 shadow-sm fs-3 wobble-animation">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </span>
                        Lengkapi Profil Anda
                    </h5>
                </div>
                <div class="modal-body text-center pb-1 pt-1">

                    <h5 class="fw-semibold mb-3 text-warning-emphasis">
                        Profil Anda Masih Kosong!
                    </h5>
                    <p class="mb-0 text-muted" style="font-size: 1.04rem;">
                        Anda belum melengkapi data profil, seperti <b>Departemen</b>, <b>Jabatan</b>, dan
                        <b>Email</b>.<br>
                        <span class="d-inline-block mt-2">
                            Mohon update <b>email aktif</b> Anda agar seluruh notifikasi dapat terkirim dengan baik.<br>
                            <span class="mt-2 d-block">Lengkapi profil agar bisa mengakses semua fitur sistem secara
                                optimal dan tidak melewatkan pemberitahuan penting.</span>
                        </span>
                    </p>
                    <div class="alert alert-warning d-inline-block mt-3" style="font-size: 0.98rem;">
                        <i class="fas fa-info-circle me-1"></i>
                        Pastikan alamat email yang Anda gunakan valid & aktif. Seluruh notifikasi (approval, status, dan
                        lainnya) akan dikirim ke email yang tercantum di profil.
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center mt-2 pb-4">
                    <a href="{{ route('users.show', encrypt(auth()->id())) }}"
                        class="btn btn-warning rounded-4 px-4 py-2 fw-bold shadow-sm lift-hover">
                        <i class="fas fa-user-edit me-2"></i> Lengkapi & Update Profil Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .wobble-animation {
            animation: wobble 1.5s infinite;
        }

        @keyframes wobble {
            0% {
                transform: rotate(-6deg);
            }

            25% {
                transform: rotate(4deg);
            }

            50% {
                transform: rotate(-2deg);
            }

            75% {
                transform: rotate(3deg);
            }

            100% {
                transform: rotate(-6deg);
            }
        }

        .animate-floating {
            animation: floatY 3s ease-in-out infinite;
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .lift-hover:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 24px 0 #ffe08242, 0 1.5px 2.5px rgba(0, 0, 0, 0.07);
        }
    </style>

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var modalEl = document.getElementById('alertLengkapiProfileModal');
            if (modalEl) {
                var myModal = new bootstrap.Modal(modalEl);
                myModal.show();
            }
        });
    </script>
@endif
