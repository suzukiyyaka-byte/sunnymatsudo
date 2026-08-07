<?php
// 送信ステータスの初期化（GETパラメータ等で判定）
$message_sent = isset($_GET['sent']) && $_GET['sent'] === '1';
$error_message = '';

// 送信ボタンが押された場合の処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ==========================================
    // 【管理者設定】通知を受けるメールアドレスを設定してください
    // ==========================================
    $to_email = "suzuki.yuji@yaka.co.jp"; 

    // 入力データの取得とサニタイズ
    $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8')) : '';
    $tel = isset($_POST['tel']) ? trim(htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8')) : '';
    $contents = isset($_POST['content']) && is_array($_POST['content']) ? $_POST['content'] : array();
    $message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8')) : '';

    // チェックボックスの配列を文字列に結合
    $selected_contents = !empty($contents) ? implode(" / ", array_map('htmlspecialchars', $contents)) : '未選択';

    $is_ajax = !empty($_POST['ajax']);

    // 必須項目のチェック
    if (!empty($name) && !empty($tel)) {
        // メール言語と文字コードを UTF-8 (uni) で統一
        mb_language("uni");
        mb_internal_encoding("UTF-8");

        // 件名と本文の構築
        $subject = "【サニープレイス松戸・サニーパーク松戸】WEBサイトよりお問い合わせ";
        
        $body = "サニープレイス松戸・サニーパーク松戸 WEBサイトよりお問い合わせがありました。\n\n";
        $body .= "--------------------------------------------------\n";
        $body .= "■ お名前： " . $name . "\n";
        $body .= "■ 電話番号： " . $tel . "\n";
        $body .= "■ お問い合わせ内容： " . $selected_contents . "\n";
        $body .= "■ 備考・ご質問：\n" . ($message ?: 'なし') . "\n";
        $body .= "--------------------------------------------------\n";
        $body .= "送信日時: " . date("Y-m-d H:i:s") . "\n";
        $body .= "送信元IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

        // ヘッダーの設定
        $headers = array(
            'From: info@sunnymatsudo.net',
            'Reply-To: info@sunnymatsudo.net',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'MIME-Version: 1.0',
            'X-Mailer: PHP/' . phpversion()
        );

        // メール送信処理
        if (mb_send_mail($to_email, $subject, $body, implode("\r\n", $headers), "-f info@sunnymatsudo.net")) {
            if ($is_ajax) {
                header('Content-Type: application/json; charset=utf-8');
                $res = array();
                $res['success'] = true;
                echo json_encode($res);
                exit;
            }
            // PRG (Post/Redirect/Get) パターン：再読み込み時の二重送信・警告を防止
            header("Location: index.php?sent=1#contact");
            exit;
        } else {
            $error_message = 'メールの送信に失敗しました。時間をおいて再度お試しいただくか、お電話にてお問い合わせください。';
        }
    } else {
        $error_message = '必須項目（お名前・電話番号）をご入力ください。';
    }

    if ($is_ajax && !empty($error_message)) {
        header('Content-Type: application/json; charset=utf-8');
        $res = array();
        $res['success'] = false;
        $res['message'] = $error_message;
        echo json_encode($res);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>【公式】サニープレイス松戸・サニーパーク松戸｜陽光満ちる2つの都市型洋風霊園</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;600;700&family=Zen+Kaku+Gothic+New:wght@400;500;700;900&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: {
                            50: '#f2f8f5',
                            100: '#e1f0e8',
                            600: '#2d6a4f',
                            800: '#1b4d3e',
                            900: '#11342a',
                        },
                        gold: {
                            100: '#fdf8ec',
                            400: '#e2c070',
                            500: '#d4af37',
                            600: '#b89228',
                        },
                        sunshine: '#fffdf0',
                    },
                    fontFamily: {
                        serif: ['"Noto Serif JP"', 'serif'],
                        sans: ['"Zen Kaku Gothic New"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Zen Kaku Gothic New', sans-serif;
            background-color: #ffffff;
            color: #2c3e50;
            font-size: 18px; /* 高齢者・スマホ配慮の基本フォントサイズ */
            line-height: 1.8;
            overflow-x: hidden;
        }

        /* 陽光と光の拡散エフェクト */
        .sunbeam-bg {
            background: radial-gradient(circle at 80% 10%, rgba(255,248,220,0.8) 0%, rgba(255,255,255,0) 60%),
                        radial-gradient(circle at 10% 50%, rgba(225,240,232,0.5) 0%, rgba(255,255,255,0) 50%);
        }

        /* アスペクト比を保ち見切れを防ぐ画像スタイル */
        .img-fit-contain {
            width: 100%;
            height: auto;
            max-width: 100%;
            object-fit: contain;
            display: block;
        }

        /* 52.jpg用 ゆっくりズーム（Ken Burns）エフェクト */
        .zoom-container {
            position: relative;
            width: 100%;
            /* 52.jpgの元画像比率（4:3）に合わせて縦幅を広げ、全貌が表示されるアスペクト比に設定 */
            aspect-ratio: 4 / 3;
            overflow: hidden;
        }
        .zoom-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            animation: slowZoomEffect 20s infinite alternate ease-in-out;
            will-change: transform;
        }

        /* ゆっくり拡大・縮小を繰り替えるアニメーション */
        @keyframes slowZoomEffect {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(1.15);
            }
        }

        /* 螺旋的カードアニメーション */
        .spiral-container {
            perspective: 1200px;
        }
        .spiral-card {
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            transform-style: preserve-3d;
        }
        
        /* ガラスモフィズム効果 */
        .glass-panel {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        /* 視認性の高い太線見出しアンダーライン */
        .heading-accent {
            position: relative;
            display: inline-block;
        }
        .heading-accent::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #d4af37, #2d6a4f);
            border-radius: 2px;
        }

        /* アニメーション用パルス効果 */
        @keyframes subtle-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .animate-pulse-subtle {
            animation: subtle-pulse 3s infinite ease-in-out;
        }

        /* タップしやすい操作ボタンの最低サイズ確保 */
        .touch-btn {
            min-height: 54px;
        }
    </style>
