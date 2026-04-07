<?php
require_once __DIR__ . '/db.php';


// Авторизует пользователя по имени и паролю; запускает сессию при успехе
function login($username, $password){
    $db = polacz_z_baza();
    $result = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM users WHERE name = '$username' AND password = '$password' LIMIT 1"));
    if($result){
        $_SESSION["username"] = $result["name"];
        $_SESSION["email"]    = $result["email"];
        $_SESSION["role"]     = $result["role"];
        $_SESSION["login-in"] = true;
        header("Location: main.php");
        exit;
    } else {
        echo "<p style='color:red;text-align:center'>Nieprawidłowy login lub hasło</p>";
    }
}









// Регистрирует нового пользователя и создаёт сессию; выводит сообщение если логин занят
function registration($usernamedb, $passworddb, $emaildb){
    $db = polacz_z_baza();

    // Защита от спецсимволов
    $u = mysqli_real_escape_string($db, $usernamedb);
    $p = mysqli_real_escape_string($db, $passworddb);
    $e = mysqli_real_escape_string($db, $emaildb);

    $is_free = checklogin($u, $db);

    if($is_free){
        // ВАЖНО: Добавил created_at и CURDATE(), иначе база отклоняет запрос
        $sql = "INSERT INTO users (name, email, password, role, created_at) 
                VALUES ('$u', '$e', '$p', 'user', CURDATE())";
        
        $res = mysqli_query($db, $sql);

        if($res){
            $_SESSION["username"] = $usernamedb;
            $_SESSION["email"] = $emaildb;
            $_SESSION["role"] = "user";
            $_SESSION["login-in"] = true;
            header("Location: main.php");
            exit();    
        } else {
            // Если снова не сработает, эта строка покажет реальную причину
            die("Ошибка MySQL: " . mysqli_error($db));
        }
    } else {
        $_SESSION["error"] = "Ten login jest już zajęty";
        header("Location: register.php");
        exit();
    }
}

