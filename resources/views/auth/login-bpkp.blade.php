<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Login – SimonTL</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />
  

  <!-- Fonts and icons -->
  <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700"] },
      custom: {
        families: ["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons"],
        urls: ["{{ asset('assets/css/fonts.min.css') }}"],
      },
      active: function(){ sessionStorage.fonts = true; },
    });
  </script>

  <!-- CSS base (pakai Bootstrap yg sudah ada) -->
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}" />


  <!-- Custom styles for this page -->

</head>
<body>
  <x-auth-session-status class="mb-4" :status="session('status')" />

  <main class="auth-wrap">
    <section class="panel lines">
      <div class="grid">
        <!-- Left: Welcome -->
        <div class="welcome">
          <div class="logo-mark" aria-hidden="true"></div>
          <h1>Welcome to <span style="color: #FF6347; font-weight: bold; font-size: 1.5em;">SimonTL!</span></h1>
          <p>
            SimonTL merupakan aplikasi yang dirancang untuk memantau progress tindak lanjut Laporan Hasil Pengawasan
          </p>
          {{-- <button type="button" class="btn-learn">Learn More</button> --}}
        </div>

        <!-- Right: Sign in -->
        <div class="signin">
          <div class="glass">
            <h3>Login</h3>
            <form action="{{ route('login') }}" method="POST" novalidate>
              @csrf
              <div class="mb-3">
                <label for="nip" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="{{ old('username') }}">
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
              </div>

             <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" name="password" id="password" class="form-control pe-5" placeholder="••••••••">
                    <span class="toggle-password" onclick="togglePassword()">
                    <i id="eye-icon" class="fas fa-eye"></i>
                    </span>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
              </div>

              <button class="btn-gradient" type="submit">{{ __('Submit') }}</button>

              {{-- <div class="d-flex align-items-center justify-content-center mt-4 socials">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a>
              </div> --}}
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>
</html>

<script>
function togglePassword(){
  const passInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eye-icon');
  if(passInput.type === 'password'){
    passInput.type = 'text';
    eyeIcon.classList.remove('fa-eye');
    eyeIcon.classList.add('fa-eye-slash');
  } else {
    passInput.type = 'password';
    eyeIcon.classList.remove('fa-eye-slash');
    eyeIcon.classList.add('fa-eye');
  }
}
</script>