</head>
<body class="antialiased selection:bg-gold-500 selection:text-white">

    <!-- 背景に漂う光の粒子と風に舞う新緑の葉キャンバス -->
    <canvas id="sunlightCanvas" class="fixed inset-0 pointer-events-none z-20"></canvas>

    <!-- ヘッダー -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-emerald-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="bg-forest-800 text-white text-xs font-bold px-2 py-1 rounded">松戸市</span>
                <h1 class="text-lg md:text-xl font-bold font-serif text-forest-800 tracking-wide">
                    サニープレイス松戸・サニーパーク松戸
                </h1>
            </div>
            <a href="tel:0120183289" class="hidden md:flex items-center bg-forest-800 hover:bg-forest-900 text-white font-bold px-4 py-2 rounded-full text-base transition shadow">
                <i class="fa-solid fa-phone mr-2 text-gold-400"></i>0120-18-3289
            </a>
        </div>
    </header>

    <main class="relative z-10">

        <!-- 1. トップビジュアル -->
        <section class="relative bg-gradient-to-b from-sunshine via-white to-forest-50 py-8 md:py-16 sunbeam-bg overflow-hidden">
            <div class="max-w-5xl mx-auto px-4">
                
                <!-- キャッチコピー -->
                <div class="text-center mb-6">
                    <span class="inline-block bg-gold-100 text-forest-800 font-bold px-4 py-1.5 rounded-full text-base md:text-lg mb-3 border border-gold-400">
                        <i class="fa-solid fa-sun text-gold-500 mr-2"></i>宗教・宗派不問／安心の管理体制
                    </span>
                    <h2 class="text-2xl md:text-4xl lg:text-5xl font-bold font-serif text-forest-900 leading-tight tracking-wide mb-3">
                        陽光満ちる、<br class="block md:hidden">2つの都市型洋風霊園
                    </h2>
                    <p class="text-base md:text-xl text-gray-700 font-medium">
                        JR東松戸駅・JR市川大野駅からお車で約5分の好アクセス
                    </p>
                </div>

                <!-- メイン画像 (52.jpg ゆっくりズーム効果) -->
                <div class="-mx-4 sm:mx-auto sm:max-w-2xl mb-8">
                    <div class="zoom-container sm:rounded-2xl overflow-hidden sm:shadow-xl sm:border-4 sm:border-white bg-gray-100">
                        <img src="52.jpg" alt="サニープレイス松戸・サニーパーク松戸 全景" class="zoom-img" onerror="this.onerror=null; this.src='https://placehold.co/1000x800/1b4d3e/ffffff?text=全景(52.jpg)';">
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-3 md:p-4 text-white text-center z-10">
                            <p class="text-xs md:text-base font-bold">陽光あふれる澄み渡る青空と安らぎの全景【サニープレイス松戸・サニーパーク松戸】</p>
                        </div>
                    </div>
                </div>

                <!-- 家族共有ボタンエリア (LINE / SMS) -->
                <div class="bg-emerald-50/90 rounded-2xl p-4 md:p-6 text-center border border-emerald-200 shadow-sm max-w-2xl mx-auto">
                    <p class="text-base md:text-lg font-bold text-forest-900 mb-3">
                        <i class="fa-solid fa-users mr-2 text-forest-600"></i>ご家族・ご親族へこのページを共有する
                    </p>
                    <div class="grid grid-cols-2 gap-3 max-w-md mx-auto">
                        <button onclick="shareLINE()" class="touch-btn bg-[#06C755] hover:opacity-90 text-white font-bold px-4 py-3 rounded-xl shadow flex items-center justify-center space-x-2 text-base md:text-lg transition">
                            <i class="fa-brands fa-line text-2xl"></i>
                            <span>LINEで送る</span>
                        </button>
                        <button onclick="shareSMS()" class="touch-btn bg-sky-600 hover:bg-sky-700 text-white font-bold px-4 py-3 rounded-xl shadow flex items-center justify-center space-x-2 text-base md:text-lg transition">
                            <i class="fa-solid fa-comment-sms text-xl"></i>
                            <span>SMSで送る</span>
                        </button>
                    </div>
                </div>

            </div>
        </section>


        <!-- 2. 現地案内会開催中 -->
        <section class="py-10 bg-white border-y border-gold-400/30">
            <div class="max-w-4xl mx-auto px-4">
                
                <div class="bg-gradient-to-br from-gold-100 via-white to-gold-50 border-2 border-gold-500 rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-gold-500 text-white text-xs md:text-sm font-bold px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider">
                        ご来園特典
                    </div>
                    
                    <div class="text-center mb-6">
                        <span class="inline-block bg-red-600 text-white font-bold text-sm md:text-base px-3 py-1 rounded-full mb-2 animate-bounce">
                            毎日開催中（雨天実施）
                        </span>
                        <h3 class="text-2xl md:text-3xl font-bold font-serif text-forest-900">
                            現地案内会 開催案内
                        </h3>
                        <p class="text-lg md:text-xl text-forest-800 font-bold mt-1">
                            受付時間：9：00 ～ 17：00
                        </p>
                    </div>

                    <!-- 提示特典デジタルカード -->
                    <div class="bg-white rounded-2xl p-5 border-2 border-dashed border-forest-600 shadow-inner text-center my-4">
                        <p class="text-sm md:text-base text-gray-600 font-bold mb-1">【ご来園時のお願い】</p>
                        <p class="text-base md:text-xl font-bold text-forest-900 leading-snug">
                            ご来園の際は霊園ご案内所にお寄りいただき、<br>
                            <span class="text-red-600 underline decoration-gold-500 decoration-4">このページのトップ画面</span> を現地係員にご提示ください。
                        </p>
                        <p class="text-sm md:text-base text-gray-700 mt-2">
                            スタッフが心を込めて園内をご案内いたします。
                        </p>
                    </div>

                    <div class="text-center mt-6">
                        <a href="#access" class="inline-flex items-center justify-center touch-btn bg-forest-800 hover:bg-forest-900 text-white font-bold px-8 py-4 rounded-full text-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-location-dot text-gold-400 mr-2 text-xl"></i>現地へのアクセス方法を見る
                        </a>
                    </div>
                </div>

            </div>
        </section>


        <!-- 3. 2つの霊園 螺旋的インタラクティブ紹介 -->
        <section class="py-12 md:py-20 bg-slate-50 relative">
            <div class="max-w-6xl mx-auto px-4">
                
                <div class="text-center mb-10">
                    <p class="text-gold-600 font-bold text-base md:text-lg">隣接するふたつの霊園、ふたつの個性</p>
                    <h2 class="text-2xl md:text-4xl font-bold font-serif text-forest-900 heading-accent mb-4">
                        サニープレイス松戸・サニーパーク松戸
                    </h2>
                    <p class="text-base md:text-lg text-gray-600 mt-4 max-w-2xl mx-auto">
                        螺旋を描くように互いを引き立て合う2つの霊園。それぞれの特徴や魅力をお確かめください。
                    </p>
                </div>

                <!-- スイッチタブ切り替え (スマホ・高齢者向け大サイズ) -->
                <div class="flex justify-center mb-8">
                    <div class="bg-gray-200 p-1.5 rounded-2xl flex flex-wrap md:flex-nowrap gap-2 max-w-xl w-full shadow-inner">
                        <button id="btn-both" onclick="switchCemetery('both')" class="flex-1 touch-btn py-3 px-3 rounded-xl font-bold text-sm md:text-base transition duration-300 bg-emerald-700 text-white shadow-md">
                            両方を比較
                        </button>
                        <button id="btn-place" onclick="switchCemetery('place')" class="flex-1 touch-btn py-3 px-3 rounded-xl font-bold text-sm md:text-base transition duration-300 text-gray-700 hover:text-forest-800">
                            サニープレイス松戸
                        </button>
                        <button id="btn-park" onclick="switchCemetery('park')" class="flex-1 touch-btn py-3 px-3 rounded-xl font-bold text-sm md:text-base transition duration-300 text-gray-700 hover:text-forest-800">
                            サニーパーク松戸
                        </button>
                    </div>
                </div>

                <!-- 霊園コンテンツ表示エリア -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    
                    <!-- 左カード: サニープレイス松戸 -->
                    <div id="card-place" class="bg-white rounded-3xl p-6 md:p-8 shadow-xl border-2 border-forest-600 transition-all duration-500">
                        <div class="flex items-center space-x-4 mb-4 pb-4 border-b border-gray-100">
                            <img src="08.jpg" alt="サニープレイス松戸 ロゴ" class="h-12 md:h-16 object-contain" onerror="this.onerror=null; this.src='https://placehold.co/200x80/2d6a4f/ffffff?text=Place+Logo(08.jpg)';">
                            <div>
                                <span class="bg-emerald-100 text-forest-800 text-xs font-bold px-2.5 py-1 rounded-full">多様な供養スタイル</span>
                                <h3 class="text-xl md:text-2xl font-bold font-serif text-forest-900 mt-1">サニープレイス松戸</h3>
                            </div>
                        </div>

                        <p class="text-base text-gray-700 mb-6">
                            ペットと一緒に眠れる区画や芝墓地、樹木葬など、現代のライフスタイルに寄り添った多彩なお墓をご用意しています。
                        </p>

                        <!-- 代表写真リスト (アスペクト比重視) -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-bold text-forest-800 mb-1"><i class="fa-solid fa-image text-gold-500 mr-1"></i>一般区画</p>
                                <img src="32.jpg" alt="サニープレイス松戸 一般区" class="img-fit-contain rounded-xl border border-gray-200 shadow-sm" onerror="this.onerror=null; this.src='https://placehold.co/600x400/2d6a4f/ffffff?text=%E4%B8%80%E8%8A%2C%E5%8C%BA(32.jpg)';">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-forest-800 mb-1"><i class="fa-solid fa-image text-gold-500 mr-1"></i>芝墓地</p>
                                <img src="29.png" alt="サニープレイス松戸 芝墓地" class="img-fit-contain rounded-xl border border-gray-200 shadow-sm" onerror="this.onerror=null; this.src='https://placehold.co/600x400/2d6a4f/ffffff?text=%E8%8A%9D%E5%A2%93%E5%9C%B0(29.png)';">
                            </div>
                        </div>
                    </div>

                    <!-- 右カード: サニーパーク松戸 -->
                    <div id="card-park" class="bg-white rounded-3xl p-6 md:p-8 shadow-xl border-2 border-gold-400 transition-all duration-500">
                        <div class="flex items-center space-x-4 mb-4 pb-4 border-b border-gray-100">
                            <img src="07.jpg" alt="サニーパーク松戸 ロゴ" class="h-12 md:h-16 object-contain" onerror="this.onerror=null; this.src='https://placehold.co/200x80/b89228/ffffff?text=Park+Logo(07.jpg)';">
                            <div>
                                <span class="bg-gold-100 text-gold-800 text-xs font-bold px-2.5 py-1 rounded-full">安心の限定セット</span>
                                <h3 class="text-xl md:text-2xl font-bold font-serif text-forest-900 mt-1">サニーパーク松戸</h3>
                            </div>
                        </div>

                        <p class="text-base text-gray-700 mb-6">
                            明るい陽光が注ぐ心地よい公園型霊園。お求めやすい特別限定セット墓石が人気です。
                        </p>

                        <!-- 代表写真リスト (アスペクト比重視) -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-bold text-forest-800 mb-1"><i class="fa-solid fa-image text-gold-500 mr-1"></i>一般区画</p>
                                <img src="31.jpg" alt="サニーパーク松戸 一般区" class="img-fit-contain rounded-xl border border-gray-200 shadow-sm" onerror="this.onerror=null; this.src='https://placehold.co/600x400/b89228/ffffff?text=%E4%B8%80%E8%8A%2C%E5%8C%BA(31.jpg)';">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-forest-800 mb-1"><i class="fa-solid fa-image text-gold-500 mr-1"></i>0.6㎡ 永代使用料案内</p>
                                <img src="09.jpg" alt="0.6㎡永代使用料" class="img-fit-contain rounded-xl border border-gray-200 shadow-sm" onerror="this.onerror=null; this.src='https://placehold.co/600x400/b89228/ffffff?text=0.6m2%E2%80%B3%E6%B0%B8%E4%BD%A3%E4%BD%BF%E7%94%A8%E6%96%99(09.jpg)';">
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>


        <!-- 4. 特別限定セット墓石 -->
        <section class="py-12 bg-white">
            <div class="max-w-4xl mx-auto px-4">
                
                <div class="bg-gradient-to-r from-forest-900 to-forest-800 text-white rounded-3xl p-6 md:p-10 shadow-2xl relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-gold-500/20 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="text-center mb-6">
                        <span class="inline-block bg-gold-500 text-forest-900 font-bold px-3 py-1 rounded text-sm md:text-base mb-2">
                            サニーパーク松戸 おすすめ区画
                        </span>
                        <h3 class="text-2xl md:text-3xl font-bold font-serif text-gold-400">
                            0.6㎡ 特別限定セット墓石
                        </h3>
                        <p class="text-base md:text-lg text-emerald-100 mt-1">
                            墓石と工事代がセットになった安心のお求めやすいプラン
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-4 md:p-6 text-gray-800 shadow-lg">
                        <img src="05.jpg" alt="0.6㎡特別限定セット墓石" class="img-fit-contain mx-auto rounded-lg mb-4" onerror="this.onerror=null; this.src='https://placehold.co/800x500/1b4d3e/ffffff?text=0.6m2%E7%89%B9%E5%88%A5%E9%99%90%E5%AE%9A%E3%82%BB%E3%83%83%E3%83%8B%E5%A2%93%E7%9F%B3(05.jpg)';">
                        
                        <div class="text-center border-t border-gray-200 pt-4">
                            <p class="text-sm md:text-base text-gray-600 font-bold">価格等の詳細は現地ご案内所またはお電話にてお気軽にお尋ねください</p>
                            <a href="tel:0120183289" class="inline-flex items-center justify-center mt-3 touch-btn bg-gold-500 hover:bg-gold-600 text-forest-900 font-bold px-6 py-3 rounded-full text-base md:text-lg shadow transition">
                                <i class="fa-solid fa-phone mr-2"></i>お電話で相談する（無料）
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </section>


        <!-- 5. 価格表 -->
        <section class="py-12 md:py-16 bg-emerald-50/50">
            <div class="max-w-5xl mx-auto px-4">
                
                <div class="text-center mb-8">
                    <h2 class="text-2xl md:text-4xl font-bold font-serif text-forest-900 heading-accent mb-3">
                        区画価格表
                    </h2>
                    <p class="text-base md:text-lg text-gray-700 mt-3">
                        ご予算やご要望に応じた多彩なプランをご用意しております
                    </p>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-3xl shadow-xl border border-emerald-100">
                    <!-- 16.jpg 価格表画像 -->
                    <img src="16.jpg" alt="サニープレイス松戸・サニーパーク松戸 区画価格表" class="img-fit-contain mx-auto rounded-xl" onerror="this.onerror=null; this.src='https://placehold.co/900x600/ffffff/1b4d3e?text=%E5%8C%BA%E7%94%BB%E4%BE%A1%E6%A0%BC%E8%A1%A8(16.jpg)';">
                </div>

            </div>
        </section>


        <!-- 6. 霊園の特徴 (48.jpg 表示) -->
        <section class="py-12 md:py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4">
                
                <div class="text-center mb-10">
                    <h2 class="text-2xl md:text-4xl font-bold font-serif text-forest-900 heading-accent mb-3">
                        霊園の充実した特徴・施設
                    </h2>
                    <p class="text-base md:text-lg text-gray-600 mt-3">
                        愛するペットと一緒に眠れる墓所や樹木葬など、多彩なニーズにお応えします
                    </p>
                </div>

                <!-- 48.jpg 表示（アスペクト比を維持して全体を表示） -->
                <div class="mb-10 max-w-4xl mx-auto">
                    <img src="48.jpg" alt="霊園の特徴・施設" class="img-fit-contain rounded-2xl shadow-lg border border-gray-200" onerror="this.onerror=null; this.src='https://placehold.co/1000x562/2d6a4f/ffffff?text=%E7%89%B9%E5%B4%8B%E3%83%BB%E6%96%BD%E8%A8%AD(48.jpg)';">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- 1. ペット共葬墓 -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 shadow-md flex flex-col justify-between">
                        <div>
                            <div class="bg-forest-800 text-white font-bold text-center py-2 rounded-xl mb-4 text-lg">
                                ペット共葬墓（サニープレイス松戸）
                            </div>
                            <img src="02.jpg" alt="ペット共葬墓" class="img-fit-contain rounded-xl mb-4" onerror="this.onerror=null; this.src='https://placehold.co/500x350/2d6a4f/ffffff?text=%E3%83%9A%E3%83%83%E3%83%8B%E5%85%B1%E8%91%AC%E5%A2%93(02.jpg)';">
                            <p class="text-base text-gray-700 leading-relaxed">
                                大切な家族の一員であるペットと一緒に埋葬できる人気の区画です。
                            </p>
                        </div>
                    </div>

                    <!-- 2. 樹木葬 -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 shadow-md flex flex-col justify-between">
                        <div>
                            <div class="bg-forest-800 text-white font-bold text-center py-2 rounded-xl mb-4 text-lg">
                                樹木葬
                            </div>
                            <img src="27.jpg" alt="樹木葬" class="img-fit-contain rounded-xl mb-4" onerror="this.onerror=null; this.src='https://placehold.co/500x350/2d6a4f/ffffff?text=%E6%A8%B9%E6%9C%A8%E8%91%AC(27.jpg)';">
                            <p class="text-base text-gray-700 leading-relaxed">
                                緑と花々に囲まれた自然豊かな樹木葬エリア。安らかな眠りをお約束します。
                            </p>
                        </div>
                    </div>

                    <!-- 3. ペット永代供養墓 -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 shadow-md flex flex-col justify-between">
                        <div>
                            <div class="bg-forest-800 text-white font-bold text-center py-2 rounded-xl mb-4 text-lg">
                                ペット永代供養墓
                            </div>
                            <img src="26.jpg" alt="ペット永代供養墓" class="img-fit-contain rounded-xl mb-4" onerror="this.onerror=null; this.src='https://placehold.co/500x350/2d6a4f/ffffff?text=%E3%83%9A%E3%83%83%E3%83%8B%EB%B0%B8%E4%BB%A3%E4%BE%9B%E9%A4%8A%E5%A2%93(26.jpg)';">
                            <p class="text-base text-gray-700 leading-relaxed">
                                ペット専用の永代供養墓も完備。手厚くお弔いいたします。
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </section>


        <!-- 7. あんしん永代供養付き墓託制度 -->
        <section class="py-12 bg-gradient-to-b from-emerald-50 to-white">
            <div class="max-w-5xl mx-auto px-4">
                
                <div class="bg-white rounded-3xl p-6 md:p-10 shadow-xl border-2 border-forest-600">
                    <div class="text-center mb-6">
                        <span class="bg-gold-100 text-forest-900 font-bold px-4 py-1.5 rounded-full text-base border border-gold-400">
                            継承者がいなくても安心
                        </span>
                        <h3 class="text-2xl md:text-3xl font-bold font-serif text-forest-900 mt-3">
                            あんしん永代供養付き 墓託制度
                        </h3>
                        <p class="text-lg md:text-xl text-red-600 font-bold mt-2">
                            お墓の継承者がいなくても安心してご建墓いただけます！
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
                        <img src="03.jpg" alt="墓託制度 案内1" class="img-fit-contain rounded-xl shadow-sm border border-gray-200" onerror="this.onerror=null; this.src='https://placehold.co/600x400/1b4d3e/ffffff?text=%E5%A2%93%E8%A8%97%E5%88%B6%E5%BA%A6(03.jpg)';">
                        <img src="14.jpg" alt="墓託制度 案内2" class="img-fit-contain rounded-xl shadow-sm border border-gray-200" onerror="this.onerror=null; this.src='https://placehold.co/600x400/1b4d3e/ffffff?text=%E5%A2%93%E8%A8%97%E5%88%B6%E5%BA%A6(14.jpg)';">
                    </div>

                    <p class="text-base md:text-lg text-gray-700 leading-relaxed text-center">
                        将来的にお墓を見る方がいなくなった場合でも、寺院が責任を持って永代にわたり供養・管理いたします。単身の方や後継者にお悩みの方もご安心ください。
                    </p>
                </div>

            </div>
        </section>


        <!-- 8. 改葬を検討している方へ -->
        <section class="py-12 bg-white">
            <div class="max-w-4xl mx-auto px-4">
                <div class="bg-slate-100 rounded-3xl p-6 md:p-8 border-l-8 border-gold-500 shadow-md">
                    <h3 class="text-xl md:text-2xl font-bold font-serif text-forest-900 mb-3 flex items-center">
                        <i class="fa-solid fa-arrows-rotate text-gold-600 mr-3"></i>お墓の移動・「改葬」をご検討の方へ
                    </h3>
                    <p class="text-base md:text-lg text-gray-700 leading-relaxed">
                        「遠方にあるお墓を近くに移動したい」「田舎のお墓を墓じまいして移転したい」といったご相談も承っております。手続きや手順についても専門スタッフが親身にお手伝いいたします。
                    </p>
                </div>
            </div>
        </section>


        <!-- 9. 安心の管理体制 -->
        <section class="py-12 bg-emerald-50/60">
            <div class="max-w-4xl mx-auto px-4">
                <div class="text-center mb-6">
                    <h3 class="text-2xl md:text-3xl font-bold font-serif text-forest-900 heading-accent">
                        安心の管理体制
                    </h3>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-lg flex flex-col md:flex-row items-center gap-6">
                    <div class="w-full md:w-1/2">
                        <img src="17.jpg" alt="管理事務所" class="img-fit-contain rounded-2xl shadow border border-gray-200" onerror="this.onerror=null; this.src='https://placehold.co/600x400/2d6a4f/ffffff?text=%E7%AE%A1%E7%90%86%E4%BA%8B%E5%8B%99%E6%89%80(17.jpg)';">
                    </div>
                    <div class="w-full md:w-1/2">
                        <h4 class="text-xl font-bold text-forest-800 mb-2">常駐スタッフによる行き届いた手入れ</h4>
                        <p class="text-base text-gray-700 leading-relaxed">
                            園内には管理事務所を設置。常時きれいに清掃・管理されており、いつお参りに来られても気持ちよく過ごしていただけます。水汲み場やバリアフリー設備も整っております。
                        </p>
                    </div>
                </div>
            </div>
        </section>


        <!-- 10. アクセス案内 -->
        <section id="access" class="py-12 md:py-20 bg-white">
            <div class="max-w-5xl mx-auto px-4">
                
                <div class="text-center mb-10">
                    <h2 class="text-2xl md:text-4xl font-bold font-serif text-forest-900 heading-accent mb-3">
                        交通・アクセス案内
                    </h2>
                    <p class="text-lg md:text-xl font-bold text-forest-800 mt-3">
                        JR東松戸駅・JR市川大野駅からお車で約5分
                    </p>
                    <p class="text-base text-gray-600">住所：千葉県松戸市高塚新田21-4 他</p>
                </div>

                <!-- 広域図・詳細地図 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-center font-bold text-forest-800 mb-2">【広域アクセスマップ】</p>
                        <img src="10.jpg" alt="広域図" class="img-fit-contain rounded-xl border border-gray-200 shadow" onerror="this.onerror=null; this.src='https://placehold.co/600x400/ffffff/1b4d3e?text=%E5%BA%83%E5%9F%9F%E5%9B%B3(10.jpg)';">
                    </div>
                    <div>
                        <p class="text-center font-bold text-forest-800 mb-2">【詳細アクセスマップ】</p>
                        <img src="12.jpg" alt="詳細地図" class="img-fit-contain rounded-xl border border-gray-200 shadow" onerror="this.onerror=null; this.src='https://placehold.co/600x400/ffffff/1b4d3e?text=%E8%A9%B3%E7%B4%B0%E5%9C%B0%E5%9B%B3(12.jpg)';">
                    </div>
                </div>

                <!-- 無料送迎バス情報 -->
                <div class="bg-amber-50 border-2 border-gold-400 rounded-3xl p-6 md:p-8 mb-10 shadow-md">
                    <div class="text-center mb-4">
                        <span class="bg-gold-500 text-forest-900 font-bold px-3 py-1 rounded text-sm md:text-base">
                            お車がない方も安心
                        </span>
                        <h3 class="text-xl md:text-2xl font-bold font-serif text-forest-900 mt-2">
                            無料送迎バスのご案内
                        </h3>
                        <p class="text-base text-gray-700 font-bold mt-1">
                            東松戸駅より運行（土曜・日曜・祝日および春秋お彼岸期間のみ）
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <img src="11.jpg" alt="無料送迎バス 案内1" class="img-fit-contain rounded-xl border border-amber-200" onerror="this.onerror=null; this.src='https://placehold.co/600x400/fffdf0/b89228?text=%E9%80%81%E8%BF%8E%E3%83%90%E3%82%B9(11.jpg)';">
                        <img src="13.jpg" alt="無料送迎バス 案内2" class="img-fit-contain rounded-xl border border-amber-200" onerror="this.onerror=null; this.src='https://placehold.co/600x400/fffdf0/b89228?text=%E9%80%81%E8%BF%8E%E3%83%90%E3%82%B9(13.jpg)';">
                    </div>
                </div>

                <!-- Google Maps iframe 埋め込み -->
                <div class="rounded-3xl overflow-hidden shadow-2xl border-2 border-forest-800 mb-6 bg-gray-100">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2082.0749414309503!2d139.950464073598!3d35.76362435500183!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x601883e3c1f512d3%3A0xf5f3eb73a3395d74!2z44K144OL44O844OX44Os44Kk44K55p2-5oi4IOaoueacqOiRrOODu-awuOS7o-S-m-mkiuWikw!5e1!3m2!1sja!2sjp!4v1784764098907!5m2!1sja!2sjp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>

                <!-- Google Maps ルート案内ボタン -->
                <div class="text-center">
                    <a href="https://maps.app.goo.gl/esUJZjvw62Anf71m6" target="_blank" rel="noopener" class="inline-flex items-center justify-center touch-btn bg-sky-600 hover:bg-sky-700 text-white font-bold px-8 py-4 rounded-full text-lg md:text-xl shadow-lg transition transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-diamond-turn-right text-gold-400 mr-2 text-2xl"></i>Googleマップでルート案内を見る
                    </a>
                </div>

            </div>
        </section>


        <!-- 11. 天気予報リンク -->
        <section class="py-8 bg-slate-100">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <div class="bg-white rounded-2xl p-5 shadow border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-left">
                        <p class="text-sm font-bold text-gray-500">ご見学前の事前チェック</p>
                        <p class="text-lg font-bold text-forest-900"><i class="fa-solid fa-cloud-sun text-gold-500 mr-2"></i>松戸市高塚新田のピンポイント天気</p>
                    </div>
                    <a href="https://weathernews.jp/onebox/tenki/chiba/12207/" target="_blank" rel="noopener" class="touch-btn inline-flex items-center bg-forest-800 hover:bg-forest-900 text-white font-bold px-6 py-2.5 rounded-xl text-base shadow transition">
                        ウェザーニュースで見る <i class="fa-solid fa-arrow-up-right-from-square ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
        </section>


        <!-- 12. よくある質問 -->
        <section class="py-12 md:py-20 bg-white">
            <div class="max-w-4xl mx-auto px-4">
                
                <div class="text-center mb-10">
                    <h2 class="text-2xl md:text-4xl font-bold font-serif text-forest-900 heading-accent mb-3">
                        よくある質問（FAQ）
                    </h2>
                </div>

                <div class="space-y-4">
                    
                    <details class="bg-slate-50 rounded-2xl p-5 border border-gray-200 cursor-pointer group">
                        <summary class="font-bold text-lg md:text-xl text-forest-900 flex justify-between items-center list-none">
                            <span><i class="fa-solid fa-circle-question text-gold-500 mr-2"></i>宗教や宗旨・宗派の制限はありますか？</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-base text-gray-700 mt-4 pt-3 border-t border-gray-200 leading-relaxed">
                            宗教・宗旨・宗派を問わず、どなたでもお申し込みいただけます。無宗教の方も安心してお求めいただけます。
                        </p>
                    </details>

                    <details class="bg-slate-50 rounded-2xl p-5 border border-gray-200 cursor-pointer group">
                        <summary class="font-bold text-lg md:text-xl text-forest-900 flex justify-between items-center list-none">
                            <span><i class="fa-solid fa-circle-question text-gold-500 mr-2"></i>後継者がいなくても墓地を購入できますか？</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-base text-gray-700 mt-4 pt-3 border-t border-gray-200 leading-relaxed">
                            はい、「あんしん永代供養付き墓託制度」をご用意しておりますので、後継者のおられない方も安心してご建墓いただけます。
                        </p>
                    </details>

                    <details class="bg-slate-50 rounded-2xl p-5 border border-gray-200 cursor-pointer group">
                        <summary class="font-bold text-lg md:text-xl text-forest-900 flex justify-between items-center list-none">
                            <span><i class="fa-solid fa-circle-question text-gold-500 mr-2"></i>ペットと一緒に埋葬できる区画はありますか？</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-base text-gray-700 mt-4 pt-3 border-t border-gray-200 leading-relaxed">
                            「サニープレイス松戸」内にペット共葬区画を設けております。大切な家族であるペットと一緒に眠ることができます。
                        </p>
                    </details>

                    <details class="bg-slate-50 rounded-2xl p-5 border border-gray-200 cursor-pointer group">
                        <summary class="font-bold text-lg md:text-xl text-forest-900 flex justify-between items-center list-none">
                            <span><i class="fa-solid fa-circle-question text-gold-500 mr-2"></i>雨の日の現地見学は可能ですか？</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-base text-gray-700 mt-4 pt-3 border-t border-gray-200 leading-relaxed">
                            はい、現地案内会は雨天でも実施しております。霊園案内所にて傘の貸出等も行っておりますのでお気軽にお越しください。
                        </p>
                    </details>

                </div>

            </div>
        </section>


        <!-- 13. お問い合わせフォーム -->
        <section id="contact" class="py-12 md:py-20 bg-emerald-50">
            <div class="max-w-3xl mx-auto px-4">
                
                <div id="formContainer" class="bg-white rounded-3xl p-6 md:p-10 shadow-2xl border border-emerald-100">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold font-serif text-forest-900 mb-2">
                            ご見学予約・資料請求フォーム
                        </h2>
                        <p class="text-base text-gray-600">
                            必要事項をご入力の上、「送信する」ボタンを押してください。
                        </p>
                    </div>

                    <?php if ($message_sent): ?>
                        <!-- 送信完了時メッセージ -->
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-emerald-100 text-forest-800 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-forest-900 mb-2">送信が完了いたしました</h3>
                            <p class="text-base text-gray-700">
                                お問い合わせありがとうございます。担当者より折り返しご連絡させていただきます。
                            </p>
                        </div>
                    <?php else: ?>

                        <?php if (!empty($error_message)): ?>
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-center font-bold">
                                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>

                        <form id="contactForm" method="POST" action="#contact" class="space-y-6">
                            
                            <div>
                                <label class="block font-bold text-forest-900 mb-2">お名前 <span class="text-red-600 text-sm">※必須</span></label>
                                <input type="text" name="name" required placeholder="例）山田 太郎" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-forest-600 text-base" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>

                            <div>
                                <label class="block font-bold text-forest-900 mb-2">電話番号 <span class="text-red-600 text-sm">※必須</span></label>
                                <input type="tel" name="tel" required placeholder="例）090-1234-5678" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-forest-600 text-base" value="<?php echo isset($_POST['tel']) ? htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>

                            <div>
                                <label class="block font-bold text-forest-900 mb-2">ご希望のお問い合わせ内容</label>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-base cursor-pointer">
                                        <input type="checkbox" name="content[]" value="現地見学のご予約" class="w-5 h-5 text-forest-800 rounded">
                                        <span>現地見学のご予約</span>
                                    </label>
                                    <label class="flex items-center space-x-2 text-base cursor-pointer">
                                        <input type="checkbox" name="content[]" value="詳しい資料のご請求" class="w-5 h-5 text-forest-800 rounded">
                                        <span>詳しい資料のご請求</span>
                                    </label>
                                    <label class="flex items-center space-x-2 text-base cursor-pointer">
                                        <input type="checkbox" name="content[]" value="ご相談・その他質問" class="w-5 h-5 text-forest-800 rounded">
                                        <span>ご相談・その他質問</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-forest-900 mb-2">備考・ご質問など</label>
                                <textarea name="message" rows="4" placeholder="ご希望の日時やご質問がございましたらご記入ください" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-forest-600 text-base"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                            </div>

                            <div class="text-center pt-2">
                                <button type="submit" class="touch-btn w-full md:w-auto bg-forest-800 hover:bg-forest-900 text-white font-bold px-12 py-4 rounded-full text-lg shadow-xl transition transform hover:-translate-y-0.5">
                                    送信する
                                </button>
                            </div>

                        </form>
                    <?php endif; ?>
                </div>

            </div>
        </section>


        <!-- 14. 霊園概要 & 経営主体 -->
        <section class="py-12 bg-white border-t border-gray-200">
            <div class="max-w-5xl mx-auto px-4">
                
                <h2 class="text-xl md:text-2xl font-bold font-serif text-forest-900 mb-6 text-center">
                    霊園概要
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base text-gray-700 mb-8">
                    
                    <div class="bg-slate-50 p-6 rounded-2xl border border-gray-200">
                        <h3 class="font-bold text-lg text-forest-800 mb-3 border-b border-gray-300 pb-2">サニープレイス松戸</h3>
                        <ul class="space-y-2">
                            <li><span class="font-bold">所在地：</span>千葉県松戸市高塚新田22-1</li>
                            <li><span class="font-bold">総面積：</span>2,999.50㎡</li>
                            <li><span class="font-bold">総区画数：</span>1,565区画</li>
                            <li><span class="font-bold">経営許可番号：</span>第19-3号</li>
                            <li><span class="font-bold">宗教法人：</span>松源寺</li>
                        </ul>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-2xl border border-gray-200">
                        <h3 class="font-bold text-lg text-forest-800 mb-3 border-b border-gray-300 pb-2">サニーパーク松戸</h3>
                        <ul class="space-y-2">
                            <li><span class="font-bold">所在地：</span>千葉県松戸市高塚新田21-4</li>
                            <li><span class="font-bold">総面積：</span>1,999.00㎡</li>
                            <li><span class="font-bold">総区画数：</span>1,480区画</li>
                            <li><span class="font-bold">経営許可番号：</span>第21-1号</li>
                            <li><span class="font-bold">宗教法人：</span>松源寺</li>
                        </ul>
                    </div>

                </div>

                <div class="bg-emerald-50 rounded-2xl p-4 text-center font-bold text-forest-900 text-base md:text-lg mb-8 border border-emerald-200">
                    ◎ 申込資格：宗教・宗旨を問わず、どなたでもお申込みいただけます。
                </div>

                <!-- 電話受付画像 01.jpg (アスペクト比維持) -->
                <div class="max-w-2xl mx-auto text-center">
                    <p class="font-bold text-forest-900 text-lg mb-2">お気軽にお問い合わせください</p>
                    <a href="tel:0120183289" class="block">
                        <img src="01.jpg" alt="電話受付 0120-18-3289" class="img-fit-contain mx-auto rounded-2xl shadow-lg border-2 border-gold-400 hover:opacity-95 transition" onerror="this.onerror=null; this.src='https://placehold.co/800x300/1b4d3e/ffffff?text=%E3%83%95%E3%83%AA%E3%83%BC%E3%83%80%E3%82%A4%E3%83%A4%E3%83%AB+0120-18-3289(01.jpg)';">
                    </a>
                </div>

            </div>
        </section>

    </main>


    <!-- スマホ専用固定ボトムナビゲーション -->
    <div class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-emerald-200 p-2 shadow-2xl">
        <div class="grid grid-cols-2 gap-2 max-w-md mx-auto">
            <a href="tel:0120183289" class="touch-btn bg-forest-800 text-white font-bold rounded-xl flex items-center justify-center space-x-2 text-base shadow">
                <i class="fa-solid fa-phone text-gold-400 text-xl"></i>
                <span>電話で相談</span>
            </a>
            <a href="#contact" class="touch-btn bg-gold-500 text-forest-900 font-bold rounded-xl flex items-center justify-center space-x-2 text-base shadow">
                <i class="fa-solid fa-envelope text-xl"></i>
                <span>見学予約・資料</span>
            </a>
        </div>
    </div>


    <!-- フッター -->
    <footer class="bg-forest-900 text-emerald-100 py-10 pb-20 md:pb-10 border-t border-forest-800">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <p class="text-xl font-bold font-serif text-white mb-2">
                サニープレイス松戸・サニーパーク松戸
            </p>
            <p class="text-sm text-emerald-200/80 mb-6">
                経営主体：宗教法人 松源寺
            </p>
            <p class="text-xs text-emerald-300/60">
                &copy; サニープレイス松戸・サニーパーク松戸 All Rights Reserved.
            </p>
        </div>
    </footer>


    <!-- インタラクション＆機能スクリプト -->
    <script>
        // 1. 光の拡散＆新緑・金色の葉っぱが舞うキャンバスアニメーション
        const canvas = document.getElementById('sunlightCanvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        let leaves = [];

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // 光の粒子クラス
        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 3 + 1;
                this.speedX = Math.random() * 0.4 - 0.2;
                this.speedY = Math.random() * 0.3 + 0.1;
                this.opacity = Math.random() * 0.4 + 0.1;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.y > canvas.height) {
                    this.y = 0;
                    this.x = Math.random() * canvas.width;
                }
            }
            draw() {
                ctx.save();
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 248, 220, ${this.opacity})`;
                ctx.fill();
                ctx.restore();
            }
        }

        // 舞い落ちる葉っぱクラス（画面全体で美しくひらひら舞う演出）
        class Leaf {
            constructor(isInitial = false) {
                this.reset(isInitial);
            }
            reset(isInitial = false) {
                this.x = Math.random() * (canvas.width + 100) - 50;
                // 初回は画面全域の縦幅に散らすことで、最初から画面全体で葉っぱが舞うようにする
                this.y = isInitial ? Math.random() * canvas.height : -30 - Math.random() * 100;
                this.size = Math.random() * 8 + 12; // 12px〜20pxの上品なサイズ
                this.speedY = Math.random() * 0.5 + 0.3; // ゆっくり穏やかに舞い落ちるスピード
                this.windX = Math.random() * 0.4 + 0.1; // 穏やかなそよ風効果
                this.sway = Math.random() * Math.PI * 2; // 左右揺れの位相
                this.swaySpeed = Math.random() * 0.02 + 0.01; // 揺れの速さ
                this.swayWidth = Math.random() * 2.5 + 1.0; // 左右の揺れ幅
                this.flip = Math.random() * Math.PI * 2; // 裏返るひらひら回転
                this.flipSpeed = Math.random() * 0.03 + 0.01;
                this.angle = Math.random() * Math.PI * 2;
                this.angularSpeed = (Math.random() - 0.5) * 0.02;
                this.opacity = Math.random() * 0.2 + 0.15; // 淡く優しい不透明度(15%〜35%)

                // 新緑・深緑・若葉・陽光ゴールドの美しく鮮やかなカラーバリエーション
                const leafTypes = [
                    { fill: '#2d6a4f', vein: '#11342a' }, // 落ち着いた深緑
                    { fill: '#38b000', vein: '#1f7a00' }, // 鮮やかな新緑
                    { fill: '#70e000', vein: '#38b000' }, // 若葉色
                    { fill: '#d4af37', vein: '#b89228' }  // 陽光に輝くゴールド
                ];
                this.type = leafTypes[Math.floor(Math.random() * leafTypes.length)];
            }
            update() {
                this.sway += this.swaySpeed;
                this.flip += this.flipSpeed;
                this.angle += this.angularSpeed;

                // 風に乗って斜め右下へとゆったり舞い降りる
                this.x += Math.sin(this.sway) * this.swayWidth + this.windX;
                this.y += this.speedY;

                // 画面外（下または右）に出たら、上または左から再投入して循環
                if (this.y > canvas.height + 40 || this.x > canvas.width + 50) {
                    this.reset(false);
                    if (Math.random() < 0.3) {
                        this.x = -30;
                        this.y = Math.random() * (canvas.height * 0.8);
                    }
                }
            }
            draw() {
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.angle);
                
                // 風に翻って3D風に裏返るスケール変化（ひらひら効果）
                const scaleX = Math.cos(this.flip);
                ctx.scale(Math.abs(scaleX) < 0.1 ? 0.1 : scaleX, 1);
                
                ctx.globalAlpha = this.opacity;

                // 1. 葉っぱの本体シルエット描画（繊細で上品な葉型）
                ctx.beginPath();
                ctx.moveTo(0, -this.size);
                ctx.bezierCurveTo(this.size * 0.85, -this.size * 0.3, this.size * 0.75, this.size * 0.6, 0, this.size);
                ctx.bezierCurveTo(-this.size * 0.75, this.size * 0.6, -this.size * 0.85, -this.size * 0.3, 0, -this.size);
                ctx.fillStyle = this.type.fill;
                ctx.fill();

                // 2. 葉脈（すじ）と葉茎を描画
                ctx.beginPath();
                ctx.moveTo(0, -this.size * 0.9);
                ctx.lineTo(0, this.size * 1.3);
                ctx.strokeStyle = this.type.vein;
                ctx.lineWidth = 1.2;
                ctx.stroke();

                // 左右の細い葉脈
                ctx.beginPath();
                ctx.moveTo(0, -this.size * 0.4);
                ctx.lineTo(this.size * 0.4, -this.size * 0.1);
                ctx.moveTo(0, -this.size * 0.4);
                ctx.lineTo(-this.size * 0.4, -this.size * 0.1);
                ctx.moveTo(0, this.size * 0.1);
                ctx.lineTo(this.size * 0.35, this.size * 0.4);
                ctx.moveTo(0, this.size * 0.1);
                ctx.lineTo(-this.size * 0.35, this.size * 0.4);
                ctx.lineWidth = 0.8;
                ctx.stroke();

                ctx.restore();
            }
        }

        // 初期パーティクル作成
        for (let i = 0; i < 20; i++) {
            particles.push(new Particle());
        }
        for (let i = 0; i < 25; i++) { // 25枚の控えめで上品な数量に調整
            leaves.push(new Leaf(true));
        }

        function animateSunlight() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.update();
                p.draw();
            });
            leaves.forEach(l => {
                l.update();
                l.draw();
            });
            requestAnimationFrame(animateSunlight);
        }
        animateSunlight();


        // 2. 霊園切り替えタブ（サニープレイス松戸 / サニーパーク松戸 / 両方比較）
        function switchCemetery(type) {
            const btnBoth = document.getElementById('btn-both');
            const btnPlace = document.getElementById('btn-place');
            const btnPark = document.getElementById('btn-park');
            const cardPlace = document.getElementById('card-place');
            const cardPark = document.getElementById('card-park');

            // 既存の装飾クラスを一旦リセット
            [cardPlace, cardPark].forEach(card => {
                card.classList.remove('opacity-40', 'opacity-50', 'opacity-100', 'scale-95', 'scale-100', 'ring-4', 'ring-forest-600', 'ring-gold-500', 'shadow-2xl');
            });

            const btnInactiveClass = 'flex-1 touch-btn py-3 px-3 rounded-xl font-bold text-sm md:text-base transition duration-300 text-gray-700 hover:text-forest-800 bg-transparent shadow-none';

            if (type === 'place') {
                btnPlace.className = 'flex-1 touch-btn py-3 px-3 rounded-xl font-bold text-sm md:text-base transition duration-300 bg-forest-800 text-white shadow-md';
                btnPark.className = btnInactiveClass;
                if (btnBoth) btnBoth.className = btnInactiveClass;

                cardPlace.classList.add('opacity-100', 'scale-100', 'ring-4', 'ring-forest-600', 'shadow-2xl');
                cardPark.classList.add('opacity-40', 'scale-95');
            } else if (type === 'park') {
                btnPark.className = 'flex-1 touch-btn py-3 px-3 rounded-xl font-bold text-sm md:text-base transition duration-300 bg-gold-500 text-forest-900 shadow-md';
                btnPlace.className = btnInactiveClass;
                if (btnBoth) btnBoth.className = btnInactiveClass;

                cardPark.classList.add('opacity-100', 'scale-100', 'ring-4', 'ring-gold-500', 'shadow-2xl');
                cardPlace.classList.add('opacity-40', 'scale-95');
            } else {
                // 両方比較 (both)
                if (btnBoth) btnBoth.className = 'flex-1 touch-btn py-3 px-3 rounded-xl font-bold text-sm md:text-base transition duration-300 bg-emerald-700 text-white shadow-md';
                btnPlace.className = btnInactiveClass;
                btnPark.className = btnInactiveClass;

                cardPlace.classList.add('opacity-100', 'scale-100');
                cardPark.classList.add('opacity-100', 'scale-100');
            }
        }


        // 3. LINE共有機能
        function shareLINE() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('陽光満ちる都市型洋風霊園【サニープレイス松戸・サニーパーク松戸】のご案内');
            window.open(`https://line.me/R/msg/text/?${text}%20${url}`, '_blank');
        }


        // 4. SMS共有機能
        function shareSMS() {
            const url = window.location.href;
            const text = encodeURIComponent(`【サニープレイス松戸・サニーパーク松戸】ご家族で共有したい霊園案内はこちら: ${url}`);
            window.location.href = `sms:?body=${text}`;
        }

        // 5. 非同期（Ajax）フォーム送信処理：画面再読み込み時の二重送信警告を防止
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>送信中...';

                const formData = new FormData(contactForm);
                formData.append('ajax', '1');

                fetch(contactForm.action || window.location.pathname, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const container = document.getElementById('formContainer');
                        container.innerHTML = `
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-emerald-100 text-forest-800 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-forest-900 mb-2">送信が完了いたしました</h3>
                                <p class="text-base text-gray-700">
                                    お問い合わせありがとうございます。担当者より折り返しご連絡させていただきます。
                                </p>
                            </div>
                        `;
                    } else {
                        alert(data.message || '送信エラーが発生しました。時間を置いて再度お試しください。');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(err => {
                    // 通信失敗等の場合は通常のフォーム送信（PRGパターン）へフォールバック
                    contactForm.submit();
                });
            });
        }
    </script>

</body>
</html>