// Проверяет является ли текущий пользователь сессии администратором
// Возвращает true если role === 'admin', иначе false
function isAdmin(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Проверяет существует ли хотя бы один администратор в базе данных
// Используется чтобы показать кнопку создания первого админа если его ещё нет
function adminExists(){
    $db = polacz_z_baza();
    $result = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS count FROM users WHERE role = 'admin'"));



    return $result['count'] > 0;
}

// Проверяет свободен ли логин: возвращает true если не занят, false если уже существует
function checklogin($username, $db){
    $u = mysqli_real_escape_string($db, $username);
    $res = mysqli_query($db, "SELECT id FROM users WHERE name = '$u' LIMIT 1");
    
    if(mysqli_num_rows($res) > 0){
        return false; // Занят
    }
    return true; // Свободен
}


function loginstatus(){
if($_SESSION["login-in"] == true){
    return true;
}else{
    return false;
}
}


// ================================================================
// === LANDING PAGE: ФУНКЦИИ РЕНДЕРИНГА ЭЛЕМЕНТОВ INDEX.PHP =======
// ================================================================

// Возвращает <style> + шрифтовые ссылки для лендинга — вставляется в <head>
function get_landing_styles() {
    return <<<'ENDSTYLES'
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Trispace:wght@100..800&display=swap" rel="stylesheet">
<style>
    *, *::before, *::after {
        margin: 0; padding: 0; box-sizing: border-box;
        font-family: "Trispace", sans-serif;
    }
    /* overflow-x: clip НЕ создаёт scroll-container, поэтому не ломает position:sticky */
    html { overflow-x: clip; }
    body { background: #000; color: #fff; }

    /* ── HERO: scroll-driven video ── */
    /* #hero-section — высокий контейнер для прокрутки (500vh) */
    #hero-section { height: 500vh; }

    /* .hero-sticky — прилипает к верху, занимает ровно 1 экран */
    /* НЕТ overflow:hidden/clip-path на sticky-элементе — они ломают sticky в браузерах */
    .hero-sticky {
        position: sticky;
        top: 0;
        height: 100vh;
        width: 100%;
        background: #000;
    }

    /* Видео — абсолютный фон */
    #hero-video {
        position: absolute; inset: 0;
        width: 100%; height: 100%;
        object-fit: cover; display: block;
    }

    /* Тёмный градиент слева для читаемости текста */
    .hero-grad {
        position: absolute; inset: 0; z-index: 1;
        background: linear-gradient(
            90deg,
            rgba(0,0,0,0.82) 0%,
            rgba(0,0,0,0.45) 45%,
            transparent 70%
        );
        pointer-events: none;
    }

    /* Контейнер с тремя группами текста поверх видео, слева */
    .text-overlay {
        position: absolute; z-index: 2;
        left: 8vw; top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        /* минимальные размеры чтобы браузер не схлопнул контейнер */
        min-width: 1px; min-height: 1px;
    }

    /* Группы стакаются в одном месте; видна только одна активная */
    .text-group {
        position: absolute; top: 0; left: 0;
        display: flex; flex-direction: column; gap: 0.9rem;
    }

    /* ── FLOW LINE: влёт слева ── */
    .flow-line {
        font-size: clamp(1.2rem, 2.5vw, 2.4rem);
        font-weight: 800;
        letter-spacing: 0.06em;
        white-space: nowrap;
        opacity: 0;
        transform: translateX(-90px);
        transition: opacity 0.6s ease, transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        will-change: opacity, transform;
    }
    .flow-line.visible {
        opacity: 1;
        transform: translateX(0);
    }
    .flow-line[data-delay="0"] { transition-delay: 0s;   }
    .flow-line[data-delay="1"] { transition-delay: 0.4s; }
    .flow-line[data-delay="2"] { transition-delay: 0.8s; }

    #tg-brand    .flow-line { color: #ccff00; }
    #tg-desc     .flow-line { color: #ffffff; }
    #tg-contacts .flow-line { color: #aaaaaa; }

    /* ── SCROLL HINT: стрелка вниз по центру ── */
    #scroll-hint {
        position: fixed;
        bottom: 6vh; left: 50%;
        transform: translateX(-50%);
        z-index: 100;
        display: flex; flex-direction: column;
        align-items: center; gap: 5px;
        pointer-events: none;
        opacity: 1;
        transition: opacity 0.5s ease;
    }
    #scroll-hint.hidden { opacity: 0; }
    #scroll-hint .sh-label {
        font-size: 0.55rem;
        letter-spacing: 0.5em;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .sh-chevron {
        width: 16px; height: 16px;
        border-right: 2px solid rgba(255,255,255,0.7);
        border-bottom: 2px solid rgba(255,255,255,0.7);
        transform: rotate(45deg);
        animation: chev-pulse 1.4s ease-in-out infinite;
    }
    .sh-chevron:nth-child(2) { animation-delay: 0s;    opacity: 0.95; }
    .sh-chevron:nth-child(3) { animation-delay: 0.15s; opacity: 0.6;  }
    .sh-chevron:nth-child(4) { animation-delay: 0.30s; opacity: 0.25; }
    @keyframes chev-pulse {
        0%,100% { transform: rotate(45deg) translate(0,0);   }
        50%     { transform: rotate(45deg) translate(3px,3px); }
    }

    /* ── CTA FOOTER: ровно 1 экран ── */
    #cta-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2.5rem;
        height: 100vh;
        background: #000;
    }
    .cta-eyebrow {
        font-size: clamp(0.6rem, 1.6vw, 0.9rem);
        letter-spacing: 0.5em;
        color: #444;
        text-transform: uppercase;
        opacity: 0; transform: translateY(14px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .cta-eyebrow.visible { opacity: 1; transform: translateY(0); }
    .cta-wrap {
        opacity: 0; transform: translateY(24px);
        transition: opacity 0.7s 0.28s ease, transform 0.7s 0.28s ease;
    }
    .cta-wrap.visible { opacity: 1; transform: translateY(0); }
    .cta-button {
        padding: 1.4rem 5rem;
        font-size: clamp(0.9rem, 2vw, 1.4rem);
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        border: none; cursor: pointer;
        color: #000;
        background: linear-gradient(
            90deg,
            #ccff00 0%, #f0f0f0 22%, #00e5ff 44%,
            #ccff00 66%, #f0f0f0 88%, #ccff00 100%
        );
        background-size: 300% auto;
        animation: shimmer 2.5s linear infinite;
        transition: transform 0.2s ease, box-shadow 0.3s ease;
    }
    .cta-button:hover {
        transform: scale(1.06);
        box-shadow: 0 0 50px rgba(204,255,0,0.6);
    }
    .cta-button:active { transform: scale(0.96); }
    @keyframes shimmer {
        to { background-position: 300% center; }
    }

    /* ── CRACK OVERLAY ── */
    #crack-overlay {
        position: fixed; inset: 0; z-index: 9999;
        display: none; pointer-events: none;
    }
    #crack-canvas { display: block; width: 100%; height: 100%; }

    /* ── PAGE SHAKE ── */
    @keyframes page-shake {
        0%,100% { transform: translate(0,0); }
        15%     { transform: translate(-6px,-2px); }
        30%     { transform: translate( 6px, 2px); }
        45%     { transform: translate(-4px, 3px); }
        60%     { transform: translate( 4px,-3px); }
        75%     { transform: translate(-3px, 1px); }
        90%     { transform: translate( 3px,-1px); }
    }
</style>
ENDSTYLES;
}

