@extends('layouts.main')

@section('title', $title)

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
        <style>
            .card input:focus,
            .card select:focus {
                box-shadow: none !important;
                outline: none !important;
                border-color: #AEA07A;
            }

            .status-badge {
                display: inline-block;
                padding: 3px 9px;
                font-size: 0.8rem;
                font-weight: 600;
                border-radius: 5px;
                text-align: center;
                min-width: 80px;
            }

            /* Warna Status Berdasarkan Slug */
            .status-pengajuan { background: #e0e0e0; color: #6C757D }
            .status-draft-admin, .status-proses, .status-warga---admin { background: #e8f2ff; color: #1e63ff }
            .status-warga { background: #eef2f7; color: #556070 }
            .status-warga---sekkel, .status-admin---sekkel, .status-warga---lurah, .status-admin---lurah { background: #ffe9d7; color: #F4A261 }
            .status-warga---camat, .status-admin---camat { background: #b4b1af; color: #B2784A }
            .status-disetujui-warga, .status-disetujui-admin, .status-ttd-basah---bukti-uploaded { background: #e6f6ee; color: #0e8a5f }
            .status-disetujui-camat-warga, .status-disetujui-camat-admin { background: #ebe7f5; color: #A78BFA }
            .status-ditolak-warga, .status-ditolak-admin, .status-dihapus { background: #fde4e6; color: #d2353c }
            .status-ttd-basah---belum-upload-bukti { background: #fff4e5; color: #b45309 }
            .status-dinaikkan-ke-sekkel, .status-dinaikkan-ke-lurah { background: #ffe9d7; color: #F4A261 }
            .status-dinaikkan-ke-camat { background: #b4b1af; color: #B2784A }
            .status-disetujui, .status-disetujui-lurah { background: #e6f6ee; color: #0e8a5f }
            .status-disetujui-camat { background: #ebe7f5; color: #A78BFA }
            .status-ditolak, .status-dihapus { background: #fde4e6; color: #d2353c }
            .status-dinilai { background: #f6f1dd; color: #8b6f1d }


            .notif-hidden {
                display: none !important;
            }

            .admin-notif-rail {
                display: grid;
                grid-template-columns: repeat(12, 1fr);
                gap: 16px;
                margin-bottom: 16px;
            }

            .admin-notif-card {
                position: relative;
                overflow: hidden;
                grid-column: span 6;
                width: 100%;
                min-height: 102px;
                border: 1px solid transparent;
                border-radius: 24px;
                padding: 16px 18px;
                display: flex;
                align-items: center;
                gap: 15px;
                text-align: left;
                background: #fff;
                transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
                box-shadow: 0 14px 34px rgba(15, 23, 42, .08);
                cursor: pointer;
                isolation: isolate;
            }

            .admin-notif-card::before {
                content: "";
                position: absolute;
                inset: -70px -90px auto auto;
                width: 210px;
                height: 210px;
                border-radius: 50%;
                opacity: .28;
                z-index: -1;
                animation: notifOrbFloat 5.6s ease-in-out infinite;
            }

            .admin-notif-card::after {
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(110deg, transparent 0%, rgba(255,255,255,.64) 42%, transparent 70%);
                transform: translateX(-130%);
                opacity: 0;
                pointer-events: none;
            }

            .admin-notif-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 20px 44px rgba(15, 23, 42, .13);
            }

            .admin-notif-card:hover::after {
                opacity: 1;
                animation: notifScan 1.25s ease;
            }

            .admin-notif-card:focus {
                outline: none;
                box-shadow: 0 0 0 4px rgba(174, 160, 122, .18), 0 20px 44px rgba(15, 23, 42, .13);
            }

            .admin-notif-card.notif-warga {
                border-color: rgba(220, 38, 38, .18);
                background: radial-gradient(circle at 12% 15%, rgba(255,255,255,.95) 0 18%, transparent 38%), linear-gradient(135deg, #fff1f2 0%, #fff7ed 52%, #ffffff 100%);
            }

            .admin-notif-card.notif-warga::before {
                background: #ef4444;
                box-shadow: -55px 70px 110px rgba(249, 115, 22, .32);
            }

            .admin-notif-card.notif-manual {
                border-color: rgba(217, 119, 6, .20);
                background: radial-gradient(circle at 12% 15%, rgba(255,255,255,.95) 0 18%, transparent 38%), linear-gradient(135deg, #fff7ed 0%, #fffbeb 52%, #ffffff 100%);
            }

            .admin-notif-card.notif-manual::before {
                background: #f59e0b;
                box-shadow: -55px 70px 110px rgba(234, 88, 12, .32);
            }

            .notif-bot {
                position: relative;
                width: 66px;
                height: 66px;
                border-radius: 22px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.55), 0 16px 32px rgba(15, 23, 42, .17);
                animation: botBreathe 2.8s ease-in-out infinite;
            }

            .notif-bot.red {
                background: linear-gradient(145deg, #ff6b6b 0%, #ef4444 48%, #b91c1c 100%);
            }

            .notif-bot.orange {
                background: linear-gradient(145deg, #ffc247 0%, #f97316 50%, #c2410c 100%);
            }

            .notif-bot::before {
                content: "";
                position: absolute;
                top: -10px;
                width: 16px;
                height: 16px;
                border-radius: 999px;
                background: #fff;
                border: 4px solid currentColor;
                color: inherit;
                box-shadow: 0 0 0 4px rgba(255,255,255,.22);
            }

            .notif-bot::after {
                content: "";
                position: absolute;
                top: 6px;
                right: 8px;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: rgba(255,255,255,.35);
                filter: blur(.2px);
            }

            .bot-face {
                position: relative;
                width: 44px;
                height: 35px;
                border-radius: 16px;
                background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.78));
                box-shadow: inset 0 -5px 12px rgba(15, 23, 42, .12);
            }

            .bot-eye {
                position: absolute;
                top: 10px;
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: #111827;
                box-shadow: 0 0 10px rgba(255,255,255,.9);
                animation: botBlink 3.8s infinite;
            }

            .bot-eye.left { left: 10px; }
            .bot-eye.right { right: 10px; }

            .bot-mouth {
                position: absolute;
                left: 13px;
                right: 13px;
                bottom: 8px;
                height: 4px;
                border-radius: 999px;
                background: rgba(17, 24, 39, .72);
                animation: botTalk 1.35s ease-in-out infinite;
            }

            .bot-ear {
                position: absolute;
                top: 24px;
                width: 8px;
                height: 18px;
                border-radius: 999px;
                background: rgba(255,255,255,.75);
                box-shadow: inset 0 -3px 6px rgba(15, 23, 42, .14);
            }

            .bot-ear.left { left: -4px; }
            .bot-ear.right { right: -4px; }

            .notif-bot.red .bot-eye { background: #dc2626; box-shadow: 0 0 12px rgba(220, 38, 38, .72); }
            .notif-bot.orange .bot-eye { background: #d97706; box-shadow: 0 0 12px rgba(217, 119, 6, .72); }

            .notif-copy {
                min-width: 0;
                flex: 1 1 auto;
            }

            .notif-official-badges {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
                margin-bottom: 5px;
            }

            .notif-official-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 3px 8px;
                border-radius: 999px;
                background: rgba(255,255,255,.85);
                border: 1px solid rgba(148,163,184,.24);
                color: #475467;
                font-size: .62rem;
                font-weight: 800;
                letter-spacing: .03em;
                text-transform: uppercase;
            }

            .notif-official-badge .dot {
                width: 7px;
                height: 7px;
                border-radius: 999px;
                display: inline-block;
                background: currentColor;
                box-shadow: 0 0 0 3px rgba(255,255,255,.55);
            }

            .notif-official-badge.kediri { color: #b91c1c; }
            .notif-official-badge.esuket { color: #0f766e; }

            .robot-government-seal {
                position: absolute;
                top: 8px;
                left: 50%;
                transform: translateX(-50%);
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: radial-gradient(circle at 30% 30%, #fef3c7 0%, #f59e0b 55%, #b45309 100%);
                box-shadow: 0 2px 6px rgba(15,23,42,.18);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 3;
                border: 2px solid rgba(255,255,255,.9);
                color: #7c2d12;
                font-size: 8px;
                font-weight: 900;
                line-height: 1;
            }

            .robot-esuket-plate {
                position: absolute;
                left: 50%;
                top: 4px;
                transform: translateX(-50%);
                min-width: 36px;
                padding: 1px 5px;
                border-radius: 999px;
                background: rgba(255,255,255,.92);
                color: #0f172a;
                font-size: 6px;
                font-weight: 900;
                letter-spacing: .04em;
                text-align: center;
                box-shadow: inset 0 -2px 3px rgba(15,23,42,.08);
            }

            .robot-public-service-ribbon {
                position: absolute;
                top: 10px;
                left: -8px;
                transform: rotate(-20deg);
                padding: 1px 6px;
                border-radius: 999px;
                background: rgba(255,255,255,.92);
                color: #7c2d12;
                font-size: 6px;
                font-weight: 900;
                letter-spacing: .03em;
                box-shadow: 0 3px 7px rgba(15,23,42,.12);
                z-index: 4;
            }

            .notif-eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: .64rem;
                font-weight: 900;
                letter-spacing: .09em;
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .notif-eyebrow::before {
                content: "";
                width: 7px;
                height: 7px;
                border-radius: 999px;
                background: currentColor;
                box-shadow: 0 0 0 5px rgba(220,38,38,.08);
                animation: notifDot 1.15s infinite;
            }

            .notif-title {
                font-size: 1.03rem;
                font-weight: 900;
                line-height: 1.25;
                margin-bottom: 4px;
                color: #111827;
            }

            .notif-desc {
                font-size: .83rem;
                color: #64748b;
                line-height: 1.45;
            }

            .notif-count-badge {
                position: relative;
                min-width: 46px;
                height: 46px;
                padding: 0 13px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 1rem;
                font-weight: 900;
                flex-shrink: 0;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.34), 0 14px 24px rgba(15,23,42,.15);
            }

            .notif-count-badge.red {
                background: linear-gradient(135deg, #ef4444, #b91c1c);
            }

            .notif-count-badge.orange {
                background: linear-gradient(135deg, #f59e0b, #c2410c);
            }

            .notif-soft-pulse {
                animation: cardAlertPulse 1.8s infinite;
            }

            .notif-card-attention {
                animation: cardShake .62s ease both, cardAlertPulse 1.8s infinite;
            }

            .notif-toolbar-hint {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: .76rem;
                color: #64748b;
                margin-top: -5px;
                margin-bottom: 14px;
            }

            .notif-toolbar-hint::before {
                content: "";
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: #22c55e;
                box-shadow: 0 0 0 5px rgba(34,197,94,.10);
            }

            .filter-count-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 22px;
                height: 22px;
                padding: 0 7px;
                margin-left: 6px;
                border-radius: 999px;
                background: #dc2626;
                color: #fff;
                font-size: .75rem;
                font-weight: 800;
                line-height: 1;
            }

            .filter-live-info {
                margin-top: 6px;
                font-size: .72rem;
                color: #6b7280;
            }

            @keyframes cardAlertPulse {
                0%, 100% { box-shadow: 0 14px 34px rgba(15, 23, 42, .08), 0 0 0 0 rgba(220, 38, 38, .16); }
                50% { box-shadow: 0 18px 40px rgba(15, 23, 42, .12), 0 0 0 9px rgba(220, 38, 38, 0); }
            }

            @keyframes botBreathe {
                0%, 100% { transform: translateY(0) rotate(-1deg) scale(1); }
                50% { transform: translateY(-3px) rotate(1deg) scale(1.025); }
            }

            @keyframes botBlink {
                0%, 86%, 100% { transform: scaleY(1); }
                90%, 94% { transform: scaleY(.18); }
            }

            @keyframes botTalk {
                0%, 100% { width: 18px; transform: translateX(0); opacity: .72; }
                50% { width: 12px; transform: translateX(3px); opacity: .95; }
            }

            @keyframes notifDot {
                0%, 100% { opacity: 1; transform: scale(1); }





                50% { opacity: .45; transform: scale(.72); }
            }

            @keyframes notifOrbFloat {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(-18px, 18px) scale(1.05); }
            }

            @keyframes notifScan {
                from { transform: translateX(-130%); }
                to { transform: translateX(130%); }
            }

            @keyframes cardShake {
                0%, 100% { transform: translateX(0); }
                15% { transform: translateX(-4px); }
                35% { transform: translateX(4px); }
                55% { transform: translateX(-3px); }
                75% { transform: translateX(3px); }
            }



            /* ==========================================================
               ROBOT NOTIFIKASI SUPER HIDUP - VERSI PEMERINTAH KOTA KEDIRI
               Fokus: lebih ringkas, lebih resmi, dan tetap terasa hidup
               ========================================================== */
            .admin-notif-rail.pro-robot-rail {
                gap: 14px;
                align-items: stretch;
            }

            .admin-notif-card.robot-live-card {
                min-height: 132px;
                padding: 14px 16px;
                border-radius: 24px;
                overflow: hidden;
                align-items: stretch;
                gap: 14px;
                backdrop-filter: blur(12px);
            }

            .admin-notif-card.robot-live-card::before {
                inset: -88px -102px auto auto;
                width: 220px;
                height: 220px;
                opacity: .30;
            }

            .robot-live-card .notif-copy {
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-width: 0;
                padding-right: 6px;
            }

            .robot-live-card .notif-title {
                font-size: 1.02rem;
                letter-spacing: -.01em;
            }

            .robot-live-card .notif-desc {
                max-width: 520px;
                font-size: .82rem;
            }

            .notif-robot-stage {
                position: relative;
                width: 102px;
                min-width: 102px;
                height: 104px;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                align-self: center;
                perspective: 900px;
            }

            .notif-robot-glow {
                position: absolute;
                bottom: 0;
                width: 72px;
                height: 15px;
                border-radius: 999px;
                filter: blur(5px);
                opacity: .35;
                animation: robotShadowPulse 1.8s ease-in-out infinite;
            }

            .robot-red .notif-robot-glow { background: #ef4444; }
            .robot-orange .notif-robot-glow { background: #f59e0b; }

            .notif-robot-signal {
                position: absolute;
                top: 2px;
                right: 4px;
                width: 22px;
                height: 22px;
                border-radius: 999px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: .70rem;
                color: #fff;
                font-weight: 900;
                z-index: 5;
                box-shadow: 0 0 0 6px rgba(255,255,255,.70), 0 10px 22px rgba(15,23,42,.18);
                animation: robotSignalPing 1.15s ease-in-out infinite;
            }

            .robot-red .notif-robot-signal { background: linear-gradient(135deg, #ef4444, #b91c1c); }
            .robot-orange .notif-robot-signal { background: linear-gradient(135deg, #f59e0b, #c2410c); }

            .notif-robot-full {
                position: relative;
                width: 74px;
                height: 96px;
                transform-style: preserve-3d;
                animation: robotAliveFloat 2.2s ease-in-out infinite;
            }

            .notif-robot-antenna {
                position: absolute;
                top: -2px;
                left: 50%;
                width: 4px;
                height: 15px;
                border-radius: 999px;
                transform: translateX(-50%);
                background: #344054;
                z-index: 4;
            }

            .notif-robot-antenna::after {
                content: "";
                position: absolute;
                left: 50%;
                top: -7px;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                transform: translateX(-50%);
                background: #fff;
                box-shadow: 0 0 0 5px rgba(255,255,255,.35), 0 0 22px currentColor;
                color: inherit;
                animation: robotAntennaGlow .95s ease-in-out infinite;
            }

            .robot-red .notif-robot-antenna::after { border: 4px solid #ef4444; }
            .robot-orange .notif-robot-antenna::after { border: 4px solid #f59e0b; }

            .notif-robot-head {
                position: absolute;
                top: 10px;
                left: 50%;
                width: 56px;
                height: 40px;
                transform: translateX(-50%);
                border-radius: 22px 22px 18px 18px;
                background: linear-gradient(145deg, rgba(255,255,255,.98), rgba(226,232,240,.88));
                border: 1px solid rgba(255,255,255,.92);
                box-shadow: inset 0 5px 10px rgba(255,255,255,.8), inset 0 -8px 16px rgba(15,23,42,.10), 0 13px 28px rgba(15,23,42,.18);
                z-index: 3;
                animation: robotHeadNod 1.75s ease-in-out infinite;
            }

            .notif-robot-head::before,
            .notif-robot-head::after {
                content: "";
                position: absolute;
                top: 14px;
                width: 7px;
                height: 15px;
                border-radius: 999px;
                background: rgba(148,163,184,.55);
                box-shadow: inset 0 -4px 7px rgba(15,23,42,.14);
            }

            .notif-robot-head::before { left: -6px; }
            .notif-robot-head::after { right: -6px; }

            .robot-eye-live {
                position: absolute;
                top: 14px;
                width: 9px;
                height: 9px;
                border-radius: 999px;
                animation: robotEyeBlink 3.5s infinite;
            }

            .robot-eye-live.left { left: 14px; }
            .robot-eye-live.right { right: 14px; }
            .robot-red .robot-eye-live { background: #dc2626; box-shadow: 0 0 12px rgba(220,38,38,.85), 0 0 26px rgba(248,113,113,.45); }
            .robot-orange .robot-eye-live { background: #d97706; box-shadow: 0 0 12px rgba(217,119,6,.85), 0 0 26px rgba(251,191,36,.45); }

            .robot-mouth-live {
                position: absolute;
                left: 18px;
                right: 18px;
                bottom: 9px;
                height: 4px;
                border-radius: 999px;
                background: #111827;
                opacity: .78;
                animation: robotSpeaking .72s ease-in-out infinite;
            }

            .notif-robot-neck {
                position: absolute;
                top: 47px;
                left: 50%;
                width: 17px;
                height: 10px;
                transform: translateX(-50%);
                border-radius: 5px;
                background: #94a3b8;
                z-index: 2;
            }

            .notif-robot-body {
                position: absolute;
                top: 55px;
                left: 50%;
                width: 54px;
                height: 34px;
                transform: translateX(-50%);
                border-radius: 20px 20px 17px 17px;
                border: 1px solid rgba(255,255,255,.70);
                box-shadow: inset 0 7px 12px rgba(255,255,255,.30), inset 0 -10px 16px rgba(15,23,42,.15), 0 15px 30px rgba(15,23,42,.16);
                z-index: 2;
            }

            .robot-red .notif-robot-body { background: linear-gradient(145deg, #ff6b6b, #ef4444 55%, #991b1b); }
            .robot-orange .notif-robot-body { background: linear-gradient(145deg, #ffd166, #f97316 55%, #9a3412); }

            .robot-chest-screen {
                position: absolute;
                top: 15px;
                left: 50%;
                width: 28px;
                height: 12px;
                transform: translateX(-50%);
                border-radius: 8px;
                background: rgba(255,255,255,.90);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 3px;
                box-shadow: inset 0 -3px 8px rgba(15,23,42,.12);
            }

            .robot-chest-screen i {
                width: 3px;
                height: 6px;
                border-radius: 999px;
                background: currentColor;
                animation: robotEqualizer .75s ease-in-out infinite;
            }
            .robot-chest-screen i:nth-child(2) { animation-delay: .12s; height: 9px; }
            .robot-chest-screen i:nth-child(3) { animation-delay: .24s; }
            .robot-red .robot-chest-screen { color: #dc2626; }
            .robot-orange .robot-chest-screen { color: #d97706; }

            .notif-robot-arm {
                position: absolute;
                top: 59px;
                width: 11px;
                height: 31px;
                border-radius: 999px;
                background: linear-gradient(180deg, #cbd5e1, #64748b);
                transform-origin: top center;
                z-index: 1;
            }
            .notif-robot-arm.left { left: 6px; transform: rotate(28deg); animation: robotArmWaveLeft 1.05s ease-in-out infinite; }
            .notif-robot-arm.right { right: 6px; transform: rotate(-28deg); animation: robotArmWaveRight 1.05s ease-in-out infinite; }

            .notif-robot-hand {
                position: absolute;
                bottom: -4px;
                left: 50%;
                width: 17px;
                height: 11px;
                transform: translateX(-50%);
                border-radius: 999px;
                background: #f8fafc;
                box-shadow: inset 0 -3px 5px rgba(15,23,42,.13);
            }

            .notif-robot-leg {
                position: absolute;
                bottom: -2px;
                width: 16px;
                height: 23px;
                border-radius: 999px 999px 8px 8px;
                background: linear-gradient(180deg, #94a3b8, #475569);
                z-index: 1;
            }
            .notif-robot-leg.left { left: 24px; animation: robotLegLeft 1.1s ease-in-out infinite; }
            .notif-robot-leg.right { right: 24px; animation: robotLegRight 1.1s ease-in-out infinite; }
            .notif-robot-foot {
                position: absolute;
                bottom: -4px;
                left: 50%;
                width: 23px;
                height: 8px;
                transform: translateX(-50%);
                border-radius: 999px;
                background: #334155;
            }

            .notif-speech-bubble {
                position: absolute;
                top: 10px;
                left: 105px;
                min-width: 105px;
                padding: 8px 10px;
                border-radius: 14px 14px 14px 4px;
                background: rgba(255,255,255,.92);
                color: #111827;
                font-size: .70rem;
                font-weight: 900;
                line-height: 1.12;
                letter-spacing: .01em;
                box-shadow: 0 12px 26px rgba(15,23,42,.13);
                animation: bubblePop 1.6s ease-in-out infinite;
                z-index: 6;
            }

            .robot-live-card .notif-count-badge {
                align-self: center;
                min-width: 55px;
                height: 55px;
                font-size: 1.13rem;
                animation: badgeBreathing 1.15s ease-in-out infinite;
            }

            .notif-action-strip {
                display: flex;
                flex-wrap: wrap;
                gap: 7px;
                margin-top: 10px;
            }

            .notif-action-chip {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                border-radius: 999px;
                padding: 5px 9px;
                font-size: .70rem;
                font-weight: 800;
                background: rgba(255,255,255,.72);
                color: #475569;
                border: 1px solid rgba(148,163,184,.18);
            }

            .notif-action-chip::before {
                content: "";
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: currentColor;
                box-shadow: 0 0 11px currentColor;
            }

            .robot-live-card.notif-card-attention .notif-robot-full {
                animation: robotEmergencyJump .58s ease both;
            }

            .robot-live-card.notif-card-attention .notif-speech-bubble {
                animation: bubbleAlert .58s ease both;
            }

            @keyframes robotAliveFloat {
                0%, 100% { transform: translateY(0) rotateY(-5deg) rotateZ(-1deg); }
                50% { transform: translateY(-6px) rotateY(5deg) rotateZ(1deg); }
            }
            @keyframes robotHeadNod {
                0%, 100% { transform: translateX(-50%) rotate(-1deg); }
                50% { transform: translateX(-50%) rotate(2deg); }
            }
            @keyframes robotEyeBlink {
                0%, 84%, 100% { transform: scaleY(1); }
                88%, 92% { transform: scaleY(.15); }
            }
            @keyframes robotSpeaking {
                0%, 100% { transform: scaleX(.72); opacity: .62; }
                50% { transform: scaleX(1.25); opacity: .95; }
            }
            @keyframes robotEqualizer {
                0%, 100% { transform: scaleY(.55); opacity: .62; }
                50% { transform: scaleY(1.25); opacity: 1; }
            }
            @keyframes robotArmWaveLeft {
                0%, 100% { transform: rotate(30deg); }
                50% { transform: rotate(54deg) translateY(-1px); }
            }
            @keyframes robotArmWaveRight {
                0%, 100% { transform: rotate(-30deg); }
                50% { transform: rotate(-54deg) translateY(-1px); }
            }
            @keyframes robotLegLeft {
                0%, 100% { transform: translateY(0) rotate(0); }
                50% { transform: translateY(-2px) rotate(4deg); }
            }
            @keyframes robotLegRight {
                0%, 100% { transform: translateY(-2px) rotate(0); }
                50% { transform: translateY(0) rotate(-4deg); }
            }
            @keyframes robotSignalPing {
                0%, 100% { transform: scale(1); box-shadow: 0 0 0 6px rgba(255,255,255,.70), 0 10px 22px rgba(15,23,42,.18); }
                50% { transform: scale(1.10); box-shadow: 0 0 0 10px rgba(255,255,255,.18), 0 10px 26px rgba(15,23,42,.20); }
            }
            @keyframes robotAntennaGlow {
                0%, 100% { transform: translateX(-50%) scale(1); }
                50% { transform: translateX(-50%) scale(1.16); }
            }
            @keyframes robotShadowPulse {
                0%, 100% { transform: scaleX(1); opacity: .33; }
                50% { transform: scaleX(.78); opacity: .22; }
            }
            @keyframes bubblePop {
                0%, 100% { transform: translateY(0) scale(1); }
                50% { transform: translateY(-4px) scale(1.035); }
            }
            @keyframes bubbleAlert {
                0% { transform: scale(.85) rotate(-2deg); }
                45% { transform: scale(1.12) rotate(2deg); }
                100% { transform: scale(1) rotate(0); }
            }
            @keyframes robotEmergencyJump {
                0% { transform: translateY(0) scale(1); }
                35% { transform: translateY(-13px) scale(1.06) rotateZ(-2deg); }
                70% { transform: translateY(2px) scale(.99) rotateZ(2deg); }
                100% { transform: translateY(0) scale(1); }
            }
            @keyframes badgeBreathing {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.075); }
            }



            /* ==========================================================
               ROBOT EMBLEM PEMERINTAH KOTA KEDIRI - SIMPLE & HIDUP
               Bentuk terinspirasi emblem Kota Kediri dan dimodif jadi robot.
               ========================================================== */
            .kediri-bot-card {
                min-height: 120px;
                padding: 12px 14px;
                border-radius: 22px;
                gap: 12px;
            }

            .kediri-bot-card .notif-copy {
                padding-right: 2px;
            }

            .kediri-bot-card .notif-eyebrow {
                font-size: .60rem;
                letter-spacing: .08em;
                margin-bottom: 4px;
            }

            .kediri-bot-card .notif-title {
                font-size: .98rem;
                margin-bottom: 3px;
            }

            .kediri-bot-card .notif-desc {
                font-size: .78rem;
                line-height: 1.35;
                max-width: 360px;
            }

            .kediri-bot-card .notif-count-badge {
                min-width: 46px;
                height: 46px;
                font-size: 1rem;
                border-radius: 16px;
            }

            .kediri-robot-stage {
                position: relative;
                width: 110px;
                min-width: 110px;
                height: 90px;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: visible;
            }

            .kediri-robot {
                position: relative;
                width: 98px;
                height: 88px;
                animation: kediriRobotFloat 2.1s ease-in-out infinite;
                transform-origin: center bottom;
            }

            .kediri-wing {
                position: absolute;
                top: 19px;
                width: 43px;
                height: 48px;
                background: linear-gradient(180deg, #fde047, #facc15 55%, #ca8a04);
                box-shadow: inset 0 -5px 10px rgba(0,0,0,.12), 0 6px 12px rgba(15,23,42,.16);
                z-index: 1;
            }

            .kediri-wing.left {
                left: -4px;
                clip-path: polygon(100% 12%, 26% 0, 0 14%, 18% 30%, 3% 45%, 22% 58%, 8% 74%, 30% 86%, 17% 100%, 100% 86%);
                transform-origin: right center;
                animation: kediriWingLeft 2.2s ease-in-out infinite;
            }

            .kediri-wing.right {
                right: -4px;
                clip-path: polygon(0 12%, 74% 0, 100% 14%, 82% 30%, 97% 45%, 78% 58%, 92% 74%, 70% 86%, 83% 100%, 0 86%);
                transform-origin: left center;
                animation: kediriWingRight 2.2s ease-in-out infinite;
            }

            .kediri-tail {
                position: absolute;
                left: 50%;
                bottom: -2px;
                width: 30px;
                height: 22px;
                transform: translateX(-50%);
                background: linear-gradient(180deg, #fde047, #eab308 60%, #a16207);
                clip-path: polygon(50% 100%, 0 32%, 18% 0, 50% 15%, 82% 0, 100% 32%);
                filter: drop-shadow(0 4px 6px rgba(0,0,0,.18));
                z-index: 0;
                animation: kediriTailSwish 2.2s ease-in-out infinite;
            }

            .kediri-crown {
                position: absolute;
                top: 0;
                left: 50%;
                width: 28px;
                height: 18px;
                transform: translateX(-50%);
                background: linear-gradient(180deg, #fde047, #eab308 72%, #a16207);
                clip-path: polygon(0 100%, 0 44%, 18% 54%, 28% 8%, 50% 44%, 72% 8%, 82% 54%, 100% 44%, 100% 100%);
                filter: drop-shadow(0 3px 3px rgba(0,0,0,.18));
                z-index: 5;
                animation: kediriCrownGlow 1.4s ease-in-out infinite;
            }

            .kediri-head {
                position: absolute;
                top: 11px;
                left: 50%;
                width: 40px;
                height: 26px;
                transform: translateX(-50%);
                border-radius: 14px;
                background: linear-gradient(180deg, #7f1d1d, #991b1b 60%, #7f1d1d);
                border: 2px solid #3f0d0d;
                box-shadow: inset 0 3px 5px rgba(255,255,255,.10), 0 7px 14px rgba(15,23,42,.20);
                z-index: 4;
                animation: kediriHeadNod 1.6s ease-in-out infinite;
            }

            .kediri-head::before,
            .kediri-head::after {
                content: "";
                position: absolute;
                top: 11px;
                width: 6px;
                height: 10px;
                border-radius: 999px;
                background: #7f1d1d;
                border: 1px solid #3f0d0d;
            }

            .kediri-head::before { left: -5px; }
            .kediri-head::after { right: -5px; }

            .kediri-eye {
                position: absolute;
                top: 7px;
                width: 9px;
                height: 9px;
                border-radius: 999px;
                border: 2px solid #2b0a0a;
                background: #fca5a5;
                box-shadow: inset 0 0 0 2px #7f1d1d, 0 0 10px rgba(248,113,113,.45);
                animation: kediriEyeBlink 3.4s infinite;
            }

            .kediri-eye.left { left: 8px; }
            .kediri-eye.right { right: 8px; }

            .kediri-mouth {
                position: absolute;
                left: 12px;
                right: 12px;
                bottom: 4px;
                height: 5px;
                border-radius: 999px;
                background: linear-gradient(90deg, #f3f4f6, #ffffff, #f3f4f6);
                box-shadow: 0 0 0 1px #2b0a0a inset;
                animation: kediriTalk .7s ease-in-out infinite;
            }

            .kediri-fang {
                position: absolute;
                bottom: -4px;
                width: 6px;
                height: 10px;
                background: #fff;
                border-radius: 0 0 5px 5px;
                box-shadow: 0 0 0 1px rgba(43,10,10,.6) inset;
                z-index: 5;
            }

            .kediri-fang.left { left: 10px; transform: rotate(12deg); }
            .kediri-fang.right { right: 10px; transform: rotate(-12deg); }

            .kediri-body {
                position: absolute;
                top: 34px;
                left: 50%;
                width: 48px;
                height: 38px;
                transform: translateX(-50%);
                background: linear-gradient(180deg, #ef4444, #dc2626 52%, #991b1b);
                border: 2px solid #3f0d0d;
                clip-path: polygon(14% 0, 86% 0, 100% 18%, 95% 82%, 50% 100%, 5% 82%, 0 18%);
                box-shadow: inset 0 4px 7px rgba(255,255,255,.14), 0 10px 18px rgba(15,23,42,.18);
                z-index: 3;
            }

            .kediri-body::before {
                content: "";
                position: absolute;
                left: 50%;
                top: 5px;
                width: 18px;
                height: 10px;
                transform: translateX(-50%);
                background: #fff;
                border-radius: 6px;
                box-shadow: inset 0 -2px 3px rgba(15,23,42,.12);
            }

            .kediri-badge-mini {
                position: absolute;
                top: 2px;
                left: 50%;
                transform: translateX(-50%);
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: radial-gradient(circle at 30% 30%, #fde68a, #f59e0b 70%, #b45309);
                border: 2px solid rgba(255,255,255,.9);
                color: #7c2d12;
                font-size: 7px;
                font-weight: 900;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
            }

            .kediri-esuket-mini {
                position: absolute;
                bottom: 8px;
                left: 50%;
                transform: translateX(-50%);
                padding: 2px 5px;
                border-radius: 999px;
                background: rgba(255,255,255,.92);
                color: #111827;
                font-size: 6px;
                font-weight: 900;
                letter-spacing: .04em;
                line-height: 1;
                white-space: nowrap;
            }

            .kediri-hand {
                position: absolute;
                top: 43px;
                width: 13px;
                height: 25px;
                border-radius: 999px;
                background: linear-gradient(180deg, #7f1d1d, #5b1111);
                border: 1px solid #3f0d0d;
                z-index: 2;
            }

            .kediri-hand.left {
                left: 18px;
                transform-origin: top center;
                transform: rotate(38deg);
                animation: kediriArmLeft 1.1s ease-in-out infinite;
            }

            .kediri-hand.right {
                right: 18px;
                transform-origin: top center;
                transform: rotate(-38deg);
                animation: kediriArmRight 1.1s ease-in-out infinite;
            }

            .kediri-leg {
                position: absolute;
                bottom: 8px;
                width: 9px;
                height: 18px;
                border-radius: 999px;
                background: linear-gradient(180deg, #7f1d1d, #5b1111);
                z-index: 2;
            }

            .kediri-leg.left {
                left: 36px;
                animation: kediriLegLeft 1s ease-in-out infinite;
            }

            .kediri-leg.right {
                right: 36px;
                animation: kediriLegRight 1s ease-in-out infinite;
            }

            .kediri-foot {
                position: absolute;
                bottom: -2px;
                left: 50%;
                width: 15px;
                height: 6px;
                transform: translateX(-50%);
                border-radius: 999px;
                background: #5b1111;
            }

            .kediri-sound-bubble {
                position: absolute;
                top: 4px;
                right: -2px;
                padding: 3px 7px;
                border-radius: 999px;
                background: rgba(255,255,255,.96);
                color: #7c2d12;
                font-size: .54rem;
                font-weight: 900;
                letter-spacing: .04em;
                box-shadow: 0 6px 14px rgba(15,23,42,.12);
                animation: bubblePop 1.5s ease-in-out infinite;
                z-index: 7;
            }

            @keyframes kediriRobotFloat {
                0%, 100% { transform: translateY(0) scale(1); }
                50% { transform: translateY(-5px) scale(1.01); }
            }

            @keyframes kediriWingLeft {
                0%, 100% { transform: rotate(-5deg); }
                50% { transform: rotate(6deg); }
            }

            @keyframes kediriWingRight {
                0%, 100% { transform: rotate(5deg); }
                50% { transform: rotate(-6deg); }
            }

            @keyframes kediriTailSwish {
                0%, 100% { transform: translateX(-50%) rotate(0deg); }
                50% { transform: translateX(-50%) rotate(6deg); }
            }

            @keyframes kediriCrownGlow {
                0%, 100% { filter: drop-shadow(0 3px 3px rgba(0,0,0,.18)); }
                50% { filter: drop-shadow(0 3px 8px rgba(250,204,21,.65)); }
            }

            @keyframes kediriHeadNod {
                0%, 100% { transform: translateX(-50%) rotate(-1deg); }
                50% { transform: translateX(-50%) rotate(2deg); }
            }

            @keyframes kediriEyeBlink {
                0%, 86%, 100% { transform: scaleY(1); }
                90%, 94% { transform: scaleY(.15); }
            }

            @keyframes kediriTalk {
                0%, 100% { transform: scaleX(.86); }
                50% { transform: scaleX(1.18); }
            }

            @keyframes kediriArmLeft {
                0%, 100% { transform: rotate(38deg); }
                50% { transform: rotate(18deg); }
            }

            @keyframes kediriArmRight {
                0%, 100% { transform: rotate(-38deg); }
                50% { transform: rotate(-18deg); }
            }

            @keyframes kediriLegLeft {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-2px); }
            }

            @keyframes kediriLegRight {
                0%, 100% { transform: translateY(-2px); }
                50% { transform: translateY(0); }
            }

            .kediri-bot-card.notif-card-attention .kediri-robot {
                animation: robotEmergencyJump .58s ease both;
            }

            @media (max-width: 1199.98px) {
                .kediri-robot-stage {
                    width: 98px;
                    min-width: 98px;
                }
            }

            @media (max-width: 575.98px) {
                .kediri-bot-card {
                    grid-template-columns: 82px 1fr auto !important;
                }

                .kediri-robot-stage {
                    width: 82px;
                    min-width: 82px;
                    transform: scale(.84);
                    transform-origin: left center;
                }

                .kediri-bot-card .notif-desc {
                    font-size: .74rem;
                }
            }

            @media (max-width: 1199.98px) {
                .notif-speech-bubble { display: none; }
                .notif-robot-stage { width: 96px; min-width: 96px; }
                .admin-notif-card.robot-live-card { min-height: 126px; }
            }

            @media (max-width: 575.98px) {
                .admin-notif-card.robot-live-card {
                    display: grid;
                    grid-template-columns: 82px 1fr auto;
                    min-height: 126px;
                    padding: 15px;
                    gap: 10px;
                }
                .notif-robot-stage { width: 82px; min-width: 82px; transform: scale(.78); transform-origin: center; }
                .robot-live-card .notif-title { font-size: .98rem; }
                .robot-live-card .notif-desc { font-size: .76rem; }
                .notif-action-strip { display: none; }
            }


            @media (max-width: 991.98px) {
                .admin-notif-card {
                    grid-column: span 12;
                }
            }



            /* Perbedaan warna robot: Surat Warga = merah, TTD Basah = orange/emas */
            #adminWargaAlert .kediri-head {
                background: linear-gradient(180deg, #7f1d1d, #991b1b 60%, #7f1d1d) !important;
                border-color: #3f0d0d !important;
            }

            #adminWargaAlert .kediri-body {
                background: linear-gradient(180deg, #ef4444, #dc2626 52%, #991b1b) !important;
                border-color: #3f0d0d !important;
            }

            #adminWargaAlert .kediri-hand,
            #adminWargaAlert .kediri-leg {
                background: linear-gradient(180deg, #7f1d1d, #5b1111) !important;
                border-color: #3f0d0d !important;
            }

            #adminWargaAlert .kediri-foot {
                background: #5b1111 !important;
            }

            #adminWargaAlert .kediri-eye {
                background: #fca5a5 !important;
                box-shadow: inset 0 0 0 2px #7f1d1d, 0 0 10px rgba(248,113,113,.55) !important;
            }

            #adminManualBasahAlert .kediri-head {
                background: linear-gradient(180deg, #92400e, #d97706 58%, #78350f) !important;
                border-color: #451a03 !important;
            }

            #adminManualBasahAlert .kediri-body {
                background: linear-gradient(180deg, #fbbf24, #f97316 52%, #c2410c) !important;
                border-color: #451a03 !important;
            }

            #adminManualBasahAlert .kediri-hand,
            #adminManualBasahAlert .kediri-leg {
                background: linear-gradient(180deg, #b45309, #78350f) !important;
                border-color: #451a03 !important;
            }

            #adminManualBasahAlert .kediri-foot {
                background: #78350f !important;
            }

            #adminManualBasahAlert .kediri-eye {
                background: #fde68a !important;
                box-shadow: inset 0 0 0 2px #b45309, 0 0 12px rgba(245,158,11,.65) !important;
            }

            #adminManualBasahAlert .kediri-badge-mini {
                background: radial-gradient(circle at 30% 30%, #fff7ed, #f59e0b 68%, #c2410c) !important;
                color: #431407 !important;
            }


            /* Sembunyikan indikator processing DataTables saat auto refresh agar reload terasa halus. */
            #tableSurat_processing {
                display: none !important;
            }

            #tableSurat th.text-center,
            #tableSurat td.text-center {
                text-align: center !important;
                vertical-align: middle !important;
            }

            #tableSurat thead th {
                vertical-align: middle !important;
                white-space: nowrap;
            }

        </style>
    @endpush

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-0 fw-bold">{{ $title }}</h3>
            </div>
            <div class="col-md-12 d-flex align-items-center justify-content-between">
                <h6 class="text-muted mb-0">
                    Daftar seluruh pengajuan surat warga <b>Kota Kediri</b> dalam satu sistem terpadu.
                </h6>
                <div class="d-flex gap-2">
                    @if (in_array(auth()->user()->role_id, [1, 8, 9]))
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-add-fill me-1"></i> Tambah Surat
                            </button>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'suket') }}">Surat Keterangan</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skbn') }}">SK Belum Menikah</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skboro') }}">SK Boro</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'sktm') }}">SK Miskin (SKTM)</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skdom') }}">SK Domisili</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skusaha') }}">SK Usaha</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skhsl') }}">SK Penghasilan</a></li>
                            </ul>
                        </div>
                    @endif
                    <button class="btn btn-secondary btn-sm shadow-sm" onclick="reload()">
                        <i class="ri-loop-right-fill me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        @if ((int) auth()->user()->role_id === 1)
            <div class="admin-notif-rail pro-robot-rail">
                <button type="button" id="adminWargaAlert" class="admin-notif-card robot-live-card kediri-bot-card notif-warga notif-soft-pulse {{ (($wargaMasukCount ?? 0) > 0) ? '' : 'notif-hidden' }}">
                    <div class="kediri-robot-stage robot-red" aria-hidden="true">
                        <div class="notif-robot-signal">!</div>
                        <div class="kediri-sound-bubble">E-SUKET</div>
                        <div class="kediri-robot">
                            <span class="kediri-wing left"></span>
                            <span class="kediri-wing right"></span>
                            <span class="kediri-tail"></span>
                            <span class="kediri-crown"></span>
                            <div class="kediri-head">
                                <span class="kediri-eye left"></span>
                                <span class="kediri-eye right"></span>
                                <span class="kediri-mouth"></span>
                                <span class="kediri-fang left"></span>
                                <span class="kediri-fang right"></span>
                            </div>
                            <span class="kediri-hand left"></span>
                            <span class="kediri-hand right"></span>
                            <div class="kediri-body">
                                <div class="kediri-badge-mini">KOTA</div>
                                <div class="kediri-esuket-mini">KEDIRI</div>
                            </div>
                            <span class="kediri-leg left"><span class="kediri-foot"></span></span>
                            <span class="kediri-leg right"><span class="kediri-foot"></span></span>
                        </div>
                    </div>
                    <div class="notif-copy">
                        <div class="notif-eyebrow text-danger">PEMERINTAH KOTA KEDIRI</div>
                        <div class="notif-title">Surat Warga Masuk</div>
                        <div class="notif-desc">Silahkan Klik Proses Surat Masuk Dari Warga.</div>
                    </div>
                    <div class="notif-count-badge red" id="adminWargaAlertCount">{{ $wargaMasukCount }}</div>
                </button>

                <button type="button" id="adminManualBasahAlert" class="admin-notif-card robot-live-card kediri-bot-card notif-manual {{ (($manualPendingCount ?? 0) > 0) ? '' : 'notif-hidden' }}">
                    <div class="kediri-robot-stage robot-orange" aria-hidden="true">
                        <div class="notif-robot-signal">!</div>
                        <div class="kediri-sound-bubble">E-SUKET</div>
                        <div class="kediri-robot">
                            <span class="kediri-wing left"></span>
                            <span class="kediri-wing right"></span>
                            <span class="kediri-tail"></span>
                            <span class="kediri-crown"></span>
                            <div class="kediri-head">
                                <span class="kediri-eye left"></span>
                                <span class="kediri-eye right"></span>
                                <span class="kediri-mouth"></span>
                                <span class="kediri-fang left"></span>
                                <span class="kediri-fang right"></span>
                            </div>
                            <span class="kediri-hand left"></span>
                            <span class="kediri-hand right"></span>
                            <div class="kediri-body">
                                <div class="kediri-badge-mini">KOTA</div>
                                <div class="kediri-esuket-mini">KEDIRI</div>
                            </div>
                            <span class="kediri-leg left"><span class="kediri-foot"></span></span>
                            <span class="kediri-leg right"><span class="kediri-foot"></span></span>
                        </div>
                    </div>
                    <div class="notif-copy">
                        <div class="notif-eyebrow" style="color:#c2410c;">PEMERINTAH KOTA KEDIRI</div>
                        <div class="notif-title">TTD Basah Belum Upload Bukti</div>
                        <div class="notif-desc">Ada Surat Cetak Manual TTD Basah Yang Wajib Dilengkapi. Klik Untuk Membuka Dan Pilih Edit Upload <b>Bukti TTD Basah</b>.</div>
                    </div>
                    <div class="notif-count-badge orange" id="adminManualBasahCount">{{ $manualPendingCount ?? 0 }}</div>
                </button>
            </div>
            <div class="notif-toolbar"><b></b></div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-4" style="border-color: #AEA07A">
                    <div class="card-header bg-transparent border-0 p-4 pb-0">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted">Filter Jenis Surat</label>
                                <select id="filterJenis" class="form-select form-select-sm shadow-sm">
                                    <option value="">-- Semua Jenis --</option>
                                    <option value="suket">Surat Keterangan</option>
                                    <option value="skbn">SK Belum Menikah</option>
                                    <option value="skboro">SK Boro</option>
                                    <option value="skdom">SK Domisili</option>
                                    <option value="skhsl">SK Penghasilan</option>
                                    <option value="sktm">SK Miskin (SKTM)</option>
                                    <option value="skusaha">SK Usaha</option>
                                </select>
                            </div>
                            @if ((int) auth()->user()->role_id === 1)
                                <div class="col-md-3">
                                    <label class="small fw-bold text-muted">Filter Status Surat</label>
                                    <select id="filterStatus" class="form-select form-select-sm shadow-sm">
                                        <option value="" data-key="default" data-label="Belum Diproses">Belum Diproses ({{ $statusCounts['default'] ?? 0 }})</option>
                                        <option value="to_sekkel" data-key="to_sekkel" data-label="Sudah Naik ke Sekkel">Sudah Naik ke Sekkel ({{ $statusCounts['to_sekkel'] ?? 0 }})</option>
                                        <option value="to_lurah" data-key="to_lurah" data-label="Sudah Naik ke Lurah">Sudah Naik ke Lurah ({{ $statusCounts['to_lurah'] ?? 0 }})</option>
                                        <option value="sktm_wait_sekcam" data-key="sktm_wait_sekcam" data-label="SKTM Belum Naik ke Sekcam">SKTM Belum Naik ke Sekcam ({{ $statusCounts['sktm_wait_sekcam'] ?? 0 }})</option>
                                        <option value="to_sekcam" data-key="to_sekcam" data-label="Sudah Naik ke Sekcam">Sudah Naik ke Sekcam ({{ $statusCounts['to_sekcam'] ?? 0 }})</option>
                                        <option value="to_camat" data-key="to_camat" data-label="Sudah Naik ke Camat">Sudah Naik ke Camat ({{ $statusCounts['to_camat'] ?? 0 }})</option>
                                        <option value="manual_pending" data-key="manual_pending" data-label="TTD Basah - Belum Upload Bukti">TTD Basah - Belum Upload Bukti ({{ $statusCounts['manual_pending'] ?? 0 }})</option>
                                        <option value="rejected" data-key="rejected" data-label="Surat Ditolak">Surat Ditolak ({{ $statusCounts['rejected'] ?? 0 }})</option>
                                        <option value="approved" data-key="approved" data-label="Surat Disetujui">Surat Disetujui ({{ $statusCounts['approved'] ?? 0 }})</option>
                                    </select>
                                    <div class="filter-live-info" id="filterLiveInfo"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="tableSurat" class="table table-hover align-middle mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Tipe</th>
                                        <th>No Surat</th>
                                        <th class="text-center">NIK Pengajuan</th>
                                        <th class="text-center">Tanggal</th>
                                        <th>Peruntukan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-esign></x-esign>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script src="{{ asset('assets/js/actions.js') }}"></script>

        <script type="text/javascript">
            $(function() {
                var table = $('#tableSurat').DataTable({
                    // Auto refresh dibuat silent agar admin tidak melihat loading/spinner terus-menerus.
                    processing: false,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: "{{ route('admin.surat.index') }}",
                        data: function (d) {
                            d.jenis = $('#filterJenis').val(); // Kirim filter jenis ke backend
                            d.filter_status = $('#filterStatus').length ? $('#filterStatus').val() : ''; // Filter status khusus Admin

                            // Search global dibuat manual di backend agar hasil pencarian tidak terkunci filter default.
                            // Contoh: cari NIK/No Surat/Peruntukan harus menampilkan semua status yang cocok.
                            d.global_search = d.search && d.search.value ? d.search.value : '';
                            if (d.search) {
                                d.search.value = '';
                            }
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle', width: '5%' },
                        { data: 'tipe', name: 'jenis_surat', orderable: true, searchable: false, className: 'text-center align-middle', width: '7%' },
                        { data: 'no_surat', name: 'no_surat', orderable: false, searchable: false, className: 'align-middle' },
                        { data: 'nik', name: 'nik', searchable: false, className: 'text-center align-middle' },
                        { data: 'tgl_surat', name: 'tgl_surat', searchable: false, className: 'text-center align-middle', width: '12%' },
                        { data: 'peruntukan', name: 'peruntukan', searchable: false, className: 'align-middle' },
                        {
							data: 'st',
							name: 'status',
                            searchable: false,
							render: function(data) {
								if (!data) return '-';
						
								let colorMap = {
									blue:   { bg: '#DBEAFE', text: '#1D4ED8' },
									orange: { bg: '#FFEDD5', text: '#C2410C' },
									green:  { bg: '#DCFCE7', text: '#15803D' },
									red:    { bg: '#FEE2E2', text: '#DC2626' },
									black:  { bg: '#F3F4F6', text: '#111827' },
									purple: { bg: '#EDE9FE', text: '#7C3AED' },
								
									'#6B7280': { bg: '#F3F4F6', text: '#6B7280' },
									'#EFBF04': { bg: '#FEF3C7', text: '#B45309' },
									'#B2784A': { bg: '#F3E8DB', text: '#B2784A' },
									'#A78BFA': { bg: '#EDE9FE', text: '#7C3AED' }
								};
								
						
								let c = colorMap[data.color] || colorMap.black;
						
								return `<span class="status-badge" style="background:${c.bg};color:${c.text};">${data.name}</span>`;
							},
							className: 'text-center'
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                    ],
                    order: [], // Default urutkan berdasarkan Tanggal
                    language: {
                        searchPlaceholder: "Cari NIK Pengajuan, No Surat, atau Peruntukan...",
                        processing: '<div class="spinner-border text-primary" role="status"></div>'
                    }
                });

                // Reload tabel saat filter dropdown berubah
                $('#filterJenis, #filterStatus').on('change', function() {
                    table.ajax.reload(null, false);
                    refreshAdminNotifCounts();
                });

                @if ((int) auth()->user()->role_id === 1)
                    initAdminWargaRealtimeNotif(table);
                @endif
            });

            function reload() {
                $('#tableSurat').DataTable().ajax.reload(null, false);
                if (typeof refreshAdminNotifCounts === 'function') {
                    refreshAdminNotifCounts();
                }
            }

            @if ((int) auth()->user()->role_id === 1)
                let adminNotifOriginalTitle = document.title;
                let adminNotifTitleBlink = null;
                let adminNotifLastCount = {{ (int) ($wargaMasukCount ?? 0) }};
                let adminNotifCurrentCount = {{ (int) ($wargaMasukCount ?? 0) }};
                let adminManualLastCount = {{ (int) ($manualPendingCount ?? 0) }};
                let adminManualCurrentCount = {{ (int) ($manualPendingCount ?? 0) }};
                let adminNotifRefreshBusy = false;
                let adminNotifAudioReady = false;

                const adminRobotRawUserName = {!! json_encode(auth()->check() ? (auth()->user()->username ?? auth()->user()->name ?? auth()->user()->email ?? 'Admin') : 'Admin') !!};

                function formatAdminRobotVoiceName(rawName) {
                    let name = String(rawName || '').trim();
                    if (!name) return 'Admin';

                    name = name.split('@')[0];
                    name = name
                        .replace(/^(kelurahan|kel|kecamatan|kec|admin|operator|user)[\s._-]+/i, '')
                        .replace(/^(kelurahan|kecamatan)[\s]+/i, '')
                        .replace(/[._-]+/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();

                    if (!name) return 'Admin';

                    return name.toLowerCase().replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
                }

                const adminRobotVoiceName = formatAdminRobotVoiceName(adminRobotRawUserName);

                function setFilterOptionCount(key, count) {
                    const opt = $('#filterStatus option[data-key="' + key + '"]');
                    const label = opt.data('label') || opt.text();
                    opt.text(label + ' (' + count + ')');
                }

                function playAdminWarningSound(type = 'warga') {
                    if (!adminNotifAudioReady) return;
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContext) return;
                        const ctx = new AudioContext();
                        const gain = ctx.createGain();
                        const freqs = type === 'manual' ? [740, 580, 740] : [920, 720, 920];

                        gain.gain.setValueAtTime(0.17, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.82);

                        freqs.forEach(function(freq, index) {
                            const osc = ctx.createOscillator();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(freq, ctx.currentTime + (index * 0.16));
                            osc.connect(gain);
                            osc.start(ctx.currentTime + (index * 0.16));
                            osc.stop(ctx.currentTime + 0.82);
                        });
                        gain.connect(ctx.destination);
                    } catch (e) {}
                }

                function speakAdminRobotMessage(message) {
                    try {
                        if (!('speechSynthesis' in window)) return;
                        window.speechSynthesis.cancel();
                        const utter = new SpeechSynthesisUtterance(message);
                        utter.lang = 'id-ID';
                        utter.rate = 0.96;
                        utter.pitch = 1;
                        utter.volume = 1;

                        const voices = window.speechSynthesis.getVoices ? window.speechSynthesis.getVoices() : [];
                        const preferredVoice = voices.find(function(v) {
                            const lang = (v.lang || '').toLowerCase();
                            const name = (v.name || '').toLowerCase();
                            return lang.indexOf('id') === 0 || name.indexOf('indones') !== -1;
                        });
                        if (preferredVoice) utter.voice = preferredVoice;

                        window.speechSynthesis.speak(utter);
                    } catch (e) {}
                }

                function triggerNotifAttention(selector) {
                    const el = $(selector);
                    el.removeClass('notif-card-attention');
                    void el[0]?.offsetWidth;
                    el.addClass('notif-card-attention');
                    setTimeout(function() {
                        el.removeClass('notif-card-attention');
                    }, 900);
                }

                function updateAdminBrowserTitle() {
                    const warga = parseInt(adminNotifCurrentCount || 0, 10);
                    const manual = parseInt(adminManualCurrentCount || 0, 10);

                    if ((warga + manual) <= 0) {
                        if (adminNotifTitleBlink) {
                            clearInterval(adminNotifTitleBlink);
                            adminNotifTitleBlink = null;
                        }
                        document.title = adminNotifOriginalTitle;
                        return;
                    }

                    if (!adminNotifTitleBlink) {
                        let blink = true;
                        adminNotifTitleBlink = setInterval(function() {
                            const latestWarga = parseInt(adminNotifCurrentCount || 0, 10);
                            const latestManual = parseInt(adminManualCurrentCount || 0, 10);

                            if ((latestWarga + latestManual) <= 0) {
                                clearInterval(adminNotifTitleBlink);
                                adminNotifTitleBlink = null;
                                document.title = adminNotifOriginalTitle;
                                return;
                            }

                            let titleParts = [];
                            if (latestWarga > 0) titleParts.push(latestWarga + ' SURAT WARGA');
                            if (latestManual > 0) titleParts.push(latestManual + ' BUKTI TTD');

                            document.title = blink ? ('🤖 ' + titleParts.join(' | ')) : adminNotifOriginalTitle;
                            blink = !blink;
                        }, 850);
                    }
                }

                function updateAdminNotifUI(count) {
                    count = parseInt(count || 0, 10);
                    adminNotifCurrentCount = count;

                    $('#adminWargaAlertCount').text(count);
                    if (count > 0) {
                        $('#adminWargaAlert').removeClass('notif-hidden');
                    } else {
                        $('#adminWargaAlert').addClass('notif-hidden');
                    }

                    updateAdminBrowserTitle();
                }

                function updateAdminManualNotifUI(count) {
                    count = parseInt(count || 0, 10);
                    adminManualCurrentCount = count;

                    $('#adminManualBasahCount').text(count);
                    if (count > 0) {
                        $('#adminManualBasahAlert').removeClass('notif-hidden');
                    } else {
                        $('#adminManualBasahAlert').addClass('notif-hidden');
                    }

                    updateAdminBrowserTitle();
                }

                function refreshAdminNotifCounts() {
                    if (adminNotifRefreshBusy) return;
                    adminNotifRefreshBusy = true;

                    $.ajax({
                        url: "{{ route('admin.surat.index') }}",
                        method: 'GET',
                        cache: false,
                        dataType: 'json',
                        data: {
                            notif_counts: 1,
                            _ts: Date.now()
                        },
                        success: function(res) {
                            const counts = res.statusCounts || {};
                            setFilterOptionCount('default', counts.default || 0);
                            setFilterOptionCount('to_sekkel', counts.to_sekkel || 0);
                            setFilterOptionCount('to_lurah', counts.to_lurah || 0);
                            setFilterOptionCount('to_camat', counts.to_camat || 0);
                            setFilterOptionCount('manual_pending', counts.manual_pending || 0);
                            setFilterOptionCount('rejected', counts.rejected || 0);
                            setFilterOptionCount('approved', counts.approved || 0);

                            const newCount = parseInt(res.wargaMasukCount || 0, 10);
                            const newManualCount = parseInt(res.manualPendingCount || 0, 10);
                            updateAdminNotifUI(newCount);
                            updateAdminManualNotifUI(newManualCount);

                            if ((newCount !== adminNotifLastCount || newManualCount !== adminManualLastCount) && $.fn.DataTable && $.fn.DataTable.isDataTable('#tableSurat')) {
                                $('#tableSurat').DataTable().ajax.reload(null, false);
                            }

                            // Robot bergerak + bunyi hanya saat jumlah bertambah agar terasa hidup tetapi tidak mengganggu terus-menerus.
                            if (newCount > adminNotifLastCount) {
                                triggerNotifAttention('#adminWargaAlert');
                                playAdminWarningSound('warga');
                            }

                            if (newManualCount > adminManualLastCount) {
                                triggerNotifAttention('#adminManualBasahAlert');
                                playAdminWarningSound('manual');
                            }

                            adminNotifLastCount = newCount;
                            adminManualLastCount = newManualCount;
                        },
                        complete: function() {
                            adminNotifRefreshBusy = false;
                        }
                    });
                }

                function initAdminWargaRealtimeNotif(table) {
                    // Aktifkan audio setelah interaksi pertama user, sesuai aturan browser modern.
                    $(document).one('click keydown mousemove', function() {
                        adminNotifAudioReady = true;
                        if (adminNotifLastCount > 0) {
                            playAdminWarningSound('warga');
                        }
                        if (adminManualLastCount > 0) {
                            playAdminWarningSound('manual');
                        }
                    });

                    updateAdminNotifUI(adminNotifLastCount);
                    updateAdminManualNotifUI(adminManualLastCount);

                    // Update cepat setiap DataTables selesai reload.
                    table.on('draw', function() {
                        refreshAdminNotifCounts();
                    });

                    // Polling ringan agar notif tetap realtime.
                    // Tabel hanya ikut reload saat jumlah data benar-benar berubah,
                    // supaya tidak terlihat seperti refresh/loading terus-menerus.
                    setInterval(function() {
                        refreshAdminNotifCounts();
                    }, 4000);

                    // Klik kartu notifikasi untuk langsung membuka filter yang relevan.
                    $('#adminWargaAlert').on('click', function() {
                        adminNotifAudioReady = true;
                        triggerNotifAttention('#adminWargaAlert');
                        playAdminWarningSound('warga');
                        speakAdminRobotMessage('Halo ' + adminRobotVoiceName + ', Anda masuk ke tabel Surat Warga belum diproses.');
                        setTimeout(function() {
                            $('#filterStatus').val('').trigger('change');
                        }, 120);
                    });

                    $('#adminManualBasahAlert').on('click', function() {
                        adminNotifAudioReady = true;
                        triggerNotifAttention('#adminManualBasahAlert');
                        playAdminWarningSound('manual');
                        speakAdminRobotMessage('Halo ' + adminRobotVoiceName + ', Anda masuk ke tabel Surat Wajib Upload Bukti.');
                        setTimeout(function() {
                            $('#filterStatus').val('manual_pending').trigger('change');
                        }, 120);
                    });

                    // Setelah tombol aksi AJAX diproses, hitung ulang lebih cepat.
                    $(document).ajaxComplete(function(event, xhr, settings) {
                        const url = settings && settings.url ? settings.url : '';
                        const data = settings && settings.data ? settings.data : '';
                        const dataText = (typeof data === 'string') ? data : JSON.stringify(data || {});

                        // Hindari loop: jangan hitung ulang setelah request polling notif atau request DataTables sendiri.
                        if (url.indexOf('notif_counts=1') !== -1 || dataText.indexOf('notif_counts') !== -1 || dataText.indexOf('draw') !== -1) return;

                        setTimeout(refreshAdminNotifCounts, 350);
                    });

                    refreshAdminNotifCounts();
                }
            @endif

            // Fungsi Global Handle Cetak (BSRE Signed File)
            function handleCetak(id, jenis) {
                $.ajax({
                    type: "GET",
                    url: `/admin/surat/cetak/${id}`,
                    data: { jenis: jenis },
                    dataType: "json",
                    success: function(response) {
                        if(response.file) {
                            window.open(response.file, '_blank', 'width=800,height=1000');
                        } else {
                            alert("File tidak ditemukan.");
                        }
                    }
                });
            }

            // Integrasi Modal Esign
            $('#esignModal').on('show.bs.modal', function(e) {
                let btn = $(e.relatedTarget);
                $(this).find('[name="_id"]').val(btn.data('id'));
                $(this).find('[name="jenis"]').val(btn.data('jenis'));
                $(this).find('[name="role"]').val(btn.data('role'));
                $(this).find('.modal-title').text("Tanda Tangan Surat : " + btn.data('no_surat'));
            });

            // Handler aman untuk tombol Naik khusus alur SKTM Lurah -> Sekcam -> Camat.
            $(document).on('click', '.btn-naik-surat', function (e) {
                e.preventDefault();
                const btn = $(this);
                const url = btn.data('url');
                const title = btn.data('title') || 'Naikkan surat ini ke level berikutnya?';

                const runRequest = function () {
                    btn.prop('disabled', true);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            if (window.Swal) {
                                Swal.fire('Berhasil', res.message || 'Status surat berhasil diperbarui.', 'success');
                            } else {
                                alert(res.message || 'Status surat berhasil diperbarui.');
                            }
                            if ($.fn.DataTable.isDataTable('#tableSurat')) {
                                $('#tableSurat').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function (xhr) {
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memperbarui status surat.';
                            if (window.Swal) {
                                Swal.fire('Gagal', msg, 'error');
                            } else {
                                alert(msg);
                            }
                        },
                        complete: function () {
                            btn.prop('disabled', false);
                        }
                    });
                };

                if (window.Swal) {
                    Swal.fire({
                        title: title,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Naikkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) runRequest();
                    });
                } else if (confirm(title)) {
                    runRequest();
                }
            });

        </script>
    @endpush
@endsection
