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
                left: -75%;
                width: 50%;
                height: 100%;
                background: rgba(255, 255, 255, 0.2);
                transform: skewX(-25deg);
                transition: left 0.5s ease;
            }

            .card:hover::before {
                left: 125%;
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
                max-width: 280px;
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

            .btn-reset {
                border-radius: 15px !important;
                font-weight: 600;
            }


            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body>
        <!-- ✨ Particles (hiasan lembut) -->
        <div class="particle" style="top:10%; left:20%; animation-delay:0s;"></div>
        <div class="particle" style="top:30%; left:80%; animation-delay:2s;"></div>
        <div class="particle" style="top:70%; left:50%; animation-delay:4s;"></div>
        <div class="particle" style="top:50%; left:10%; animation-delay:6s;"></div>

        <div class="container py-5">
            <h1 class="text-center mb-3">Monitoring DHT22 Sensor</h1>
            <div class="row justify-content-center">
                <div class="card col-3 mx-2 temperature">
                    <i class="bi bi-thermometer-sun icon"></i>
                    <h5>Temperature</h5>
                    <p><span id="temperature"></span> °C</p>
                </div>
                <div class="card col-3 mx-2 humidity">
                    <i class="bi bi-droplet-half icon"></i>
                    <h5>Humidity</h5>
                    <p><span id="humidity"></span> %</p>
                </div>
            </div>
            <div class="card p-4 mt-4" style="max-width:350px; margin:auto;">

    <h5 class="mb-3">Set Target Suhu</h5>

    {{-- INFORMASI TARGET SEBELUMNYA --}}
    <div class="previous-target-box">
        <strong>Target suhu sebelumnya:</strong><br>
        <span class="previous-target-value">
            {{ $dht->target_temperature ?? 'Belum ada data' }}
        </span> °C
    </div>

    {{-- FORM UPDATE TARGET --}}
    <form action="/control" method="POST">
        @csrf
        <input 
            type="number" 
            name="target_temperature"
            class="form-control target-input"
            placeholder="Masukkan suhu baru">

        <div class="button-group mt-3">
            <button type="submit" class="btn btn-light w-100 btn-update">
                Update
            </button>
        </div>
    </form>

</div>

        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function () {
                function getData() {
                    $.ajax({
                        type: "GET",
                        url: "/get-data",
                        success: function (response) {
                            let temperature = response.temperature;
                            let humidity = response.humidity;
                            $("#temperature").text(temperature);
                            $("#humidity").text(humidity);
                        }
                    });
                }
                setInterval(() => {
                    getData();
                }, 2000);
            });
        </script>
        <script>
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
        </script>
    </body>
</html>