// Возвращает HTML блока HERO: видео-фон + текстовые группы слева поверх
function render_landing_hero() {
    return <<<'ENDHERO'
<section id="hero-section">
    <div class="hero-sticky">
        <video id="hero-video" src="indexvid.mp4" muted playsinline preload="auto"></video>
        <div class="hero-grad"></div>
        <div class="text-overlay">
            <!-- Группа 1: бренд -->
            <div class="text-group" id="tg-brand">
                <span class="flow-line" data-delay="0">SWAG STORE</span>
                <span class="flow-line" data-delay="1">OSTRAVA</span>
            </div>
            <!-- Группа 2: описание -->
            <div class="text-group" id="tg-desc">
                <span class="flow-line" data-delay="0">PREMIUM SNEAKERS</span>
                <span class="flow-line" data-delay="1">AUTHENTIC STREETWEAR</span>
                <span class="flow-line" data-delay="2">LIMITED COLLECTIONS</span>
            </div>
            <!-- Группа 3: контакты -->
            <div class="text-group" id="tg-contacts">
                <span class="flow-line" data-delay="0">OSTRAVA, CZECH REPUBLIC</span>
                <span class="flow-line" data-delay="1">INFO@SWAGSTORE.CZ</span>
                <span class="flow-line" data-delay="2">+420 777 XXX XXX</span>
            </div>
        </div>
    </div>
</section>
<!-- Стрелка scroll: position:fixed, вне sticky-контейнера -->
<div id="scroll-hint">
    <span class="sh-label">scroll</span>
    <div class="sh-chevron"></div>
    <div class="sh-chevron"></div>
    <div class="sh-chevron"></div>
</div>
ENDHERO;
}

// render_landing_flow не нужна — текст встроен в hero
function render_landing_flow() { return ''; }

// Возвращает HTML CTA-футера: переливающаяся кнопка → crack → main.php
function render_landing_cta() {
    return <<<'ENDCTA'
<footer id="cta-section">
    <p class="cta-eyebrow" id="cta-eyebrow">— READY TO STEP UP —</p>
    <div class="cta-wrap" id="cta-wrap">
        <button class="cta-button" onclick="triggerCrack()">ENTER THE STORE</button>
    </div>
</footer>
ENDCTA;
}

