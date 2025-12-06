<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Monitoring DHT22 Sensor</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            /* 🌈 Background gradient & animation */
            body {
                background: linear-gradient(135deg, #4f46e5, #9333ea, #3b82f6);
                background-size: 300% 300%;
                animation: gradientShift 10s ease infinite;
                color: white;
                min-height: 100vh;
                font-family: "Poppins", sans-serif;
                overflow: hidden;
                position: relative;
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .main-wrapper {
                max-width: 1400px;
                width: 100%;
                margin: auto;
            }


            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* 🌟 Floating particles */
            .particle {
                position: absolute;
                width: 8px;
                height: 8px;
                background: rgba(255,255,255,0.3);
                border-radius: 50%;
                animation: float 10s infinite ease-in-out;
            }
            @keyframes float {
                0% { transform: translateY(0) scale(1); opacity: 1; }
                50% { transform: translateY(-80px) scale(1.3); opacity: 0.7; }
                100% { transform: translateY(0) scale(1); opacity: 1; }
            }

            /* ✨ Card Styling */
            .card {
                backdrop-filter: blur(20px);
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 25px;
                color: white;
                text-align: center;
                padding: 35px 20px;
                transition: transform 0.4s ease, box-shadow 0.4s ease;
                position: relative;
                overflow: hidden;
            }

            .card::before {
                content: "";
                position: absolute;
                top: 0;
                left: -80%;
                width: 50%;
                height: 100%;
                background: rgba(255, 255, 255, 0.2);
                transform: skewX(-25deg);
                transition: left 0.5s ease;
            }

            .card:hover::before {
                left: 130%;
            }

            .card:hover {
                transform: translateY(-8px) scale(1.03);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            }

            /* 🌡️ Suhu (Temperature) */
            .card.temperature {
                background: linear-gradient(145deg, rgba(255, 100, 50, 0.8), rgba(255, 150, 80, 0.7));
                box-shadow: 0 8px 32px rgba(255, 120, 60, 0.4);
            }

            /* 💧 Kelembaban (Humidity) */
            .card.humidity {
                background: linear-gradient(145deg, rgba(0, 150, 255, 0.8), rgba(80, 220, 255, 0.6));
                box-shadow: 0 8px 32px rgba(0, 180, 255, 0.4);
            }

            /* 🧊 Icons */
            .icon {
                font-size: 3.5rem;
                margin-bottom: 10px;
                text-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
            }

            /* 🔢 Animasi angka */
            #temperature, #humidity {
                font-size: 2.8rem;
                font-weight: 700;
                display: inline-block;
                min-width: 80px;
                transition: all 0.5s ease-in-out;
                text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.4);
            }

            /* 🌟 Heading */
            h1 {
                font-weight: 700;
                text-shadow: 3px 3px 15px rgba(0, 0, 0, 0.4);
                letter-spacing: 1px;
                margin-bottom: 2rem;
            }

            h5 {
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                margin-bottom: 10px;
            }

            .row .card {
                max-width: 1500px;
            }

            .container {
                animation: fadeIn 1s ease-in-out;
                position: relative;
                z-index: 2;
            }

            /* bagian tombol update dan history suhu */
            .previous-target-box {
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 12px 15px;
                border-radius: 15px;
                color: #fff;
                margin-bottom: 15px;
                text-align: center;
                backdrop-filter: blur(10px);
            }

            .previous-target-value {
                font-size: 1.6rem;
                font-weight: 700;
            }

            .target-input {
                border-radius: 15px !important;
                font-weight: 600;
                text-align: center;
            }

            .button-group {
                display: flex;
                gap: 10px;
            }

            .btn-update {
                border-radius: 15px !important;
                font-weight: 600;
            }

            .led-buzzer-row {
                display: flex;
                gap: 15px; /* jarak antar kolom */
            }

            .led-buzzer-row .previous-target-box {
                flex: 1;   /* bikin ukuran kedua kolom sama */
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            #lampControl {
                display: grid;
                grid-template-columns: repeat(2, 1fr); /* 2 kolom */
                gap: 20px;
                justify-items: center; /* biar semua switch berada di tengah */
                width: 100%;
            }

            .switch-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 30px;
            }

            .switch {
                position: relative;
                display: inline-block;
                width: 70px;
                height: 34px;
            }

            .switch input { display: none; }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #d9534f; /* merah OFF */
                transition: .4s;
                border-radius: 34px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked + .slider {
                background-color: #28a745; /* hijau ON */
            }

            input:checked + .slider:before {
                transform: translateX(36px);
            }

            .slider-text {
                position: relative;
                display: block;
                margin-top: -5px; /* turunkan supaya tidak tabrakan */
                margin-bottom: 8px;
                text-align: center;
                font-size: 14px;
                font-weight: 700;
                color: white;
            }

        </style>
    </head>
    <body>
        <!-- ✨ Particles (hiasan lembut) -->
        <div class="particle" style="top:10%; left:20%; animation-delay:0s;"></div>
        <div class="particle" style="top:30%; left:80%; animation-delay:2s;"></div>
        <div class="particle" style="top:70%; left:50%; animation-delay:4s;"></div>
        <div class="particle" style="top:50%; left:10%; animation-delay:6s;"></div>

    <div class="main-wrapper">
    <!-- seluruh container py-5 di sini -->
        <div class="container py-5">
            <div class="row justify-content-center">

                <!-- BAGIAN MONITORING -->
                <div class="col-lg-6">
                    <h1 class="text-center mb-3">Monitoring DHT22 Sensor</h1>

                    <div class="row justify-content-center">
                        <div class="card col-5 mx-2 temperature">
                            <i class="bi bi-thermometer-sun icon"></i>
                            <h5>Temperature</h5>
                            <p><span id="temperature"></span> °C</p>
                        </div>
                        <div class="card col-5 mx-2 humidity">
                            <i class="bi bi-droplet-half icon"></i>
                            <h5>Humidity</h5>
                            <p><span id="humidity"></span> %</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <div class="card p-4" style="max-width:350px; flex:1;">
                            <h5 class="mb-3">Set Target Suhu</h5>

                            <div class="previous-target-box">
                                <strong>Target suhu sebelumnya:</strong><br>
                                <span class="previous-target-value">
                                    {{ $dht->target_temperature ?? 'Belum ada data' }}
                                </span> °C
                            </div>

                            <h5 class="mb-3">Status LED & Buzzer</h5>
                            <div class="led-buzzer-row">
                                <div class="previous-target-box">
                                    <h5>LED</h5>
                                    <p><span id="ledStatus" class="previous-target-value"></span></p>
                                </div>
                                <div class="previous-target-box">
                                    <h5>Buzzer</h5>
                                    <p><span id="buzzerStatus" class="previous-target-value"></span></p>
                                </div>
                            </div>

                            <form action="/control" method="POST">
                                @csrf
                                <input type="number" name="target_temperature" class="form-control target-input" placeholder="Masukkan suhu baru">
                                <div class="button-group mt-3">
                                    <button type="submit" class="btn btn-light w-100 btn-update">Update</button>
                                </div>
                            </form>
                        </div>

                        <div class="card p-4" style="max-width:350px; flex:1;">
                            <div class="previous-target-box mt-3">
                                <h5>SERVO</h5>
                                <p><span id="servoStatus" class="previous-target-value"></span></p>
                                <button id="servoOn" class="btn btn-success w-100 mt-2">Servo ON</button>
                                <button id="servoOff" class="btn btn-danger w-100 mt-2">Servo OFF</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN CONTROL LAMPU -->
                <div class="col-lg-6">
                    <div class="card p-4 mt-5">
                        <h5 class="mb-3 text-center">Control Lampu</h5>

                        <div class="row justify-content-center text-center" id="lampControl">
                            <script>
                                const buttonLabels = ["Lampu 1", "Lampu 2", "Lampu 3", "Lampu 4", "Lampu 5", "Lampu 6"];
                            </script>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

       



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function () {

                function getData() {
                    $.ajax({
                        type: "GET",
                        url: "/get-data",
                        success: function (response) {

                            // Update suhu
                            $("#temperature").text(response.temperature);
                            $("#humidity").text(response.humidity);

                            // Update LED
                            $("#ledStatus").text(response.led == 1 ? "ON" : "OFF");

                            // Update Buzzer
                            $("#buzzerStatus").text(response.buzzer == 1 ? "ON" : "OFF");

                            // Update Servo
                            $("#servoStatus").text(response.servo == 1 ? "Garasi Terbuka" : "Garasi Tertutup");

                        }
                    });
                }

                // Refresh data tiap 2 detik
                setInterval(getData, 2000);

                // Tombol set suhu
                $("#btnSet").click(function () {
                    $.ajax({
                        type: "POST",
                        url: "/control",
                        data: {
                            target_temperature: $("#target").val(),
                        },
                        success: function () {
                            alert("Suhu kontrol diperbarui!");
                        }
                    });
                });

                // Tombol Servo ON
                $("#servoOn").click(function () {
                    $.get('/update-servo/1', function () {
                        $("#servoStatus").text("ON");  // update langsung di view
                        // alert("Garasi Terbuka!");
                    });
                });

                // Tombol Servo OFF
                $("#servoOff").click(function () {
                    $.get('/update-servo/0', function () {
                        $("#servoStatus").text("OFF"); // update langsung di view
                        // alert("Garasi Tertutup!");
                    });
                });


                //==================== SWITCH LAMPU ====================
                function loadLampStatus() {
                    $.ajax({
                        type: "GET",
                        url: "/lamp",
                        success: function(res) {

                            for (let i = 1; i <= 6; i++) {
                                let status = res[`lampu${i}`] ?? "off";

                                $(`#lampStatus${i}`).text(status.toUpperCase());
                                $(`#label${i}`).text(status === "on" ? "ON" : "OFF");
                                $(`#lampu${i}`).prop("checked", status === "on");
                            }
                        }
                    });
                }




                // Buat tombol dinamis sesuai 6 lampu
                $(function() {
                    const lampControl = document.getElementById("lampControl");
                    // Render switch ke halaman
                    for (let i = 1; i <= 6; i++) {
                        $("#lampControl").append(`
                            <div class="switch-wrapper">
                                <span class="slider-text" id="label${i}">Lampu ${i}</span>
                                <label class="switch">
                                    <input type="checkbox" id="lamp${i}">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        `);

                        // Event ON/OFF
                        $('#lamp' + i).change(function () {
                            let status = $(this).is(':checked') ? 'on' : 'off';

                            $.get(`/lamp/lampu${i}?status=${status}`, function (res) {
                                $('#label' + i).text("Lampu " + i + " (" + res.status.toUpperCase() + ")");
                            });
                        });
                    }

                    // Load status awal
                    function loadLampStatus() {
                        $.get('/lamp', function(res) {
                            for (let i = 1; i <= 6; i++) {
                                let status = res['lampu' + i];
                                $('#lamp' + i).prop('checked', status === 'on');
                                $('#label' + i).text("Lampu " + i + " (" + status.toUpperCase() + ")");
                            }
                        });
                    }

                    loadLampStatus();                 // --- Update status pertama kali
                    setInterval(loadLampStatus, 3000);

                    // klik tombol lampu
                    $(document).on("change", ".toggleLamp", function () {
                        let id = $(this).data("id");
                        let current = $(this).is(":checked") ? "on" : "off";

                        $.ajax({
                            type: "GET",
                            url: `/lamp/lampu${id}?status=${current}`,
                            success: function() {
                                loadLampStatus();
                            }
                        });
                    });

                });


            });
        </script>

    </body>
</html>
