<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#5b1730">
    <meta name="description" content="Mi Ruta del Vino: guardá los vinos que probás, tus fotos, calificaciones y recuerdos.">

    <title>Mi Ruta del Vino</title>

    <style>
        :root {
            --wine: #641c36;
            --wine-dark: #3d1022;
            --wine-soft: #8e3151;
            --cream: #fffaf4;
            --sand: #f4eadf;
            --gold: #c99545;
            --ink: #211b1d;
            --muted: #74686c;
            --white: #ffffff;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: var(--cream);
        }

        a { color: inherit; text-decoration: none; }

        .wrap {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 250, 244, .94);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(100, 28, 54, .09);
        }

        .nav {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            font-weight: 850;
            letter-spacing: -.02em;
            color: var(--wine-dark);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: white;
            font-size: 23px;
            background: linear-gradient(145deg, var(--wine-soft), var(--wine-dark));
            box-shadow: 0 10px 24px rgba(100, 28, 54, .22);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            color: white;
            background: var(--wine);
            box-shadow: 0 10px 24px rgba(100, 28, 54, .22);
        }

        .btn-primary:hover { background: var(--wine-dark); }

        .btn-ghost {
            color: var(--wine-dark);
            border: 1px solid rgba(100, 28, 54, .18);
            background: rgba(255, 255, 255, .72);
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 86px 0 78px;
            background:
                radial-gradient(circle at 85% 18%, rgba(201, 149, 69, .20), transparent 23%),
                radial-gradient(circle at 72% 76%, rgba(142, 49, 81, .12), transparent 25%),
                linear-gradient(180deg, #fffaf4 0%, #f9f0e7 100%);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.12fr .88fr;
            gap: 56px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            color: var(--wine);
            background: rgba(100, 28, 54, .08);
            font-size: 13px;
            font-weight: 850;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 20px 0 18px;
            max-width: 760px;
            font-size: clamp(43px, 6vw, 74px);
            line-height: .98;
            letter-spacing: -.055em;
            color: var(--wine-dark);
        }

        .hero-copy {
            max-width: 660px;
            margin: 0;
            font-size: clamp(18px, 2.1vw, 22px);
            line-height: 1.58;
            color: var(--muted);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .hero-note {
            margin-top: 17px;
            font-size: 13px;
            color: #87787d;
        }

        .phone {
            position: relative;
            width: min(390px, 100%);
            margin-left: auto;
            padding: 14px;
            border-radius: 38px;
            background: #24191d;
            box-shadow: 0 32px 70px rgba(61, 16, 34, .24);
            transform: rotate(2deg);
        }

        .screen {
            overflow: hidden;
            border-radius: 28px;
            background: #fff;
        }

        .screen-head {
            padding: 24px 22px 18px;
            color: white;
            background: linear-gradient(145deg, #4b1229, #8d2d50 62%, #b8753d);
        }

        .screen-kicker {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            opacity: .85;
        }

        .screen-title {
            margin-top: 5px;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -.03em;
        }

        .wine-card {
            margin: 18px;
            overflow: hidden;
            border: 1px solid #eadfe3;
            border-radius: 22px;
            background: white;
            box-shadow: 0 12px 28px rgba(58, 28, 39, .08);
        }

        .wine-photo {
            height: 162px;
            display: grid;
            place-items: center;
            font-size: 66px;
            background:
                radial-gradient(circle at 70% 20%, rgba(255,255,255,.65), transparent 18%),
                linear-gradient(145deg, #f4d9ce, #ead7b8 55%, #d7b178);
        }

        .wine-body { padding: 17px 18px 19px; }
        .wine-label { color: var(--wine); font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; }
        .wine-name { margin-top: 4px; font-size: 21px; font-weight: 900; color: #2c2024; }
        .wine-meta { margin-top: 4px; font-size: 13px; color: #86777c; }
        .cups { margin-top: 13px; font-size: 18px; letter-spacing: 2px; }

        .section { padding: 80px 0; }
        .section-white { background: white; }

        .section-head {
            max-width: 720px;
            margin-bottom: 36px;
        }

        .section-head h2 {
            margin: 10px 0 12px;
            font-size: clamp(32px, 4vw, 48px);
            line-height: 1.05;
            letter-spacing: -.045em;
            color: var(--wine-dark);
        }

        .section-head p {
            margin: 0;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.65;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .feature {
            min-height: 205px;
            padding: 23px;
            border: 1px solid #eadfe3;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(66, 32, 44, .05);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            font-size: 25px;
            background: #f7ecef;
        }

        .feature h3 {
            margin: 17px 0 8px;
            font-size: 18px;
            color: var(--wine-dark);
        }

        .feature p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
            font-size: 14px;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .step {
            position: relative;
            padding: 27px;
            border-radius: 26px;
            background: var(--sand);
        }

        .step-number {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            color: white;
            background: var(--wine);
            font-weight: 900;
        }

        .step h3 { margin: 18px 0 8px; color: var(--wine-dark); font-size: 20px; }
        .step p { margin: 0; color: var(--muted); line-height: 1.6; font-size: 15px; }

        .roadmap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: stretch;
        }

        .roadmap-card {
            padding: 30px;
            border-radius: 28px;
            color: white;
            background: linear-gradient(145deg, var(--wine-dark), var(--wine-soft));
            box-shadow: 0 22px 48px rgba(61, 16, 34, .17);
        }

        .roadmap-card.alt {
            color: var(--wine-dark);
            border: 1px solid #ead6c6;
            background: linear-gradient(145deg, #fff4e7, #f4dfc3);
            box-shadow: none;
        }

        .roadmap-card .big { font-size: 39px; }
        .roadmap-card h3 { margin: 13px 0 9px; font-size: 25px; letter-spacing: -.02em; }
        .roadmap-card p { margin: 0; line-height: 1.62; opacity: .86; }

        .cta {
            padding: 78px 0;
            color: white;
            background:
                radial-gradient(circle at 85% 15%, rgba(212, 159, 74, .28), transparent 28%),
                linear-gradient(145deg, #35101f, #681e39 58%, #81324c);
        }

        .cta-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
        }

        .cta h2 {
            max-width: 700px;
            margin: 0;
            font-size: clamp(31px, 4vw, 48px);
            letter-spacing: -.045em;
            line-height: 1.06;
        }

        .cta p { max-width: 650px; margin: 13px 0 0; color: #efdce3; line-height: 1.6; }

        .cta .btn-primary {
            flex: 0 0 auto;
            color: var(--wine-dark);
            background: white;
            box-shadow: none;
        }

        footer {
            padding: 28px 0;
            background: #25151b;
            color: #cdbfc4;
            font-size: 13px;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
        }

        @media (max-width: 900px) {
            .hero { padding-top: 58px; }
            .hero-grid { grid-template-columns: 1fr; gap: 42px; }
            .phone { margin: 0 auto; transform: none; }
            .features { grid-template-columns: repeat(2, 1fr); }
            .steps { grid-template-columns: 1fr; }
            .roadmap { grid-template-columns: 1fr; }
            .cta-box { align-items: flex-start; flex-direction: column; }
        }

        @media (max-width: 620px) {
            .wrap { width: min(100% - 24px, 1120px); }
            .nav { min-height: 66px; }
            .brand span:last-child { display: none; }
            .nav-actions .btn { min-height: 40px; padding: 0 13px; font-size: 13px; }
            .hero { padding: 48px 0 58px; }
            .hero-actions { flex-direction: column; }
            .hero-actions .btn { width: 100%; }
            .features { grid-template-columns: 1fr; }
            .section { padding: 62px 0; }
            .footer-row { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="wrap nav">
            <a class="brand" href="{{ url('/') }}" aria-label="Mi Ruta del Vino">
                <span class="brand-mark">🍷</span>
                <span>Mi Ruta del Vino</span>
            </a>

            <nav class="nav-actions" aria-label="Acceso">
                @auth
                    <a class="btn btn-primary" href="{{ route('mi-bodega.index') }}">Entrar a mi bodega</a>
                @else
                    @if (Route::has('login'))
                        <a class="btn btn-ghost" href="{{ route('login') }}">Iniciar sesión</a>
                    @endif
                    @if (Route::has('register'))
                        <a class="btn btn-primary" href="{{ route('register') }}">Crear cuenta</a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="wrap hero-grid">
                <div>
                    <span class="eyebrow">🍇 Tu historia, copa a copa</span>
                    <h1>Los vinos pasan. Los recuerdos quedan.</h1>
                    <p class="hero-copy">
                        Guardá los vinos que probás, sumá fotos, calificá cada experiencia y armá una bodega personal que realmente cuente tu recorrido.
                    </p>

                    <div class="hero-actions">
                        @auth
                            <a class="btn btn-primary" href="{{ route('mi-bodega.index') }}">🍷 Ver mi bodega</a>
                        @else
                            @if (Route::has('register'))
                                <a class="btn btn-primary" href="{{ route('register') }}">Empezar mi ruta</a>
                            @endif
                            @if (Route::has('login'))
                                <a class="btn btn-ghost" href="{{ route('login') }}">Ya tengo cuenta</a>
                            @endif
                        @endauth
                    </div>

                    <p class="hero-note">Simple, personal y pensado para usar desde el celular.</p>
                </div>

                <div class="phone" aria-hidden="true">
                    <div class="screen">
                        <div class="screen-head">
                            <div class="screen-kicker">Mi Ruta del Vino</div>
                            <div class="screen-title">Mi Bodega Personal</div>
                        </div>
                        <div class="wine-card">
                            <div class="wine-photo">🍷</div>
                            <div class="wine-body">
                                <div class="wine-label">Una experiencia guardada</div>
                                <div class="wine-name">Ese vino que querés recordar</div>
                                <div class="wine-meta">Bodega · Añada · Varietal</div>
                                <div class="cups">🍷 🍷 🍷 🍷 ◐</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-white" id="que-podes-hacer">
            <div class="wrap">
                <div class="section-head">
                    <span class="eyebrow">Tu bodega personal</span>
                    <h2>Más que una lista de etiquetas.</h2>
                    <p>La idea es recordar qué tomaste, cuándo, dónde y qué te pasó con ese vino. Todo queda ordenado para volver a encontrarlo después.</p>
                </div>

                <div class="features">
                    <article class="feature">
                        <div class="feature-icon">📸</div>
                        <h3>Guardá la botella</h3>
                        <p>Sumá una foto para reconocer ese vino de un vistazo cuando lo vuelvas a cruzar.</p>
                    </article>

                    <article class="feature">
                        <div class="feature-icon">🍷</div>
                        <h3>Calificá a tu manera</h3>
                        <p>Registrá cuánto te gustó y construí tu propio criterio con el paso del tiempo.</p>
                    </article>

                    <article class="feature">
                        <div class="feature-icon">📝</div>
                        <h3>Guardá la experiencia</h3>
                        <p>Fecha, lugar, acompañamiento, precio, notas y ese recuerdo que hizo especial el momento.</p>
                    </article>

                    <article class="feature">
                        <div class="feature-icon">❤️</div>
                        <h3>Volvé a tus favoritos</h3>
                        <p>Marcá los que querés repetir y encontralos rápido cuando llegue la próxima ocasión.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <div class="section-head">
                    <span class="eyebrow">Así de simple</span>
                    <h2>Probás. Guardás. Recordás.</h2>
                </div>

                <div class="steps">
                    <article class="step">
                        <div class="step-number">1</div>
                        <h3>Probá un vino</h3>
                        <p>En tu casa, en un restaurante, de viaje, en una bodega o donde sea.</p>
                    </article>

                    <article class="step">
                        <div class="step-number">2</div>
                        <h3>Registrá el momento</h3>
                        <p>Foto, calificación y los datos que quieras conservar. No hace falta completar todo.</p>
                    </article>

                    <article class="step">
                        <div class="step-number">3</div>
                        <h3>Construí tu ruta</h3>
                        <p>Con cada experiencia tu bodega personal va contando mejor qué te gusta y dónde lo descubriste.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-white">
            <div class="wrap">
                <div class="section-head">
                    <span class="eyebrow">Lo que viene</span>
                    <h2>La ruta también va a estar en el mapa.</h2>
                    <p>Mi Ruta del Vino está creciendo para conectar cada botella con los lugares que forman parte de la experiencia.</p>
                </div>

                <div class="roadmap">
                    <article class="roadmap-card">
                        <div class="big">📍🍷</div>
                        <h3>Tu mapa del vino</h3>
                        <p>Origen del vino y lugar donde lo tomaste, para ver tu recorrido personal a través de ciudades, regiones y bodegas.</p>
                    </article>

                    <article class="roadmap-card alt">
                        <div class="big">🗺️</div>
                        <h3>Descubrí dónde tomar o comprar</h3>
                        <p>La idea es que, cuando estés en una ciudad, puedas encontrar lugares cercanos relacionados con el vino.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="cta">
            <div class="wrap cta-box">
                <div>
                    <h2>Tu próxima botella puede ser el comienzo de una historia.</h2>
                    <p>Creá tu cuenta y empezá a construir una memoria personal de los vinos que vas descubriendo.</p>
                </div>

                @auth
                    <a class="btn btn-primary" href="{{ route('mi-bodega.index') }}">Entrar a mi bodega</a>
                @else
                    @if (Route::has('register'))
                        <a class="btn btn-primary" href="{{ route('register') }}">Crear mi cuenta</a>
                    @endif
                @endauth
            </div>
        </section>
    </main>

    <footer>
        <div class="wrap footer-row">
            <div>🍷 Mi Ruta del Vino</div>
            <div>Hecho para recordar buenos vinos y mejores momentos.</div>
        </div>
    </footer>
</body>
</html>