// Возвращает HTML canvas-оверлея для анимации разбитого экрана
function render_landing_crack_overlay() {
    return <<<'ENDOVERLAY'
<div id="crack-overlay"><canvas id="crack-canvas"></canvas></div>
ENDOVERLAY;
}

// Весь JS лендинга: scroll-scrub видео, смена текстовых групп, scroll-hint, CTA reveal, crack
function get_landing_scripts() {
    return <<<'ENDSCRIPTS'
<script>
(function () {
    var heroSection = document.getElementById('hero-section');
    var video       = document.getElementById('hero-video');
    var hint        = document.getElementById('scroll-hint');
    var cta         = document.getElementById('cta-section');
    var groups      = [
        document.getElementById('tg-brand'),
        document.getElementById('tg-desc'),
        document.getElementById('tg-contacts')
    ];
    /* Зоны прогресса (0–1) когда появляется каждая группа */
    var zones       = [0.12, 0.38, 0.65];
    var activeGroup = -1;

    video.addEventListener('loadedmetadata', function () { video.pause(); });
    video.play().then(function () { video.pause(); }).catch(function () {});

    function hideGroup(g) {
        if (!g) return;
        g.querySelectorAll('.flow-line').forEach(function (l) {
            l.classList.remove('visible');
        });
    }
    function showGroup(g) {
        if (!g) return;
        g.querySelectorAll('.flow-line').forEach(function (l) {
            l.classList.add('visible');
        });
    }

    function onScroll() {
        var scrolled  = window.scrollY;
        var heroH     = heroSection.offsetHeight - window.innerHeight;
        if (heroH <= 0) return;
        var progress  = Math.min(Math.max(scrolled / heroH, 0), 1);

        /* ── скраб видео (только если загружено) ── */
        if (video.readyState >= 1 && video.duration) {
            video.currentTime = video.duration * progress;
        }

        /* ── смена текстовых групп по зонам ── */
        var zone = -1;
        for (var i = zones.length - 1; i >= 0; i--) {
            if (progress >= zones[i]) { zone = i; break; }
        }
        if (zone !== activeGroup) {
            if (activeGroup >= 0) hideGroup(groups[activeGroup]);
            activeGroup = zone;
            if (zone >= 0) showGroup(groups[zone]);
        }

        /* ── скрыть scroll-hint при входе в CTA ── */
        if (hint) {
            var ctaTop = cta.getBoundingClientRect().top;
            if (ctaTop < window.innerHeight) {
                hint.classList.add('hidden');
            } else {
                hint.classList.remove('hidden');
            }
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    /* Запускаем один раз сразу — для корректного состояния при загрузке с позиции */
    onScroll();

    /* ── CTA reveal через IntersectionObserver ── */
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (!e.isIntersecting) return;
            document.getElementById('cta-eyebrow').classList.add('visible');
            setTimeout(function () {
                document.getElementById('cta-wrap').classList.add('visible');
            }, 300);
        });
    }, { threshold: 0.1 });
    io.observe(cta);
})();

/* ─────────────────────────────────────────────────────────────
   4. CRACK / SHATTER TRANSITION
   Вызывается по клику на кнопку CTA.
   Последовательность: шейк страницы → полупрозрачный оверлей →
   покадровая отрисовка трещин → центральное свечение →
   плавное затемнение → переход на main.php
───────────────────────────────────────────────────────────── */
function triggerCrack() {
    const overlay = document.getElementById('crack-overlay');
    const canvas  = document.getElementById('crack-canvas');
    const ctx     = canvas.getContext('2d');

    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;

    overlay.style.display       = 'block';
    overlay.style.pointerEvents = 'all';

    // Шейк страницы перед ударом
    document.body.style.animation = 'page-shake 0.38s ease';

    setTimeout(function () {
        document.body.style.animation = '';

        // Мгновенный белый флеш — имитация удара
        ctx.fillStyle = 'rgba(255,255,255,0.15)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Тёмный полупрозрачный фон
        ctx.fillStyle = 'rgba(0,0,0,0.82)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        var lines = _generateCrackLines(canvas.width / 2, canvas.height / 2);
        _animateCracks(ctx, canvas, lines, function () {
            // Плавное затемнение → редирект
            var alpha = 0.82;
            var fade  = setInterval(function () {
                alpha = Math.min(alpha + 0.055, 1);
                ctx.fillStyle = 'rgba(0,0,0,' + alpha + ')';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                if (alpha >= 1) { clearInterval(fade); window.location.href = 'main.php'; }
            }, 18);
        });
    }, 380);
}

/* Генерирует массив линий трещин от центральной точки (cx, cy) */
function _generateCrackLines(cx, cy) {
    var lines   = [];
    var numMain = 6 + Math.floor(Math.random() * 4);
    for (var i = 0; i < numMain; i++) {
        var angle = (i / numMain) * Math.PI * 2 + (Math.random() - 0.5) * 0.5;
        var len   = 230 + Math.random() * 190;
        _addBranch(lines, cx, cy, angle, len, 0, 5);
    }
    return lines;
}

/* Рекурсивно добавляет ветки трещин в массив lines */
function _addBranch(lines, x, y, angle, len, depth, maxDepth) {
    if (depth >= maxDepth || len < 12) return;
    var a  = angle + (Math.random() - 0.5) * 0.5;
    var ex = x + Math.cos(a) * len;
    var ey = y + Math.sin(a) * len;
    lines.push({ x1: x, y1: y, x2: ex, y2: ey, depth: depth });
    var t  = 0.35 + Math.random() * 0.45;
    var bx = x + Math.cos(a) * len * t;
    var by = y + Math.sin(a) * len * t;
    _addBranch(lines, bx, by, a + 0.55 + Math.random() * 0.4, len * 0.55, depth + 1, maxDepth);
    _addBranch(lines, bx, by, a - 0.55 - Math.random() * 0.4, len * 0.45, depth + 1, maxDepth);
}

/* Покадрово рисует трещины; по завершению вызывает onComplete */
function _animateCracks(ctx, canvas, lines, onComplete) {
    ctx.shadowColor = '#ccff00';
    ctx.shadowBlur  = 6;
    var drawn    = 0;
    var total    = lines.length;
    var perFrame = Math.max(1, Math.ceil(total / 20));

    function drawFrame() {
        var end = Math.min(drawn + perFrame, total);
        for (var i = drawn; i < end; i++) {
            var l = lines[i];
            ctx.strokeStyle = l.depth === 0 ? '#ffffff' : ('rgba(255,255,255,' + (0.9 - l.depth * 0.15) + ')');
            ctx.lineWidth   = Math.max(0.4, 1.8 - l.depth * 0.3);
            ctx.beginPath();
            ctx.moveTo(l.x1, l.y1);
            ctx.lineTo(l.x2, l.y2);
            ctx.stroke();
        }
        drawn = end;
        if (drawn < total) {
            requestAnimationFrame(drawFrame);
        } else {
            // Центральное кислотное свечение
            var grd = ctx.createRadialGradient(
                canvas.width / 2, canvas.height / 2, 0,
                canvas.width / 2, canvas.height / 2, 200
            );
            grd.addColorStop(0,   'rgba(204,255,0,0.5)');
            grd.addColorStop(0.6, 'rgba(204,255,0,0.08)');
            grd.addColorStop(1,   'rgba(0,0,0,0)');
            ctx.fillStyle = grd;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            setTimeout(onComplete, 480);
        }
    }
    requestAnimationFrame(drawFrame);
}
</script>
ENDSCRIPTS;
}

?>